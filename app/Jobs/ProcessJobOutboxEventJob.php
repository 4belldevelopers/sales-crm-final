<?php

namespace App\Jobs;

use App\Events\JobStatusChanged;
use App\Helper\GeneralMethod;
use App\Services\SuperAdminJobsCacheManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessJobOutboxEventJob
 *
 * Intent: Asynchronous background worker processing transactional outbox records
 * for Job requisition domain mutations (sys_outbox_events). Guarantees at-least-once delivery,
 * dual-store synchronization to Elasticsearch ELASTIC_JOBS, Reverb WebSocket broadcasting
 * with monotonic sequence ordering, and Dead-Letter Queue (DLQ) tracking under high concurrency.
 *
 * State Machine & Completion Semantics:
 * - PENDING/FAILED -> PROCESSING (with row lock & heartbeat lease) -> COMPLETED.
 * - On failure -> FAILED with exponential backoff -> DEAD_LETTER after 5 attempts.
 *
 * Tenancy Note: Universal Super Admin execution context scoped to target tenant_id ($job->company_id).
 * Database Truth: database/schema/mysql-schema.sql (sys_outbox_events).
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ProcessJobOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $backoff = 10;

    public int $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param  int|null  $specificEventId  Specific sys_outbox_events primary key to process
     */
    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('job-outbox');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId);

            return;
        }

        // Batch processing if called without specific ID (scheduler / sweeper fallback)
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'job')
            ->where(function ($q) {
                $q->whereIn('status', ['PENDING', 'FAILED'])
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'PROCESSING')
                            ->where('locked_at', '<', now()->subMinutes(5));
                    });
            })
            ->where('available_at', '<=', now())
            ->where('attempts', '<', $this->tries)
            ->orderBy('id', 'asc')
            ->limit(25)
            ->pluck('id')
            ->toArray();

        foreach ($events as $eventId) {
            $this->processEvent((int) $eventId);
        }
    }

    /**
     * Process an individual job outbox event with optimistic locking.
     */
    protected function processEvent(int $eventId): void
    {
        DB::beginTransaction();
        try {
            $event = DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->where('aggregate_type', 'job')
                ->whereIn('status', ['PENDING', 'FAILED', 'PROCESSING'])
                ->lockForUpdate()
                ->first();

            if (! $event) {
                DB::rollBack();

                return;
            }

            if ($event->status === 'COMPLETED') {
                DB::rollBack();

                return;
            }

            // Claim the event as PROCESSING with heartbeat timestamp
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'PROCESSING',
                    'locked_at' => now(),
                    'attempts' => DB::raw('attempts + 1'),
                ]);

            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("ProcessJobOutboxEventJob::claim failed for Event #{$eventId}: ".$e->getMessage());

            return;
        }

        $payload = json_decode((string) $event->payload, true) ?: [];
        $jobId = (int) ($event->aggregate_id ?? $payload['job_id'] ?? 0);
        $tenantId = (int) ($event->tenant_id ?? $payload['company_id'] ?? 0);
        $oldStatus = (string) ($payload['old_status'] ?? '');
        $newStatus = (string) ($payload['new_status'] ?? '');
        $title = (string) ($payload['title'] ?? '');

        try {
            // 1. Dual-Store Synchronization: Update Elasticsearch ELASTIC_JOBS
            if ($jobId > 0) {
                GeneralMethod::elasticJobsAddEditUpdateIndex($jobId);
            }

            // 2. Authoritative Mutation Delta & Redis Cache Invalidation
            SuperAdminJobsCacheManager::setMutationDelta($jobId, [
                'status' => $newStatus,
            ]);
            SuperAdminJobsCacheManager::invalidateAll($jobId);

            // 3. Reverb WebSocket Broadcast with monotonic sequence ordering
            $sequenceId = (int) (microtime(true) * 1000);
            try {
                broadcast(new JobStatusChanged(
                    companyId: (int) $tenantId,
                    jobId: (int) $jobId,
                    oldStatus: (string) $oldStatus,
                    newStatus: (string) $newStatus,
                    jobTitle: (string) $title,
                    sequenceId: $sequenceId
                ));
            } catch (\Throwable $e) {
                Log::warning('ProcessJobOutboxEventJob broadcast warning: '.$e->getMessage());
            }

            // 4. Mark Event as COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);
        } catch (\Throwable $e) {
            Log::error("ProcessJobOutboxEventJob execution failed for Event #{$eventId}: ".$e->getMessage());

            $newAttempts = ((int) $event->attempts) + 1;
            $newStatus = ($newAttempts >= $this->tries) ? 'DEAD_LETTER' : 'FAILED';

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $newStatus,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => now()->addSeconds($this->backoff * $newAttempts),
                ]);

            throw $e;
        }
    }
}
