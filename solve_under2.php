<?php
$refPath = __DIR__ . '/storage/app/reference_programs_summary.csv';
$refHandle = fopen($refPath, 'r');
$header = fgetcsv($refHandle, 0, ',', '"', '\\');

$programsByCat = [];
while (($r = fgetcsv($refHandle, 0, ',', '"', '\\')) !== false) {
    if (empty(array_filter($r))) continue;
    $programsByCat[$r[1]][] = [
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
fclose($refHandle);

// Target numbers:
// FAT: Trainee=247, Culled=30, Passed=217, Resign=62, Existing=155
// MAT: Trainee=56, Culled=6, Passed=50, Resign=11, Existing=39
// AAT: Trainee=37, Culled=14, Passed=23, Resign=0, Existing=23
// TAT: Trainee=21, Culled=0, Passed=21, Resign=7, Existing=14

echo "=== SOLVING FOR MAT ===\n";
// MAT target: Trainee=56, Culled=6, Passed=50, Resign=11, Existing=39
$matProgs = $programsByCat['B'];
// Let's test combinations or contiguous slices from the end of MAT
for ($start = 0; $start < count($matProgs); $start++) {
    $sumT = 0; $sumC = 0; $sumP = 0; $sumR = 0; $sumE = 0;
    $names = [];
    for ($i = $start; $i < count($matProgs); $i++) {
        $sumT += $matProgs[$i]['trainee'];
        $sumC += $matProgs[$i]['culled'];
        $sumP += $matProgs[$i]['passed'];
        $sumR += $matProgs[$i]['resign'];
        $sumE += $matProgs[$i]['eksis'];
        $names[] = $matProgs[$i]['name'];
    }
    if ($sumT === 56 && $sumC === 6) {
        echo "Found MAT match starting at index $start ({$matProgs[$start]['name']}):\n";
        echo "  Trainee=$sumT, Culled=$sumC, Passed=$sumP, Resign=$sumR, Existing=$sumE\n";
        echo "  Batches: " . implode(', ', $names) . "\n";
    }
}

echo "\n=== SOLVING FOR AAT ===\n";
// AAT target: Trainee=37, Culled=14, Passed=23, Resign=0, Existing=23
$aatProgs = $programsByCat['C'];
for ($start = 0; $start < count($aatProgs); $start++) {
    $sumT = 0; $sumC = 0; $sumP = 0; $sumR = 0; $sumE = 0;
    $names = [];
    for ($i = $start; $i < count($aatProgs); $i++) {
        $sumT += $aatProgs[$i]['trainee'];
        $sumC += $aatProgs[$i]['culled'];
        $sumP += $aatProgs[$i]['passed'];
        $sumR += $aatProgs[$i]['resign'];
        $sumE += $aatProgs[$i]['eksis'];
        $names[] = $aatProgs[$i]['name'];
    }
    if ($sumT === 37 && $sumC === 14) {
        echo "Found AAT match starting at index $start ({$aatProgs[$start]['name']}):\n";
        echo "  Trainee=$sumT, Culled=$sumC, Passed=$sumP, Resign=$sumR, Existing=$sumE\n";
        echo "  Batches: " . implode(', ', $names) . "\n";
    }
}

echo "\n=== SOLVING FOR TAT ===\n";
// TAT target: Trainee=21, Culled=0, Passed=21, Resign=7, Existing=14
$tatProgs = $programsByCat['D'];
for ($start = 0; $start < count($tatProgs); $start++) {
    $sumT = 0; $sumC = 0; $sumP = 0; $sumR = 0; $sumE = 0;
    $names = [];
    for ($i = $start; $i < count($tatProgs); $i++) {
        $sumT += $tatProgs[$i]['trainee'];
        $sumC += $tatProgs[$i]['culled'];
        $sumP += $tatProgs[$i]['passed'];
        $sumR += $tatProgs[$i]['resign'];
        $sumE += $tatProgs[$i]['eksis'];
        $names[] = $tatProgs[$i]['name'];
    }
    if ($sumT === 21 && $sumC === 0) {
        echo "Found TAT match starting at index $start ({$tatProgs[$start]['name']}):\n";
        echo "  Trainee=$sumT, Culled=$sumC, Passed=$sumP, Resign=$sumR, Existing=$sumE\n";
        echo "  Batches: " . implode(', ', $names) . "\n";
    }
}

echo "\n=== SOLVING FOR FAT ===\n";
// FAT target: Trainee=247, Culled=30, Passed=217, Resign=62, Existing=155
$fatProgs = $programsByCat['A'];
for ($start = 0; $start < count($fatProgs); $start++) {
    for ($end = $start; $end < count($fatProgs); $end++) {
        $sumT = 0; $sumC = 0; $sumP = 0; $sumR = 0; $sumE = 0;
        $names = [];
        for ($i = $start; $i <= $end; $i++) {
            $sumT += $fatProgs[$i]['trainee'];
            $sumC += $fatProgs[$i]['culled'];
            $sumP += $fatProgs[$i]['passed'];
            $sumR += $fatProgs[$i]['resign'];
            $sumE += $fatProgs[$i]['eksis'];
            $names[] = $fatProgs[$i]['name'];
        }
        if ($sumT === 247 && $sumC === 30) {
            echo "Found FAT match from $start ({$fatProgs[$start]['name']}) to $end ({$fatProgs[$end]['name']}):\n";
            echo "  Trainee=$sumT, Culled=$sumC, Passed=$sumP, Resign=$sumR, Existing=$sumE\n";
            echo "  Batches: " . implode(', ', $names) . "\n";
        }
    }
}
