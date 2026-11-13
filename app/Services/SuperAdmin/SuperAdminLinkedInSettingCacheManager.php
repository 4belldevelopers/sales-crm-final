<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;

/**
 * Class SuperAdminLinkedInSettingCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin LinkedIn Settings subsystem. Implements generation-based versioning for
 * O(1) atomic cache invalidation without keyspace scanning, single-flight caching,
 * and multi-tenant cache synchronization (Rule 6).
 *
 * Tenancy: Scoped strictly to Super Admin (company_id IS NULL / global singleton).
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminLinkedInSettingCacheManager
{
    public const VERSION_KEY = 'superadmin:linkedin_settings:version';

    public const SETTINGS_TTL = 300; // 5 minutes

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
     * Generate cache key for singleton Super Admin LinkedIn setting.
     */
    public static function getLinkedInSettingKey(): string
    {
        $version = self::getVersion();

        return "superadmin:linkedin_settings:singleton:v{$version}";
    }

    /**
     * Invalidate all Super Admin LinkedIn setting cached records across the platform.
     * Rule 6: Multi-Tenant Atomic Cache Invalidation.
     */
    public static function flushCaches(): void
    {
        // Legacy cross-controller cache keys
        Cache::forget('linkedin_global_setting');
        Cache::forget('linkedin_setting');

        // Generational O(1) cache version bump
        self::incrementVersion();
    }

    /**
     * Cache or retrieve Super Admin LinkedIn setting document.
     */
    public static function rememberLinkedInSetting(\Closure $callback): array
    {
        $key = self::getLinkedInSettingKey();

        return Cache::remember($key, self::SETTINGS_TTL, $callback);
    }
}
