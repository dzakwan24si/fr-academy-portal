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
        $tahunAbsen = $dateObj->format('Y');

        // Kalkulasi Periode (Q1, Q2, Q3, Q4)
        $monthNum = $dateObj->month;
        $periode = 'Q'.ceil($monthNum / 3);

        $parentProgramNonReg = DB::table('program_non_reguler')->where('jenis_pelatihan', $dailyClass->title)->first();
        
        $idNonReguler = $parentProgramNonReg ? $parentProgramNonReg->id_nonreguler : null;
        $subKategori = $parentProgramNonReg ? $parentProgramNonReg->sub_kategori : '-';
        $trainer = $dailyClass->trainer_name ?: ($parentProgramNonReg ? $parentProgramNonReg->trainer : '-');

        // Cari record yang sudah ada
        $existingQuery = DB::table('training_records')
            ->where('participant_identifier', $trainee->nik)
            ->where('nama_pelatihan', $dailyClass->title);
            
        if ($idNonReguler) {
            $existingQuery->orWhere(function($q) use ($trainee, $idNonReguler) {
                $q->where('participant_identifier', $trainee->nik)
                  ->where('program_id', $idNonReguler)
                  ->where('program_type', 'non_reguler');
            });
        }
        
        $existingRecord = $existingQuery->first();

        if ($existingRecord) {
            // Update record: Tambahkan tanggal baru jika belum ada
            $tanggalArray = explode(', ', $existingRecord->tanggal);
            if (! in_array($hariAbsen, $tanggalArray)) {
                $tanggalArray[] = $hariAbsen;
                // Urutkan tanggal agar rapi
                sort($tanggalArray);
                $newTanggal = implode(', ', $tanggalArray);

                DB::table('training_records')
                    ->where('id', $existingRecord->id)
                    ->update([
                        'tanggal' => $newTanggal,
                        'updated_at' => now(),
                    ]);
            }
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
                'program_type' => $idNonReguler ? 'non_reguler' : null,
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
    }
}
