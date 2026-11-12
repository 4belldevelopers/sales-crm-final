<?php

namespace App\Services\SuperAdmin;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class ThemeSettingCircuitBreaker
 *
 * Intent: Complete circuit-breaker state machine for Elasticsearch and Redis operations
 * in the Super Admin Theme Settings subsystem.
 *
 * Resolves the architectural contradiction between 0% MySQL reads and failure fallback:
 * - MODE_NORMAL_CQRS (CLOSED): Normal steady-state operation. 100% of queries route to Elasticsearch / L2 Cache. 0% MySQL reads.
 * - MODE_DEGRADED_ES_OUTAGE (OPEN): Failure threshold exceeded (3 consecutive errors). Fast-fails to emergency cached MySQL fallback immediately without socket timeout lag.
 * - HALF_OPEN: Controlled canary probe interval elapsed (exponential backoff 30s..300s). Tests if Elasticsearch cluster has recovered.
 *
 * Observability: Records fallback read executions for honest reporting and SLA monitoring.
 *
 * Tenancy: Scoped strictly to Super Admin (company_id IS NULL).
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ThemeSettingCircuitBreaker
{
    public const STATE_CLOSED = 'CLOSED';

    public const STATE_OPEN = 'OPEN';

    public const STATE_HALF_OPEN = 'HALF_OPEN';

    public const MODE_NORMAL_CQRS = 'NORMAL_CQRS';

    public const MODE_DEGRADED_ES_OUTAGE = 'DEGRADED_ES_OUTAGE';

    protected const KEY_STATE = 'superadmin:circuit:theme_settings:state';

    protected const KEY_FAILURES = 'superadmin:circuit:theme_settings:failures';

    protected const KEY_SUCCESSES = 'superadmin:circuit:theme_settings:successes';

    protected const KEY_LAST_FAILURE = 'superadmin:circuit:theme_settings:last_failure';

    protected const KEY_FALLBACK_COUNT = 'superadmin:circuit:theme_settings:fallback_count';

    protected const KEY_TRIP_COUNT = 'superadmin:circuit:theme_settings:trip_count';

    protected const KEY_DB_FALLBACK_CACHE = 'superadmin:circuit:theme_settings:db_fallback_cache';

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
                    Log::info('ThemeSettingCircuitBreaker: Circuit recovered and CLOSED.');
                }
            } elseif ($state === self::STATE_CLOSED) {
                Cache::forget(self::KEY_FAILURES);
            }
        } catch (Throwable $e) {
            Log::warning('ThemeSettingCircuitBreaker::recordSuccess error: '.$e->getMessage());
        }
    }

    /**
     * Record an Elasticsearch query failure and trip circuit if threshold exceeded.
     */
    public static function recordFailure(Throwable $e): void
    {
        try {
            Log::error('ThemeSettingCircuitBreaker: Elasticsearch failure detected: '.$e->getMessage());
            Cache::put(self::KEY_LAST_FAILURE, Carbon::now()->toIso8601String(), 86400);

            $state = Cache::get(self::KEY_STATE, self::STATE_CLOSED);

            if ($state === self::STATE_HALF_OPEN) {
                // Canary probe failed; trip straight back to OPEN with exponential penalty
                Cache::increment(self::KEY_TRIP_COUNT);
                self::transitionTo(self::STATE_OPEN);
                Cache::forget(self::KEY_SUCCESSES);
                Log::warning('ThemeSettingCircuitBreaker: Canary probe failed in HALF_OPEN. Tripping back to OPEN.');

                return;
            }

            $failures = (int) Cache::increment(self::KEY_FAILURES);
            if ($failures >= self::FAILURE_THRESHOLD) {
                Cache::increment(self::KEY_TRIP_COUNT);
                self::transitionTo(self::STATE_OPEN);
                Log::alert("ThemeSettingCircuitBreaker: Tripped to OPEN after {$failures} consecutive failures. Activating emergency MySQL fallback.");
            }
        } catch (Throwable $ex) {
            Log::warning('ThemeSettingCircuitBreaker::recordFailure error: '.$ex->getMessage());
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
     * Cache storm protection guard for emergency DB fallback reads.
     * Caches MySQL results for 60 seconds to prevent DB saturation during ES outages.
     */
    public static function rememberDbFallback(\Closure $callback): array
    {
        self::recordFallbackExecution();

        try {
            return Cache::remember(self::KEY_DB_FALLBACK_CACHE, self::DB_FALLBACK_CACHE_TTL, $callback);
        } catch (Throwable) {
            return $callback();
        }
    }

    /**
     * Transition the circuit breaker to a new state.
     */
    protected static function transitionTo(string $newState): void
    {
        Cache::put(self::KEY_STATE, $newState, 86400);
        Log::info("ThemeSettingCircuitBreaker: State transitioned to {$newState}.");
    }

    /**
     * Reset the circuit breaker to clean state.
     */
    public static function reset(): void
    {
        Cache::forget(self::KEY_STATE);
        Cache::forget(self::KEY_FAILURES);
        Cache::forget(self::KEY_SUCCESSES);
        Cache::forget(self::KEY_LAST_FAILURE);
        Cache::forget(self::KEY_FALLBACK_COUNT);
        Cache::forget(self::KEY_TRIP_COUNT);
        Cache::forget(self::KEY_DB_FALLBACK_CACHE);
    }

    /**
     * Export telemetry status for observability and health endpoints.
     */
    public static function getTelemetry(): array
    {
        return [
            'state' => self::getState(),
            'mode' => self::getMode(),
            'is_available' => self::isAvailable(),
            'fallback_count' => self::getFallbackCount(),
            'recovery_timeout_seconds' => self::getRecoveryTimeout(),
            'last_failure' => Cache::get(self::KEY_LAST_FAILURE),
        ];
    }
}
