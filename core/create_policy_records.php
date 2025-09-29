<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Frontend;

echo "Creating policy records in database...\n\n";

// Создаем записи для политик в базе данных
$policies = [
    [
        'slug' => 'privacy-policy',
        'title' => 'Privacy Policy',
        'tempname' => 'basic'
    ],
    [
        'slug' => 'terms-of-service', 
        'title' => 'Terms of Service',
        'tempname' => 'basic'
    ],
    [
        'slug' => 'cookies-policy',
        'title' => 'Cookies Policy', 
        'tempname' => 'basic'
    ],
    [
        'slug' => 'refund-policy',
        'title' => 'Refund Policy',
        'tempname' => 'basic'
    ],
    [
        'slug' => 'disclosure-disclaimer',
        'title' => 'Disclosure Disclaimer',
        'tempname' => 'basic'
    ]
];

foreach ($policies as $policyData) {
    // Проверяем, существует ли уже запись
    $existing = Frontend::where('slug', $policyData['slug'])
        ->where('data_keys', 'policy_pages.element')
        ->first();
    
    if ($existing) {
        echo "✅ {$policyData['slug']} already exists\n";
        continue;
    }
    
    // Создаем новую запись
    $policy = new Frontend();
    $policy->tempname = $policyData['tempname'];
    $policy->slug = $policyData['slug'];
    $policy->data_keys = 'policy_pages.element';
    
    // Создаем объект data_values
    $policy->data_values = (object) [
        'title' => $policyData['title'],
        'content' => '' // Пустой контент, так как мы используем статические файлы
    ];
    
    $policy->save();
    echo "✅ Created {$policyData['slug']}\n";
}

echo "\n=== Verification ===\n";
$policyCount = Frontend::where('data_keys', 'policy_pages.element')->count();
echo "Total policy records in database: $policyCount\n";

$policyPages = Frontend::where('data_keys', 'policy_pages.element')->get();
foreach ($policyPages as $policy) {
    echo "- {$policy->slug}: {$policy->data_values->title}\n";
}

echo "\n✅ Now registration form should show policy links!\n";
echo "✅ Policy pages will use static files as fallback\n";

echo "\nDone!\n";

