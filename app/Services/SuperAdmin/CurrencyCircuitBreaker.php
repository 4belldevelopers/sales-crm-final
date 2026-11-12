<?php

namespace App\Services\SuperAdmin;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class CurrencyCircuitBreaker
 *
 * Intent: Complete circuit-breaker state machine for Elasticsearch and Redis operations
 * in the Super Admin Currency Settings & Global FX Engine subsystem.
 *
 * Resolves the architectural contradiction between 0% MySQL reads and failure fallback:
 * - MODE_NORMAL_CQRS (CLOSED): Normal steady-state operation. 100% of queries route to Elasticsearch / L2 Cache. 0% MySQL reads.
 * - MODE_DEGRADED_ES_OUTAGE (OPEN): Failure threshold exceeded (3 consecutive errors). Fast-fails to emergency cached MySQL fallback immediately without socket timeout lag.
 * - HALF_OPEN: Controlled canary probe interval elapsed (exponential backoff 30s..300s). Tests if Elasticsearch cluster has recovered.
 *
 * Observability: Records fallback read executions for honest reporting and SLA monitoring.
 *
 * Tenancy: Scoped strictly to Super Admin root currency infrastructure.
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class CurrencyCircuitBreaker
{
    public const STATE_CLOSED = 'CLOSED';

    public const STATE_OPEN = 'OPEN';

    public const STATE_HALF_OPEN = 'HALF_OPEN';

    public const MODE_NORMAL_CQRS = 'NORMAL_CQRS';

    public const MODE_DEGRADED_ES_OUTAGE = 'DEGRADED_ES_OUTAGE';

    protected const KEY_STATE = 'superadmin:circuit:currencies:state';

    protected const KEY_FAILURES = 'superadmin:circuit:currencies:failures';

    protected const KEY_SUCCESSES = 'superadmin:circuit:currencies:successes';

    protected const KEY_LAST_FAILURE = 'superadmin:circuit:currencies:last_failure';

    protected const KEY_FALLBACK_COUNT = 'superadmin:circuit:currencies:fallback_count';

    protected const KEY_TRIP_COUNT = 'superadmin:circuit:currencies:trip_count';

    public const FAILURE_THRESHOLD = 3;

    public const SUCCESS_THRESHOLD = 2; // Require 2 consecutive canary successes before closing circuit

    public const BASE_RECOVERY_TIMEOUT_SECONDS = 30;

    public const MAX_RECOVERY_TIMEOUT_SECONDS = 300;

    /**
     * Compute exponential recovery timeout based on consecutive circuit trips.
     */
    public static function getRecoveryTimeout(): int
    {
        try {
            $trips = (int) Cache::get(self::KEY_TRIP_COUNT, 0);
            $timeout = self::BASE_RECOVERY_TIMEOUT_SECONDS * (int) pow(2, min($trips, 4));

            return min($timeout, self::MAX_RECOVERY_TIMEOUT_SECONDS);
        } catch (\Throwable) {
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
        } catch (\Throwable) {
            // If Redis itself is unreachable, return OPEN to avoid blocking requests
            return self::STATE_OPEN;
        }
    }

    /**
     * Determine active CQRS operating mode for honest reporting.
     */
    public static function getOperatingMode(): string
    {
        $state = self::getState();

        return ($state === self::STATE_CLOSED)
            ? self::MODE_NORMAL_CQRS
            : self::MODE_DEGRADED_ES_OUTAGE;
    }

    /**
     * Get consecutive failure count recorded in current window.
     */
    public static function getFailures(): int
    {
        try {
            return (int) Cache::get(self::KEY_FAILURES, 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * Get consecutive success count recorded during HALF_OPEN probes.
     */
    public static function getSuccesses(): int
    {
        try {
            return (int) Cache::get(self::KEY_SUCCESSES, 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * Get total fallback reads served during circuit open periods.
     */
    public static function getFallbackReadCount(): int
    {
        try {
            return (int) Cache::get(self::KEY_FALLBACK_COUNT, 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * Record a fallback read execution for honest observability metrics.
     */
    public static function recordFallbackRead(): void
    {
        try {
            Cache::increment(self::KEY_FALLBACK_COUNT);
        } catch (\Throwable) {
        }
    }

    /**
     * Determine if queries are allowed to attempt Elasticsearch execution.
     */
    public static function isAvailable(): bool
    {
        $state = self::getState();

        return $state === self::STATE_CLOSED || $state === self::STATE_HALF_OPEN;
    }

    /**
     * Record a successful operation to reset or close the circuit.
     */
    public static function recordSuccess(): void
    {
        try {
            $state = self::getState();

            if ($state === self::STATE_HALF_OPEN) {
                $successes = (int) Cache::increment(self::KEY_SUCCESSES);
                if ($successes >= self::SUCCESS_THRESHOLD) {
                    self::transitionTo(self::STATE_CLOSED);
                    Cache::forget(self::KEY_FAILURES);
                    Cache::forget(self::KEY_SUCCESSES);
                    Cache::forget(self::KEY_TRIP_COUNT);
                    Log::info('CurrencyCircuitBreaker: Circuit recovered from HALF_OPEN and transitioned to CLOSED. Mode: NORMAL_CQRS (0% MySQL reads).');
                }
            } elseif ($state === self::STATE_CLOSED) {
                Cache::forget(self::KEY_FAILURES);
            }
        } catch (\Throwable) {
        }
    }

    /**
     * Record a failure and evaluate if the circuit should trip to OPEN with exponential backoff.
     */
    public static function recordFailure(?\Throwable $e = null): void
    {
        try {
            $state = self::getState();
            Cache::put(self::KEY_LAST_FAILURE, Carbon::now()->toIso8601String(), 86400);

            if ($state === self::STATE_HALF_OPEN) {
                // Immediate trip back to OPEN if canary fails, and increment trip count for backoff
                Cache::increment(self::KEY_TRIP_COUNT);
                self::transitionTo(self::STATE_OPEN);
                Cache::forget(self::KEY_SUCCESSES);
                Log::warning('CurrencyCircuitBreaker: Canary probe failed; circuit tripped back to OPEN with backoff: '.self::getRecoveryTimeout().'s', [
                    'error' => $e?->getMessage(),
                ]);

                return;
            }

            $failures = (int) Cache::increment(self::KEY_FAILURES);
            if ($failures >= self::FAILURE_THRESHOLD && $state !== self::STATE_OPEN) {
                Cache::increment(self::KEY_TRIP_COUNT);
                self::transitionTo(self::STATE_OPEN);
                Log::critical('CurrencyCircuitBreaker: Failure threshold reached ('.$failures.'); circuit tripped to OPEN. Mode switched to DEGRADED_ES_OUTAGE. Fast-failing to cached MySQL fallback.', [
                    'error' => $e?->getMessage(),
                ]);
            }
        } catch (\Throwable) {
        }
    }

    /**
     * Transition state machine to target state.
     */
    public static function transitionTo(string $state): void
    {
        try {
            Cache::put(self::KEY_STATE, $state, 86400);
        } catch (\Throwable) {
        }
    }

    /**
     * Reset breaker state for testing and administrative maintenance.
     */
    public static function reset(): void
    {
        try {
            Cache::forget(self::KEY_STATE);
            Cache::forget(self::KEY_FAILURES);
            Cache::forget(self::KEY_SUCCESSES);
            Cache::forget(self::KEY_LAST_FAILURE);
            Cache::forget(self::KEY_FALLBACK_COUNT);
            Cache::forget(self::KEY_TRIP_COUNT);
        } catch (\Throwable) {
        }
    }
}
