<?php

namespace Database\Seeders;

use App\Models\Monitoring\ResignedStaff;
use Illuminate\Database\Seeder;

class ResignedStaffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clusters = ['Pindah perusahaan', 'Working Condition', 'Under perform', 'Keluarga', 'Efisiensi', 'Kasus', 'Pergi tanpa Keterangan', 'Habis Kontrak', 'Indisipliner'];
        $jabatans = ['Asisten Kebun', 'Askep', 'Manager Kebun', 'Asisten Bibitan', 'Group Manager Kebun', 'Plantation Controller'];
        $kebuns = ['Kebun KALE', 'Kebun SMM', 'Kebun ANJAS', 'Kebun ANJA', 'MSSP', 'KTBM', 'SAGM', 'USP', 'LS2', 'MKB2'];
        $regions = ['Riau', 'Kalbar', 'Kubar'];
        $jenis = ['Voluntary', 'Involuntary'];
        $alumnis = ['Alumni', 'Non Alumni'];

        $data = [];
        for ($i = 1; $i <= 100; $i++) {
            $year = rand(2025, 2026);
            $month = rand(1, 12);
            $day = rand(1, 28);
            $resignDate = sprintf('%04d-%02d-%02d', $year, $month, $day);
            $joinDate = sprintf('%04d-%02d-%02d', rand(2020, 2024), rand(1, 12), rand(1, 28));

            // Bias: Voluntary usually means Pindah Perusahaan, Working Condition. Involuntary usually Under Perform, Kasus, dll.
            $isVoluntary = (rand(1, 100) <= 70); // 70% voluntary
            $jenisResign = $isVoluntary ? 'Voluntary' : 'Involuntary';

            if ($isVoluntary) {
                $clusterKeys = ['Pindah perusahaan', 'Working Condition', 'Keluarga'];
                $cluster = $clusterKeys[array_rand($clusterKeys)];
            } else {
                $clusterKeys = ['Under perform', 'Efisiensi', 'Kasus', 'Indisipliner'];
                $cluster = $clusterKeys[array_rand($clusterKeys)];
            }

            $data[] = [
                'employee_id' => 'EMP'.rand(10000, 99999),
                'nama' => 'Dummy Staf '.$i,
                'join_date' => $joinDate,
                'resign_date' => $resignDate,
                'jabatan' => $jabatans[array_rand($jabatans)],
                'kebun' => $kebuns[array_rand($kebuns)],
                'deskripsi_resign' => 'Deskripsi dummy resign',
                'jenis_resign' => $jenisResign,
                'cluster_resign' => $cluster,
                'alumni' => $alumnis[array_rand($alumnis)],
                'keterangan' => 'Keterangan dummy',
                'region' => $regions[array_rand($regions)],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        ResignedStaff::insert($data);
    }
}
