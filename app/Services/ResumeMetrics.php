<?php

namespace App\Services;

use App\Logging\ResumeLogContext;
use App\Models\ResumeUpload;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use RuntimeException;
use Throwable;

/**
 * Phase 6 — Redis-direct, fail-fast observability facade for the
 * resume-upload subsystem.
 *
 * Phase 5 introduced named counters in Laravel cache. On
 * `cache.default=file` or `database`, two pods running the same
 * reconcile pass see independent counter namespaces; on
 * `cache.default=redis` the counters are shared but drivers like
 * `array` (used by the test suite) silently no-op on
 * `Cache::increment('missing_key')`.
 *
 * Phase 6 contracts:
 *   - Counter store is Redis-only. INCRBY / EXPIRE / GET go through
 *     the Redis facade. Other drivers are unreachable.
 *   - The constructor asserts the runtime precondition:
 *     `cache.default === 'redis'` (or, equivalently, that the resolve
 *     of `Cache::store('redis')` does not throw). When
 *     `config('resumes.metrics_require_redis')` is false, the
 *     assertion is skipped — that flag is the test-only override.
 *   - Failures are loud: a Redis exception is logged with full
 *     context and RE-THROWN. We never silently swallow because an
 *     undercounting metrics pipeline is worse than an alert.
 *
 * Backward compat: API surface (`incrementOrphanDeletions`,
 * `currentOrphanUploads`, etc.) is unchanged. The state machine
 * correctness mechanism (DB conditional UPDATEs) is unchanged;
 * metrics are observability, not locks.
 */
class ResumeMetrics
{
    /** Counter key prefix (lives in Redis; never user-visible). */
    public const PREFIX = 'resumes:metrics:';

    /** Default TTL for counter keys (24h, resets on cache eviction). */
    private const COUNTER_TTL_SECONDS = 86400;

    /**
     * In-process fallback counters used ONLY when the test-only
     * `metrics_require_redis=false` escape hatch is set. Without
     * this, the Reconcile/Repair tests cannot assert "counter went
     * 0→1" because their Redis facade is unbound. Production never
     * enters this path — the require_redis assertion runs first.
     */
    private static array $fallback = [];

    public function __construct()
    {
        $this->assertRedisAvailable();
    }

    // -------------------- counters (atomic Redis INCRBY) --------------------

    public function incrementOrphanDeletions(?int $companyId = null, string $reason = 'reconciler'): void
    {
        $this->bump('orphan_deletions_total', ['reason' => $reason], $companyId);
    }

    public function incrementDeleteFailures(?int $companyId = null, string $reason = 'r2_delete_failed'): void
    {
        $this->bump('delete_failures_total', ['reason' => $reason], $companyId);
        $this->maybeAlert('delete_failures', [
            'company_id' => $companyId,
            'reason' => $reason,
            'window' => 'last_hour',
        ]);
    }

    public function incrementRepairAttempts(string $kind = 'stuck_uploading'): void
    {
        $this->bump('repair_attempts_total', ['kind' => $kind]);
    }

    public function incrementRepairCompletions(string $kind = 'stuck_uploading'): void
    {
        $this->bump('repair_completions_total', ['kind' => $kind]);
    }

    public function incrementStuckDetected(string $kind = 'stuck_uploading'): void
    {
        $this->bump('stuck_detected_total', ['kind' => $kind]);
        $this->maybeAlert('stuck_sessions', ['kind' => $kind]);
    }

    public function incrementFinalizeFailures(string $reason = 'size_mismatch'): void
    {
        $this->bump('finalize_failures_total', ['reason' => $reason]);
        $this->maybeAlert('finalize_failures', ['reason' => $reason]);
    }

    // -------------------- gauges (computed, not cached) --------------------

    public function currentOrphanUploads(): int
    {
        $grace = (int) config('resumes.orphan_grace_hours', 24);
        $threshold = Carbon::now()->copy()->subHours($grace);
        return (int) ResumeUpload::query()
            ->where('status', ResumeUpload::STATUS_UPLOADED)
            ->whereNotNull('upload_completed_at')
            ->where('upload_completed_at', '<', $threshold)
            ->whereNull('candidate_id')
            ->whereNull('bench_application_id')
            ->count();
    }

