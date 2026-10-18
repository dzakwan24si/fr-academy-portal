<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\Asisten;
use App\Models\Monitoring\AsistenProgramMonitoring;

$currentYear = 2026;
$minYear = $currentYear - 2; // 2024

$allAsisten = Asisten::all();

$keywords = ['FIELD ASSISTANT', 'FAT'];

$asistenRecent = $allAsisten->filter(function ($a) use ($keywords, $minYear) {
    $pName = strtoupper((string) $a->program);
    $matchesProg = false;
    foreach ($keywords as $kw) {
        if (str_contains($pName, $kw)) {
            $matchesProg = true;
            break;
        }
    }
    if (!$matchesProg) return false;

    preg_match('/\b(20\d{2})\b/', $pName, $matches);
    if (!empty($matches[1])) {
        return (int) $matches[1] >= $minYear;
    }
    return true;
});

echo "Total asistenRecent for FAT: " . $asistenRecent->count() . "\n";

$byKet = [];
foreach ($asistenRecent as $a) {
    $ket = $a->keterangan;
    $byKet[$ket] = ($byKet[$ket] ?? 0) + 1;
}
echo "By keterangan in asistenRecent: " . json_encode($byKet) . "\n";

// Now let's check which programs are in batchPrograms for FAT
$batchPrograms = AsistenProgramMonitoring::where(function ($q) use ($minYear) {
    $q->where('tahun_training', '>=', $minYear)
        ->orWhere('tahun_lulus', '>=', $minYear)
        ->orWhere('tahun_rekrut', '>=', $minYear);
})->get()->filter(function ($b) use ($keywords) {
    $name = strtoupper((string) $b->nama_program);
    $cat = strtoupper((string) $b->kategori_program);
    foreach ($keywords as $kw) {
        if (str_contains($name, $kw) || str_contains($cat, $kw)) return true;
    }
    return false;
});

echo "\nFAT batches in batchPrograms (minYear $minYear):\n";
foreach ($batchPrograms as $b) {
    echo "  {$b->nama_program} | Trainee: {$b->jumlah_trainee}, Culled: {$b->jumlah_culled}\n";
}

// Now let's check asisten matching THESE EXACT BATCHES:
$batchNames = $batchPrograms->pluck('nama_program')->all();
echo "\nMatching these exact batches:\n";
$norm = function ($s) {
    $str = strtolower((string) $s);
    $str = str_replace(['field assistant', 'fat'], 'fat', $str);
    return preg_replace('/[^a-zA-Z0-9]/', '', $str);
};

$normBatchNames = array_map($norm, $batchNames);

$matchingAsisten = $allAsisten->filter(function($a) use ($norm, $normBatchNames) {
    return in_array($norm($a->program), $normBatchNames, true);
});

echo "Matching asisten count: " . $matchingAsisten->count() . "\n";
$byKetExact = [];
$byResignExact = 0;
$byEksisExact = 0;
$byCulledExact = 0;
foreach ($matchingAsisten as $a) {
    $ket = strtolower(trim((string)$a->keterangan));
    $statusKel = strtolower(trim((string)$a->status_kelulusan));
    $statusRes = strtolower(trim((string)$a->status_resign));
    $tglRes = trim((string)$a->tanggal_resign);

    $isCulled = (str_contains($ket, 'culled') || str_contains($ket, 'gagal'));
    $isResign = (str_contains($ket, 'resign') || str_contains($ket, 'phk') || str_contains($statusRes, 'resign') || str_contains($statusRes, 'phk') || !empty($tglRes));
    $isEksis = ($ket === 'eksis' || $ket === 'aktif') && !$isCulled && !$isResign;

    if ($isCulled) $byCulledExact++;
    elseif ($isResign) $byResignExact++;
    elseif ($isEksis) $byEksisExact++;
}
echo "Exact matches -> Resign: $byResignExact, Eksis: $byEksisExact, Culled: $byCulledExact\n";
