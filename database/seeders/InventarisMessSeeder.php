<?php

namespace Database\Seeders;

use App\Models\Administrasi\Barang;
use App\Models\Administrasi\Pembelian;
use App\Models\Administrasi\StockOpname;
use Illuminate\Database\Seeder;

class InventarisMessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Seed Barang Mess
        $barangData = [
            [
                'nama_asset' => 'Tempat Tidur',
                'lokasi' => 'Mess',
                'satuan' => 'Unit',
                'keterangan' => 'Kamar hunian peserta & instruktur',
            ],
            [
                'nama_asset' => 'Lemari',
                'lokasi' => 'Mess',
                'satuan' => 'Unit',
                'keterangan' => 'Lemari pakaian 2 pintu',
            ],
            [
                'nama_asset' => 'AC',
                'lokasi' => 'Mess',
                'satuan' => 'Unit',
                'keterangan' => 'Pendingin kamar mess',
            ],
        ];

        $createdBarang = [];
        foreach ($barangData as $item) {
            $createdBarang[$item['nama_asset']] = Barang::firstOrCreate(
                ['nama_asset' => $item['nama_asset'], 'lokasi' => 'Mess'],
                $item
            );
        }

        // 2. Seed Pembelian Mess
        $pembelianData = [
            [
                'barang_id' => $createdBarang['Tempat Tidur']->id,
                'tanggal_pembelian' => '2024-01-15',
                'jumlah' => 50,
                'keterangan' => 'Pengadaan tempat tidur mess baru',
            ],
            [
                'barang_id' => $createdBarang['Lemari']->id,
                'tanggal_pembelian' => '2024-01-20',
                'jumlah' => 30,
                'keterangan' => 'Pengadaan lemari kamar',
            ],
            [
                'barang_id' => $createdBarang['AC']->id,
                'tanggal_pembelian' => '2024-02-01',
                'jumlah' => 10,
                'keterangan' => 'Pengadaan unit AC kamar mess',
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

        // 3. Seed Stock Opname Mess
        $so = [
            [
                'barang_id' => $createdBarang['Tempat Tidur']->id,
                'tanggal_stock_opname' => '2025-11-15',
                'status' => 'Selesai',
                'catatan_sesi' => 'Stock opname fasilitas mess.',
                'jumlah_baik' => 48,
                'jumlah_rusak_ringan' => 2,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
                'grand_total' => 50,
                'keterangan' => '-',
            ],
            [
                'barang_id' => $createdBarang['Lemari']->id,
                'tanggal_stock_opname' => '2025-11-15',
                'status' => 'Selesai',
                'catatan_sesi' => 'Stock opname fasilitas mess.',
                'jumlah_baik' => 25,
                'jumlah_rusak_ringan' => 5,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
                'grand_total' => 30,
                'keterangan' => '-',
            ],
            [
                'barang_id' => $createdBarang['AC']->id,
                'tanggal_stock_opname' => '2025-11-15',
                'status' => 'Selesai',
                'catatan_sesi' => 'Stock opname fasilitas mess.',
                'jumlah_baik' => 9,
                'jumlah_rusak_ringan' => 1,
                'jumlah_rusak_berat' => 0,
                'jumlah_hilang' => 0,
                'grand_total' => 10,
                'keterangan' => '1 unit servis rutin',
            ],
        ];

        foreach ($so as $item) {
            StockOpname::firstOrCreate(
                [
                    'barang_id' => $item['barang_id'],
                    'tanggal_stock_opname' => $item['tanggal_stock_opname'],
                ],
                $item
            );
        }
    }
}