    public function currentPendingFinalize(): int
    {
        return (int) ResumeUpload::query()
            ->whereIn('status', [
                ResumeUpload::STATUS_CREATED,
                ResumeUpload::STATUS_UPLOADING,
                ResumeUpload::STATUS_UPLOADED,
            ])
            ->count();
    }

    public function currentStuckSessions(): int
    {
        $uploadMin = (int) config('resumes.uploading_timeout_minutes', 30);
        $parseMin = (int) config('resumes.parsing_timeout_minutes', 60);
        $now = Carbon::now();
        $uploadThreshold = $now->copy()->subMinutes($uploadMin);
        $parseThreshold = $now->copy()->subMinutes($parseMin);
        return (int) ResumeUpload::query()
            ->where(function ($q) use ($uploadThreshold) {
                $q->where('status', ResumeUpload::STATUS_UPLOADING)
                  ->where('upload_started_at', '<', $uploadThreshold);
            })
            ->orWhere(function ($q) use ($parseThreshold) {
                $q->where('status', ResumeUpload::STATUS_PARSING)
                  ->where('parse_started_at', '<', $parseThreshold);
            })
            ->count();
    }

    // -------------------- counter reads (for dashboards) --------------------

    /**
     * Generic counter increment for ad-hoc metric names. Phase 9
     * uses this for `parse_started_total` and
     * `parse_completed_total` from the parser-controller. Callers
     * that don't have a domain-specific helper can still attach
     * labels and emit observability without forcing a new helper
     * method every time.
     *
     * Backward compatible: domain-specific increment*(...) method
     * shapes are unchanged. The Redis-direct semantics
     * (require_redis=true => atomic INCRBY + EXPIRE) apply
     * identically here.
     */
    public function increment(string $metric, array $labels = [], ?int $companyId = null): void
    {
        $this->bump($metric, $labels, $companyId);
    }

    /**
     * Phase 9 follow-up — record a parser outcome AND its per-hour
     * bucket atomically. We need per-hour windowing so the
     * `parser_fail_rate` alert can fire on a smalot regression
     * without recovering from a counter that has been accumulating
     * since the deploy. Buckets live in Redis with a 25 h TTL — one
     * hour of overlap is enough margin that an alert triggered at
     * 02:59 still has its 02:00 bucket live when read at 03:00.
     *
     * The generic `increment()` path is unchanged so existing callers
     * (parse_started_total from the controller) are not affected.
     */
    public function recordParserOutcome(string $outcome): void
    {
        $this->bump('parse_completed_total', ['outcome' => $outcome]);
        $this->bumpHourlyBucket('parse_outcome', $outcome);
        if ($outcome === 'fail') {
            $this->maybeAlertParserFailRate();
        }
    }

    /**
     * Read a counter. Pass the same labels passed to the writer for a
     * deterministic match. With no labels, returns the no-label aggregate.
     */
    public function counterValue(string $metric, ?int $companyId = null, array $labels = []): int
    {
        if (! $this->enabled()) {
            return 0;
        }
        $key = $this->key($metric, $companyId, $labels);

        if ($this->storeMode() === 'fallback') {
            return (int) (self::$fallback[$key] ?? 0);
        }

        try {
            return (int) (Redis::get($key) ?? 0);
        } catch (Throwable $e) {
            // Production: read failure is non-fatal — observability
            // missing one datum is recoverable, a missing write is not.
            ResumeLogContext::warning('metrics.read_failure', [
                'metric' => $metric,
                'key' => $key,
                'exception' => $e->getMessage(),
            ]);
            return 0;
        }
    }

    // -------------------- internals --------------------

    private function enabled(): bool
    {
        return (bool) config('resumes.metrics_enabled', false);
    }

    /**
     * Returns "redis" in production (require_redis=true), or
     * "fallback" in tests (require_redis=false). Centralized so the
     * same predicate is checked at every read + write site; the
     * fallback path is deterministic in tests rather than
     * opportunistic (catch-and-record) which depends on whether
     * the bound Redis facade throws.
     */
    private function storeMode(): string
    {
        return (bool) config('resumes.metrics_require_redis', true) ? 'redis' : 'fallback';
    }

    /** Test-only reset hook; never called in production. */
    public static function flushFallback(): void
    {
        self::$fallback = [];
    }

