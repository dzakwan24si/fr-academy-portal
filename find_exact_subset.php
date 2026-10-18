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

$slice = array_slice($fat, 58);
$n = count($slice);

for ($mask = 1; $mask < (1 << $n); $mask++) {
    $sumT = 0; $sumC = 0; $sumP = 0; $sumR = 0; $sumE = 0;
    for ($i = 0; $i < $n; $i++) {
        if ($mask & (1 << $i)) {
            $sumT += $slice[$i]['trainee'];
            $sumC += $slice[$i]['culled'];
            $sumP += $slice[$i]['passed'];
            $sumR += $slice[$i]['resign'];
            $sumE += $slice[$i]['eksis'];
        }
    }
    if ($sumT === 247 && $sumC === 30 && $sumP === 217) {
        $batches = [];
        for ($i = 0; $i < $n; $i++) {
            if ($mask & (1 << $i)) {
                $batches[] = $slice[$i]['name'];
            }
        }
        echo "Resign=$sumR, Existing=$sumE\n";
        echo "Batches: " . implode(', ', $batches) . "\n\n";
    }
}
