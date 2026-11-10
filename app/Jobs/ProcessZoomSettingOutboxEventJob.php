<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminZoomSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminZoomSettingReadService;
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
 * Class ProcessZoomSettingOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Super Admin Zoom Video Meeting subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_zoom_setting),
 * cache invalidation in Redis/Dragonfly, and atomic status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessZoomSettingOutboxEventJob implements ShouldQueue
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
     * Resolve target Elasticsearch index name.
     */
    protected function getIndexName(): string
    {
        if (defined('ELASTIC_ZOOM_SETTING')) {
            return ELASTIC_ZOOM_SETTING;
        }

        return (string) config('constants.ELASTIC_ZOOM_SETTING', 'local_zoom_setting');
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
            ->where('aggregate_type', 'ZoomSetting')
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
            $readService = app(SuperAdminZoomSettingReadService::class);

            // Ensure index exists
            if (! $elasticService->indexExists($indexName)) {
                $this->createIndexWithSchema($elasticService, $indexName, $readService);
            }

            $record = DB::table('zoomsettings')->where('id', 1)->first()
                ?? DB::table('zoomsettings')->first();

            if (! $record) {
                // If record was removed or unpopulated, use payload data
                $payload = is_string($event->payload) ? json_decode($event->payload, true) : (array) $event->payload;
                $record = (object) ($payload['attributes'] ?? [
                    'id' => 1,
                    'title' => 'JERO Official Zoom Integration',
                    'redirect_uri' => '',
                    'client_id' => '',
                    'client_secret' => '',
                    'zoom_access_token' => '',
                    'status' => 1,
                ]);
            }

            $doc = $readService->buildZoomSettingDocument($record);

            $elasticService->getClient()->index([
                'index' => $indexName,
                'id' => '1',
                'body' => $doc,
                'refresh' => 'wait_for',
            ]);

            // Invalidate L2 caches
            SuperAdminZoomSettingCacheManager::flushCaches();

            // Mark outbox event completed
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => Carbon::now(),
                    'last_error' => null,
                ]);

            Log::info("ProcessZoomSettingOutboxEventJob: Outbox event #{$eventId} synchronized to Elasticsearch index {$indexName}.");
        } catch (Throwable $e) {
            Log::error("ProcessZoomSettingOutboxEventJob: Event #{$eventId} failed: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $attempts = (int) $event->attempts + 1;
            $newStatus = $attempts >= $this->tries ? 'FAILED' : 'PENDING';
            $nextAvailable = Carbon::now()->addSeconds($this->backoff * $attempts);

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $newStatus,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => $nextAvailable,
                    'locked_at' => null,
                ]);

            throw $e;
        }
    }

    /**
     * Provision the Elasticsearch index with schema mappings.
     */
    protected function createIndexWithSchema(ElasticService $elasticService, string $indexName, SuperAdminZoomSettingReadService $readService): void
    {
        try {
            $elasticService->getClient()->indices()->create([
                'index' => $indexName,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.requests.cache.enable' => true,
                    ],
                    'mappings' => $readService->getIndexMapping(),
                ],
            ]);
            Log::info("ProcessZoomSettingOutboxEventJob: Created Elasticsearch index {$indexName} with complete mappings.");
        } catch (Throwable $e) {
            // Check if already created concurrently
            if (! $elasticService->indexExists($indexName)) {
                throw $e;
            }
        }
    }
}
