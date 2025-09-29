<?php
require 'vendor/autoload.php';
$app = require 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// List of tables that already exist
$existingTables = [
    'countries' => '2025_08_24_205434_create_countries_table',
    'country_plan' => '2025_08_24_205434_create_country_plan_table',
    'currencies' => '2025_08_24_205434_create_currencies_table',
    'plans' => '2025_08_24_205434_create_plans_table',
    'regions' => '2025_08_24_205434_create_regions_table',
    'general_settings' => '2025_08_24_205434_create_general_settings_table',
    'pages' => '2025_08_24_205434_create_pages_table',
    'languages' => '2025_08_24_205434_create_languages_table',
    'frontends' => '2025_08_24_205434_create_frontends_table',
    'extensions' => '2025_08_24_205434_create_extensions_table',
    'forms' => '2025_08_24_205434_create_forms_table',
    'gateways' => '2025_08_24_205434_create_gateways_table',
    'gateway_currencies' => '2025_08_24_205434_create_gateway_currencies_table',
    'orders' => '2025_08_24_205434_create_orders_table',
    'order_items' => '2025_08_24_205434_create_order_items_table',
    'esims' => '2025_08_24_205434_create_esims_table',
    'deposits' => '2025_08_24_205434_create_deposits_table',
    'device_tokens' => '2025_08_24_205434_create_device_tokens_table',
    'notification_logs' => '2025_08_24_205434_create_notification_logs_table',
    'notification_templates' => '2025_08_24_205434_create_notification_templates_table',
    'support_attachments' => '2025_08_24_205434_create_support_attachments_table',
    'support_messages' => '2025_08_24_205434_create_support_messages_table',
    'support_tickets' => '2025_08_24_205434_create_support_tickets_table',
    'cron_jobs' => '2025_08_24_205434_create_cron_jobs_table',
    'cron_job_logs' => '2025_08_24_205434_create_cron_job_logs_table',
    'cron_schedules' => '2025_08_24_205434_create_cron_schedules_table',
    'update_logs' => '2025_08_24_205434_create_update_logs_table',
    'password_resets' => '2025_08_24_205434_create_password_resets_table',
    'personal_access_tokens' => '2025_08_24_205434_create_personal_access_tokens_table'
];

$batch = 8;
foreach ($existingTables as $table => $migration) {
    // Check if table exists
    if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
        // Check if migration is already recorded
        $exists = \Illuminate\Support\Facades\DB::table('migrations')
            ->where('migration', $migration)
            ->exists();
            
        if (!$exists) {
            \Illuminate\Support\Facades\DB::table('migrations')->insert([
                'migration' => $migration,
                'batch' => $batch
            ]);
            echo "Marked migration: $migration (table: $table)\n";
        } else {
            echo "Migration already exists: $migration\n";
        }
    } else {
        echo "Table $table does not exist, skipping migration: $migration\n";
    }
}

echo "Done! Now run: php artisan migrate --force\n";



