<?php
$refPath = __DIR__ . '/storage/app/reference_programs_summary.csv';
$refHandle = fopen($refPath, 'r');
$header = fgetcsv($refHandle, 0, ',', '"', '\\');

$allBatches = [];
while (($r = fgetcsv($refHandle, 0, ',', '"', '\\')) !== false) {
    if (empty(array_filter($r))) continue;
    $allBatches[] = $r;
}
fclose($refHandle);

echo "All batches in Category A:\n";
foreach ($allBatches as $b) {
    if ($b[1] === 'A') {
        echo "{$b[0]} | {$b[3]} | Trainee: {$b[7]}, Culled: {$b[8]}, Passed: {$b[9]}, Resign: {$b[10]}, Eksis: {$b[11]}\n";
    }
}
