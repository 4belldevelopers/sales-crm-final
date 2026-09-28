<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class CandidateOutboxTelemetryService
 *
 * Intent: Operational governance and outbox telemetry tracking per docs/drawer_candidate.md.
 * Measures projection lag, pending age, dead letters, and alerts on SLO breaches.
 */
class CandidateOutboxTelemetryService
{
    /**
     * Compute production SLO metrics and alert evaluations.
     *
     * @return array{
     *   max_projection_lag_ver: int,
     *   oldest_pending_event_age_sec: float,
     *   outbox_dead_letter_count: int,
     *   mutation_resilience_rate: float,
     *   alerts: array<string>
     * }
     */
    public function getTelemetryMetrics(): array
    {
        // 1. Oldest pending event age in seconds
        $oldestPending = DB::table('sys_outbox_events')
            ->where('status', 'PENDING')
            ->orderBy('available_at', 'asc')
            ->value('available_at');

        $oldestPendingAgeSec = 0.0;
        if ($oldestPending) {
            $oldestPendingAgeSec = (float) max(0, now()->diffInSeconds(Carbon::parse($oldestPending)));
        }

        // 2. Dead letter count
        $deadLetterCount = (int) DB::table('sys_outbox_events')
            ->where('status', 'DEAD_LETTER')
            ->count();

        // 3. Max projection lag versions
        // In full operational mode: DB aggregate_version - ES aggregate_version
        // If pending events exist for candidate, lag = max(aggregate_version) - min(aggregate_version) for that candidate
        $maxLag = 0;
        $lagRecord = DB::table('sys_outbox_events')
            ->whereIn('status', ['PENDING', 'PROCESSING'])
            ->select('aggregate_id', DB::raw('MAX(aggregate_version) - MIN(aggregate_version) as lag'))
            ->groupBy('aggregate_id')
            ->orderByDesc('lag')
            ->first();

        if ($lagRecord && isset($lagRecord->lag)) {
            $maxLag = (int) $lagRecord->lag;
        }

        // 4. Mutation resilience rate (successful mutations vs failed)
        $totalIdempotency = (int) DB::table('sys_idempotency_keys')->count();
        $resilienceRate = 100.0;
        if ($totalIdempotency > 0) {
            $failedInfra = (int) DB::table('sys_idempotency_keys')->where('status', 'FAILED_RETRYABLE')->count();
            $resilienceRate = round((($totalIdempotency - $failedInfra) / $totalIdempotency) * 100, 2);
        }

        // 5. Evaluate critical alert thresholds
        $alerts = [];
        if ($maxLag > 100) {
            $alerts[] = "CRITICAL (P0): max_projection_lag_ver is {$maxLag} (Threshold: > 100 versions)";
        }
        if ($oldestPendingAgeSec > 30.0) {
            $alerts[] = "CRITICAL (P0): oldest_pending_event_age is {$oldestPendingAgeSec}s (Threshold: > 30s)";
        }
        if ($deadLetterCount > 0) {
            $alerts[] = "CRITICAL (P0): outbox_dead_letter_count is {$deadLetterCount} (Threshold: > 0)";
        }
        if ($resilienceRate < 99.99 && $totalIdempotency > 100) {
            $alerts[] = "CRITICAL (P0): mutation_resilience_rate is {$resilienceRate}% (Threshold: < 99.99%)";
        }

        return [
            'max_projection_lag_ver' => $maxLag,
            'oldest_pending_event_age_sec' => $oldestPendingAgeSec,
            'outbox_dead_letter_count' => $deadLetterCount,
            'mutation_resilience_rate' => $resilienceRate,
            'alerts' => $alerts,
        ];
    }
}
