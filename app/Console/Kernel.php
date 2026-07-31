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

        // Resume v2 — orphan sweep (feature flag guards the actual work inside the command).
        // withoutOverlapping prevents pile-up if the previous run is still processing.
        $schedule->command('resumes:reconcile-orphans')
            ->hourly()
            ->withoutOverlapping(10)
            ->runInBackground();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
