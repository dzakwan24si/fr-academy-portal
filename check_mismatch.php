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
$lvlIdx = array_search('LVL', $header);
$progIdx = array_search('PROG', $header);
$programIdx = array_search('Program', $header);

$nonEksisInCsv = [];
$r = 0;
while (($row = fgetcsv($f, 0, ';', '"', '\\')) !== false) {
    if (empty(array_filter($row))) continue;
    $r++;
    $ket = trim($row[$ketIdx] ?? '');
    if ($ket !== 'Eksis') {
        $nonEksisInCsv[$r] = [
            'row' => $r,
            'name' => trim($row[2] ?? ''),
            'ket' => $ket,
            'lvl' => trim($row[$lvlIdx] ?? ''),
            'program' => trim($row[$programIdx] ?? ''),
        ];
    }
}
fclose($f);

// Check if any of these rows are 'Aktif' in the database
$dbAsisten = Asisten::orderBy('id')->get();
$dbRow = 0;
$mismatches = [];
foreach ($dbAsisten as $a) {
    $dbRow++;
    if (isset($nonEksisInCsv[$dbRow])) {
        if ($a->keterangan === 'Aktif') {
            $mismatches[] = [
                'id' => $a->id,
                'name' => $a->nama,
                'program' => $a->program,
                'db_ket' => $a->keterangan,
                'csv_ket' => $nonEksisInCsv[$dbRow]['ket'],
                'lvl' => $a->level_jabatan,
                'tgl_res' => $a->tanggal_resign,
            ];
        }
    }
}

echo "Total mismatches where DB is Aktif but CSV is not Eksis: " . count($mismatches) . "\n";
echo "Sample mismatches:\n";
foreach (array_slice($mismatches, 0, 15) as $m) {
    echo "ID: {$m['id']}, Name: {$m['name']}, Prog: {$m['program']}, DB: {$m['db_ket']}, CSV: '{$m['csv_ket']}', LVL: '{$m['lvl']}', TglRes: '{$m['tgl_res']}'\n";
}
