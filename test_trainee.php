<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$rows = \App\Models\Monitoring\AsistenProgramMonitoring::where('kategori_program', 'like', '%Field Assistant%')
    ->orWhere('nama_program', 'like', '%Field Assistant%')
    ->get();

foreach ($rows as $r) {
    echo "ID: {$r->id}, No: {$r->no}, Nama: {$r->nama_program}, Rekrut: {$r->tahun_rekrut}, Training: {$r->tahun_training}, Lulus: {$r->tahun_lulus}, Trainee: {$r->jumlah_trainee}, Culled: {$r->jumlah_culled}\n";
}
