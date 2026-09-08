<?php

namespace Tests\Feature;

use Tests\TestCase;

class BenchCandidateDetailsDrawerTest extends TestCase
{
    /**
     * Test benchCandidateDetails drawer endpoint returns successful JSON response with redesigned view.
     */
    public function test_bench_candidate_details_drawer_renders_modern_view(): void
    {
        $response = $this->get('/benchCandidateDetails/public_show/1?company_id=2');
        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'view']);
    }
}
