<?php

use Illuminate\Contracts\Console\Kernel;
use App\Models\Country;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

$code = strtoupper($argv[1] ?? 'LV');

$country = Country::where('code', $code)->first();
if (!$country) {
    echo "Country not found: {$code}\n";
    exit(1);
}

$count = $country->plans()->count();
echo "{$code}: {$count}\n";






