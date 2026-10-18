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
            'rekrut' => (int)$r[4],
            'training' => (int)$r[5],
            'lulus' => (int)$r[6],
            'trainee' => (int)$r[7],
            'culled' => (int)$r[8],
            'passed' => (int)$r[9],
            'resign' => (int)$r[10],
            'eksis' => (int)$r[11],
        ];
    }
}
fclose($refHandle);

// Test from index 58 (Field Assistant - I/2021) to 73 (Field Assistant - I/2026): that's 16 programs (2^16 = 65536, very fast!)
$slice = array_slice($fat, 58);
$n = count($slice);
echo "Testing $n programs (2^$n = " . (1 << $n) . " subsets)...\n";

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
    if ($sumT === 247 && $sumC === 30) {
        echo "MATCH FOUND!\n";
        echo "  Trainee=$sumT, Culled=$sumC, Passed=$sumP, Resign=$sumR, Existing=$sumE\n";
        $batches = [];
        for ($i = 0; $i < $n; $i++) {
            if ($mask & (1 << $i)) {
                $batches[] = $slice[$i]['name'];
            }
        }
        echo "  Batches: " . implode(', ', $batches) . "\n\n";
    }
}
