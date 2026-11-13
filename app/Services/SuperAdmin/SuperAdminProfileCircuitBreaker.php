<?php

namespace App\Services\SuperAdmin;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class SuperAdminProfileCircuitBreaker
 *
 * Intent: Complete circuit-breaker state machine for Elasticsearch read operations
 * in the Super Admin Root Profile & Identity subsystem.
 *
 * Resolves the architectural contradiction between 0% MySQL reads and failure fallback:
 * - CLOSED: Normal steady-state operation. 100% of queries route to Elasticsearch / L2 Cache. 0% MySQL reads.
 * - OPEN: Failure threshold exceeded (3 consecutive errors). Fast-fails to emergency MySQL fallback immediately without socket timeout lag.
 * - HALF_OPEN: Probe interval elapsed (30s). Allows a canary request to test if Elasticsearch cluster has recovered.
 *
 * Observability: Records fallback read executions for honest reporting and SLA monitoring.
 *
 * Tenancy: Scoped strictly to Super Admin root infrastructure.
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminProfileCircuitBreaker
{
    public const STATE_CLOSED = 'CLOSED';

    public const STATE_OPEN = 'OPEN';

    public const STATE_HALF_OPEN = 'HALF_OPEN';

    protected const KEY_STATE = 'superadmin:circuit:profile:state';

    protected const KEY_FAILURES = 'superadmin:circuit:profile:failures';

    protected const KEY_SUCCESSES = 'superadmin:circuit:profile:successes';

    protected const KEY_LAST_FAILURE = 'superadmin:circuit:profile:last_failure';

    protected const KEY_FALLBACK_COUNT = 'superadmin:circuit:profile:fallback_count';

    public const FAILURE_THRESHOLD = 3;

    public const SUCCESS_THRESHOLD = 2; // Require 2 consecutive canary successes before closing circuit

    public const RECOVERY_TIMEOUT_SECONDS = 30;

    /**
     * Get the current circuit breaker state.
     */
    public static function getState(): string
    {
        $state = Cache::get(self::KEY_STATE, self::STATE_CLOSED);

        if ($state === self::STATE_OPEN) {
            $lastFailure = Cache::get(self::KEY_LAST_FAILURE);
            if ($lastFailure && Carbon::parse($lastFailure)->addSeconds(self::RECOVERY_TIMEOUT_SECONDS)->isPast()) {
                self::transitionTo(self::STATE_HALF_OPEN);

                return self::STATE_HALF_OPEN;
            }
        }

        return $state;
    }

    /**
     * Get consecutive failure count recorded in current window.
     */
    public static function getFailures(): int
    {
        return (int) Cache::get(self::KEY_FAILURES, 0);
    }

    /**
     * Get consecutive success count recorded during HALF_OPEN probes.
     */
    public static function getSuccesses(): int
    {
        return (int) Cache::get(self::KEY_SUCCESSES, 0);
    }

    /**
     * Get total fallback reads served during circuit open periods.
     */
    public static function getFallbackReadCount(): int
    {
        return (int) Cache::get(self::KEY_FALLBACK_COUNT, 0);
    }

    /**
     * Record a fallback read execution for honest observability metrics.
     */
    public static function recordFallbackRead(): void
    {
        Cache::increment(self::KEY_FALLBACK_COUNT);
    }

    /**
     * Check if a request is permitted through to Elasticsearch.
     */
    public static function isAvailable(): bool
    {
        $state = self::getState();

        return $state === self::STATE_CLOSED || $state === self::STATE_HALF_OPEN;
    }

    /**
     * Record a successful call. Resets failure count and closes the circuit after sufficient successes.
     */
    public static function recordSuccess(): void
    {
        $currentState = Cache::get(self::KEY_STATE, self::STATE_CLOSED);

        if ($currentState === self::STATE_HALF_OPEN) {
            $successes = Cache::increment(self::KEY_SUCCESSES);
            if ($successes >= self::SUCCESS_THRESHOLD) {
                self::transitionTo(self::STATE_CLOSED);
                Cache::forget(self::KEY_FAILURES);
                Cache::forget(self::KEY_SUCCESSES);
                Log::info('[CIRCUIT_BREAKER] SuperAdmin Profile ES circuit fully recovered and transitioned to CLOSED.');
            }
        } elseif ($currentState === self::STATE_CLOSED) {
            Cache::forget(self::KEY_FAILURES);
        }
    }

    /**
     * Record an Elasticsearch failure. Increments failure count and trips circuit if threshold exceeded.
     */
    public static function recordFailure(\Throwable $exception): void
    {
        $failures = Cache::increment(self::KEY_FAILURES);
        Cache::put(self::KEY_LAST_FAILURE, Carbon::now()->toIso8601String(), 86400);

        Log::warning('[CIRCUIT_BREAKER] SuperAdmin Profile ES error recorded: ' . $exception->getMessage(), [
            'failures' => $failures,
            'threshold' => self::FAILURE_THRESHOLD,
        ]);

        if ($failures >= self::FAILURE_THRESHOLD) {
            self::transitionTo(self::STATE_OPEN);
            Log::alert('[CIRCUIT_BREAKER] SuperAdmin Profile ES circuit TRIPPED to OPEN. Emergency MySQL fallback activated.');
        }
    }

    /**
     * Transition circuit to a target state.
     */
    protected static function transitionTo(string $newState): void
    {
        Cache::put(self::KEY_STATE, $newState, 86400);
        Log::info("[CIRCUIT_BREAKER] SuperAdmin Profile state transitioned to {$newState}.");
    }

    /**
     * Manual reset for administrative testing or maintenance.
     */
    public static function reset(): void
    {
        Cache::forget(self::KEY_STATE);
        Cache::forget(self::KEY_FAILURES);
        Cache::forget(self::KEY_SUCCESSES);
        Cache::forget(self::KEY_LAST_FAILURE);
        Cache::forget(self::KEY_FALLBACK_COUNT);
    }
}
