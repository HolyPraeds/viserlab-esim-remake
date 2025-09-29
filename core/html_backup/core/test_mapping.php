<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Test Fixed Mapping ===\n\n";

$testRegions = [
    ['name' => 'Europe (40+ areas)', 'slug' => 'EU-42'],
    ['name' => 'South America (15+ areas)', 'slug' => 'SA-18'],
    ['name' => 'North America (3 areas)', 'slug' => 'NA-3'],
    ['name' => 'Africa (25+ areas)', 'slug' => 'AF-29'],
    ['name' => 'Global (130+ areas)', 'slug' => 'GL-139'],
    ['name' => 'Middle East', 'slug' => 'ME-13'],
    ['name' => 'Caribbean (20+ areas)', 'slug' => 'CB-25'],
    ['name' => 'Australia & New Zealand', 'slug' => 'AUNZ-2'],
];

foreach ($testRegions as $test) {
    $bucket = mapRegionNameBySlug($test['slug'], $test['name']);
    echo $test['name'] . " (" . $test['slug'] . ") → " . ($bucket ?: 'NULL') . "\n";
}




