<?php

namespace App\Services\SuperAdmin;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class FeatureCircuitBreaker
 *
 * Intent: Complete circuit-breaker state machine for Elasticsearch and Redis operations
 * in the Super Admin Platform Features & Expiration Rules subsystem.
 *
 * Resolves the architectural contradiction between 0% MySQL reads and failure fallback:
 * - MODE_NORMAL_CQRS (CLOSED): Normal steady-state operation. 100% of queries route to Elasticsearch / L2 Cache. 0% MySQL reads.
 * - MODE_DEGRADED_ES_OUTAGE (OPEN): Failure threshold exceeded (3 consecutive errors). Fast-fails to emergency cached MySQL fallback immediately without socket timeout lag.
 * - HALF_OPEN: Controlled canary probe interval elapsed (exponential backoff 30s..300s). Tests if Elasticsearch cluster has recovered.
 *
 * Observability: Records fallback read executions for honest reporting and SLA monitoring.
 *
 * Tenancy: Scoped strictly to Super Admin (Global System Policies).
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class FeatureCircuitBreaker
{
    public const STATE_CLOSED = 'CLOSED';

    public const STATE_OPEN = 'OPEN';

    public const STATE_HALF_OPEN = 'HALF_OPEN';

    public const MODE_NORMAL_CQRS = 'NORMAL_CQRS';

    public const MODE_DEGRADED_ES_OUTAGE = 'DEGRADED_ES_OUTAGE';

    protected const KEY_STATE = 'superadmin:circuit:features_list:state';

    protected const KEY_FAILURES = 'superadmin:circuit:features_list:failures';

    protected const KEY_SUCCESSES = 'superadmin:circuit:features_list:successes';

    protected const KEY_LAST_FAILURE = 'superadmin:circuit:features_list:last_failure';

    protected const KEY_FALLBACK_COUNT = 'superadmin:circuit:features_list:fallback_count';

    protected const KEY_TRIP_COUNT = 'superadmin:circuit:features_list:trip_count';

    protected const KEY_DB_FALLBACK_CACHE = 'superadmin:circuit:features_list:db_fallback_cache';

    public const FAILURE_THRESHOLD = 3;

    public const SUCCESS_THRESHOLD = 2; // Require 2 consecutive canary successes before closing circuit

    public const BASE_RECOVERY_TIMEOUT_SECONDS = 30;

    public const MAX_RECOVERY_TIMEOUT_SECONDS = 300;

    public const DB_FALLBACK_CACHE_TTL = 60; // 60 seconds storm guard

    /**
     * Compute exponential recovery timeout based on consecutive circuit trips.
     */
    public static function getRecoveryTimeout(): int
    {
        try {
            $trips = (int) Cache::get(self::KEY_TRIP_COUNT, 0);
            $timeout = self::BASE_RECOVERY_TIMEOUT_SECONDS * (int) pow(2, min($trips, 4));

            return min($timeout, self::MAX_RECOVERY_TIMEOUT_SECONDS);
        } catch (Throwable) {
            return self::BASE_RECOVERY_TIMEOUT_SECONDS;
        }
    }

    /**
     * Get the current circuit breaker state.
     */
    public static function getState(): string
    {
        try {
            $state = Cache::get(self::KEY_STATE, self::STATE_CLOSED);

            if ($state === self::STATE_OPEN) {
                $lastFailure = Cache::get(self::KEY_LAST_FAILURE);
                $timeout = self::getRecoveryTimeout();
                if ($lastFailure && Carbon::parse($lastFailure)->addSeconds($timeout)->isPast()) {
                    self::transitionTo(self::STATE_HALF_OPEN);

                    return self::STATE_HALF_OPEN;
                }
            }

            return $state;
        } catch (Throwable) {
            // If Redis itself is unreachable, return OPEN to avoid blocking requests
            return self::STATE_OPEN;
        }
    }

    /**
     * Determine active CQRS operating mode for honest reporting.
     */
    public static function getMode(): string
    {
        $state = self::getState();

        return ($state === self::STATE_OPEN)
            ? self::MODE_DEGRADED_ES_OUTAGE
            : self::MODE_NORMAL_CQRS;
    }

    /**
     * Check if Elasticsearch is available to receive requests.
     */
    public static function isAvailable(): bool
    {
        $state = self::getState();

        return $state === self::STATE_CLOSED || $state === self::STATE_HALF_OPEN;
    }

    /**
     * Record a successful Elasticsearch query execution.
     */
    public static function recordSuccess(): void
    {
        try {
            $state = Cache::get(self::KEY_STATE, self::STATE_CLOSED);

            if ($state === self::STATE_HALF_OPEN) {
                $successes = (int) Cache::increment(self::KEY_SUCCESSES);
                if ($successes >= self::SUCCESS_THRESHOLD) {
                    self::transitionTo(self::STATE_CLOSED);
                    Cache::forget(self::KEY_FAILURES);
                    Cache::forget(self::KEY_SUCCESSES);
                    Cache::forget(self::KEY_TRIP_COUNT);
                    Log::info('FeatureCircuitBreaker: Circuit recovered and CLOSED.');
                }
            } else {
                // Decay failures counter on success
                Cache::forget(self::KEY_FAILURES);
            }
        } catch (Throwable $e) {
            Log::warning('FeatureCircuitBreaker::recordSuccess cache error: '.$e->getMessage());
        }
    }

    /**
     * Record an Elasticsearch operation failure.
     */
    public static function recordFailure(Throwable $e): void
    {
        try {
            Cache::put(self::KEY_LAST_FAILURE, Carbon::now()->toIso8601String(), 86400);
            $failures = (int) Cache::increment(self::KEY_FAILURES);

            $currentState = Cache::get(self::KEY_STATE, self::STATE_CLOSED);

            if ($currentState === self::STATE_HALF_OPEN) {
                // Canary failed -> trip back to OPEN immediately with trip count increment
                Cache::increment(self::KEY_TRIP_COUNT);
                self::transitionTo(self::STATE_OPEN);
                Cache::forget(self::KEY_SUCCESSES);
                Log::warning('FeatureCircuitBreaker: Canary probe failed in HALF_OPEN. Retripping to OPEN. Reason: '.$e->getMessage());
            } elseif ($failures >= self::FAILURE_THRESHOLD && $currentState === self::STATE_CLOSED) {
                Cache::increment(self::KEY_TRIP_COUNT);
                self::transitionTo(self::STATE_OPEN);
                Log::error('FeatureCircuitBreaker: Failure threshold reached ('.$failures.' failures). Circuit tripped to OPEN. ES outage detected: '.$e->getMessage());
            }
        } catch (Throwable $cacheEx) {
            Log::warning('FeatureCircuitBreaker::recordFailure cache error: '.$cacheEx->getMessage());
        }
    }

    /**
     * Transition the circuit to a new operational state.
     */
    public static function transitionTo(string $newState): void
    {
        try {
            Cache::forever(self::KEY_STATE, $newState);
            Log::info("FeatureCircuitBreaker: Transitioned state to {$newState}");
        } catch (Throwable $e) {
            Log::warning('FeatureCircuitBreaker::transitionTo cache error: '.$e->getMessage());
        }
    }

    /**
     * Execute emergency database fallback with storm guard cache.
     * Caches MySQL results for 60 seconds to protect DB pool from query stampedes during ES outages.
     *
     * @template T
     *
     * @param  \Closure(): T  $callback
     * @return T
     */
    public static function rememberDbFallback(\Closure $callback, int $ttl = self::DB_FALLBACK_CACHE_TTL): mixed
    {
        try {
            Cache::increment(self::KEY_FALLBACK_COUNT);
        } catch (Throwable) {
            // Ignore metric increment failure
        }

        try {
            return Cache::remember(self::KEY_DB_FALLBACK_CACHE, $ttl, $callback);
        } catch (Throwable) {
            // If cache store is down, invoke callback directly
            return $callback();
        }
    }

    /**
     * Retrieve telemetry snapshot for honest system reporting.
     */
    public static function getTelemetry(): array
    {
        try {
            return [
                'state' => self::getState(),
                'mode' => self::getMode(),
                'failures' => (int) Cache::get(self::KEY_FAILURES, 0),
                'successes' => (int) Cache::get(self::KEY_SUCCESSES, 0),
                'trip_count' => (int) Cache::get(self::KEY_TRIP_COUNT, 0),
                'fallback_count' => (int) Cache::get(self::KEY_FALLBACK_COUNT, 0),
                'last_failure' => Cache::get(self::KEY_LAST_FAILURE),
                'recovery_timeout_sec' => self::getRecoveryTimeout(),
            ];
        } catch (Throwable) {
            return [
                'state' => self::STATE_OPEN,
                'mode' => self::MODE_DEGRADED_ES_OUTAGE,
                'error' => 'Cache unreachable',
            ];
        }
    }

    /**
     * Force reset circuit breaker to closed state (for testing / admin recovery).
     */
    public static function reset(): void
    {
        try {
            Cache::forget(self::KEY_STATE);
            Cache::forget(self::KEY_FAILURES);
            Cache::forget(self::KEY_SUCCESSES);
            Cache::forget(self::KEY_LAST_FAILURE);
            Cache::forget(self::KEY_TRIP_COUNT);
            Cache::forget(self::KEY_DB_FALLBACK_CACHE);
            Log::info('FeatureCircuitBreaker: Manually reset to CLOSED state.');
        } catch (Throwable $e) {
            Log::warning('FeatureCircuitBreaker::reset cache error: '.$e->getMessage());
        }
    }
}
