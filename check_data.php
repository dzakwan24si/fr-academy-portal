<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\Asisten;
use App\Models\Monitoring\AsistenProgramMonitoring;
use Illuminate\Support\Facades\Schema;

echo "Asisten count: " . Asisten::count() . "\n";
echo "Programs count: " . AsistenProgramMonitoring::count() . "\n";

$sample = Asisten::whereNotNull('level_jabatan')->limit(10)->get(['id', 'nama', 'program', 'level_jabatan', 'jabatan_terakhir', 'keterangan', 'status_resign', 'tanggal_resign']);
echo "Sample with level_jabatan:\n";
foreach ($sample as $s) {
    echo "ID: {$s->id} | {$s->nama} | {$s->program} | LVL: {$s->level_jabatan} | JAB: {$s->jabatan_terakhir} | KET: {$s->keterangan} | RES: {$s->status_resign} | TGL_RES: {$s->tanggal_resign}\n";
}

$sampleNullLvl = Asisten::whereNull('level_jabatan')->orWhere('level_jabatan', '')->orWhere('level_jabatan', '-')->count();
echo "Count of null/empty level_jabatan: {$sampleNullLvl}\n";

$keteranganValues = Asisten::distinct()->pluck('keterangan')->toArray();
echo "Distinct keterangan: " . json_encode($keteranganValues) . "\n";

$statusResignValues = Asisten::distinct()->pluck('status_resign')->toArray();
echo "Distinct status_resign: " . json_encode($statusResignValues) . "\n";

$levelJabatanValues = Asisten::distinct()->pluck('level_jabatan')->toArray();
echo "Distinct level_jabatan: " . json_encode($levelJabatanValues) . "\n";
