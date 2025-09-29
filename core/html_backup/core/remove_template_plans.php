<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Finding Template Plans ===\n";

// Находим все шаблонные планы
$templatePlans = \App\Models\Plan::where(function($query) {
    $query->where('capacity_unit', '!=', 'B')
          ->orWhereNull('speed')
          ->orWhere('slug', '')
          ->orWhere('slug', '0');
})->get();

echo "Found " . $templatePlans->count() . " template plans:\n\n";

foreach ($templatePlans as $plan) {
    echo "Template Plan: " . $plan->name . "\n";
    echo "  ID: " . $plan->id . "\n";
    echo "  Slug: '" . $plan->slug . "'\n";
    echo "  Capacity Unit: " . $plan->capacity_unit . "\n";
    echo "  Speed: " . ($plan->speed ?: 'NULL') . "\n";
    echo "  Countries: " . $plan->countries->pluck('code')->implode(', ') . "\n";
    echo "---\n";
}

if ($templatePlans->count() > 0) {
    echo "\n=== Removing Template Plans ===\n";
    
    // Удаляем связи с странами
    foreach ($templatePlans as $plan) {
        $plan->countries()->detach();
        echo "Detached countries from plan: " . $plan->name . "\n";
    }
    
    // Удаляем сами планы
    $deletedCount = \App\Models\Plan::where(function($query) {
        $query->where('capacity_unit', '!=', 'B')
              ->orWhereNull('speed')
              ->orWhere('slug', '')
              ->orWhere('slug', '0');
    })->delete();
    
    echo "\nDeleted " . $deletedCount . " template plans\n";
    
    // Проверяем оставшиеся планы
    $remainingPlans = \App\Models\Plan::count();
    echo "Remaining plans: " . $remainingPlans . "\n";
    
    // Проверяем планы для США
    $usPlans = \App\Models\Plan::whereHas('countries', function($q) {
        $q->where('code', 'US');
    })->count();
    echo "US plans after cleanup: " . $usPlans . "\n";
    
} else {
    echo "\nNo template plans found!\n";
}




