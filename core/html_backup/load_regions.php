<?php
/**
 * Load Regions from API
 */

require_once 'core/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'core/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Region;
use App\Lib\DataPlans;
use Illuminate\Support\Str;

echo "🔍 Loading Regions from API\n";
echo "==========================\n\n";

$dataPlans = new DataPlans();

// Fetch regions from API
echo "Fetching regions from API...\n";
$regions = $dataPlans->fetchRegions();

if (isset($regions['error'])) {
    echo "❌ Error: " . $regions['error'] . "\n";
    exit(1);
}

echo "✅ Found " . count($regions) . " regions\n\n";

// Process regions
$data = [];
$newCount = 0;
$existingCount = 0;

foreach ($regions as $item) {
    $regionExist = Region::where('code', $item['code'])->exists();
    
    if (!$regionExist) {
        $region = [];
        $region['name'] = $item['name'];
        $region['code'] = $item['code'];
        $region['countries_list'] = json_encode($item['countries'] ?? []);
        $region['status'] = 1;
        $region['created_at'] = now();
        $region['updated_at'] = now();
        $data[] = $region;
        $newCount++;
    } else {
        $existingCount++;
    }
}

if (empty($data)) {
    echo "ℹ️  No new regions to add. All regions already exist.\n";
} else {
    Region::insert($data);
    echo "✅ Successfully added {$newCount} new regions\n";
}

echo "📊 Summary:\n";
echo "   - New regions: {$newCount}\n";
echo "   - Existing regions: {$existingCount}\n";
echo "   - Total regions in API: " . count($regions) . "\n";

// Check database
$dbCount = Region::count();
echo "   - Total regions in database: {$dbCount}\n";

echo "\n🎯 Regions loaded successfully!\n";
