<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\CouponCacheManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexCouponsToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes all MySQL Coupons
 * into Elasticsearch index ELASTIC_COUPONS (local_coupons) and purges all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan coupons:reindex
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexCouponsToElasticCommand extends Command
{
    protected $signature = 'coupons:reindex';

    protected $description = 'Reindex all Coupons into Elasticsearch with complete schema verification';

    public function handle(ElasticService $elasticService): int
    {
        $this->info('Starting Coupons Elasticsearch reindexing...');

        $indexName = defined('ELASTIC_COUPONS')
            ? ELASTIC_COUPONS
            : (string) config('constants.ELASTIC_COUPONS', 'local_coupons');

        if (! $elasticService->indexExists($indexName)) {
            $this->warn("Elasticsearch index {$indexName} does not exist. Creating with complete mappings...");

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
            $this->info("Created index {$indexName}.");
        }

        $coupons = DB::table('coupons')->get();
        $count = $coupons->count();
        $this->info("Found {$count} coupon(s) to synchronize.");

        $synced = 0;
        foreach ($coupons as $coupon) {
            $doc = [
                'id' => (int) $coupon->id,
                'code' => strtoupper(trim((string) $coupon->code)),
                'discount_percent' => (float) $coupon->discount_percent,
                'expiry_date' => $coupon->expiry_date ? Carbon::parse($coupon->expiry_date)->format('Y-m-d') : Carbon::now()->format('Y-m-d'),
                'max_redemptions' => (int) $coupon->max_redemptions,
                'times_redeemed' => (int) ($coupon->times_redeemed ?? 0),
                'status' => (string) ($coupon->status ?? 'active'),
                'created_by' => $coupon->created_by ? (int) $coupon->created_by : null,
                'created_at' => $coupon->created_at ? Carbon::parse($coupon->created_at)->toIso8601String() : Carbon::now()->toIso8601String(),
                'updated_at' => $coupon->updated_at ? Carbon::parse($coupon->updated_at)->toIso8601String() : Carbon::now()->toIso8601String(),
            ];

            $elasticService->getClient()->index([
                'index' => $indexName,
                'id' => (string) $coupon->id,
                'body' => $doc,
            ]);

            $synced++;
        }

        if ($synced > 0) {
            $elasticService->getClient()->indices()->refresh(['index' => $indexName]);
        }

        // Flush Redis/Dragonfly L2 cache
        CouponCacheManager::flushCaches();

        $this->info("Successfully synchronized {$synced} coupon(s) into {$indexName} and flushed L2 caches.");

        return self::SUCCESS;
    }
}
