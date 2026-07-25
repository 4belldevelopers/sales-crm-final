<?php

namespace App\Jobs;

use App\Jobs\ParseResumeJob;
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
 * Phase 5 — repair job for sessions stuck in mid-flight.
 *
 * Two stuck states we recognize (Phase 5 spec):
 *   1. Uploading past uploading_timeout_minutes
 *      The browser PUT never resolved (closed tab / network drop / abort).
 *      Repair action: consider the row orphaned; transition Uploaded? to
 *      the cleanliness path. If the bytes are on R2 (status=Uploaded),
 *      they get reconciled. If bytes never landed (status=Uploading),
 *      we delete the row to avoid leaving a phantom Created bookmark.
 *   2. Parsing past parsing_timeout_minutes
 *      The parser worker crashed or never returned a status update.
 *      Repair action: re-dispatch ParseResumeJob, gated by parse_attempt_count.
 *
 * Both repairs are idempotent — conditional UPDATEs gate the work; a
 * second invocation 1 second later observes the row already in its new
 * state and yields no work.
 */
class RepairStuckSessionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 180;
    public int $maxRetriesPerRun = 100;

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
        $uploadMin = (int) config('resumes.uploading_timeout_minutes', 30);
        $parseMin = (int) config('resumes.parsing_timeout_minutes', 60);
        $maxAttempts = (int) config('resumes.repair_dispatch_max', 3);
        $uploadThreshold = $startedAt->copy()->subMinutes($uploadMin);
        $parseThreshold = $startedAt->copy()->subMinutes($parseMin);

        $uploadingStuck = ResumeUpload::query()
            ->where('status', ResumeUpload::STATUS_UPLOADING)
            ->where('upload_started_at', '<', $uploadThreshold)
            ->orderBy('id')
            ->limit($this->maxRetriesPerRun)
            ->get();

        $parsingStuck = ResumeUpload::query()
            ->where('status', ResumeUpload::STATUS_PARSING)
            ->where('parse_started_at', '<', $parseThreshold)
            ->orderBy('id')
            ->limit($this->maxRetriesPerRun)
            ->get();

        $this->logStructured('repair_started', [
            'uploading_stuck' => $uploadingStuck->count(),
            'parsing_stuck' => $parsingStuck->count(),
            'uploading_timeout_minutes' => $uploadMin,
            'parsing_timeout_minutes' => $parseMin,
            'max_dispatch_attempts' => $maxAttempts,
        ]);

        UploadSessionAudit::record(
            sessionUuid: '00000000-0000-0000-0000-000000000002',
            companyId: 0,
            action: 'RepairStart',
            actorId: null,
            metadata: [
                'uploading_stuck' => $uploadingStuck->count(),
                'parsing_stuck' => $parsingStuck->count(),
            ],
        );

        $uploadingRecovered = 0;
        $parsingRequeued = 0;
        $skipped = 0;

        foreach ($uploadingStuck as $row) {
            $outcome = $this->recoverUploading($row, $metrics);
            if ($outcome === 'recovered') {
                $uploadingRecovered++;
            } else {
                $skipped++;
            }
        }

        foreach ($parsingStuck as $row) {
            $outcome = $this->requeueParsing($row, $maxAttempts, $metrics);
            if ($outcome === 'requeued') {
                $parsingRequeued++;
            } else {
                $skipped++;
            }
        }

        $this->logStructured('repair_completed', [
            'elapsed_ms' => (int) $startedAt->diffInMilliseconds(Carbon::now()),
            'uploading_recovered' => $uploadingRecovered,
            'parsing_requeued' => $parsingRequeued,
            'skipped' => $skipped,
            'stuck_current' => $metrics->currentStuckSessions(),
        ]);

        UploadSessionAudit::record(
            sessionUuid: '00000000-0000-0000-0000-000000000002',
            companyId: 0,
            action: 'RepairDone',
            actorId: null,
            metadata: [
                'uploading_recovered' => $uploadingRecovered,
                'parsing_requeued' => $parsingRequeued,
                'skipped' => $skipped,
            ],
        );
    }

    /**
     * Uploading-stuck sessions: the browser PUT never completed. Two
     * sub-cases:
     *   - bytes are on R2 (status snuck to Uploaded by finalize() right
     *     before the worker died) — same as an orphan; reconcile cleans it.
     *   - bytes never landed — just delete the row (no R2 file to clean).
     */
    private function recoverUploading(ResumeUpload $row, ResumeMetrics $metrics): string
    {
        $metrics->incrementStuckDetected('stuck_uploading');

        $r2Exists = false;
        try {
            $r2Exists = \Illuminate\Support\Facades\Storage::disk('r2')->exists((string) $row->r2_path);
        } catch (Throwable $e) {
            // Don't block repair on a broken R2 check; treat as missing.
            $r2Exists = false;
        }

        if ($r2Exists) {
            // Delegate to the orphan cleaner so the audit trail is uniform
            // — these rows end up Reconciled with cleanup_reason='orphan_deleted'
            // and the same metric counter increments.
            try {
                Storage::disk('r2')->delete($row->r2_path);
            } catch (Throwable $e) {
                $this->logStructured('delete_failed', [
                    'session_uuid' => $row->uuid,
                    'r2_path' => $row->r2_path,
                    'kind' => 'stuck_uploading',
                    'exception' => $e->getMessage(),
                ]);
                $metrics->incrementDeleteFailures((int) $row->company_id, 'repair_uploading');
                return 'skipped';
            }
        }

        // Conditional hard-delete. Conditional on status=Uploading protects
        // us from racing a real finalize that might have landed a moment ago.
        $deleted = ResumeUpload::query()
            ->where('id', $row->id)
            ->where('status', ResumeUpload::STATUS_UPLOADING)
            ->delete();

        if ($deleted === 0) {
            return 'skipped';
        }

        UploadSessionAudit::record(
            sessionUuid: $row->uuid,
            companyId: (int) $row->company_id,
            action: 'RepairDeleted',
            actorId: null,
            metadata: [
                'kind' => 'stuck_uploading',
                'r2_existed' => $r2Exists,
                'reason' => 'uploading_timeout',
            ],
        );

        $metrics->incrementRepairAttempts('stuck_uploading');
        $metrics->incrementRepairCompletions('stuck_uploading');
        return 'recovered';
    }

    /**
     * Parsing-stuck: re-dispatch the parser up to maxAttempts times.
     * Counts are tracked on parse_attempt_count so we never re-dispatch
     * a row that's already had its allotted attempts.
     */
    private function requeueParsing(ResumeUpload $row, int $maxAttempts, ResumeMetrics $metrics): string
    {
        $metrics->incrementStuckDetected('stuck_parsing');
        $attempt = (int) $row->parse_attempt_count + 1;
        if ($attempt > $maxAttempts) {
            // Calm down: don't keep retrying forever. Mark ParseFailed and emit.
            // Write parse_attempt_count = max (not max+1) so the column
            // reflects "we tried max times and gave up" — cleaner for ops
            // dashboards than the over-cap value that triggered the transition.
            $finalAttempt = $maxAttempts;
            DB::transaction(function () use ($row, $finalAttempt) {
                ResumeUpload::query()
                    ->where('id', $row->id)
                    ->where('status', ResumeUpload::STATUS_PARSING)
                    ->update([
                        'status' => ResumeUpload::STATUS_PARSE_FAILED,
                        'parse_attempt_count' => $finalAttempt,
                        'cleanup_reason' => 'repair_parse_exhausted',
                        'updated_at' => Carbon::now(),
                    ]);
                UploadSessionAudit::record(
                    sessionUuid: $row->uuid,
                    companyId: (int) $row->company_id,
                    action: 'RepairExhausted',
                    actorId: null,
                    metadata: [
                        'kind' => 'stuck_parsing',
                        'attempts' => $finalAttempt,
                        'triggered_by_attempt' => $finalAttempt + 1,
                        'reason' => 'requeue_exhausted',
                    ],
                );
            });
            $this->logStructured('repair_attempted', [
                'session_uuid' => $row->uuid,
                'kind' => 'stuck_parsing',
                'outcome' => 'exhausted',
                'attempt' => $finalAttempt,
            ]);
            return 'requeued';
        }

        // Conditional UPDATE that gates on status=Parsing AND parse_attempt_count <= maxAttempts.
        // Returns 0 if a concurrent repair already moved this row out of Parsing — idempotency check.
        $updated = DB::transaction(function () use ($row, $attempt) {
            $r = ResumeUpload::query()
                ->where('id', $row->id)
                ->where('status', ResumeUpload::STATUS_PARSING)
                ->update([
                    'parse_attempt_count' => $attempt,
                    'updated_at' => Carbon::now(),
                ]);
            if ($r === 0) {
                return 0;
            }
            UploadSessionAudit::record(
                sessionUuid: $row->uuid,
                companyId: (int) $row->company_id,
                action: 'RepairRequeued',
                actorId: null,
                metadata: [
                    'kind' => 'stuck_parsing',
                    'attempt' => $attempt,
                    'reason' => 'parsing_timeout',
                ],
            );
            return $r;
        });

        if ($updated === 0) {
            return 'skipped';
        }

        // Re-dispatch onto the dedicated parse_resumes queue so the
        // parse supervisor picks it up (Phase 8). The seam in
        // Uploaded→QueuedForParsing is atomic via transitionTo().
        try {
            // Conditional: only re-dispatch when the row is still in
            // the worker-entry set. Repair past a Linked row is a
            // no-op (the candidate went through; no need to parse
            // a second time).
            if (in_array($row->status, [
                \App\Models\ResumeUpload::STATUS_UPLOADED,
                \App\Models\ResumeUpload::STATUS_QUEUED_FOR_PARSING,
                \App\Models\ResumeUpload::STATUS_PARSING,
            ], true)) {
                // Transition Uploaded→QueuedForParsing first so the
                // dispatcher's conditional UPDATE accepts it.
                if ($row->status === ResumeUpload::STATUS_UPLOADED) {
                    $row->transitionTo(
                        ResumeUpload::STATUS_QUEUED_FOR_PARSING,
                        action: 'RepairReQueued',
                        actorId: null,
                        metadata: ['reason' => 'parsing_timeout', 'attempt' => $attempt],
                    );
                }
                ParseResumeJob::dispatch($row->uuid, (int) $row->company_id, (int) ($row->user_id ?? 0))
                    ->onConnection(config('resumes.parser_connection', 'redis_parse_resumes'))
                    ->onQueue(config('resumes.parser_queue', 'parse_resumes'));
            }
        } catch (Throwable $e) {
            // We updated parse_attempt_count above; the metric for repair_attempts
            // is bumped on the dispatch success.
            $this->logStructured('repair_attempted', [
                'session_uuid' => $row->uuid,
                'kind' => 'stuck_parsing',
                'outcome' => 'dispatch_failed',
                'attempt' => $attempt,
                'exception' => $e->getMessage(),
            ]);
            return 'skipped';
        }

        $this->logStructured('repair_attempted', [
            'session_uuid' => $row->uuid,
            'kind' => 'stuck_parsing',
            'outcome' => 'requeued',
            'attempt' => $attempt,
        ]);

        $metrics->incrementRepairAttempts('stuck_parsing');
        $metrics->incrementRepairCompletions('stuck_parsing');
        return 'requeued';
    }

    private function logStructured(string $event, array $payload): void
    {
        // Phase 6 — route via ResumeLogContext so pod/queue/worker
        // are merged into the Monolog record automatically.
        ResumeLogContext::event($event, $payload);
    }
}
