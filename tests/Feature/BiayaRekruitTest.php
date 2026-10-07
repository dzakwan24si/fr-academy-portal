<?php

namespace Tests\Feature;

use App\Models\Cost\BiayaRekruit;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class BiayaRekruitTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
        BiayaRekruit::query()->delete();

        BiayaRekruit::create([
            'tahun' => 2026,
            'tanggal' => '2026-08-05',
            'nama' => 'Ticket Pekanbaru - Jakarta',
            'total_biaya' => 3250000,
            'jenis_rekruit' => 'REKRUT FAT II 2026',
            'penanggung_jawab' => 'HR Recruiter Lead',
        ]);
    }

    public function test_can_view_biaya_rekruit_page()
    {
        $response = $this->get('/administrasi/biaya-rekruit');
        $response->assertStatus(200);
        $response->assertSee('Biaya Rekruit');
        $response->assertSee('REKRUT FAT II 2026');
    }

    public function test_can_create_biaya_rekruit()
    {
        $payload = [
            'tanggal' => '2026-08-25',
            'nama' => 'Biaya Psikotes & Interview Batch 4',
            'total_biaya' => 4500000,
            'jenis_rekruit' => 'Regular Staff',
            'penanggung_jawab' => 'HR Talent Acquisition',
        ];

        $response = $this->post('/administrasi/biaya-rekruit', $payload);
        $response->assertRedirect('/administrasi/biaya-rekruit');

        $this->assertDatabaseHas('biaya_rekruit', [
            'nama' => 'Biaya Psikotes & Interview Batch 4',
            'total_biaya' => 4500000,
        ]);
    }

    public function test_can_show_and_update_biaya_rekruit()
    {
        $record = BiayaRekruit::first();

        // Show endpoint
        $showResponse = $this->getJson("/administrasi/biaya-rekruit/{$record->id_biaya_rekruit}");
        $showResponse->assertStatus(200)
            ->assertJsonPath('data.nama', $record->nama);

        // Update endpoint
        $updateResponse = $this->put("/administrasi/biaya-rekruit/{$record->id_biaya_rekruit}", [
            'tanggal' => '2026-08-06',
            'nama' => 'Ticket Pekanbaru - Jakarta (Revisi)',
            'total_biaya' => 3800000,
            'jenis_rekruit' => 'Executive',
            'penanggung_jawab' => 'HR Senior Lead',
        ]);

        $updateResponse->assertSessionHas('success');
        $this->assertDatabaseHas('biaya_rekruit', [
            'id_biaya_rekruit' => $record->id_biaya_rekruit,
            'nama' => 'Ticket Pekanbaru - Jakarta (Revisi)',
            'total_biaya' => 3800000,
        ]);
    }

    public function test_can_export_biaya_rekruit()
    {
        $response = $this->get('/administrasi/biaya-rekruit/export/report');
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="Biaya_Rekruit_Report_'.date('Y-m-d').'.xlsx"');
    }

    public function test_can_download_template()
    {
        $response = $this->get('/administrasi/biaya-rekruit/template/download');
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="Template_Import_Biaya_Rekruit.xlsx"');
    }

    public function test_can_filter_by_month()
    {
        $responseAugust = $this->get('/administrasi/biaya-rekruit?year=2026&month=8');
        $responseAugust->assertStatus(200);
        $responseAugust->assertSee('Ticket Pekanbaru - Jakarta');

        $responseMay = $this->get('/administrasi/biaya-rekruit?year=2026&month=5');
        $responseMay->assertStatus(200);
        $responseMay->assertDontSee('Ticket Pekanbaru - Jakarta');
    }

    public function test_can_import_csv()
    {
        $csvContent = "cost_rekruit_id,tanggal,nama,pic_rekruiter,total_biaya\n"
                    ."999,2026-06-30,TEST IMPORT CSV BIAYA REKRUIT MCU,RS Prima Test,850000\n";

        $file = UploadedFile::fake()->createWithContent('biaya_rekruit.csv', $csvContent);

        $response = $this->post('/administrasi/biaya-rekruit/import', [
            'csv_file' => $file,
            'import_mode' => 'append',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('biaya_rekruit', [
            'nama' => 'TEST IMPORT CSV BIAYA REKRUIT MCU',
            'penanggung_jawab' => 'RS Prima Test',
            'total_biaya' => 850000,
            'jenis_rekruit' => 'Medical Check Up (MCU) Calon Karyawan',
        ]);
    }
}
