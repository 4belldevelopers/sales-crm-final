<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminLanguageCacheManager;
use App\Services\SuperAdmin\SuperAdminLanguageReadService;
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
 * Class ProcessLanguageOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Super Admin Language & Localization Hub subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_language_settings),
 * cache invalidation in Redis/Dragonfly L2 cache, and atomic status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessLanguageOutboxEventJob implements ShouldQueue
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
     * Resolve target Elasticsearch index name for languages.
     */
    protected function getIndexName(): string
    {
        if (defined('ELASTIC_LANGUAGE_SETTINGS')) {
            return ELASTIC_LANGUAGE_SETTINGS;
        }

        return (string) config('constants.ELASTIC_LANGUAGE_SETTINGS', 'local_language_settings');
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
            ->where('aggregate_type', 'LanguageSetting')
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

        $indexName = $this->getIndexName();
        $readService = app(SuperAdminLanguageReadService::class);

        try {
            // Provision index if missing
            if (! $elasticService->indexExists($indexName)) {
                $elasticService->createIndex($indexName, $readService->getIndexMapping());
            }

            $payload = json_decode($event->payload, true) ?: [];
            $aggregateId = (int) $event->aggregate_id;
            $eventType = $event->event_type;
            $aggregateVersion = (int) ($event->aggregate_version ?? 0);

            // Tombstone check: if entity was deleted, do not resurrect
            if ($eventType !== 'LANGUAGE_DELETED' && SuperAdminLanguageCacheManager::hasTombstone($aggregateId)) {
                DB::table('sys_outbox_events')->where('id', $eventId)->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => 'Skipped: entity has active deletion tombstone',
                ]);

                return;
            }

            if ($eventType === 'LANGUAGE_DELETED') {
                try {
                    $elasticService->getClient()->delete([
                        'index' => $indexName,
                        'id' => (string) $aggregateId,
                        'refresh' => 'wait_for',
                    ]);
                } catch (Throwable $delEx) {
                    // Ignore 404 not found on delete
                    if (! str_contains($delEx->getMessage(), '404') && ! str_contains($delEx->getMessage(), 'not_found')) {
                        throw $delEx;
                    }
                }
            } else {
                // Optimistic Concurrency Control (OCC) check
                $shouldIndex = true;
                try {
                    $existingDoc = $elasticService->getClient()->get([
                        'index' => $indexName,
                        'id' => (string) $aggregateId,
                    ]);

                    $existingVersion = (int) ($existingDoc['_source']['aggregate_version'] ?? 0);
                    if ($existingVersion > $aggregateVersion && $aggregateVersion > 0) {
                        $shouldIndex = false;
                        Log::info("ProcessLanguageOutboxEventJob: Skipped out-of-order event {$eventId}. Existing ES version {$existingVersion} > Event version {$aggregateVersion}");
                    }
                } catch (Throwable $getEx) {
                    // Document doesn't exist yet, safe to index
                }

                if ($shouldIndex) {
                    $doc = $payload['attributes'] ?? $payload;
                    $doc['id'] = $aggregateId;
                    $doc['aggregate_version'] = $aggregateVersion;

                    $elasticService->getClient()->index([
                        'index' => $indexName,
                        'id' => (string) $aggregateId,
                        'body' => $doc,
                        'refresh' => 'wait_for',
                    ]);
                }
            }

            // Mark completed
            DB::table('sys_outbox_events')->where('id', $eventId)->update([
                'status' => 'COMPLETED',
                'processed_at' => now(),
                'locked_at' => null,
            ]);

            // Flush caches
            SuperAdminLanguageCacheManager::flushCaches();
        } catch (Throwable $e) {
            $isLastAttempt = ($event->attempts >= $this->tries);
            $newStatus = $isLastAttempt ? 'DEAD_LETTER' : 'PENDING';
            $nextAvailableAt = Carbon::now()->addSeconds($this->backoff * (int) pow(2, min($event->attempts, 4)));

            DB::table('sys_outbox_events')->where('id', $eventId)->update([
                'status' => $newStatus,
                'available_at' => $nextAvailableAt,
                'last_error' => substr($e->getMessage(), 0, 1000),
                'locked_at' => null,
            ]);

            Log::error("ProcessLanguageOutboxEventJob failed for event {$eventId}: ".$e->getMessage(), [
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}
