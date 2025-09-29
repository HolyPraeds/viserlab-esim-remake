<?php

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== НАСТРОЙКА СИСТЕМЫ ДЕПОЗИТОВ И ПОКУПОК ===\n\n";

// 1. Создаем таблицу orders
echo "1. Создание таблицы orders...\n";
if (!Schema::hasTable('orders')) {
    Schema::create('orders', function ($table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('plan_id');
        $table->decimal('amount', 28, 8);
        $table->integer('quantity')->default(1);
        $table->tinyInteger('status')->default(0);
        $table->string('payment_method')->default('gateway');
        $table->string('gateway_code')->nullable();
        $table->string('trx')->unique();
        $table->timestamps();
    });
    echo "✅ Таблица orders создана\n";
} else {
    echo "ℹ️ Таблица orders уже существует\n";
}

// 2. Создаем таблицу esims
echo "\n2. Создание таблицы esims...\n";
if (!Schema::hasTable('esims')) {
    Schema::create('esims', function ($table) {
        $table->id();
        $table->unsignedBigInteger('user_id');
        $table->unsignedBigInteger('plan_id');
        $table->unsignedBigInteger('order_id');
        $table->tinyInteger('status')->default(1);
        $table->timestamp('activation_date')->nullable();
        $table->timestamp('expiry_date')->nullable();
        $table->decimal('data_used', 28, 8)->default(0);
        $table->decimal('data_limit', 28, 8)->default(0);
        $table->string('trx')->unique();
        $table->text('qr_code')->nullable();
        $table->text('activation_code')->nullable();
        $table->timestamps();
    });
    echo "✅ Таблица esims создана\n";
} else {
    echo "ℹ️ Таблица esims уже существует\n";
}

// 3. Добавляем order_id в таблицу deposits
echo "\n3. Обновление таблицы deposits...\n";
if (!Schema::hasColumn('deposits', 'order_id')) {
    Schema::table('deposits', function ($table) {
        $table->unsignedBigInteger('order_id')->nullable()->after('user_id');
    });
    echo "✅ Добавлено поле order_id в таблицу deposits\n";
} else {
    echo "ℹ️ Поле order_id уже существует в таблице deposits\n";
}

// 4. Проверяем структуру таблиц
echo "\n4. Проверка структуры таблиц...\n";

echo "Таблица orders:\n";
$orderColumns = DB::select('DESCRIBE orders');
foreach($orderColumns as $column) {
    echo "   {$column->Field} - {$column->Type}\n";
}

echo "\nТаблица esims:\n";
$esimColumns = DB::select('DESCRIBE esims');
foreach($esimColumns as $column) {
    echo "   {$column->Field} - {$column->Type}\n";
}

echo "\nТаблица deposits (новые поля):\n";
$depositColumns = DB::select('DESCRIBE deposits');
foreach($depositColumns as $column) {
    if (in_array($column->Field, ['user_id', 'order_id', 'amount', 'charge', 'final_amount', 'trx'])) {
        echo "   {$column->Field} - {$column->Type}\n";
    }
}

echo "\n✅ СИСТЕМА ДЕПОЗИТОВ И ПОКУПОК НАСТРОЕНА!\n";
echo "\nДоступные маршруты:\n";
echo "- /user/deposit - Пополнение баланса\n";
echo "- /user/deposit/history - История депозитов\n";
echo "- /user/purchase - Покупка eSIM\n";
echo "- /user/purchase/{id} - Детали плана\n";
echo "- /user/orders - Мои заказы\n";
echo "\nСистема готова к использованию!\n";

