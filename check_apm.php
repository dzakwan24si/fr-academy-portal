<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Monitoring\AsistenProgramMonitoring;
use Illuminate\Support\Facades\Schema;

echo "Columns in asisten_program_monitoring: " . implode(', ', Schema::getColumnListing('asisten_program_monitoring')) . "\n";
echo "Count: " . AsistenProgramMonitoring::count() . "\n";

$first3 = AsistenProgramMonitoring::limit(3)->get();
echo json_encode($first3, JSON_PRETTY_PRINT) . "\n";
