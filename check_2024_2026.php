<?php
$refPath = __DIR__ . '/storage/app/reference_programs_summary.csv';
$refHandle = fopen($refPath, 'r');
$header = fgetcsv($refHandle, 0, ',', '"', '\\');

echo "Programs in reference_programs_summary.csv with 2024, 2025, 2026:\n";
while (($r = fgetcsv($refHandle, 0, ',', '"', '\\')) !== false) {
    if (empty(array_filter($r))) continue;
    $p = $r[3];
    $rekrut = (int)$r[4];
    $train = (int)$r[5];
    $lulus = (int)$r[6];
    if (str_contains($p, '2024') || str_contains($p, '2025') || str_contains($p, '2026') || $rekrut >= 2024 || $train >= 2024 || $lulus >= 2024) {
        echo "{$r[1]} | {$r[0]} | $p | R=$rekrut, T=$train, L=$lulus | Trainee={$r[7]}, Culled={$r[8]}, Passed={$r[9]}, Resign={$r[10]}, Eksis={$r[11]}\n";
    }
}
fclose($refHandle);
