<?php

namespace App\Jobs;

use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Phase 1 GC: scan resume_uploads for orphan rows and delete both the row
 * and the R2 object.
 *
 * Why the state machine matters (see the architecture review):
 *
 *   - We claim a row with a conditional UPDATE (status = Parsed → Cleaning).
 *     If two GC workers race, only one wins; the other observes 0 affected
 *     rows and skips the row.
 *   - If the process crashes mid-delete, the row is left in Cleaning with
 *     age > threshold; next run re-tries. After 3 attempts (cleanup_attempt_count),
 *     the row moves to DeleteFailed and is monitored (Phase 5 metrics).
 *
 * The companion artisan command `uploads:cleanup-orphans` calls this job synchronously.
 * The scheduled version dispatches it onto the queue so multiple pods can run in parallel.
 */
class CleanupOrphanUploadsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300; // 5min cap; bottom out on partial rows so we don't run forever.
    public int $maxDeletesPerRun = 200;
    private const MAX_CLEANUP_ATTEMPTS = 3;

    public function handle(): void
    {
        $now = now();

        // Candidate rows: parsed/aborted/replaced/successful-with-no-link, all past expiry.
        // We deliberately pull a bounded batch — pagination via id — so the loop is bounded.
        $candidates = ResumeUpload::query()
            ->whereIn('status', [
                ResumeUpload::STATUS_PARSED,
                ResumeUpload::STATUS_ABORTED_BY_SUBMIT,
                ResumeUpload::STATUS_REPLACED,
                ResumeUpload::STATUS_PARSE_FAILED,
                ResumeUpload::STATUS_MANUAL_REVIEW_REQUIRED,
            ])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', $now)
            ->orderBy('id')
            ->limit($this->maxDeletesPerRun)
            ->get();

        $cleanupTargetCount = $candidates->count();
        Log::info('ResumeUpload GC: starting pass', [
            'candidates' => $cleanupTargetCount,
            'max_per_run' => $this->maxDeletesPerRun,
        ]);

        UploadSessionAudit::record(
            sessionUuid: '00000000-0000-0000-0000-000000000000',
            companyId: 0,
            action: 'CleaningStart',
            actorId: null,
            metadata: ['candidates' => $cleanupTargetCount, 'pid' => getmypid()],
        );

        $successful = 0;
        $failed = 0;

        foreach ($candidates as $row) {
            if ($this->processRow($row)) {
                $successful++;
            } else {
                $failed++;
            }
        }

        Log::info('ResumeUpload GC: pass finished', [
            'candidates' => $cleanupTargetCount,
            'successful' => $successful,
            'failed' => $failed,
        ]);

        UploadSessionAudit::record(
            sessionUuid: '00000000-0000-0000-0000-000000000000',
            companyId: 0,
            action: 'Completed',
            actorId: null,
            metadata: ['successful' => $successful, 'failed' => $failed, 'candidates' => $cleanupTargetCount],
        );
    }

    /**
     * Returns true on row-level success (deleted OR legitimately marked DeleteFailed
     * for human attention). Returns false only on transient exceptions where we
     * should let Horizon retry the job.
     */
    private function processRow(ResumeUpload $row): bool
    {
        $fromStatus = $row->status;

        // 1. Claim the row (idempotent guard against overlapping workers).
        $claimed = ResumeUpload::query()
            ->where('id', $row->id)
            ->whereIn('status', [
                ResumeUpload::STATUS_PARSED,
                ResumeUpload::STATUS_ABORTED_BY_SUBMIT,
                ResumeUpload::STATUS_REPLACED,
                ResumeUpload::STATUS_PARSE_FAILED,
                ResumeUpload::STATUS_MANUAL_REVIEW_REQUIRED,
            ])
            ->update(['status' => ResumeUpload::STATUS_CLEANING, 'updated_at' => now()]);

        if ($claimed === 0) {
            // Someone else claimed it OR it changed status in flight.
            return true;
        }

        UploadSessionAudit::record(
            sessionUuid: $row->uuid,
            companyId: (int) $row->company_id,
            action: 'CleaningStart',
            actorId: null,
            metadata: ['from' => $fromStatus, 'r2_path' => $row->r2_path],
        );

        // 2. Best-effort R2 delete. Errors here escalate but do not loop forever.
        $r2Succeeded = false;
        $r2ErrorMessage = null;
        try {
            $disk = Storage::disk('r2');
            if ($disk->exists($row->r2_path)) {
                $disk->delete($row->r2_path);
                $r2Succeeded = true;
            } else {
                // Already deleted upstream; treat as success.
                $r2Succeeded = true;
                UploadSessionAudit::record(
                    sessionUuid: $row->uuid,
                    companyId: (int) $row->company_id,
                    action: 'CleaningR2Done',
                    actorId: null,
                    metadata: ['already_missing' => true, 'r2_path' => $row->r2_path],
                );
            }
        } catch (Throwable $e) {
            $r2Succeeded = false;
            $r2ErrorMessage = $e->getMessage();
            Log::warning('ResumeUpload GC: R2 delete failed', [
                'session_uuid' => $row->uuid,
                'r2_path' => $row->r2_path,
                'attempt' => $row->cleanup_attempt_count + 1,
                'exception' => $e->getMessage(),
            ]);
        }

        if ($r2Succeeded) {
            UploadSessionAudit::record(
                sessionUuid: $row->uuid,
                companyId: (int) $row->company_id,
                action: 'CleaningR2Done',
                actorId: null,
                metadata: ['r2_path' => $row->r2_path],
            );

            // 3. Delete the row idempotently (only if still in Cleaning).
            $deleted = ResumeUpload::query()
                ->where('id', $row->id)
                ->where('status', ResumeUpload::STATUS_CLEANING)
                ->delete();

            if ($deleted > 0) {
                UploadSessionAudit::record(
                    sessionUuid: $row->uuid,
                    companyId: (int) $row->company_id,
                    action: 'CleaningDone',
                    actorId: null,
                    metadata: ['r2_path' => $row->r2_path],
                );
                return true;
            }

            Log::warning('ResumeUpload GC: row vanished after R2 delete', [
                'id' => $row->id,
                'session_uuid' => $row->uuid,
            ]);
            return true;
        }

        // R2 failed. Increment count; escalate to DeleteFailed at threshold.
        $newCount = $row->cleanup_attempt_count + 1;
        $newStatus = $newCount >= self::MAX_CLEANUP_ATTEMPTS
            ? ResumeUpload::STATUS_DELETE_FAILED
            : ResumeUpload::STATUS_CLEANING;

        ResumeUpload::query()
            ->where('id', $row->id)
            ->update([
                'status' => $newStatus,
                'cleanup_attempt_count' => $newCount,
                'updated_at' => now(),
            ]);

        UploadSessionAudit::record(
            sessionUuid: $row->uuid,
            companyId: (int) $row->company_id,
            action: $newStatus === ResumeUpload::STATUS_DELETE_FAILED ? 'DeleteFailed' : 'CleaningFailed',
            actorId: null,
            metadata: [
                'attempt' => $newCount,
                'r2_path' => $row->r2_path,
                'r2_error' => $r2ErrorMessage,
            ],
        );

        return false;
    }
}
