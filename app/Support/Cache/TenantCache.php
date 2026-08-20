<?php

namespace App\Support\Cache;

use Closure;
use Illuminate\Support\Facades\Cache;

/**
 * TenantCache — High-performance O(1) tenant-isolated cache layer.
 *
 * Intent:
 * Provides versioned caching per tenant. Increments a single tenant version counter
 * (`tenant:{company_id}:version`) to invalidate all cache entries for a tenant in O(1)
 * time without using expensive O(N) pattern scans or Redis tag overhead.
 *
 * Tenancy:
 * Scoped strictly by $companyId.
 *
 * Author: Antigravity AI
 * Date: August 20, 2026
 */
class TenantCache
{
    /**
     * Default TTL for tenant cache items in seconds (1 hour).
     */
    public const DEFAULT_TTL = 3600;

    /**
     * Get current version number for a tenant.
     */
    public static function getVersion(int|string $companyId): int
    {
        $versionKey = "tenant:{$companyId}:version";

        return (int) Cache::rememberForever($versionKey, fn () => 1);
    }

    /**
     * Build version-prefixed cache key for a tenant.
     */
    public static function buildKey(int|string $companyId, string $key): string
    {
        $version = static::getVersion($companyId);

        return "t:{$companyId}:v{$version}:{$key}";
    }

    /**
     * Get an item from the cache, or execute the given Closure and store the result.
     */
    public static function remember(int|string $companyId, string $key, int $ttl, Closure $callback): mixed
    {
        $versionedKey = static::buildKey($companyId, $key);

        return Cache::remember($versionedKey, $ttl, $callback);
    }

    /**
     * Retrieve an item from the tenant cache by key.
     */
    public static function get(int|string $companyId, string $key, mixed $default = null): mixed
    {
        $versionedKey = static::buildKey($companyId, $key);

        return Cache::get($versionedKey, $default);
    }

    /**
     * Store an item in the tenant cache.
     */
    public static function put(int|string $companyId, string $key, mixed $value, int $ttl = self::DEFAULT_TTL): bool
    {
        $versionedKey = static::buildKey($companyId, $key);

        return Cache::put($versionedKey, $value, $ttl);
    }

    /**
     * Remove a specific item from the tenant cache.
     */
    public static function forget(int|string $companyId, string $key): bool
    {
        $versionedKey = static::buildKey($companyId, $key);

        return Cache::forget($versionedKey);
    }

    /**
     * Invalidate ALL cache entries for a tenant in O(1) time by incrementing tenant version.
     *
     * @return int The new version number.
     */
    public static function invalidateTenant(int|string $companyId): int
    {
        $versionKey = "tenant:{$companyId}:version";
        $newVersion = static::getVersion($companyId) + 1;
        Cache::forever($versionKey, $newVersion);

        return $newVersion;
    }
}
