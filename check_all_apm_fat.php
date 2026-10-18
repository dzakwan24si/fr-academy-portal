<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\AsistenProgramMonitoring;

$allA = AsistenProgramMonitoring::where('kode_kategori', 'A')->get();
echo "Total Category A in AsistenProgramMonitoring: " . $allA->count() . "\n";
foreach ($allA as $a) {
    if ($a->no >= 60 || $a->id >= 60) {
        echo "ID: {$a->id}, No: {$a->no}, Name: '{$a->nama_program}', R: {$a->tahun_rekrut}, T: {$a->tahun_training}, L: {$a->tahun_lulus}, Trainee: {$a->jumlah_trainee}, Culled: {$a->jumlah_culled}\n";
    }
}
