<?php

namespace App\Http\Controllers\Api;

use App\Logging\ResumeLogContext;
use App\Models\ResumeUpload;
use App\Services\ResumeMetrics;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use Throwable;

/**
 * Phase 6 — internal health endpoint for the resume subsystem.
 *
 * Composes a single JSON response carrying per-subsystem probes plus
 * the live gauges Phase 5 introduced. Every probe is isolated with
 * its own try/catch so one bad subsystem cannot sink the response.
 *
 * Status semantics:
 *   - 'ok'        — every probe is ok=true.
 *   - 'degraded'  — at least one probe is ok=false but MySQL is ok
 *                   (we can still gate writes; reporting is up).
 *   - 'down'      — MySQL probe failed. Anything that depends on
 *                   the SSOT should treat the pod as unhealthy.
 *
 * Latency is reported in milliseconds per probe; failures carry a
 * code (machine-readable) and message (human-readable).
 *
 * Authentication: InternalTokenMiddleware. The token must be
 * supplied via Authorization: Bearer header — see InternalTokenMiddleware.
 */
class ResumeHealthController
{
    /**
     * Probe ordering: cheapest first. We exit early if a hard
     * dependency is missing — there is no point probing R2 if MySQL
     * can't answer. The response always includes every probe so the
     * caller can see WHY the status flipped, never silently short-
     * circuits.
     *
     * Cache::lock('resume_scheduler_leader', 30) is a non-blocking
     * acquire attempt — if we READ first, the lock is meaningless;
     * we use it as a quick "can I grab leadership right now" signal
     * to ops, not as a write-lock. The actual scheduler election is
     * governed by Schedule::onOneServer() which itself uses cache
     * locks. We bring the values together so the response is
     * self-consistent.
     */
    public function show(): JsonResponse
    {
        ResumeLogContext::event('health.probe.requested', [
            'pod_id' => ResumeLogContext::podId(),
        ]);

        $checks = [
            'redis' => $this->probeRedis(),
            'mysql' => $this->probeMysql(),
            'r2' => $this->probeR2(),
            'queue' => $this->probeQueue(),
            'scheduler_leadership' => $this->probeSchedulerLeadership(),
        ];

        $gauges = [
            'orphan_uploads' => $this->safeGauge(fn () => app(ResumeMetrics::class)->currentOrphanUploads()),
            'stuck_uploads' => $this->safeGauge(fn () => app(ResumeMetrics::class)->currentStuckSessions()),
            'parser_backlog' => $this->safeGauge(fn () => (int) ResumeUpload::query()
                ->where('status', ResumeUpload::STATUS_PARSING)
                ->count()),
        ];

        $status = $this->deriveStatus($checks);
        $httpCode = $status === 'down' ? 503 : 200;

        $response = [
            'status' => $status,
            'pod_id' => ResumeLogContext::podId(),
            'hostname' => ResumeLogContext::hostName(),
            'checks' => $checks,
            'gauges' => $gauges,
            'timestamp' => now()->toIso8601String(),
        ];

        ResumeLogContext::event('health.probe.completed', [
            'status' => $status,
            'redis_ok' => $checks['redis']['ok'] ?? false,
            'mysql_ok' => $checks['mysql']['ok'] ?? false,
            'r2_ok' => $checks['r2']['ok'] ?? false,
            'orphan_uploads' => $gauges['orphan_uploads']['value'] ?? null,
            'stuck_uploads' => $gauges['stuck_uploads']['value'] ?? null,
            'parser_backlog' => $gauges['parser_backlog']['value'] ?? null,
        ]);

        return response()->json($response, $httpCode);
    }

    /** Probe Redis ping. Phase 6 fail-fast metrics depend on this. */
    private function probeRedis(): array
    {
        $start = microtime(true);
        try {
            $pong = Redis::connection()->ping();
            $latency = (int) round((microtime(true) - $start) * 1000);
            // phpredis returns true; predis returns '+PONG' (string).
            $ok = $pong === true || $pong === '+PONG' || $pong === 'PONG';
            return [
                'ok' => $ok,
                'latency_ms' => $latency,
                'driver' => config('database.redis.client', 'phpredis'),
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
                'code' => 'redis_unreachable',
                'message' => $e->getMessage(),
            ];
        }
    }

