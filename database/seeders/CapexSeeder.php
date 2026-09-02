<?php

namespace Database\Seeders;

use App\Models\Administrasi\Capex;
use Illuminate\Database\Seeder;

class CapexSeeder extends Seeder
{
    public function run(): void
    {
        // --- RAB Kantor 2026 ---
        $rabKantor = [
            ['nama_barang' => 'Laptop / Notebook', 'type_merk' => 'ASUS ExpertBook B1', 'satuan' => 'Unit', 'jumlah_unit' => 5, 'harga_satuan' => 12500000],
            ['nama_barang' => 'Printer Laser A4', 'type_merk' => 'HP LaserJet Pro M404n', 'satuan' => 'Unit', 'jumlah_unit' => 2, 'harga_satuan' => 4800000],
            ['nama_barang' => 'AC Split 1 PK', 'type_merk' => 'Daikin FTC25NV14', 'satuan' => 'Unit', 'jumlah_unit' => 3, 'harga_satuan' => 3750000],
            ['nama_barang' => 'Meja Kerja Eksekutif', 'type_merk' => 'Siantano Type D120', 'satuan' => 'Set', 'jumlah_unit' => 4, 'harga_satuan' => 2500000],
            ['nama_barang' => 'Kursi Ergonomis', 'type_merk' => 'Brother Furniture', 'satuan' => 'Pcs', 'jumlah_unit' => 10, 'harga_satuan' => 1800000],
        ];

        foreach ($rabKantor as $item) {
            Capex::create(array_merge($item, [
                'tahun'         => 2026,
                'jenis_capex'   => 'kantor',
                'jenis_catatan' => 'rab',
                'total_rab'     => $item['jumlah_unit'] * $item['harga_satuan'],
            ]));
        }

        // --- Realisasi Kantor 2026 ---
        $realisasiKantor = [
            ['tanggal' => '2026-02-15', 'nama_barang' => 'Laptop / Notebook', 'type_merk' => 'ASUS ExpertBook B1', 'satuan' => 'Unit', 'nama_transaksi' => 'Pembelian Laptop Staf Admin Batch 1', 'debit' => 37500000, 'kredit' => 0],
            ['tanggal' => '2026-04-10', 'nama_barang' => 'Printer Laser A4', 'type_merk' => 'HP LaserJet Pro M404n', 'satuan' => 'Unit', 'nama_transaksi' => 'Pembelian Printer Kantor', 'debit' => 9600000, 'kredit' => 0],
            ['tanggal' => '2026-06-20', 'nama_barang' => 'AC Split 1 PK', 'type_merk' => 'Daikin FTC25NV14', 'satuan' => 'Unit', 'nama_transaksi' => 'Pembelian & Pemasangan AC Ruang Meeting', 'debit' => 7500000, 'kredit' => 0],
        ];

        foreach ($realisasiKantor as $item) {
            Capex::create(array_merge($item, [
                'tahun'         => 2026,
                'jenis_capex'   => 'kantor',
                'jenis_catatan' => 'realisasi',
            ]));
        }

        // --- RAB Mess 2026 ---
        $rabMess = [
            ['nama_barang' => 'AC Split 1.5 PK', 'type_merk' => 'Panasonic CS-PN12RKJ', 'satuan' => 'Unit', 'jumlah_unit' => 6, 'harga_satuan' => 5200000],
            ['nama_barang' => 'Kasur Spring Bed', 'type_merk' => 'Sleep Care Deluxe 160x200', 'satuan' => 'Pcs', 'jumlah_unit' => 10, 'harga_satuan' => 2900000],
            ['nama_barang' => 'Lemari Pakaian 3 Pintu', 'type_merk' => 'Olympic Classic Series', 'satuan' => 'Pcs', 'jumlah_unit' => 8, 'harga_satuan' => 3200000],
            ['nama_barang' => 'TV LED 43 inch', 'type_merk' => 'Sharp Aquos LC-43SA5500X', 'satuan' => 'Unit', 'jumlah_unit' => 4, 'harga_satuan' => 4500000],
        ];

        foreach ($rabMess as $item) {
            Capex::create(array_merge($item, [
                'tahun'         => 2026,
                'jenis_capex'   => 'mess',
                'jenis_catatan' => 'rab',
                'total_rab'     => $item['jumlah_unit'] * $item['harga_satuan'],
            ]));
        }

        // --- Realisasi Mess 2026 ---
        $realisasiMess = [
            ['tanggal' => '2026-03-05', 'nama_barang' => 'AC Split 1.5 PK', 'type_merk' => 'Panasonic CS-PN12RKJ', 'satuan' => 'Unit', 'nama_transaksi' => 'Pembelian & Pemasangan AC Mess Blok A', 'debit' => 18720000, 'kredit' => 0],
            ['tanggal' => '2026-05-18', 'nama_barang' => 'Kasur Spring Bed', 'type_merk' => 'Sleep Care Deluxe', 'satuan' => 'Pcs', 'nama_transaksi' => 'Pembelian Kasur Mess Renovasi Blok B', 'debit' => 17400000, 'kredit' => 0],
        ];

        foreach ($realisasiMess as $item) {
            Capex::create(array_merge($item, [
                'tahun'         => 2026,
                'jenis_capex'   => 'mess',
                'jenis_catatan' => 'realisasi',
            ]));
        }
    }
}
