<?php

/**
 * Скрипт для проверки планов в базе данных
 * Запуск: php check_plans_in_db.php
 */

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Plan;
use App\Models\Region;
use App\Models\Country;
use App\Constants\Status;

echo "=== Проверка планов в базе данных ===\n\n";

// 1. Общее количество планов
$totalPlans = Plan::count();
echo "1. Общее количество планов в БД: {$totalPlans}\n";

if ($totalPlans == 0) {
    echo "   ❌ ПЛАНЫ ОТСУТСТВУЮТ В БД!\n";
    echo "   Нужно синхронизировать планы через API или добавить вручную.\n\n";
    exit;
}

// 2. Активные планы
$activePlans = Plan::where('status', Status::ENABLE)->count();
echo "   Активных планов: {$activePlans}\n";
echo "   Неактивных планов: " . ($totalPlans - $activePlans) . "\n\n";

// 3. Планы с регионами
$plansWithRegions = Plan::whereHas('region', function($q) {
    $q->where('status', Status::ENABLE);
})->where('status', Status::ENABLE)->count();

echo "2. Планы с активными регионами: {$plansWithRegions}\n\n";

// 4. Планы со странами
$plansWithCountries = Plan::whereHas('countries', function($q) {
    $q->where('status', Status::ENABLE);
})->where('status', Status::ENABLE)->count();

echo "3. Планы с активными странами: {$plansWithCountries}\n\n";

// 5. Показываем примеры планов
echo "4. Примеры планов (первые 5):\n";
$samplePlans = Plan::with('region', 'countries')->limit(5)->get();

if ($samplePlans->isEmpty()) {
    echo "   ❌ Не удалось загрузить планы\n\n";
} else {
    foreach ($samplePlans as $plan) {
        echo "   - ID: {$plan->id}, Название: {$plan->name}\n";
        echo "     Статус: " . ($plan->status == Status::ENABLE ? 'Активен' : 'Неактивен') . "\n";
        echo "     Регион: " . ($plan->region ? $plan->region->name : 'НЕТ') . "\n";
        echo "     Страны: " . $plan->countries->count() . "\n";
        echo "     Цена: {$plan->retail_price} {$plan->price_currency}\n";
        echo "\n";
    }
}

// 6. Проверяем регионы с планами
echo "5. Регионы с планами:\n";
$regionsWithPlans = Region::whereHas('plans', function($q) {
    $q->where('status', Status::ENABLE);
})->where('status', Status::ENABLE)->get();

if ($regionsWithPlans->isEmpty()) {
    echo "   ❌ Нет регионов с активными планами\n\n";
} else {
    echo "   Найдено регионов: " . $regionsWithPlans->count() . "\n";
    foreach ($regionsWithPlans->take(5) as $region) {
        $plansCount = $region->plans()->where('status', Status::ENABLE)->count();
        echo "   - {$region->name} (slug: {$region->slug}): {$plansCount} планов\n";
    }
    echo "\n";
}

// 7. Проверяем страны с планами
echo "6. Страны с планами:\n";
$countriesWithPlans = Country::whereHas('plans', function($q) {
    $q->where('status', Status::ENABLE)
      ->whereHas('region', fn($q) => $q->where('status', Status::ENABLE));
})->where('status', Status::ENABLE)->get();

if ($countriesWithPlans->isEmpty()) {
    echo "   ❌ Нет стран с активными планами\n\n";
} else {
    echo "   Найдено стран: " . $countriesWithPlans->count() . "\n";
    foreach ($countriesWithPlans->take(5) as $country) {
        $plansCount = $country->plans()->where('status', Status::ENABLE)->count();
        echo "   - {$country->name} (slug: {$country->slug}): {$plansCount} планов\n";
    }
    echo "\n";
}

// 8. Рекомендации
echo "=== РЕКОМЕНДАЦИИ ===\n\n";

if ($activePlans == 0) {
    echo "❌ КРИТИЧНО: Нет активных планов!\n";
    echo "   Решение: Активировать планы в админке или через БД:\n";
    echo "   UPDATE plans SET status = 1 WHERE status = 0;\n\n";
}

if ($plansWithRegions == 0) {
    echo "❌ КРИТИЧНО: Нет планов с активными регионами!\n";
    echo "   Решение: Проверить связи планов с регионами\n\n";
}

if ($regionsWithPlans->isEmpty()) {
    echo "❌ КРИТИЧНО: Нет регионов с планами!\n";
    echo "   Решение: Синхронизировать планы через API или добавить связи вручную\n\n";
}

if ($activePlans > 0 && $plansWithRegions > 0) {
    echo "✅ Планы есть в БД и связаны с регионами\n";
    echo "   Если планы не отображаются на сайте, проверьте:\n";
    echo "   1. Консоль браузера на ошибки JavaScript\n";
    echo "   2. Логи Laravel: storage/logs/laravel.log\n";
    echo "   3. Правильность slug регионов/стран в URL\n";
}

echo "\n=== Проверка завершена ===\n";







