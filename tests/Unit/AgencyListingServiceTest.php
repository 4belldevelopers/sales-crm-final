<?php

namespace Tests\Unit;

/**
 * Class AgencyListingServiceTest
 *
 * INTENT: Unit tests for AgencyListingService batch stat loading, empty input handling,
 * and location/industry pre-hydration.
 */

use App\Services\AgencyListingService;
use App\Services\ElasticService;
use Mockery;
use Tests\TestCase;

class AgencyListingServiceTest extends TestCase
{
    private AgencyListingService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $elasticMock = Mockery::mock(ElasticService::class);
        $this->service = new AgencyListingService($elasticMock);
    }

    public function test_get_batch_agency_stats_with_empty_input_returns_empty_array(): void
    {
        $result = $this->service->getBatchAgencyStats([], 1);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_batch_locations_with_empty_input_returns_empty_array(): void
    {
        $result = $this->service->getBatchLocations([]);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_batch_industries_with_empty_input_returns_empty_array(): void
    {
        $result = $this->service->getBatchIndustries([]);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    public function test_get_batch_relation_data_with_empty_input_returns_empty_array(): void
    {
        $result = $this->service->getBatchRelationData([], 1);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }
}
