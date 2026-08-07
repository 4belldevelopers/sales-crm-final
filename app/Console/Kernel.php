<?php

namespace App\Console;

use App\Jobs\AutoCloseExpiredJobsJob;
use App\Jobs\ProcessOfferTimeoutJob;
use App\Jobs\ProcessOperationalCoinDeductionsJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Artisan;

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

        // Step 9: keep pipeline_counters (and Redis cache) in sync with MySQL every 5 min.
        $schedule->command('counters:reconcile')
            ->everyFiveMinutes()
            ->withoutOverlapping(4)
            ->runInBackground();

        // V2 Dashboard Phase 1 — background maintenance decoupled from HTTP GET.
        // Coin deductions (team + job) run every 5 min; withoutOverlapping prevents double-deduction.
        $schedule->job(new ProcessOperationalCoinDeductionsJob)
            ->everyFiveMinutes()
            ->withoutOverlapping(4)
            ->runInBackground();

        // Job expiry + free-package renewal: hourly is sufficient (same cadence as the old per-request calls).
        $schedule->job(new AutoCloseExpiredJobsJob)
            ->hourly()
            ->withoutOverlapping(50)
            ->runInBackground();

        // Offer 72-hour timeout: every 5 min, single bulk UPDATE (replaces per-request N+1 loop).
        $schedule->job(new ProcessOfferTimeoutJob)
            ->everyFiveMinutes()
            ->withoutOverlapping(4)
            ->runInBackground();

        // V3 Three-Drawer Step 9: detect and self-heal cache drift every 10 minutes.
        // Clears stale v3:status_job:{id} entries and re-syncs job_counters.
        $schedule->command('drawers:verify-drift --heal --hours=1')
            ->everyTenMinutes()
            ->withoutOverlapping(9)
            ->runInBackground();

        // Moved from Controller::__construct() — was spawning an artisan subprocess on every HTTP request.
        // Nightly is sufficient; real deployments should run `php artisan migrate --force` explicitly.
        $schedule->call(function () {
            if (Artisan::call('migrate:check')) {
                Artisan::call('migrate', ['--force' => true]);
                Artisan::call('config:clear');
                Artisan::call('route:clear');
                Artisan::call('view:clear');
                Artisan::call('cache:clear');
            }
        })->daily()->withoutOverlapping()->runInBackground();
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
