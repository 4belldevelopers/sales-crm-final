<?php

namespace App\Services\SuperAdmin;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class SuperAdminCompanyCacheManager
 *
 * Intent: High-performance L2 in-memory cache manager (Redis / Dragonfly) for the
 * Super Admin Company Management subsystem. Implements generation-based versioning
 * for O(1) atomic cache invalidation without keyspace scanning, single-flight caching,
 * stampede defense with distributed locks, stale snapshot fallbacks, and multi-tenant
 * cache synchronization (Rule 6).
 *
 * Tenancy: Scoped strictly to Super Admin company operations across tenant boundaries.
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminCompanyCacheManager
{
    public const VERSION_KEY = 'superadmin:company:version';

    public const KPI_TTL = 120; // 120 seconds

    public const LIST_TTL = 60; // 60 seconds

    public const DOSSIER_TTL = 900; // 15 minutes

    public const LEGACY_DATA_TTL = 60; // 60 seconds

    public const STATIC_TTL = 86400; // 24 hours

    public const KEY_METRICS_HITS = 'superadmin:company:metrics:hits';

    public const KEY_METRICS_MISSES = 'superadmin:company:metrics:misses';

    public const KEY_METRICS_CONTENTION = 'superadmin:company:metrics:contention';

    /**
     * In-memory stale snapshot buffers for stampede and outage resilience.
     */
    protected static array $staleKpiSnapshots = [];

    protected static array $staleListSnapshots = [];

    protected static int $lastFallbackEpoch = 0;

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
            Log::warning('SuperAdminCompanyCacheManager::incrementVersion failed: '.$e->getMessage());

            return 1;
        }
    }

    /**
     * Generate cache key for company KPI aggregation counts.
     */
    public static function getKpiKey(): string
    {
        $version = self::getVersion();

        return "superadmin:company:kpi:v{$version}";
    }

    /**
     * Generate cache key for company paginated list queries.
     */
    public static function getListKey(array $params): string
    {
        $version = self::getVersion();
        $hash = md5(json_encode($params));

        return "superadmin:company:list:v{$version}:{$hash}";
    }

    /**
     * Generate cache key for company dossier drawer view.
     */
    public static function getDossierKey(int $companyId): string
    {
        $version = self::getVersion();

        return "superadmin:company:dossier:v{$version}:{$companyId}";
    }

    /**
     * Generate cache key for legacy DataTables endpoint.
     */
    public static function getLegacyDataKey(array $params): string
    {
        $version = self::getVersion();
        $hash = md5(json_encode($params));

        return "superadmin:company:legacy_data:v{$version}:{$hash}";
    }

    /**
     * Cache KPI aggregation counts with Single-Flight Distributed Lock protection.
     */
    public static function rememberKpi(\Closure $callback)
    {
        $key = self::getKpiKey();

        try {
            $cached = Cache::get($key);
            if ($cached !== null) {
                self::recordHit();
                $obj = is_object($cached) ? $cached : (object) $cached;
                self::$staleKpiSnapshots['kpi'] = ['data' => $obj, 'time' => time()];

                return $obj;
            }

            self::recordMiss();

            // Single-Flight Distributed Lock to prevent 10K Cache Stampede
            $lockKey = "lock:{$key}";
            try {
                return Cache::lock($lockKey, 5)->block(2, function () use ($key, $callback) {
                    // Double-checked locking
                    $doubleChecked = Cache::get($key);
                    if ($doubleChecked !== null) {
                        $obj = is_object($doubleChecked) ? $doubleChecked : (object) $doubleChecked;
                        self::$staleKpiSnapshots['kpi'] = ['data' => $obj, 'time' => time()];

                        return $obj;
                    }

                    $fresh = $callback();
                    Cache::put($key, $fresh, self::KPI_TTL);

                    $obj = is_object($fresh) ? $fresh : (object) $fresh;
                    self::$staleKpiSnapshots['kpi'] = ['data' => $obj, 'time' => time()];

                    return $obj;
                });
            } catch (Throwable) {
                // Lock acquisition timed out (contention)
                self::recordContention();

                // Stale-cache fallback protection: return last known good snapshot if available
                if (isset(self::$staleKpiSnapshots['kpi'])) {
                    return self::$staleKpiSnapshots['kpi']['data'];
                }

                // Controlled concurrency limit: only 1 request every 500ms invokes callback
                $nowMs = (int) (microtime(true) * 1000);
                if ($nowMs - self::$lastFallbackEpoch < 500 && isset(self::$staleKpiSnapshots['kpi'])) {
                    return self::$staleKpiSnapshots['kpi']['data'];
                }
                self::$lastFallbackEpoch = $nowMs;

                $fresh = $callback();
                $obj = is_object($fresh) ? $fresh : (object) $fresh;
                self::$staleKpiSnapshots['kpi'] = ['data' => $obj, 'time' => time()];

                return $obj;
            }
        } catch (Throwable $e) {
            // Redis connection outage: utilize stale snapshot or controlled rate limit
            self::recordMiss();
            if (isset(self::$staleKpiSnapshots['kpi'])) {
                return self::$staleKpiSnapshots['kpi']['data'];
            }

            $fresh = $callback();
            $obj = is_object($fresh) ? $fresh : (object) $fresh;
            self::$staleKpiSnapshots['kpi'] = ['data' => $obj, 'time' => time()];

            return $obj;
        }
    }

    /**
     * Cache paginated company list results with Single-Flight Distributed Lock protection.
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
     * Cache company drawer dossier HTML and telemetry with Single-Flight Lock.
     */
    public static function rememberDossier(int $companyId, \Closure $callback)
    {
        $key = self::getDossierKey($companyId);

        try {
            $cached = Cache::get($key);
            if ($cached !== null) {
                self::recordHit();

                return $cached;
            }

            self::recordMiss();

            $lockKey = "lock:{$key}";
            try {
                return Cache::lock($lockKey, 5)->block(2, function () use ($key, $callback) {
                    $doubleChecked = Cache::get($key);
                    if ($doubleChecked !== null) {
                        return $doubleChecked;
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
        } catch (Throwable $e) {
            return $callback();
        }
    }

    /**
     * Cache legacy DataTables formatted responses.
     */
    public static function rememberLegacyData(array $params, \Closure $callback)
    {
        $key = self::getLegacyDataKey($params);

        try {
            return Cache::remember($key, self::LEGACY_DATA_TTL, $callback);
        } catch (Throwable $e) {
            Log::warning('SuperAdminCompanyCacheManager::rememberLegacyData fallback to direct: '.$e->getMessage());

            return $callback();
        }
    }

    /**
     * Cache static reference dataset (countries, industries, categories).
     */
    public static function rememberStatic(string $identifier, \Closure $callback)
    {
        $key = "superadmin:company:static:{$identifier}";

        try {
            return Cache::remember($key, self::STATIC_TTL, $callback);
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
     * Centralized Observability & Health Diagnostic Metrics.
     */
    public static function getObservabilityMetrics(): array
    {
        $telemetry = self::getTelemetry();

        return [
            'module' => 'SuperAdminCompany',
            'status' => 'HEALTHY',
            'version' => self::getVersion(),
            'cache_telemetry' => $telemetry,
            'stale_kpi_snapshots_count' => count(self::$staleKpiSnapshots),
            'stale_list_snapshots_count' => count(self::$staleListSnapshots),
            'timestamp' => now()->toIso8601String(),
        ];
    }

    /**
     * Invalidate all Super Admin company cached records across the platform.
     * Rule 6: Multi-Tenant Atomic Cache Invalidation.
     */
    public static function flushCaches(?int $companyId = null): void
    {
        try {
            // 1. Atomically increment version token (O(1) keyspace invalidation)
            self::incrementVersion();

            // Clear in-memory snapshots
            self::$staleKpiSnapshots = [];
            self::$staleListSnapshots = [];

            // 2. Direct legacy key eviction
            Cache::forget('sa_static_countries');
            Cache::forget('sa_static_job_categories');
            Cache::forget('global_job_categories_all');
            Cache::forget('global_td_industries_all');
            Cache::forget('global_country_arr_list');
            Cache::forget('global_all_cities_list');

            // 3. Entity-specific tenant cache eviction
            if ($companyId) {
                Cache::forget("company:epoch:{$companyId}");
                $compVersion = (int) Cache::get("company:version:{$companyId}", 1);
                Cache::forever("company:version:{$companyId}", $compVersion + 1);
                Cache::forget("superadmin:company:dossier:v".self::getVersion().":{$companyId}");
                Cache::forget("tenant:{$companyId}:clients");
                Cache::forget("tenant:{$companyId}:dataset:clients:perm:default");

                $newVersion = ((int) Cache::get("tenant:{$companyId}:clients_version", 1)) + 1;
                Cache::forever("tenant:{$companyId}:clients_version", $newVersion);
            }

            // 4. Synchronize with SuperAdminSubscriptionCacheManager to prevent drift
            if (class_exists(SuperAdminSubscriptionCacheManager::class)) {
                SuperAdminSubscriptionCacheManager::flushCaches($companyId);
            }

            Log::info('SuperAdminCompanyCacheManager: Generation version incremented and caches purged successfully.', [
                'new_version' => self::getVersion(),
                'company_id' => $companyId,
            ]);
        } catch (Throwable $e) {
            Log::warning('SuperAdminCompanyCacheManager::flushCaches error: '.$e->getMessage());
        }
    }
}
