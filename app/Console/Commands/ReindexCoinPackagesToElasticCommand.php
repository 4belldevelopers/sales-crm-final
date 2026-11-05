<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\CoinPackageCacheManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexCoinPackagesToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes all MySQL Coin Packages,
 * Coin Package Types, and Feature Points into Elasticsearch index ELASTIC_COIN_PACKAGES
 * (local_coin_packages) and purges all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan coin-packages:reindex
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexCoinPackagesToElasticCommand extends Command
{
    protected $signature = 'coin-packages:reindex';

    protected $description = 'Reindex all Coin Packages into Elasticsearch with denormalized points and metadata';

    public function handle(ElasticService $elasticService): int
    {
        $this->info('Starting Coin Packages Elasticsearch reindexing...');

        if (! defined('ELASTIC_COIN_PACKAGES')) {
            $this->error('ELASTIC_COIN_PACKAGES constant is not defined.');

            return self::FAILURE;
        }

        if (! $elasticService->indexExists(ELASTIC_COIN_PACKAGES)) {
            $this->warn('Elasticsearch index '.ELASTIC_COIN_PACKAGES.' does not exist. Creating with complete mappings...');

            $elasticService->getClient()->indices()->create([
                'index' => ELASTIC_COIN_PACKAGES,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.requests.cache.enable' => true,
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'coin_package_type_id' => ['type' => 'long'],
                            'title' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'price' => ['type' => 'double'],
                            'price_usd' => ['type' => 'double'],
                            'total_price_monthly' => ['type' => 'double'],
                            'total_price_days' => ['type' => 'double'],
                            'total_price_rate' => ['type' => 'double'],
                            'total_price_usd_monthly' => ['type' => 'double'],
                            'total_price_usd_days' => ['type' => 'double'],
                            'total_price_usd_rate' => ['type' => 'double'],
                            'visibility_show' => ['type' => 'integer'],
                            'coins' => ['type' => 'double'],
                            'coins_auto' => ['type' => 'double'],
                            'discount' => ['type' => 'integer'],
                            'days' => ['type' => 'integer'],
                            'status' => ['type' => 'integer'],
                            'custome_pck' => ['type' => 'integer'],
                            'event_type' => ['type' => 'keyword'],
                            'type_default_days' => ['type' => 'integer'],
                            'type_event_type' => ['type' => 'keyword'],
                            'points' => [
                                'type' => 'object',
                                'properties' => [
                                    'id' => ['type' => 'long'],
                                    'coin_package_id' => ['type' => 'long'],
                                    'title' => ['type' => 'text'],
                                    'status' => ['type' => 'integer'],
                                ],
                            ],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ]);
        }

        $packages = DB::table('coin_packages')
            ->join('coin_package_types', 'coin_package_types.id', '=', 'coin_packages.coin_package_type_id')
            ->select(
                'coin_packages.*',
                'coin_package_types.title',
                'coin_package_types.days as type_default_days',
                'coin_package_types.event_type as type_event_type'
            )
            ->get();

        $count = $packages->count();
        $this->info("Found {$count} coin package(s) to synchronize.");

        $packageIds = $packages->pluck('id')->toArray();
        $pointsMap = [];
        if (! empty($packageIds)) {
            $pointsRows = DB::table('coin_package_points')
                ->whereIn('coin_package_id', $packageIds)
                ->orderBy('id', 'asc')
                ->get();
            foreach ($pointsRows as $pt) {
                $pointsMap[$pt->coin_package_id][] = [
                    'id' => (int) $pt->id,
                    'coin_package_id' => (int) $pt->coin_package_id,
                    'title' => (string) $pt->title,
                    'status' => (int) $pt->status,
                ];
            }
        }

        $synced = 0;
        foreach ($packages as $pkg) {
            $doc = [
                'id' => (int) $pkg->id,
                'coin_package_type_id' => (int) $pkg->coin_package_type_id,
                'title' => (string) $pkg->title,
                'price' => (float) $pkg->price,
                'price_usd' => (float) ($pkg->price_usd ?? 0),
                'total_price_monthly' => (float) $pkg->total_price_monthly,
                'total_price_days' => (float) $pkg->total_price_days,
                'total_price_rate' => (float) $pkg->total_price_rate,
                'total_price_usd_monthly' => (float) $pkg->total_price_usd_monthly,
                'total_price_usd_days' => (float) $pkg->total_price_usd_days,
                'total_price_usd_rate' => (float) $pkg->total_price_usd_rate,
                'visibility_show' => (int) $pkg->visibility_show,
                'coins' => (float) $pkg->coins,
                'coins_auto' => (float) ($pkg->coins_auto ?? 0),
                'discount' => (int) $pkg->discount,
                'days' => (int) $pkg->days,
                'status' => (int) $pkg->status,
                'custome_pck' => (int) $pkg->custome_pck,
                'event_type' => (string) $pkg->event_type,
                'type_default_days' => (int) ($pkg->type_default_days ?? 0),
                'type_event_type' => (string) ($pkg->type_event_type ?? $pkg->event_type),
                'points' => $pointsMap[$pkg->id] ?? [],
                'created_at' => $pkg->created_at ? Carbon::parse($pkg->created_at)->toIso8601String() : null,
                'updated_at' => $pkg->updated_at ? Carbon::parse($pkg->updated_at)->toIso8601String() : null,
            ];

            $elasticService->getClient()->update([
                'index' => ELASTIC_COIN_PACKAGES,
                'id' => (string) $pkg->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => $doc,
                    'doc_as_upsert' => true,
                ],
            ]);
            $synced++;
        }

        CoinPackageCacheManager::flushCaches();

        $this->info("Successfully synchronized {$synced} coin package(s) to Elasticsearch index '".ELASTIC_COIN_PACKAGES."'.");
        $this->info('Redis/Dragonfly caches flushed.');

        return self::SUCCESS;
    }
}
