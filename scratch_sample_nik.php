<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Program\TrainingRecord;

$recs = TrainingRecord::where('tahun', '2027')->orWhere('tahun', 2027)->get();
echo "Total records with tahun = 2027: " . $recs->count() . "\n";
foreach ($recs as $r) {
    echo "ID: " . $r->id . " | prog_id: " . $r->program_id . " | type: " . $r->program_type . " | notes: " . $r->notes . " | nama_pelatihan: " . $r->nama_pelatihan . " | part: " . ($r->participant_name ?: $r->participant_identifier) . "\n";
}
