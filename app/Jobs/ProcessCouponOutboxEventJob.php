<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\CouponCacheManager;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessCouponOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Coupons subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_coupons),
 * cache invalidation in Redis/Dragonfly, and atomic status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessCouponOutboxEventJob implements ShouldQueue
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
            ->where('aggregate_type', 'Coupon')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit(50)
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
            $couponId = (int) $event->aggregate_id;
            $eventType = (string) $event->event_type;

            $indexName = defined('ELASTIC_COUPONS')
                ? ELASTIC_COUPONS
                : (string) config('constants.ELASTIC_COUPONS', 'local_coupons');

            if ($eventType === 'COUPON_DELETED') {
                if ($elasticService->indexExists($indexName)) {
                    try {
                        $elasticService->getClient()->delete([
                            'index' => $indexName,
                            'id' => (string) $couponId,
                            'refresh' => 'wait_for',
                        ]);
                    } catch (\Throwable $deleteEx) {
                        Log::info("[COUPON_OUTBOX] Document #{$couponId} already removed or not found in ES: {$deleteEx->getMessage()}");
                    }
                }
                $payload = json_decode($event->payload, true) ?: [];
                $code = $payload['code'] ?? null;
                CouponCacheManager::flushCaches($code);
            } else {
                // Upsert document into Elasticsearch
                $row = DB::table('coupons')->where('id', $couponId)->first();

                if ($row) {
                    if (! $elasticService->indexExists($indexName)) {
                        $elasticService->getClient()->indices()->create([
                            'index' => $indexName,
                            'body' => [
                                'settings' => [
                                    'number_of_shards' => 1,
                                    'number_of_replicas' => 0,
                                    'index.requests.cache.enable' => true,
                                ],
                                'mappings' => [
                                    'properties' => [
                                        'id' => ['type' => 'long'],
                                        'code' => [
                                            'type' => 'text',
                                            'fields' => [
                                                'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                            ],
                                        ],
                                        'discount_percent' => ['type' => 'double'],
                                        'expiry_date' => [
                                            'type' => 'date',
                                            'format' => 'yyyy-MM-dd||strict_date_optional_time||epoch_millis',
                                        ],
                                        'max_redemptions' => ['type' => 'long'],
                                        'times_redeemed' => ['type' => 'long'],
                                        'status' => ['type' => 'keyword'],
                                        'created_by' => ['type' => 'long'],
                                        'created_at' => ['type' => 'date'],
                                        'updated_at' => ['type' => 'date'],
                                    ],
                                ],
                            ],
                        ]);
                    }

                    $doc = [
                        'id' => (int) $row->id,
                        'code' => strtoupper(trim((string) $row->code)),
                        'discount_percent' => (float) $row->discount_percent,
                        'expiry_date' => $row->expiry_date ? Carbon::parse($row->expiry_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                        'max_redemptions' => (int) $row->max_redemptions,
                        'times_redeemed' => (int) ($row->times_redeemed ?? 0),
                        'status' => (string) ($row->status ?? 'active'),
                        'created_by' => $row->created_by ? (int) $row->created_by : null,
                        'created_at' => $row->created_at ? Carbon::parse($row->created_at)->toIso8601String() : Carbon::now()->toIso8601String(),
                        'updated_at' => $row->updated_at ? Carbon::parse($row->updated_at)->toIso8601String() : Carbon::now()->toIso8601String(),
                    ];

                    $elasticService->getClient()->index([
                        'index' => $indexName,
                        'id' => (string) $row->id,
                        'body' => $doc,
                        'refresh' => 'wait_for',
                    ]);

                    CouponCacheManager::flushCaches($row->code);
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
        } catch (\Throwable $e) {
            Log::error("[COUPON_OUTBOX] Outbox event #{$eventId} execution failed: {$e->getMessage()}");

            $attempts = (int) ($event->attempts ?? 1);
            $newStatus = $attempts >= 3 ? 'DEAD_LETTER' : 'FAILED';

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $newStatus,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                ]);

            if ($newStatus === 'FAILED') {
                throw $e;
            }
        }
    }
}
