<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Debug Region Mapping ===\n\n";

// Получаем все активные регионы
$regions = \App\Models\Region::active()->with(['plans' => function($q){
    $q->active()->with('countries');
}])->get();

echo "Total active regions: " . $regions->count() . "\n\n";

$buckets = [
    'Africa' => [], 'Asia' => [], 'Europe' => [], 'North America' => [], 'South America' => [], 'Oceania' => []
];

foreach ($regions as $region) {
    echo "Region: " . $region->name . " (slug: " . $region->slug . ")\n";
    
    $bucket = mapRegionNameBySlug($region->slug, $region->name);
    echo "  Mapped to bucket: " . ($bucket ?: 'NULL') . "\n";
    
    if (!isset($buckets[$bucket])) {
        echo "  SKIPPED: bucket not found\n";
        continue;
    }
    
    $validPlans = $region->plans->filter(fn($p) => $p->countries->isNotEmpty());
    if ($validPlans->isEmpty()) {
        echo "  SKIPPED: no valid plans\n";
        continue;
    }
    
    echo "  VALID: " . $validPlans->count() . " plans\n";
    $buckets[$bucket][] = $region;
}

echo "\n=== Bucket Results ===\n";
foreach ($buckets as $bucketName => $items) {
    echo $bucketName . ": " . count($items) . " regions\n";
    if (!empty($items)) {
        foreach ($items as $region) {
            echo "  - " . $region->name . " (" . $region->slug . ")\n";
        }
    }
}




