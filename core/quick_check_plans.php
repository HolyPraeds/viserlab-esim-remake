<?php
/**
 * Быстрая проверка планов
 * Запуск: php quick_check_plans.php
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Plan;
use App\Models\Region;
use App\Constants\Status;

echo "=== Быстрая проверка планов ===\n\n";

// 1. Всего планов
$all = Plan::count();
echo "Всего планов в БД: {$all}\n";

// 2. Активных
$active = Plan::where('status', Status::ENABLE)->count();
echo "Активных планов: {$active}\n";

// 3. С регионами
$withRegions = Plan::whereNotNull('region_id')->where('status', Status::ENABLE)->count();
echo "Планов с регионами: {$withRegions}\n";

// 4. Примеры
echo "\nПримеры планов:\n";
$plans = Plan::where('status', Status::ENABLE)->limit(3)->get(['id', 'name', 'status', 'region_id']);
foreach ($plans as $p) {
    echo "  ID:{$p->id} - {$p->name} (region_id: {$p->region_id})\n";
}

// 5. Регионы
echo "\nРегионы с планами:\n";
$regions = Region::where('status', Status::ENABLE)->withCount(['plans' => function($q) {
    $q->where('status', Status::ENABLE);
}])->having('plans_count', '>', 0)->limit(5)->get(['id', 'name', 'slug']);
foreach ($regions as $r) {
    echo "  {$r->name} (slug: {$r->slug}) - {$r->plans_count} планов\n";
}

echo "\n";







