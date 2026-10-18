<?php
$refPath = __DIR__ . '/storage/app/reference_programs_summary.csv';
$refHandle = fopen($refPath, 'r');
$header = fgetcsv($refHandle, 0, ',', '"', '\\');

while (($r = fgetcsv($refHandle, 0, ',', '"', '\\')) !== false) {
    if (empty(array_filter($r))) continue;
    $num = (int)$r[0];
    if ($r[1] === 'A' && $num >= 55) {
        echo "Row {$num} | {$r[3]} | Years: R={$r[4]}, T={$r[5]}, L={$r[6]} | Trainee: {$r[7]}, Culled: {$r[8]}, Passed: {$r[9]}, Resign: {$r[10]}, Eksis: {$r[11]}\n";
    }
}
fclose($refHandle);
