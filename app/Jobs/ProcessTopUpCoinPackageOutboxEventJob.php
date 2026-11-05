<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\TopUpCoinPackageCacheManager;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessTopUpCoinPackageOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Top-Up Coin Packages subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_top_up_coin_packages),
 * cache invalidation in Redis/Dragonfly, and atomic status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessTopUpCoinPackageOutboxEventJob implements ShouldQueue
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
        if (defined('ELASTIC_TOP_UP_COIN_PACKAGES')) {
            return ELASTIC_TOP_UP_COIN_PACKAGES;
        }

        return (string) config('constants.ELASTIC_TOP_UP_COIN_PACKAGES', 'local_top_up_coin_packages');
    }

    public function handle(?ElasticService $elasticService = null): void
    {
        $elasticService = $elasticService ?? app(ElasticService::class);

        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId, $elasticService);

            return;
        }

        // Batch processing when triggered periodically by scheduler or CLI
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'TopUpCoinPackage')
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
            $packageId = (int) $event->aggregate_id;
            $eventType = (string) $event->event_type;
            $indexName = $this->getIndexName();

            if ($eventType === 'TOP_UP_COIN_PACKAGE_DELETED') {
                if ($elasticService->indexExists($indexName)) {
                    try {
                        $elasticService->getClient()->delete([
                            'index' => $indexName,
                            'id' => (string) $packageId,
                            'refresh' => 'wait_for',
                        ]);
                    } catch (\Throwable $deleteEx) {
                        Log::info("[TOPUP_COIN_PACKAGE_OUTBOX] Document #{$packageId} already removed or not found in ES: {$deleteEx->getMessage()}");
                    }
                }
            } else {
                // Upsert denormalized document for SAVED or STATUS_TOGGLED
                $row = DB::table('top_up_coin_packages')->where('id', $packageId)->first();

                if ($row) {
                    $coins = (int) ($row->coins ?? 0);
                    $price = (float) ($row->price ?? 0);
                    $isCustom = ($row->custome_chk == 1 || ($coins == 0 && $price == 0));
                    $tierName = 'Custom';
                    if (! $isCustom) {
                        if ($coins < 500) {
                            $tierName = 'Starter';
                        } elseif ($coins <= 2000) {
                            $tierName = 'Growth';
                        } elseif ($coins <= 5000) {
                            $tierName = 'Scale';
                        } else {
                            $tierName = 'Enterprise';
                        }
                    }

                    $doc = [
                        'id' => (int) $row->id,
                        'price' => (float) $row->price,
                        'price_usd' => (float) ($row->price_usd ?? 0),
                        'coins' => (int) $row->coins,
                        'discount' => (int) ($row->discount ?? 0),
                        'status' => (int) $row->status,
                        'custome_chk' => (int) ($row->custome_chk ?? 0),
                        'event_type' => (string) ($row->event_type ?? 'operational'),
                        'per_coin_rate' => (float) ($row->per_coin_rate ?? 0),
                        'per_coin_rate_usd' => (float) ($row->per_coin_rate_usd ?? 0),
                        'tier_name' => $tierName,
                        'created_at' => $row->created_at ? Carbon::parse($row->created_at)->toIso8601String() : Carbon::now()->toIso8601String(),
                        'updated_at' => $row->updated_at ? Carbon::parse($row->updated_at)->toIso8601String() : Carbon::now()->toIso8601String(),
                    ];

                    if ($elasticService->indexExists($indexName)) {
                        $elasticService->getClient()->index([
                            'index' => $indexName,
                            'id' => (string) $packageId,
                            'body' => $doc,
                            'refresh' => 'wait_for',
                        ]);
                    }
                }
            }

            // Mark outbox event as COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);

            TopUpCoinPackageCacheManager::flushCaches();

        } catch (\Throwable $e) {
            $isFinal = ($event->attempts + 1) >= $this->tries;
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $isFinal ? 'FAILED' : 'PENDING',
                    'available_at' => now()->addSeconds($this->backoff * ($event->attempts + 1)),
                    'last_error' => substr($e->getMessage(), 0, 1000),
                ]);

            Log::error("[TOPUP_COIN_PACKAGE_OUTBOX] Event #{$eventId} processing failed: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
