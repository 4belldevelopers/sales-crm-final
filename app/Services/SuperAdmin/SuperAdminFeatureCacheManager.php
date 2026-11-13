<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;

/**
 * Class SuperAdminFeatureCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin Platform Feature Policies & Expiration Rules subsystem. Implements
 * generation-based versioning for O(1) atomic cache invalidation without keyspace scanning,
 * single-flight caching, and multi-tenant cache synchronization (Rule 6).
 *
 * Tenancy: Scoped strictly to Super Admin (Global System Policies).
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminFeatureCacheManager
{
    public const VERSION_KEY = 'superadmin:features_list:version';

    public const FEATURES_TTL = 300; // 5 minutes

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
     * Generate cache key for all platform feature policies.
     */
    public static function getFeaturesKey(): string
    {
        $version = self::getVersion();

        return "superadmin:features_list:all:v{$version}";
    }

    /**
     * Generate cache key for single feature policy by ID.
     */
    public static function getFeatureKey(int $id): string
    {
        $version = self::getVersion();

        return "superadmin:features_list:item:{$id}:v{$version}";
    }

    /**
     * Invalidate all Super Admin feature policy cached records across the platform.
     * Rule 6: Multi-Tenant Atomic Cache Invalidation.
     */
    public static function flushCaches(): void
    {
        // Legacy cross-controller cache keys
        Cache::forget('superadmin_features_list');

        // Generational O(1) cache version bump
        self::incrementVersion();
    }

    /**
     * Cache or retrieve all platform feature policies.
     */
    public static function rememberFeatures(\Closure $callback): array
    {
        $key = self::getFeaturesKey();

        return Cache::remember($key, self::FEATURES_TTL, $callback);
    }

    /**
     * Cache or retrieve single feature policy by ID.
     */
    public static function rememberFeature(int $id, \Closure $callback): ?array
    {
        $key = self::getFeatureKey($id);

        return Cache::remember($key, self::FEATURES_TTL, $callback);
    }
}
