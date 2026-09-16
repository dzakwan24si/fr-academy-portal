<?php

namespace Tests\Feature;

use App\Models\Program\ProgramReguler;
use App\Models\Program\RealisasiVsBatch;
use App\Models\Program\TrainingRecord;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class RealisasiVsBatchTest extends TestCase
{
    use RefreshDatabase;

    public function test_realization_uses_training_records_and_prevents_duplicates(): void
    {
        ProgramReguler::create([
            'Program_reguler_id' => 900001,
            'Training_Program' => 'Test Realisasi',
            'Mulai_program' => '2026-07-01',
            'Selesai_program' => '2026-07-31',
            'target_peserta' => 10,
            'tanggal_pelaksanaan' => ['2026-07-01'],
        ]);
        RealisasiVsBatch::create([
            'program_type' => 'reguler',
            'program_id' => 900001,
            'tahun' => 2026,
            'bulan' => 7,
            'legacy_realized_batch' => 0,
            'legacy_realized_participants' => 2,
        ]);
        TrainingRecord::create([
            'program_type' => 'reguler',
            'program_id' => 900001,
            'participant_identifier' => 'P-1',
            'training_date' => '2026-07-01',
            'status' => 'valid',
        ]);
        TrainingRecord::create([
            'program_type' => 'reguler',
            'program_id' => 900001,
            'participant_identifier' => 'P-2',
            'training_date' => '2026-07-02',
            'status' => 'valid',
        ]);

        $response = $this->get('/administrasi/realisasi-vs-batch?year=2026&month=7&type=reguler');

        $response->assertOk()
            ->assertSee('Test Realisasi')
            ->assertSee('2 / 1')
            ->assertSee('20%');

        $this->post('/administrasi/realisasi-vs-batch', [
            'program_type' => 'reguler',
            'program_id' => 900001,
            'tahun' => 2026,
            'bulan' => 7,
        ]);
        $this->post('/administrasi/realisasi-vs-batch', [
            'program_type' => 'reguler',
            'program_id' => 900001,
            'tahun' => 2026,
            'bulan' => 7,
        ]);

        $this->assertSame(1, RealisasiVsBatch::where('program_id', 900001)->count());
    }

    public function test_legacy_csv_import_maps_month_names(): void
    {
        $csv = implode("\n", [
            'Realisasi_ID,Program_id,bulan,REALISASI_BATCH,% BATCH,REALISASI_PESERTA,% PESERTA',
            '1,900002,Juli,1,1,7,1',
        ]);

        $response = $this->post('/administrasi/realisasi-vs-batch/import', [
            'file' => UploadedFile::fake()->createWithContent('realisasi_reguler.csv', $csv),
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('realisasi_vs_batch', [
            'program_type' => 'reguler',
            'program_id' => 900002,
            'tahun' => 2026,
            'bulan' => 7,
            'legacy_realized_participants' => 7,
        ]);
    }
}
