<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Monitoring\AsistenController;
use Illuminate\Http\Request;

$controller = new AsistenController();
$req = new Request();
$res = $controller->getMonitoringProgramData($req);
$data = json_decode($res->getContent(), true);

echo "Status: " . $data['status'] . "\n";
echo "Total rows: " . count($data['data']) . "\n";
echo "Summary:\n";
print_r($data['summary']);