    /**
     * Boot-time assertion. Failure = boot failure. The user
     * explicitly asked for fail-fast when Redis is unavailable,
     * because a "metric counter that silently undercounts" hides
     * real incidents.
     */
    private function assertRedisAvailable(): void
    {
        if ($this->storeMode() === 'fallback') {
            return; // Test fixture escape hatch.
        }
        $default = (string) config('cache.default', 'database');
        if ($default !== 'redis') {
            throw new RuntimeException(sprintf(
                'ResumeMetrics requires cache.default=redis in production (got "%s"). '
                . 'Either set RESUMES_CACHE_STORE=redis, or set RESUMES_METRICS_REQUIRE_REDIS=false '
                . '(test-only override).',
                $default
            ));
        }
        try {
            // Ping is cheap and exercises the full facade path. If
            // auth or network is wrong we want the boot to scream.
            Redis::connection()->ping();
        } catch (Throwable $e) {
            throw new RuntimeException(
                'ResumeMetrics cannot reach Redis at boot: ' . $e->getMessage(),
                previous: $e
            );
        }
    }

    /**
     * Single write path. INCRBY is atomic in Redis which is why we
     * can drop the Cache::add + Cache::increment dance — both
     * operations happen in one round-trip per call (vs two in
     * Phase 5), and we add a TTL via EXPIRE only when the count
     * first becomes positive to amortize the cost.
     *
     * Errors here are LOUD: we rethrow so the surrounding job fails
     * fast and Horizon increments `failed_jobs`. Better a noisy
     * operator page than a metric that undercounts forever.
     */
    private function bump(string $metric, array $labels = [], ?int $companyId = null): void
    {
        if (! $this->enabled()) {
            return;
        }
        $key = $this->key($metric, $companyId, $labels);

        if ($this->storeMode() === 'fallback') {
            // Test path — content-addressed in-process counter.
            // Same key shape as the Redis path so label-mismatch
            // bugs surface identically in test and prod.
            self::$fallback[$key] = (int) (self::$fallback[$key] ?? 0) + 1;
            return;
        }

        try {
            $newValue = (int) Redis::incrby($key, 1);
            // First-write TTL set. We only EXPIRE on the first bump
            // so steady-state traffic doesn't pay the EXPIRE round-
            // trip. INCRBY on a non-existent key seeds it at the
            // increment value, hence newValue === 1 is the marker.
            if ($newValue === 1) {
                Redis::expire($key, self::COUNTER_TTL_SECONDS);
            }
            ResumeLogContext::event('metric.incremented', [
                'metric' => $metric,
                'labels' => $labels,
                'company_id' => $companyId,
                'value' => $newValue,
            ]);
        } catch (Throwable $e) {
            // Production: loud + rethrow. Operators want a page, not
            // a silent under-count.
            ResumeLogContext::warning('metrics.write_failure', [
                'metric' => $metric,
                'labels' => $labels,
                'company_id' => $companyId,
                'key' => $key,
                'exception' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function maybeAlert(string $alertKey, array $context = []): void
    {
        if (! (bool) config('resumes.alerts_enabled', false)) {
            return;
        }
        $thresholds = (array) config("resumes.alert_thresholds.{$alertKey}", []);
        if (empty($thresholds)) {
            return;
        }
        $window = (string) ($context['window'] ?? 'last_hour');
        $threshold = (int) ($thresholds[$window] ?? 0);
        if ($threshold <= 0) {
            return;
        }
        $value = match ($alertKey) {
            'orphan_uploads' => $this->currentOrphanUploads(),
            'stuck_sessions' => $this->currentStuckSessions(),
            'delete_failures' => $this->counterValue('delete_failures_total'),
            'finalize_failures' => $this->counterValue('finalize_failures_total'),
            default => 0,
        };
        if ($value > $threshold) {
            ResumeLogContext::warning('alert', array_merge($context, [
                'alert' => $alertKey,
                'threshold' => $threshold,
                'observed' => $value,
            ]));
        }
    }

    // -------------------- hourly-bucketed counters (Phase 9 follow-up) ----

    /**
     * Increment a per-hour bucket counter. Key shape:
     *   resumes:metrics:h:parse_outcome:success:2026-07-24-15
     *
     * The hour suffix lets `readHourlyBucketCount()` sum the trailing
     * 1 h without needing a sliding-window data structure. We set the
     * TTL at 25 h so two adjacent windows stay addressable for
     * minute-by-minute roll-ups; counter reads only ever span 2 buckets
     * at the boundary.
     */
    private function bumpHourlyBucket(string $metric, string $bucket): void
    {
        if (! $this->enabled()) {
            return;
        }
        $hour = Carbon::now()->format('Y-m-d-H');
        $key = self::PREFIX . 'h:' . $metric . ':' . $bucket . ':' . $hour;

        if ($this->storeMode() === 'fallback') {
            self::$fallback[$key] = (int) (self::$fallback[$key] ?? 0) + 1;
            return;
        }

        try {
            $newValue = (int) Redis::incrby($key, 1);
            if ($newValue === 1) {
                Redis::expire($key, 25 * 3600);
            }
        } catch (Throwable $e) {
            // Hourly buckets are observability-only — never rethrow.
            // A missing bucket just means the next request writes
            // fresh after Redis recovers.
            ResumeLogContext::warning('metrics.hourly_bucket_write_failure', [
                'metric' => $metric,
                'bucket' => $bucket,
                'key' => $key,
                'exception' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Sum a single bucket across the trailing N hours. We include the
     * in-progress hour as bucket 0 and look back N-1 additional full
     * hours. Bounds at 2 ≤ N ≤ 24 so a misconfigured call cannot span
     * an entire day.
     */
    private function readHourlyBucketCount(string $metric, string $bucket, int $hours = 1): int
    {
        $hours = max(2, min(24, $hours));
        $sum = 0;
        for ($i = 0; $i < $hours; $i++) {
            $hour = Carbon::now()->copy()->subHours($i)->format('Y-m-d-H');
            $key = self::PREFIX . 'h:' . $metric . ':' . $bucket . ':' . $hour;
            if ($this->storeMode() === 'fallback') {
                $sum += (int) (self::$fallback[$key] ?? 0);
                continue;
            }
            try {
                $sum += (int) (Redis::get($key) ?? 0);
            } catch (Throwable $e) {
                ResumeLogContext::warning('metrics.hourly_bucket_read_failure', [
                    'metric' => $metric,
                    'bucket' => $bucket,
                    'key' => $key,
                    'exception' => $e->getMessage(),
                ]);
                return $sum;
            }
        }
        return $sum;
    }

    /**
     * Phase 9 follow-up — alert when the trailing-hour inline-parser
     * fail ratio crosses `alert_thresholds.parser_fail_rate.last_hour_ratio`.
     * Fires a `jobs.resume.alert` event the existing alert pipeline
     * picks up identically to the other Phase-5 alerts — same log
     * shape so the operator's router does not need to learn a new
     * vocabulary.
     *
     * Minimum 20 samples before the ratio can be evaluated — a single
     * failed request on a quiet day is noise we do not page on.
     */
    private function maybeAlertParserFailRate(): void
    {
        if (! (bool) config('resumes.alerts_enabled', false)) {
            return;
        }
        $ratio = (float) config(
            'resumes.alert_thresholds.parser_fail_rate.last_hour_ratio',
            0.25,
        );
        if ($ratio <= 0) {
            return;
        }
        $fails = $this->readHourlyBucketCount('parse_outcome', 'fail');
        $ok = $this->readHourlyBucketCount('parse_outcome', 'success');
        $total = $fails + $ok;
        if ($total < 20) {
            return;
        }
        $observed = $total > 0 ? $fails / $total : 0.0;
        if ($observed >= $ratio) {
            ResumeLogContext::warning('alert', [
                'alert' => 'parser_fail_rate',
                'window' => 'last_hour',
                'threshold' => $ratio,
                'observed' => round($observed, 4),
                'fail_count' => $fails,
                'success_count' => $ok,
                'total_count' => $total,
            ]);
        }
    }

    /**
     * Compose a Redis key. Stay under 200 chars total to avoid
     * Redis's 512MB limit (well above what we need but cheap to
     * assert). Labels are truncated to 24 chars per part to bound
     * entropy from arbitrary user strings.
     */
    private function key(string $metric, ?int $companyId = null, array $labels = []): string
    {
        $parts = [self::PREFIX, $metric];
        if ($companyId !== null) {
            $parts[] = 'c' . $companyId;
        }
        foreach ($labels as $k => $v) {
            $parts[] = substr((string) $k, 0, 24) . '_' . substr((string) $v, 0, 24);
        }
        return implode(':', $parts);
    }
}
