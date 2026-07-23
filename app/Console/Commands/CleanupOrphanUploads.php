<?php

namespace App\Console\Commands;

use App\Jobs\CleanupOrphanUploadsJob;
use Illuminate\Console\Command;

/**
 * Companion command to {@see CleanupOrphanUploadsJob}.
 *   - Synchronously runs the GC pass for ad-hoc operator use (`php artisan uploads:cleanup-orphans`).
 *   - Asynchronous dispatch is used by the scheduler; run this command with --dispatch to enqueue instead.
 *
 * Default schedule (Kernel.php):
 *   $schedule->command('uploads:cleanup-orphans')->dailyAt('03:30')->timezone('Asia/Kolkata');
 *
 * Operational guidance:
 *   - Run with --max=N to size the queue digest for noisy days.
 *   - Tail observer: the CleaningDone vs CleaningFailed ratio is the main SLO.
 */
class CleanupOrphanUploads extends Command
{
    protected $signature = 'uploads:cleanup-orphans
                            {--dispatch : Dispatch onto the queue instead of running synchronously}
                            {--max=200 : Max rows processed per run when dispatched}';

    protected $description = 'Phase 1 GC: delete orphan resume_uploads rows and their R2 files (state-machine driven, idempotent)';

    public function handle(): int
    {
        if ($this->option('dispatch')) {
            $job = new CleanupOrphanUploadsJob();
            $job->maxDeletesPerRun = (int) $this->option('max');
            dispatch($job);
            $this->info('CleanupOrphanUploadsJob dispatched to the queue (max=' . $job->maxDeletesPerRun . ').');
            return self::SUCCESS;
        }

        $job = new CleanupOrphanUploadsJob();
        $job->maxDeletesPerRun = (int) $this->option('max');
        $job->handle();
        return self::SUCCESS;
    }
}
