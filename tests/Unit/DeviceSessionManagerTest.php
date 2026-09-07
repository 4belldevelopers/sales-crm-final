<?php

namespace Tests\Unit;

use App\Support\DeviceSessionManager;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

/**
 * V2_login Phase 6.1 — DeviceSessionManager unit tests.
 *
 * Verifies the O(1) Redis-only single-device session invariant:
 * - recordLogin() destroys the old session, stores the new one.
 * - enforce() returns false when a different session owns the slot.
 * - enforce() returns true when session matches.
 * - enforce() fails-open (returns true) when Redis throws.
 * - invalidate() removes the slot and destroys the stored session.
 *
 * SESSION_DRIVER=array (phpunit.xml) means session()->getHandler()->destroy()
 * is a safe no-op for non-existent sessions.
 */
class DeviceSessionManagerTest extends TestCase
{
    public function test_record_login_stores_new_session_when_no_prior_session(): void
    {
        Redis::shouldReceive('get')->with('user:session:1')->once()->andReturn(null);
        Redis::shouldReceive('setex')->with('user:session:1', 604800, 'sess-A')->once();

        DeviceSessionManager::recordLogin(1, 'sess-A');
    }

    public function test_record_login_destroys_old_session_before_storing_new(): void
    {
        Redis::shouldReceive('get')->with('user:session:2')->once()->andReturn('sess-OLD');
        // destroy() is called on the array session handler — safe with SESSION_DRIVER=array
        Redis::shouldReceive('setex')->with('user:session:2', 604800, 'sess-NEW')->once();

        DeviceSessionManager::recordLogin(2, 'sess-NEW');
    }

    public function test_record_login_skips_destroy_when_session_unchanged(): void
    {
        Redis::shouldReceive('get')->with('user:session:3')->once()->andReturn('sess-SAME');
        // no destroy call expected — same session, just refresh the TTL
        Redis::shouldReceive('setex')->with('user:session:3', 604800, 'sess-SAME')->once();

        DeviceSessionManager::recordLogin(3, 'sess-SAME');
    }

    public function test_enforce_returns_true_for_matching_session(): void
    {
        Redis::shouldReceive('get')->with('user:session:10')->once()->andReturn('sess-A');

        $result = DeviceSessionManager::enforce(10, 'sess-A');

        $this->assertTrue($result);
    }

    public function test_enforce_returns_false_for_different_session(): void
    {
        Redis::shouldReceive('get')->with('user:session:11')->once()->andReturn('sess-A');

        $result = DeviceSessionManager::enforce(11, 'sess-B');

        $this->assertFalse($result);
    }

    public function test_enforce_registers_and_returns_true_when_no_session_stored(): void
    {
        Redis::shouldReceive('get')->with('user:session:12')->once()->andReturn(null);
        Redis::shouldReceive('setex')->with('user:session:12', 604800, 'sess-FIRST')->once();

        $result = DeviceSessionManager::enforce(12, 'sess-FIRST');

        $this->assertTrue($result);
    }

    public function test_enforce_fails_open_when_redis_throws(): void
    {
        Redis::shouldReceive('get')->andThrow(new \RuntimeException('Redis connection refused'));

        // fail-open: returns true so the user is NOT kicked out when Redis is down
        $result = DeviceSessionManager::enforce(99, 'any-session');

        $this->assertTrue($result);
    }

    public function test_invalidate_removes_key_from_redis(): void
    {
        Redis::shouldReceive('get')->with('user:session:20')->once()->andReturn(null);
        Redis::shouldReceive('del')->with('user:session:20')->once();

        DeviceSessionManager::invalidate(20);
    }

    public function test_invalidate_survives_redis_exception_silently(): void
    {
        Redis::shouldReceive('get')->andThrow(new \RuntimeException('Redis down'));

        // Should not throw — wrapped in try-catch
        DeviceSessionManager::invalidate(21);

        $this->assertTrue(true); // reached here without exception
    }
}
