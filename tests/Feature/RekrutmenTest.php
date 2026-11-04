<?php

namespace Tests\Feature;

use App\Models\Administrasi\Rekrutmen;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RekrutmenTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();
        $this->authenticateUser();
    }

    public function test_can_download_rekrutmen_template(): void
    {
        $response = $this->get('/administrasi/rekrutmen/template/download');
        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_can_import_rekrutmen_without_no_auto_increments(): void
    {
        $csv = implode("\n", [
            'LOKASI REKRUT,METODE REKRUTMEN,PROGRAM,TANGGAL,TAHUN,JUMLAH PESERTA HADIR,JUMLAH LOLOS PSIKOTEST,JUMLAH HADIR INTERVIEW AWAL,JUMLAH LOLOS INTERVIEW AWAL,JUMLAH LOLOS INTERVIEW USER,JUMLAH IKUT MCU,JUMLAH LOLOS MCU,JOIN FR',
            'Bandung,Offline,FAT Batch 3,2026-07-10,2026,120,60,55,30,20,15,12,12',
        ]);

        $file = UploadedFile::fake()->createWithContent('rekrutmen_noid.csv', $csv);

        $response = $this->post('/administrasi/rekrutmen/api/import', [
            'file' => $file,
            'mode' => 'append',
        ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('rekrutmen', [
            'lokasi_rekrut' => 'Bandung',
            'program' => 'FAT Batch 3',
        ]);

        $row = Rekrutmen::where('lokasi_rekrut', 'Bandung')->where('program', 'FAT Batch 3')->first();
        $this->assertNotNull($row->no);
        $this->assertGreaterThan(0, $row->no);
        $this->assertNotNull($row->id);
        $this->assertGreaterThan(0, $row->id);
    }
}
