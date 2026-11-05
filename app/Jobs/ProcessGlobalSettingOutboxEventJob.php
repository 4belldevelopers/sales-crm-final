<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminGlobalSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminGlobalSettingReadService;
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
 * Class ProcessGlobalSettingOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Super Admin Global Settings subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch
 * (local_global_settings & local_sold_by_address), atomic cache invalidation in Redis/Dragonfly,
 * and status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessGlobalSettingOutboxEventJob implements ShouldQueue
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
     * Resolve target Elasticsearch index name for global settings.
     */
    protected function getGlobalSettingsIndexName(): string
    {
        if (defined('ELASTIC_GLOBAL_SETTINGS')) {
            return ELASTIC_GLOBAL_SETTINGS;
        }

        return (string) config('constants.ELASTIC_GLOBAL_SETTINGS', 'local_global_settings');
    }

    /**
     * Resolve target Elasticsearch index name for sold by address.
     */
    protected function getSoldByAddressIndexName(): string
    {
        if (defined('ELASTIC_SOLD_BY_ADDRESS')) {
            return ELASTIC_SOLD_BY_ADDRESS;
        }

        return (string) config('constants.ELASTIC_SOLD_BY_ADDRESS', 'local_sold_by_address');
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
            ->where('aggregate_type', 'GlobalSetting')
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
            $gsIndex = $this->getGlobalSettingsIndexName();
            $sbaIndex = $this->getSoldByAddressIndexName();

            // Ensure indices exist with schema
            if (! $elasticService->indexExists($gsIndex)) {
                $this->createGlobalSettingsIndex($elasticService, $gsIndex);
            }
            if (! $elasticService->indexExists($sbaIndex)) {
                $this->createSoldByAddressIndex($elasticService, $sbaIndex);
            }

            $readService = app(SuperAdminGlobalSettingReadService::class);

            // Fetch live records from MySQL for authoritative projection
            $settingRow = DB::table('global_settings')->where('id', 1)->first()
                ?: DB::table('global_settings')->first();
            $soldByRow = DB::table('sold_by_address')->where('id', 1)->first()
                ?: DB::table('sold_by_address')->first();

            if ($settingRow) {
                $currencyRow = DB::table('currencies')->where('id', $settingRow->currency_id)->first();
                $gsDoc = $readService->buildGlobalSettingDocument($settingRow, $currencyRow);

                $elasticService->getClient()->index([
                    'index' => $gsIndex,
                    'id' => '1',
                    'body' => $gsDoc,
                    'refresh' => 'wait_for',
                ]);
            }

            if ($soldByRow) {
                $sbaDoc = $readService->buildSoldByAddressDocument($soldByRow);

                $elasticService->getClient()->index([
                    'index' => $sbaIndex,
                    'id' => '1',
                    'body' => $sbaDoc,
                    'refresh' => 'wait_for',
                ]);
            }

            // Invalidate L2 caches
            SuperAdminGlobalSettingCacheManager::flushCaches();

            // Mark outbox event as completed
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => Carbon::now(),
                    'last_error' => null,
                ]);

            Log::info("ProcessGlobalSettingOutboxEventJob: Event #{$eventId} synchronized to Elasticsearch successfully.");
        } catch (Throwable $e) {
            Log::error("ProcessGlobalSettingOutboxEventJob: Event #{$eventId} failed: ".$e->getMessage(), [
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
                ]);

            if ($newStatus === 'FAILED') {
                throw $e;
            }
        }
    }

    /**
     * Provision local_global_settings index mapping.
     */
    public function createGlobalSettingsIndex(ElasticService $elasticService, string $indexName): void
    {
        $params = [
            'index' => $indexName,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                    'analysis' => [
                        'analyzer' => [
                            'case_insensitive_sort' => [
                                'tokenizer' => 'keyword',
                                'filter' => ['lowercase'],
                            ],
                        ],
                    ],
                ],
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'integer'],
                        'currency_id' => ['type' => 'integer'],
                        'company_name' => [
                            'type' => 'text',
                            'fields' => ['keyword' => ['type' => 'keyword', 'ignore_above' => 256]],
                        ],
                        'company_email' => ['type' => 'keyword'],
                        'company_phone' => ['type' => 'keyword'],
                        'logo' => ['type' => 'keyword'],
                        'logo_url' => ['type' => 'keyword'],
                        'address' => ['type' => 'text'],
                        'website' => ['type' => 'keyword'],
                        'locale' => ['type' => 'keyword'],
                        'google_recaptcha_key' => ['type' => 'keyword'],
                        'system_update' => ['type' => 'integer'],
                        'show_review_modal' => ['type' => 'integer'],
                        'delete_account_in' => ['type' => 'integer'],
                        'delete_account_hour_day' => ['type' => 'keyword'],
                        'currency' => [
                            'properties' => [
                                'id' => ['type' => 'integer'],
                                'currency_name' => ['type' => 'text'],
                                'currency_symbol' => ['type' => 'keyword'],
                                'currency_code' => ['type' => 'keyword'],
                            ],
                        ],
                        'created_at' => ['type' => 'date', 'format' => 'strict_date_optional_time||epoch_millis'],
                        'updated_at' => ['type' => 'date', 'format' => 'strict_date_optional_time||epoch_millis'],
                    ],
                ],
            ],
        ];

        $elasticService->getClient()->indices()->create($params);
    }

    /**
     * Provision local_sold_by_address index mapping if missing.
     */
    public function createSoldByAddressIndex(ElasticService $elasticService, string $indexName): void
    {
        $params = [
            'index' => $indexName,
            'body' => [
                'settings' => [
                    'number_of_shards' => 1,
                    'number_of_replicas' => 0,
                ],
                'mappings' => [
                    'properties' => [
                        'id' => ['type' => 'long'],
                        'company_id' => ['type' => 'long'],
                        'country_id' => ['type' => 'long'],
                        'first_name' => ['type' => 'text'],
                        'last_name' => ['type' => 'text'],
                        'phone' => ['type' => 'text'],
                        'building' => ['type' => 'text'],
                        'street' => ['type' => 'text'],
                        'state_id' => ['type' => 'long'],
                        'city' => ['type' => 'long'],
                        'postal_code' => ['type' => 'text'],
                        'organization' => ['type' => 'text'],
                        'gstin' => ['type' => 'text'],
                        'pan_no' => ['type' => 'text'],
                        'updated_at' => ['type' => 'date', 'format' => 'strict_date_optional_time||epoch_millis'],
                    ],
                ],
            ],
        ];

        $elasticService->getClient()->indices()->create($params);
    }
}
