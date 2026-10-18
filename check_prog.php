<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\Asisten;
use App\Models\Monitoring\AsistenProgramMonitoring;

$programs = AsistenProgramMonitoring::orderBy('kode_kategori')->orderBy('no')->get();
echo "Total programs in AsistenProgramMonitoring: " . $programs->count() . "\n";

// Let's check category codes
$categories = AsistenProgramMonitoring::selectRaw('kode_kategori, kategori_program, count(*) as count, sum(jumlah_trainee) as s_trainee, sum(jumlah_culled) as s_culled')
    ->groupBy('kode_kategori', 'kategori_program')
    ->get();

foreach ($categories as $c) {
    echo "Category: {$c->kode_kategori} - {$c->kategori_program} | count: {$c->count} | Trainee: {$c->s_trainee} | Culled: {$c->s_culled}\n";
}
