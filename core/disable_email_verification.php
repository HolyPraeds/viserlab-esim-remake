<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Disable email verification
\App\Models\GeneralSetting::where('key', 'ev')->update(['value' => '0']);

// Also verify the latest user's email manually
$user = \App\Models\User::latest()->first();
if ($user) {
    $user->ev = 1; // Set to verified
    $user->save();
    echo "User {$user->email} email verified manually\n";
}

echo "Email verification disabled!\n";
echo "New email verification setting: " . gs('ev') . "\n";



