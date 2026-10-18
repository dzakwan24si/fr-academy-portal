<?php
$refPath = __DIR__ . '/storage/app/reference_programs_summary.csv';
$refHandle = fopen($refPath, 'r');
$header = fgetcsv($refHandle);

echo "Searching for programs in Category A with trainee around 34:\n";
while (($r = fgetcsv($refHandle)) !== false) {
    if (empty(array_filter($r))) continue;
    if ($r[1] === 'A') {
        echo "Row {$r[0]} | {$r[3]} | Years: R={$r[4]}, T={$r[5]}, L={$r[6]} | Trainee: {$r[7]}, Culled: {$r[8]}, Passed: {$r[9]}, Resign: {$r[10]}, Eksis: {$r[11]}\n";
    }
}
fclose($refHandle);
