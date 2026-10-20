<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\Asisten;
use App\Models\Monitoring\AsistenProgramMonitoring;

$programs = AsistenProgramMonitoring::orderBy('kode_kategori')->orderBy('no')->get();

$asistenRecords = Asisten::select([
    'id', 'nama', 'program', 'keterangan', 'status_kelulusan', 'status_resign',
    'jabatan_terakhir', 'level_jabatan', 'tanggal_resign'
])->whereNotNull('program')->get();

$norm = function ($s) {
    $str = strtolower((string) $s);
    $str = str_replace(['field assistant', 'fat'], 'fat', $str);
    $str = str_replace(['mill assistant', 'mat'], 'mat', $str);
    $str = str_replace(['admin assistant', 'admin', 'aat'], 'aat', $str);
    $str = str_replace(['traction assistant', 'tat'], 'tat', $str);
    $str = str_replace(['downstream management trainee', 'downstream management training', 'downstream management', 'dmt'], 'dmt', $str);
    return preg_replace('/[^a-zA-Z0-9]/', '', $str);
};

$asistenByNormProgram = [];
foreach ($asistenRecords as $a) {
    $key = $norm($a->program);
    if ($key !== '') {
        $asistenByNormProgram[$key][] = $a;
    }
}

echo "Total distinct normalized programs in asisten: " . count($asistenByNormProgram) . "\n";

// Let's test by category:
$catTotals = [];
foreach ($programs as $prog) {
    $cat = $prog->kode_kategori;
    if (!isset($catTotals[$cat])) {
        $catTotals[$cat] = [
            'trainee' => 0, 'culled' => 0, 'passed' => 0, 'resign' => 0, 'eksis' => 0,
            'pc' => 0, 'gm' => 0, 'mgr' => 0, 'askep' => 0, 'asisten' => 0, 'matched_asisten' => 0
        ];
    }

    $normKey = $norm($prog->nama_program);
    $matching = $asistenByNormProgram[$normKey] ?? [];
    if (empty($matching)) {
        foreach ($asistenByNormProgram as $k => $list) {
            if (str_contains($k, $normKey) || str_contains($normKey, $k)) {
                $matching = array_merge($matching, $list);
            }
        }
    }

    $catTotals[$cat]['matched_asisten'] += count($matching);

    foreach ($matching as $m) {
        $ket = strtolower(trim((string)$m->keterangan));
        $statusKel = strtolower(trim((string)$m->status_kelulusan));
        $statusRes = strtolower(trim((string)$m->status_resign));
        $tglRes = trim((string)$m->tanggal_resign);
        $lvl = strtoupper(trim((string)$m->level_jabatan));
        $jab = strtoupper(trim((string)$m->jabatan_terakhir));

        $isCulled = (str_contains($ket, 'gagal') || str_contains($statusKel, 'gagal') || str_contains($ket, 'culled'));
        $isResign = (str_contains($ket, 'resign') || str_contains($ket, 'phk') || str_contains($statusRes, 'resign') || str_contains($statusRes, 'phk') || !empty($tglRes));
        $isEksis = ($ket === 'eksis' || $ket === 'aktif' || str_contains($ket, 'eksis') || str_contains($ket, 'aktif')) && !$isCulled && !$isResign;

        if ($isCulled) {
            $catTotals[$cat]['culled']++;
        } elseif ($isResign) {
            $catTotals[$cat]['resign']++;
        } elseif ($isEksis) {
            $catTotals[$cat]['eksis']++;

            if (in_array($lvl, ['PC', 'P', 'DIR'], true) || str_contains($jab, 'PC') || str_contains($jab, 'PROJECT COORDINATOR') || str_contains($jab, 'DIREKTUR')) {
                $catTotals[$cat]['pc']++;
            } elseif ($lvl === 'GM' || str_contains($jab, 'GM') || str_contains($jab, 'GENERAL MANAGER')) {
                $catTotals[$cat]['gm']++;
            } elseif (in_array($lvl, ['MGR', 'MK', 'MM', 'MANAGER'], true) || str_contains($jab, 'MGR') || str_contains($jab, 'MANAGER')) {
                $catTotals[$cat]['mgr']++;
            } elseif (in_array($lvl, ['ASKEP', 'KTU'], true) || str_contains($jab, 'ASKEP') || str_contains($jab, 'KTU') || str_contains($jab, 'ASSISTANT KEPALA')) {
                $catTotals[$cat]['askep']++;
            } else {
                $catTotals[$cat]['asisten']++;
            }
        }
    }
}

foreach ($catTotals as $cat => $t) {
    echo "Category {$cat}: Matched: {$t['matched_asisten']}, Resign: {$t['resign']}, Culled: {$t['culled']}, Eksis: {$t['eksis']} | PC: {$t['pc']}, GM: {$t['gm']}, MGR: {$t['mgr']}, ASKEP: {$t['askep']}, ASISTEN: {$t['asisten']}\n";
}
