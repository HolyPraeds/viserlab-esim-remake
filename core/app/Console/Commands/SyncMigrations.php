<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SyncMigrations extends Command
{
    protected $signature = 'migrations:sync {--batch=1}';
    protected $description = 'Sync migration table with existing migration files without running them';

    public function handle()
    {
        $batch = (int) $this->option('batch');
        $migrationFiles = File::files(database_path('migrations'));
        $inserted = 0;

        foreach ($migrationFiles as $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            $exists = DB::table('migrations')->where('migration', $name)->exists();

            if (!$exists) {
                DB::table('migrations')->insert([
                    'migration' => $name,
                    'batch' => $batch,
                ]);
                $inserted++;
            }
        }

        $this->info("Synced {$inserted} migrations with batch {$batch}.");
        return 0;
    }
}
