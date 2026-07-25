<?php

namespace App\Logging;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Phase 6 — structured-log context helper for the resume subsystem.
 *
 * Phase 5 introduced `jobs.resume.*` events without pod/host/request
 * context. Multi-pod debugging required knowing which pod saw what; this
 * helper centralizes the propagation of `pod_id`, `hostname`,
 * `request_id`, `trace_id`, `queue`, and `worker` into every log line
 * produced by the resume subsystem.
 *
 * Two entry points:
 *   - initJob(): called from job handle() to bind pod/queue/worker to
 *     the per-process log context (Log::withContext). Survives the
 *     lifetime of the worker process.
 *   - initRequest(): called from HTTP middleware; binds request_id and
 *     trace_id (Log::shareContext). The same fields propagate across
 *     the request → job handoff because Horizon shares the context.
 *
 * One emission helper:
 *   - event(): single channel for structured events so every
 *     `jobs.resume.*` line carries the merged context automatically.
 *
 * Backward compat: any code that still calls Log::info('jobs.resume.*')
 * directly will work, but ONLY if the Monolog processor in
 * AppServiceProvider is registered. This helper is the canonical path.
 */
class ResumeLogContext
{
    /** Per-process job context, set once at handle() entry. */
    private static ?array $jobContext = null;

    /** True after initJob() ran in the current process. */
    public static function isInitialized(): bool
    {
        return self::$jobContext !== null;
    }

    /**
     * Bind pod_id/hostname/queue/worker to Log::withContext().
     *
     * Calling this multiple times is safe; the latest values win.
     * Each call updates both the in-process store and the Monolog
     * channel so messages issued after the call see the merge.
     */
    public static function initJob(?string $queue = null, ?string $worker = null): void
    {
        if (self::$jobContext === null) {
            self::$jobContext = [
                'pod_id' => self::podId(),
                'hostname' => self::hostName(),
            ];
        }
        self::$jobContext['queue'] = $queue;
        self::$jobContext['worker'] = $worker;
        Log::withContext(self::$jobContext);
    }

    /**
     * Bind a request-scoped request_id and trace_id. Called once per
     * HTTP request from RequestContextMiddleware. trace_id is the
     * same as request_id unless an upstream proxy supplies one via
     * X-Trace-Id — that combination is useful when correlating a
     * browser session across multiple pods that share a load balancer.
     */
    public static function initRequest(?string $requestId = null, ?string $traceId = null): void
    {
        Log::shareContext([
            'request_id' => $requestId ?: (string) Str::uuid(),
            'trace_id' => $traceId ?: (string) Str::uuid(),
        ]);
    }

    /**
     * Single-channel emit. Every structural event in the resume
     * subsystem goes through here so the merge-context is guaranteed.
     *
     * The event name becomes the second segment of `jobs.resume.<name>`
     * which is what the ops log pipeline greps for.
     */
    public static function event(string $name, array $payload = []): void
    {
        Log::info('jobs.resume.' . $name, $payload);
    }

    /**
     * Warning-level event. Use sparingly — most event() calls should
     * be Log::info so the alert pipeline (`jobs.resume.alert`) can
     * distinguish true alerts from operator-curious tracing.
     */
    public static function warning(string $name, array $payload = []): void
    {
        Log::warning('jobs.resume.' . $name, $payload);
    }

    /** Read-only snapshot of the currently bound job context. */
    public static function currentJobContext(): array
    {
        return self::$jobContext ?? [];
    }

    /** Cheap, stable pod identifier. Falls back to process PID-less form. */
    public static function podId(): string
    {
        $name = gethostname();
        if ($name === false || $name === null || $name === '') {
            return 'pod-' . getmypid();
        }
        return $name;
    }

    /** Same as podId() — retention of the legacy phrase used by ops. */
    public static function hostName(): string
    {
        $name = php_uname('n');
        if ($name === false || $name === null || $name === '') {
            return self::podId();
        }
        return $name;
    }
}
