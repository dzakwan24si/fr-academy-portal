<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\AsistenProgramMonitoring;

$progs = AsistenProgramMonitoring::where('kode_kategori', 'A')->get();

$n = count($progs);
echo "Total FAT programs: $n\n";

// Let's test combinations from index 55 onwards
$subProgs = [];
foreach ($progs as $idx => $p) {
    if ($idx >= 55) {
        $subProgs[] = [
            'id' => $p->id,
            'name' => $p->nama_program,
            'trainee' => (int)$p->jumlah_trainee,
            'culled' => (int)$p->jumlah_culled,
            'passed' => (int)($p->jumlah_trainee - $p->jumlah_culled),
        ];
    }
}

$m = count($subProgs);
echo "Testing $m programs from index 55 onwards:\n";
foreach ($subProgs as $i => $sp) {
    echo "  [$i] {$sp['name']}: Trainee={$sp['trainee']}, Culled={$sp['culled']}, Passed={$sp['passed']}\n";
}

for ($mask = 1; $mask < (1 << $m); $mask++) {
    $sumT = 0; $sumC = 0; $sumP = 0;
    for ($i = 0; $i < $m; $i++) {
        if ($mask & (1 << $i)) {
            $sumT += $subProgs[$i]['trainee'];
            $sumC += $subProgs[$i]['culled'];
            $sumP += $subProgs[$i]['passed'];
        }
    }
    if ($sumT === 247 && $sumC === 30 && $sumP === 217) {
        echo "\n>>> EXACT MATCH FOUND! <<<\n";
        for ($i = 0; $i < $m; $i++) {
            if ($mask & (1 << $i)) {
                echo "  + {$subProgs[$i]['name']} (T:{$subProgs[$i]['trainee']}, C:{$subProgs[$i]['culled']})\n";
            }
        }
    }
}
