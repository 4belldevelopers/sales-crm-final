<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminThemeSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminThemeSettingReadService;
use App\Services\SuperAdmin\ThemeSettingCircuitBreaker;
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
 * Class ProcessThemeSettingOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Super Admin Theme Settings subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_theme_settings),
 * atomic cache invalidation in Redis/Dragonfly, and status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessThemeSettingOutboxEventJob implements ShouldQueue
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
     * Resolve target Elasticsearch index name for theme settings.
     */
    protected function getThemeSettingsIndexName(): string
    {
        if (defined('ELASTIC_THEME_SETTINGS')) {
            return ELASTIC_THEME_SETTINGS;
        }

        return (string) config('constants.ELASTIC_THEME_SETTINGS', 'local_theme_settings');
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
            ->where('aggregate_type', 'ThemeSetting')
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
            $indexName = $this->getThemeSettingsIndexName();

            // Ensure index exists with complete schema mapping
            if (! $elasticService->indexExists($indexName)) {
                $this->createThemeSettingsIndex($elasticService, $indexName);
            }

            $readService = app(SuperAdminThemeSettingReadService::class);

            // Fetch live authoritative record from MySQL
            $themeRow = DB::table('theme_settings')
                ->whereNull('company_id')
                ->first();

            if ($themeRow) {
                $doc = $readService->buildThemeSettingDocument($themeRow);

                $elasticService->getClient()->index([
                    'index' => $indexName,
                    'id' => '1',
                    'body' => $doc,
                    'refresh' => 'wait_for',
                ]);

                ThemeSettingCircuitBreaker::recordSuccess();
            }

            // Invalidate L2 caches
            SuperAdminThemeSettingCacheManager::flushCaches();

            // Mark outbox event as completed
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => Carbon::now(),
                    'last_error' => null,
                ]);

            Log::info("ProcessThemeSettingOutboxEventJob: Event #{$eventId} synchronized to Elasticsearch successfully.");
        } catch (Throwable $e) {
            Log::error("ProcessThemeSettingOutboxEventJob: Event #{$eventId} failed: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            ThemeSettingCircuitBreaker::recordFailure($e);

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
     * Provision local_theme_settings index mapping.
     */
    public function createThemeSettingsIndex(ElasticService $elasticService, string $indexName): void
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
                        'company_id' => ['type' => 'integer'],
                        'primary_color' => ['type' => 'keyword'],
                        'admin_custom_css' => ['type' => 'text', 'index' => false],
                        'front_custom_css' => ['type' => 'text', 'index' => false],
                        'home_background_image' => ['type' => 'keyword'],
                        'background_image_url' => ['type' => 'keyword'],
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
