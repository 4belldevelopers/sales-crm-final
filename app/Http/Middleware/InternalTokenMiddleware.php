<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Phase 6 — bearer-token gate for internal-only endpoints.
 *
 * Phase 6 introduces /api/internal/health/resume which exposes pod +
 * infrastructure state to internal tooling (load balancer probes,
 * uptime dashboards). It MUST NOT be reachable from the public front.
 * A static bearer token is sufficient: ops rotates it during deploys,
 * nothing automated writes here, and 401-by-default is correct.
 *
 * The expected token lives in config('resumes.health_token') which is
 * populated from env RESUMES_HEALTH_TOKEN. If the env is unset, the
 * endpoint refuses all requests — there is NO unauthenticated fallback
 * because there is no value in returning defaults to a probe that
 * cannot authenticate.
 */
class InternalTokenMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $expected = (string) config('resumes.health_token', '');
        $supplied = (string) ($request->bearerToken() ?? '');
        // Constant-time comparison guards against timing attacks even
        // though the threat model is narrow (host-internal only).
        if ($expected === '' || ! hash_equals($expected, $supplied)) {
            return response()->json([
                'status' => 'unauthorized',
                'message' => 'Missing or invalid internal token',
            ], 401);
        }
        return $next($request);
    }
}
