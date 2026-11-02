<?php

namespace App\Jobs;

use App\Models\DailyClass;
use App\Models\Trainee;
use App\Services\TrainingMetricService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UpdateTrainingRecordJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $traineeId;

    protected $dailyClassId;

    protected $attendanceDate;

    public function __construct($traineeId, $dailyClassId, $attendanceDate)
    {
        $this->traineeId = $traineeId;
        $this->dailyClassId = $dailyClassId;
        $this->attendanceDate = $attendanceDate;
    }

    public function handle()
    {
        $trainee = Trainee::find($this->traineeId);
        $dailyClass = DailyClass::find($this->dailyClassId);

        if (! $trainee || ! $dailyClass) {
            return;
        }

        $dateObj = Carbon::parse($this->attendanceDate);
        $hariAbsen = $dateObj->format('d');
        $bulanAbsen = strtoupper($dateObj->translatedFormat('M')); // e.g., FEB
        $tahunAbsen = (int) $dateObj->format('Y');

        // Kalkulasi Periode (Q1, Q2, Q3, Q4)
        $monthNum = $dateObj->month;
        $periode = 'Q'.ceil($monthNum / 3);

        $parentProgramNonReg = DB::table('program_non_reguler')
            ->where('jenis_pelatihan', $dailyClass->title)
            ->first();

        // Learning Activity tidak boleh menambah data ke program_non_reguler (Program Training).
        // Jika belum terdaftar di master program_non_reguler, program_id dibiarkan null.
        $catName = 'Non Reguler';
        $subCatName = 'Softskill';
        if ($parentProgramNonReg) {
            $catName = $parentProgramNonReg->kategori_pelatihan ?: 'Non Reguler';
            $subCatName = $parentProgramNonReg->sub_kategori ?: 'Softskill';
        } else {
            $parentProgram = $dailyClass->trainingProgram;
            $rawCat = $parentProgram ? $parentProgram->name : 'Non Reguler';
            if (str_contains(strtolower($rawCat), 'managerial') || str_contains(strtolower($rawCat), 'leadership')) {
                $catName = 'Managerial and Leadership';
                $subCatName = 'Softskill';
            } elseif (str_contains(strtolower($rawCat), 'estate')) {
                $catName = 'Technical';
                $subCatName = 'Estate';
            } elseif (str_contains(strtolower($rawCat), 'mill')) {
                $catName = 'Technical';
                $subCatName = 'Mill';
            } elseif (str_contains(strtolower($rawCat), 'traksi')) {
                $catName = 'Technical';
                $subCatName = 'Traksi';
            } elseif (str_contains(strtolower($rawCat), 'downstream')) {
                $catName = 'Technical';
                $subCatName = 'Downstream';
            } elseif (str_contains(strtolower($rawCat), 'administrasi')) {
                $catName = 'Technical';
                $subCatName = 'Administrasi';
            }
        }

        $idNonReguler = $parentProgramNonReg ? $parentProgramNonReg->id_nonreguler : null;
        $subKategori = $subCatName;
        $trainer = $dailyClass->trainer_name ?: ($parentProgramNonReg ? $parentProgramNonReg->trainer : '-');

        // Hitung jam per hari dari waktu riil pelaksanaan sesi (jika ada start_time & end_time)
        $jamPerHari = 0;
        if ($dailyClass->start_time && $dailyClass->end_time) {
            $st = Carbon::parse($dailyClass->start_time);
            $et = Carbon::parse($dailyClass->end_time);
            $diffHours = round($st->diffInMinutes($et) / 60);
            if ($diffHours > 0) {
                $jamPerHari = (int) $diffHours;
            }
        } elseif ($parentProgramNonReg && $parentProgramNonReg->total_jam && (int) $parentProgramNonReg->total_jam > 0) {
            $parentTotalHari = (int) ($parentProgramNonReg->total_hari ?: 1);
            $jamPerHari = (int) round(((int) $parentProgramNonReg->total_jam) / ($parentTotalHari > 0 ? $parentTotalHari : 1));
        }

        // Hitung Jumlah Hari & Rentang Tanggal Program
        $jmlHari = 1;
        $tanggalFormatted = $hariAbsen;
        if ($dailyClass->date && $dailyClass->end_date) {
            $startDateObj = Carbon::parse($dailyClass->date);
            $endDateObj = Carbon::parse($dailyClass->end_date);
            $jmlHari = $startDateObj->diffInDays($endDateObj) + 1;
            if ($jmlHari > 1) {
                $tanggalFormatted = $startDateObj->format('d') . '-' . $endDateObj->format('d');
            } else {
                $tanggalFormatted = $startDateObj->format('d');
            }
        } elseif ($parentProgramNonReg && $parentProgramNonReg->total_hari && (int) $parentProgramNonReg->total_hari > 0) {
            $jmlHari = (int) $parentProgramNonReg->total_hari;
        }

        // Total jam adalah durasi jam per hari dikalikan jumlah hari pelatihan
        $totalJam = $jamPerHari * ($jmlHari > 0 ? $jmlHari : 1);

        // Cari record yang sudah ada
        $existingQuery = DB::table('training_records')
            ->where('participant_identifier', $trainee->nik)
            ->where(function ($q) use ($dailyClass, $idNonReguler) {
                $q->where('nama_pelatihan', $dailyClass->title);
                if ($idNonReguler) {
                    $q->orWhere(function ($sq) use ($idNonReguler) {
                        $sq->where('program_id', $idNonReguler)
                           ->where('program_type', 'non_reguler');
                    });
                }
            });

        $existingRecord = $existingQuery->first();

        if ($existingRecord) {
            $updatePayload = [
                'program_type' => 'non_reguler',
                'program_id' => $idNonReguler ?: $existingRecord->program_id,
                'reg_non_reg' => 'Non Reguler',
                'tanggal' => $tanggalFormatted,
                'total_jam' => $totalJam,
                'jml_hari' => $jmlHari,
                'instructor' => $trainer,
                'tempat_pelatihan' => $dailyClass->location ?: ($existingRecord->tempat_pelatihan ?: '-'),
                'notes' => $existingRecord->notes && $existingRecord->notes !== '-' ? $existingRecord->notes : $subKategori,
                'status' => 'valid',
                'updated_at' => now(),
            ];

            DB::table('training_records')
                ->where('id', $existingRecord->id)
                ->update($updatePayload);
        } else {
            // Coba cari Golongan dari riwayat pelatihan sebelumnya
            $gol = DB::table('training_records')
                ->where('participant_identifier', $trainee->nik)
                ->whereNotNull('gol')
                ->where('gol', '!=', '-')
                ->orderBy('created_at', 'desc')
                ->value('gol');

            if (! $gol) {
                // Mapping sederhana tebak golongan jika tidak ada history
                $jabatan = strtolower($trainee->talent_to ?? '');
                if (str_contains($jabatan, 'askep') || str_contains($jabatan, 'asst') || str_contains($jabatan, 'manager') || str_contains($jabatan, 'staff') || str_contains($jabatan, 'ktu')) {
                    $gol = 'Staf';
                } elseif (str_contains($jabatan, 'mandor') || str_contains($jabatan, 'kerani') || str_contains($jabatan, 'pekerja')) {
                    $gol = 'Non Staf';
                } else {
                    $gol = '-';
                }
            }

            // Coba cari data dari riwayat pelatihan sebelumnya (prioritas pertama)
            $pastRecord = DB::table('training_records')
                ->where('participant_identifier', $trainee->nik)
                ->orderBy('created_at', 'desc')
                ->first();

            $gender = ($pastRecord && $pastRecord->gender && $pastRecord->gender !== '-') ? $pastRecord->gender : ($trainee->gender ?: '-');
            $company = ($pastRecord && $pastRecord->company && $pastRecord->company !== '-') ? $pastRecord->company : ($trainee->unit ?: '-');
            $workLoc = ($pastRecord && $pastRecord->work_location && $pastRecord->work_location !== '-') ? $pastRecord->work_location : ($trainee->unit ?: '-');
            $group = ($pastRecord && $pastRecord->group_name && $pastRecord->group_name !== '-') ? $pastRecord->group_name : ($trainee->group ?: '-');
            $region = ($pastRecord && $pastRecord->region && $pastRecord->region !== '-') ? $pastRecord->region : ($trainee->origin ?: '-');
            $level = ($pastRecord && $pastRecord->level && $pastRecord->level !== '-') ? $pastRecord->level : '-';
            $department = ($pastRecord && $pastRecord->department && $pastRecord->department !== '-') ? $pastRecord->department : '-';

            // Menentukan Keterangan (Notes) berdasarkan Nama Program
            $judul = strtolower($dailyClass->title . ' ' . ($dailyClass->trainingProgram->name ?? ''));
            if (str_contains($judul, 'managerial') || str_contains($judul, 'leadership')) {
                $keterangan = 'Softskill';
            } elseif (str_contains($judul, 'estate')) {
                $keterangan = 'Estate';
            } elseif (str_contains($judul, 'traksi')) {
                $keterangan = 'Traksi';
            } else {
                $keterangan = $subKategori ?: '-';
            }

            // Create record baru
            DB::table('training_records')->insert([
                'program_type' => 'non_reguler',
                'program_id' => $idNonReguler,
                'nama_pelatihan' => $dailyClass->title,
                'tempat_pelatihan' => $dailyClass->location ?? '-',
                'reg_non_reg' => 'Non Reguler',
                'participant_identifier' => $trainee->nik,
                'participant_name' => $trainee->name,
                'gender' => $gender,
                'company' => $company,
                'department' => $department,
                'work_location' => $workLoc,
                'group_name' => $group,
                'region' => $region,
                'gol' => $gol,
                'position' => $trainee->talent_to ?? '-',
                'instructor' => $trainer,
                'notes' => $keterangan,
                'internal_external' => $dailyClass->provider ?: 'Internal',
                'level' => $level,
                'periode' => $periode,
                'bulan' => $bulanAbsen,
                'tahun' => $tahunAbsen,
                'tanggal' => $tanggalFormatted,
                'jml_hari' => $jmlHari,
                'total_jam' => $totalJam,
                'training_date' => $this->attendanceDate,
                'status' => 'valid',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Invalidate all dashboard & program caches
        TrainingMetricService::clearAllDashboardCaches();
        Cache::forever('tr_cache_version', ((int) Cache::get('tr_cache_version', 1)) + 1);
        Cache::forget('tr_years');
        Cache::forget('tr_non_reg_subcats');
        Cache::forever('dashboard_cache_version', ((int) Cache::get('dashboard_cache_version', 1)) + 1);
        Cache::forever('rvb_cache_version', ((int) Cache::get('rvb_cache_version', 1)) + 1);
    }
}
