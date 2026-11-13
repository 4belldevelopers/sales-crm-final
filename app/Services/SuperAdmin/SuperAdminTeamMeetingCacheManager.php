<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class SuperAdminTeamMeetingCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin Microsoft Teams Meeting subsystem.
 *
 * Enterprise Hardening Features:
 * - Generation-based versioning for O(1) atomic cache invalidation without keyspace scanning.
 * - Distributed single-flight atomic locks to prevent cache stampedes under 10,000 concurrent users.
 * - Strict query parameter normalization and cardinality bounds (50-char bounded search).
 * - Version-aware deletion tombstones to protect against entity resurrection from delayed outbox events.
 * - Fast-path fallback database overload protection cache.
 * - Multi-tenant & ATS atomic cache invalidation per Rule 6 (`jero:team_meeting_links:active_pool`).
 *
 * Scoped strictly to Super Admin (tenant_id = 0) with platform-wide ATS interview scheduling cache eviction.
 * Target: 10,000 concurrent users / 0% server & database read strain.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminTeamMeetingCacheManager
{
    public const VERSION_KEY = 'superadmin:team_meeting:version';

    public const SCHEDULE_KEY = 'superadmin:team_meeting:schedules';

    public const SCHEDULE_COUNT_KEY = 'superadmin:team_meeting:schedule_count';

    public const TOMBSTONE_PREFIX = 'superadmin:team_meeting:tombstone:';

    public const DB_FALLBACK_PREFIX = 'superadmin:team_meeting:db_fallback:';

    public const LIST_TTL = 300; // 5 minutes

    public const SCHEDULE_TTL = 180; // 3 minutes

    public const DB_FALLBACK_TTL = 60; // 1 minute overload guard

    public const TOMBSTONE_TTL = 86400; // 24 hours

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
     * Normalize query parameters to prevent uncontrolled cache key cardinality.
     */
    public static function normalizeParams(array $params): array
    {
        $rawSearch = (string) ($params['search'] ?? '');
        $cleanSearch = trim(preg_replace('/\s+/', ' ', mb_strtolower($rawSearch)));
        $boundedSearch = mb_substr($cleanSearch, 0, 50);

        $rawStatus = strtolower(trim((string) ($params['status'] ?? '')));
        $normalizedStatus = match ($rawStatus) {
            '1', 'active' => '1',
            '0', 'inactive' => '0',
            default => 'all',
        };

        $page = max(1, (int) ($params['page'] ?? 1));
        $perPage = max(1, min(100, (int) ($params['per_page'] ?? 15)));

        return [
            'q' => $boundedSearch,
            'st' => $normalizedStatus,
            'p' => $page,
            'pp' => $perPage,
        ];
    }

    /**
     * Generate cache key for a specific query fingerprint.
     */
    public static function getListKey(array $params): string
    {
        $version = self::getVersion();
        $normalized = self::normalizeParams($params);
        ksort($normalized);
        $hash = substr(md5(json_encode($normalized)), 0, 16);

        return "superadmin:team_meeting:list:v{$version}:{$hash}";
    }

    /**
     * Cache or retrieve query results with single-flight stampede protection.
     */
    public static function rememberList(array $params, \Closure $callback): array
    {
        $key = self::getListKey($params);

        $cached = Cache::get($key);
        if ($cached !== null && is_array($cached)) {
            return $cached;
        }

        // Single-flight stampede lock
        $lockKey = 'lock:tmeet:list:'.md5($key);
        $lock = Cache::lock($lockKey, 5);

        try {
            if ($lock->get()) {
                // Double-check cache after lock acquisition
                $cached = Cache::get($key);
                if ($cached !== null && is_array($cached)) {
                    return $cached;
                }

                $data = $callback();
                Cache::put($key, $data, self::LIST_TTL);

                return $data;
            }

            // Other workers wait briefly and serve from newly primed cache
            usleep(150000); // 150ms
            $cached = Cache::get($key);
            if ($cached !== null && is_array($cached)) {
                return $cached;
            }

            return $callback();
        } catch (\Throwable $e) {
            Log::warning('SuperAdminTeamMeetingCacheManager::rememberList stampede lock error: '.$e->getMessage());

            return $callback();
        } finally {
            optional($lock)->release();
        }
    }

    /**
     * Cache or retrieve schedule history allocations.
     */
    public static function rememberScheduleHistory(\Closure $callback): array
    {
        return Cache::remember(self::SCHEDULE_KEY, self::SCHEDULE_TTL, $callback);
    }

    /**
     * Cache or retrieve schedule count.
     */
    public static function rememberScheduleCount(\Closure $callback): int
    {
        return (int) Cache::remember(self::SCHEDULE_COUNT_KEY, self::SCHEDULE_TTL, $callback);
    }

    /**
     * Cache short-lived fallback results when Elasticsearch circuit is open.
     */
    public static function rememberDbFallback(string $subKey, \Closure $callback): array
    {
        $key = self::DB_FALLBACK_PREFIX.md5($subKey);

        return Cache::remember($key, self::DB_FALLBACK_TTL, $callback);
    }

    /**
     * Set a version-aware deletion tombstone for a deleted link ID.
     */
    public static function setTombstone(int|string $id, int $version): void
    {
        Cache::put(self::TOMBSTONE_PREFIX.$id, $version, self::TOMBSTONE_TTL);
    }

    /**
     * Verify if a version-aware deletion tombstone exists and supersedes an event.
     */
    public static function hasTombstone(int|string $id, int $version): bool
    {
        $tombstoneVersion = Cache::get(self::TOMBSTONE_PREFIX.$id);
        if ($tombstoneVersion !== null) {
            return (int) $version <= (int) $tombstoneVersion;
        }

        return false;
    }

    /**
     * Invalidate all Teams Meet caches and platform-wide ATS booking pool.
     * Rule 6: Multi-Tenant Atomic Cache Invalidation.
     */
    public static function flushCaches(): void
    {
        Cache::forget(self::SCHEDULE_KEY);
        Cache::forget(self::SCHEDULE_COUNT_KEY);
        Cache::forget('jero:team_meeting_links:active_pool');

        self::incrementVersion();
    }
}
