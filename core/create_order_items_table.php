<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== СОЗДАНИЕ ТАБЛИЦЫ ORDER_ITEMS ===\n\n";

// Создаем таблицу order_items
echo "Создание таблицы order_items...\n";
if (!Schema::hasTable('order_items')) {
    Schema::create('order_items', function ($table) {
        $table->id();
        $table->unsignedBigInteger('order_id');
        $table->unsignedBigInteger('plan_id');
        $table->integer('quantity')->default(1);
        $table->decimal('amount', 28, 8);
        $table->timestamps();
    });
    echo "✅ Таблица order_items создана\n";
} else {
    echo "ℹ️ Таблица order_items уже существует\n";
}

// Проверяем структуру
echo "\nСтруктура таблицы order_items:\n";
$columns = DB::select('DESCRIBE order_items');
foreach($columns as $column) {
    echo "   {$column->Field} - {$column->Type}\n";
}

echo "\n✅ ТАБЛИЦА ORDER_ITEMS ГОТОВА!\n";

