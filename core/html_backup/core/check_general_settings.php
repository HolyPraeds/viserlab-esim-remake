<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Check general_settings table structure
$columns = \Illuminate\Support\Facades\Schema::getColumnListing('general_settings');
echo "General settings table columns: " . implode(', ', $columns) . "\n";

// Check what's in the table
$settings = \App\Models\GeneralSetting::all();
echo "Settings in table:\n";
foreach ($settings as $setting) {
    echo "- " . $setting->toJson() . "\n";
}



