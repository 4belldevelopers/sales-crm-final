<?php

namespace Tests\Feature;

use App\Jobs\DecrementPipelineCountersJob;
use App\Jobs\UpdatePipelineCountersJob;
use ReflectionClass;
use Tests\TestCase;

/**
 * CandidatePipelineCountTest — Verifies that adding candidates with master_status_id = 8
 * (InReview / default unassigned status) does NOT increment the Sourcing stage count.
 */
class CandidatePipelineCountTest extends TestCase
{
    /**
     * Test that UpdatePipelineCountersJob stageFromMasterStatus maps status 8 to null
     * and status 1 to 'sourcing'.
     */
    public function test_update_pipeline_counters_job_stage_mapping(): void
    {
        $job = new ReflectionClass(UpdatePipelineCountersJob::class);
        $method = $job->getMethod('stageFromMasterStatus');
        $method->setAccessible(true);

        $instance = new UpdatePipelineCountersJob(1);

        // Status 8 (InReview) should NOT map to any stage (returns null)
        $this->assertNull($method->invoke($instance, 8));

        // Status 1 (Sourcing) must map to 'sourcing'
        $this->assertEquals('sourcing', $method->invoke($instance, 1));

        // Status 2 (Screening) must map to 'screening'
        $this->assertEquals('screening', $method->invoke($instance, 2));

        // Status 3 (Evaluation) must map to 'evaluation'
        $this->assertEquals('evaluation', $method->invoke($instance, 3));

        // Status 4 (Negotiation) must map to 'negotiation'
        $this->assertEquals('negotiation', $method->invoke($instance, 4));

        // Status 5 (Offered) must map to 'offered'
        $this->assertEquals('offered', $method->invoke($instance, 5));

        // Status 6 (Joined) must map to 'joined'
        $this->assertEquals('joined', $method->invoke($instance, 6));

        // Status 7 and 9 (Rejected) must map to 'rejected'
        $this->assertEquals('rejected', $method->invoke($instance, 7));
        $this->assertEquals('rejected', $method->invoke($instance, 9));
    }

    /**
     * Test that DecrementPipelineCountersJob stageFromMasterStatus maps status 8 to null
     * and status 1 to 'sourcing'.
     */
    public function test_decrement_pipeline_counters_job_stage_mapping(): void
    {
        $job = new ReflectionClass(DecrementPipelineCountersJob::class);
        $method = $job->getMethod('stageFromMasterStatus');
        $method->setAccessible(true);

        $instance = new DecrementPipelineCountersJob(1);

        // Status 8 (InReview) should NOT map to any stage (returns null)
        $this->assertNull($method->invoke($instance, 8));

        // Status 1 (Sourcing) must map to 'sourcing'
        $this->assertEquals('sourcing', $method->invoke($instance, 1));
    }
}
