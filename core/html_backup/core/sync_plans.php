<?php

use Illuminate\Contracts\Console\Kernel;

// Ensure CLI-safe server vars
$_SERVER['REQUEST_URI'] = $_SERVER['REQUEST_URI'] ?? '/';
$_SERVER['REQUEST_METHOD'] = $_SERVER['REQUEST_METHOD'] ?? 'GET';

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "Starting plan sync...\n";

try {
    $dataPlan = dataPlans();
    $plans    = $dataPlan->fetchPlans();

    if (isset($plans['error'])) {
        throw new Exception($plans['error']);
    }

    $dataPlan->addOrUpdatePlans($plans);
    echo "Plan sync completed successfully.\n";
} catch (Throwable $e) {
    echo "Plan sync failed: ".$e->getMessage()."\n";
}


