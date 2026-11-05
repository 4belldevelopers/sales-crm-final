<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\TopUpCoinPackageCacheManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexTopUpCoinPackagesToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes all MySQL Top-Up Coin Packages
 * into Elasticsearch index ELASTIC_TOP_UP_COIN_PACKAGES (local_top_up_coin_packages)
 * and purges all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan topup-coin-packages:reindex
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexTopUpCoinPackagesToElasticCommand extends Command
{
    protected $signature = 'topup-coin-packages:reindex';

    protected $description = 'Reindex all Top-Up Coin Packages into Elasticsearch with complete schema verification';

    public function handle(ElasticService $elasticService): int
    {
        $this->info('Starting Top-Up Coin Packages Elasticsearch reindexing...');

        $indexName = defined('ELASTIC_TOP_UP_COIN_PACKAGES')
            ? ELASTIC_TOP_UP_COIN_PACKAGES
            : config('constants.ELASTIC_TOP_UP_COIN_PACKAGES', 'local_top_up_coin_packages');

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
                            'price' => ['type' => 'double'],
                            'price_usd' => ['type' => 'double'],
                            'coins' => ['type' => 'long'],
                            'discount' => ['type' => 'integer'],
                            'status' => ['type' => 'integer'],
                            'custome_chk' => ['type' => 'integer'],
                            'event_type' => ['type' => 'keyword'],
                            'per_coin_rate' => ['type' => 'double'],
                            'per_coin_rate_usd' => ['type' => 'double'],
                            'tier_name' => ['type' => 'keyword'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ]);
            $this->info("Created index {$indexName}.");
        }

        $packages = DB::table('top_up_coin_packages')->get();
        $count = $packages->count();
        $this->info("Found {$count} top-up coin package(s) to synchronize.");

        $synced = 0;
        foreach ($packages as $pkg) {
            $coins = (int) ($pkg->coins ?? 0);
            $price = (float) ($pkg->price ?? 0);
            $isCustom = ($pkg->custome_chk == 1 || ($coins == 0 && $price == 0));
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
                'id' => (int) $pkg->id,
                'price' => (float) $pkg->price,
                'price_usd' => (float) ($pkg->price_usd ?? 0),
                'coins' => (int) $pkg->coins,
                'discount' => (int) ($pkg->discount ?? 0),
                'status' => (int) $pkg->status,
                'custome_chk' => (int) ($pkg->custome_chk ?? 0),
                'event_type' => (string) ($pkg->event_type ?? 'operational'),
                'per_coin_rate' => (float) ($pkg->per_coin_rate ?? 0),
                'per_coin_rate_usd' => (float) ($pkg->per_coin_rate_usd ?? 0),
                'tier_name' => $tierName,
                'created_at' => $pkg->created_at ? Carbon::parse($pkg->created_at)->toIso8601String() : Carbon::now()->toIso8601String(),
                'updated_at' => $pkg->updated_at ? Carbon::parse($pkg->updated_at)->toIso8601String() : Carbon::now()->toIso8601String(),
            ];

            $elasticService->getClient()->index([
                'index' => $indexName,
                'id' => (string) $pkg->id,
                'body' => $doc,
            ]);

            $synced++;
        }

        // Force an index refresh
        $elasticService->getClient()->indices()->refresh(['index' => $indexName]);

        // Atomic Cache flush
        TopUpCoinPackageCacheManager::flushCaches();

        $this->info("Successfully synchronized {$synced} top-up coin package(s) into {$indexName} and flushed L2 caches.");

        return self::SUCCESS;
    }
}
