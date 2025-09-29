<?php
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Проверяем планы для Латвии
$lvPlansCount = \App\Models\Plan::whereHas('countries', function($q) {
    $q->where('code', 'LV');
})->count();

echo "LV plans: " . $lvPlansCount . "\n";

// Проверяем общее количество планов
$totalPlans = \App\Models\Plan::count();
echo "Total plans: " . $totalPlans . "\n";

// Проверяем планы для Франции
$frPlansCount = \App\Models\Plan::whereHas('countries', function($q) {
    $q->where('code', 'FR');
})->count();

echo "FR plans: " . $frPlansCount . "\n";




