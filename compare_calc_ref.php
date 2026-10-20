<?php
$refPath = __DIR__ . '/storage/app/reference_programs_summary.csv';
$csvPath = __DIR__ . '/storage/app/alumni_monitoring.csv';

$norm = function ($s) {
    $str = strtolower((string) $s);
    $str = str_replace(['field assistant', 'fat'], 'fat', $str);
    $str = str_replace(['mill assistant', 'mat'], 'mat', $str);
    $str = str_replace(['admin assistant', 'admin', 'aat'], 'aat', $str);
    $str = str_replace(['traction assistant', 'tat'], 'tat', $str);
    $str = str_replace(['downstream management trainee', 'downstream management training', 'downstream management', 'dmt'], 'dmt', $str);
    return preg_replace('/[^a-zA-Z0-9]/', '', $str);
};

// 1. Read Raw CSV
$handle = fopen($csvPath, 'r');
$header = fgetcsv($handle, 0, ';', '"', '\\');
$lvlIdx = array_search('LVL', $header);
$progIdx = array_search('PROG', $header);
$programIdx = array_search('Program', $header);
$ketIdx = array_search('Keterangan', $header);
$ketResignIdx = array_search('Keterangan Resign', $header);
$tglResignIdx = array_search('Tanggal Resign', $header);
$jabIdx = array_search('Jabatan Terakhir', $header);

$rawByNormProg = [];
while (($row = fgetcsv($handle, 0, ';', '"', '\\')) !== false) {
    if (empty(array_filter($row))) continue;
    $p = trim($row[$programIdx] ?? '');
    $key = $norm($p);
    $rawByNormProg[$key][] = $row;
}
fclose($handle);

// 2. Read Reference
$refHandle = fopen($refPath, 'r');
$refHeader = fgetcsv($refHandle);

$totalRefEksis = 0;
$totalCalcEksis = 0;
$mismatches = [];

while (($refRow = fgetcsv($refHandle)) !== false) {
    if (empty(array_filter($refRow))) continue;
    $pName = trim($refRow[3]);
    $cat = trim($refRow[1]);
    $refTrainee = (int)$refRow[7];
    $refCulled = (int)$refRow[8];
    $refPassed = (int)$refRow[9];
    $refResign = (int)$refRow[10];
    $refEksis = (int)$refRow[11];
    $refPC = (int)$refRow[16];
    $refGM = (int)$refRow[17];
    $refMGR = (int)$refRow[18];
    $refAskep = (int)$refRow[19];
    $refAsisten = (int)$refRow[20];

    $normKey = $norm($pName);
    $matching = $rawByNormProg[$normKey] ?? [];
    if (empty($matching)) {
        foreach ($rawByNormProg as $k => $list) {
            if ($k !== '' && (str_contains($k, $normKey) || str_contains($normKey, $k))) {
                $matching = array_merge($matching, $list);
            }
        }
    }

    $calcCulled = 0;
    $calcResign = 0;
    $calcEksis = 0;
    $calcPC = 0; $calcGM = 0; $calcMGR = 0; $calcAskep = 0; $calcAsisten = 0;

    foreach ($matching as $m) {
        $ket = strtolower(trim($m[$ketIdx] ?? ''));
        $ketRes = strtolower(trim($m[$ketResignIdx] ?? ''));
        $tglRes = trim($m[$tglResignIdx] ?? '');
        $lvl = strtoupper(trim($m[$lvlIdx] ?? ''));
        $jab = strtoupper(trim($m[$jabIdx] ?? ''));

        $isCulled = (str_contains($ket, 'culled') || str_contains($ket, 'gagal'));
        $isResign = (str_contains($ket, 'resign') || str_contains($ket, 'phk') || str_contains($ketRes, 'resign') || str_contains($ketRes, 'phk') || !empty($tglRes));
        $isEksis = ($ket === 'eksis' || $ket === 'aktif' || str_contains($ket, 'eksis') || str_contains($ket, 'aktif')) && !$isCulled && !$isResign;

        if ($isCulled) {
            $calcCulled++;
        } elseif ($isResign) {
            $calcResign++;
        } elseif ($isEksis) {
            $calcEksis++;

            // Let's test position mapping:
            // Notice: In FAT row 2: Firdaus Tarigan, LVL=DIR, PC in reference is 0! Wait, let's see how reference mapped positions!
            if ($lvl === 'DIR' || $lvl === 'P' || $lvl === 'PC' || str_contains($jab, 'CONTROLLER') || str_contains($jab, 'PC')) {
                // Check if reference has PC or what
                $calcPC++;
            } elseif ($lvl === 'GM' || str_contains($jab, 'GENERAL MANAGER') || str_contains($jab, 'GM')) {
                $calcGM++;
            } elseif (in_array($lvl, ['MGR', 'MK', 'MM', 'MANAGER'], true) || str_contains($jab, 'MGR') || str_contains($jab, 'MANAGER')) {
                $calcMGR++;
            } elseif (in_array($lvl, ['ASKEP', 'KTU'], true) || str_contains($jab, 'ASKEP') || str_contains($jab, 'KTU')) {
                $calcAskep++;
            } else {
                $calcAsisten++;
            }
        }
    }

    $totalRefEksis += $refEksis;
    $totalCalcEksis += $calcEksis;

    if ($refEksis !== $calcEksis || $refPC !== $calcPC || $refGM !== $calcGM || $refMGR !== $calcMGR || $refAskep !== $calcAskep || $refAsisten !== $calcAsisten) {
        $mismatches[] = [
            'program' => $pName,
            'ref' => "Eksis=$refEksis [PC=$refPC, GM=$refGM, MGR=$refMGR, Ask=$refAskep, Ast=$refAsisten]",
            'calc' => "Eksis=$calcEksis [PC=$calcPC, GM=$calcGM, MGR=$calcMGR, Ask=$calcAskep, Ast=$calcAsisten]",
            'matching_count' => count($matching),
        ];
    }
}
fclose($refHandle);

echo "Total Ref Eksis: $totalRefEksis, Total Calc Eksis: $totalCalcEksis\n";
echo "Total mismatched rows: " . count($mismatches) . "\n";
echo "First 20 mismatched rows:\n";
foreach (array_slice($mismatches, 0, 20) as $m) {
    echo "  {$m['program']}:\n    REF:  {$m['ref']}\n    CALC: {$m['calc']} (Matched raw rows: {$m['matching_count']})\n";
}
