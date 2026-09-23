<?php

namespace Tests\Unit;

use App\Services\CandidateJobAlignmentService;
use Tests\TestCase;

/**
 * CandidateJobAlignmentServiceTest
 *
 * INTENT: Verifies that CandidateJobAlignmentService validates inputs safely,
 * handles edge cases, and provides reliable candidate alignment methods.
 */
class CandidateJobAlignmentServiceTest extends TestCase
{
    private CandidateJobAlignmentService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(CandidateJobAlignmentService::class);
    }

    public function test_service_instance_resolves_from_container(): void
    {
        $this->assertInstanceOf(CandidateJobAlignmentService::class, $this->service);
    }

    public function test_align_candidate_to_job_with_invalid_ids_returns_false(): void
    {
        $this->assertFalse($this->service->alignCandidateToJob(0, 0, 0));
        $this->assertFalse($this->service->alignCandidateToJob(-1, 10, 20));
        $this->assertFalse($this->service->alignCandidateToJob(10, 0, 20));
        $this->assertFalse($this->service->alignCandidateToJob(10, 20, 0));
    }

    public function test_align_candidate_to_job_with_non_existent_candidate_returns_false(): void
    {
        $result = $this->service->alignCandidateToJob(999999999, 999999999, 999999999, 1);
        $this->assertFalse($result);
    }
}
