<?php

namespace App\Services\SuperAdmin;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class LanguageCircuitBreaker
 *
 * Intent: Complete circuit-breaker state machine for Elasticsearch and Redis operations
 * in the Super Admin Language & Localization Hub subsystem.
 *
 * Resolves the architectural contradiction between 0% MySQL reads and failure fallback:
 * - MODE_NORMAL_CQRS (CLOSED): Normal steady-state operation. 100% of queries route to Elasticsearch / L2 Cache. 0% MySQL reads.
 * - MODE_DEGRADED_ES_OUTAGE (OPEN): Failure threshold exceeded (3 consecutive errors). Fast-fails to emergency cached MySQL fallback immediately without socket timeout lag.
 * - HALF_OPEN: Controlled canary probe interval elapsed (exponential backoff 30s..300s). Tests if Elasticsearch cluster has recovered.
 *
 * Observability: Records fallback read executions for honest reporting and SLA monitoring.
 *
 * Tenancy: Scoped strictly to Super Admin root localization infrastructure.
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class LanguageCircuitBreaker
{
    public const STATE_CLOSED = 'CLOSED';

    public const STATE_OPEN = 'OPEN';

    public const STATE_HALF_OPEN = 'HALF_OPEN';

    public const MODE_NORMAL_CQRS = 'NORMAL_CQRS';

    public const MODE_DEGRADED_ES_OUTAGE = 'DEGRADED_ES_OUTAGE';

    protected const KEY_STATE = 'superadmin:circuit:languages:state';

    protected const KEY_FAILURES = 'superadmin:circuit:languages:failures';

    protected const KEY_SUCCESSES = 'superadmin:circuit:languages:successes';

    protected const KEY_LAST_FAILURE = 'superadmin:circuit:languages:last_failure';

    protected const KEY_FALLBACK_COUNT = 'superadmin:circuit:languages:fallback_count';

    protected const KEY_TRIP_COUNT = 'superadmin:circuit:languages:trip_count';

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
            return self::STATE_OPEN;
        }
    }

    /**
     * Determine active CQRS operating mode for honest reporting.
     */
    public static function getMode(): string
    {
        return self::getState() === self::STATE_CLOSED
            ? self::MODE_NORMAL_CQRS
            : self::MODE_DEGRADED_ES_OUTAGE;
    }

    /**
     * Check whether Elasticsearch is available for queries.
     */
    public static function isAvailable(): bool
    {
        $state = self::getState();

        return $state === self::STATE_CLOSED || $state === self::STATE_HALF_OPEN;
    }

    /**
     * Record a successful operation against Elasticsearch.
     */
    public static function recordSuccess(): void
    {
        try {
            $state = self::getState();

            if ($state === self::STATE_HALF_OPEN) {
                $consecutiveSuccesses = (int) Cache::increment(self::KEY_SUCCESSES);
                if ($consecutiveSuccesses >= self::SUCCESS_THRESHOLD) {
                    self::transitionTo(self::STATE_CLOSED);
                    Cache::forget(self::KEY_FAILURES);
                    Cache::forget(self::KEY_SUCCESSES);
                    Cache::forget(self::KEY_TRIP_COUNT);
                    Log::info('LanguageCircuitBreaker: Canary probe succeeded. Circuit transitioned to CLOSED.');
                }
            } elseif ($state === self::STATE_CLOSED) {
                Cache::forget(self::KEY_FAILURES);
            }
        } catch (\Throwable $e) {
            Log::warning('LanguageCircuitBreaker::recordSuccess error: '.$e->getMessage());
        }
    }

    /**
     * Record a failed operation against Elasticsearch.
     */
    public static function recordFailure(\Throwable $e): void
    {
        try {
            Cache::put(self::KEY_LAST_FAILURE, Carbon::now()->toIso8601String(), 86400);
            $consecutiveFailures = (int) Cache::increment(self::KEY_FAILURES);

            $state = self::getState();

            if ($state === self::STATE_HALF_OPEN || $consecutiveFailures >= self::FAILURE_THRESHOLD) {
                if ($state !== self::STATE_OPEN) {
                    Cache::increment(self::KEY_TRIP_COUNT);
                    self::transitionTo(self::STATE_OPEN);
                    $timeout = self::getRecoveryTimeout();
                    Log::error("LanguageCircuitBreaker: Circuit tripped to OPEN. Entering degraded DB fallback mode for {$timeout}s. Reason: {$e->getMessage()}");
                }
            }
        } catch (\Throwable $loggingError) {
            Log::error('LanguageCircuitBreaker::recordFailure logging error: '.$loggingError->getMessage());
        }
    }

    /**
     * Explicitly record an emergency MySQL fallback read execution.
     */
    public static function recordFallback(): void
    {
        try {
            Cache::increment(self::KEY_FALLBACK_COUNT);
        } catch (\Throwable) {
            // Non-critical telemetry
        }
    }

    /**
     * Get cumulative fallback count.
     */
    public static function getFallbackCount(): int
    {
        try {
            return (int) Cache::get(self::KEY_FALLBACK_COUNT, 0);
        } catch (\Throwable) {
            return 0;
        }
    }

    /**
     * Transition the circuit breaker to a new state.
     */
    public static function transitionTo(string $newState): void
    {
        try {
            Cache::put(self::KEY_STATE, $newState, 86400);
            if ($newState === self::STATE_HALF_OPEN) {
                Cache::put(self::KEY_SUCCESSES, 0, 86400);
            }
        } catch (\Throwable $e) {
            Log::warning('LanguageCircuitBreaker::transitionTo error: '.$e->getMessage());
        }
    }

    /**
     * Reset the circuit breaker to CLOSED (normal) state.
     */
    public static function reset(): void
    {
        try {
            Cache::put(self::KEY_STATE, self::STATE_CLOSED, 86400);
            Cache::forget(self::KEY_FAILURES);
            Cache::forget(self::KEY_SUCCESSES);
            Cache::forget(self::KEY_LAST_FAILURE);
            Cache::forget(self::KEY_FALLBACK_COUNT);
            Cache::forget(self::KEY_TRIP_COUNT);
        } catch (\Throwable $e) {
            Log::warning('LanguageCircuitBreaker::reset error: '.$e->getMessage());
        }
    }
}
