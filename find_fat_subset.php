<?php
$refPath = __DIR__ . '/storage/app/reference_programs_summary.csv';
$refHandle = fopen($refPath, 'r');
$header = fgetcsv($refHandle, 0, ',', '"', '\\');

$fatProgs = [];
while (($r = fgetcsv($refHandle, 0, ',', '"', '\\')) !== false) {
    if (empty(array_filter($r))) continue;
    if ($r[1] === 'A') {
        $fatProgs[] = [
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

echo "Total FAT progs: " . count($fatProgs) . "\n";

// Let's test all subsets of recent FAT programs (from row 60 onwards)
$recentFat = array_slice($fatProgs, 60); // from index 60 (which is row 61: Estate Cadet Trainee - I/2022) to 73
echo "Recent FAT progs count: " . count($recentFat) . "\n";
foreach ($recentFat as $idx => $p) {
    echo "  [$idx] No {$p['no']} | {$p['name']} | Trainee: {$p['trainee']}, Culled: {$p['culled']}, Passed: {$p['passed']}, Resign: {$p['resign']}, Eksis: {$p['eksis']}\n";
}

// Find subset of recentFat where sum of trainee == 247 and sum of culled == 30
$n = count($recentFat);
for ($mask = 1; $mask < (1 << $n); $mask++) {
    $sumT = 0; $sumC = 0; $sumP = 0; $sumR = 0; $sumE = 0;
    $names = [];
    for ($i = 0; $i < $n; $i++) {
        if ($mask & (1 << $i)) {
            $sumT += $recentFat[$i]['trainee'];
            $sumC += $recentFat[$i]['culled'];
            $sumP += $recentFat[$i]['passed'];
            $sumR += $recentFat[$i]['resign'];
            $sumE += $recentFat[$i]['eksis'];
            $names[] = $recentFat[$i]['name'];
        }
    }
    if ($sumT === 247) {
        echo "\nFOUND SUBSET matching Trainee 247! Culled = $sumC, Passed = $sumP, Resign = $sumR, Existing = $sumE\n";
        echo "Batches: " . implode(', ', $names) . "\n";
    }
}
