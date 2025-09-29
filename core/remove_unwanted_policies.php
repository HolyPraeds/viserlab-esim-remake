<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Frontend;

echo "Removing unwanted policies...\n\n";

// Удаляем ненужные политики
$policiesToRemove = ['refund-policy', 'disclosure-disclaimer'];

foreach ($policiesToRemove as $slug) {
    $policy = Frontend::where('slug', $slug)
        ->where('data_keys', 'policy_pages.element')
        ->first();
    
    if ($policy) {
        $policy->delete();
        echo "✅ Removed $slug\n";
    } else {
        echo "❌ $slug not found\n";
    }
}

echo "\n=== Current Policy List ===\n";
$remainingPolicies = Frontend::where('data_keys', 'policy_pages.element')->get();

if ($remainingPolicies->count() > 0) {
    echo "Remaining policies in registration form:\n";
    foreach ($remainingPolicies as $policy) {
        echo "- {$policy->slug}: {$policy->data_values->title}\n";
    }
} else {
    echo "No policies found in database\n";
}

echo "\n✅ Registration form will now show only:\n";
echo "- Privacy Policy\n";
echo "- Terms of Service\n"; 
echo "- Cookies Policy\n";

echo "\nDone!\n";

