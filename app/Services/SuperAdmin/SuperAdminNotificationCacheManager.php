<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;

/**
 * Class SuperAdminNotificationCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin Email Notifications & Role Permissions subsystem. Implements generation-based versioning
 * for O(1) atomic invalidation, multi-tenant cache synchronization (Rule 6), and sub-millisecond read offloading.
 *
 * Scoped strictly to Super Admin (tenant_id = 0) with cross-tenant and consumer service cache eviction.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminNotificationCacheManager
{
    public const VERSION_KEY = 'superadmin:notifications:version';

    public const KPI_CACHE_KEY = 'superadmin:notifications:kpi';

    public const ROLES_CACHE_KEY = 'superadmin:notifications:roles';

    public const LIST_TTL = 300; // 5 minutes

    public const ITEM_TTL = 300; // 5 minutes

    public const ROLES_TTL = 3600; // 1 hour

    public const PREVIEW_TTL = 600; // 10 minutes

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
     * Generate cache key for a specific query fingerprint or listing.
     */
    public static function getListKey(array $params = []): string
    {
        $version = self::getVersion();
        ksort($params);
        $hash = ! empty($params) ? substr(md5(json_encode($params)), 0, 16) : 'all';

        return "superadmin:notifications:list:v{$version}:{$hash}";
    }

    /**
     * Generate cache key for a single notification item.
     */
    public static function getItemKey(int $id): string
    {
        $version = self::getVersion();

        return "superadmin:notifications:item:v{$version}:{$id}";
    }

    /**
     * Generate cache key for sandboxed preview.
     */
    public static function getPreviewKey(int $id, bool $isJson = false): string
    {
        $version = self::getVersion();
        $format = $isJson ? 'json' : 'html';

        return "superadmin:notifications:preview:v{$version}:{$id}:{$format}";
    }

    /**
     * Generate cache key for global distinct roles.
     */
    public static function getRolesKey(): string
    {
        $version = self::getVersion();

        return "superadmin:notifications:roles:v{$version}";
    }

    /**
     * Invalidate all notification listings, single items, previews, and dependent caches atomically in O(1) time.
     * Also invalidates tenant-facing email notification caches per Rule 6.
     *
     * @param  int|null  $specificId  Optional specific notification ID to evict
     */
    public static function flushCaches(?int $specificId = null): void
    {
        Cache::forget(self::KPI_CACHE_KEY);
        Cache::forget(self::ROLES_CACHE_KEY);
        Cache::forget('email_notification_cpr_admin');
        Cache::forget('email_notification_cpr_customer');

        if ($specificId !== null && $specificId > 0) {
            Cache::forget("email_notification:{$specificId}");
        }

        self::incrementVersion();
    }

    /**
     * Cache or retrieve query listing.
     */
    public static function rememberList(array $params, \Closure $callback): mixed
    {
        $key = self::getListKey($params);

        return Cache::remember($key, self::LIST_TTL, $callback);
    }

    /**
     * Cache or retrieve single notification item.
     */
    public static function rememberItem(int $id, \Closure $callback): mixed
    {
        $key = self::getItemKey($id);

        return Cache::remember($key, self::ITEM_TTL, $callback);
    }

    /**
     * Cache or retrieve preview result.
     */
    public static function rememberPreview(int $id, bool $isJson, \Closure $callback): mixed
    {
        $key = self::getPreviewKey($id, $isJson);

        return Cache::remember($key, self::PREVIEW_TTL, $callback);
    }

    /**
     * Cache or retrieve distinct roles.
     */
    public static function rememberRoles(\Closure $callback): mixed
    {
        $key = self::getRolesKey();

        return Cache::remember($key, self::ROLES_TTL, $callback);
    }
}
