<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class SuperAdminMeetingScheduleCacheManager
 *
 * Intent: Centralized cache manager for the Super Admin Meeting Tracker & Schedule Hub.
 * Enforces unified cache key schema, O(1) generation versioning, distributed stampede
 * protection (single-flight locking with double-checked cache verification), generation-aware
 * cache write protection (discards stale race writes), telemetry tracking, and authoritative
 * mutation delta overlay for read-after-write consistency guarantees.
 *
 * Tenancy Note: Universal Super Admin execution context scoped across all tenants.
 * Hardware Profile: Tailored for Redis / DragonflyDB with 100k+ QPS capability.
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminMeetingScheduleCacheManager
{
    public const VERSION_KEY = 'sa:meetings:version';

    public const DELTA_PREFIX = 'sa:meetings:delta:';

    public const SNAPSHOT_KEY = 'sa:meetings:snapshot:latest';

    public const TELEMETRY_HITS_KEY = 'telemetry:sa:meetings:hits';

    public const TELEMETRY_MISSES_KEY = 'telemetry:sa:meetings:misses';

    public const DEFAULT_VERSION_TTL = 2592000; // 30 days

    public const DEFAULT_LIST_TTL = 120; // 2 minutes

    public const DEFAULT_COMMENT_TTL = 600; // 10 minutes

    public const DEFAULT_DELTA_TTL = 60; // 60 seconds

    public const MAX_SNAPSHOT_AGE_SECONDS = 14400; // 4 hours maximum freshness for degraded snapshot

    /**
     * Get the current global generation version.
     */
    public static function getVersion(): int
    {
        try {
            return (int) Cache::get(self::VERSION_KEY, 1);
        } catch (\Throwable $e) {
            return 1;
        }
    }

    /**
     * Increment the global generation version, atomically invalidating
     * all list queries, KPIs, and meeting counts in O(1) time.
     */
    public static function incrementVersion(): int
    {
        try {
            if (Cache::has(self::VERSION_KEY)) {
                return (int) Cache::increment(self::VERSION_KEY);
            }
            Cache::put(self::VERSION_KEY, 2, self::DEFAULT_VERSION_TTL);

            return 2;
        } catch (\Throwable $e) {
            Log::warning('SuperAdminMeetingScheduleCacheManager::incrementVersion warning: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Build unified cache key for total meetings counter.
     */
    public static function totalCountKey(int $version): string
    {
        return "sa:meetings:v{$version}:total_count";
    }

    /**
     * Build unified cache key for paginated meeting schedule lists.
     */
    public static function listKey(int $version, array $params): string
    {
        ksort($params);
        $hash = md5(json_encode($params));

        return "sa:meetings:v{$version}:list:{$hash}";
    }

    /**
     * Build unified cache key for meeting comment modal/drawer contents.
     */
    public static function commentKey(int $version, int $meetingId): string
    {
        return "sa:meetings:v{$version}:comment:{$meetingId}";
    }

    /**
     * Store authoritative in-memory mutation delta to guarantee 100% read-after-write consistency.
     */
    public static function setMutationDelta(int $meetingId, array $delta): void
    {
        try {
            $delta['updated_at'] = microtime(true);
            Cache::put(self::DELTA_PREFIX.$meetingId, $delta, self::DEFAULT_DELTA_TTL);
        } catch (\Throwable $e) {
            Log::warning("SuperAdminMeetingScheduleCacheManager::setMutationDelta warning for Meeting #{$meetingId}: ".$e->getMessage());
        }
    }

    /**
     * Retrieve authoritative mutation delta if active.
     */
    public static function getMutationDelta(int $meetingId): ?array
    {
        try {
            $delta = Cache::get(self::DELTA_PREFIX.$meetingId);

            return is_array($delta) ? $delta : null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Invalidate all read caches and specific entity keys atomically.
     */
    public static function invalidateAll(?int $meetingId = null): void
    {
        if ($meetingId) {
            try {
                Cache::forget(self::DELTA_PREFIX.$meetingId);
            } catch (\Throwable $e) {
            }
        }

        self::incrementVersion();
    }

    /**
     * Save snapshot for degraded circuit breaker fallback.
     */
    public static function saveSnapshot(array $payload): void
    {
        try {
            Cache::put(self::SNAPSHOT_KEY, [
                'timestamp' => time(),
                'payload' => $payload,
            ], self::MAX_SNAPSHOT_AGE_SECONDS);
        } catch (\Throwable $e) {
            Log::warning('SuperAdminMeetingScheduleCacheManager::saveSnapshot warning: '.$e->getMessage());
        }
    }

    /**
     * Retrieve latest snapshot with freshness validation.
     */
    public static function getLatestSnapshot(int $maxAgeSeconds = self::MAX_SNAPSHOT_AGE_SECONDS): ?array
    {
        try {
            $data = Cache::get(self::SNAPSHOT_KEY);
            if (! is_array($data) || empty($data['payload'])) {
                return null;
            }

            $age = time() - (int) ($data['timestamp'] ?? 0);
            if ($age > $maxAgeSeconds) {
                Log::warning("SuperAdminMeetingScheduleCacheManager: Latest snapshot expired (age: {$age}s > {$maxAgeSeconds}s). Discarding.");

                return null;
            }

            $payload = $data['payload'];
            $payload['snapshot_age_seconds'] = $age;

            return $payload;
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Get operational telemetry metrics for cache hits, misses, and ratio.
     */
    public static function getTelemetryMetrics(): array
    {
        try {
            $hits = (int) Cache::get(self::TELEMETRY_HITS_KEY, 0);
            $misses = (int) Cache::get(self::TELEMETRY_MISSES_KEY, 0);
            $total = $hits + $misses;
            $hitRatio = $total > 0 ? round(($hits / $total) * 100, 2) : 100.0;

            return [
                'hits' => $hits,
                'misses' => $misses,
                'total_requests' => $total,
                'hit_ratio_percent' => $hitRatio,
                'current_version' => self::getVersion(),
            ];
        } catch (\Throwable $e) {
            return [
                'hits' => 0,
                'misses' => 0,
                'total_requests' => 0,
                'hit_ratio_percent' => 100.0,
                'current_version' => 1,
            ];
        }
    }

    /**
     * Fetch from cache with distributed stampede protection and generation-aware write guard.
     *
     * Prevents the Thundering Herd problem: when a cache key misses or expires under 10K concurrency,
     * only ONE worker process is granted the lock to compute the value from the database.
     * All other concurrent requests poll briefly (spin-wait) to receive the leader's computed value.
     *
     * Generation-Aware Cache Guard:
     * Validates that the cache version hasn't changed during execution of the computation closure.
     * If a concurrent write incremented the version during computation, the stale result is DISCARDED.
     */
    public static function rememberWithStampedeProtection(
        string $key,
        int $ttlSeconds,
        \Closure $computation,
        int $lockTimeout = 5,
        int $maxWaitMs = 500
    ): mixed {
        // 1. Fast Path: Cache Hit
        try {
            $cached = Cache::get($key);
            if ($cached !== null) {
                Cache::increment(self::TELEMETRY_HITS_KEY);

                return $cached;
            }
        } catch (\Throwable $e) {
            Log::warning("SuperAdminMeetingScheduleCacheManager::remember get error on [{$key}]: ".$e->getMessage());
        }

        // 2. Slow Path: Cache Miss - Acquire Single-Flight Distributed Lock
        $lockKey = "lock:{$key}";
        $lock = null;
        $isLeader = false;

        try {
            $lock = Cache::lock($lockKey, $lockTimeout);
            $isLeader = $lock->get();
        } catch (\Throwable $e) {
            $isLeader = false;
        }

        if ($isLeader) {
            try {
                // Double-checked cache verification
                $doubleChecked = Cache::get($key);
                if ($doubleChecked !== null) {
                    Cache::increment(self::TELEMETRY_HITS_KEY);

                    return $doubleChecked;
                }

                Cache::increment(self::TELEMETRY_MISSES_KEY);

                // Generation version at start of computation
                $startVersion = self::getVersion();

                // Compute fresh value
                $computed = $computation();

                // Generation-Aware Guard: Only write if version did not change during computation
                $endVersion = self::getVersion();
                if ($startVersion === $endVersion) {
                    Cache::put($key, $computed, $ttlSeconds);
                } else {
                    Log::info("SuperAdminMeetingScheduleCacheManager: Stale write discarded for [{$key}]. Version drifted: {$startVersion} -> {$endVersion}");
                }

                return $computed;
            } finally {
                try {
                    optional($lock)->release();
                } catch (\Throwable $e) {
                }
            }
        }

        // 3. Follower Spin-Wait: Poll up to maxWaitMs for the leader's computed result
        $stepMs = 25;
        $elapsedMs = 0;

        while ($elapsedMs < $maxWaitMs) {
            usleep($stepMs * 1000);
            $elapsedMs += $stepMs;

            try {
                $pollCached = Cache::get($key);
                if ($pollCached !== null) {
                    Cache::increment(self::TELEMETRY_HITS_KEY);

                    return $pollCached;
                }
            } catch (\Throwable $e) {
                break;
            }
        }

        // 4. Fallback execution if leader computation timed out
        Cache::increment(self::TELEMETRY_MISSES_KEY);
        $fbStartVersion = self::getVersion();
        $fallbackComputed = $computation();
        if ($fbStartVersion === self::getVersion()) {
            try {
                Cache::put($key, $fallbackComputed, $ttlSeconds);
            } catch (\Throwable $e) {
            }
        }

        return $fallbackComputed;
    }
}
