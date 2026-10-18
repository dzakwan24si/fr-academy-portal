<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\AsistenProgramMonitoring;

$progs = AsistenProgramMonitoring::where('jumlah_trainee', 34)->get();
echo "Programs with jumlah_trainee = 34:\n";
foreach ($progs as $p) {
    echo "ID: {$p->id} | {$p->kode_kategori} | {$p->nama_program} | Trainee: {$p->jumlah_trainee}, Culled: {$p->jumlah_culled}, R={$p->tahun_rekrut}, T={$p->tahun_training}, L={$p->tahun_lulus}\n";
}
