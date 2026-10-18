<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\AsistenProgramMonitoring;

$progs = AsistenProgramMonitoring::where('kode_kategori', 'A')->orderBy('id')->get();
echo "Total FAT in DB: " . $progs->count() . "\n";
foreach ($progs as $p) {
    if ($p->id >= 60) {
        echo "ID: {$p->id} | No: {$p->no} | {$p->nama_program} | R={$p->tahun_rekrut}, T={$p->tahun_training}, L={$p->tahun_lulus} | Trainee={$p->jumlah_trainee}, Culled={$p->jumlah_culled}\n";
    }
}
