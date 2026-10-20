<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\Asisten;
use App\Models\Monitoring\AsistenProgramMonitoring;

$programs = AsistenProgramMonitoring::orderBy('kode_kategori', 'asc')->orderBy('no', 'asc')->get();

$asistenRecords = Asisten::select(['id', 'nama', 'program', 'keterangan', 'status_kelulusan', 'status_resign', 'jabatan_terakhir', 'level_jabatan', 'status_karyawan', 'tanggal_resign'])
    ->whereNotNull('program')
    ->get();

$norm = function ($s) {
    $str = strtolower((string) $s);
    $str = str_replace(['field assistant', 'fat'], 'fat', $str);
    $str = str_replace(['mill assistant', 'mat'], 'mat', $str);
    $str = str_replace(['admin assistant', 'aat'], 'aat', $str);
    $str = str_replace(['traction assistant', 'tat'], 'tat', $str);
    $str = str_replace(['downstream management', 'dmt'], 'dmt', $str);

    return preg_replace('/[^a-zA-Z0-9]/', '', $str);
};

$asistenByNormProgram = [];
foreach ($asistenRecords as $a) {
    $key = $norm($a->program);
    if ($key !== '') {
        $asistenByNormProgram[$key][] = $a;
    }
}

$transformedData = [];
$totalTraineeAll = 0;
$totalCulledAll = 0;
$totalPassedAll = 0;
$totalResignAll = 0;
$totalEksisAll = 0;
$totalPcAll = 0;
$totalGmAll = 0;
$totalMgrAll = 0;
$totalAskepAll = 0;
$totalAsistenAll = 0;

$byCat = [];

foreach ($programs as $prog) {
    $cat = $prog->kode_kategori;
    if (!isset($byCat[$cat])) {
        $byCat[$cat] = ['trainee' => 0, 'culled' => 0, 'passed' => 0, 'resign' => 0, 'eksis' => 0, 'pc' => 0, 'gm' => 0, 'mgr' => 0, 'askep' => 0, 'asisten' => 0];
    }

    $normKey = $norm($prog->nama_program);
    $matchingAsisten = $asistenByNormProgram[$normKey] ?? [];

    if (empty($matchingAsisten)) {
        foreach ($asistenByNormProgram as $k => $list) {
            if ($k !== '' && (str_contains($k, $normKey) || str_contains($normKey, $k))) {
                $matchingAsisten = array_merge($matchingAsisten, $list);
            }
        }
    }

    $culledCountFromDb = 0;
    $resignCount = 0;
    $eksisCount = 0;
    $posPC = 0;
    $posGM = 0;
    $posMGR = 0;
    $posAskep = 0;
    $posAsisten = 0;

    foreach ($matchingAsisten as $m) {
        $ket = strtolower(trim((string) $m->keterangan));
        $statusKel = strtolower(trim((string) $m->status_kelulusan));
        $statusRes = strtolower(trim((string) $m->status_resign));
        $tglRes = trim((string) $m->tanggal_resign);
        $lvl = strtoupper(trim((string) $m->level_jabatan));
        $jab = strtoupper(trim((string) $m->jabatan_terakhir));

        $isCulled = (str_contains($ket, 'gagal') || str_contains($statusKel, 'gagal') || str_contains($ket, 'culled'));
        $isResign = (str_contains($ket, 'resign') || str_contains($ket, 'phk') || str_contains($statusRes, 'resign') || str_contains($statusRes, 'phk') || !empty($tglRes));
        $isEksis = ($ket === 'eksis' || $ket === 'aktif' || str_contains($ket, 'eksis') || str_contains($ket, 'aktif')) && !$isCulled && !$isResign;

        if ($isCulled) {
            $culledCountFromDb++;
        } elseif ($isResign) {
            $resignCount++;
        } elseif ($isEksis) {
            $eksisCount++;

            // Position breakdown among active/eksis strictly:
            if (in_array($lvl, ['PC', 'P', 'DIR'], true) || str_contains($jab, 'PC') || str_contains($jab, 'PROJECT COORDINATOR') || str_contains($jab, 'DIREKTUR')) {
                $posPC++;
            } elseif ($lvl === 'GM' || str_contains($jab, 'GM') || str_contains($jab, 'GENERAL MANAGER') || str_contains($jab, 'GROUP MANAGER')) {
                $posGM++;
            } elseif (in_array($lvl, ['MGR', 'MK', 'MM', 'MANAGER'], true) || str_contains($jab, 'MGR') || str_contains($jab, 'MANAGER')) {
                $posMGR++;
            } elseif (in_array($lvl, ['ASKEP', 'KTU'], true) || str_contains($jab, 'ASKEP') || str_contains($jab, 'KTU') || str_contains($jab, 'ASSISTANT KEPALA')) {
                $posAskep++;
            } else {
                $posAsisten++;
            }
        }
    }

    $asistenCountInDb = count($matchingAsisten);
    if ($asistenCountInDb > 0) {
        $trainee = max((int) $prog->jumlah_trainee, $asistenCountInDb);
        $culled = $culledCountFromDb > 0 ? $culledCountFromDb : (int) $prog->jumlah_culled;
    } else {
        $trainee = (int) $prog->jumlah_trainee;
        $culled = (int) $prog->jumlah_culled;
    }

    $passed = max(0, $trainee - $culled);

    $totalTraineeAll += $trainee;
    $totalCulledAll += $culled;
    $totalPassedAll += $passed;
    $totalResignAll += $resignCount;
    $totalEksisAll += $eksisCount;
    $totalPcAll += $posPC;
    $totalGmAll += $posGM;
    $totalMgrAll += $posMGR;
    $totalAskepAll += $posAskep;
    $totalAsistenAll += $posAsisten;

    $byCat[$cat]['trainee'] += $trainee;
    $byCat[$cat]['culled'] += $culled;
    $byCat[$cat]['passed'] += $passed;
    $byCat[$cat]['resign'] += $resignCount;
    $byCat[$cat]['eksis'] += $eksisCount;
    $byCat[$cat]['pc'] += $posPC;
    $byCat[$cat]['gm'] += $posGM;
    $byCat[$cat]['mgr'] += $posMGR;
    $byCat[$cat]['askep'] += $posAskep;
    $byCat[$cat]['asisten'] += $posAsisten;
}

echo "=== SUMMARY BY CATEGORY ===\n";
foreach ($byCat as $cat => $s) {
    echo "Category $cat: Trainee={$s['trainee']}, Culled={$s['culled']}, Passed={$s['passed']}, Resign={$s['resign']}, Eksis={$s['eksis']} | PC={$s['pc']}, GM={$s['gm']}, MGR={$s['mgr']}, ASKEP={$s['askep']}, ASISTEN={$s['asisten']}\n";
}

echo "\n=== GLOBAL SUMMARY ===\n";
echo "Trainee: $totalTraineeAll\n";
echo "Culled: $totalCulledAll\n";
echo "Passed: $totalPassedAll\n";
echo "Resign: $totalResignAll\n";
echo "Eksis: $totalEksisAll\n";
echo "PC: $totalPcAll, GM: $totalGmAll, MGR: $totalMgrAll, ASKEP: $totalAskepAll, ASISTEN: $totalAsistenAll\n";
echo "Sum of positions: " . ($totalPcAll + $totalGmAll + $totalMgrAll + $totalAskepAll + $totalAsistenAll) . "\n";
