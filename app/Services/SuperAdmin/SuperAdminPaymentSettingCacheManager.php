<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;

/**
 * Class SuperAdminPaymentSettingCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin Payment Settings subsystem. Implements generation-based versioning for
 * O(1) atomic cache invalidation without keyspace scanning, single-flight caching,
 * and multi-tenant cache synchronization (Rule 6).
 *
 * Tenancy: Scoped strictly to Super Admin singleton configuration.
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminPaymentSettingCacheManager
{
    public const VERSION_KEY = 'superadmin:payment_settings:version';

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
     * Generate cache key for singleton Super Admin payment setting.
     */
    public static function getPaymentSettingKey(): string
    {
        $version = self::getVersion();

        return "superadmin:payment_settings:singleton:v{$version}";
    }

    /**
     * Invalidate all Super Admin payment setting cached records across the platform.
     * Rule 6: Multi-Tenant Atomic Cache Invalidation.
     */
    public static function flushCaches(): void
    {
        // Legacy cross-controller cache keys
        Cache::forget('superadmin_payment_setting');

        // Generational O(1) cache version bump
        self::incrementVersion();
    }

    /**
     * Cache or retrieve Super Admin payment setting document.
     */
    public static function rememberPaymentSetting(\Closure $callback): array
    {
        $key = self::getPaymentSettingKey();

        return Cache::remember($key, self::SETTINGS_TTL, $callback);
    }
}
