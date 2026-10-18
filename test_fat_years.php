<?php
$refPath = __DIR__ . '/storage/app/reference_programs_summary.csv';
$refHandle = fopen($refPath, 'r');
$header = fgetcsv($refHandle, 0, ',', '"', '\\');

$allRows = [];
while (($r = fgetcsv($refHandle, 0, ',', '"', '\\')) !== false) {
    if (empty(array_filter($r))) continue;
    $allRows[] = $r;
}
fclose($refHandle);

// Let's check in allRows (all 74 rows of Category A)
$fat = array_filter($allRows, fn($r) => $r[1] === 'A');

echo "Searching which batches sum to Trainee=247, Culled=30, Passed=217:\n";
// Let's check any combination of batches
// Could it be batches with specific criteria? e.g. Year?
// Let's check all years
$years = [2022, 2023, 2024, 2025, 2026];
foreach ($years as $y) {
    $sub = array_filter($fat, fn($r) => (int)$r[4] >= $y || (int)$r[5] >= $y || (int)$r[6] >= $y);
    $t = array_sum(array_column($sub, 7));
    $c = array_sum(array_column($sub, 8));
    $p = array_sum(array_column($sub, 9));
    $res = array_sum(array_column($sub, 10));
    $e = array_sum(array_column($sub, 11));
    echo "Tahun >= $y: Count=" . count($sub) . " | Trainee=$t, Culled=$c, Passed=$p, Resign=$res, Eksis=$e\n";
}

// What if Tahun Lulus >= $y?
foreach ($years as $y) {
    $sub = array_filter($fat, fn($r) => (int)$r[6] >= $y);
    $t = array_sum(array_column($sub, 7));
    $c = array_sum(array_column($sub, 8));
    $p = array_sum(array_column($sub, 9));
    $res = array_sum(array_column($sub, 10));
    $e = array_sum(array_column($sub, 11));
    echo "Tahun Lulus >= $y: Count=" . count($sub) . " | Trainee=$t, Culled=$c, Passed=$p, Resign=$res, Eksis=$e\n";
}

// What if Tahun Training >= $y?
foreach ($years as $y) {
    $sub = array_filter($fat, fn($r) => (int)$r[5] >= $y);
    $t = array_sum(array_column($sub, 7));
    $c = array_sum(array_column($sub, 8));
    $p = array_sum(array_column($sub, 9));
    $res = array_sum(array_column($sub, 10));
    $e = array_sum(array_column($sub, 11));
    echo "Tahun Training >= $y: Count=" . count($sub) . " | Trainee=$t, Culled=$c, Passed=$p, Resign=$res, Eksis=$e\n";
}

// What if Tahun Rekrut >= $y?
foreach ($years as $y) {
    $sub = array_filter($fat, fn($r) => (int)$r[4] >= $y);
    $t = array_sum(array_column($sub, 7));
    $c = array_sum(array_column($sub, 8));
    $p = array_sum(array_column($sub, 9));
    $res = array_sum(array_column($sub, 10));
    $e = array_sum(array_column($sub, 11));
    echo "Tahun Rekrut >= $y: Count=" . count($sub) . " | Trainee=$t, Culled=$c, Passed=$p, Resign=$res, Eksis=$e\n";
}
