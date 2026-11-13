<?php

namespace App\Services\SuperAdmin;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class SubscriptionCircuitBreaker
 *
 * Intent: Complete circuit-breaker state machine for Elasticsearch and Redis operations
 * in the Super Admin Subscription subsystem.
 *
 * Resolves the architectural contradiction between 0% MySQL reads and cluster failure fallback:
 * - MODE_NORMAL_CQRS (CLOSED): Normal steady-state operation. 100% of read queries route to Elasticsearch / L2 Cache. 0% MySQL reads.
 * - MODE_DEGRADED_ES_OUTAGE (OPEN): Failure threshold exceeded (3 consecutive errors). Fast-fails to emergency cached MySQL fallback immediately without socket timeout lag.
 * - HALF_OPEN: Controlled canary probe interval elapsed (exponential backoff 30s..300s). Tests if Elasticsearch cluster has recovered.
 *
 * Observability: Records fallback read executions for honest reporting and SLA monitoring.
 *
 * Tenancy: Scoped strictly to Super Admin subscription management.
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SubscriptionCircuitBreaker
{
    public const STATE_CLOSED = 'CLOSED';

    public const STATE_OPEN = 'OPEN';

    public const STATE_HALF_OPEN = 'HALF_OPEN';

    public const MODE_NORMAL_CQRS = 'NORMAL_CQRS';

    public const MODE_DEGRADED_ES_OUTAGE = 'DEGRADED_ES_OUTAGE';

    protected const KEY_STATE = 'superadmin:circuit:subscription:state';

    protected const KEY_FAILURES = 'superadmin:circuit:subscription:failures';

    protected const KEY_SUCCESSES = 'superadmin:circuit:subscription:successes';

    protected const KEY_LAST_FAILURE = 'superadmin:circuit:subscription:last_failure';

    protected const KEY_FALLBACK_COUNT = 'superadmin:circuit:subscription:fallback_count';

    protected const KEY_TRIP_COUNT = 'superadmin:circuit:subscription:trip_count';

    protected const KEY_DB_FALLBACK_CACHE = 'superadmin:circuit:subscription:db_fallback_cache';

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
     * Determine active CQRS operating mode for telemetry reporting.
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
                    Log::info('SubscriptionCircuitBreaker: Circuit recovered and CLOSED.');
                }
            } elseif ($state === self::STATE_CLOSED) {
                Cache::forget(self::KEY_FAILURES);
            }
        } catch (Throwable $e) {
            Log::warning('SubscriptionCircuitBreaker::recordSuccess error: '.$e->getMessage());
        }
    }

    /**
     * Record an Elasticsearch query failure and trip circuit if threshold exceeded.
     */
    public static function recordFailure(Throwable $e): void
    {
        try {
            Log::error('SubscriptionCircuitBreaker: Elasticsearch failure detected: '.$e->getMessage());
            Cache::put(self::KEY_LAST_FAILURE, Carbon::now()->toIso8601String(), 86400);

            $state = Cache::get(self::KEY_STATE, self::STATE_CLOSED);

            if ($state === self::STATE_HALF_OPEN) {
                // Canary probe failed; trip straight back to OPEN with exponential penalty
                Cache::increment(self::KEY_TRIP_COUNT);
                self::transitionTo(self::STATE_OPEN);
                Cache::forget(self::KEY_SUCCESSES);
                Log::warning('SubscriptionCircuitBreaker: Canary probe failed in HALF_OPEN. Tripping back to OPEN.');

                return;
            }

            $failures = (int) Cache::increment(self::KEY_FAILURES);
            if ($failures >= self::FAILURE_THRESHOLD) {
                Cache::increment(self::KEY_TRIP_COUNT);
                self::transitionTo(self::STATE_OPEN);
                Log::alert("SubscriptionCircuitBreaker: Tripped to OPEN after {$failures} consecutive failures. Activating emergency MySQL fallback.");
            }
        } catch (Throwable $ex) {
            Log::warning('SubscriptionCircuitBreaker::recordFailure error: '.$ex->getMessage());
        }
    }

    /**
     * Record that an emergency database fallback read occurred.
     */
    public static function recordFallbackExecution(): void
    {
        try {
            Cache::increment(self::KEY_FALLBACK_COUNT);
        } catch (Throwable) {
            // Non-critical telemetry counter
        }
    }

    /**
     * Get the count of emergency fallback reads executed during degraded mode.
     */
    public static function getFallbackCount(): int
    {
        try {
            return (int) Cache::get(self::KEY_FALLBACK_COUNT, 0);
        } catch (Throwable) {
            return 0;
        }
    }

    /**
     * Cache emergency database fallback responses to prevent database storms under 10K concurrency.
     */
    public static function rememberDbFallback(string $keySuffix, \Closure $callback)
    {
        self::recordFallbackExecution();
        $cacheKey = self::KEY_DB_FALLBACK_CACHE.':'.$keySuffix;

        try {
            return Cache::remember($cacheKey, self::DB_FALLBACK_CACHE_TTL, $callback);
        } catch (Throwable) {
            return $callback();
        }
    }

    /**
     * Transition the circuit to a new state.
     */
    protected static function transitionTo(string $newState): void
    {
        try {
            Cache::put(self::KEY_STATE, $newState, 86400);
            Log::info("SubscriptionCircuitBreaker: Transitioned state to {$newState}");
        } catch (Throwable $e) {
            Log::warning('SubscriptionCircuitBreaker::transitionTo error: '.$e->getMessage());
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
     * Alias for getTelemetry() for backward compatibility.
     */
    public static function getStatus(): array
    {
        return self::getTelemetry();
    }

    /**
     * Manually reset the circuit breaker back to CLOSED.
     */
    public static function reset(): void
    {
        try {
            Cache::forget(self::KEY_STATE);
            Cache::forget(self::KEY_FAILURES);
            Cache::forget(self::KEY_SUCCESSES);
            Cache::forget(self::KEY_LAST_FAILURE);
            Cache::forget(self::KEY_TRIP_COUNT);
            Cache::forget(self::KEY_FALLBACK_COUNT);
            Log::info('SubscriptionCircuitBreaker: Manually reset to CLOSED.');
        } catch (Throwable $e) {
            Log::warning('SubscriptionCircuitBreaker::reset error: '.$e->getMessage());
        }
    }
}
