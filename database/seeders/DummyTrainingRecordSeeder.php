<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Program\ProgramNonReguler;

class DummyTrainingRecordSeeder extends Seeder
{
    public function run()
    {
        // 1. Buat Dummy Program Non Reguler
        $programs = [
            [
                'kategori_pelatihan' => 'Managerial and Leadership',
                'jenis_pelatihan' => 'Awareness Leadership',
                'sub_kategori' => 'Softskill',
                'region' => 'Riau',
                'trainer' => 'Internal',
                'lokasi_training' => 'FRLC',
                'total_hari' => 2,
                'total_jam' => 16,
            ],
            [
                'kategori_pelatihan' => 'Managerial and Leadership',
                'jenis_pelatihan' => 'Strategic Leadership',
                'sub_kategori' => 'Softskill',
                'region' => 'Riau',
                'trainer' => 'Eksternal',
                'lokasi_training' => 'Jakarta',
                'total_hari' => 2,
                'total_jam' => 16,
            ]
        ];

        foreach ($programs as $prog) {
            ProgramNonReguler::updateOrCreate(
                ['jenis_pelatihan' => $prog['jenis_pelatihan']],
                $prog
            );
        }

        $awarnesId = ProgramNonReguler::where('jenis_pelatihan', 'Awareness Leadership')->value('id_nonreguler');
        $strategicId = ProgramNonReguler::where('jenis_pelatihan', 'Strategic Leadership')->value('id_nonreguler');

        // Hapus dummy data sebelumnya supaya tidak dobel jika di-run ulang
        DB::table('training_records')
            ->whereIn('participant_identifier', ['2023030337', '2023070868'])
            ->where('notes', 'like', '%Leadership%')
            ->delete();

        // 2. Buat Dummy Training Records
        $records = [
            // Data Lengkap (Awareness Leadership - Askep)
            [
                'program_type' => 'non_reguler',
                'program_id' => $awarnesId,
                'participant_identifier' => '2023030337',
                'participant_name' => 'AHMAD PAJAR',
                'gender' => 'Male',
                'company' => 'ANJAS',
                'work_location' => 'ANJAS',
                'group_name' => 'FR',
                'region' => 'Riau',
                'gol' => 'Staf',
                'position' => 'Askep',
                'instructor' => 'Internal',
                'notes' => 'Softskill',
                'internal_external' => 'Internal',
                'level' => 'workers',
                'periode' => '2026',
                'status' => 'valid',
                'created_at' => now(),
            ],
            // Data Kosong 1 Kolom (Test Fallback / Kekurangan Data)
            [
                'program_type' => 'non_reguler',
                'program_id' => $strategicId,
                'participant_identifier' => '2023070868',
                'participant_name' => 'MUHAMMAD RICKI PURBA',
                'gender' => 'Male',
                'company' => 'ATS1',
                'work_location' => 'ATS1',
                'group_name' => 'FR',
                'region' => 'Riau',
                'gol' => 'Staf',
                'position' => 'Manager',
                'instructor' => 'Eksternal',
                'notes' => 'Softskill',
                'internal_external' => 'External',
                'level' => 'senior',
                'periode' => '2026',
                'status' => 'valid',
                'created_at' => now()->subDays(1),
            ]
        ];

        DB::table('training_records')->insert($records);
    }
}
