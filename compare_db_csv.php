<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\Asisten;

$csvPath = __DIR__ . '/storage/app/alumni_monitoring.csv';
$f = fopen($csvPath, 'r');
$header = fgetcsv($f, 0, ';', '"', '\\');
$ketIdx = array_search('Keterangan', $header);
$ketResignIdx = array_search('Keterangan Resign', $header);
$tglResignIdx = array_search('Tanggal Resign', $header);
$lvlIdx = array_search('LVL', $header);
$progIdx = array_search('PROG', $header);
$programIdx = array_search('Program', $header);
$nikIdx = 1; // NIK Trainee
$namaIdx = 2; // Nama

// Check how many are in database asisten where keterangan='Aktif'
$dbAktif = Asisten::where('keterangan', 'Aktif')->get();
echo "DB Aktif count: " . $dbAktif->count() . "\n";

// Let's check how many in DB Aktif have non-empty tanggal_resign or status_resign != Aktif
$dbAktifResign = Asisten::where('keterangan', 'Aktif')
    ->where(function($q) {
        $q->whereNotNull('tanggal_resign')->where('tanggal_resign', '!=', '')
          ->orWhere('status_resign', '!=', 'Aktif')
          ->orWhere('status_resign', 'like', '%resign%');
    })->count();
echo "DB Aktif with resign/tgl_resign: " . $dbAktifResign . "\n";

// Let's check what values of keterangan exist in CSV
$csvKets = [];
$csvActiveRows = [];
$rIdx = 0;
while (($row = fgetcsv($f, 0, ';', '"', '\\')) !== false) {
    if (empty(array_filter($row))) continue;
    $rIdx++;
    $ket = trim($row[$ketIdx] ?? '');
    $csvKets[$ket] = ($csvKets[$ket] ?? 0) + 1;

    $ketLower = strtolower($ket);
    $ketResign = trim($row[$ketResignIdx] ?? '');
    $tglResign = trim($row[$tglResignIdx] ?? '');

    $isEksis = ($ketLower === 'eksis' || $ketLower === 'aktif' || str_contains($ketLower, 'eksis') || str_contains($ketLower, 'aktif'));
    if ($isEksis && !str_contains($ketLower, 'resign') && !str_contains($ketLower, 'phk') && !str_contains($ketLower, 'culled') && !str_contains($ketLower, 'gagal') && !str_contains(strtolower($ketResign), 'resign') && !str_contains(strtolower($ketResign), 'phk') && empty($tglResign)) {
        $csvActiveRows[] = [
            'row' => $rIdx,
            'nik' => trim($row[$nikIdx] ?? ''),
            'nama' => trim($row[$namaIdx] ?? ''),
            'program' => trim($row[$programIdx] ?? ''),
            'lvl' => trim($row[$lvlIdx] ?? ''),
        ];
    }
}
fclose($f);

echo "CSV Keterangan counts: " . json_encode($csvKets) . "\n";
echo "CSV clean active count: " . count($csvActiveRows) . "\n";

// Now let's compare DB asisten vs CSV
// Is asisten table synced from CSV?
// Let's check first 5 db records vs csv records
$first5Db = Asisten::limit(5)->get();
echo "\nFirst 5 DB records:\n";
foreach ($first5Db as $d) {
    echo "ID: {$d->id}, NIK: {$d->nik}, Nama: {$d->nama}, Prog: {$d->program}, Ket: {$d->keterangan}, LVL: {$d->level_jabatan}\n";
}
