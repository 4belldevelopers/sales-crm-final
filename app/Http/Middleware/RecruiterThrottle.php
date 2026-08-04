<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

/**
 * Protects the Jobs listing endpoints from per-recruiter request bursts.
 * 30 requests / 60s per authenticated user — well above normal browser usage,
 * blocks only F5 hammers and runaway scripts.
 *
 * Fail-open on Redis outage so infra issues never lock out real recruiters.
 */
class RecruiterThrottle
{
    private const LIMIT  = 30;
    private const WINDOW = 60;

    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->id();
        if (!$userId) {
            return $next($request);
        }

        try {
            $key   = "throttle:jobs:{$userId}";
            $count = Redis::incr($key);
            if ($count === 1) {
                Redis::expire($key, self::WINDOW);
            }
            if ($count > self::LIMIT) {
                return response()->json([
                    'error'       => 'rate_limit_exceeded',
                    'message'     => 'Too many requests. Please slow down.',
                    'retry_after' => self::WINDOW,
                ], 429);
            }
        } catch (\Throwable) {
            // Redis down — fail open; never block recruiters for infra issues
        }

        return $next($request);
    }
}
