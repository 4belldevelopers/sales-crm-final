<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureIdempotentSubmission — Idempotency Middleware for Prevent Duplicate Form Submissions.
 *
 * Intent: Intercepts POST/PUT form submission requests carrying an `X-Idempotency-Key` header.
 * Caches initial successful responses in Redis for 10 minutes; returns cached payload on retries to prevent duplicate DB writes.
 *
 * Tenancy Note: Scope is uniquely defined by client UUID idempotency key.
 *
 * Author: Antigravity AI
 * Date: August 14, 2026
 */
class EnsureIdempotentSubmission
{
    public const TTL_SECONDS = 600; // 10 minutes

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $idempotencyKey = $request->header('X-Idempotency-Key');

        if (! $idempotencyKey || ! $request->isMethodSafe()) {
            if ($idempotencyKey && in_array($request->method(), ['POST', 'PUT', 'PATCH'], true)) {
                $cacheKey = 'idempotency:'.$idempotencyKey;

                if (Cache::has($cacheKey)) {
                    $cachedData = Cache::get($cacheKey);

                    return response()->json($cachedData['content'], $cachedData['status'], $cachedData['headers']);
                }

                $response = $next($request);

                if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
                    $content = json_decode($response->getContent(), true) ?: $response->getContent();
                    Cache::put($cacheKey, [
                        'content' => $content,
                        'status' => $response->getStatusCode(),
                        'headers' => ['X-Cache-Lookup' => 'HIT-IDEMPOTENT'],
                    ], self::TTL_SECONDS);
                }

                return $response;
            }
        }

        return $next($request);
    }
}
