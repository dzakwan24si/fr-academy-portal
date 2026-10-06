<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Program\ProgramReguler;
use App\Models\Program\TrainingRecord;
use App\Http\Controllers\Api\V1\TrainingRecordExportController;

DB::transaction(function () {
    $prog = ProgramReguler::create([
        'jenis_program_reguler' => 'Staf',
        'Training_Program' => 'Basic Leadership Development Program',
        'Batch' => 'Batch 1',
        'Lokasi_training' => 'Training Center Riau',
        'Mulai_program' => '2026-01-10',
        'Selesai_program' => '2026-01-20',
        'Total_training_days' => 80,
        'target_peserta' => 10,
    ]);

    $rec = TrainingRecord::create([
        'program_type' => 'reguler',
        'program_id' => $prog->Program_reguler_id,
        'participant_identifier' => '2007030015',
        'participant_name' => 'Budi Pratama',
        'gender' => 'Male',
        'company' => 'PT FR',
        'department' => 'Agronomy',
        'work_location' => 'Kebun Sejahtera',
        'group_name' => 'FR',
        'region' => 'Riau',
        'gol' => 'III',
        'position' => 'Asisten Agronomy',
        'bulan' => 'JAN',
        'tahun' => '2026',
        'jml_hari' => '10',
        'total_jam' => '80', // Kolom total_jam baru
        'instructor' => 'Budi Santoso',
        'internal_external' => 'Internal',
        'level' => 'Staff',
        'periode' => 'Q1',
        'status' => 'valid',
    ]);

    $controller = new TrainingRecordExportController();
    $request = Illuminate\Http\Request::create('/api/v1/training-records/export', 'GET', ['since' => '2026-10-06']);
    $response = json_decode($controller->export($request)->getContent(), true);

    echo "--- SAMPLE RESPONSE ROW FOR REGULER ---\n";
    echo json_encode($response['data'][0], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    DB::rollBack();
});
