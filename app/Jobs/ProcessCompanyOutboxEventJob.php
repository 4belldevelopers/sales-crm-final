<?php

namespace App\Jobs;

use App\Events\CompanyDeltaUpdated;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessCompanyOutboxEventJob
 *
 * Intent: Asynchronous background worker processing transactional outbox records
 * for Company domain mutations (sys_outbox_events). Guarantees at-least-once delivery,
 * dual-store synchronization to Elasticsearch ELASTIC_COMPANIES_INDEX, Reverb WebSocket
 * broadcasting, and Dead-Letter Queue (DLQ) tracking under high concurrency.
 *
 * Tenancy Note: Events are tenant-scoped by tenant_id (company_id).
 *
 * Author: Antigravity AI — Phase 5 Outbox & Idempotency Architecture
 * Date: October 22, 2026
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ProcessCompanyOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of allowed attempts before dead-lettering.
     */
    public int $tries = 5;

    /**
     * Timeout for worker execution in seconds.
     */
    public int $timeout = 60;

    /**
     * Create a new job instance.
     *
     * @param  int|null  $specificEventId  Specific sys_outbox_events primary key to process
     */
    public function __construct(
        public readonly ?int $specificEventId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(ElasticService $elasticService): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId, $elasticService);

            return;
        }

        // Batch processing if called without specific ID (scheduler / sweeper fallback)
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Company')
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
            $this->processEvent((int) $eventId, $elasticService);
        }
    }

    /**
     * Process an individual company outbox event with optimistic locking.
     */
    protected function processEvent(int $eventId, ElasticService $elasticService): void
    {
        // Atomically claim the outbox record
        $claimed = DB::table('sys_outbox_events')
            ->where('id', $eventId)
            ->where('aggregate_type', 'Company')
            ->where(function ($q) {
                $q->whereIn('status', ['PENDING', 'FAILED'])
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'PROCESSING')
                            ->where('locked_at', '<', now()->subMinutes(5));
                    });
            })
            ->update([
                'status' => 'PROCESSING',
                'locked_at' => now(),
                'attempts' => DB::raw('attempts + 1'),
            ]);

        if (! $claimed) {
            return;
        }

        $event = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        if (! $event) {
            return;
        }

        try {
            $payload = is_string($event->payload) ? json_decode($event->payload, true) : (array) $event->payload;
            $payload = $payload ?: [];
            $companyId = (int) ($event->aggregate_id ?: ($payload['company_id'] ?? 0));
            $eventType = $event->event_type;

            // 1. Synchronize to Elasticsearch ELASTIC_COMPANIES_INDEX
            if ($eventType === 'COMPANY_VERIFICATION_TOGGLED') {
                $accountVerify = (int) ($payload['accountVerify'] ?? 0);
                if ($elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
                    $elasticService->getClient()->update([
                        'index' => ELASTIC_COMPANIES_INDEX,
                        'id' => (string) $companyId,
                        'retry_on_conflict' => 5,
                        'body' => [
                            'doc' => [
                                'id' => $companyId,
                                'accountVerify' => $accountVerify,
                                'curr_date' => now()->toIso8601String(),
                            ],
                            'doc_as_upsert' => true,
                            'detect_noop' => false,
                        ],
                    ]);
                }
            } elseif ($eventType === 'COMPANY_DELETED') {
                if ($elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
                    try {
                        $elasticService->getClient()->delete([
                            'index' => ELASTIC_COMPANIES_INDEX,
                            'id' => (string) $companyId,
                        ]);
                    } catch (\Throwable $esDelEx) {
                        // Ignore 404 document not found
                        if (! str_contains($esDelEx->getMessage(), '404')) {
                            throw $esDelEx;
                        }
                    }
                }
                Cache::forget('company:epoch:'.$companyId);
            } elseif ($eventType === 'COMPANY_PACKAGE_UPDATED') {
                if ($elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
                    $elasticService->getClient()->update([
                        'index' => ELASTIC_COMPANIES_INDEX,
                        'id' => (string) $companyId,
                        'retry_on_conflict' => 5,
                        'body' => [
                            'doc' => [
                                'package_id' => $payload['package_id'] ?? null,
                                'package_type' => $payload['package_type'] ?? null,
                                'curr_date' => now()->toIso8601String(),
                            ],
                            'doc_as_upsert' => true,
                            'detect_noop' => false,
                        ],
                    ]);
                }
            }

            // 2. Broadcast real-time delta pulse to Super Admin sessions via Reverb
            try {
                broadcast(new CompanyDeltaUpdated(
                    companyId: $companyId,
                    action: $payload['action'] ?? strtolower(str_replace('COMPANY_', '', $eventType)),
                    delta: $payload['delta'] ?? [],
                    entityVersion: (int) ($event->aggregate_version ?? 1),
                    counts: $payload['counts'] ?? [],
                    updatedBy: $payload['updated_by'] ?? 'Super Admin'
                ));
            } catch (\Throwable $broadcastEx) {
                Log::warning("[COMPANY_OUTBOX] Reverb broadcast failed; proceeding: {$broadcastEx->getMessage()}", [
                    'event_id' => $eventId,
                ]);
            }

            // 3. Mark outbox record as COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);
        } catch (\Throwable $e) {
            $attempts = (int) ($event->attempts + 1);
            $isFinal = $attempts >= $this->tries;
            $newStatus = $isFinal ? 'DEAD_LETTER' : 'FAILED';
            $backoffSeconds = min((int) pow(2, $attempts) * 5, 900);

            Log::error("[COMPANY_OUTBOX] Processing failed for event {$eventId}: {$e->getMessage()}", [
                'event_id' => $eventId,
                'attempts' => $attempts,
                'status' => $newStatus,
            ]);

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $newStatus,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => Carbon::now()->addSeconds($backoffSeconds),
                    'locked_at' => null,
                ]);

            if (! $isFinal) {
                throw $e;
            }
        }
    }
}
