<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * EnsureIdempotency — V2 Coins Engine Idempotency Middleware.
 *
 * Intent: Intercepts POST/PUT/DELETE API requests containing an 'X-Idempotency-Key' header.
 * If the key has already been processed for the current tenant company, returns the cached
 * HTTP response payload immediately without re-executing database debits or business actions.
 *
 * Tenancy Note: Idempotency keys are namespaced per tenant: 'idempotency:{company_id}:{key}'.
 *
 * Author: Antigravity AI
 * Date: August 26, 2026
 */
class EnsureIdempotency
{
    /**
     * Handle an incoming request with idempotency key verification.
     *
     * @param  \Illuminate\Http\Request  $request  Incoming HTTP request.
     * @param  \Closure  $next  Next request handler pipeline.
     * @return \Symfony\Component\HttpFoundation\Response Response object.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $idempotencyKey = $request->header('X-Idempotency-Key');

        // If no idempotency key header is present, pass request to next middleware
        if (! $idempotencyKey) {
            return $next($request);
        }

        $companyId = auth()->check() ? (int) auth()->user()->company_id : 0;
        $cacheKey = "idempotency:{$companyId}:{$idempotencyKey}";

        // Fast-path: Check if response for this idempotency key is already cached
        try {
            $cachedResponse = Cache::get($cacheKey);
            if ($cachedResponse !== null && is_array($cachedResponse)) {
                return response()->json($cachedResponse['content'], $cachedResponse['status'])
                    ->header('X-Cache', 'HIT-IDEMPOTENT')
                    ->header('X-Idempotency-Key', $idempotencyKey);
            }
        } catch (\Throwable $e) {
            // Non-blocking fallback if Cache driver is down
        }

        /** @var Response $response */
        $response = $next($request);

        // Cache successful response payloads (HTTP 200 - 299) for 24 hours (86400 seconds)
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() < 300) {
            try {
                $content = json_decode($response->getContent(), true) ?? $response->getContent();
                Cache::put($cacheKey, [
                    'status' => $response->getStatusCode(),
                    'content' => $content,
                ], 86400);
            } catch (\Throwable $e) {
                // Non-blocking cache write exception catch
            }
        }

        return $response->header('X-Idempotency-Key', $idempotencyKey);
    }
}
