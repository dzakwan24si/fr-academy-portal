<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\Asisten;

$fat = Asisten::where(function($q) {
    $q->where('program', 'like', '%Field Assistant%')
      ->orWhere('program', 'like', '%FAT%')
      ->orWhere('program', 'like', '%Estate Cadet%');
})->get();

$byBatch = [];
foreach ($fat as $a) {
    $p = $a->program;
    if (!isset($byBatch[$p])) {
        $byBatch[$p] = ['trainee' => 0, 'culled' => 0, 'resign' => 0, 'eksis' => 0];
    }
    $byBatch[$p]['trainee']++;
    $ket = strtolower(trim((string)$a->keterangan));
    if ($ket === 'gagal') $byBatch[$p]['culled']++;
    elseif ($ket === 'resign') $byBatch[$p]['resign']++;
    elseif ($ket === 'aktif') $byBatch[$p]['eksis']++;
}

// Print batches that have 2023, 2024, 2025, 2026
foreach ($byBatch as $name => $counts) {
    if (preg_match('/202[3-6]/', $name)) {
        echo "$name => Trainee={$counts['trainee']}, Culled={$counts['culled']}, Resign={$counts['resign']}, Eksis={$counts['eksis']}\n";
    }
}
