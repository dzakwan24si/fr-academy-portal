<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\Asisten;
use App\Models\Monitoring\AsistenProgramMonitoring;

// First let's check how many records in asisten table
echo "Asisten count: " . Asisten::count() . "\n";

// Let's test the matching between asisten and programs from the CSV
$csvPath = __DIR__ . '/storage/app/alumni_monitoring.csv';
$handle = fopen($csvPath, 'r');
$header = fgetcsv($handle, 0, ';', '"', '\\');

$lvlIdx = array_search('LVL', $header);
$progIdx = array_search('PROG', $header);
$programIdx = array_search('Program', $header);
$ketIdx = array_search('Keterangan', $header);
$ketResignIdx = array_search('Keterangan Resign', $header);
$tglResignIdx = array_search('Tanggal Resign', $header);
$jabIdx = array_search('Jabatan Terakhir', $header);

$norm = function ($s) {
    $str = strtolower((string) $s);
    $str = str_replace(['field assistant', 'fat'], 'fat', $str);
    $str = str_replace(['mill assistant', 'mat'], 'mat', $str);
    $str = str_replace(['admin assistant', 'admin', 'aat'], 'aat', $str);
    $str = str_replace(['traction assistant', 'tat'], 'tat', $str);
    $str = str_replace(['downstream management trainee', 'downstream management training', 'downstream management', 'dmt'], 'dmt', $str);
    return preg_replace('/[^a-zA-Z0-9]/', '', $str);
};

// Group csv rows by normalized program
$csvByProg = [];
$totalCsvRows = 0;
while (($row = fgetcsv($handle, 0, ';', '"', '\\')) !== false) {
    if (empty(array_filter($row))) continue;
    $totalCsvRows++;
    $progName = trim($row[$programIdx] ?? '');
    $key = $norm($progName);
    $csvByProg[$key][] = $row;
}
fclose($handle);
echo "Total CSV rows: $totalCsvRows, distinct norm programs: " . count($csvByProg) . "\n";

// Now let's calculate per program
$programs = AsistenProgramMonitoring::orderBy('kode_kategori')->orderBy('no')->get();

$discrepancies = [];
$catSummary = [];

foreach ($programs as $p) {
    $cat = $p->kode_kategori;
    if (!isset($catSummary[$cat])) {
        $catSummary[$cat] = ['trainee' => 0, 'culled' => 0, 'passed' => 0, 'resign' => 0, 'eksis' => 0, 'pc' => 0, 'gm' => 0, 'mgr' => 0, 'askep' => 0, 'asisten' => 0];
    }

    $normKey = $norm($p->nama_program);
    $rows = $csvByProg[$normKey] ?? [];
    if (empty($rows)) {
        foreach ($csvByProg as $k => $list) {
            if ($k !== '' && (str_contains($k, $normKey) || str_contains($normKey, $k))) {
                $rows = array_merge($rows, $list);
            }
        }
    }

    $cTrainee = count($rows);
    $cCulled = 0;
    $cResign = 0;
    $cEksis = 0;
    $cPc = 0; $cGm = 0; $cMgr = 0; $cAskep = 0; $cAsisten = 0;

    foreach ($rows as $r) {
        $ket = strtolower(trim($r[$ketIdx] ?? ''));
        $ketRes = strtolower(trim($r[$ketResignIdx] ?? ''));
        $tglRes = trim($r[$tglResignIdx] ?? '');
        $lvl = strtoupper(trim($r[$lvlIdx] ?? ''));
        $jab = strtoupper(trim($r[$jabIdx] ?? ''));

        $isCulled = (str_contains($ket, 'culled') || str_contains($ket, 'gagal'));
        $isResign = (str_contains($ket, 'resign') || str_contains($ket, 'phk') || str_contains($ketRes, 'resign') || str_contains($ketRes, 'phk') || !empty($tglRes));
        $isEksis = ($ket === 'eksis' || $ket === 'aktif' || str_contains($ket, 'eksis') || str_contains($ket, 'aktif')) && !$isCulled && !$isResign;

        if ($isCulled) {
            $cCulled++;
        } elseif ($isResign) {
            $cResign++;
        } elseif ($isEksis) {
            $cEksis++;

            if (in_array($lvl, ['PC', 'P', 'DIR'], true) || str_contains($jab, 'PC') || str_contains($jab, 'PROJECT COORDINATOR') || str_contains($jab, 'DIREKTUR')) {
                $cPc++;
            } elseif ($lvl === 'GM' || str_contains($jab, 'GM') || str_contains($jab, 'GENERAL MANAGER')) {
                $cGm++;
            } elseif (in_array($lvl, ['MGR', 'MK', 'MM', 'MANAGER'], true) || str_contains($jab, 'MGR') || str_contains($jab, 'MANAGER')) {
                $cMgr++;
            } elseif (in_array($lvl, ['ASKEP', 'KTU'], true) || str_contains($jab, 'ASKEP') || str_contains($jab, 'KTU') || str_contains($jab, 'ASSISTANT KEPALA')) {
                $cAskep++;
            } else {
                $cAsisten++;
            }
        }
    }

    $catSummary[$cat]['trainee'] += $cTrainee;
    $catSummary[$cat]['culled'] += $cCulled;
    $catSummary[$cat]['passed'] += ($cTrainee - $cCulled);
    $catSummary[$cat]['resign'] += $cResign;
    $catSummary[$cat]['eksis'] += $cEksis;
    $catSummary[$cat]['pc'] += $cPc;
    $catSummary[$cat]['gm'] += $cGm;
    $catSummary[$cat]['mgr'] += $cMgr;
    $catSummary[$cat]['askep'] += $cAskep;
    $catSummary[$cat]['asisten'] += $cAsisten;
}

echo "\nSummary per category from RAW CSV:\n";
foreach ($catSummary as $cat => $s) {
    echo "Category $cat: Trainee={$s['trainee']}, Culled={$s['culled']}, Passed={$s['passed']}, Resign={$s['resign']}, Eksis={$s['eksis']} | PC={$s['pc']}, GM={$s['gm']}, MGR={$s['mgr']}, ASKEP={$s['askep']}, ASISTEN={$s['asisten']}\n";
}
