<?php

namespace Database\Seeders;

use App\Models\Administrasi\Barang;
use App\Models\Administrasi\Pembelian;
use App\Models\Administrasi\StockOpname;
use Illuminate\Database\Seeder;

class InventarisGudangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Barang Gudang
        $barangData = [
            [
                'nama_asset' => 'Laptop',
                'lokasi' => 'Gudang',
                'satuan' => 'Unit',
                'keterangan' => 'Digunakan untuk kegiatan praktek',
            ],
            [
                'nama_asset' => 'Printer',
                'lokasi' => 'Gudang',
                'satuan' => 'Unit',
                'keterangan' => '-',
            ],
            [
                'nama_asset' => 'Proyektor',
                'lokasi' => 'Gudang',
                'satuan' => 'Unit',
                'keterangan' => 'Ruang training',
            ],
            [
                'nama_asset' => 'Kursi',
                'lokasi' => 'Gudang',
                'satuan' => 'Buah',
                'keterangan' => '-',
            ],
            [
                'nama_asset' => 'Meja Training',
                'lokasi' => 'Gudang',
                'satuan' => 'Buah',
                'keterangan' => 'Ruang kelas A & B',
            ],
            [
                'nama_asset' => 'Kabel Roll',
                'lokasi' => 'Gudang',
                'satuan' => 'Unit',
                'keterangan' => 'Cadangan inventaris',
            ],
        ];

        $createdBarang = [];
        foreach ($barangData as $item) {
            $createdBarang[$item['nama_asset']] = Barang::firstOrCreate(
                ['nama_asset' => $item['nama_asset'], 'lokasi' => 'Gudang'],
                $item
            );
        }

        // 2. Seed Pembelian
        $pembelianData = [
            [
                'barang_id' => $createdBarang['Laptop']->id,
                'tanggal_pembelian' => '2025-11-12',
                'jumlah' => 10,
                'keterangan' => 'Pengadaan staff baru',
            ],
            [
                'barang_id' => $createdBarang['Printer']->id,
                'tanggal_pembelian' => '2025-09-20',
                'jumlah' => 5,
                'keterangan' => '-',
            ],
            [
                'barang_id' => $createdBarang['Proyektor']->id,
                'tanggal_pembelian' => '2024-04-15',
                'jumlah' => 3,
                'keterangan' => 'Penggantian unit lama',
            ],
            [
                'barang_id' => $createdBarang['Kursi']->id,
                'tanggal_pembelian' => '2024-02-10',
                'jumlah' => 20,
                'keterangan' => 'Penambahan kapasitas',
            ],
            [
                'barang_id' => $createdBarang['Laptop']->id,
                'tanggal_pembelian' => '2026-01-10',
                'jumlah' => 5,
                'keterangan' => 'Batch pengadaan 2026',
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

        // 3. Seed Stock Opname Sessions
        // Session 1: 12 November 2025 (Selesai)
        $so1 = [
            [
                'barang_id' => $createdBarang['Laptop']->id,
                'tanggal_stock_opname' => '2025-11-12',
                'status' => 'Selesai',
                'catatan_sesi' => '25 jenis aset diperiksa. Selisih tercatat pada 2 item consumable.',
                'jumlah_baik' => 17,
                'jumlah_rusak_ringan' => 2,
                'jumlah_rusak_berat' => 1,
                'jumlah_hilang' => 0,
                'grand_total' => 20,
                'keterangan' => 'Layar laptop rusak, keyboard beberapa tombol tidak fungsi.',
            ],
            [
                'barang_id' => $createdBarang['Printer']->id,
                'tanggal_stock_opname' => '2025-11-12',
                'status' => 'Selesai',
                'catatan_sesi' => '25 jenis aset diperiksa. Selisih tercatat pada 2 item consumable.',
                'jumlah_baik' => 8,
                'jumlah_rusak_ringan' => 1,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
                'grand_total' => 9,
                'keterangan' => '-',
            ],
            [
                'barang_id' => $createdBarang['Proyektor']->id,
                'tanggal_stock_opname' => '2025-11-12',
                'status' => 'Selesai',
                'catatan_sesi' => '25 jenis aset diperiksa. Selisih tercatat pada 2 item consumable.',
                'jumlah_baik' => 5,
                'jumlah_rusak_ringan' => 0,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
                'grand_total' => 5,
                'keterangan' => 'Kondisi prima',
            ],
            [
                'barang_id' => $createdBarang['Kursi']->id,
                'tanggal_stock_opname' => '2025-11-12',
                'status' => 'Selesai',
                'catatan_sesi' => '25 jenis aset diperiksa. Selisih tercatat pada 2 item consumable.',
                'jumlah_baik' => 45,
                'jumlah_rusak_ringan' => 3,
                'jumlah_rusak_berat' => 2,
                'jumlah_hilang' => 0,
                'grand_total' => 50,
                'keterangan' => 'Beberapa baut kendor',
            ],
        ];

        // Session 2: 05 Agustus 2025 (Selesai)
        $so2 = [
            [
                'barang_id' => $createdBarang['Laptop']->id,
                'tanggal_stock_opname' => '2025-08-05',
                'status' => 'Selesai',
                'catatan_sesi' => '120 jenis aset diperiksa. Audit kuartal Q3 berjalan lancar tanpa selisih major.',
                'jumlah_baik' => 18,
                'jumlah_rusak_ringan' => 2,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
                'grand_total' => 20,
                'keterangan' => 'Semua unit telah dicek',
            ],
            [
                'barang_id' => $createdBarang['Printer']->id,
                'tanggal_stock_opname' => '2025-08-05',
                'status' => 'Selesai',
                'catatan_sesi' => '120 jenis aset diperiksa. Audit kuartal Q3 berjalan lancar tanpa selisih major.',
                'jumlah_baik' => 9,
                'jumlah_rusak_ringan' => 0,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
                'grand_total' => 9,
                'keterangan' => '-',
            ],
        ];

        // Session 3: 20 Maret 2025 (Draf)
        $so3 = [
            [
                'barang_id' => $createdBarang['Laptop']->id,
                'tanggal_stock_opname' => '2025-03-20',
                'status' => 'Draf',
                'catatan_sesi' => '45 jenis aset diperiksa. Menunggu persetujuan final dari Kepala Gudang.',
                'jumlah_baik' => 15,
                'jumlah_rusak_ringan' => 1,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
                'grand_total' => 16,
                'keterangan' => 'Menunggu verifikasi fisik',
            ],
        ];

        // Session 4: 15 Desember 2024 (Selesai)
        $so4 = [
            [
                'barang_id' => $createdBarang['Laptop']->id,
                'tanggal_stock_opname' => '2024-12-15',
                'status' => 'Selesai',
                'catatan_sesi' => 'Audit akhir tahun. 210 jenis aset diperiksa. Rekonsiliasi selesai.',
                'jumlah_baik' => 15,
                'jumlah_rusak_ringan' => 1,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
                'grand_total' => 16,
                'keterangan' => 'Tutup buku 2024',
            ],
        ];

        foreach (array_merge($so1, $so2, $so3, $so4) as $so) {
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
