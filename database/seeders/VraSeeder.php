<?php

namespace Database\Seeders;

use App\Models\Administrasi\Vra;
use Illuminate\Database\Seeder;

class VraSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vra::truncate();

        $vehicles = [
            [
                'nomor_polisi' => 'B 1234 ABC',
                'kendaraan' => 'Toyota Hilux Double Cabin',
                'upah_tahun' => 45000000,
                'BBM_tahun' => 12500000,
                'preventif_maintenance_tahun' => 8000000,
                'biaya_lainnya_tahun' => 1200000,
                'total_biaya_tahun' => 66700000,
                'KM_tahun' => 24500,
                // Monthly average
                'upah_bulan' => 3750000,
                'BBM_bulan' => 1041666.67,
                'preventif_maintenance_bulan' => 666666.67,
                'biaya_lainnya_bulan' => 100000,
                'total_biaya_bulan' => 5558333.34,
                'KM_awal_base' => 10000,
                'KM_step' => 2041.67,
            ],
            [
                'nomor_polisi' => 'B 5678 DEF',
                'kendaraan' => 'Mitsubishi Triton',
                'upah_tahun' => 45000000,
                'BBM_tahun' => 14200000,
                'preventif_maintenance_tahun' => 9500000,
                'biaya_lainnya_tahun' => 800000,
                'total_biaya_tahun' => 69500000,
                'KM_tahun' => 28100,
                // Monthly average
                'upah_bulan' => 3750000,
                'BBM_bulan' => 1183333.33,
                'preventif_maintenance_bulan' => 791666.67,
                'biaya_lainnya_bulan' => 66666.67,
                'total_biaya_bulan' => 5791666.67,
                'KM_awal_base' => 25000,
                'KM_step' => 2341.67,
            ],
            [
                'nomor_polisi' => 'BM 9012 GHI',
                'kendaraan' => 'Isuzu D-Max',
                'upah_tahun' => 42000000,
                'BBM_tahun' => 11800000,
                'preventif_maintenance_tahun' => 7200000,
                'biaya_lainnya_tahun' => 2100000,
                'total_biaya_tahun' => 63100000,
                'KM_tahun' => 22400,
                // Monthly average
                'upah_bulan' => 3500000,
                'BBM_bulan' => 983333.33,
                'preventif_maintenance_bulan' => 600000,
                'biaya_lainnya_bulan' => 175000,
                'total_biaya_bulan' => 5258333.33,
                'KM_awal_base' => 40000,
                'KM_step' => 1866.67,
            ],
        ];

        // Seed for 2026 across months 1 to 12
        foreach ($vehicles as $v) {
            for ($month = 1; $month <= 12; $month++) {
                $kmAwal = $v['KM_awal_base'] + (($month - 1) * $v['KM_step']);
                $kmAkhir = $kmAwal + $v['KM_step'];
                $kmBulan = $v['KM_step'];

                Vra::create([
                    'nomor_polisi' => $v['nomor_polisi'],
                    'kendaraan' => $v['kendaraan'],
                    'bulan' => $month,
                    'tahun' => 2026,
                    'upah_bulan' => $v['upah_bulan'],
                    'upah_tahun' => $v['upah_tahun'],
                    'BBM_bulan' => $v['BBM_bulan'],
                    'BBM_tahun' => $v['BBM_tahun'],
                    'preventif_maintenance_bulan' => $v['preventif_maintenance_bulan'],
                    'preventif_maintenance_tahun' => $v['preventif_maintenance_tahun'],
                    'biaya_lainnya_bulan' => $v['biaya_lainnya_bulan'],
                    'biaya_lainnya_tahun' => $v['biaya_lainnya_tahun'],
                    'total_biaya_bulan' => $v['total_biaya_bulan'],
                    'total_biaya_tahun' => $v['total_biaya_tahun'],
                    'KM_awal' => $kmAwal,
                    'KM_akhir' => $kmAkhir,
                    'KM_bulan' => $kmBulan,
                    'KM_tahun' => $v['KM_tahun'],
                ]);
            }
        }
    }
}
