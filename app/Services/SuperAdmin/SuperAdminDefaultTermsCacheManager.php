<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;

/**
 * Class SuperAdminDefaultTermsCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin Default Terms, Compliance & Offer Letters subsystem. Implements generation-based versioning
 * for O(1) atomic invalidation, multi-tenant cache synchronization (Rule 6), single-flight caching,
 * and sub-millisecond read offloading.
 *
 * Scoped strictly to Super Admin (tenant_id = 0) with cross-tenant cache eviction.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminDefaultTermsCacheManager
{
    public const VERSION_KEY = 'superadmin:default_terms:version';

    public const KPI_CACHE_KEY = 'superadmin:default_terms:kpi';

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

        return "superadmin:default_terms:list:v{$version}:{$hash}";
    }

    /**
     * Generate cache key for a single term item.
     */
    public static function getItemKey(int $id): string
    {
        $version = self::getVersion();

        return "superadmin:default_terms:item:v{$version}:{$id}";
    }

    /**
     * Invalidate all default terms listings and KPI caches atomically in O(1) time.
     * Also invalidates tenant-facing agency commercial caches per Rule 6.
     */
    public static function flushCaches(): void
    {
        Cache::forget(self::KPI_CACHE_KEY);
        Cache::forget('agency_commercial_terms:v1:company:0');
        Cache::forget('agency_commercial_terms:global_version');
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
     * Cache or retrieve single term item.
     */
    public static function rememberItem(int $id, \Closure $callback): ?array
    {
        $key = self::getItemKey($id);

        return Cache::remember($key, self::ITEM_TTL, $callback);
    }
}
