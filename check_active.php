<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\Asisten;
use App\Models\Monitoring\AsistenProgramMonitoring;

echo "Keterangan breakdown:\n";
$byKet = Asisten::selectRaw('keterangan, count(*) as cnt')->groupBy('keterangan')->get();
foreach ($byKet as $b) {
    echo "  {$b->keterangan}: {$b->cnt}\n";
}

echo "\nActive with level_jabatan breakdown:\n";
$byLvl = Asisten::where('keterangan', 'Aktif')->selectRaw('level_jabatan, count(*) as cnt')->groupBy('level_jabatan')->get();
foreach ($byLvl as $b) {
    echo "  " . ($b->level_jabatan ?? 'NULL') . ": {$b->cnt}\n";
}

echo "\nActive with non-empty tanggal_resign:\n";
echo Asisten::where('keterangan', 'Aktif')->whereNotNull('tanggal_resign')->where('tanggal_resign', '!=', '')->count() . "\n";
