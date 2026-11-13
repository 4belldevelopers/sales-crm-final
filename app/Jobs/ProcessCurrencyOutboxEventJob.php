<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminCurrencyCacheManager;
use App\Services\SuperAdmin\SuperAdminCurrencyReadService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class ProcessCurrencyOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Super Admin Currency Settings subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_currencies),
 * cache invalidation in Redis/Dragonfly L2 cache, and atomic status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessCurrencyOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public int $timeout = 60;

    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('outbox');
    }

    /**
     * Resolve target Elasticsearch index name for currencies.
     */
    protected function getIndexName(): string
    {
        if (defined('ELASTIC_CURRENCIES')) {
            return ELASTIC_CURRENCIES;
        }

        return (string) config('constants.ELASTIC_CURRENCIES', 'local_currencies');
    }

    public function handle(?ElasticService $elasticService = null): void
    {
        $elasticService = $elasticService ?? app(ElasticService::class);

        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId, $elasticService);

            return;
        }

        // Batch processing when triggered by scheduler or CLI
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Currency')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit(25)
            ->pluck('id')
            ->toArray();

        foreach ($events as $eventId) {
            $this->processEvent((int) $eventId, $elasticService);
        }
    }

    protected function processEvent(int $eventId, ElasticService $elasticService): void
    {
        $claimed = DB::table('sys_outbox_events')
            ->where('id', $eventId)
            ->where('status', 'PENDING')
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
            $indexName = $this->getIndexName();
            $readService = app(SuperAdminCurrencyReadService::class);

            // Ensure primary currencies index exists
            if (! $elasticService->indexExists($indexName)) {
                $elasticService->createIndex($indexName, $readService->getIndexMapping());
            }

            $payload = json_decode($event->payload, true) ?? [];
            $eventType = $event->event_type;
            $aggregateId = (int) $event->aggregate_id;
            $aggregateVersion = (int) $event->aggregate_version;

            if ($eventType === 'CURRENCY_CREATED' || $eventType === 'CURRENCY_UPDATED') {
                $attributes = $payload['attributes'] ?? [];

                // Guard: Deletion tombstone check prevents resurrecting deleted entities
                if (SuperAdminCurrencyCacheManager::hasTombstone($aggregateId)) {
                    Log::info("ProcessCurrencyOutboxEventJob: Skipping indexing for aggregate ID {$aggregateId} because deletion tombstone is active.", [
                        'event_id' => $eventId,
                        'event_type' => $eventType,
                    ]);

                    $this->markCompleted($eventId);

                    return;
                }

                // Optimistic Concurrency Control: verify existing document version in ES
                try {
                    $existingDoc = $elasticService->getClient()->get([
                        'index' => $indexName,
                        'id' => (string) $aggregateId,
                    ]);

                    $existingVersion = (int) ($existingDoc['_source']['aggregate_version'] ?? 0);
                    if ($existingVersion > $aggregateVersion) {
                        Log::warning("ProcessCurrencyOutboxEventJob: Stale aggregate event skipped (existing: {$existingVersion}, event: {$aggregateVersion})", [
                            'event_id' => $eventId,
                            'currency_id' => $aggregateId,
                        ]);

                        $this->markCompleted($eventId);

                        return;
                    }
                } catch (Throwable) {
                    // Document does not exist yet; safe to proceed with index
                }

                $elasticService->getClient()->index([
                    'index' => $indexName,
                    'id' => (string) $aggregateId,
                    'body' => $attributes,
                    'refresh' => false,
                ]);
            } elseif ($eventType === 'CURRENCY_DELETED') {
                // Ensure tombstone is active in Redis
                SuperAdminCurrencyCacheManager::setTombstone($aggregateId);

                try {
                    $elasticService->getClient()->delete([
                        'index' => $indexName,
                        'id' => (string) $aggregateId,
                        'refresh' => false,
                    ]);
                } catch (Throwable) {
                    // Document already deleted or never existed
                }
            } elseif ($eventType === 'CURRENCY_RATES_SYNCED') {
                $updatedList = $payload['updated_currencies'] ?? [];
                if (! empty($updatedList)) {
                    $bulkParams = ['body' => []];
                    foreach ($updatedList as $currDoc) {
                        $bulkParams['body'][] = [
                            'index' => [
                                '_index' => $indexName,
                                '_id' => (string) $currDoc['id'],
                            ],
                        ];
                        $bulkParams['body'][] = $currDoc;
                    }

                    $elasticService->bulkIndex($bulkParams);
                }
            }

            // Invalidate L2 cache
            SuperAdminCurrencyCacheManager::flushCaches();

            $this->markCompleted($eventId);
        } catch (Throwable $e) {
            Log::error("ProcessCurrencyOutboxEventJob failed for event ID {$eventId}: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $attempts = (int) $event->attempts;
            $nextStatus = ($attempts >= $this->tries) ? 'DEAD_LETTER' : 'PENDING';

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $nextStatus,
                    'last_error' => substr($e->getMessage()."\n".$e->getTraceAsString(), 0, 4000),
                    'available_at' => Carbon::now()->addSeconds($this->backoff * $attempts),
                    'locked_at' => null,
                ]);

            if ($nextStatus === 'DEAD_LETTER') {
                Log::critical("ProcessCurrencyOutboxEventJob: Event ID {$eventId} moved to DEAD_LETTER queue after {$attempts} attempts.");
            }
        }
    }

    protected function markCompleted(int $eventId): void
    {
        DB::table('sys_outbox_events')
            ->where('id', $eventId)
            ->update([
                'status' => 'COMPLETED',
                'processed_at' => Carbon::now(),
                'locked_at' => null,
                'last_error' => null,
            ]);
    }
}
