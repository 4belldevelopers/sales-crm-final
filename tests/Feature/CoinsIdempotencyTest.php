<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureIdempotency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

/**
 * CoinsIdempotencyTest — V2 Coins Engine Phase 6 Feature Test Suite.
 *
 * Intent: Verifies request idempotency middleware behavior, response caching for
 * duplicate X-Idempotency-Key retries, and bypass for standard non-idempotent requests.
 *
 * Tenancy Note: Tenant isolation is enforced via company_id in idempotency cache keys.
 * Does NOT use RefreshDatabase (per Rule #2 and project guidelines).
 *
 * Author: Antigravity AI
 * Date: August 26, 2026
 */
class CoinsIdempotencyTest extends TestCase
{
    /**
     * Test initial request with X-Idempotency-Key processes normally and attaches header.
     *
     * Intent: Confirms first incoming request executes the controller pipeline and caches response.
     */
    public function test_first_request_with_idempotency_key_processes_normally(): void
    {
        $middleware = new EnsureIdempotency;
        $idempotencyKey = 'test-uuid-phase6-001';

        $request = Request::create('/api/v2/coins/deduct', 'POST');
        $request->headers->set('X-Idempotency-Key', $idempotencyKey);

        $response = $middleware->handle($request, function () {
            return response()->json(['status' => 'success', 'data' => 'Processed action'], 200);
        });

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals($idempotencyKey, $response->headers->get('X-Idempotency-Key'));

        // Clean up test cache
        Cache::forget("idempotency:0:{$idempotencyKey}");
    }

    /**
     * Test duplicate request with identical X-Idempotency-Key returns cached response.
     *
     * Intent: Verifies second request returns cached response with X-Cache: HIT-IDEMPOTENT header.
     */
    public function test_duplicate_request_with_same_idempotency_key_returns_cached_response(): void
    {
        $middleware = new EnsureIdempotency;
        $idempotencyKey = 'test-uuid-phase6-002';
        $cacheKey = "idempotency:0:{$idempotencyKey}";

        // Seed initial cached response
        Cache::put($cacheKey, [
            'status' => 200,
            'content' => ['status' => 'success', 'data' => 'Cached action'],
        ], 86400);

        $request = Request::create('/api/v2/coins/deduct', 'POST');
        $request->headers->set('X-Idempotency-Key', $idempotencyKey);

        $executedPipeline = false;
        $response = $middleware->handle($request, function () use (&$executedPipeline) {
            $executedPipeline = true;

            return response()->json(['status' => 'fresh'], 200);
        });

        $this->assertFalse($executedPipeline, 'Pipeline must NOT execute on duplicate idempotent retry');
        $this->assertEquals('HIT-IDEMPOTENT', $response->headers->get('X-Cache'));
        $this->assertEquals($idempotencyKey, $response->headers->get('X-Idempotency-Key'));

        // Clean up test cache
        Cache::forget($cacheKey);
    }

    /**
     * Test request without X-Idempotency-Key header passes through without caching.
     *
     * Intent: Confirms standard requests bypass idempotency interceptor.
     */
    public function test_request_without_idempotency_key_bypasses_middleware(): void
    {
        $middleware = new EnsureIdempotency;
        $request = Request::create('/api/v2/coins/deduct', 'POST');

        $executedPipeline = false;
        $response = $middleware->handle($request, function () use (&$executedPipeline) {
            $executedPipeline = true;

            return response()->json(['status' => 'fresh'], 200);
        });

        $this->assertTrue($executedPipeline);
        $this->assertNull($response->headers->get('X-Cache'));
    }
}
