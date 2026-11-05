<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\CoinPackageCacheManager;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessCoinPackageOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Coin Packages subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_coin_packages),
 * cache invalidation in Redis/Dragonfly, and atomic status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessCoinPackageOutboxEventJob implements ShouldQueue
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

    public function handle(?ElasticService $elasticService = null): void
    {
        $elasticService = $elasticService ?? app(ElasticService::class);

        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId, $elasticService);

            return;
        }

        // Batch processing when triggered periodically by scheduler or CLI
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CoinPackage')
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

            if ($eventType === 'COIN_PACKAGE_DELETED') {
                if (defined('ELASTIC_COIN_PACKAGES') && $elasticService->indexExists(ELASTIC_COIN_PACKAGES)) {
                    try {
                        $elasticService->getClient()->delete([
                            'index' => ELASTIC_COIN_PACKAGES,
                            'id' => (string) $packageId,
                            'refresh' => 'wait_for',
                        ]);
                    } catch (\Throwable $deleteEx) {
                        Log::info("[COIN_PACKAGE_OUTBOX] Document #{$packageId} already removed or not found in ES: {$deleteEx->getMessage()}");
                    }
                }
            } else {
                // Upsert denormalized document for SAVED or STATUS_TOGGLED
                $row = DB::table('coin_packages')
                    ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
                    ->where('coin_packages.id', $packageId)
                    ->select(
                        'coin_packages.*',
                        'coin_package_types.title',
                        'coin_package_types.days as type_default_days',
                        'coin_package_types.event_type as type_event_type'
                    )
                    ->first();

                if ($row) {
                    $pointsRows = DB::table('coin_package_points')
                        ->where('coin_package_id', $packageId)
                        ->orderBy('id', 'asc')
                        ->get();

                    $pointsArray = [];
                    foreach ($pointsRows as $pt) {
                        $pointsArray[] = [
                            'id' => (int) $pt->id,
                            'coin_package_id' => (int) $pt->coin_package_id,
                            'title' => (string) $pt->title,
                            'status' => (int) $pt->status,
                        ];
                    }

                    $doc = [
                        'id' => (int) $row->id,
                        'coin_package_type_id' => (int) $row->coin_package_type_id,
                        'title' => (string) $row->title,
                        'price' => (float) $row->price,
                        'price_usd' => (float) ($row->price_usd ?? 0),
                        'total_price_monthly' => (float) $row->total_price_monthly,
                        'total_price_days' => (float) $row->total_price_days,
                        'total_price_rate' => (float) $row->total_price_rate,
                        'total_price_usd_monthly' => (float) $row->total_price_usd_monthly,
                        'total_price_usd_days' => (float) $row->total_price_usd_days,
                        'total_price_usd_rate' => (float) $row->total_price_usd_rate,
                        'visibility_show' => (int) $row->visibility_show,
                        'coins' => (float) $row->coins,
                        'coins_auto' => (float) ($row->coins_auto ?? 0),
                        'discount' => (int) $row->discount,
                        'days' => (int) $row->days,
                        'status' => (int) $row->status,
                        'custome_pck' => (int) $row->custome_pck,
                        'event_type' => (string) $row->event_type,
                        'type_default_days' => (int) ($row->type_default_days ?? 0),
                        'type_event_type' => (string) ($row->type_event_type ?? $row->event_type),
                        'points' => $pointsArray,
                        'created_at' => $row->created_at ? Carbon::parse($row->created_at)->toIso8601String() : null,
                        'updated_at' => $row->updated_at ? Carbon::parse($row->updated_at)->toIso8601String() : null,
                    ];

                    if (defined('ELASTIC_COIN_PACKAGES') && $elasticService->indexExists(ELASTIC_COIN_PACKAGES)) {
                        $elasticService->getClient()->update([
                            'index' => ELASTIC_COIN_PACKAGES,
                            'id' => (string) $row->id,
                            'retry_on_conflict' => 5,
                            'refresh' => 'wait_for',
                            'body' => [
                                'doc' => $doc,
                                'doc_as_upsert' => true,
                            ],
                        ]);
                    }
                }
            }

            // Invalidate Redis / Dragonfly caches
            CoinPackageCacheManager::flushCaches();

            // Mark event as COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);

        } catch (\Throwable $e) {
            Log::error("[COIN_PACKAGE_OUTBOX] Failed processing event #{$eventId}: {$e->getMessage()}", [
                'trace' => $e->getTraceAsString(),
            ]);

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => ($event->attempts >= $this->tries) ? 'FAILED' : 'PENDING',
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => now()->addSeconds($this->backoff * $event->attempts),
                ]);

            throw $e;
        }
    }
}
