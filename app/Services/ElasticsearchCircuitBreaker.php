<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Class ElasticsearchCircuitBreaker
 *
 * Intent: Distributed circuit breaker pattern guarding Elasticsearch read queries.
 * Prevents cascading thread pool exhaustion and socket timeout latency spikes when
 * the Elasticsearch cluster experiences high latency, partition, or node failure.
 *
 * State Machine:
 * - CLOSED: Normal operation. Requests execute against Elasticsearch.
 * - OPEN: Cluster tripped after 5 consecutive failures. Requests bypass Elasticsearch immediately
 *         without waiting for network timeouts and return degraded cached fallback payloads.
 * - HALF_OPEN: Cooldown period (30s) elapsed. A single probe request tests cluster recovery.
 *              On success, circuit resets to CLOSED. On failure, returns to OPEN.
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ElasticsearchCircuitBreaker
{
    public const STATE_CLOSED = 'CLOSED';

    public const STATE_OPEN = 'OPEN';

    public const STATE_HALF_OPEN = 'HALF_OPEN';

    public const CACHE_KEY_FAILURES = 'cb:es:failures';

    public const CACHE_KEY_STATE = 'cb:es:state';

    public const CACHE_KEY_LAST_FAILURE = 'cb:es:last_failure';

    public const CACHE_KEY_PROBE_LOCK = 'cb:es:probe_lock';

    protected int $failureThreshold;

    protected int $cooldownSeconds;

    public function __construct(int $failureThreshold = 5, int $cooldownSeconds = 30)
    {
        $this->failureThreshold = $failureThreshold;
        $this->cooldownSeconds = $cooldownSeconds;
    }

    /**
     * Get the current circuit state.
     */
    public function getState(): string
    {
        try {
            $state = Cache::get(self::CACHE_KEY_STATE, self::STATE_CLOSED);
            if ($state === self::STATE_OPEN) {
                $lastFailure = (int) Cache::get(self::CACHE_KEY_LAST_FAILURE, 0);
                if (time() - $lastFailure >= $this->cooldownSeconds) {
                    return self::STATE_HALF_OPEN;
                }
            }

            return $state;
        } catch (\Throwable $e) {
            return self::STATE_CLOSED;
        }
    }
    /**
     * Check if the circuit breaker is available (not OPEN).
     */
    public static function isAvailable(): bool
    {
        return app(self::class)->getState() !== self::STATE_OPEN;
    }
    /**
     * Execute a callable guarded by the circuit breaker with a fallback strategy.
     *
     * @param  callable  $action  Target action to execute (Elasticsearch call)
     * @param  callable|null  $fallback  Fallback action if circuit is OPEN or call fails
     * @return mixed Result of action or fallback
     *
     * @throws \Throwable If action fails and no fallback is provided
     */
    public function execute(callable $action, ?callable $fallback = null): mixed
    {
        $state = $this->getState();

        // 1. OPEN state: Immediate bypass without network wait
        if ($state === self::STATE_OPEN) {
            Log::warning('ElasticsearchCircuitBreaker: Circuit is OPEN. Bypassing Elasticsearch.');

            return $fallback ? $fallback(new \RuntimeException('Circuit breaker is OPEN')) : null;
        }

        // 2. HALF_OPEN state: Single probe execution
        if ($state === self::STATE_HALF_OPEN) {
            $lock = null;
            try {
                $lock = Cache::lock(self::CACHE_KEY_PROBE_LOCK, 10);
            } catch (\Throwable $e) {
            }

            if ($lock && $lock->get()) {
                try {
                    Log::info('ElasticsearchCircuitBreaker: Executing recovery probe in HALF_OPEN state.');
                    $result = $action();
                    $this->recordSuccess();

                    return $result;
                } catch (\Throwable $e) {
                    $this->recordFailure($e);

                    return $fallback ? $fallback($e) : throw $e;
                } finally {
                    optional($lock)->release();
                }
            } else {
                // Another process is running the probe; fallback immediately
                return $fallback ? $fallback(new \RuntimeException('Circuit breaker is probing in HALF_OPEN state')) : null;
            }
        }

        // 3. CLOSED state: Standard execution with failure tracking
        try {
            $result = $action();
            $this->recordSuccess();

            return $result;
        } catch (\Throwable $e) {
            $this->recordFailure($e);
            if ($fallback) {
                return $fallback($e);
            }
            throw $e;
        }
    }

    /**
     * Record a successful operation, resetting consecutive failure counts.
     */
    public function recordSuccess(): void
    {
        try {
            $failures = (int) Cache::get(self::CACHE_KEY_FAILURES, 0);
            if ($failures > 0) {
                Cache::forget(self::CACHE_KEY_FAILURES);
            }
            $state = Cache::get(self::CACHE_KEY_STATE, self::STATE_CLOSED);
            if ($state !== self::STATE_CLOSED) {
                Cache::put(self::CACHE_KEY_STATE, self::STATE_CLOSED, 86400);
                Log::info('ElasticsearchCircuitBreaker: Circuit recovered and reset to CLOSED.');
            }
        } catch (\Throwable $e) {
        }
    }

    /**
     * Record a failure, incrementing consecutive failure count and tripping circuit if threshold reached.
     */
    public function recordFailure(\Throwable $e): void
    {
        try {
            Cache::put(self::CACHE_KEY_LAST_FAILURE, time(), 86400);
            $failures = (int) Cache::increment(self::CACHE_KEY_FAILURES);

            if ($failures >= $this->failureThreshold) {
                Cache::put(self::CACHE_KEY_STATE, self::STATE_OPEN, 86400);
                Log::error("ElasticsearchCircuitBreaker: Tripped to OPEN after {$failures} consecutive failures! Reason: ".$e->getMessage());
            } else {
                Log::warning("ElasticsearchCircuitBreaker: Consecutive failure count: {$failures}/{$this->failureThreshold}. Error: ".$e->getMessage());
            }
        } catch (\Throwable $ex) {
            Log::warning('ElasticsearchCircuitBreaker::recordFailure exception: '.$ex->getMessage());
        }
    }

    /**
     * Force reset circuit breaker to CLOSED.
     */
    public function reset(): void
    {
        try {
            Cache::forget(self::CACHE_KEY_FAILURES);
            Cache::forget(self::CACHE_KEY_LAST_FAILURE);
            Cache::put(self::CACHE_KEY_STATE, self::STATE_CLOSED, 86400);
        } catch (\Throwable $e) {
        }
    }

    /**
     * Force trip circuit breaker to OPEN (for tests / manual failover).
     */
    public function trip(): void
    {
        try {
            Cache::put(self::CACHE_KEY_FAILURES, $this->failureThreshold, 86400);
            Cache::put(self::CACHE_KEY_LAST_FAILURE, time(), 86400);
            Cache::put(self::CACHE_KEY_STATE, self::STATE_OPEN, 86400);
        } catch (\Throwable $e) {
        }
    }
}
