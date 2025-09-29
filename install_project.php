<?php
/**
 * Project Installation Script
 * This script will set up the ViserLab eSIM project
 */

echo "🚀 ViserLab eSIM - Project Installation\n";
echo "=======================================\n\n";

// Check if we're in the right directory
if (!file_exists('core')) {
    echo "❌ Error: 'core' directory not found!\n";
    echo "   Make sure you're running this script from the project root.\n";
    exit(1);
}

echo "✅ Project structure found\n";

// Change to core directory
chdir('core');

echo "\n📦 Installing PHP dependencies...\n";
$composerOutput = shell_exec('composer install 2>&1');
echo $composerOutput;

echo "\n🔧 Creating .env file...\n";
if (!file_exists('.env')) {
    if (file_exists('env_template.txt')) {
        copy('env_template.txt', '.env');
        echo "✅ .env file created from template\n";
    } else {
        echo "⚠️  Warning: env_template.txt not found\n";
        echo "   You'll need to create .env manually\n";
    }
} else {
    echo "✅ .env file already exists\n";
}

echo "\n🔑 Generating application key...\n";
$keyOutput = shell_exec('php artisan key:generate 2>&1');
echo $keyOutput;

echo "\n🗄️  Running database migrations...\n";
$migrateOutput = shell_exec('php artisan migrate 2>&1');
echo $migrateOutput;

echo "\n🌱 Seeding database...\n";
$seedOutput = shell_exec('php artisan db:seed 2>&1');
echo $seedOutput;

echo "\n🌱 Seeding test data...\n";
$testSeedOutput = shell_exec('php artisan db:seed --class=TestDataSeeder 2>&1');
echo $testSeedOutput;

echo "\n🔗 Setting up storage permissions...\n";
$storageOutput = shell_exec('php artisan storage:link 2>&1');
echo $storageOutput;

echo "\n🧹 Clearing caches...\n";
$configOutput = shell_exec('php artisan config:clear 2>&1');
echo $configOutput;

$cacheOutput = shell_exec('php artisan cache:clear 2>&1');
echo $cacheOutput;

$viewOutput = shell_exec('php artisan view:clear 2>&1');
echo $viewOutput;

echo "\n=======================================\n";
echo "🎉 Installation completed!\n";
echo "=======================================\n\n";

echo "📋 Next steps:\n";
echo "1. Configure your database in core/.env\n";
echo "2. Start your web server (XAMPP)\n";
echo "3. Get API key from https://esims.gitbook.io/dataplans/\n";
echo "4. Configure API in admin panel\n";
echo "5. Run: php artisan cron:run --alias=sync_data_plan\n";
echo "6. Visit http://localhost\n\n";

echo "🧪 Test the installation:\n";
echo "- Run: php test_dataplans_api.php\n";
echo "- Run: php test_api.php\n";
echo "- Visit: http://localhost/api/countries\n\n";
