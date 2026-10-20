<?php
$refPath = __DIR__ . '/storage/app/reference_programs_summary.csv';
$refHandle = fopen($refPath, 'r');
$header = fgetcsv($refHandle);

$rows = [];
while (($r = fgetcsv($refHandle)) !== false) {
    if (empty(array_filter($r))) continue;
    $rows[] = $r;
}
fclose($refHandle);

// Let's filter by year: What programs have Tahun >= 2024, or Tahun >= 2025?
echo "=== PROGRAMS WITH TAHUN >= 2024 ===\n";
foreach ($rows as $r) {
    $yRekrut = (int)$r[4];
    $yTraining = (int)$r[5];
    $yLulus = (int)$r[6];
    $effYear = $yLulus ?: ($yTraining ?: $yRekrut);
    if ($effYear >= 2024) {
        echo "{$r[1]} | {$r[3]} | Rekrut: $yRekrut, Train: $yTraining, Lulus: $yLulus | Trainee: {$r[7]}, Culled: {$r[8]}, Passed: {$r[9]}, Resign: {$r[10]}, Eksis: {$r[11]}\n";
    }
}
