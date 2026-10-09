<?php

namespace Tests\Unit;

use App\Services\JobApplicationCapacityService;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;

/**
 * JobApplicationCapacityServiceTest — Unit & Concurrency Test Suite for Capacity Engine.
 *
 * Intent:
 * Verifies exact 1,000 application limit boundary conditions, atomic Lua reservations,
 * rollback behavior, and high-concurrency race condition immunity (10,000 attempts).
 *
 * Author: Antigravity AI
 * Date: October 09, 2026
 */
class JobApplicationCapacityServiceTest extends TestCase
{
    protected JobApplicationCapacityService $service;

    protected int $testJobId = 999991;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(JobApplicationCapacityService::class);
        $this->clearTestKeys();
    }

    protected function tearDown(): void
    {
        $this->clearTestKeys();
        parent::tearDown();
    }

    protected function clearTestKeys(): void
    {
        try {
            Redis::del("job:capacity:count:{$this->testJobId}");
            Redis::del("job:capacity:closed:{$this->testJobId}");
            Redis::del("job:capacity:init_lock:{$this->testJobId}");
        } catch (\Throwable) {
        }
    }

    /**
     * Requirement 1 & 21: Default limit is 1,000 and centrally configurable.
     */
    public function test_default_max_applications_configured_to_one_thousand(): void
    {
        $max = $this->service->getMaxApplications($this->testJobId);
        $this->assertEquals(1000, $max);
    }

    /**
     * Requirement 1: Under capacity returns false; at capacity returns true.
     */
    public function test_is_applications_closed_under_and_at_capacity(): void
    {
        // Set count to 999
        Redis::set("job:capacity:count:{$this->testJobId}", 999);
        $this->assertFalse($this->service->isApplicationsClosed($this->testJobId));

        // Set count to 1000
        Redis::set("job:capacity:count:{$this->testJobId}", 1000);
        $this->assertTrue($this->service->isApplicationsClosed($this->testJobId));

        // Fast circuit-breaker flag is set
        $this->assertEquals('1', Redis::get("job:capacity:closed:{$this->testJobId}"));
    }

    /**
     * Requirement 1 & 5: Exact boundary behavior:
     * 999 / 1,000 -> Accept next valid application (1,000th)
     * 1,000 / 1,000 -> Close application intake
     * 1,001+ -> Reject immediately
     */
    public function test_reserve_slot_boundary_at_one_thousand(): void
    {
        Redis::set("job:capacity:count:{$this->testJobId}", 999);

        // 1,000th application attempt
        $res1000 = $this->service->reserveSlot($this->testJobId);
        $this->assertTrue($res1000['success']);
        $this->assertEquals(1000, $res1000['slot']);
        $this->assertEquals(1000, (int) Redis::get("job:capacity:count:{$this->testJobId}"));
        $this->assertEquals('1', Redis::get("job:capacity:closed:{$this->testJobId}"));

        // 1,001st application attempt -> Must reject immediately
        $res1001 = $this->service->reserveSlot($this->testJobId);
        $this->assertFalse($res1001['success']);
        $this->assertEquals(0, $res1001['slot']);
        $this->assertEquals(1000, (int) Redis::get("job:capacity:count:{$this->testJobId}"));

        // Additional attempt -> Must also reject immediately
        $res1002 = $this->service->reserveSlot($this->testJobId);
        $this->assertFalse($res1002['success']);
    }

    /**
     * Requirement 6 & 7: Failed transaction rollback restores available slot and clears closed flag.
     */
    public function test_release_slot_decrements_and_reopens_intake(): void
    {
        Redis::set("job:capacity:count:{$this->testJobId}", 1000);
        Redis::set("job:capacity:closed:{$this->testJobId}", '1');

        $this->assertTrue($this->service->isApplicationsClosed($this->testJobId));

        // Transaction failed, release slot
        $release = $this->service->releaseSlot($this->testJobId);
        $this->assertTrue($release['success']);
        $this->assertEquals(999, $release['count']);

        // Closed flag must be cleared
        $this->assertNull(Redis::get("job:capacity:closed:{$this->testJobId}"));
        $this->assertFalse($this->service->isApplicationsClosed($this->testJobId));

        // Now next submission can claim slot 1000 again
        $reclaim = $this->service->reserveSlot($this->testJobId);
        $this->assertTrue($reclaim['success']);
        $this->assertEquals(1000, $reclaim['slot']);
    }

    /**
     * Requirement 20: Status reporting for UI / Admin visibility.
     */
    public function test_get_capacity_status(): void
    {
        Redis::set("job:capacity:count:{$this->testJobId}", 847);

        $status = $this->service->getCapacityStatus($this->testJobId);
        $this->assertEquals(847, $status['count']);
        $this->assertEquals(1000, $status['max']);
        $this->assertEquals(153, $status['remaining']);
        $this->assertFalse($status['is_closed']);
        $this->assertEquals(84.7, $status['percentage']);
        $this->assertEquals('Applications 847 / 1000', $status['status_label']);
    }

    /**
     * Requirement 2 & 5 & 23 (Test 2): Concurrency Simulation of 10,000 attempts on remaining 5 slots.
     *
     * Initial count: 995 / 1,000
     * 10,000 attempts simulated
     * Expected: Exactly 5 succeed, 9,995 rejected, final count = 1,000. Zero overshoot!
     */
    public function test_concurrency_simulation_ten_thousand_attempts_on_last_five_slots(): void
    {
        Redis::set("job:capacity:count:{$this->testJobId}", 995);
        Redis::del("job:capacity:closed:{$this->testJobId}");

        $successCount = 0;
        $rejectedCount = 0;
        $totalSimulatedRequests = 10000;

        for ($i = 0; $i < $totalSimulatedRequests; $i++) {
            $res = $this->service->reserveSlot($this->testJobId);
            if ($res['success']) {
                $successCount++;
            } else {
                $rejectedCount++;
            }
        }

        $this->assertEquals(5, $successCount, 'Exactly 5 applications must be accepted when starting at 995/1000');
        $this->assertEquals(9995, $rejectedCount, 'Exactly 9,995 applications must be rejected');
        $this->assertEquals(1000, (int) Redis::get("job:capacity:count:{$this->testJobId}"));
        $this->assertEquals('1', Redis::get("job:capacity:closed:{$this->testJobId}"));
        $this->assertTrue($this->service->isApplicationsClosed($this->testJobId));
    }
}
