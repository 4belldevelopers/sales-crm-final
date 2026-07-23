<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('interview:send-reminder')
            ->everyMinute()
            ->timezone('Asia/Kolkata');

        // Phase 1 — Resume Upload Session orphan cleanup.
        // Runs at 03:30 IST to avoid overlap with the bulk of recruiter traffic.
        // Dispatches CleanupOrphanUploadsJob onto the queue so multiple pods can
        // share the work without locking; the job itself is idempotent at the row
        // level via the conditional UPDATE on the Cleaning transition.
        $schedule->command('uploads:cleanup-orphans --dispatch')
            ->dailyAt('03:30')
            ->timezone('Asia/Kolkata')
            ->withoutOverlapping();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
