<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;

/**
 * Class SuperAdminProfileCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin Root Profile & Security subsystem. Implements generation-based versioning
 * for O(1) atomic cache invalidation, multi-tenant cache synchronization (Rule 6),
 * and sub-millisecond read offloading.
 *
 * Tenancy: Master Super Admin singleton scope (tenant_id = 0).
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminProfileCacheManager
{
    public const VERSION_KEY = 'superadmin:profile:version';

    public const PROFILE_TTL = 300; // 5 minutes

    public const SMS_SETTINGS_TTL = 3600; // 1 hour

    public const CALLING_CODES_TTL = 86400; // 24 hours

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
     * Generate cache key for user profile.
     */
    public static function getProfileKey(int $userId): string
    {
        $version = self::getVersion();

        return "superadmin:profile:user:v{$version}:{$userId}";
    }

    /**
     * Generate cache key for sms settings.
     */
    public static function getSmsSettingsKey(): string
    {
        $version = self::getVersion();

        return "superadmin:profile:sms_settings:v{$version}";
    }

    /**
     * Generate cache key for calling codes catalog.
     */
    public static function getCallingCodesKey(): string
    {
        $version = self::getVersion();

        return "superadmin:profile:calling_codes:v{$version}";
    }

    /**
     * Cache or retrieve the Super Admin profile document attributes.
     */
    public static function rememberProfile(int $userId, \Closure $callback): ?array
    {
        $key = self::getProfileKey($userId);

        return Cache::remember($key, self::PROFILE_TTL, $callback);
    }

    /**
     * Cache or retrieve SMS Settings gateway attributes.
     */
    public static function rememberSmsSettings(\Closure $callback): ?array
    {
        $key = self::getSmsSettingsKey();

        return Cache::remember($key, self::SMS_SETTINGS_TTL, $callback);
    }

    /**
     * Cache or retrieve calling codes reference list.
     */
    public static function rememberCallingCodes(\Closure $callback): array
    {
        $key = self::getCallingCodesKey();

        return Cache::remember($key, self::CALLING_CODES_TTL, $callback);
    }

    /**
     * Invalidate all Super Admin profile caches, user auth caches, and bump generation version.
     * Rule 6: Multi-Tenant Atomic Cache Invalidation.
     */
    public static function flushCaches(?int $userId = null): void
    {
        if ($userId) {
            Cache::forget("user_{$userId}");
            Cache::forget("superadmin_user_{$userId}");
            Cache::forget("user:auth:{$userId}");
        }

        Cache::forget('superadmin_sms_settings_singleton');

        self::incrementVersion();
    }
}
