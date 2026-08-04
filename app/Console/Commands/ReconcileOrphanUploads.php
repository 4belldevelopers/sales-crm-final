<?php

namespace App\Console\Commands;

// Intent: Cron sweep that transitions stale resume upload sessions to terminal
// states and removes orphaned R2 objects, keeping the resume_uploads table clean.
//
// Three passes (all chunked to bound memory use at 100M+ row scale):
//   Pass 1 — Pending | Uploading past expires_at  → Expired  (no R2 object)
//   Pass 2 — Uploaded past expires_at             → Expired  (R2 object deleted, fail-open)
//   Pass 3 — Expired older than N days            → Reconciled (final terminal state)
//
// Guarded by feature flag: resumes.features.reconcile_orphans (default OFF).
// Run: php artisan resumes:reconcile-orphans [--dry-run] [--chunk=100]

use App\Enums\ResumeUploadStatus;
use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ReconcileOrphanUploads extends Command
{
    protected $signature = 'resumes:reconcile-orphans
                            {--dry-run : Preview what would change without modifying any rows}
                            {--chunk=  : Override the DB chunk size (default: resumes.reconcile.chunk_size)}';

    protected $description = 'Expire stale upload sessions and delete orphaned R2 objects.';

    public function handle(): int
    {
        if (!config('resumes.features.reconcile_orphans')) {
            $this->line('Feature flag resumes.features.reconcile_orphans is OFF — skipping.');
            return 0;
        }

        $dryRun    = $this->option('dry-run');
        $chunkSize = (int) ($this->option('chunk') ?: config('resumes.reconcile.chunk_size', 100));

        if ($dryRun) {
            $this->warn('[DRY RUN] No rows will be modified.');
        }

        $expired    = $this->expireStale($dryRun, $chunkSize);
        $r2Cleaned  = $this->expireUploaded($dryRun, $chunkSize);
        $reconciled = $this->reconcileExpired($dryRun, $chunkSize);

        $this->info(sprintf(
            'Done — stale_expired:%d r2_cleaned:%d reconciled:%d%s',
            $expired, $r2Cleaned, $reconciled, $dryRun ? ' (dry-run)' : ''
        ));

        Log::info('jobs.resume.reconcile_complete', [
            'stale_expired' => $expired,
            'r2_cleaned'    => $r2Cleaned,
            'reconciled'    => $reconciled,
            'dry_run'       => $dryRun,
            'chunk_size'    => $chunkSize,
        ]);

        return 0;
    }

    // ── Pass 1: Pending / Uploading past TTL → Expired ───────────────────────
    // No R2 object exists yet for these states, so bulk UPDATE is safe.

    private function expireStale(bool $dryRun, int $chunkSize): int
    {
        $staleStatuses = [ResumeUploadStatus::Pending->value, ResumeUploadStatus::Uploading->value];
        $count = 0;

        ResumeUpload::whereIn('status', $staleStatuses)
            ->where('expires_at', '<', now())
            ->chunkById($chunkSize, function ($sessions) use ($dryRun, $staleStatuses, &$count) {
                $ids = $sessions->pluck('id')->all();

                if (!$dryRun) {
                    // Conditional UPDATE guards against concurrent state changes.
                    $updated = ResumeUpload::whereIn('id', $ids)
                        ->whereIn('status', $staleStatuses)
                        ->update(['status' => ResumeUploadStatus::Expired->value, 'updated_at' => now()]);

                    if ($updated > 0) {
                        $now = now()->toDateTimeString();
                        $pod = gethostname();
                        $auditRows = $sessions
                            ->filter(fn($s) => in_array($s->status, $staleStatuses, true))
                            ->map(fn($s) => [
                                'resume_upload_id' => $s->id,
                                'company_id'       => $s->company_id,
                                'action'           => 'CronExpiredStale',
                                'payload'          => json_encode(['old_status' => $s->status]),
                                'pod_id'           => $pod,
                                'created_at'       => $now,
                                'updated_at'       => $now,
                            ])
                            ->values()
                            ->all();

                        if (!empty($auditRows)) {
                            DB::table('upload_session_audits')->insert($auditRows);
                        }
                    }
                    $count += $updated;
                } else {
                    $count += count($ids);
                }
            });

        return $count;
    }

    // ── Pass 2: Uploaded past TTL → delete R2 object (fail-open) → Expired ──
    // Must process row-by-row because R2 deletes can fail independently per file.

    private function expireUploaded(bool $dryRun, int $chunkSize): int
    {
        $count = 0;

        ResumeUpload::where('status', ResumeUploadStatus::Uploaded->value)
            ->where('expires_at', '<', now())
            ->chunkById($chunkSize, function ($sessions) use ($dryRun, &$count) {
                foreach ($sessions as $session) {
                    if ($dryRun) {
                        $count++;
                        continue;
                    }

                    // Conditional UPDATE first — if another process already moved this
                    // row (e.g., concurrent submit finalized it), rows=0 means skip.
                    $claimed = ResumeUpload::where('id', $session->id)
                        ->where('status', ResumeUploadStatus::Uploaded->value)
                        ->update(['status' => ResumeUploadStatus::Expired->value, 'updated_at' => now()]);

                    if ($claimed === 0) {
                        continue;   // concurrent submit won — don't delete the file
                    }

                    // R2 delete is fail-open: a failed delete leaves an orphaned object
                    // in R2 (acceptable cost) but never blocks the reconciliation sweep.
                    $r2Deleted = false;
                    try {
                        $r2Path = 'resumes/' . $session->uuid;
                        if (Storage::disk('r2')->exists($r2Path)) {
                            Storage::disk('r2')->delete($r2Path);
                            $r2Deleted = true;
                        }
                    } catch (\Throwable $e) {
                        Log::warning('jobs.resume.reconcile_r2_delete_failed', [
                            'uuid'  => $session->uuid,
                            'error' => $e->getMessage(),
                        ]);
                    }

                    UploadSessionAudit::record($session->id, 'CronExpiredUploaded', [
                        'r2_deleted' => $r2Deleted,
                    ], $session->company_id);

                    $count++;
                }
            });

        return $count;
    }

    // ── Pass 3: Expired older than N days → Reconciled (final cleanup) ───────
    // Bulk UPDATE — no R2 work, no individual audit records needed.

    private function reconcileExpired(bool $dryRun, int $chunkSize): int
    {
        $cutoff = now()->subDays(config('resumes.reconcile.expire_after_days', 7));
        $count  = 0;

        if ($dryRun) {
            return (int) ResumeUpload::where('status', ResumeUploadStatus::Expired->value)
                ->where('updated_at', '<', $cutoff)
                ->count();
        }

        ResumeUpload::where('status', ResumeUploadStatus::Expired->value)
            ->where('updated_at', '<', $cutoff)
            ->chunkById($chunkSize, function ($sessions) use (&$count) {
                $ids = $sessions->pluck('id')->all();
                $updated = ResumeUpload::whereIn('id', $ids)
                    ->where('status', ResumeUploadStatus::Expired->value)
                    ->update(['status' => ResumeUploadStatus::Reconciled->value, 'updated_at' => now()]);
                $count += $updated;
            });

        return $count;
    }
}
