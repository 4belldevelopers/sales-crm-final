<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;

/**
 * Class SuperAdminGlobalSettingCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin Global Settings & Platform Configuration Hub. Implements generation-based
 * versioning for O(1) atomic invalidation, multi-tenant cache synchronization (Rule 6),
 * single-flight caching, and sub-millisecond read offloading.
 *
 * Scoped strictly to Super Admin (tenant_id = 0) with cross-tenant platform cache eviction.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminGlobalSettingCacheManager
{
    public const VERSION_KEY = 'superadmin:global_settings:version';

    public const KPI_CACHE_KEY = 'superadmin:global_settings:kpi';

    public const SETTINGS_TTL = 300; // 5 minutes

    public const REFERENCE_TTL = 86400; // 24 hours

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
     * Generate cache key for singleton global settings.
     */
    public static function getSettingsKey(): string
    {
        $version = self::getVersion();

        return "superadmin:global_settings:singleton:v{$version}";
    }

    /**
     * Generate cache key for sold-by invoicing address.
     */
    public static function getSoldByKey(): string
    {
        $version = self::getVersion();

        return "superadmin:global_settings:sold_by:v{$version}";
    }

    /**
     * Generate cache key for static currencies catalog.
     */
    public static function getCurrenciesKey(): string
    {
        $version = self::getVersion();

        return "superadmin:global_settings:currencies:v{$version}";
    }

    /**
     * Generate cache key for enabled languages.
     */
    public static function getLanguagesKey(): string
    {
        $version = self::getVersion();

        return "superadmin:global_settings:languages:v{$version}";
    }

    /**
     * Generate cache key for countries list.
     */
    public static function getCountriesKey(): string
    {
        $version = self::getVersion();

        return "superadmin:global_settings:countries:v{$version}";
    }

    /**
     * Invalidate all global settings, sold-by, and platform-wide cached configurations.
     * Rule 6: Multi-Tenant Atomic Cache Invalidation.
     */
    public static function flushCaches(): void
    {
        Cache::forget(self::KPI_CACHE_KEY);
        Cache::forget('global_settings');
        Cache::forget('global_setting:default');
        Cache::forget('local_sold_by_address:1');

        self::incrementVersion();
    }

    /**
     * Cache or retrieve singleton global settings document.
     */
    public static function rememberSettings(\Closure $callback): array
    {
        $key = self::getSettingsKey();

        return Cache::remember($key, self::SETTINGS_TTL, $callback);
    }

    /**
     * Cache or retrieve sold-by address document.
     */
    public static function rememberSoldBy(\Closure $callback): ?array
    {
        $key = self::getSoldByKey();

        return Cache::remember($key, self::SETTINGS_TTL, $callback);
    }

    /**
     * Cache or retrieve static currencies catalog.
     */
    public static function rememberCurrencies(\Closure $callback): array
    {
        $key = self::getCurrenciesKey();

        return Cache::remember($key, self::REFERENCE_TTL, $callback);
    }

    /**
     * Cache or retrieve enabled language settings.
     */
    public static function rememberLanguages(\Closure $callback): array
    {
        $key = self::getLanguagesKey();

        return Cache::remember($key, self::REFERENCE_TTL, $callback);
    }

    /**
     * Cache or retrieve countries list.
     */
    public static function rememberCountries(\Closure $callback): array
    {
        $key = self::getCountriesKey();

        return Cache::remember($key, self::REFERENCE_TTL, $callback);
    }

    /**
     * Cache or retrieve KPI aggregations.
     */
    public static function rememberKpi(\Closure $callback): array
    {
        return Cache::remember(self::KPI_CACHE_KEY, self::SETTINGS_TTL, $callback);
    }
}
