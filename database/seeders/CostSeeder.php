<?php

namespace Database\Seeders;

use App\Models\Administrasi\Cost;
use Illuminate\Database\Seeder;

class CostSeeder extends Seeder
{
    public function run(): void
    {
        $year = 2026;

        $data = [
            // ---- JANUARI ----
            ['bulan' => 1, 'kategori' => 'ATK & Percetakan',   'nama_item' => 'Kertas A4 80gr',            'budget' => 1500000,  'realisasi' => 1425000,  'tanggal' => '2026-01-08', 'no_bukti' => 'BV/2026/01/001'],
            ['bulan' => 1, 'kategori' => 'ATK & Percetakan',   'nama_item' => 'Tinta Printer Canon',        'budget' => 800000,   'realisasi' => 780000,   'tanggal' => '2026-01-10', 'no_bukti' => 'BV/2026/01/002'],
            ['bulan' => 1, 'kategori' => 'Konsumsi & Jamuan',  'nama_item' => 'Konsumsi Rapat Bulanan',     'budget' => 3500000,  'realisasi' => 3200000,  'tanggal' => '2026-01-15', 'no_bukti' => 'BV/2026/01/003'],
            ['bulan' => 1, 'kategori' => 'Utilitas & Listrik',  'nama_item' => 'Tagihan Listrik Kantor',    'budget' => 8500000,  'realisasi' => 8740000,  'tanggal' => '2026-01-20', 'no_bukti' => 'BV/2026/01/004'],
            ['bulan' => 1, 'kategori' => 'Pemeliharaan',        'nama_item' => 'Service AC Kantor',         'budget' => 2000000,  'realisasi' => 1800000,  'tanggal' => '2026-01-25', 'no_bukti' => 'BV/2026/01/005'],

            // ---- FEBRUARI ----
            ['bulan' => 2, 'kategori' => 'ATK & Percetakan',   'nama_item' => 'Amplop & Map Dokumen',       'budget' => 600000,   'realisasi' => 590000,   'tanggal' => '2026-02-05', 'no_bukti' => 'BV/2026/02/001'],
            ['bulan' => 2, 'kategori' => 'Konsumsi & Jamuan',  'nama_item' => 'Konsumsi Tamu / Kunjungan', 'budget' => 2000000,  'realisasi' => 2350000,  'tanggal' => '2026-02-12', 'no_bukti' => 'BV/2026/02/002'],
            ['bulan' => 2, 'kategori' => 'Utilitas & Listrik',  'nama_item' => 'Tagihan Listrik Kantor',    'budget' => 8500000,  'realisasi' => 8420000,  'tanggal' => '2026-02-20', 'no_bukti' => 'BV/2026/02/003'],
            ['bulan' => 2, 'kategori' => 'Perjalanan Dinas',    'nama_item' => 'Transport Rekruit Jakarta',  'budget' => 5000000,  'realisasi' => 5200000,  'tanggal' => '2026-02-18', 'no_bukti' => 'BV/2026/02/004'],

            // ---- MARET ----
            ['bulan' => 3, 'kategori' => 'ATK & Percetakan',   'nama_item' => 'Materai & Dokumen Hukum',    'budget' => 1200000,  'realisasi' => 1100000,  'tanggal' => '2026-03-04', 'no_bukti' => 'BV/2026/03/001'],
            ['bulan' => 3, 'kategori' => 'Konsumsi & Jamuan',  'nama_item' => 'Konsumsi Rapat Q1',          'budget' => 4000000,  'realisasi' => 3850000,  'tanggal' => '2026-03-15', 'no_bukti' => 'BV/2026/03/002'],
            ['bulan' => 3, 'kategori' => 'Utilitas & Listrik',  'nama_item' => 'Tagihan Listrik Kantor',    'budget' => 8500000,  'realisasi' => 8650000,  'tanggal' => '2026-03-20', 'no_bukti' => 'BV/2026/03/003'],
            ['bulan' => 3, 'kategori' => 'Pemeliharaan',        'nama_item' => 'Perbaikan Plafon Ruang IT', 'budget' => 3500000,  'realisasi' => 3200000,  'tanggal' => '2026-03-22', 'no_bukti' => 'BV/2026/03/004'],

            // ---- APRIL ----
            ['bulan' => 4, 'kategori' => 'ATK & Percetakan',   'nama_item' => 'Kertas HVS & Folio',        'budget' => 1400000,  'realisasi' => 1380000,  'tanggal' => '2026-04-07', 'no_bukti' => 'BV/2026/04/001'],
            ['bulan' => 4, 'kategori' => 'Konsumsi & Jamuan',  'nama_item' => 'Konsumsi Pelatihan Internal','budget' => 5000000,  'realisasi' => 4700000,  'tanggal' => '2026-04-14', 'no_bukti' => 'BV/2026/04/002'],
            ['bulan' => 4, 'kategori' => 'Utilitas & Listrik',  'nama_item' => 'Tagihan Listrik Kantor',    'budget' => 8500000,  'realisasi' => 8310000,  'tanggal' => '2026-04-20', 'no_bukti' => 'BV/2026/04/003'],
            ['bulan' => 4, 'kategori' => 'Perjalanan Dinas',    'nama_item' => 'Tiket Pesawat Riau',        'budget' => 6000000,  'realisasi' => 5900000,  'tanggal' => '2026-04-25', 'no_bukti' => 'BV/2026/04/004'],

            // ---- MEI ----
            ['bulan' => 5, 'kategori' => 'ATK & Percetakan',   'nama_item' => 'Toner Printer Laserjet',     'budget' => 1800000,  'realisasi' => 1750000,  'tanggal' => '2026-05-06', 'no_bukti' => 'BV/2026/05/001'],
            ['bulan' => 5, 'kategori' => 'Konsumsi & Jamuan',  'nama_item' => 'Konsumsi Rapat Direksi',     'budget' => 6000000,  'realisasi' => 6200000,  'tanggal' => '2026-05-15', 'no_bukti' => 'BV/2026/05/002'],
            ['bulan' => 5, 'kategori' => 'Utilitas & Listrik',  'nama_item' => 'Tagihan Listrik Kantor',    'budget' => 8500000,  'realisasi' => 8780000,  'tanggal' => '2026-05-20', 'no_bukti' => 'BV/2026/05/003'],
            ['bulan' => 5, 'kategori' => 'Pemeliharaan',        'nama_item' => 'Servis Genset & UPS',       'budget' => 4000000,  'realisasi' => 4150000,  'tanggal' => '2026-05-28', 'no_bukti' => 'BV/2026/05/004'],

            // ---- JUNI ----
            ['bulan' => 6, 'kategori' => 'ATK & Percetakan',   'nama_item' => 'Form & Blangko HR',          'budget' => 900000,   'realisasi' => 870000,   'tanggal' => '2026-06-04', 'no_bukti' => 'BV/2026/06/001'],
            ['bulan' => 6, 'kategori' => 'Konsumsi & Jamuan',  'nama_item' => 'Konsumsi Acara Mid-Year',    'budget' => 8000000,  'realisasi' => 7650000,  'tanggal' => '2026-06-13', 'no_bukti' => 'BV/2026/06/002'],
            ['bulan' => 6, 'kategori' => 'Utilitas & Listrik',  'nama_item' => 'Tagihan Listrik Kantor',    'budget' => 8500000,  'realisasi' => 8490000,  'tanggal' => '2026-06-20', 'no_bukti' => 'BV/2026/06/003'],
            ['bulan' => 6, 'kategori' => 'Perjalanan Dinas',    'nama_item' => 'Hotel & Uang Harian Kaltim','budget' => 7500000,  'realisasi' => 7300000,  'tanggal' => '2026-06-25', 'no_bukti' => 'BV/2026/06/004'],

            // ---- JULI (current) ----
            ['bulan' => 7, 'kategori' => 'ATK & Percetakan',   'nama_item' => 'Kertas A4 & Ballpen',        'budget' => 1500000,  'realisasi' => 1420000,  'tanggal' => '2026-07-05', 'no_bukti' => 'BV/2026/07/001'],
            ['bulan' => 7, 'kategori' => 'ATK & Percetakan',   'nama_item' => 'Stempel & Tinta Stempel',    'budget' => 450000,   'realisasi' => 430000,   'tanggal' => '2026-07-06', 'no_bukti' => 'BV/2026/07/002'],
            ['bulan' => 7, 'kategori' => 'Konsumsi & Jamuan',  'nama_item' => 'Konsumsi Rapat Bulanan Juli','budget' => 3500000,  'realisasi' => 3200000,  'tanggal' => '2026-07-10', 'no_bukti' => 'BV/2026/07/003'],
            ['bulan' => 7, 'kategori' => 'Utilitas & Listrik',  'nama_item' => 'Tagihan Listrik Kantor',    'budget' => 8500000,  'realisasi' => 8600000,  'tanggal' => '2026-07-20', 'no_bukti' => 'BV/2026/07/004'],
            ['bulan' => 7, 'kategori' => 'Perjalanan Dinas',    'nama_item' => 'Transport Rekruitmen Baru', 'budget' => 4000000,  'realisasi' => 3850000,  'tanggal' => '2026-07-22', 'no_bukti' => 'BV/2026/07/005'],
            ['bulan' => 7, 'kategori' => 'Pemeliharaan',        'nama_item' => 'Pengecatan Dinding Kantor', 'budget' => 5000000,  'realisasi' => 4800000,  'tanggal' => '2026-07-28', 'no_bukti' => 'BV/2026/07/006'],
        ];

        foreach ($data as $item) {
            Cost::create(array_merge(['tahun' => $year], $item));
        }
    }
}
