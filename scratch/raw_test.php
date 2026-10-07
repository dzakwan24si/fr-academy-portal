<?php

use App\Http\Controllers\Api\V1\TrainingRecordExportController;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Http\Request;

require __DIR__.'/../vendor/autoload.php';
$app = require __DIR__.'/../bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$req = Request::create('/api/v1/training-records/export?per_page=2', 'GET');
$ctrl = new TrainingRecordExportController;
$response = $ctrl->export($req);

echo $response->getContent()."\n";
