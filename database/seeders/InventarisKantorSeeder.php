<?php

namespace Database\Seeders;

use App\Models\Administrasi\Barang;
use App\Models\Administrasi\Pembelian;
use App\Models\Administrasi\StockOpname;
use Illuminate\Database\Seeder;

class InventarisKantorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Barang Kantor
        $barangData = [
            [
                'nama_asset' => 'PC All-in-One Lenovo',
                'lokasi' => 'Kantor',
                'satuan' => 'Unit',
                'keterangan' => 'Komputer kerja staff administrasi & HR',
            ],
            [
                'nama_asset' => 'Kursi Ergonomis',
                'lokasi' => 'Kantor',
                'satuan' => 'Buah',
                'keterangan' => 'Kursi kerja staf kantor pusat',
            ],
            [
                'nama_asset' => 'Meja Kerja Staff',
                'lokasi' => 'Kantor',
                'satuan' => 'Buah',
                'keterangan' => 'Meja partisi 4 workstation',
            ],
            [
                'nama_asset' => 'AC Split 2 PK Daikin',
                'lokasi' => 'Kantor',
                'satuan' => 'Unit',
                'keterangan' => 'Ruang utama kantor & ruang rapat',
            ],
            [
                'nama_asset' => 'Smart TV 65 Inch',
                'lokasi' => 'Kantor',
                'satuan' => 'Unit',
                'keterangan' => 'Ruang Meeting Utama',
            ],
            [
                'nama_asset' => 'Lemari Arsip Besi',
                'lokasi' => 'Kantor',
                'satuan' => 'Unit',
                'keterangan' => 'Penyimpanan berkas HR & Finance',
            ],
        ];

        $createdBarang = [];
        foreach ($barangData as $item) {
            $createdBarang[$item['nama_asset']] = Barang::firstOrCreate(
                ['nama_asset' => $item['nama_asset'], 'lokasi' => 'Kantor'],
                $item
            );
        }

        // 2. Seed Pembelian Kantor
        $pembelianData = [
            [
                'barang_id' => $createdBarang['PC All-in-One Lenovo']->id,
                'tanggal_pembelian' => '2025-10-15',
                'jumlah' => 8,
                'keterangan' => 'Upgrade komputer tim HR & Accounting',
            ],
            [
                'barang_id' => $createdBarang['Kursi Ergonomis']->id,
                'tanggal_pembelian' => '2025-08-10',
                'jumlah' => 12,
                'keterangan' => 'Penggantian kursi rusak',
            ],
            [
                'barang_id' => $createdBarang['AC Split 2 PK Daikin']->id,
                'tanggal_pembelian' => '2024-06-20',
                'jumlah' => 2,
                'keterangan' => 'Pemasangan ruang rapat baru',
            ],
            [
                'barang_id' => $createdBarang['Meja Kerja Staff']->id,
                'tanggal_pembelian' => '2024-03-12',
                'jumlah' => 6,
                'keterangan' => 'Pengadaan meja kantor',
            ],
        ];

        foreach ($pembelianData as $pem) {
            Pembelian::firstOrCreate(
                [
                    'barang_id' => $pem['barang_id'],
                    'tanggal_pembelian' => $pem['tanggal_pembelian'],
                ],
                $pem
            );
        }

        // 3. Seed Stock Opname Sessions Kantor
        // Session 1: 18 November 2025 (Selesai)
        $so1 = [
            [
                'barang_id' => $createdBarang['PC All-in-One Lenovo']->id,
                'tanggal_stock_opname' => '2025-11-18',
                'status' => 'Selesai',
                'catatan_sesi' => 'Audit berkala aset operasional dan perlengkapan kerja kantor November 2025.',
                'jumlah_baik' => 7,
                'jumlah_rusak_ringan' => 1,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
                'grand_total' => 8,
                'keterangan' => '1 PC power supply drop, sedang diajukan perbaikan.',
            ],
            [
                'barang_id' => $createdBarang['Kursi Ergonomis']->id,
                'tanggal_stock_opname' => '2025-11-18',
                'status' => 'Selesai',
                'catatan_sesi' => 'Audit berkala aset operasional dan perlengkapan kerja kantor November 2025.',
                'jumlah_baik' => 10,
                'jumlah_rusak_ringan' => 2,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
                'grand_total' => 12,
                'keterangan' => 'Hidrolik 2 kursi agak kendor.',
            ],
            [
                'barang_id' => $createdBarang['Smart TV 65 Inch']->id,
                'tanggal_stock_opname' => '2025-11-18',
                'status' => 'Selesai',
                'catatan_sesi' => 'Audit berkala aset operasional dan perlengkapan kerja kantor November 2025.',
                'jumlah_baik' => 1,
                'jumlah_rusak_ringan' => 0,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
                'grand_total' => 1,
                'keterangan' => 'Kondisi baik di ruang meeting.',
            ],
        ];

        // Session 2: 14 Juli 2025 (Selesai)
        $so2 = [
            [
                'barang_id' => $createdBarang['PC All-in-One Lenovo']->id,
                'tanggal_stock_opname' => '2025-07-14',
                'status' => 'Selesai',
                'catatan_sesi' => 'Stock opname semester 1 aset kantor.',
                'jumlah_baik' => 8,
                'jumlah_rusak_ringan' => 0,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
                'grand_total' => 8,
                'keterangan' => 'Semua unit berfungsi normal.',
            ],
        ];

        foreach (array_merge($so1, $so2) as $so) {
            StockOpname::firstOrCreate(
                [
                    'barang_id' => $so['barang_id'],
                    'tanggal_stock_opname' => $so['tanggal_stock_opname'],
                ],
                $so
            );
        }
    }
}
