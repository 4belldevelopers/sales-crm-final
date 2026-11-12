<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\FeatureCircuitBreaker;
use App\Services\SuperAdmin\SuperAdminFeatureCacheManager;
use App\Services\SuperAdmin\SuperAdminFeatureReadService;
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
 * Class ProcessFeatureOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Super Admin Platform Features & Expiration Rules subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_features_list),
 * atomic cache invalidation in Redis/Dragonfly, and status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessFeatureOutboxEventJob implements ShouldQueue
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
     * Resolve target Elasticsearch index name for feature policies.
     */
    protected function getFeaturesListIndexName(): string
    {
        if (defined('ELASTIC_FEATURES_LIST')) {
            return ELASTIC_FEATURES_LIST;
        }

        return (string) config('constants.ELASTIC_FEATURES_LIST', 'local_features_list');
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
            ->where('aggregate_type', 'Feature')
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
            $indexName = $this->getFeaturesListIndexName();

            // Ensure index exists with complete schema mapping
            if (! $elasticService->indexExists($indexName)) {
                $this->createFeaturesListIndex($elasticService, $indexName);
            }

            $readService = app(SuperAdminFeatureReadService::class);
            $featureId = (int) $event->aggregate_id;

            // Fetch live authoritative record from MySQL
            $featureRow = DB::table('features')
                ->where('id', $featureId)
                ->first();

            if ($featureRow) {
                $doc = $readService->buildFeatureDocument($featureRow);

                $elasticService->getClient()->index([
                    'index' => $indexName,
                    'id' => (string) $featureRow->id,
                    'body' => $doc,
                    'refresh' => 'wait_for',
                ]);

                FeatureCircuitBreaker::recordSuccess();
            }

            // Invalidate L2 caches
            SuperAdminFeatureCacheManager::flushCaches();

            // Mark outbox event as completed
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => Carbon::now(),
                    'last_error' => null,
                ]);

            Log::info("ProcessFeatureOutboxEventJob: Event #{$eventId} synchronized to Elasticsearch successfully.");
        } catch (Throwable $e) {
            Log::error("ProcessFeatureOutboxEventJob: Event #{$eventId} failed: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            FeatureCircuitBreaker::recordFailure($e);

            $attempts = (int) $event->attempts + 1;
            $newStatus = $attempts >= $this->tries ? 'FAILED' : 'PENDING';
            $nextAvailable = Carbon::now()->addSeconds($this->backoff * $attempts);

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $newStatus,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => $nextAvailable,
                ]);

            if ($newStatus === 'FAILED') {
                throw $e;
            }
        }
    }

    /**
     * Provision local_features_list index mapping.
     */
    public function createFeaturesListIndex(ElasticService $elasticService, string $indexName): void
    {
        $params = [
            'index' => $indexName,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'refresh_interval' => '1s',
                ],
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'integer'],
                        'title' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => ['type' => 'keyword'],
                            ],
                        ],
                        'days' => ['type' => 'integer'],
                        'status' => ['type' => 'integer'],
                        'category' => ['type' => 'keyword'],
                        'scope_badge' => ['type' => 'keyword'],
                        'description' => ['type' => 'text'],
                        'governance_note' => ['type' => 'text'],
                        'is_active' => ['type' => 'boolean'],
                        'is_immediate' => ['type' => 'boolean'],
                        'created_at' => ['type' => 'date'],
                        'updated_at' => ['type' => 'date'],
                    ],
                ],
            ],
        ];

        $elasticService->getClient()->indices()->create($params);
        Log::info("Created Elasticsearch index: {$indexName}");
    }
}
