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

            // Guard against zombie resurrection: If company was deleted in MySQL or tombstoned in Cache, skip delayed updates/toggles
            $aggregateVersion = (int) ($event->aggregate_version ?? 1);
            $tombstoneVersion = Cache::get("company:tombstone:{$companyId}");

            if ($eventType !== 'COMPANY_DELETED' && $eventType !== 'COMPANY_CREATED') {
                if ($tombstoneVersion !== null && $aggregateVersion <= $tombstoneVersion) {
                    Log::warning("ProcessCompanyOutboxEventJob: Obsolete event {$eventType} (v{$aggregateVersion}) blocked by tombstone fence (v{$tombstoneVersion}) for company {$companyId}");
                    DB::table('sys_outbox_events')
                        ->where('id', $eventId)
                        ->update([
                            'status' => 'COMPLETED',
                            'processed_at' => now(),
                        ]);

                    return;
                }

                if (! DB::table('companies')->where('id', $companyId)->exists()) {
                    Log::warning("ProcessCompanyOutboxEventJob: Skipping event {$eventType} for deleted/non-existent company {$companyId}");
                    DB::table('sys_outbox_events')
                        ->where('id', $eventId)
                        ->update([
                            'status' => 'COMPLETED',
                            'processed_at' => now(),
                        ]);

                    return;
                }
            }

            // 1. Synchronize to Elasticsearch ELASTIC_COMPANIES_INDEX
            if ($eventType === 'COMPANY_VERIFICATION_TOGGLED') {
                $accountVerify = (int) ($payload['accountVerify'] ?? 0);
                $updateTime = now()->toIso8601String();

                if ($elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
                    try {
                        $elasticService->getClient()->update([
                            'index' => ELASTIC_COMPANIES_INDEX,
                            'id' => (string) $companyId,
                            'refresh' => 'wait_for',
                            'retry_on_conflict' => 5,
                            'body' => [
                                'script' => [
                                    'source' => '
                                        if (ctx._source.aggregate_version == null || ctx._source.aggregate_version <= params.aggregate_version) {
                                            ctx._source.accountVerify = params.accountVerify;
                                            ctx._source.aggregate_version = params.aggregate_version;
                                            ctx._source.updated_at = params.updated_at;
                                            ctx._source.curr_date = params.updated_at;
                                        } else {
                                            ctx.op = "none";
                                        }
                                    ',
                                    'lang' => 'painless',
                                    'params' => [
                                        'accountVerify' => $accountVerify,
                                        'aggregate_version' => $aggregateVersion,
                                        'updated_at' => $updateTime,
                                    ],
                                ],
                            ],
                        ]);
                    } catch (\Throwable $esEx) {
                        if (str_contains($esEx->getMessage(), 'document_missing_exception') || str_contains($esEx->getMessage(), '404')) {
                            Log::info("ProcessCompanyOutboxEventJob: Document {$companyId} absent in ES, verification update safely ignored without resurrection.");
                        } else {
                            throw $esEx;
                        }
                    }
                }
            } elseif ($eventType === 'COMPANY_DELETED') {
                Cache::put("company:tombstone:{$companyId}", $aggregateVersion, now()->addDays(7));
                Cache::forget('company:epoch:'.$companyId);

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
            } elseif ($eventType === 'COMPANY_PACKAGE_UPDATED') {
                $updateTime = now()->toIso8601String();

                if ($elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
                    try {
                        $elasticService->getClient()->update([
                            'index' => ELASTIC_COMPANIES_INDEX,
                            'id' => (string) $companyId,
                            'refresh' => 'wait_for',
                            'retry_on_conflict' => 5,
                            'body' => [
                                'script' => [
                                    'source' => '
                                        if (ctx._source.aggregate_version == null || ctx._source.aggregate_version <= params.aggregate_version) {
                                            ctx._source.package_id = params.package_id;
                                            ctx._source.package_type = params.package_type;
                                            ctx._source.aggregate_version = params.aggregate_version;
                                            ctx._source.updated_at = params.updated_at;
                                            ctx._source.curr_date = params.updated_at;
                                        } else {
                                            ctx.op = "none";
                                        }
                                    ',
                                    'lang' => 'painless',
                                    'params' => [
                                        'package_id' => $payload['package_id'] ?? null,
                                        'package_type' => $payload['package_type'] ?? null,
                                        'aggregate_version' => $aggregateVersion,
                                        'updated_at' => $updateTime,
                                    ],
                                ],
                            ],
                        ]);
                    } catch (\Throwable $esEx) {
                        if (str_contains($esEx->getMessage(), 'document_missing_exception') || str_contains($esEx->getMessage(), '404')) {
                            Log::info("ProcessCompanyOutboxEventJob: Document {$companyId} absent in ES, package update safely ignored without resurrection.");
                        } else {
                            throw $esEx;
                        }
                    }
                }
            } elseif ($eventType === 'COMPANY_CREATED') {
                if ($elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
                    try {
                        app(\App\Services\ElasticCaompanyIndex::class)->companyIndexElastic($companyId);
                    } catch (\Throwable $indexEx) {
                        Log::error("ProcessCompanyOutboxEventJob: Elastic indexing failed on COMPANY_CREATED for company {$companyId}: ".$indexEx->getMessage());
                    }
                }
            } elseif ($eventType === 'COMPANY_UPDATED') {
                if ($elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
                    $doc = [
                        'id' => $companyId,
                        'curr_date' => now()->toIso8601String(),
                    ];
                    if (isset($payload['company_name'])) {
                        $doc['company_name'] = $payload['company_name'];
                    }
                    if (isset($payload['website'])) {
                        $doc['website'] = $payload['website'];
                    }
                    if (isset($payload['industries'])) {
                        $doc['industries'] = $payload['industries'];
                    }
                    if (isset($payload['company_size'])) {
                        $doc['company_size'] = $payload['company_size'];
                    }
                    if (isset($payload['industries_type'])) {
                        $doc['industries_type'] = $payload['industries_type'];
                    }
                    if (isset($payload['founded'])) {
                        $doc['founded'] = $payload['founded'];
                    }
                    if (isset($payload['date_format'])) {
                        $doc['date_format'] = $payload['date_format'];
                    }
                    if (isset($payload['description'])) {
                        $doc['description'] = $payload['description'];
                    }
                    if (! empty($payload['logo'])) {
                        $doc['logo'] = $payload['logo'];
                    }
                    if (isset($payload['accountVerify'])) {
                        $doc['accountVerify'] = (int) $payload['accountVerify'];
                    }

                    try {
                        $elasticService->getClient()->update([
                            'index' => ELASTIC_COMPANIES_INDEX,
                            'id' => (string) $companyId,
                            'retry_on_conflict' => 5,
                            'body' => [
                                'doc' => $doc,
                                'doc_as_upsert' => false,
                                'detect_noop' => true,
                            ],
                        ]);
                    } catch (\Throwable $esUpEx) {
                        if (str_contains($esUpEx->getMessage(), 'document_missing_exception') || str_contains($esUpEx->getMessage(), '404')) {
                            Log::info("ProcessCompanyOutboxEventJob: Document {$companyId} absent in ES, update skipped safely.");
                        } else {
                            throw $esUpEx;
                        }
                    }
                }
            } elseif ($eventType === 'COMPANY_STATUS_TOGGLED') {
                $packStatus = $payload['pack_status'] ?? ($payload['status'] ?? 'active');
                $updateTime = now()->toIso8601String();

                // 1. Elastic Users Sync
                try {
                    if ($elasticService->indexExists(ELASTIC_USERS_LIST)) {
                        $elasticService->getClient()->updateByQuery([
                            'index' => ELASTIC_USERS_LIST,
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
                    }
                } catch (\Throwable $e) {
                    Log::error("ProcessCompanyOutboxEventJob: ELASTIC_USERS_LIST update failed for company {$companyId}: ".$e->getMessage());
                }

                // 2. Elastic Companies Sync
                try {
                    $aggregateVersion = (int) ($event->aggregate_version ?? 1);
                    if ($elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
                        $elasticService->getClient()->update([
                            'index' => ELASTIC_COMPANIES_INDEX,
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
                            ],
                        ]);
                    }
                } catch (\Throwable $e) {
                    if (str_contains($e->getMessage(), 'document_missing_exception') || str_contains($e->getMessage(), '404')) {
                        Log::info("ProcessCompanyOutboxEventJob: Document {$companyId} absent in ES, status update skipped safely.");
                    } else {
                        Log::error("ProcessCompanyOutboxEventJob: ELASTIC_COMPANIES_INDEX update failed for company {$companyId}: ".$e->getMessage());
                    }
                }
            } elseif ($eventType === 'COMPANY_COIN_STATUS_TOGGLED') {
                $coinEventType = $payload['coin_event_type'] ?? 'operational';
                $packStatus = $payload['pack_status'] ?? 'active';
                $aggregateVersion = (int) ($event->aggregate_version ?? 1);

                try {
                    if ($elasticService->indexExists(ELASTIC_COMPANIES_INDEX)) {
                        $elasticService->getClient()->update([
                            'index' => ELASTIC_COMPANIES_INDEX,
                            'id' => (string) $companyId,
                            'refresh' => 'wait_for',
                            'retry_on_conflict' => 5,
                            'body' => [
                                'script' => [
                                    'source' => '
                                        if (ctx._source.aggregate_version == null || ctx._source.aggregate_version <= params.aggregate_version) {
                                            if (ctx._source.company_coins != null) {
                                                for (int i = 0; i < ctx._source.company_coins.length; i++) {
                                                    if (ctx._source.company_coins[i].event_type == params.event_type) {
                                                        ctx._source.company_coins[i].free_pack_status = params.status;
                                                    }
                                                }
                                            }
                                            ctx._source.aggregate_version = params.aggregate_version;
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
                } catch (\Throwable $e) {
                    Log::error("ProcessCompanyOutboxEventJob: ELASTIC_COMPANIES_INDEX coin update failed for company {$companyId}: ".$e->getMessage());
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

            // 3. Invalidate company caches & mark outbox record as COMPLETED
            Cache::forget('company:epoch:'.$companyId);
            if (class_exists(\App\Services\SuperAdmin\SuperAdminCompanyCacheManager::class)) {
                \App\Services\SuperAdmin\SuperAdminCompanyCacheManager::flushCaches($companyId);
            }
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
