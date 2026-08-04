<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;
use Symfony\Component\HttpFoundation\Response;

/**
 * Atomic Lua sliding-window rate limiter for dashboard reads.
 * 60 requests / 60s per authenticated user, evaluated with microsecond precision.
 *
 * Use this on the Jobs index route if you need stronger guarantees than RecruiterThrottle.
 * RecruiterThrottle uses a simple counter (cheaper, 2 Redis ops); this uses a sorted-set
 * sliding window (1 Lua eval, no race between incr + expire).
 *
 * Register alias in bootstrap/app.php: 'recruiter.sliding' => self::class
 * Apply: ->middleware('recruiter.sliding') on the route.
 */
class RecruiterSlidingWindowThrottle
{
    private const MAX    = 60;
    private const WINDOW = 60; // seconds

    private const LUA = <<<'LUA'
local k      = KEYS[1]
local now    = tonumber(ARGV[1])
local window = tonumber(ARGV[2])
local max    = tonumber(ARGV[3])
redis.call('ZREMRANGEBYSCORE', k, '-inf', now - window * 1000)
local cnt = redis.call('ZCARD', k)
if cnt >= max then return 0 end
redis.call('ZADD', k, now, now)
redis.call('PEXPIRE', k, window * 1000)
return 1
LUA;

    public function handle(Request $request, Closure $next): Response
    {
        $userId = auth()->id();
        if (!$userId) {
            return $next($request);
        }

        try {
            $key    = "rslide:jobs:{$userId}";
            $nowMs  = (int) (microtime(true) * 1000);
            $result = Redis::eval(self::LUA, 1, $key, $nowMs, self::WINDOW, self::MAX);

            if (!$result) {
                return response()->json([
                    'error'       => 'rate_limit_exceeded',
                    'message'     => 'Too many requests. Please wait.',
                    'retry_after' => self::WINDOW,
                ], 429);
            }
        } catch (\Throwable) {
            // Redis down — fail open
        }

        return $next($request);
    }
}
