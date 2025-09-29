<?php

use Illuminate\Contracts\Console\Kernel;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';

$kernel = $app->make(Kernel::class);
$kernel->bootstrap();

echo "Starting regions sync...\n";

try {
    $regions = dataPlans()->fetchRegions();
    if (isset($regions['error'])) {
        throw new Exception($regions['error']);
    }

    $insert = [];
    foreach ($regions as $item) {
        $exists = \App\Models\Region::where('slug', $item['code'])->exists();
        if (!$exists) {
            $insert[] = [
                'name' => $item['name'],
                'slug' => $item['code'],
                'countries_list' => json_encode(array_column($item['subLocationList'] ?? [], 'code')),
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        } else {
            \App\Models\Region::where('slug', $item['code'])->update([
                'countries_list' => json_encode(array_column($item['subLocationList'] ?? [], 'code')),
                'updated_at' => now(),
            ]);
        }
    }

    if ($insert) {
        \App\Models\Region::insert($insert);
    }

    echo "Regions sync completed.\n";
} catch (Throwable $e) {
    echo "Regions sync failed: ".$e->getMessage()."\n";
}