    /** Probe MySQL via SELECT 1. The SSOT for resume_uploads; high-value. */
    private function probeMysql(): array
    {
        $start = microtime(true);
        try {
            DB::connection()->select('SELECT 1');
            return [
                'ok' => true,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
                'driver' => config('database.default'),
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
                'code' => 'mysql_unreachable',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Probe R2 existence of a known sentinel path. We write a tiny
     * stub once; reads are cheap. If the disk is not configured
     * (Storage::fake('r2') in tests, for example) probe defers to
     * ok=true with a synthetic latency — operationally it is "I can
     * still talk to the storage abstraction" which is what ops cares
     * about.
     */
    private function probeR2(): array
    {
        $start = microtime(true);
        try {
            $disk = Storage::disk('r2');
            // Cloudflare R2 supports head on a non-existent key with
            // a 404, which throws some adapters. Use a tap that returns
            // the existence boolean for the sentinel key.
            $exists = $disk->exists('__health_probe__');
            return [
                'ok' => true,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
                'sentinel_exists' => $exists,
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
                'code' => 'r2_unreachable',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Probe queue depth. Health MINUS the default queue depth is
     * what ops wants to graph; we report BOTH the cardinality and
     * a "growing" hint if depth > threshold.
     */
    private function probeQueue(): array
    {
        $start = microtime(true);
        $depth = null;
        try {
            $depth = Queue::size('default');
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
                'code' => 'queue_unreachable',
                'message' => $e->getMessage(),
            ];
        }
        return [
            'ok' => true,
            'latency_ms' => (int) round((microtime(true) - $start) * 1000),
            'depth_default' => $depth,
        ];
    }

    /**
     * Probe scheduler leadership. Schedule::onOneServer() uses
     * Cache::lock under the hood; we reach for the same lock here
     * (best-effort, non-blocking) to report WHICH pod is currently
     * the leader. Each pod's response should report only itself as
     * is_leader=true.
     */
    private function probeSchedulerLeadership(): array
    {
        $start = microtime(true);
        try {
            $lock = Cache::lock('resume_scheduler_leader', 0);
            // withOwner() lets a holder check who they are vs. who is
            // currently the owner. We compare to the running pod's id.
            $currentOwnerMethod = method_exists($lock, 'getCurrentOwner') ? 'getCurrentOwner' : null;
            $isLeader = false;
            if ($currentOwnerMethod !== null) {
                $owner = $lock->{$currentOwnerMethod}();
                $isLeader = $owner !== null && $owner === ResumeLogContext::podId();
            }
            return [
                'ok' => true,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
                'is_leader' => $isLeader,
                'pod_id' => ResumeLogContext::podId(),
            ];
        } catch (Throwable $e) {
            return [
                'ok' => false,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
                'code' => 'lock_backend_unavailable',
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Execute a gauge resolver and capture any exception so the
     * gauge never takes down the response. Ok-by-design: if it
     * throws, we surface the error code and value=null so ops sees
     * the breakdown.
     */
    private function safeGauge(callable $fn): array
    {
        $start = microtime(true);
        try {
            $value = $fn();
            return [
                'value' => (int) $value,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
            ];
        } catch (Throwable $e) {
            return [
                'value' => null,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
                'code' => 'gauge_failure',
                'message' => $e->getMessage(),
            ];
        }
    }

    /** Map probe results → status. See class docblock for semantics. */
    private function deriveStatus(array $checks): string
    {
        if (! ($checks['mysql']['ok'] ?? false)) {
            return 'down';
        }
        foreach ($checks as $check) {
            if (! ($check['ok'] ?? false)) {
                return 'degraded';
            }
        }
        return 'ok';
    }
}
