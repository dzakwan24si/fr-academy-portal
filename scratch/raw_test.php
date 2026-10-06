<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$req = Illuminate\Http\Request::create('/api/v1/training-records/export?per_page=2', 'GET');
$ctrl = new App\Http\Controllers\Api\V1\TrainingRecordExportController();
$response = $ctrl->export($req);

echo $response->getContent() . "\n";
