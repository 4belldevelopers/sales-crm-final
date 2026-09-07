<?php

namespace Tests\Unit;

/**
 * ShortlistBenchCandidateListingServiceTest — Unit tests for ShortlistBenchCandidateListingService.
 *
 * INTENT: Verifies single-pass batch hydration, graceful fallback on missing Elasticsearch index,
 * and zero-N+1 pre-hydrated property attachments for shortlisted bench candidate listing data.
 */

use App\Services\ElasticService;
use App\Services\ShortlistBenchCandidateListingService;
use Mockery;
use Tests\TestCase;

class ShortlistBenchCandidateListingServiceTest extends TestCase
{
    private ShortlistBenchCandidateListingService $service;

    private $elasticMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->elasticMock = Mockery::mock(ElasticService::class);
        $this->service = new ShortlistBenchCandidateListingService($this->elasticMock);
    }

    /**
     * Test that batchHydrateCandidates with empty hit array returns an empty array immediately.
     */
    public function test_batch_hydrate_candidates_with_empty_input_returns_empty_array(): void
    {
        $result = $this->service->batchHydrateCandidates([], 1, (object) ['id' => 1]);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test that getOfferStats returns default structure when Elasticsearch candidates index does not exist.
     */
    public function test_get_offer_stats_returns_default_when_index_missing(): void
    {
        $this->elasticMock->shouldReceive('indexExists')
            ->once()
            ->with(ELASTIC_CANDIDATES)
            ->andReturn(false);

        $stats = $this->service->getOfferStats(1, 1);

        $this->assertIsArray($stats);
        $this->assertArrayHasKey('hero', $stats);
        $this->assertArrayHasKey('tabs', $stats);
        $this->assertArrayHasKey('locations', $stats);
        $this->assertEquals(0, $stats['hero']['total_shortlisted']);
    }

    /**
     * Test that batchHydrateCandidates pre-populates _prehydrated_ confidential_allow and status properties.
     */
    public function test_batch_hydrate_candidates_attaches_prehydrated_properties(): void
    {
        $hits = [
            [
                'candidate_id' => 101,
                'wiseId' => 101,
                'status' => 1,
                'jobId' => 50,
                'job_id' => 50,
                'full_name' => 'John Doe',
                'duplicate' => 0,
            ],
        ];

        $user = (object) ['id' => 99, 'company_id' => 1];

        $hydrated = $this->service->batchHydrateCandidates($hits, 1, $user);

        $this->assertCount(1, $hydrated);
        $this->assertArrayHasKey('_prehydrated_master_status_name', $hydrated[0]);
        $this->assertArrayHasKey('_prehydrated_status_subline', $hydrated[0]);
        $this->assertArrayHasKey('_prehydrated_confidential_allow', $hydrated[0]);
    }
}
