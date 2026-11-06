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
        \App\Models\ActivityLog::query()->delete();

        BiayaRekruit::create([
            'tahun' => 2026,
            'tanggal' => '2026-08-05',
            'nama' => 'Ticket Pekanbaru - Jakarta',
            'total_biaya' => 3250000,
            'jenis_rekruit' => 'REKRUT FAT II 2026',
            'penanggung_jawab' => 'HR Recruiter Lead',
        ]);
        \App\Models\ActivityLog::query()->delete();
    }

    public function test_can_view_biaya_rekruit_page()
    {
        $response = $this->get('/administrasi/biaya-rekruit');
        $response->assertStatus(200);
        $response->assertSee('Biaya Rekruit');
        $response->assertSee('REKRUT FAT II 2026');
        $response->assertSee('Sub Total REKRUT FAT II 2026');
        $response->assertViewHas('subTotalPerJenis');
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

    public function test_can_import_csv_without_id_auto_increment()
    {
        $csvContent = "tanggal,nama,pic_rekruiter,total_biaya\n"
                    ."2026-09-15,BIAYA TRANSPORT CANDIDATE AUTO,HR Team,1250000\n";

        $file = UploadedFile::fake()->createWithContent('biaya_rekruit_noid.csv', $csvContent);

        $response = $this->post('/administrasi/biaya-rekruit/import', [
            'csv_file' => $file,
            'import_mode' => 'append',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('biaya_rekruit', [
            'nama' => 'BIAYA TRANSPORT CANDIDATE AUTO',
            'total_biaya' => 1250000,
        ]);

        $record = BiayaRekruit::where('nama', 'BIAYA TRANSPORT CANDIDATE AUTO')->first();
        $this->assertNotNull($record->id_biaya_rekruit);
        $this->assertGreaterThan(0, $record->id_biaya_rekruit);
    }

    public function test_can_import_csv_with_jenis_biaya_rekruit_column()
    {
        $csvContent = "tanggal,nama,jenis_biaya_rekruit,pic_rekruiter,total_biaya\n"
                    ."2026-10-10,BIAYA ACCOMMODATION TEST,Perjalanan Dinas & Akomodasi Recruiter,HR Lead,950000\n";

        $file = UploadedFile::fake()->createWithContent('biaya_rekruit_with_jenis.csv', $csvContent);

        $response = $this->post('/administrasi/biaya-rekruit/import', [
            'csv_file' => $file,
            'import_mode' => 'append',
        ]);

        $response->assertSessionHas('success');
        $this->assertDatabaseHas('biaya_rekruit', [
            'nama' => 'BIAYA ACCOMMODATION TEST',
            'jenis_rekruit' => 'Perjalanan Dinas & Akomodasi Recruiter',
            'penanggung_jawab' => 'HR Lead',
            'total_biaya' => 950000,
        ]);
    }

    public function test_can_import_csv_with_indonesian_rupiah_currency_formatting()
    {
        $csvContent = "tanggal;nama;jenis_biaya_rekruit;pic_rekruiter;total_biaya\n"
                    ."2026-09-26;FAT II 2027 : Biaya Konsumsi Tim rekrut dalam rangka Rekrut FAT II 2027 & MAT II 2027 di Kampus UNRI An Josefhine;Biaya Lain-lain Rekrut;;Rp 187.338\n"
                    ."2026-05-31;REKRUT FAT II 2026 : REALISASI LPJ AN JOSEFHINE E DINAS REKRUT (Sewa Ruangan Meeting Betaive Coworking);Perjalanan Dinas & Akomodasi Recruiter;Josefhine E;Rp 709.091\n"
                    ."2026-01-31;MAT I 2026 : KLAIM BY PERJDIN AN.BENYAMIN BY DLM RANGKA REKRUT MAT I 2026 & TAT I 2026 KE MEDAN DAN YOGYA;Perjalanan Dinas & Akomodasi Recruiter;Benyamin Basuki Yulianto & Pinondang Lindarto Simanungkalit;Rp 2.460.660\n"
                    ."2026-09-26;FAT II 2027 : Biaya pembelian 2 Dus air minum cup untuk peserta rekrut FAT II 2027 & MAT II 2027 di kampus UNRI An Marojahan;Biaya Lain-lain Rekrut;;Rp 31.846\n";

        $file = UploadedFile::fake()->createWithContent('biaya_rekruit_formatted.csv', $csvContent);

        $response = $this->post('/administrasi/biaya-rekruit/import', [
            'csv_file' => $file,
            'import_mode' => 'append',
        ]);

        $response->assertSessionHas('success');

        $this->assertDatabaseHas('biaya_rekruit', [
            'nama' => 'FAT II 2027 : Biaya Konsumsi Tim rekrut dalam rangka Rekrut FAT II 2027 & MAT II 2027 di Kampus UNRI An Josefhine',
            'total_biaya' => 187338,
        ]);

        $this->assertDatabaseHas('biaya_rekruit', [
            'nama' => 'REKRUT FAT II 2026 : REALISASI LPJ AN JOSEFHINE E DINAS REKRUT (Sewa Ruangan Meeting Betaive Coworking)',
            'total_biaya' => 709091,
        ]);

        $this->assertDatabaseHas('biaya_rekruit', [
            'nama' => 'MAT I 2026 : KLAIM BY PERJDIN AN.BENYAMIN BY DLM RANGKA REKRUT MAT I 2026 & TAT I 2026 KE MEDAN DAN YOGYA',
            'total_biaya' => 2460660,
        ]);

        $this->assertDatabaseHas('biaya_rekruit', [
            'nama' => 'FAT II 2027 : Biaya pembelian 2 Dus air minum cup untuk peserta rekrut FAT II 2027 & MAT II 2027 di kampus UNRI An Marojahan',
            'total_biaya' => 31846,
        ]);
    }
}
