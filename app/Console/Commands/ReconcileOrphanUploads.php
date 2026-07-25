<?php

namespace App\Console\Commands;

use App\Jobs\ReconcileOrphanUploadsJob;
use Illuminate\Console\Command;

/**
 * Companion command to {@see ReconcileOrphanUploadsJob}.
 *
 * Daily runs are wired to the scheduler in Console/Kernel.php as
 *   $schedule->command('uploads:reconcile --dispatch')->weekly()->...;
 *
 * Operational knobs:
 *   --dry-run      : show what would be culled without performing any
 *                    R2 delete or row update. Useful for staging smoke tests.
 *   --dispatch     : push onto the queue for parallel execution across pods.
 *   --tenant=ID    : limit scan to a single (int) company_id. Prints a per-row
 *                    table when used with --dry-run.
 *   --max=N        : cap rows processed per run (default config-resumes.reconcile_batch_size).
 *
 * Exit code: 0 on success regardless of how many rows were processed. The
 * Metrics counters carry the "how many left" signal — operators should
 * alert on those, not on this command's exit code.
 */
class ReconcileOrphanUploads extends Command
{
    protected $signature = 'uploads:reconcile
                            {--dry-run : Show candidates only; do not modify rows or R2}
                            {--dispatch : Dispatch onto the queue instead of running synchronously}
                            {--tenant= : Limit to a single company_id (int)}
                            {--max= : Max rows processed per run (default config(reconcile_batch_size))}';

    protected $description = 'Phase 5: weekly scanner for Uploaded-but-never-linked rows; culls orphans and writes audit + metrics';

    public function handle(): int
    {
        $max = (int) ($this->option('max') ?? config('resumes.reconcile_batch_size', 200));

        if ($this->option('dispatch')) {
            $job = new ReconcileOrphanUploadsJob();
            $job->maxDeletesPerRun = $max;
            dispatch($job);
            $this->info("ReconcileOrphanUploadsJob dispatched (max={$max}).");
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            return $this->dryRun($max);
        }

        $job = new ReconcileOrphanUploadsJob();
        $job->maxDeletesPerRun = $max;
        $job->handle(app(\App\Services\ResumeMetrics::class));
        $this->info("ReconcileOrphanUploads synchronous run completed (max={$max}).");
        return self::SUCCESS;
    }

    private function dryRun(int $max): int
    {
        $threshold = \Carbon\Carbon::now()->copy()->subHours((int) config('resumes.orphan_grace_hours', 24));
        $q = \App\Models\ResumeUpload::query()
            ->where('status', \App\Models\ResumeUpload::STATUS_UPLOADED)
            ->whereNotNull('upload_completed_at')
            ->where('upload_completed_at', '<', $threshold)
            ->whereNull('candidate_id')
            ->whereNull('bench_application_id');
        if ($this->option('tenant')) {
            $q->where('company_id', (int) $this->option('tenant'));
        }
        $rows = $q->orderBy('id')->limit($max)->get([
            'id', 'uuid', 'company_id', 'user_id', 'r2_path', 'upload_completed_at', 'cleanup_attempt_count',
        ]);
        $this->table(
            ['id', 'uuid', 'company_id', 'user_id', 'r2_path', 'upload_completed_at', 'cleanup_attempt_count'],
            $rows->map(fn ($r) => [
                $r->id,
                substr((string) $r->uuid, 0, 8) . '...',
                $r->company_id,
                $r->user_id,
                $r->r2_path,
                optional($r->upload_completed_at)->toIso8601String(),
                $r->cleanup_attempt_count,
            ])->all(),
        );
        $this->info("Dry-run: {$rows->count()} candidate row(s) past grace threshold {$threshold->toIso8601String()}.");
        return self::SUCCESS;
    }
}
