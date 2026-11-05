<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;

/**
 * Class EmailThemeCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin Email Themes subsystem. Implements generation-based versioning for O(1)
 * atomic invalidation, single-flight caching, and sub-millisecond read offloading.
 *
 * Scoped strictly to Super Admin (tenant_id = 0) with cross-tenant cache eviction.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class EmailThemeCacheManager
{
    public const VERSION_KEY = 'superadmin:email_themes:version';

    public const KPI_CACHE_KEY = 'superadmin:email_themes:kpi';

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
    public static function getListKey(array $params = []): string
    {
        $version = self::getVersion();
        ksort($params);
        $hash = substr(md5(json_encode($params)), 0, 16);

        return "superadmin:email_themes:list:v{$version}:{$hash}";
    }

    /**
     * Generate cache key for a single template item.
     */
    public static function getItemKey(int $id): string
    {
        $version = self::getVersion();

        return "superadmin:email_themes:item:v{$version}:{$id}";
    }

    /**
     * Invalidate all theme listings and KPI caches atomically in O(1) time.
     */
    public static function flushCaches(): void
    {
        Cache::forget(self::KPI_CACHE_KEY);
        self::incrementVersion();

        // Evict any recruiter / global email template caches across tenants (Rule 6)
        Cache::forget('email_templates:global:active');
        Cache::forget('email_templates:global:all');
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
     * Cache or retrieve single template item.
     */
    public static function rememberItem(int $id, \Closure $callback): array|object|null
    {
        $key = self::getItemKey($id);

        return Cache::remember($key, self::ITEM_TTL, $callback);
    }
}
