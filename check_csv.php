<?php
$csvPath = __DIR__ . '/storage/app/alumni_monitoring.csv';
$f = fopen($csvPath, 'r');
$header = fgetcsv($f, 0, ';', '"', '\\');
echo "CSV Columns (" . count($header) . "): " . implode(' | ', array_slice($header, 0, 30)) . "\n";

$progs = [];
$totalRows = 0;
$byKet = [];
$byLvl = [];
$activeCount = 0;

$lvlIdx = array_search('LVL', $header);
$progIdx = array_search('PROG', $header);
$programIdx = array_search('Program', $header);
$ketIdx = array_search('Keterangan', $header);
$ketResignIdx = array_search('Keterangan Resign', $header);
$tglResignIdx = array_search('Tanggal Resign', $header);

echo "Indices: LVL=$lvlIdx, PROG=$progIdx, Program=$programIdx, Ket=$ketIdx, KetResign=$ketResignIdx, TglResign=$tglResignIdx\n";

$byCategory = [
    'FAT' => ['pc' => 0, 'gm' => 0, 'mgr' => 0, 'askep' => 0, 'asisten' => 0, 'total' => 0],
    'MAT' => ['pc' => 0, 'gm' => 0, 'mgr' => 0, 'askep' => 0, 'asisten' => 0, 'total' => 0],
    'AAT' => ['pc' => 0, 'gm' => 0, 'mgr' => 0, 'askep' => 0, 'asisten' => 0, 'total' => 0],
    'TAT' => ['pc' => 0, 'gm' => 0, 'mgr' => 0, 'askep' => 0, 'asisten' => 0, 'total' => 0],
    'DMT' => ['pc' => 0, 'gm' => 0, 'mgr' => 0, 'askep' => 0, 'asisten' => 0, 'total' => 0],
];

while (($row = fgetcsv($f, 0, ';', '"', '\\')) !== false) {
    if (empty(array_filter($row))) continue;
    $totalRows++;

    $ket = trim($row[$ketIdx] ?? '');
    $ketResign = trim($row[$ketResignIdx] ?? '');
    $tglResign = trim($row[$tglResignIdx] ?? '');
    $lvl = strtoupper(trim($row[$lvlIdx] ?? ''));

    $ketLower = strtolower($ket);
    $isEksis = ($ketLower === 'eksis' || $ketLower === 'aktif' || str_contains($ketLower, 'eksis') || str_contains($ketLower, 'aktif'));
    if (!$isEksis) continue;
    if (str_contains($ketLower, 'resign') || str_contains($ketLower, 'phk') || str_contains($ketLower, 'culled') || str_contains($ketLower, 'gagal')) continue;
    if (str_contains(strtolower($ketResign), 'resign') || str_contains(strtolower($ketResign), 'phk')) continue;
    if (!empty($tglResign)) continue;

    $activeCount++;

    $prog = strtoupper(trim($row[$progIdx] ?? ''));
    $program = trim($row[$programIdx] ?? '');

    if ($prog === 'ECT' || str_contains(strtoupper($program), 'ESTATE CADET')) {
        $prog = 'FAT';
    } elseif ($prog === '') {
        $progUpper = strtoupper($program);
        if (str_contains($progUpper, 'FIELD ASSISTANT') || str_contains($progUpper, 'STAFF OPERATION') || str_contains($progUpper, 'FAT')) {
            $prog = 'FAT';
        } elseif (str_contains($progUpper, 'MILL ASSISTANT') || str_contains($progUpper, 'MAT')) {
            $prog = 'MAT';
        } elseif (str_contains($progUpper, 'ADMIN ASSISTANT') || str_contains($progUpper, 'AAT')) {
            $prog = 'AAT';
        } elseif (str_contains($progUpper, 'TRACTION ASSISTANT') || str_contains($progUpper, 'TAT')) {
            $prog = 'TAT';
        } elseif (str_contains($progUpper, 'DOWNSTREAM') || str_contains($progUpper, 'DMT')) {
            $prog = 'DMT';
        }
    }

    if (!isset($byCategory[$prog])) continue;

    $byCategory[$prog]['total']++;

    if (in_array($lvl, ['PC', 'P', 'DIR'], true)) {
        $byCategory[$prog]['pc']++;
    } elseif ($lvl === 'GM') {
        $byCategory[$prog]['gm']++;
    } elseif (in_array($lvl, ['MGR', 'MK', 'MM', 'MANAGER'], true)) {
        $byCategory[$prog]['mgr']++;
    } elseif (in_array($lvl, ['ASKEP', 'KTU'], true)) {
        $byCategory[$prog]['askep']++;
    } elseif (in_array($lvl, ['ASISTEN', 'ASST', 'STAFF'], true)) {
        $byCategory[$prog]['asisten']++;
    } else {
        echo "Unknown LVL for active: '{$lvl}' | Program: {$program} | Name: {$row[2]}\n";
    }
}
fclose($f);

echo "Total rows: $totalRows, Active (ALL YEARS): $activeCount\n";
foreach ($byCategory as $k => $v) {
    echo "Prog $k: Total={$v['total']} | PC={$v['pc']}, GM={$v['gm']}, MGR={$v['mgr']}, ASKEP={$v['askep']}, ASISTEN={$v['asisten']}\n";
}
