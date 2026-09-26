<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {
        // After any manual/cron sync, keep zero / near-zero customer prices off the storefront.
        $schedule->command('plans:deactivate-zero-price')->daily();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
    }
}
