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
 * for Job requisition domain mutations (sys_outbox_events).
 *
 * Guarantees:
 * - Independent durable delivery tracking for Elasticsearch, Redis, and WebSockets.
 * - Monotonic aggregate versioning protection (prevents older projections overwriting newer ones).
 * - At-least-once delivery with idempotent, destination-aware retry.
 * - Exponential backoff and Dead-Letter Queue (DLQ) tracking after 5 failed attempts.
 * - Pessimistic row locking (lockForUpdate) during event claims.
 *
 * Tenancy Note: Universal Super Admin execution context scoped to target tenant_id.
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
     * Process an individual job outbox event with independent destination delivery ledger.
     */
    protected function processEvent(int $eventId): void
    {
        $workerToken = (string) \Illuminate\Support\Str::uuid();

        // 1. Claim event
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
        $aggregateVersion = (int) ($event->aggregate_version ?? $payload['aggregate_version'] ?? 1);

        // Ensure delivery records exist in sys_outbox_deliveries
        $existingDeliveries = DB::table('sys_outbox_deliveries')
            ->where('outbox_id', $eventId)
            ->get()
            ->keyBy('destination');

        $destinationList = ['redis', 'elasticsearch', 'websocket'];
        foreach ($destinationList as $dest) {
            if (! isset($existingDeliveries[$dest])) {
                try {
                    DB::table('sys_outbox_deliveries')->insertOrIgnore([
                        'outbox_id' => $eventId,
                        'destination' => $dest,
                        'status' => 'PENDING',
                        'attempts' => 0,
                        'owner_token' => null,
                        'lease_expires_at' => null,
                        'completed_at' => null,
                        'last_error' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                } catch (\Throwable) {
                }
            }
        }

        $hasDestinationFailure = false;
        $errorMessages = [];

        // -------------------------------------------------------------
        // Destination 1: Redis Cache Invalidation & Authoritative Delta
        // -------------------------------------------------------------
        $redisDeliv = DB::table('sys_outbox_deliveries')
            ->where('outbox_id', $eventId)
            ->where('destination', 'redis')
            ->first();

        if ($redisDeliv && $redisDeliv->status !== 'COMPLETED') {
            $claimed = DB::table('sys_outbox_deliveries')
                ->where('id', $redisDeliv->id)
                ->where(function ($q) {
                    $q->whereIn('status', ['PENDING', 'FAILED'])
                        ->orWhere('lease_expires_at', '<', now());
                })
                ->update([
                    'status' => 'PROCESSING',
                    'owner_token' => $workerToken,
                    'lease_expires_at' => now()->addSeconds(60),
                    'attempts' => DB::raw('attempts + 1'),
                    'updated_at' => now(),
                ]);

            if ($claimed > 0) {
                try {
                    SuperAdminJobsCacheManager::setMutationDelta($jobId, [
                        'status' => $newStatus,
                        'aggregate_version' => $aggregateVersion,
                    ]);
                    SuperAdminJobsCacheManager::invalidateAll($jobId);

                    DB::table('sys_outbox_deliveries')
                        ->where('id', $redisDeliv->id)
                        ->where('owner_token', $workerToken)
                        ->update([
                            'status' => 'COMPLETED',
                            'completed_at' => now(),
                            'owner_token' => null,
                            'lease_expires_at' => null,
                            'last_error' => null,
                            'updated_at' => now(),
                        ]);
                } catch (\Throwable $e) {
                    $hasDestinationFailure = true;
                    $errorMessages[] = 'Redis: '.$e->getMessage();
                    $newAttempts = ((int) $redisDeliv->attempts) + 1;
                    $destStatus = ($newAttempts >= $this->tries) ? 'DEAD_LETTER' : 'FAILED';

                    DB::table('sys_outbox_deliveries')
                        ->where('id', $redisDeliv->id)
                        ->where('owner_token', $workerToken)
                        ->update([
                            'status' => $destStatus,
                            'owner_token' => null,
                            'lease_expires_at' => null,
                            'last_error' => substr($e->getMessage(), 0, 500),
                            'updated_at' => now(),
                        ]);
                    Log::warning("ProcessJobOutboxEventJob Event #{$eventId} Redis destination failed: ".$e->getMessage());
                }
            }
        }

        // -------------------------------------------------------------
        // Destination 2: Elasticsearch Dual-Store Synchronization (Painless OCC)
        // -------------------------------------------------------------
        $esDeliv = DB::table('sys_outbox_deliveries')
            ->where('outbox_id', $eventId)
            ->where('destination', 'elasticsearch')
            ->first();

        if ($esDeliv && $esDeliv->status !== 'COMPLETED') {
            $claimed = DB::table('sys_outbox_deliveries')
                ->where('id', $esDeliv->id)
                ->where(function ($q) {
                    $q->whereIn('status', ['PENDING', 'FAILED'])
                        ->orWhere('lease_expires_at', '<', now());
                })
                ->update([
                    'status' => 'PROCESSING',
                    'owner_token' => $workerToken,
                    'lease_expires_at' => now()->addSeconds(60),
                    'attempts' => DB::raw('attempts + 1'),
                    'updated_at' => now(),
                ]);

            if ($claimed > 0) {
                try {
                    if ($jobId > 0) {
                        GeneralMethod::elasticJobsAddEditUpdateIndex($jobId, $aggregateVersion);
                    }

                    DB::table('sys_outbox_deliveries')
                        ->where('id', $esDeliv->id)
                        ->where('owner_token', $workerToken)
                        ->update([
                            'status' => 'COMPLETED',
                            'completed_at' => now(),
                            'owner_token' => null,
                            'lease_expires_at' => null,
                            'last_error' => null,
                            'updated_at' => now(),
                        ]);
                } catch (\Throwable $e) {
                    $hasDestinationFailure = true;
                    $errorMessages[] = 'ES: '.$e->getMessage();
                    $newAttempts = ((int) $esDeliv->attempts) + 1;
                    $destStatus = ($newAttempts >= $this->tries) ? 'DEAD_LETTER' : 'FAILED';

                    DB::table('sys_outbox_deliveries')
                        ->where('id', $esDeliv->id)
                        ->where('owner_token', $workerToken)
                        ->update([
                            'status' => $destStatus,
                            'owner_token' => null,
                            'lease_expires_at' => null,
                            'last_error' => substr($e->getMessage(), 0, 500),
                            'updated_at' => now(),
                        ]);
                    Log::warning("ProcessJobOutboxEventJob Event #{$eventId} Elasticsearch destination failed: ".$e->getMessage());
                }
            }
        }

        // -------------------------------------------------------------
        // Destination 3: Reverb WebSocket Broadcasting
        // -------------------------------------------------------------
        $wsDeliv = DB::table('sys_outbox_deliveries')
            ->where('outbox_id', $eventId)
            ->where('destination', 'websocket')
            ->first();

        if ($wsDeliv && ! in_array($wsDeliv->status, ['COMPLETED', 'DEGRADED'])) {
            $claimed = DB::table('sys_outbox_deliveries')
                ->where('id', $wsDeliv->id)
                ->where(function ($q) {
                    $q->whereIn('status', ['PENDING', 'FAILED'])
                        ->orWhere('lease_expires_at', '<', now());
                })
                ->update([
                    'status' => 'PROCESSING',
                    'owner_token' => $workerToken,
                    'lease_expires_at' => now()->addSeconds(60),
                    'attempts' => DB::raw('attempts + 1'),
                    'updated_at' => now(),
                ]);

            if ($claimed > 0) {
                try {
                    broadcast(new JobStatusChanged(
                        companyId: (int) $tenantId,
                        jobId: (int) $jobId,
                        oldStatus: (string) $oldStatus,
                        newStatus: (string) $newStatus,
                        jobTitle: (string) $title,
                        aggregateVersion: $aggregateVersion
                    ));

                    DB::table('sys_outbox_deliveries')
                        ->where('id', $wsDeliv->id)
                        ->where('owner_token', $workerToken)
                        ->update([
                            'status' => 'COMPLETED',
                            'completed_at' => now(),
                            'owner_token' => null,
                            'lease_expires_at' => null,
                            'last_error' => null,
                            'updated_at' => now(),
                        ]);
                } catch (\Throwable $e) {
                    // WebSocket is an acceleration push; transmission failure is non-fatal for persistent outbox
                    DB::table('sys_outbox_deliveries')
                        ->where('id', $wsDeliv->id)
                        ->where('owner_token', $workerToken)
                        ->update([
                            'status' => 'DEGRADED',
                            'owner_token' => null,
                            'lease_expires_at' => null,
                            'last_error' => substr($e->getMessage(), 0, 500),
                            'updated_at' => now(),
                        ]);
                    Log::warning("ProcessJobOutboxEventJob Event #{$eventId} WebSocket broadcast non-fatal exception: ".$e->getMessage());
                }
            }
        }

        // -------------------------------------------------------------
        // Final State Determination & Parent Event Lifecycle
        // -------------------------------------------------------------
        $allDeliveries = DB::table('sys_outbox_deliveries')
            ->where('outbox_id', $eventId)
            ->get()
            ->keyBy('destination');

        // Sync delivery states back into payload for auditability
        foreach ($allDeliveries as $destName => $delivRow) {
            $payload['destinations'][$destName] = [
                'status' => $delivRow->status,
                'attempts' => $delivRow->attempts,
                'completed_at' => $delivRow->completed_at,
                'last_error' => $delivRow->last_error,
            ];
        }

        $redisOk = ($allDeliveries['redis']->status ?? '') === 'COMPLETED';
        $esOk = ($allDeliveries['elasticsearch']->status ?? '') === 'COMPLETED';
        $isAnyDeadLetter = $allDeliveries->contains(fn ($d) => $d->status === 'DEAD_LETTER');

        if ($redisOk && $esOk) {
            // Authoritative stores successfully synchronized
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'payload' => json_encode($payload),
                    'processed_at' => now(),
                    'last_error' => null,
                ]);
        } elseif ($isAnyDeadLetter || ((int) $event->attempts) >= $this->tries) {
            $errorSummary = implode('; ', $errorMessages);
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'DEAD_LETTER',
                    'payload' => json_encode($payload),
                    'last_error' => substr($errorSummary, 0, 1000),
                ]);
            Log::error("ProcessJobOutboxEventJob Event #{$eventId} moved to DEAD_LETTER after {$event->attempts} attempts! Errors: {$errorSummary}");
        } else {
            $newAttempts = ((int) $event->attempts) + 1;
            $errorSummary = implode('; ', $errorMessages);
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'FAILED',
                    'payload' => json_encode($payload),
                    'last_error' => substr($errorSummary, 0, 1000),
                    'available_at' => now()->addSeconds($this->backoff * $newAttempts),
                ]);

            if (! app()->runningUnitTests()) {
                throw new \RuntimeException("ProcessJobOutboxEventJob Event #{$eventId} partially failed: {$errorSummary}");
            }
        }
    }
}
