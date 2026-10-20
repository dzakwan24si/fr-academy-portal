<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Monitoring\AsistenController;
use Illuminate\Http\Request;

$controller = new AsistenController();
$req = new Request(['kategori' => 'E']);
$res = $controller->getMonitoringProgramData($req);
$data = json_decode($res->getContent(), true);

echo "=== DMT ROWS (Kategori E) ===\n";
foreach ($data['data'] as $r) {
    echo "{$r['nama_program']}: Trainee={$r['jumlah_trainee']}, Culled={$r['jumlah_culled']}, Passed={$r['jumlah_passed']}, Resign={$r['jumlah_resign']}, Eksis={$r['jumlah_eksis']} | PC={$r['posisi_pc']}, GM={$r['posisi_gm']}, MGR={$r['posisi_mgr']}, ASKEP={$r['posisi_askep']}, ASISTEN={$r['posisi_asisten']}\n";
}
