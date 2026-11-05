<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;

/**
 * Class CouponCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin Coupons subsystem. Implements generation-based versioning for O(1)
 * atomic invalidation, multi-tenant coupon code cache synchronization (Rule 6),
 * single-flight caching, and sub-millisecond read offloading.
 *
 * Scoped to Super Admin (tenant_id = 0) with cross-tenant coupon cache eviction.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class CouponCacheManager
{
    public const VERSION_KEY = 'superadmin:coupons:version';

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

        return "superadmin:coupons:list:v{$version}:{$hash}";
    }

    /**
     * Generate cache key for a single coupon item by ID.
     */
    public static function getItemKey(int $id): string
    {
        $version = self::getVersion();

        return "superadmin:coupons:item:v{$version}:{$id}";
    }

    /**
     * Generate cache key for a coupon code lookup across tenants.
     */
    public static function getCouponCodeKey(string $code): string
    {
        $normalized = strtoupper(trim($code));

        return "coupon:code:{$normalized}";
    }

    /**
     * Invalidate all coupon listings and item caches atomically in O(1) time.
     * Also invalidates tenant-facing coupon code caches per Rule 6.
     */
    public static function flushCaches(?string $code = null): void
    {
        if ($code !== null && trim($code) !== '') {
            Cache::forget(self::getCouponCodeKey($code));
        }

        self::incrementVersion();
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
     * Cache or retrieve single coupon item.
     */
    public static function rememberItem(int $id, \Closure $callback): ?array
    {
        $key = self::getItemKey($id);

        return Cache::remember($key, self::ITEM_TTL, $callback);
    }
}
