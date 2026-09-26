<?php
/**
 * Исправление настроек валюты
 */

require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\GeneralSetting;

echo "=== ИСПРАВЛЕНИЕ НАСТРОЕК ВАЛЮТЫ ===\n\n";

$gs = GeneralSetting::first();

if (!$gs) {
    echo "❌ Настройки не найдены!\n";
    exit(1);
}

echo "Текущие настройки:\n";
echo "  cur_text: " . ($gs->cur_text ?? 'не установлено') . "\n";
echo "  cur_sym: " . ($gs->cur_sym ?? 'не установлено') . "\n";
echo "  payment_cur_text: " . ($gs->payment_cur_text ?? 'не установлено') . "\n\n";

// Исправляем если установлено "credits" или "Credits"
if (strtolower($gs->cur_text) === 'credits' || strtolower($gs->cur_text) === 'credit') {
    echo "❌ Обнаружено 'credits' в cur_text, исправляем на EUR...\n";
    $gs->cur_text = 'EUR';
    $gs->save();
    echo "✅ Исправлено: cur_text = EUR\n\n";
}

if (strtolower($gs->payment_cur_text) === 'credits' || strtolower($gs->payment_cur_text) === 'credit') {
    echo "❌ Обнаружено 'credits' в payment_cur_text, исправляем на EUR...\n";
    $gs->payment_cur_text = 'EUR';
    $gs->save();
    echo "✅ Исправлено: payment_cur_text = EUR\n\n";
}

// Проверяем символ валюты
if (empty($gs->cur_sym) || strtolower($gs->cur_sym) === 'credits') {
    echo "❌ Проблема с cur_sym, устанавливаем €...\n";
    $gs->cur_sym = '€';
    $gs->save();
    echo "✅ Исправлено: cur_sym = €\n\n";
}

echo "Обновленные настройки:\n";
$gs->refresh();
echo "  cur_text: " . $gs->cur_text . "\n";
echo "  cur_sym: " . $gs->cur_sym . "\n";
echo "  payment_cur_text: " . $gs->payment_cur_text . "\n\n";

echo "✅ Готово!\n";







