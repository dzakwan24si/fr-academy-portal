<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\Asisten;

$active = Asisten::where('keterangan', 'Aktif')->get();
$byCatLvl = [];

foreach ($active as $a) {
    $prog = $a->program;
    $p = strtoupper($prog);
    $cat = 'OTHER';
    if (str_contains($p, 'FIELD ASSISTANT') || str_contains($p, 'STAFF OPERATION') || str_contains($p, 'FAT') || str_contains($p, 'ESTATE CADET')) {
        $cat = 'FAT';
    } elseif (str_contains($p, 'MILL ASSISTANT') || str_contains($p, 'MAT')) {
        $cat = 'MAT';
    } elseif (str_contains($p, 'ADMIN ASSISTANT') || str_contains($p, 'AAT')) {
        $cat = 'AAT';
    } elseif (str_contains($p, 'TRACTION ASSISTANT') || str_contains($p, 'TAT')) {
        $cat = 'TAT';
    } elseif (str_contains($p, 'DOWNSTREAM') || str_contains($p, 'DMT')) {
        $cat = 'DMT';
    }

    if (!isset($byCatLvl[$cat])) {
        $byCatLvl[$cat] = ['PC' => 0, 'GM' => 0, 'MGR' => 0, 'ASKEP' => 0, 'ASISTEN' => 0, 'total' => 0];
    }

    $lvl = strtoupper(trim((string)$a->level_jabatan));
    $jab = strtoupper(trim((string)$a->jabatan_terakhir));

    $byCatLvl[$cat]['total']++;

    // Prioritize LVL column first:
    if (in_array($lvl, ['PC', 'P', 'DIR'], true)) {
        $byCatLvl[$cat]['PC']++;
    } elseif ($lvl === 'GM') {
        $byCatLvl[$cat]['GM']++;
    } elseif (in_array($lvl, ['MGR', 'MK', 'MM', 'MANAGER'], true)) {
        $byCatLvl[$cat]['MGR']++;
    } elseif (in_array($lvl, ['ASKEP', 'KTU'], true)) {
        $byCatLvl[$cat]['ASKEP']++;
    } elseif (in_array($lvl, ['ASISTEN', 'ASST', 'STAFF'], true)) {
        $byCatLvl[$cat]['ASISTEN']++;
    } else {
        // Fallback to jab
        if (str_contains($jab, 'PC') || str_contains($jab, 'CONTROLLER') || str_contains($jab, 'DIREKTUR')) {
            $byCatLvl[$cat]['PC']++;
        } elseif (str_contains($jab, 'GENERAL MANAGER') || str_contains($jab, 'GROUP MANAGER') || str_contains($jab, 'GM')) {
            $byCatLvl[$cat]['GM']++;
        } elseif (str_contains($jab, 'MANAGER') || str_contains($jab, 'MGR')) {
            $byCatLvl[$cat]['MGR']++;
        } elseif (str_contains($jab, 'ASKEP') || str_contains($jab, 'KTU') || str_contains($jab, 'KEPALA TATA')) {
            $byCatLvl[$cat]['ASKEP']++;
        } else {
            $byCatLvl[$cat]['ASISTEN']++;
        }
    }
}

echo "=== BREAKDOWN BY LVL FIRST ===\n";
foreach ($byCatLvl as $cat => $c) {
    echo "$cat: Total={$c['total']} | PC={$c['PC']}, GM={$c['GM']}, MGR={$c['MGR']}, ASKEP={$c['ASKEP']}, ASISTEN={$c['ASISTEN']}\n";
}
