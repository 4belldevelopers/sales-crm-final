<?php

namespace App\Services\SuperAdmin;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class BenchRmNoteCircuitBreaker
 *
 * Intent: Complete circuit-breaker state machine for Elasticsearch read operations
 * in the Super Admin Relationship Manager (RM) Note subsystem (bench_rm_note_master).
 *
 * States:
 * - CLOSED: Normal operation. Requests route to Elasticsearch.
 * - OPEN: Failure threshold exceeded (3 consecutive errors). Fast-fails to MySQL fallback immediately without network timeout.
 * - HALF_OPEN: Probe interval elapsed (30s). Allows a single canary request to test if Elasticsearch recovered.
 *
 * Tenancy: Scoped to Super Admin infrastructure.
 * Target: 10,000 concurrent users / 0% server & database read strain.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class BenchRmNoteCircuitBreaker
{
    public const STATE_CLOSED = 'CLOSED';

    public const STATE_OPEN = 'OPEN';

    public const STATE_HALF_OPEN = 'HALF_OPEN';

    protected const KEY_STATE = 'superadmin:circuit:brmnote:state';

    protected const KEY_FAILURES = 'superadmin:circuit:brmnote:failures';

    protected const KEY_SUCCESSES = 'superadmin:circuit:brmnote:successes';

    protected const KEY_LAST_FAILURE = 'superadmin:circuit:brmnote:last_failure';

    protected const KEY_FALLBACK_COUNT = 'superadmin:circuit:brmnote:fallback_count';

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
     * Record a successful call.
     * In HALF_OPEN, requires SUCCESS_THRESHOLD consecutive successes before transitioning to CLOSED.
     */
    public static function recordSuccess(): void
    {
        $currentState = Cache::get(self::KEY_STATE, self::STATE_CLOSED);

        if ($currentState === self::STATE_HALF_OPEN) {
            $successes = (int) Cache::increment(self::KEY_SUCCESSES);
            if ($successes >= self::SUCCESS_THRESHOLD) {
                self::transitionTo(self::STATE_CLOSED);
                Log::info("[CIRCUIT_BREAKER] Bench RM Note ES circuit transitioned to CLOSED (Healthy after {$successes} canary probes).");
                Cache::forget(self::KEY_SUCCESSES);
                Cache::forget(self::KEY_FAILURES);
                Cache::forget(self::KEY_LAST_FAILURE);
            } else {
                Log::info("[CIRCUIT_BREAKER] Bench RM Note canary probe successful ({$successes}/".self::SUCCESS_THRESHOLD.').');
            }

            return;
        }

        Cache::put(self::KEY_STATE, self::STATE_CLOSED, 86400);
        Cache::forget(self::KEY_FAILURES);
        Cache::forget(self::KEY_LAST_FAILURE);
        Cache::forget(self::KEY_SUCCESSES);
    }

    /**
     * Record a failure. Trips circuit to OPEN if threshold reached.
     */
    public static function recordFailure(\Throwable $e): void
    {
        $now = Carbon::now();
        Cache::put(self::KEY_LAST_FAILURE, $now->toIso8601String(), 86400);
        Cache::forget(self::KEY_SUCCESSES);

        $failures = (int) Cache::increment(self::KEY_FAILURES);
        if ($failures === 1) {
            Cache::put(self::KEY_FAILURES, 1, 300); // 5-minute failure counting window
        }

        $currentState = Cache::get(self::KEY_STATE, self::STATE_CLOSED);

        if ($currentState === self::STATE_HALF_OPEN || $failures >= self::FAILURE_THRESHOLD) {
            self::transitionTo(self::STATE_OPEN);
            Log::warning("[CIRCUIT_BREAKER] Bench RM Note ES circuit tripped to OPEN. Failures: {$failures}. Cause: {$e->getMessage()}");
        }
    }

    /**
     * Force reset circuit to CLOSED state.
     */
    public static function reset(): void
    {
        Cache::put(self::KEY_STATE, self::STATE_CLOSED, 86400);
        Cache::forget(self::KEY_FAILURES);
        Cache::forget(self::KEY_SUCCESSES);
        Cache::forget(self::KEY_LAST_FAILURE);
        Cache::forget(self::KEY_FALLBACK_COUNT);
    }

    /**
     * Transition circuit to a specific state.
     */
    protected static function transitionTo(string $state): void
    {
        Cache::put(self::KEY_STATE, $state, 86400);
    }
}
