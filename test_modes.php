<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Monitoring\AsistenController;
use Illuminate\Http\Request;

$controller = new AsistenController();

// 1. Test batch mode
$reqBatch = new Request(['view_mode' => 'batch']);
$resBatch = $controller->getMonitoringProgramData($reqBatch);
$dataBatch = json_decode($resBatch->getContent(), true);

echo "=== BATCH MODE ===\n";
echo "Total Rows: " . count($dataBatch['data']) . "\n";
echo "Summary: " . json_encode($dataBatch['summary']) . "\n";

// 2. Test aggregated mode
$reqAgg = new Request(['view_mode' => 'aggregated']);
$resAgg = $controller->getMonitoringProgramData($reqAgg);
$dataAgg = json_decode($resAgg->getContent(), true);

echo "\n=== AGGREGATED MODE ===\n";
echo "Total Rows: " . count($dataAgg['data']) . "\n";
echo "Summary: " . json_encode($dataAgg['summary']) . "\n";

// 3. Test Drilldown for PC in Field Assistant - I/2003
$reqDrill = new Request(['program' => 'Field Assistant - I/2003', 'filter_type' => 'pc']);
$resDrill = $controller->getDrilldownAsisten($reqDrill);
$dataDrill = json_decode($resDrill->getContent(), true);
echo "\n=== DRILLDOWN PC (Field Assistant - I/2003) ===\n";
echo "Count: " . $dataDrill['total'] . "\n";
foreach ($dataDrill['data'] as $d) {
    echo " - {$d['nama']} | LVL: {$d['level_jabatan']} | JAB: {$d['jabatan_terakhir']}\n";
}

// 4. Test Drilldown for Eksis in Field Assistant - I/2003
$reqDrillEksis = new Request(['program' => 'Field Assistant - I/2003', 'filter_type' => 'eksis']);
$resDrillEksis = $controller->getDrilldownAsisten($reqDrillEksis);
$dataDrillEksis = json_decode($resDrillEksis->getContent(), true);
echo "\n=== DRILLDOWN EKSIS (Field Assistant - I/2003) ===\n";
echo "Count: " . $dataDrillEksis['total'] . "\n";
foreach ($dataDrillEksis['data'] as $d) {
    echo " - {$d['nama']} | KET: {$d['keterangan']} | LVL: {$d['level_jabatan']} | JAB: {$d['jabatan_terakhir']}\n";
}
