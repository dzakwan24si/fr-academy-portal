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

// Test all subsets from index 55 (FAT IV/2018) to 73 (FAT I/2026)
$slice = array_slice($fat, 55);
$n = count($slice);

$closest = [];
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
        $batches = [];
        for ($i = 0; $i < $n; $i++) {
            if ($mask & (1 << $i)) {
                $batches[] = $slice[$i]['name'];
            }
        }
        $closest[] = [
            't' => $sumT, 'c' => $sumC, 'p' => $sumP, 'r' => $sumR, 'e' => $sumE,
            'batches' => $batches
        ];
    }
}

echo "Found " . count($closest) . " subsets with Trainee=247, Culled=30:\n";
usort($closest, fn($a, $b) => abs($a['r'] - 62) <=> abs($b['r'] - 62));
foreach (array_slice($closest, 0, 10) as $c) {
    echo "Resign: {$c['r']} (diff " . abs($c['r'] - 62) . "), Existing: {$c['e']}\n";
    echo "  Batches: " . implode(', ', $c['batches']) . "\n\n";
}
