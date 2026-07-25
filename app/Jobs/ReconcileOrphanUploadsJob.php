<?php

namespace App\Jobs;

use App\Logging\ResumeLogContext;
use App\Models\ResumeUpload;
use App\Models\UploadSessionAudit;
use App\Services\ResumeMetrics;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Phase 5 — weekly reconciler for orphan Uploaded-but-never-linked rows.
 *
 * The GC clock (CleanupOrphanUploadsJob) handles rows that have at least
 * *started* the parse step but exhausted their lifecycle without linkage.
 * This job handles the longer-tail case the GC clock can miss:
 *
 *   Uploaded (file on R2) → user dropped off without ever calling submit
 *   → row sat Uploaded for > orphan_grace_hours → bytes are orphaned.
 *
 * Distinct from GC because:
 *   - GC runs nightly with no lower bound on upload_completed_at.
 *   - Reconciler runs weekly with FULL state-machine guard (does NOT
 *     touch any row that has reached Parsed + expires_at, or any row
 *     that has any candidate/bench link). That separation keeps the
 *     audit trail clean — GC's volume is bounded by expires_at, ours
 *     is bounded by orphan_grace_hours.
 *
 * Idempotent: re-run yields 0 work. Each row's transition is conditional
 * (status = Uploaded AND upload_completed_at < now - grace). If the row
 * is already Reconciled, the WHERE filters it out before read.
 */
class ReconcileOrphanUploadsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 300;
    public int $maxDeletesPerRun = 200;

    public function handle(ResumeMetrics $metrics): void
    {
        // Phase 6 — bind pod/queue/worker to the shared log context
        // for the duration of this worker process. Done at handle()
        // entry so every event() call below carries them.
        ResumeLogContext::initJob(
            queue: $this->job?->getQueue(),
            worker: $this->job?->getName() . ':' . (string) ($this->attempts() ?? 0),
        );

        $startedAt = Carbon::now();
        $graceHours = (int) config('resumes.orphan_grace_hours', 24);
        $attemptMax = (int) config('resumes.delete_attempt_max', 3);
        $threshold = $startedAt->copy()->subHours($graceHours);

        $candidates = ResumeUpload::query()
            ->where('status', ResumeUpload::STATUS_UPLOADED)
            ->whereNotNull('upload_completed_at')
            ->where('upload_completed_at', '<', $threshold)
            ->whereNull('candidate_id')
            ->whereNull('bench_application_id')
            ->orderBy('id')
            ->limit($this->maxDeletesPerRun)
            ->get();

        $this->logStructured('reconciliation_started', [
            'candidates' => $candidates->count(),
            'orphan_grace_hours' => $graceHours,
            'threshold' => $threshold->toIso8601String(),
            'max_per_run' => $this->maxDeletesPerRun,
        ]);

        UploadSessionAudit::record(
            sessionUuid: '00000000-0000-0000-0000-000000000001',
            companyId: 0,
            action: 'ReconcileStart',
            actorId: null,
            metadata: [
                'candidates' => $candidates->count(),
                'orphan_grace_hours' => $graceHours,
                'threshold' => $threshold->toIso8601String(),
            ],
        );

        $success = 0;
        $missing = 0;
        $failed = 0;
        $skipped = 0;

        foreach ($candidates as $row) {
            try {
                $outcome = $this->processRow($row, $attemptMax, $metrics);
                match ($outcome) {
                    'success' => $success++,
                    'missing' => $missing++,
                    'failed' => $failed++,
                    'skipped' => $skipped++,
                    default => null,
                };
            } catch (Throwable $e) {
                $failed++;
                $this->logStructured('reconciliation_row_error', [
                    'session_uuid' => $row->uuid,
                    'r2_path' => $row->r2_path,
                    'exception' => $e->getMessage(),
                ]);
            }
        }

        $elapsedMs = (int) ($startedAt->diffInMilliseconds(Carbon::now()));
        $this->logStructured('reconciliation_completed', [
            'elapsed_ms' => $elapsedMs,
            'successful' => $success,
            'missing_r2' => $missing,
            'failed' => $failed,
            'skipped' => $skipped,
            'orphan_current' => $metrics->currentOrphanUploads(),
        ]);

        UploadSessionAudit::record(
            sessionUuid: '00000000-0000-0000-0000-000000000001',
            companyId: 0,
            action: 'ReconcileDone',
            actorId: null,
            metadata: [
                'successful' => $success,
                'missing_r2' => $missing,
                'failed' => $failed,
                'skipped' => $skipped,
                'elapsed_ms' => $elapsedMs,
            ],
        );
    }

    /**
     * @return 'success'|'missing'|'failed'|'skipped'
     */
    private function processRow(ResumeUpload $row, int $attemptMax, ResumeMetrics $metrics): string
    {
        // Conditional claim — same shape as the GC clock; only one worker
        // can transition Uploaded → Reconciled.
        $claimed = ResumeUpload::query()
            ->where('id', $row->id)
            ->where('status', ResumeUpload::STATUS_UPLOADED)
            ->whereNull('candidate_id')
            ->whereNull('bench_application_id')
            ->update([
                'status' => ResumeUpload::STATUS_CLEANING,
                'cleanup_attempt_count' => $row->cleanup_attempt_count + 1,
                'updated_at' => Carbon::now(),
            ]);

        if ($claimed === 0) {
            // The row is no longer Uploaded (probably Linked by a late
            // submit). This is the idempotency guarantee.
            $this->logStructured('object_skipped', [
                'session_uuid' => $row->uuid,
                'r2_path' => $row->r2_path,
                'reason' => 'state_changed',
            ]);
            return 'skipped';
        }

        // Best-effort R2 delete. If R2 is unreachable, escalate to
        // DeleteFailed when attempt count reaches the ceiling.
        $r2Succeeded = false;
        $r2Error = null;
        $alreadyMissing = false;
        try {
            $disk = Storage::disk('r2');
            if ($disk->exists($row->r2_path)) {
                $disk->delete($row->r2_path);
                $r2Succeeded = true;
            } else {
                $alreadyMissing = true;
                $r2Succeeded = true;
            }
        } catch (Throwable $e) {
            $r2Succeeded = false;
            $r2Error = $e->getMessage();
        }

        if ($r2Succeeded) {
            $this->logStructured($alreadyMissing ? 'object_missing' : 'object_deleted', [
                'session_uuid' => $row->uuid,
                'r2_path' => $row->r2_path,
                'already_missing' => $alreadyMissing,
            ]);

            DB::transaction(function () use ($row, $alreadyMissing) {
                ResumeUpload::query()
                    ->where('id', $row->id)
                    ->where('status', ResumeUpload::STATUS_CLEANING)
                    ->update([
                        'status' => ResumeUpload::STATUS_RECONCILED,
                        'cleanup_reason' => $alreadyMissing ? 'orphan_already_missing' : 'orphan_deleted',
                        'updated_at' => Carbon::now(),
                    ]);
                UploadSessionAudit::record(
                    sessionUuid: $row->uuid,
                    companyId: (int) $row->company_id,
                    action: 'Reconciled',
                    actorId: null,
                    metadata: [
                        'r2_path' => $row->r2_path,
                        'already_missing' => $alreadyMissing,
                        'reason' => $alreadyMissing ? 'orphan_already_missing' : 'orphan_deleted',
                    ],
                );
            });

            $metrics->incrementOrphanDeletions((int) $row->company_id, $alreadyMissing ? 'orphan_already_missing' : 'orphan_deleted');
            return $alreadyMissing ? 'missing' : 'success';
        }

        // R2 delete failed this attempt. Escalate if at the ceiling.
        $newCount = (int) $row->cleanup_attempt_count + 1;
        $newStatus = $newCount >= $attemptMax ? ResumeUpload::STATUS_DELETE_FAILED : ResumeUpload::STATUS_CLEANING;
        ResumeUpload::query()
            ->where('id', $row->id)
            ->update([
                'status' => $newStatus,
                'cleanup_reason' => 'r2_delete_failed',
                'cleanup_attempt_count' => $newCount,
                'updated_at' => Carbon::now(),
            ]);

        $this->logStructured('delete_failed', [
            'session_uuid' => $row->uuid,
            'r2_path' => $row->r2_path,
            'attempt' => $newCount,
            'r2_error' => $r2Error,
            'terminal' => $newStatus === ResumeUpload::STATUS_DELETE_FAILED,
        ]);

        UploadSessionAudit::record(
            sessionUuid: $row->uuid,
            companyId: (int) $row->company_id,
            action: $newStatus === ResumeUpload::STATUS_DELETE_FAILED ? 'DeleteFailed' : 'CleaningFailed',
            actorId: null,
            metadata: [
                'attempt' => $newCount,
                'r2_path' => $row->r2_path,
                'r2_error' => $r2Error,
                'reason' => 'reconcile_failed',
            ],
        );

        $metrics->incrementDeleteFailures((int) $row->company_id, 'reconcile_r2_failed');
        return 'failed';
    }

    private function logStructured(string $event, array $payload): void
    {
        // Phase 6 — route via ResumeLogContext so pod/queue/worker
        // are merged into the Monolog record automatically.
        ResumeLogContext::event($event, $payload);
    }
}
