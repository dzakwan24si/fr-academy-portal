<?php

namespace Tests\Feature;

use App\Models\Administrasi\BiayaRekruit;
use Tests\TestCase;

class BiayaRekruitTest extends TestCase
{
    public function test_can_view_biaya_rekruit_page()
    {
        $response = $this->get('/administrasi/biaya-rekruit');
        $response->assertStatus(200);
        $response->assertSee('Biaya Rekruit');
        $response->assertSee('Ticket Pekanbaru - Jakarta');
    }

    public function test_can_create_biaya_rekruit()
    {
        $payload = [
            'tahun' => 2026,
            'tanggal' => '2026-08-25',
            'nama' => 'Biaya Psikotes & Interview Batch 4',
            'total_biaya' => 4500000,
            'jenis_rekruit' => 'Regular Staff',
            'penanggung_jawab' => 'HR Talent Acquisition',
        ];

        $response = $this->post('/administrasi/biaya-rekruit', $payload);
        $response->assertRedirect('/administrasi/biaya-rekruit?year=2026');

        $this->assertDatabaseHas('biaya_rekruit', [
            'nama' => 'Biaya Psikotes & Interview Batch 4',
            'tahun' => 2026,
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
        $response = $this->get('/administrasi/biaya-rekruit/export/report?year=2026');
        $response->assertStatus(200);
        $response->assertHeader('Content-Disposition', 'attachment; filename="Biaya_Rekruit_Report_2026.csv"');
    }
}
