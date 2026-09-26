<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Country;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;

echo "=== United Kingdom check ===\n\n";

$country = Country::where('slug', 'united-kingdom')
    ->orWhere('name', 'LIKE', '%United Kingdom%')
    ->first();

if (!$country) {
    echo "Country United Kingdom NOT FOUND in countries table.\n";
    $similar = Country::where('name', 'LIKE', '%United%')
        ->orWhere('slug', 'LIKE', '%uk%')
        ->orWhere('slug', 'LIKE', '%kingdom%')
        ->get(['id', 'name', 'slug', 'status']);
    echo "Similar countries:\n";
    foreach ($similar as $c) {
        echo "  - ID: {$c->id}, name: {$c->name}, slug: {$c->slug}, status: {$c->status}\n";
    }
    exit(1);
}

echo "Country found: ID={$country->id}, name={$country->name}, slug={$country->slug}, status={$country->status}\n\n";

$pivotCount = DB::table('country_plan')->where('country_id', $country->id)->count();
echo "Records in country_plan pivot: {$pivotCount}\n";

$plansAll = $country->plans()->count();
echo "Plans via relationship (all): {$plansAll}\n";

$plansFiltered = $country->plans()
    ->where('status', 1)
    ->whereHas('region', fn($q) => $q->where('status', 1))
    ->count();
echo "Plans with filters (active plan + active region): {$plansFiltered}\n\n";

if ($pivotCount > 0 && $plansFiltered == 0) {
    echo "Plans exist in pivot but are filtered out. Checking why:\n";
    $plans = $country->plans()->with('region')->get();
    foreach ($plans->take(5) as $p) {
        $r = $p->region;
        echo "  - Plan ID {$p->id}: status={$p->status}, region=" . ($r ? "{$r->name} (status={$r->status})" : 'null') . "\n";
    }
}
