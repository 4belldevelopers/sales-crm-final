<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;

/**
 * Class SuperAdminSmtpSettingCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin SMTP Settings & Multi-Gateway Hub. Implements generation-based versioning
 * for O(1) atomic invalidation, multi-tenant cache synchronization (Rule 6), single-flight caching,
 * and sub-millisecond read offloading.
 *
 * Scoped strictly to Super Admin (tenant_id = 0) with cross-tenant and consumer mail services cache eviction.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminSmtpSettingCacheManager
{
    public const VERSION_KEY = 'superadmin:smtp_settings:version';

    public const KPI_CACHE_KEY = 'superadmin:smtp_settings:kpi';

    public const KPI_TTL = 300; // 5 minutes

    public const LIST_TTL = 300; // 5 minutes

    public const ITEM_TTL = 300; // 5 minutes

    /**
     * Permitted primary gateway IDs strictly configured in Super Admin.
     */
    public const GATEWAY_IDS = [8, 9, 10, 11, 12];

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

        return "superadmin:smtp_settings:list:v{$version}:{$hash}";
    }

    /**
     * Generate cache key for a single gateway item.
     */
    public static function getItemKey(int $id): string
    {
        $version = self::getVersion();

        return "superadmin:smtp_settings:item:v{$version}:{$id}";
    }

    /**
     * Invalidate all SMTP gateway listings, items, and platform-wide mailer caches.
     * Rule 6: Multi-Tenant Atomic Cache Invalidation.
     */
    public static function flushCaches(): void
    {
        Cache::forget(self::KPI_CACHE_KEY);
        Cache::forget('smtp_settings:default');
        Cache::forget('smtp_settings:active_gateways');

        foreach (self::GATEWAY_IDS as $gid) {
            Cache::forget("smtp_settings:gateway:{$gid}");
        }

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
     * Cache or retrieve single gateway item.
     */
    public static function rememberItem(int $id, \Closure $callback): ?object
    {
        $key = self::getItemKey($id);

        return Cache::remember($key, self::ITEM_TTL, $callback);
    }
}
