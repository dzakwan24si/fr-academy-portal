<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Http\Controllers\Program\RealisasiVsBatchController;

$ctrl = new RealisasiVsBatchController();

$ref = new ReflectionMethod($ctrl, 'buildRows');
$ref->setAccessible(true);

$originalRows = $ref->invoke($ctrl, 2026, 1, 'all', '');

echo "Original rows count: " . $originalRows->count() . PHP_EOL;
$sampleOriginal = [];
foreach ($originalRows as $r) {
    if ($r->realized_participants > 0) {
        $sampleOriginal[$r->program_type . '_' . $r->program_id] = [
            'name' => $r->program_name,
            'realized_batch' => $r->realized_batch,
            'realized_participants' => $r->realized_participants,
        ];
    }
}
echo "Programs with participants > 0 in Jan: " . count($sampleOriginal) . PHP_EOL;
echo json_encode(array_slice($sampleOriginal, 0, 5), JSON_PRETTY_PRINT) . PHP_EOL;
