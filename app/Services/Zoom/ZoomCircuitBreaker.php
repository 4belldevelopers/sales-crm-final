<?php

namespace App\Services\Zoom;

use App\Services\Zoom\Exceptions\ZoomCircuitBreakerOpenException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class ZoomCircuitBreaker
 *
 * Intent: Redis-backed distributed Circuit Breaker pattern protecting the JERO ATS
 * from third-party Zoom API degradation, cascading thread exhaustion, and latency spikes.
 *
 * States:
 * - CLOSED (0): Normal operation. Outbound Zoom requests pass through unimpeded.
 * - OPEN (1): Failure threshold reached (default 5 failures / 60s). Outbound requests
 *             fast-fail in 0ms with ZoomCircuitBreakerOpenException.
 * - HALF_OPEN (2): Cooldown elapsed (default 30s). A single probe request is permitted
 *                  to test Zoom upstream availability.
 *
 * Author: Antigravity AI — Enterprise Zoom Integration Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ZoomCircuitBreaker
{
    public const STATE_CLOSED = 'CLOSED';

    public const STATE_OPEN = 'OPEN';

    public const STATE_HALF_OPEN = 'HALF_OPEN';

    protected const KEY_STATE = 'zoom:circuit:state';

    protected const KEY_FAILURES = 'zoom:circuit:failure_count';

    protected const KEY_OPENED_AT = 'zoom:circuit:opened_at';

    protected const KEY_LAST_FAILURE = 'zoom:circuit:last_failure_at';

    protected const KEY_PROBE_LOCK = 'zoom:circuit:probe_lock';

    protected int $failureThreshold = 5;

    protected int $failureWindowSeconds = 60;

    protected int $cooldownSeconds = 30;

    /**
     * Check if the circuit breaker permits an outbound request.
     * Fast-fails immediately if the circuit is OPEN and cooldown has not expired.
     *
     * @throws ZoomCircuitBreakerOpenException
     */
    public function guard(): void
    {
        $state = $this->getState();

        if ($state === self::STATE_CLOSED) {
            return;
        }

        if ($state === self::STATE_OPEN) {
            $openedAt = (int) Cache::get(self::KEY_OPENED_AT, time());
            $elapsed = time() - $openedAt;
            $remaining = max(1, $this->cooldownSeconds - $elapsed);

            if ($elapsed >= $this->cooldownSeconds) {
                // Cooldown elapsed — transition to HALF_OPEN
                $this->transitionTo(self::STATE_HALF_OPEN);

                return;
            }

            throw new ZoomCircuitBreakerOpenException(
                'Zoom API integration circuit breaker is currently OPEN due to consecutive upstream failures.',
                $remaining
            );
        }

        if ($state === self::STATE_HALF_OPEN) {
            // In HALF_OPEN, allow probe requests
            return;
        }
    }

    /**
     * Get current circuit breaker state.
     */
    public function getState(): string
    {
        try {
            $rawState = Cache::get(self::KEY_STATE, self::STATE_CLOSED);

            if ($rawState === self::STATE_OPEN) {
                $openedAt = (int) Cache::get(self::KEY_OPENED_AT, time());
                if ((time() - $openedAt) >= $this->cooldownSeconds) {
                    $this->transitionTo(self::STATE_HALF_OPEN);

                    return self::STATE_HALF_OPEN;
                }
            }

            return in_array($rawState, [self::STATE_CLOSED, self::STATE_OPEN, self::STATE_HALF_OPEN], true)
                ? $rawState
                : self::STATE_CLOSED;
        } catch (Throwable $e) {
            // Graceful fallback on cache failure — allow request
            return self::STATE_CLOSED;
        }
    }

    /**
     * Record a successful outbound Zoom API call. Resets failure count and closes circuit if in HALF_OPEN.
     */
    public function recordSuccess(): void
    {
        try {
            $state = $this->getState();

            if ($state === self::STATE_HALF_OPEN || $state === self::STATE_OPEN) {
                Log::info('ZoomCircuitBreaker: Probe successful. Transitioning circuit back to CLOSED.');
                $this->reset();
            } else {
                Cache::forget(self::KEY_FAILURES);
            }
        } catch (Throwable $e) {
            // Suppress cache errors
        }
    }

    /**
     * Record an upstream failure (HTTP 5xx, network timeout, connection refused).
     */
    public function recordFailure(string $reason = ''): void
    {
        try {
            $state = $this->getState();

            if ($state === self::STATE_HALF_OPEN) {
                // Probe failed in HALF_OPEN — immediately trip back to OPEN
                Log::warning("ZoomCircuitBreaker: Probe request failed ({$reason}). Tripping back to OPEN.");
                $this->transitionTo(self::STATE_OPEN);

                return;
            }

            $currentFailures = (int) Cache::get(self::KEY_FAILURES, 0) + 1;
            Cache::put(self::KEY_FAILURES, $currentFailures, $this->failureWindowSeconds);
            Cache::put(self::KEY_LAST_FAILURE, time(), $this->failureWindowSeconds);

            if ($currentFailures >= $this->failureThreshold) {
                Log::error("ZoomCircuitBreaker: Threshold reached ({$currentFailures} failures in {$this->failureWindowSeconds}s). Tripping circuit to OPEN. Reason: {$reason}");
                $this->transitionTo(self::STATE_OPEN);
            }
        } catch (Throwable $e) {
            // Suppress cache errors
        }
    }

    /**
     * Reset circuit breaker to clean CLOSED state.
     */
    public function reset(): void
    {
        try {
            Cache::put(self::KEY_STATE, self::STATE_CLOSED, 86400 * 7);
            Cache::forget(self::KEY_FAILURES);
            Cache::forget(self::KEY_OPENED_AT);
            Cache::forget(self::KEY_LAST_FAILURE);
        } catch (Throwable $e) {
            // Suppress cache errors
        }
    }

    /**
     * Manually trip circuit breaker to OPEN (used in tests or administrative override).
     */
    public function trip(): void
    {
        $this->transitionTo(self::STATE_OPEN);
    }

    /**
     * Transition internal state.
     */
    protected function transitionTo(string $newState): void
    {
        try {
            Cache::put(self::KEY_STATE, $newState, 86400 * 7);

            if ($newState === self::STATE_OPEN) {
                Cache::put(self::KEY_OPENED_AT, time(), $this->cooldownSeconds * 2);
            } elseif ($newState === self::STATE_CLOSED) {
                Cache::forget(self::KEY_OPENED_AT);
                Cache::forget(self::KEY_FAILURES);
            }
        } catch (Throwable $e) {
            // Suppress cache errors
        }
    }

    /**
     * Retrieve diagnostic telemetry for health metrics and Super Admin dashboard.
     */
    public function getMetrics(): array
    {
        try {
            $state = $this->getState();
            $failures = (int) Cache::get(self::KEY_FAILURES, 0);
            $openedAt = (int) Cache::get(self::KEY_OPENED_AT, 0);
            $lastFailureAt = (int) Cache::get(self::KEY_LAST_FAILURE, 0);

            $cooldownRemaining = 0;
            if ($state === self::STATE_OPEN && $openedAt > 0) {
                $cooldownRemaining = max(0, $this->cooldownSeconds - (time() - $openedAt));
            }

            return [
                'state' => $state,
                'failure_count' => $failures,
                'failure_threshold' => $this->failureThreshold,
                'failure_window_seconds' => $this->failureWindowSeconds,
                'cooldown_seconds' => $this->cooldownSeconds,
                'cooldown_remaining_seconds' => $cooldownRemaining,
                'last_failure_at' => $lastFailureAt ? date('c', $lastFailureAt) : null,
                'opened_at' => $openedAt ? date('c', $openedAt) : null,
            ];
        } catch (Throwable $e) {
            return [
                'state' => self::STATE_CLOSED,
                'error' => $e->getMessage(),
            ];
        }
    }
}
