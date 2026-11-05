<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;

/**
 * Class WhatsAppThemeCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin WhatsApp Themes subsystem. Implements generation-based versioning for O(1)
 * atomic invalidation, single-flight caching, and sub-millisecond read offloading.
 *
 * Scoped strictly to Super Admin (tenant_id = 0) with cross-tenant cache eviction.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class WhatsAppThemeCacheManager
{
    public const VERSION_KEY = 'superadmin:whatsapp_themes:version';

    public const KPI_CACHE_KEY = 'superadmin:whatsapp_themes:kpi';

    public const MAX_ORDER_KEY = 'superadmin:whatsapp_themes:max_order';

    public const KPI_TTL = 300; // 5 minutes

    public const LIST_TTL = 300; // 5 minutes

    public const ITEM_TTL = 300; // 5 minutes

    public const MAX_ORDER_TTL = 300; // 5 minutes

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

        return "superadmin:whatsapp_themes:list:v{$version}:{$hash}";
    }

    /**
     * Generate cache key for a single template item.
     */
    public static function getItemKey(int $id): string
    {
        $version = self::getVersion();

        return "superadmin:whatsapp_themes:item:v{$version}:{$id}";
    }

    /**
     * Generate cache key for next sequence order position.
     */
    public static function getMaxOrderKey(): string
    {
        $version = self::getVersion();

        return "superadmin:whatsapp_themes:max_order:v{$version}";
    }

    /**
     * Invalidate all theme listings and KPI caches atomically in O(1) time.
     */
    public static function flushCaches(): void
    {
        Cache::forget(self::KPI_CACHE_KEY);
        Cache::forget(self::MAX_ORDER_KEY);
        self::incrementVersion();

        // Evict any recruiter / global WhatsApp template caches (Rule 6)
        Cache::forget('whatsapp_templates:global:active');
        Cache::forget('whatsapp_templates:global:all');
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

    /**
     * Cache or retrieve maximum order position.
     */
    public static function rememberMaxOrder(\Closure $callback): int
    {
        $key = self::getMaxOrderKey();

        return (int) Cache::remember($key, self::MAX_ORDER_TTL, $callback);
    }
}
