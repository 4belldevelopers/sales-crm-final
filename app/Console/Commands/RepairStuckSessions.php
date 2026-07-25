<?php

namespace App\Console\Commands;

use App\Jobs\RepairStuckSessionsJob;
use Illuminate\Console\Command;

/**
 * Companion command to {@see RepairStuckSessionsJob}.
 *
 * Operational usage:
 *   - `php artisan uploads:repair`                           — synchronous run.
 *   - `php artisan uploads:repair --dispatch`                 — for parallel pods.
 *   - `php artisan uploads:repair --dry-run --tenant=12`      — staging smoke test.
 *
 * Default schedule (Kernel.php):
 *   $schedule->command('uploads:repair')->everyFifteenMinutes()
 *       ->withoutOverlapping()->timezone('Asia/Kolkata');
 *
 * Why every 15 minutes instead of nightly: stuck uploads hurt UX
 * noticeably (banner stale, future parses pile up). 15-min resolution
 * keeps the user's "I uploaded but nothing happened" window small.
 */
class RepairStuckSessions extends Command
{
    protected $signature = 'uploads:repair
                            {--dry-run : Show counts of stuck sessions; no modifications}
                            {--dispatch : Dispatch onto the queue instead of running synchronously}
                            {--tenant= : Limit to a single company_id (int)}';

    protected $description = 'Phase 5: recover sessions stuck in Uploading/Parsing; idempotent and bounded by parse_attempt_count';

    public function handle(): int
    {
        if ($this->option('dispatch')) {
            dispatch(new RepairStuckSessionsJob());
            $this->info('RepairStuckSessionsJob dispatched to the queue.');
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            return $this->dryRun();
        }

        $job = new RepairStuckSessionsJob();
        $job->handle(app(\App\Services\ResumeMetrics::class));
        $this->info('RepairStuckSessions synchronous run completed.');
        return self::SUCCESS;
    }

    private function dryRun(): int
    {
        $uploadMin = (int) config('resumes.uploading_timeout_minutes', 30);
        $parseMin = (int) config('resumes.parsing_timeout_minutes', 60);
        $uploadThreshold = \Carbon\Carbon::now()->copy()->subMinutes($uploadMin);
        $parseThreshold = \Carbon\Carbon::now()->copy()->subMinutes($parseMin);

        $stuckUploading = \App\Models\ResumeUpload::query()
            ->where('status', \App\Models\ResumeUpload::STATUS_UPLOADING)
            ->where('upload_started_at', '<', $uploadThreshold);
        $stuckParsing = \App\Models\ResumeUpload::query()
            ->where('status', \App\Models\ResumeUpload::STATUS_PARSING)
            ->where('parse_started_at', '<', $parseThreshold);

        if ($this->option('tenant')) {
            $cid = (int) $this->option('tenant');
            $stuckUploading->where('company_id', $cid);
            $stuckParsing->where('company_id', $cid);
        }

        $u = $stuckUploading->count();
        $p = $stuckParsing->count();
        $this->info(sprintf(
            'Dry-run: %d stuck Uploading (>= %dm); %d stuck Parsing (>= %dm)',
            $u, $uploadMin, $p, $parseMin,
        ));
        return self::SUCCESS;
    }
}
