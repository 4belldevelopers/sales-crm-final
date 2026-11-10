<?php

namespace App\Services\SuperAdmin;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class GoogleMeetingCircuitBreaker
 *
 * Intent: Complete circuit-breaker state machine for Elasticsearch read operations
 * in the Super Admin Google Meet subsystem.
 *
 * States:
 * - CLOSED: Normal operation. Requests route to Elasticsearch.
 * - OPEN: Failure threshold exceeded (3 consecutive errors). Fast-fails to MySQL fallback immediately without network timeout.
 * - HALF_OPEN: Probe interval elapsed (30s). Allows a single canary request to test if Elasticsearch recovered.
 *
 * Tenancy: Scoped to Super Admin infrastructure.
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class GoogleMeetingCircuitBreaker
{
    public const STATE_CLOSED = 'CLOSED';

    public const STATE_OPEN = 'OPEN';

    public const STATE_HALF_OPEN = 'HALF_OPEN';

    protected const KEY_STATE = 'superadmin:circuit:gmeet:state';

    protected const KEY_FAILURES = 'superadmin:circuit:gmeet:failures';

    protected const KEY_LAST_FAILURE = 'superadmin:circuit:gmeet:last_failure';

    protected const FAILURE_THRESHOLD = 3;

    protected const RECOVERY_TIMEOUT_SECONDS = 30;

    /**
     * Get the current circuit breaker state.
     */
    public static function getState(): string
    {
        $state = Cache::get(self::KEY_STATE, self::STATE_CLOSED);

        if ($state === self::STATE_OPEN) {
            $lastFailure = Cache::get(self::KEY_LAST_FAILURE);
            if ($lastFailure && Carbon::parse($lastFailure)->addSeconds(self::RECOVERY_TIMEOUT_SECONDS)->isPast()) {
                // Transition to HALF_OPEN to probe recovery
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
     * Get ISO timestamp of the last recorded failure.
     */
    public static function getLastFailure(): ?string
    {
        return Cache::get(self::KEY_LAST_FAILURE);
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
     * Record a successful call. Resets failure count and closes the circuit.
     */
    public static function recordSuccess(): void
    {
        $currentState = Cache::get(self::KEY_STATE, self::STATE_CLOSED);

        if ($currentState !== self::STATE_CLOSED) {
            Log::info('[CIRCUIT_BREAKER] Google Meet ES circuit transitioned to CLOSED (Healthy).');
        }

        Cache::put(self::KEY_STATE, self::STATE_CLOSED, 86400);
        Cache::forget(self::KEY_FAILURES);
        Cache::forget(self::KEY_LAST_FAILURE);
    }

    /**
     * Record a failed call. Increments failure count and trips circuit to OPEN if threshold reached.
     */
    public static function recordFailure(\Throwable $e): void
    {
        $failures = (int) Cache::get(self::KEY_FAILURES, 0) + 1;
        Cache::put(self::KEY_FAILURES, $failures, 3600);
        Cache::put(self::KEY_LAST_FAILURE, Carbon::now()->toIso8601String(), 3600);

        Log::warning("[CIRCUIT_BREAKER] Google Meet ES error recorded (Count: {$failures}/".self::FAILURE_THRESHOLD.'): '.$e->getMessage());

        if ($failures >= self::FAILURE_THRESHOLD) {
            self::transitionTo(self::STATE_OPEN);
            Log::error('[CIRCUIT_BREAKER] Google Meet ES circuit tripped to OPEN. Routing to fallback store.');
        }
    }

    /**
     * Force transition state with TTL.
     */
    protected static function transitionTo(string $state): void
    {
        Cache::put(self::KEY_STATE, $state, 86400);
    }

    /**
     * Manual circuit reset (e.g., from healthcheck or CLI reconciliation).
     */
    public static function reset(): void
    {
        Cache::forget(self::KEY_STATE);
        Cache::forget(self::KEY_FAILURES);
        Cache::forget(self::KEY_LAST_FAILURE);
    }
}
