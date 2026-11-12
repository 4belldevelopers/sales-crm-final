<?php

namespace App\Jobs;

use App\Events\CompanyDeltaUpdated;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminSubscriptionCacheManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class ProcessSubscriptionOutboxEventJob
 *
 * Intent: Asynchronous background worker processing transactional outbox records
 * for the Super Admin Subscription subsystem (sys_outbox_events). Guarantees at-least-once delivery,
 * dual-store synchronization to Elasticsearch ELASTIC_COMPANIES_INDEX, Reverb WebSocket
 * broadcasting, and Dead-Letter Queue (DLQ) tracking under 10K concurrency.
 *
 * Tenancy Note: Tenant-scoped by aggregate_id (company_id).
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessSubscriptionOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $timeout = 60;

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
            ->where('aggregate_type', 'SubscriptionCompany')
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
     * Process an individual subscription outbox event with optimistic locking.
     */
    protected function processEvent(int $eventId, ElasticService $elasticService): void
    {
        $claimed = DB::table('sys_outbox_events')
            ->where('id', $eventId)
            ->where('aggregate_type', 'SubscriptionCompany')
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

        $index = config('constants.ELASTIC_COMPANIES_INDEX', defined('ELASTIC_COMPANIES_INDEX') ? ELASTIC_COMPANIES_INDEX : 'local_companies_elastic');

        try {
            $payload = is_string($event->payload) ? json_decode($event->payload, true) : (array) $event->payload;
            $payload = $payload ?: [];
            $companyId = (int) ($event->aggregate_id ?: ($payload['company_id'] ?? 0));
            $eventType = $event->event_type;

            $aggregateVersion = (int) ($event->aggregate_version ?: ($payload['aggregate_version'] ?? 1));

            // 1. Dual-Store Synchronization to Elasticsearch
            if ($eventType === 'COMPANY_STATUS_TOGGLED') {
                $packStatus = $payload['pack_status'] ?? ($payload['status'] ?? 'active');
                $updateTime = now()->toIso8601String();

                // Prevent zombie resurrection: if company was deleted in MySQL, do not upsert into Elasticsearch
                $companyExists = DB::table('companies')->where('id', $companyId)->exists();
                if (! $companyExists) {
                    Log::info("ProcessSubscriptionOutboxEventJob: Skipping COMPANY_STATUS_TOGGLED for deleted company #{$companyId}");
                } elseif ($elasticService->indexExists($index)) {
                    $elasticService->getClient()->update([
                        'index' => $index,
                        'id' => (string) $companyId,
                        'refresh' => 'wait_for',
                        'retry_on_conflict' => 5,
                        'body' => [
                            'script' => [
                                'source' => '
                                    if (ctx._source.aggregate_version == null || ctx._source.aggregate_version <= params.aggregate_version) {
                                        ctx._source.status = params.status;
                                        ctx._source.aggregate_version = params.aggregate_version;
                                        ctx._source.updated_at = params.updated_at;
                                        ctx._source.curr_date = params.updated_at;
                                    } else {
                                        ctx.op = "none";
                                    }
                                ',
                                'lang' => 'painless',
                                'params' => [
                                    'status' => $packStatus,
                                    'aggregate_version' => $aggregateVersion,
                                    'updated_at' => $updateTime,
                                ],
                            ],
                            'upsert' => [
                                'id' => $companyId,
                                'status' => $packStatus,
                                'aggregate_version' => $aggregateVersion,
                                'updated_at' => $updateTime,
                                'curr_date' => $updateTime,
                            ],
                        ],
                    ]);
                }

                // Also update ELASTIC_USERS_LIST if index exists
                $usersIndex = config('constants.ELASTIC_USERS_LIST', defined('ELASTIC_USERS_LIST') ? ELASTIC_USERS_LIST : 'local_users_elastic');
                if ($companyExists && $elasticService->indexExists($usersIndex)) {
                    try {
                        $elasticService->getClient()->updateByQuery([
                            'index' => $usersIndex,
                            'body' => [
                                'script' => [
                                    'source' => '
                                        ctx._source.status = params.status;
                                        ctx._source.working_status = params.working_status;
                                        ctx._source.updated_at = params.updated_at;
                                    ',
                                    'lang' => 'painless',
                                    'params' => [
                                        'status' => $packStatus,
                                        'working_status' => 1,
                                        'updated_at' => $updateTime,
                                    ],
                                ],
                                'query' => [
                                    'bool' => [
                                        'must' => [
                                            ['term' => ['company_id' => $companyId]],
                                            ['term' => ['working_status' => 1]],
                                        ],
                                    ],
                                ],
                            ],
                        ]);
                    } catch (Throwable $userEx) {
                        Log::warning("ProcessSubscriptionOutboxEventJob: ELASTIC_USERS_LIST update warning for company {$companyId}: ".$userEx->getMessage());
                    }
                }
            } elseif ($eventType === 'COMPANY_COIN_STATUS_TOGGLED') {
                $coinEventType = $payload['coin_event_type'] ?? 'operational';
                $packStatus = $payload['pack_status'] ?? 'active';

                if ($elasticService->indexExists($index)) {
                    $elasticService->getClient()->update([
                        'index' => $index,
                        'id' => (string) $companyId,
                        'refresh' => 'wait_for',
                        'retry_on_conflict' => 5,
                        'body' => [
                            'script' => [
                                'source' => '
                                    if (ctx._source.aggregate_version == null || ctx._source.aggregate_version <= params.aggregate_version) {
                                        ctx._source.aggregate_version = params.aggregate_version;
                                        if (ctx._source.company_coins != null) {
                                            for (int i = 0; i < ctx._source.company_coins.length; i++) {
                                                if (ctx._source.company_coins[i].event_type == params.event_type) {
                                                    ctx._source.company_coins[i].free_pack_status = params.status;
                                                }
                                            }
                                        }
                                    } else {
                                        ctx.op = "none";
                                    }
                                ',
                                'lang' => 'painless',
                                'params' => [
                                    'event_type' => $coinEventType,
                                    'status' => $packStatus,
                                    'aggregate_version' => $aggregateVersion,
                                ],
                            ],
                        ],
                    ]);
                }
            } elseif ($eventType === 'COMPANY_DELETED') {
                if ($elasticService->indexExists($index)) {
                    try {
                        // Monotonic delete guard: ensure existing aggregate version <= event aggregate version
                        $existingDoc = null;
                        try {
                            $existingDoc = $elasticService->getClient()->get([
                                'index' => $index,
                                'id' => (string) $companyId,
                            ]);
                        } catch (Throwable) {}

                        $existingVer = (int) ($existingDoc['_source']['aggregate_version'] ?? 0);
                        if ($existingDoc === null || $existingVer <= $aggregateVersion) {
                            $elasticService->getClient()->delete([
                                'index' => $index,
                                'id' => (string) $companyId,
                                'refresh' => 'wait_for',
                            ]);
                        } else {
                            Log::info("ProcessSubscriptionOutboxEventJob: Skipping COMPANY_DELETED for company #{$companyId} because existing aggregate_version ({$existingVer}) > event version ({$aggregateVersion})");
                        }
                    } catch (Throwable $delEx) {
                        if (! str_contains($delEx->getMessage(), '404')) {
                            throw $delEx;
                        }
                    }
                }
            }

            // 2. Invalidate L2 in-memory subscription caches
            SuperAdminSubscriptionCacheManager::flushCaches($companyId);

            // 3. Broadcast real-time delta pulse via Reverb WebSockets
            try {
                broadcast(new CompanyDeltaUpdated(
                    companyId: $companyId,
                    action: $payload['pack_status'] ?? ($eventType === 'COMPANY_DELETED' ? 'deleted' : 'updated'),
                    delta: $payload,
                    entityVersion: (int) ($event->aggregate_version ?? 1),
                    counts: [],
                    updatedBy: 'Super Admin'
                ));
            } catch (Throwable $bcEx) {
                Log::warning('ProcessSubscriptionOutboxEventJob: WebSocket broadcast warning: '.$bcEx->getMessage());
            }

            // 4. Mark Outbox event COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);
        } catch (Throwable $e) {
            Log::error("ProcessSubscriptionOutboxEventJob: Outbox processing failed for event #{$eventId}: ".$e->getMessage());

            $attempts = (int) $event->attempts + 1;
            $newStatus = ($attempts >= $this->tries) ? 'DEAD_LETTER' : 'FAILED';
            $backoffSeconds = (int) pow(2, min($attempts, 6)) * 10;

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $newStatus,
                    'last_error' => substr($e->getMessage()."\n".$e->getTraceAsString(), 0, 1000),
                    'available_at' => now()->addSeconds($backoffSeconds),
                ]);
        }
    }
}
