<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Frontend;

echo "Adding Delivery Policy to database...\n\n";

// Проверяем, существует ли уже запись
$existing = Frontend::where('slug', 'delivery-policy')
    ->where('data_keys', 'policy_pages.element')
    ->first();

if ($existing) {
    echo "✅ delivery-policy already exists\n";
    echo "Slug: {$existing->slug}\n";
    echo "Title: {$existing->data_values->title}\n";
} else {
    // Создаем новую запись
    $policy = new Frontend();
    $policy->tempname = 'basic';
    $policy->slug = 'delivery-policy';
    $policy->data_keys = 'policy_pages.element';
    
    // Создаем объект data_values
    $policy->data_values = (object) [
        'title' => 'Delivery Policy',
        'content' => '' // Пустой контент, так как мы используем статические файлы
    ];
    
    $policy->save();
    echo "✅ Created delivery-policy\n";
}

echo "\n=== Verification ===\n";
$policyCount = Frontend::where('data_keys', 'policy_pages.element')->count();
echo "Total policy records in database: $policyCount\n";

echo "\n✅ Delivery Policy page is now available at: /policy/delivery-policy\n";
echo "✅ View file: core/resources/views/templates/basic/policies/delivery.blade.php\n";
echo "\nDone!\n";



