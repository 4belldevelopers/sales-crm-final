<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CoinMetricsService — V2 Coins Engine Phase 0 Instrumentation Service.
 *
 * Intent: Measures execution duration (ms) and database query counts during coin engine
 * balance lookups, debits, and validation operations. Emits telemetry records to the
 * 'coins_performance' daily log channel for observability without modifying business logic.
 *
 * Tenancy Note: Tenant isolation is tracked via 'company_id' in all logged payloads.
 *
 * Author: Antigravity AI
 * Date: August 26, 2026
 */
class CoinMetricsService
{
    /**
     * Measure execution time and query count for a given coin operation callback.
     *
     * Intent: Wraps any coin operation (e.g. deduction, validation, job pass) to record
     * latency, DB query counts, and success/failure status.
     *
     * Tenancy Note: Passes company_id into context log payload.
     *
     * @param  string  $operation  Name of the coin operation being benchmarked.
     * @param  int|null  $companyId  Tenant company identifier.
     * @param  callable  $callback  Operation logic to execute.
     * @return mixed Result of callback execution.
     */
    public function measure(string $operation, ?int $companyId, callable $callback): mixed
    {
        $startTime = microtime(true);
        $queryCount = 0;

        DB::listen(function ($query) use (&$queryCount) {
            $queryCount++;
        });

        try {
            $result = $callback();
            $durationMs = (microtime(true) - $startTime) * 1000;

            $this->logTelemetry($operation, $companyId, $durationMs, $queryCount, true);

            return $result;
        } catch (\Throwable $e) {
            $durationMs = (microtime(true) - $startTime) * 1000;

            $this->logTelemetry($operation, $companyId, $durationMs, $queryCount, false, $e->getMessage());

            throw $e;
        }
    }

    /**
     * Log structured metrics payload to dedicated coins_performance channel.
     *
     * @param  string  $operation  Name of operation.
     * @param  int|null  $companyId  Tenant ID.
     * @param  float  $durationMs  Execution duration in milliseconds.
     * @param  int  $queryCount  Total DB queries executed.
     * @param  bool  $success  Whether operation completed without exception.
     * @param  string|null  $errorMessage  Exception message if failed.
     */
    private function logTelemetry(
        string $operation,
        ?int $companyId,
        float $durationMs,
        int $queryCount,
        bool $success,
        ?string $errorMessage = null
    ): void {
        $payload = [
            'timestamp' => now()->toIso8601String(),
            'operation' => $operation,
            'company_id' => $companyId,
            'duration_ms' => round($durationMs, 2),
            'query_count' => $queryCount,
            'success' => $success,
            'error' => $errorMessage,
        ];

        // Increment Redis telemetry counters for real-time APM aggregation
        try {
            \Illuminate\Support\Facades\Cache::increment('coins_telemetry:total_operations');
            if ($queryCount === 0) {
                \Illuminate\Support\Facades\Cache::increment('coins_telemetry:cache_hits');
            }
            if ($operation === 'deduct_coins_atomic') {
                \Illuminate\Support\Facades\Cache::increment('coins_telemetry:atomic_deductions');
            }
        } catch (\Throwable $e) {
            // Non-blocking telemetry increment exception catch
        }

        try {
            Log::channel('coins_performance')->info('Coin Engine Metric', $payload);
        } catch (\Throwable $e) {
            Log::info('Coin Engine Metric (fallback)', $payload);
        }
    }
}
