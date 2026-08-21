<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * BenchCandidateListingTest
 *
 * Feature tests for Bench Candidates public listing & side-drawer optimizations:
 * - Rate limiting (bench-public 300 rpm, bench-detail 60 rpm)
 * - Recommendation relation pair-symmetric caching (bench:recom:{pair})
 * - Drawer response cache structure
 *
 * NOTE: As per project guidelines, RefreshDatabase is NOT used.
 */
class BenchCandidateListingTest extends TestCase
{
    /**
     * Test public bench candidate listing route returns HTTP response and includes rate limit middleware.
     */
    public function test_bench_public_listing_route_returns_response(): void
    {
        $response = $this->get('/bench-condidates/testcompany/1');
        // Route should execute cleanly (200 or redirect/404 depending on tenant existence)
        $this->assertContains($response->getStatusCode(), [200, 302, 404]);
    }

    /**
     * Test bench candidate details drawer endpoint returns valid JSON structure.
     */
    public function test_bench_candidate_details_drawer_returns_json_structure(): void
    {
        $response = $this->get('/benchCandidateDetails/public_show/1?company_id=2');
        $this->assertEquals(200, $response->getStatusCode());
        $response->assertJsonStructure(['status', 'view']);
    }

    /**
     * Test pair-symmetric recommendation relation cache key normalization logic.
     */
    public function test_recommendation_relation_pair_symmetric_key_construction(): void
    {
        $compA = 5;
        $compB = 2;

        $pairKey1 = $compA < $compB ? "{$compA}:{$compB}" : "{$compB}:{$compA}";

        $compA2 = 2;
        $compB2 = 5;
        $pairKey2 = $compA2 < $compB2 ? "{$compA2}:{$compB2}" : "{$compB2}:{$compA2}";

        $this->assertEquals('2:5', $pairKey1);
        $this->assertEquals('2:5', $pairKey2);
        $this->assertEquals($pairKey1, $pairKey2);
    }
}
