<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

/**
 * Phase 6 — scheduler leader-election contract.
 *
 * All scheduled resume-jobs require `cache.default=redis` to behave
 * as expected under multi-pod deployment. `onOneServer()` resolves
 * the leader pod via a Redis-backed cache lock; without Redis, every
 * pod honors the schedule independently and the row-level conditional
 * UPDATE keeps state correct but counter metrics inflate and work is
 * duplicated.
 *
 * The DB state machine remains the primary correctness mechanism —
 * `onOneServer()` is an OPTIMIZATION, not a guard. TTL values exceed
 * the worst-case single-pod runtime but stay below the per-job repeat
 * interval so the lock auto-recovers if a leader pod dies mid-run.
 */
class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule)
    {

        $schedule->command('interview:send-reminder')
            ->everyMinute()
            ->timezone('Asia/Kolkata');
<<<<<<< Updated upstream
=======

        // Phase 1 — daily orphan cleanup. Phase 6: leader-election +
        // 5-minute lock TTL. Worst-case runtime for 200 deletes × R2
        // RTT is ~3 min; TTL > that but well within daily cadence.
        $schedule->command('uploads:cleanup-orphans --dispatch')
            ->dailyAt('03:30')
            ->timezone('Asia/Kolkata')
            ->withoutOverlapping(300)
            ->onOneServer();

        // Phase 5 — Weekly reconciler. Phase 6: leader-election +
        // 10-minute lock TTL. Sundays at 04:30 IST — 1h after the
        // 03:30 daily GC to avoid any row-level contention.
        $schedule->command('uploads:reconcile --dispatch')
            ->weekly()
            ->sundays()
            ->at('04:30')
            ->timezone('Asia/Kolkata')
            ->withoutOverlapping(600)
            ->onOneServer();

        // Phase 5 — Stuck-session repair. Phase 6: leader-election +
        // 14-minute lock TTL. Frequency is every 15 min so the lock
        // MUST release before the next cycle — a stuck leader won't
        // block indefinitely.
        $schedule->command('uploads:repair --dispatch')
            ->everyFifteenMinutes()
            ->timezone('Asia/Kolkata')
            ->withoutOverlapping(840)
            ->onOneServer();
>>>>>>> Stashed changes
    }

    protected function commands()
    {
        $this->load(__DIR__.'/Commands');
        require base_path('routes/console.php');
    }
}
