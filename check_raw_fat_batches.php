<?php
$csvPath = __DIR__ . '/storage/app/alumni_monitoring.csv';
$handle = fopen($csvPath, 'r');
$header = fgetcsv($handle, 0, ';', '"', '\\');

$lvlIdx = array_search('LVL', $header);
$progIdx = array_search('PROG', $header);
$programIdx = array_search('Program', $header);
$ketIdx = array_search('Keterangan', $header);
$ketResignIdx = array_search('Keterangan Resign', $header);
$tglResignIdx = array_search('Tanggal Resign', $header);
$thnTrainingIdx = array_search('Tahun Training', $header);
$thnIdx = array_search('Tahun', $header);

$byBatch = [];
while (($row = fgetcsv($handle, 0, ';', '"', '\\')) !== false) {
    if (empty(array_filter($row))) continue;
    $prog = trim($row[$progIdx] ?? '');
    $program = trim($row[$programIdx] ?? '');
    $ket = strtolower(trim($row[$ketIdx] ?? ''));
    $ketRes = strtolower(trim($row[$ketResignIdx] ?? ''));
    $tglRes = trim($row[$tglResignIdx] ?? '');

    $isCulled = (str_contains($ket, 'culled') || str_contains($ket, 'gagal'));
    $isResign = (str_contains($ket, 'resign') || str_contains($ket, 'phk') || str_contains($ketRes, 'resign') || str_contains($ketRes, 'phk') || !empty($tglRes));
    $isEksis = ($ket === 'eksis' || $ket === 'aktif') && !$isCulled && !$isResign;

    $pUpper = strtoupper($prog . ' ' . $program);
    if (str_contains($pUpper, 'FIELD ASSISTANT') || str_contains($pUpper, 'FAT') || str_contains($pUpper, 'ESTATE CADET')) {
        if (!isset($byBatch[$program])) {
            $byBatch[$program] = ['count' => 0, 'culled' => 0, 'resign' => 0, 'eksis' => 0];
        }
        $byBatch[$program]['count']++;
        if ($isCulled) $byBatch[$program]['culled']++;
        elseif ($isResign) $byBatch[$program]['resign']++;
        elseif ($isEksis) $byBatch[$program]['eksis']++;
    }
}
fclose($handle);

echo "FAT Batches from alumni_monitoring.csv:\n";
foreach ($byBatch as $name => $d) {
    if (preg_match('/202[2-6]/', $name)) {
        echo "$name => Total={$d['count']}, Culled={$d['culled']}, Resign={$d['resign']}, Eksis={$d['eksis']}\n";
    }
}
