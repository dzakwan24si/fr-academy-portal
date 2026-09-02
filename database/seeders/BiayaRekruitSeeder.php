<?php

namespace Database\Seeders;

use App\Models\Administrasi\BiayaRekruit;
use Illuminate\Database\Seeder;

class BiayaRekruitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BiayaRekruit::truncate();

        $data = [
            [
                'tahun' => 2026,
                'tanggal' => '2026-08-05',
                'nama' => 'Ticket Pekanbaru - Jakarta',
                'total_biaya' => 3500000,
                'jenis_rekruit' => 'Regular Staff',
                'penanggung_jawab' => 'HR Talent Acquisition',
            ],
            [
                'tahun' => 2026,
                'tanggal' => '2026-08-12',
                'nama' => 'Biaya Recruitment Program A',
                'total_biaya' => 2500000,
                'jenis_rekruit' => 'Mandor Training',
                'penanggung_jawab' => 'HR Academy',
            ],
            [
                'tahun' => 2026,
                'tanggal' => '2026-08-18',
                'nama' => 'Ticket Jakarta - Pekanbaru',
                'total_biaya' => 2800000,
                'jenis_rekruit' => 'Regular Staff',
                'penanggung_jawab' => 'HR Talent Acquisition',
            ],
            [
                'tahun' => 2026,
                'tanggal' => '2026-07-20',
                'nama' => 'Akomodasi & Konsumsi Tim Asesor Campus Hiring UNRI',
                'total_biaya' => 4750000,
                'jenis_rekruit' => 'Campus Hiring',
                'penanggung_jawab' => 'HR Talent Acquisition',
            ],
            [
                'tahun' => 2026,
                'tanggal' => '2026-06-15',
                'nama' => 'Medical Check-Up (MCU) Calon Karyawan Batch 3',
                'total_biaya' => 6200000,
                'jenis_rekruit' => 'Regular Staff',
                'penanggung_jawab' => 'HR Medical & Ops',
            ],
            [
                'tahun' => 2026,
                'tanggal' => '2026-05-10',
                'nama' => 'Sewa Venue & Sound System Job Fair Pekanbaru',
                'total_biaya' => 5000000,
                'jenis_rekruit' => 'Job Fair',
                'penanggung_jawab' => 'HR Academy',
            ],
        ];

        foreach ($data as $item) {
            BiayaRekruit::create($item);
        }
    }
}
