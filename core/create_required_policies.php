<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Frontend;

echo "Creating only required policies...\n\n";

// Создаем только нужные политики
$requiredPolicies = [
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
    ]
];

foreach ($requiredPolicies as $policyData) {
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

echo "\n=== Final Policy List ===\n";
$policyPages = Frontend::where('data_keys', 'policy_pages.element')->get();

if ($policyPages->count() > 0) {
    echo "Policies in registration form:\n";
    foreach ($policyPages as $policy) {
        echo "- {$policy->slug}: {$policy->data_values->title}\n";
    }
} else {
    echo "No policies found\n";
}

echo "\n✅ Registration form will now show only 3 policies:\n";
echo "- Privacy Policy\n";
echo "- Terms of Service\n"; 
echo "- Cookies Policy\n";

echo "\nDone!\n";

