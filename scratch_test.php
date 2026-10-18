<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\Asisten;

$allAsisten = Asisten::all();
$keywords = ['FIELD ASSISTANT', 'FAT'];
$minYear = 2024;

$asistenRecent = $allAsisten->filter(function ($a) use ($keywords, $minYear) {
    $pName = strtoupper((string) $a->program);
    $matchesProg = false;
    foreach ($keywords as $kw) {
        if (str_contains($pName, $kw)) {
            $matchesProg = true;
            break;
        }
    }
    if (! $matchesProg) {
        return false;
    }

    preg_match('/\b(20\d{2})\b/', $pName, $matches);
    if (! empty($matches[1])) {
        return (int) $matches[1] >= $minYear;
    }

    return true;
});

echo "Total FAT 2024-2026 in asisten: " . $asistenRecent->count() . PHP_EOL;

// Breakdown by program
$byProg = $asistenRecent->groupBy('program');
foreach ($byProg as $prog => $list) {
    $resignCount = $list->filter(fn ($a) => in_array(strtoupper(trim((string) ($a->status_karyawan ?: $a->keterangan ?: ''))), ['RESIGN', 'PHK', 'TERMINATE']))->count();
    $activeCount = $list->count() - $resignCount;
    echo "$prog | total: {$list->count()} | resign: $resignCount | active: $activeCount" . PHP_EOL;
}

$totResign = $asistenRecent->filter(fn ($a) => in_array(strtoupper(trim((string) ($a->status_karyawan ?: $a->keterangan ?: ''))), ['RESIGN', 'PHK', 'TERMINATE']))->count();
echo "Total resign: $totResign" . PHP_EOL;
