<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Administrasi\Mpp;

class MppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['nama_jabatan' => 'Manager Ops', 'Staff' => 2, 'PBT' => 0, 'KHT' => 0, 'KHL' => 0],
            ['nama_jabatan' => 'Asisten Agronomi', 'Staff' => 10, 'PBT' => 2, 'KHT' => 1, 'KHL' => 0],
            ['nama_jabatan' => 'Mandor Panen', 'Staff' => 0, 'PBT' => 2, 'KHT' => 10, 'KHL' => 5],
            ['nama_jabatan' => 'Kerani', 'Staff' => 3, 'PBT' => 0, 'KHT' => 0, 'KHL' => 0],
        ];

        foreach ($data as $item) {
            foreach (['Staff', 'PBT', 'KHT', 'KHL'] as $type) {
                Mpp::updateOrCreate(
                    [
                        'nama_jabatan' => $item['nama_jabatan'],
                        'jenis_jabatan' => $type,
                        'tahun' => 2026,
                        'bulan' => 8,
                    ],
                    [
                        'jumlah' => $item[$type],
                    ]
                );
            }
        }
    }
}
