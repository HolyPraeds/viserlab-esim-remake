<?php
/**
 * Детальная проверка планов в БД
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\Plan;
use App\Models\Region;
use App\Models\Country;
use App\Constants\Status;

echo "=== ДЕТАЛЬНАЯ ПРОВЕРКА ПЛАНОВ ===\n\n";

// 1. Проверка таблицы plans
echo "1. Таблица 'plans':\n";
$plansCount = DB::table('plans')->count();
echo "   Всего записей: {$plansCount}\n";

$activeCount = DB::table('plans')->where('status', Status::ENABLE)->count();
echo "   Активных (status=1): {$activeCount}\n";

$inactiveCount = DB::table('plans')->where('status', Status::DISABLE)->count();
echo "   Неактивных (status=0): {$inactiveCount}\n";

// Проверяем все статусы
$statusCounts = DB::table('plans')
    ->select('status', DB::raw('count(*) as count'))
    ->groupBy('status')
    ->get();
if ($statusCounts->isNotEmpty()) {
    echo "   По статусам:\n";
    foreach ($statusCounts as $sc) {
        echo "     status={$sc->status}: {$sc->count}\n";
    }
}

// 2. Проверка через модель
echo "\n2. Через модель Plan:\n";
$modelCount = Plan::count();
echo "   Plan::count() = {$modelCount}\n";

$allPlans = Plan::all();
echo "   Plan::all()->count() = " . $allPlans->count() . "\n";

// 3. Проверка с регионами
echo "\n3. Планы с регионами:\n";
$withRegion = Plan::whereNotNull('region_id')->count();
echo "   С region_id: {$withRegion}\n";

$withoutRegion = Plan::whereNull('region_id')->count();
echo "   Без region_id: {$withoutRegion}\n";

// 4. Проверка связей
echo "\n4. Проверка связей:\n";
$regionsWithPlans = Region::whereHas('plans')->count();
echo "   Регионов с планами: {$regionsWithPlans}\n";

$countriesWithPlans = Country::whereHas('plans')->count();
echo "   Стран с планами: {$countriesWithPlans}\n";

// 5. Показываем первые 5 записей (если есть)
echo "\n5. Первые 5 записей из таблицы plans:\n";
$sample = DB::table('plans')->limit(5)->get(['id', 'name', 'status', 'region_id', 'slug']);
if ($sample->isEmpty()) {
    echo "   ❌ Таблица пуста\n";
} else {
    foreach ($sample as $plan) {
        echo "   ID:{$plan->id} | name:{$plan->name} | status:{$plan->status} | region_id:{$plan->region_id} | slug:{$plan->slug}\n";
    }
}

// 6. Проверка структуры таблицы
echo "\n6. Структура таблицы plans:\n";
$columns = DB::select("SHOW COLUMNS FROM plans");
echo "   Колонок: " . count($columns) . "\n";
echo "   Первые 5 колонок: " . implode(', ', array_slice(array_column($columns, 'Field'), 0, 5)) . "...\n";

// 7. Проверка других возможных таблиц
echo "\n7. Поиск похожих таблиц:\n";
$tables = DB::select("SHOW TABLES");
$tableName = 'Tables_in_' . DB::connection()->getDatabaseName();
$planTables = array_filter($tables, function($t) use ($tableName) {
    return stripos($t->$tableName, 'plan') !== false;
});
if (!empty($planTables)) {
    foreach ($planTables as $t) {
        $count = DB::table($t->$tableName)->count();
        echo "   {$t->$tableName}: {$count} записей\n";
    }
} else {
    echo "   Других таблиц с 'plan' в названии не найдено\n";
}

echo "\n=== ПРОВЕРКА ЗАВЕРШЕНА ===\n";







