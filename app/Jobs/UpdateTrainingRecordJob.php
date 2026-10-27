<?php

namespace App\Jobs;

use App\Models\DailyClass;
use App\Models\Trainee;
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

        // Jika materi kelas harian belum terdaftar di program_non_reguler, auto-register agar memiliki ID referensi yang valid
        if (! $parentProgramNonReg) {
            $parentProgram = $dailyClass->trainingProgram;
            $startDateObj = $dailyClass->date ? Carbon::parse($dailyClass->date) : $dateObj;
            $endDateObj = $dailyClass->end_date ? Carbon::parse($dailyClass->end_date) : $startDateObj;
            $totalHari = ($startDateObj && $endDateObj) ? ($startDateObj->diffInDays($endDateObj) + 1) : 1;
            $tglPelaksanaan = $startDateObj ? json_encode([$startDateObj->translatedFormat('d M Y')]) : null;

            $totalJam = 0;
            if ($dailyClass->start_time && $dailyClass->end_time) {
                $st = Carbon::parse($dailyClass->start_time);
                $et = Carbon::parse($dailyClass->end_time);
                $totalJam = round($st->diffInRealHours($et)) * $totalHari;
            }

            $catName = $parentProgram ? $parentProgram->name : 'Non Reguler';
            $subCatName = 'Softskill';
            if (str_contains(strtolower($catName), 'managerial') || str_contains(strtolower($catName), 'leadership')) {
                $catName = 'Managerial and Leadership';
                $subCatName = 'Softskill';
            } elseif (str_contains(strtolower($catName), 'estate')) {
                $catName = 'Technical';
                $subCatName = 'Estate';
            } elseif (str_contains(strtolower($catName), 'mill')) {
                $catName = 'Technical';
                $subCatName = 'Mill';
            } elseif (str_contains(strtolower($catName), 'traksi')) {
                $catName = 'Technical';
                $subCatName = 'Traksi';
            } elseif (str_contains(strtolower($catName), 'downstream')) {
                $catName = 'Technical';
                $subCatName = 'Downstream';
            } elseif (str_contains(strtolower($catName), 'administrasi')) {
                $catName = 'Technical';
                $subCatName = 'Administrasi';
            }

            $newId = DB::table('program_non_reguler')->insertGetId([
                'jenis_pelatihan' => $dailyClass->title,
                'kategori_pelatihan' => $catName,
                'sub_kategori' => $subCatName,
                'region' => $dailyClass->region ?: ($trainee->origin ?: 'Riau'),
                'trainer' => $dailyClass->trainer_name,
                'lokasi_training' => $dailyClass->location,
                'start_date' => $dailyClass->date,
                'end_date' => $dailyClass->end_date,
                'tanggal_pelaksanaan' => $tglPelaksanaan,
                'total_hari' => $totalHari,
                'total_jam' => $totalJam,
                'tahun' => $startDateObj ? (int) $startDateObj->format('Y') : $tahunAbsen,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $parentProgramNonReg = DB::table('program_non_reguler')->where('id_nonreguler', $newId)->first();
        }

        $idNonReguler = $parentProgramNonReg ? $parentProgramNonReg->id_nonreguler : null;
        $subKategori = $parentProgramNonReg ? $parentProgramNonReg->sub_kategori : 'Softskill';
        $trainer = $dailyClass->trainer_name ?: ($parentProgramNonReg ? $parentProgramNonReg->trainer : '-');

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
            // Update record: Tambahkan tanggal baru jika belum ada dan pastikan program_type & program_id terisi
            $tanggalArray = array_filter(array_map('trim', explode(',', str_replace(' ', '', (string) $existingRecord->tanggal))));
            if (! in_array($hariAbsen, $tanggalArray, true)) {
                $tanggalArray[] = $hariAbsen;
                sort($tanggalArray);
            }
            $newTanggal = implode(', ', $tanggalArray);

            DB::table('training_records')
                ->where('id', $existingRecord->id)
                ->update([
                    'program_type' => 'non_reguler',
                    'program_id' => $idNonReguler ?: $existingRecord->program_id,
                    'reg_non_reg' => 'Non Reguler',
                    'tanggal' => $newTanggal,
                    'notes' => $existingRecord->notes && $existingRecord->notes !== '-' ? $existingRecord->notes : $subKategori,
                    'updated_at' => now(),
                ]);
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

            // Menghitung Jumlah Hari
            $jmlHari = 1;
            if ($parentProgramNonReg && $parentProgramNonReg->total_hari) {
                $jmlHari = $parentProgramNonReg->total_hari;
            } elseif ($dailyClass->end_date && $dailyClass->date) {
                $jmlHari = Carbon::parse($dailyClass->date)->diffInDays(Carbon::parse($dailyClass->end_date)) + 1;
            }

            // Menghitung Jam Pelatihan
            $jamPelatihan = 0;
            if ($parentProgramNonReg && $parentProgramNonReg->total_jam) {
                $jamPelatihan = $parentProgramNonReg->total_jam;
            } elseif ($dailyClass->start_time && $dailyClass->end_time) {
                $st = Carbon::parse($dailyClass->start_time);
                $et = Carbon::parse($dailyClass->end_time);
                $jamPelatihan = round($st->diffInRealHours($et));
            }

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
                'tanggal' => $hariAbsen,
                'jml_hari' => $jmlHari,
                'total_jam' => $jamPelatihan,
                'training_date' => $this->attendanceDate,
                'status' => 'hadir',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Invalidate caches
        Cache::increment('tr_cache_version');
        Cache::forget('tr_years');
        Cache::forget('tr_non_reg_subcats');
        Cache::increment('dashboard_cache_version');
        Cache::increment('rvb_cache_version');
    }
}
