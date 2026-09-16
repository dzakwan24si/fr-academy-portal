<?php

namespace Tests\Feature;

use App\Models\Cost\Vra;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class VraTest extends TestCase
{
    use DatabaseTransactions;
    protected function setUp(): void
    {
        parent::setUp();
        Vra::query()->delete();

        $records = [
            [
                'vra_id' => 1,
                'nomor_polisi' => 'Hilux BM 8177 QG',
                'kendaraan' => 'Mobil Pool/Hilux',
                'tahun' => 2026,
                'bulan' => 5,
                'upah' => 4723210,
                'BBM' => 1122000,
                'BBM_tahun' => 3222000,
                'preventif_maintenance' => 2504820,
                'biaya_lainnya' => 6419500,
                'total_biaya_bulan' => 14769530,
                'KM_awal' => 58135,
                'KM_akhir' => 59661,
                'KM' => 1526,
                'BBM_liter' => 165,
                'BBM_KM_liter' => 9.24848484848485,
                'VRA' => 9679,
                'budget_tahun' => 3120,
            ],
            [
                'vra_id' => 2,
                'nomor_polisi' => 'Hilux BM 8177 QG',
                'kendaraan' => 'Mobil Pool/Hilux',
                'tahun' => 2026,
                'bulan' => 6,
                'upah' => 4818210,
                'BBM' => 1050200,
                'BBM_tahun' => 1050200,
                'preventif_maintenance' => 0,
                'biaya_lainnya' => 44000,
                'total_biaya_bulan' => 5912410,
                'KM_awal' => 59661,
                'KM_akhir' => 61292,
                'KM' => 1631,
                'BBM_liter' => 154.44,
                'BBM_KM_liter' => 10.5607355607356,
                'VRA' => 3625,
                'budget_tahun' => 3120,
            ],
        ];

        foreach ($records as $item) {
            Vra::create($item);
        }
    }

    public function test_can_view_vra_overall_page()
    {
        $response = $this->get('/administrasi/vra');
        $response->assertStatus(200);
        $response->assertSee('VRA (Vehicle Running Account)');
        $response->assertSee('Hilux BM 8177 QG');
        // Total Biaya Hilux (Mei + Jun): 14,769,530 + 5,912,410 = 20,681,940
        // Total KM Hilux (Mei + Jun): 1,526 + 1,631 = 3,157
        // VRA Overall = 20,681,940 / 3,157 = 6,551
        $response->assertSee('6.551');
    }

    public function test_can_view_vra_monthly_page()
    {
        $response = $this->get('/administrasi/vra?year=2026&period=5');
        $response->assertStatus(200);
        $response->assertSee('Data Rincian VRA');
        $response->assertSee('Hilux BM 8177 QG');
    }

    public function test_can_create_vra_record()
    {
        $payload = [
            'nomor_polisi' => 'BK 9999 ZZ',
            'kendaraan' => 'Ford Ranger Double Cabin',
            'tahun' => 2026,
            'bulan' => 5,
            'upah' => 4000000,
            'BBM' => 1500000,
            'BBM_tahun' => 18000000,
            'preventif_maintenance' => 500000,
            'biaya_lainnya' => 200000,
            'KM_awal' => 10000,
            'KM_akhir' => 12500,
            'KM' => 2500,
            'BBM_liter' => 250,
            'budget_tahun' => 30000,
        ];

        $response = $this->post('/administrasi/vra', $payload);
        $response->assertRedirect('/administrasi/vra?year=2026&period=5');

        $this->assertDatabaseHas('vra', [
            'nomor_polisi' => 'BK 9999 ZZ',
            'tahun' => 2026,
            'bulan' => 5,
            'total_biaya_bulan' => 6200000,
            'KM' => 2500,
            'BBM_KM_liter' => 10,
            'VRA' => 2480, // 6200000 / 2500 = 2480
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
            'upah' => 5000000,
            'BBM' => 1000000,
            'BBM_tahun' => 12000000,
            'preventif_maintenance' => 500000,
            'biaya_lainnya' => 100000,
            'KM_awal' => 5000,
            'KM_akhir' => 7000,
            'KM' => 2000,
            'BBM_liter' => 200,
            'budget_tahun' => 24000,
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('vra', [
            'vra_id' => $vra->vra_id,
            'kendaraan' => 'Updated Vehicle Name',
            'total_biaya_bulan' => 6600000,
            'VRA' => 3300, // 6600000 / 2000
        ]);
    }

    public function test_can_export_report()
    {
        $response = $this->get('/administrasi/vra/export/report?year=2026&period=overall');
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="VRA_Report_2026_Overall.xlsx"');
    }

    public function test_can_download_template()
    {
        $response = $this->get('/administrasi/vra/template/download');
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="Template_Import_VRA.xlsx"');
    }

    public function test_can_import_csv()
    {
        $csvContent = "vra_id,nomor_polisi,kendaraan,bulan,upah,BBM,BBM_tahun,preventif_maintenance,biaya_lainnya,total_biaya_bulan,KM_awal,KM_akhir,KM,BBM_liter,BBM_KM_liter,VRA,budget_tahun\n"
                    . "99,Hilux Test Import,Mobil Test,5,4723210,1122000,3222000,2504820,6419500,14769530,58135,59661,1526,165,9.25,9679,3120\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('import.csv', $csvContent);

        $response = $this->post('/administrasi/vra/import', [
            'csv_file' => $file,
            'import_mode' => 'append',
            'year' => 2026,
            'period' => 5,
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('vra', [
            'nomor_polisi' => 'Hilux Test Import',
            'tahun' => 2026,
            'bulan' => 5,
            'total_biaya_bulan' => 14769530,
            'KM' => 1526,
        ]);
    }
}
