<?php
$refPath = __DIR__ . '/storage/app/reference_programs_summary.csv';
$refHandle = fopen($refPath, 'r');
$refHeader = fgetcsv($refHandle);

$sums = [];
while (($row = fgetcsv($refHandle)) !== false) {
    if (empty(array_filter($row))) continue;
    $cat = $row[1];
    if (!isset($sums[$cat])) {
        $sums[$cat] = ['trainee' => 0, 'culled' => 0, 'passed' => 0, 'resign' => 0, 'eksis' => 0, 'pc' => 0, 'gm' => 0, 'mgr' => 0, 'askep' => 0, 'asisten' => 0];
    }
    $sums[$cat]['trainee'] += (int)$row[7];
    $sums[$cat]['culled'] += (int)$row[8];
    $sums[$cat]['passed'] += (int)$row[9];
    $sums[$cat]['resign'] += (int)$row[10];
    $sums[$cat]['eksis'] += (int)$row[11];
    $sums[$cat]['pc'] += (int)$row[16];
    $sums[$cat]['gm'] += (int)$row[17];
    $sums[$cat]['mgr'] += (int)$row[18];
    $sums[$cat]['askep'] += (int)$row[19];
    $sums[$cat]['asisten'] += (int)$row[20];
}
fclose($refHandle);

foreach ($sums as $cat => $s) {
    echo "Ref Category $cat: Trainee={$s['trainee']}, Culled={$s['culled']}, Passed={$s['passed']}, Resign={$s['resign']}, Eksis={$s['eksis']} | PC={$s['pc']}, GM={$s['gm']}, MGR={$s['mgr']}, ASKEP={$s['askep']}, ASISTEN={$s['asisten']}\n";
}
