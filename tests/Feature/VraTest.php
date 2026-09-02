<?php

namespace Tests\Feature;

use App\Models\Administrasi\Vra;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VraTest extends TestCase
{
    public function test_can_view_vra_overall_page()
    {
        $response = $this->get('/administrasi/vra');
        $response->assertStatus(200);
        $response->assertSee('Ringkasan Biaya 2026');
        $response->assertSee('B 1234 ABC');
    }

    public function test_can_view_vra_monthly_page()
    {
        $response = $this->get('/administrasi/vra?year=2026&period=8');
        $response->assertStatus(200);
        $response->assertSee('Data VRA');
        $response->assertSee('B 1234 ABC');
    }

    public function test_can_create_vra_record()
    {
        $payload = [
            'nomor_polisi' => 'BK 9999 ZZ',
            'kendaraan' => 'Ford Ranger Double Cabin',
            'tahun' => 2026,
            'bulan' => 8,
            'upah_bulan' => 4000000,
            'upah_tahun' => 48000000,
            'BBM_bulan' => 1500000,
            'BBM_tahun' => 18000000,
            'preventif_maintenance_bulan' => 500000,
            'preventif_maintenance_tahun' => 6000000,
            'biaya_lainnya_bulan' => 200000,
            'biaya_lainnya_tahun' => 2400000,
            'KM_awal' => 10000,
            'KM_akhir' => 12500,
            'KM_bulan' => 2500,
            'KM_tahun' => 30000,
        ];

        $response = $this->post('/administrasi/vra', $payload);
        $response->assertRedirect('/administrasi/vra?year=2026&period=8');

        $this->assertDatabaseHas('vra', [
            'nomor_polisi' => 'BK 9999 ZZ',
            'tahun' => 2026,
            'bulan' => 8,
            'total_biaya_bulan' => 6200000,
            'total_biaya_tahun' => 74400000,
        ]);
    }

    public function test_can_show_and_update_vra_record()
    {
        $vra = Vra::first();

        // Show endpoint
        $showResponse = $this->getJson("/administrasi/vra/{$vra->vra_id}");
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.nomor_polisi', $vra->nomor_polisi);

        // Update endpoint
        $updateResponse = $this->put("/administrasi/vra/{$vra->vra_id}", [
            'nomor_polisi' => $vra->nomor_polisi,
            'kendaraan' => 'Updated Vehicle Name',
            'upah_bulan' => 5000000,
            'upah_tahun' => 60000000,
            'BBM_bulan' => 1000000,
            'BBM_tahun' => 12000000,
            'preventif_maintenance_bulan' => 500000,
            'preventif_maintenance_tahun' => 6000000,
            'biaya_lainnya_bulan' => 100000,
            'biaya_lainnya_tahun' => 1200000,
            'KM_awal' => 5000,
            'KM_akhir' => 7000,
            'KM_bulan' => 2000,
            'KM_tahun' => 24000,
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('vra', [
            'vra_id' => $vra->vra_id,
            'kendaraan' => 'Updated Vehicle Name',
        ]);
    }

    public function test_can_export_report()
    {
        $response = $this->get('/administrasi/vra/export/report?year=2026&period=overall');
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="VRA_Report_2026_Overall.csv"');
    }

    public function test_can_download_template()
    {
        $response = $this->get('/administrasi/vra/template/download');
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="Template_Import_VRA.csv"');
    }

    public function test_can_import_csv()
    {
        $csvContent = "nomor_polisi,kendaraan,tahun,bulan,upah_bulan,BBM_bulan,preventif_maintenance_bulan,biaya_lainnya_bulan,KM_awal,KM_akhir,KM_bulan,upah_tahun,BBM_tahun,preventif_maintenance_tahun,biaya_lainnya_tahun,KM_tahun\n"
                    . "B 9999 CSV,Test Import Vehicle,2026,5,3000000,1000000,500000,100000,1000,2000,1000,36000000,12000000,6000000,1200000,12000\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('import.csv', $csvContent);

        $response = $this->post('/administrasi/vra/import', [
            'csv_file' => $file,
            'import_mode' => 'append',
            'year' => 2026,
            'period' => 5,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('vra', [
            'nomor_polisi' => 'B 9999 CSV',
            'tahun' => 2026,
            'bulan' => 5,
            'total_biaya_bulan' => 4600000,
        ]);
    }
}
