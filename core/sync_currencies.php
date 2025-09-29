<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "Starting currency sync...\n";

try {
    $currencies = \App\Models\Currency::pluck('api_currency')->toArray();
    $currencyLayer = new \App\Lib\CurrencyLayer();
    $currencyLayer->updateRates($currencies);
    echo "Currency sync completed successfully.\n";
} catch (Throwable $e) {
    echo "Currency sync failed: ".$e->getMessage()."\n";
}






