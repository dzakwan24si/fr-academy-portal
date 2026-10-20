<?php
$refPath = __DIR__ . '/storage/app/reference_programs_summary.csv';
$refHandle = fopen($refPath, 'r');
$header = fgetcsv($refHandle, 0, ',', '"', '\\');

$fat = [];
while (($r = fgetcsv($refHandle, 0, ',', '"', '\\')) !== false) {
    if (empty(array_filter($r))) continue;
    if ($r[1] === 'A') {
        $fat[] = [
            'no' => $r[0],
            'name' => $r[3],
            'trainee' => (int)$r[7],
            'culled' => (int)$r[8],
            'passed' => (int)$r[9],
            'resign' => (int)$r[10],
            'eksis' => (int)$r[11],
        ];
    }
}
fclose($refHandle);

echo "Searching for exact match: Trainee=247, Culled=30, Passed=217, Resign=62, Existing=155\n";

$n = count($fat);
// Check contiguous ranges first
for ($i = 0; $i < $n; $i++) {
    $sumT = 0; $sumC = 0; $sumP = 0; $sumR = 0; $sumE = 0;
    $names = [];
    for ($j = $i; $j < $n; $j++) {
        $sumT += $fat[$j]['trainee'];
        $sumC += $fat[$j]['culled'];
        $sumP += $fat[$j]['passed'];
        $sumR += $fat[$j]['resign'];
        $sumE += $fat[$j]['eksis'];
        $names[] = $fat[$j]['name'];

        if ($sumT === 247) {
            echo "Contiguous range from index $i ({$fat[$i]['name']}) to $j ({$fat[$j]['name']}):\n";
            echo "  Trainee=$sumT, Culled=$sumC, Passed=$sumP, Resign=$sumR, Existing=$sumE\n";
        }
    }
}
