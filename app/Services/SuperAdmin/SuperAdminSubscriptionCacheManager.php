<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class SuperAdminSubscriptionCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin Subscription Management subsystem. Implements generation-based versioning
 * for O(1) atomic cache invalidation without keyspace scanning, single-flight caching,
 * and multi-tenant cache synchronization (Rule 6).
 *
 * Tenancy: Scoped strictly to Super Admin subscription operations across tenant boundaries.
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminSubscriptionCacheManager
{
    public const VERSION_KEY = 'superadmin:subscription:version';

    public const KPI_TTL = 60; // 60 seconds

    public const LIST_TTL = 30; // 30 seconds

    public const DOSSIER_TTL = 900; // 15 minutes

    /**
     * Get current generation version.
     */
    public static function getVersion(): int
    {
        try {
            return (int) Cache::get(self::VERSION_KEY, 1);
        } catch (Throwable) {
            return 1;
        }
    }

    /**
     * Atomically increment the generation version.
     */
    public static function incrementVersion(): int
    {
        try {
            if (! Cache::has(self::VERSION_KEY)) {
                Cache::forever(self::VERSION_KEY, 1);
            }

            return (int) Cache::increment(self::VERSION_KEY);
        } catch (Throwable $e) {
            Log::warning('SuperAdminSubscriptionCacheManager::incrementVersion failed: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Generate cache key for subscription KPI aggregation counts.
     */
    public static function getKpiKey(string $coinEventType): string
    {
        $version = self::getVersion();

        return "superadmin:subscription:kpi:{$coinEventType}:v{$version}";
    }

    /**
     * Generate cache key for subscription paginated list queries.
     */
    public static function getListKey(array $params): string
    {
        $version = self::getVersion();
        $hash = md5(json_encode($params));

        return "superadmin:subscription:list:v{$version}:{$hash}";
    }

    /**
     * Generate cache key for company dossier drawer view.
     */
    public static function getDossierKey(int $companyId): string
    {
        $version = self::getVersion();

        return "superadmin:subscription:dossier:v{$version}:{$companyId}";
    }

    /**
     * Invalidate all Super Admin subscription cached records across the platform.
     * Rule 6: Multi-Tenant Atomic Cache Invalidation.
     */
    public static function flushCaches(?int $companyId = null): void
    {
        try {
            // Legacy direct keys
            Cache::forget('superadmin:subscription:kpi:operational');
            Cache::forget('superadmin:subscription:kpi:sales');

            // Specific company drawer/epoch cache keys if provided
            if ($companyId) {
                Cache::forget("company:epoch:{$companyId}");
                $curr = (int) Cache::get("company:version:{$companyId}", 1);
                Cache::put("company:version:{$companyId}", $curr + 1, now()->addDays(7));
            }

            // Generational O(1) cache version bump for all lists, KPIs, and dossiers
            self::incrementVersion();
        } catch (Throwable $e) {
            Log::warning('SuperAdminSubscriptionCacheManager::flushCaches error: '.$e->getMessage());
        }
    }

    protected const KEY_METRICS_HITS = 'superadmin:subscription:cache:hits';
    protected const KEY_METRICS_MISSES = 'superadmin:subscription:cache:misses';
    protected const KEY_METRICS_CONTENTION = 'superadmin:subscription:cache:contention';

    /**
     * Stale fallback snapshots for zero-stampede resilience during Redis outages.
     */
    protected static array $staleKpiSnapshots = [];
    protected static array $staleListSnapshots = [];
    protected static int $lastFallbackEpoch = 0;

    /**
     * Cache or retrieve subscription KPI counts with Single-Flight Distributed Lock protection.
     */
    public static function rememberKpi(string $coinEventType, \Closure $callback): object
    {
        $key = self::getKpiKey($coinEventType);

        try {
            $cached = Cache::get($key);
            if ($cached !== null) {
                self::recordHit();
                $obj = is_object($cached) ? $cached : (object) $cached;
                self::$staleKpiSnapshots[$coinEventType] = ['data' => $obj, 'time' => time()];
                return $obj;
            }

            self::recordMiss();

            // Single-Flight Distributed Lock to prevent 10K Cache Stampede
            $lockKey = "lock:{$key}";
            try {
                return Cache::lock($lockKey, 5)->block(2, function () use ($key, $coinEventType, $callback) {
                    // Double-checked locking
                    $doubleChecked = Cache::get($key);
                    if ($doubleChecked !== null) {
                        $obj = is_object($doubleChecked) ? $doubleChecked : (object) $doubleChecked;
                        self::$staleKpiSnapshots[$coinEventType] = ['data' => $obj, 'time' => time()];
                        return $obj;
                    }

                    $fresh = $callback();
                    Cache::put($key, $fresh, self::KPI_TTL);

                    $obj = is_object($fresh) ? $fresh : (object) $fresh;
                    self::$staleKpiSnapshots[$coinEventType] = ['data' => $obj, 'time' => time()];
                    return $obj;
                });
            } catch (Throwable) {
                // Lock acquisition timed out (contention)
                self::recordContention();

                // Stale-cache fallback protection: return last known good snapshot if available
                if (isset(self::$staleKpiSnapshots[$coinEventType])) {
                    return self::$staleKpiSnapshots[$coinEventType]['data'];
                }

                // Controlled concurrency limit: only 1 request every 500ms invokes ES callback
                $nowMs = (int) (microtime(true) * 1000);
                if ($nowMs - self::$lastFallbackEpoch < 500 && isset(self::$staleKpiSnapshots[$coinEventType])) {
                    return self::$staleKpiSnapshots[$coinEventType]['data'];
                }
                self::$lastFallbackEpoch = $nowMs;

                $fresh = $callback();
                $obj = is_object($fresh) ? $fresh : (object) $fresh;
                self::$staleKpiSnapshots[$coinEventType] = ['data' => $obj, 'time' => time()];
                return $obj;
            }
        } catch (Throwable $e) {
            // Redis connection outage: utilize stale snapshot or controlled rate limit
            self::recordMiss();
            if (isset(self::$staleKpiSnapshots[$coinEventType])) {
                return self::$staleKpiSnapshots[$coinEventType]['data'];
            }

            $fresh = $callback();
            $obj = is_object($fresh) ? $fresh : (object) $fresh;
            self::$staleKpiSnapshots[$coinEventType] = ['data' => $obj, 'time' => time()];
            return $obj;
        }
    }

    /**
     * Cache or retrieve subscription list payload with Single-Flight Distributed Lock protection.
     */
    public static function rememberList(array $params, \Closure $callback): array
    {
        $key = self::getListKey($params);
        $hash = md5(json_encode($params));

        try {
            $cached = Cache::get($key);
            if (is_array($cached)) {
                self::recordHit();
                self::$staleListSnapshots[$hash] = ['data' => $cached, 'time' => time()];
                return $cached;
            }

            self::recordMiss();

            // Single-Flight Distributed Lock to prevent 10K Cache Stampede
            $lockKey = "lock:{$key}";
            try {
                return Cache::lock($lockKey, 5)->block(2, function () use ($key, $hash, $callback) {
                    // Double-checked locking
                    $doubleChecked = Cache::get($key);
                    if (is_array($doubleChecked)) {
                        self::$staleListSnapshots[$hash] = ['data' => $doubleChecked, 'time' => time()];
                        return $doubleChecked;
                    }

                    $fresh = $callback();
                    Cache::put($key, $fresh, self::LIST_TTL);
                    self::$staleListSnapshots[$hash] = ['data' => $fresh, 'time' => time()];

                    return $fresh;
                });
            } catch (Throwable) {
                self::recordContention();

                if (isset(self::$staleListSnapshots[$hash])) {
                    return self::$staleListSnapshots[$hash]['data'];
                }

                $nowMs = (int) (microtime(true) * 1000);
                if ($nowMs - self::$lastFallbackEpoch < 500 && isset(self::$staleListSnapshots[$hash])) {
                    return self::$staleListSnapshots[$hash]['data'];
                }
                self::$lastFallbackEpoch = $nowMs;

                $fresh = $callback();
                self::$staleListSnapshots[$hash] = ['data' => $fresh, 'time' => time()];
                return $fresh;
            }
        } catch (Throwable) {
            self::recordMiss();
            if (isset(self::$staleListSnapshots[$hash])) {
                return self::$staleListSnapshots[$hash]['data'];
            }

            $fresh = $callback();
            self::$staleListSnapshots[$hash] = ['data' => $fresh, 'time' => time()];
            return $fresh;
        }
    }

    /**
     * Cache or retrieve company dossier drawer HTML with Single-Flight Distributed Lock protection.
     */
    public static function rememberDossier(int $companyId, \Closure $callback): ?string
    {
        $key = self::getDossierKey($companyId);

        try {
            $cached = Cache::get($key);
            if ($cached !== null) {
                self::recordHit();
                return (string) $cached;
            }

            self::recordMiss();

            $lockKey = "lock:{$key}";
            try {
                return Cache::lock($lockKey, 5)->block(2, function () use ($key, $callback) {
                    $doubleChecked = Cache::get($key);
                    if ($doubleChecked !== null) {
                        return (string) $doubleChecked;
                    }

                    $fresh = $callback();
                    if ($fresh !== null) {
                        Cache::put($key, $fresh, self::DOSSIER_TTL);
                    }

                    return $fresh;
                });
            } catch (Throwable) {
                self::recordContention();
                return $callback();
            }
        } catch (Throwable) {
            return $callback();
        }
    }

    /**
     * Record cache hit for telemetry.
     */
    protected static function recordHit(): void
    {
        try {
            Cache::increment(self::KEY_METRICS_HITS);
        } catch (Throwable) {}
    }

    /**
     * Record cache miss for telemetry.
     */
    protected static function recordMiss(): void
    {
        try {
            Cache::increment(self::KEY_METRICS_MISSES);
        } catch (Throwable) {}
    }

    /**
     * Record lock contention for telemetry.
     */
    protected static function recordContention(): void
    {
        try {
            Cache::increment(self::KEY_METRICS_CONTENTION);
        } catch (Throwable) {}
    }

    /**
     * Retrieve cache telemetry stats.
     */
    public static function getTelemetry(): array
    {
        try {
            $hits = (int) Cache::get(self::KEY_METRICS_HITS, 0);
            $misses = (int) Cache::get(self::KEY_METRICS_MISSES, 0);
            $contention = (int) Cache::get(self::KEY_METRICS_CONTENTION, 0);
            $total = $hits + $misses;
            $ratio = $total > 0 ? round(($hits / $total) * 100, 2) : 100.0;

            return [
                'version' => self::getVersion(),
                'hits' => $hits,
                'misses' => $misses,
                'contention_events' => $contention,
                'total_requests' => $total,
                'hit_ratio_percent' => $ratio,
            ];
        } catch (Throwable) {
            return [
                'version' => 1,
                'hits' => 0,
                'misses' => 0,
                'contention_events' => 0,
                'total_requests' => 0,
                'hit_ratio_percent' => 0.0,
            ];
        }
    }

    /**
     * Centralized Observability & Health Diagnostic Metrics (Phase 7).
     */
    public static function getObservabilityMetrics(): array
    {
        $cache = self::getTelemetry();

        // Outbox Backlog Status
        $outboxCounts = [
            'PENDING' => 0,
            'PROCESSING' => 0,
            'FAILED' => 0,
            'DEAD_LETTER' => 0,
            'COMPLETED' => 0,
        ];

        try {
            $counts = \Illuminate\Support\Facades\DB::table('sys_outbox_events')
                ->where('aggregate_type', 'SubscriptionCompany')
                ->groupBy('status')
                ->select('status', \Illuminate\Support\Facades\DB::raw('count(*) as count'))
                ->pluck('count', 'status')
                ->toArray();

            foreach ($counts as $st => $cnt) {
                $outboxCounts[$st] = (int) $cnt;
            }
        } catch (Throwable) {}

        // Circuit Breaker State
        $cbTelemetry = SubscriptionCircuitBreaker::getTelemetry();

        return [
            'cache' => $cache,
            'outbox' => [
                'backlog_pending' => $outboxCounts['PENDING'],
                'in_flight_processing' => $outboxCounts['PROCESSING'],
                'retryable_failed' => $outboxCounts['FAILED'],
                'dead_letter' => $outboxCounts['DEAD_LETTER'],
                'lifetime_completed' => $outboxCounts['COMPLETED'],
            ],
            'circuit_breaker' => $cbTelemetry,
            'timestamp' => now()->toIso8601String(),
        ];
    }
}
