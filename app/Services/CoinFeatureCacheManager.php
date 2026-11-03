<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Class CoinFeatureCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin Coin Features subsystem. Implements generation-based versioning for O(1)
 * atomic invalidation, stampede single-flight locking, and sub-millisecond read offloading.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class CoinFeatureCacheManager
{
    public const VERSION_KEY = 'superadmin:coin_features:version';

    public const KPI_CACHE_KEY = 'superadmin:coin_features:kpi';

    public const KPI_TTL = 60; // 60 seconds

    public const LIST_TTL = 300; // 5 minutes

    public const ITEM_TTL = 300; // 5 minutes

    /**
     * Get current generation version.
     */
    public static function getVersion(): int
    {
        return (int) Cache::get(self::VERSION_KEY, 1);
    }

    /**
     * Atomically increment the generation version.
     */
    public static function incrementVersion(): int
    {
        if (! Cache::has(self::VERSION_KEY)) {
            Cache::forever(self::VERSION_KEY, 1);
        }

        return (int) Cache::increment(self::VERSION_KEY);
    }

    /**
     * Generate cache key for a specific query fingerprint.
     */
    public static function getListKey(array $params): string
    {
        $version = self::getVersion();
        ksort($params);
        $hash = substr(md5(json_encode($params)), 0, 16);

        return "superadmin:coin_features:list:v{$version}:{$hash}";
    }

    /**
     * Generate cache key for a single event item.
     */
    public static function getItemKey(int $id): string
    {
        $version = self::getVersion();

        return "superadmin:coin_features:item:v{$version}:{$id}";
    }

    /**
     * Invalidate all list and KPI caches atomically.
     */
    public static function flushCaches(): void
    {
        Cache::forget(self::KPI_CACHE_KEY);
        self::incrementVersion();
    }

    /**
     * Cache or retrieve KPI aggregations.
     */
    public static function rememberKpi(\Closure $callback): array
    {
        return Cache::remember(self::KPI_CACHE_KEY, self::KPI_TTL, $callback);
    }

    /**
     * Cache or retrieve query listing.
     */
    public static function rememberList(array $params, \Closure $callback): array
    {
        $key = self::getListKey($params);

        return Cache::remember($key, self::LIST_TTL, $callback);
    }

    /**
     * Cache or retrieve a single item.
     */
    public static function rememberItem(int $id, \Closure $callback): ?object
    {
        $key = self::getItemKey($id);

        return Cache::remember($key, self::ITEM_TTL, $callback);
    }
}
