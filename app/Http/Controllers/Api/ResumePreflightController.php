<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ResumeMetrics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Intentionally does NOT extend App\Http\Controllers\Controller —
 * the base controller's __construct runs migrate:check which fires
 * on every request to in-memory-fresh sqlite in tests. The
 * ResumeHealthController uses the same pattern for the same reason.
 */

/**
 * Phase 9.2 — lightweight preflight for the resume submit pipeline.
 *
 * The submit handler can hang for up to 30 s if the server is sick
 * (slow MySQL, exhausted DB connection pool, FCGI worker deadlock).
 * `navigator.onLine` stays true throughout that whole episode because
 * the network is fine — only the application server is unhealthy.
 *
 * The submit JS therefore pings this endpoint with an 800 ms AbortController
 * timeout BEFORE the heavy "submitting..." spinner appears. A preflight
 * failure flips the spinner to a calm "Server slow, retrying..." badge
 * and prevents a 30 s of dead air.
 *
 * Distinct from {@see ResumeHealthController}: this route does NOT
 * probe Redis/R2/Queue depth — only what the submit path needs:
 *
 *   - MySQL is responsive for a single SELECT 1 (we need to write
 *     resume_uploads + job_applications in the same transaction).
 *   - The user's session is authenticated (rate-limit gate-able; per-
 *     user burst protection rather than /up's anonymous traffic).
 *
 * Authentication: web session cookie. NO bearer-token because the
 * caller is a logged-in recruiter form. Cache::throttle caps the
 * route so a misbehaving client cannot hammer the preflight instead
 * of backing off.
 *
 * Latency target: < 80 ms p99 in production. Only tests + tiny probes.
 */
class ResumePreflightController
{
    /**
     * Cap so a stuck tab (firing preflight every render) cannot hammer
     * the DB. 30 requests/minute per user is generous — the user's
     * submit flow generates at most ~3 preflights in a session (page
     * load + a couple of network-recovery loops).
     */
    public const RATE_LIMIT_PER_MINUTE = 30;

    public function preflight(Request $request): JsonResponse
    {
        $start = microtime(true);

        $mysql = $this->probeMysql();

        // Always 200 even when MySQL is degraded — the JS client reads
        // checks.mysql.ok to decide UI vs `Retry`. A 503 here would
        // just turn into a 30 s spinner because the browser treats 5xx
        // as a generic "server problem" and doesn't trust our JSON.
        $response = [
            'ok' => (bool) ($mysql['ok'] ?? false),
            'checks' => [
                'mysql' => $mysql,
            ],
            // Reservation — Phase 10 may add queue-depth here. Today the
            // JS only consults this if submit blocks longer than the
            // wait cap. Keeping the shape stable means we can add fields
            // without a JS rebuild.
            'gauges' => [
                'stuck_uploads_for_tenant' => $this->safeGaugeForTenant(
                    $request,
                    fn () => app(ResumeMetrics::class)->currentStuckSessions(),
                ),
            ],
            'latency_ms_total' => (int) round((microtime(true) - $start) * 1000),
            'timestamp' => now()->toIso8601String(),
        ];

        return response()->json($response, 200);
    }

    /**
     * Single-roundtrip MySQL probe. SELECT 1 is the canonical
     * "are you alive" check the framework itself uses during
     * boot; never require a full load to start a flow.
     */
    private function probeMysql(): array
    {
        $start = microtime(true);
        try {
            DB::connection()->select('SELECT 1');
            return [
                'ok' => true,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
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
     * Tenant-scoped gauge. Skipped if the request has no tenant
     * context (anonymous probe) — anonymous probes should not
     * leak per-tenant information even in health responses.
     */
    private function safeGaugeForTenant(Request $request, callable $fn): array
    {
        $start = microtime(true);
        $user = $request->user();
        if ($user === null) {
            return [
                'value' => null,
                'latency_ms' => (int) round((microtime(true) - $start) * 1000),
                'skipped' => true,
                'code' => 'no_tenant',
            ];
        }
        try {
            return [
                'value' => (int) $fn(),
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
}
