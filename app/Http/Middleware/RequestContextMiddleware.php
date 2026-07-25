<?php

namespace App\Http\Middleware;

use App\Logging\ResumeLogContext;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * Phase 6 — request-scoped log context propagation.
 *
 * Picks up trace identifiers from upstream proxies (X-Request-Id /
 * X-Trace-Id) or generates UUIDs if absent, then binds them to the
 * shared log context so every downstream log line carries them. The
 * same IDs are echoed back in the response headers so callers can
 * reference them in support tickets.
 *
 * This middleware MUST be registered globally (web + api stacks) so
 * the IDs are available before any other handler logs. It runs cheaply
 * (header read + two Log::shareContext calls) and stores nothing on
 * disk.
 */
class RequestContextMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $requestId = $request->header('X-Request-Id') ?: (string) Str::uuid();
        $traceId = $request->header('X-Trace-Id') ?: $requestId;

        ResumeLogContext::initRequest($requestId, $traceId);

        // Surface the IDs so the client + any intermediate proxy can
        // correlate. Echo only if the request did not supply them.
        $response = $next($request);
        if (! $request->header('X-Request-Id')) {
            $response->headers->set('X-Request-Id', $requestId);
        }
        $response->headers->set('X-Trace-Id', $traceId);
        return $response;
    }
}
