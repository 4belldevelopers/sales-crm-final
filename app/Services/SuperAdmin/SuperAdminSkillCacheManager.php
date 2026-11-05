<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;

/**
 * Class SuperAdminSkillCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin Skills subsystem. Implements generation-based versioning for O(1)
 * atomic invalidation, single-flight caching, and sub-millisecond read offloading.
 *
 * Scoped strictly to Super Admin (tenant_id = 0).
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminSkillCacheManager
{
    public const VERSION_KEY = 'superadmin:skills:version';

    public const KPI_CACHE_KEY = 'superadmin:skills:kpis';

    public const KPI_TTL = 300; // 5 minutes

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

        return "superadmin:skills:list:v{$version}:{$hash}";
    }

    /**
     * Generate cache key for a single skill item.
     */
    public static function getItemKey(string|int $id): string
    {
        $version = self::getVersion();

        return "superadmin:skills:item:v{$version}:{$id}";
    }

    /**
     * Invalidate all skill listings and KPI caches atomically in O(1) time.
     */
    public static function flushCaches(): void
    {
        $version = self::getVersion();
        Cache::forget(self::KPI_CACHE_KEY.":v{$version}");
        Cache::forget(self::KPI_CACHE_KEY);
        self::incrementVersion();
    }

    /**
     * Cache or retrieve KPI aggregations.
     */
    public static function rememberKpi(\Closure $callback): array|int
    {
        $version = self::getVersion();
        $key = self::KPI_CACHE_KEY.":v{$version}";

        return Cache::remember($key, self::KPI_TTL, $callback);
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
     * Cache stampede protection wrapper using atomic lock.
     */
    public static function rememberWithStampedeProtection(string $key, int $ttl, \Closure $callback): mixed
    {
        $val = Cache::get($key);
        if ($val !== null) {
            return $val;
        }

        $lock = Cache::lock("lock:{$key}", 10);

        try {
            if ($lock->get()) {
                $fresh = $callback();
                Cache::put($key, $fresh, $ttl);

                return $fresh;
            }

            // Fallback for competing threads: wait briefly and retry cache
            usleep(50000); // 50ms

            return Cache::get($key) ?? $callback();
        } finally {
            optional($lock)->release();
        }
    }
}
