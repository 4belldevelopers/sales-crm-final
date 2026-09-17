<?php

namespace Tests\Feature\Auth;

use App\Http\Middleware\EnsureOneDeviceLogin;
use App\Support\DeviceSessionManager;
use Illuminate\Auth\GenericUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

/**
 * V2_login Phase 6.1 — EnsureOneDeviceLogin middleware unit tests.
 *
 * Tests the three-stage gate directly on the middleware handle() method,
 * without a real DB or route dispatch:
 *   1. Account status — suspended users are kicked immediately.
 *   2. Epoch gate — sessions born before the latest block epoch are killed.
 *   3. Single-device — only the Redis-registered session is allowed through.
 *
 * Auth state is seeded via Auth::setUser(); Cache uses CACHE_STORE=array (safe).
 * Redis is mocked for DeviceSessionManager calls in the single-device tests.
 */
class EnsureOneDeviceLoginTest extends TestCase
{
    private EnsureOneDeviceLogin $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new EnsureOneDeviceLogin;
    }

    private function activeUser(int $id = 1, int $companyId = 1): GenericUser
    {
        return new GenericUser(['id' => $id, 'company_id' => $companyId, 'status' => 'active']);
    }

    private function makeRequest(): Request
    {
        $request = Request::create('/dashboard', 'GET');
        $request->setLaravelSession($this->app->make('session.store'));

        return $request;
    }

    // ── Stage 0: unauthenticated passthrough ────────────────────────────────

    public function test_unauthenticated_request_passes_through(): void
    {
        Auth::shouldReceive('check')->once()->andReturn(false);

        $reached = false;
        $this->middleware->handle($this->makeRequest(), function () use (&$reached) {
            $reached = true;

            return response('ok');
        });

        $this->assertTrue($reached, 'Next handler must be called for unauthenticated requests');
    }

    // ── Stage 1: account status ─────────────────────────────────────────────

    public function test_inactive_user_is_redirected_to_login(): void
    {
        $user = new GenericUser(['id' => 2, 'company_id' => 1, 'status' => 'inactive']);
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('logout')->once();

        $response = $this->middleware->handle($this->makeRequest(), fn () => response('ok'));

        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->headers->get('Location'));
    }

    public function test_active_user_status_check_passes(): void
    {
        $user = $this->activeUser();
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('logout')->never();

        // Skip epoch + device checks by seeding them as safe
        Redis::shouldReceive('get')->andReturn(session()->getId());

        $reached = false;
        $this->middleware->handle($this->makeRequest(), function () use (&$reached) {
            $reached = true;

            return response('ok');
        });

        $this->assertTrue($reached);
    }

    // ── Stage 2: epoch gate (user-level) ────────────────────────────────────

    public function test_epoch_gate_kicks_session_born_before_user_block(): void
    {
        $user = $this->activeUser(3, 1);
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('logout')->once();

        $loginAt = now()->subHour()->timestamp;
        $userEpoch = now()->timestamp; // epoch is AFTER login → session is stale

        $request = $this->makeRequest();
        $request->getSession()->put('login_at', $loginAt);
        Cache::put('user:epoch:3', $userEpoch, 3600);

        $response = $this->middleware->handle($request, fn () => response('ok'));

        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->headers->get('Location'));
    }

    public function test_epoch_gate_kicks_session_born_before_company_block(): void
    {
        $user = $this->activeUser(4, 7);
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('logout')->once();

        $loginAt = now()->subHour()->timestamp;
        $companyEpoch = now()->timestamp; // company blocked after this session logged in

        $request = $this->makeRequest();
        $request->getSession()->put('login_at', $loginAt);
        Cache::put('company:epoch:7', $companyEpoch, 3600);

        $response = $this->middleware->handle($request, fn () => response('ok'));

        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->headers->get('Location'));
    }

    public function test_epoch_gate_allows_session_born_after_epoch(): void
    {
        $user = $this->activeUser(5, 1);
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('logout')->never();

        $loginAt = now()->timestamp;
        $userEpoch = now()->subHour()->timestamp; // epoch is BEFORE login → session is fresh

        $request = $this->makeRequest();
        $request->getSession()->put('login_at', $loginAt);
        Cache::put('user:epoch:5', $userEpoch, 3600);

        // No company epoch set (defaults to 0 → always passes)
        Redis::shouldReceive('get')->andReturn(session()->getId());

        $reached = false;
        $this->middleware->handle($request, function () use (&$reached) {
            $reached = true;

            return response('ok');
        });

        $this->assertTrue($reached);
    }

    public function test_epoch_gate_skipped_when_login_at_not_in_session(): void
    {
        $user = $this->activeUser(6, 1);
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('logout')->never();

        // No login_at in session — epoch gate short-circuits (0 is never < 0)
        Cache::put('user:epoch:6', now()->timestamp, 3600);
        Redis::shouldReceive('get')->andReturn(session()->getId());

        $reached = false;
        $this->middleware->handle($this->makeRequest(), function () use (&$reached) {
            $reached = true;

            return response('ok');
        });

        $this->assertTrue($reached);
    }

    // ── Stage 3: single-device enforcement ─────────────────────────────────

    public function test_single_device_blocks_mismatched_session(): void
    {
        $user = $this->activeUser(7, 1);
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('logout')->once();

        Redis::shouldReceive('get')->with('user:session:7')->andReturn('OTHER-SESSION');

        $response = $this->middleware->handle($this->makeRequest(), fn () => response('ok'));

        $this->assertSame(302, $response->getStatusCode());
        $this->assertStringContainsString('/login', $response->headers->get('Location'));
    }

    public function test_single_device_passes_matching_session(): void
    {
        $user = $this->activeUser(8, 1);
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('logout')->never();

        $sessionId = session()->getId();
        Redis::shouldReceive('get')->with('user:session:8')->andReturn($sessionId);

        $reached = false;
        $this->middleware->handle($this->makeRequest(), function () use (&$reached) {
            $reached = true;

            return response('ok');
        });

        $this->assertTrue($reached);
    }

    public function test_single_device_fails_open_when_redis_is_down(): void
    {
        $user = $this->activeUser(9, 1);
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('logout')->never();

        Redis::shouldReceive('get')->andThrow(new \RuntimeException('Redis connection refused'));

        $reached = false;
        $this->middleware->handle($this->makeRequest(), function () use (&$reached) {
            $reached = true;

            return response('ok');
        });

        // fail-open: user stays in when Redis is down
        $this->assertTrue($reached);
    }

    // ── Super-admin token_check bypass ──────────────────────────────────────

    public function test_token_check_bypass_passes_through_without_device_check(): void
    {
        $user = $this->activeUser(1, 0);
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);

        $request = $this->makeRequest();
        $request->getSession()->put('token_check', 'SUPERCODE');

        // Pre-seed the rate-limit cache key so the middleware skips $user->save()
        // (GenericUser has no save(); the throttle guard prevents the call).
        Cache::put('token_check_save_1', true, 60);

        $reached = false;
        $this->middleware->handle($request, function () use (&$reached) {
            $reached = true;

            return response('ok');
        });

        $this->assertTrue($reached);
    }

    public function test_single_device_passes_when_feature_is_disabled(): void
    {
        config(['auth.single_device_login.enabled' => false]);

        $user = $this->activeUser(15, 1);
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('logout')->never();

        // Should not call Redis when disabled
        Redis::shouldReceive('get')->never();

        $reached = false;
        $this->middleware->handle($this->makeRequest(), function () use (&$reached) {
            $reached = true;

            return response('ok');
        });

        $this->assertTrue($reached);
    }

    public function test_single_device_passes_when_user_email_is_exempt(): void
    {
        config([
            'auth.single_device_login.enabled' => true,
            'auth.single_device_login.exempt_emails' => ['exempt@example.com'],
        ]);

        $user = new GenericUser(['id' => 16, 'company_id' => 1, 'status' => 'active', 'email' => 'exempt@example.com']);
        Auth::shouldReceive('check')->once()->andReturn(true);
        Auth::shouldReceive('user')->andReturn($user);
        Auth::shouldReceive('logout')->never();

        // Should not call Redis when exempt
        Redis::shouldReceive('get')->never();

        $reached = false;
        $this->middleware->handle($this->makeRequest(), function () use (&$reached) {
            $reached = true;

            return response('ok');
        });

        $this->assertTrue($reached);
    }
}
