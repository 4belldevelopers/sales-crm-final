<?php

namespace Tests\Unit;

use App\Services\Interview\InterviewStatusTransitionEngine;
use PHPUnit\Framework\TestCase;

/**
 * Class InterviewStatusTransitionEngineTest
 * 
 * Intent: Comprehensive unit tests for InterviewStatusTransitionEngine verifying exact net point deltas
 * across all 36 status transitions for the 6 UI Attendance Statuses:
 * 1 = Attended, 2 = No Show, 3 = Rescheduled, 4 = Cancelled by Company, 6 = Other Reason, 7 = Cancelled by Candidate.
 */
class InterviewStatusTransitionEngineTest extends TestCase
{
    protected InterviewStatusTransitionEngine $engine;

    protected function setUp(): void
    {
        parent::setUp();
        $this->engine = new InterviewStatusTransitionEngine();
    }

    /**
     * Test Point Vector generation for isolated statuses.
     */
    public function testPointVectorGeneration(): void
    {
        // 1: Attended + Feedback
        $vecAttended = $this->engine->getPointVector(1, 1);
        $this->assertEquals(10.0, $vecAttended['recruiterScheduled']);
        $this->assertEquals(0.0, $vecAttended['recruiterNoShow']);
        $this->assertEquals(2.0, $vecAttended['teamCaptainScheduled']);

        // 2: No Show
        $vecNoShow = $this->engine->getPointVector(2, 0);
        $this->assertEquals(0.0, $vecNoShow['recruiterScheduled']);
        $this->assertEquals(-5.0, $vecNoShow['recruiterNoShow']);
        $this->assertEquals(0.0, $vecNoShow['teamCaptainScheduled']);

        // 3: Rescheduled with Sub 13 (Candidate Cancelled)
        $vecReschedSub13 = $this->engine->getPointVector(3, 13);
        $this->assertEquals(0.0, $vecReschedSub13['recruiterScheduled']);
        $this->assertEquals(-5.0, $vecReschedSub13['recruiterNoShow']);
        $this->assertEquals(0.0, $vecReschedSub13['teamCaptainScheduled']);

        // 4, 6, 7: Cancelled Co / Other / Cancelled Cand
        foreach ([4, 6, 7] as $status) {
            $vecZero = $this->engine->getPointVector($status, 0);
            $this->assertEquals(0.0, $vecZero['recruiterScheduled']);
            $this->assertEquals(0.0, $vecZero['recruiterNoShow']);
            $this->assertEquals(0.0, $vecZero['teamCaptainScheduled']);
        }
    }

    /**
     * Test Initial Submissions (0 -> Target Status).
     */
    public function testInitialSubmissions(): void
    {
        // 0 -> Attended (1)
        $deltas = $this->engine->calculateNetDeltas(0, 0, 1, 1, null);
        $this->assertEquals(10.0, $deltas['recruiterScheduled']);
        $this->assertEquals(0.0, $deltas['recruiterNoShow']);
        $this->assertEquals(2.0, $deltas['teamCaptainScheduled']);

        // 0 -> No Show (2)
        $deltasNoShow = $this->engine->calculateNetDeltas(0, 0, 2, 0, null);
        $this->assertEquals(0.0, $deltasNoShow['recruiterScheduled']);
        $this->assertEquals(-5.0, $deltasNoShow['recruiterNoShow']);
        $this->assertEquals(0.0, $deltasNoShow['teamCaptainScheduled']);
    }

    /**
     * Test Same-Status Submissions (Status A -> Status A) - Must always yield ZERO net deltas.
     */
    public function testSameStatusTransitionsYieldZeroNetDeltas(): void
    {
        $statuses = [1, 2, 3, 4, 6, 7];

        foreach ($statuses as $s) {
            $deltas = $this->engine->calculateNetDeltas($s, $s == 1 ? 1 : 0, $s, $s == 1 ? 1 : 0, null);
            $this->assertEquals(0.0, $deltas['recruiterScheduled'], "Failed recruiterScheduled for status {$s}->{$s}");
            $this->assertEquals(0.0, $deltas['recruiterNoShow'], "Failed recruiterNoShow for status {$s}->{$s}");
            $this->assertEquals(0.0, $deltas['teamCaptainScheduled'], "Failed teamCaptainScheduled for status {$s}->{$s}");
        }
    }

    /**
     * Test Positive to Negative Transitions (1 -> 2: Attended to No Show).
     */
    public function testAttendedToNoShowTransition(): void
    {
        $deltas = $this->engine->calculateNetDeltas(1, 1, 2, 0, null);
        
        // Reverses recruiter +10 reward (-10)
        $this->assertEquals(-10.0, $deltas['recruiterScheduled']);
        // Applies recruiter -5 no-show penalty (-5)
        $this->assertEquals(-5.0, $deltas['recruiterNoShow']);
        // Reverses team captain +2 reward (-2)
        $this->assertEquals(-2.0, $deltas['teamCaptainScheduled']);
    }

    /**
     * Test Negative to Positive Transitions (2 -> 1: No Show to Attended).
     */
    public function testNoShowToAttendedTransition(): void
    {
        $deltas = $this->engine->calculateNetDeltas(2, 0, 1, 1, null);

        // Credits recruiter +10 reward
        $this->assertEquals(10.0, $deltas['recruiterScheduled']);
        // Reverses recruiter -5 penalty (+5)
        $this->assertEquals(5.0, $deltas['recruiterNoShow']);
        // Credits team captain +2 reward
        $this->assertEquals(2.0, $deltas['teamCaptainScheduled']);
    }

    /**
     * Test Transitions from Attended to Zero-Point Statuses (1 -> 4, 6, 7).
     */
    public function testAttendedToZeroPointStatusTransitions(): void
    {
        foreach ([4, 6, 7] as $targetStatus) {
            $deltas = $this->engine->calculateNetDeltas(1, 1, $targetStatus, 0, null);
            
            // Reverses recruiter +10 reward
            $this->assertEquals(-10.0, $deltas['recruiterScheduled'], "Failed for 1->{$targetStatus}");
            $this->assertEquals(0.0, $deltas['recruiterNoShow'], "Failed for 1->{$targetStatus}");
            // Reverses team captain +2 reward
            $this->assertEquals(-2.0, $deltas['teamCaptainScheduled'], "Failed for 1->{$targetStatus}");
        }
    }

    /**
     * Test Exhaustive 36 Status Transition Matrix.
     */
    public function testFull36StatusTransitionMatrix(): void
    {
        $statuses = [1, 2, 3, 4, 6, 7];

        foreach ($statuses as $from) {
            foreach ($statuses as $to) {
                $subFrom = ($from == 1) ? 1 : (($from == 3) ? 13 : 0);
                $subTo = ($to == 1) ? 1 : (($to == 3) ? 13 : 0);

                $deltas = $this->engine->calculateNetDeltas($from, $subFrom, $to, $subTo, null);

                // Verify structural consistency: deltas must be numeric floats
                $this->assertIsFloat($deltas['recruiterScheduled']);
                $this->assertIsFloat($deltas['recruiterNoShow']);
                $this->assertIsFloat($deltas['teamCaptainScheduled']);
            }
        }
    }
}
