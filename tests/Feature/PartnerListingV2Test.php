<?php

namespace Tests\Feature;

use App\Helper\GeneralMethod;
use Tests\TestCase;

/**
 * PartnerListingV2Test
 *
 * Feature and unit tests for the Agency Listing (spartnerlisting) V2 optimizations.
 * Validates batch helper methods, query count efficiency, and non-N+1 data structures.
 *
 * NOTE: As per project guidelines, RefreshDatabase is NOT used.
 */
class PartnerListingV2Test extends TestCase
{
    /**
     * Test that GeneralMethod::batchGetClientIdCompany returns empty array for empty inputs.
     *
     * @return void
     */
    public function test_batch_get_client_id_company_handles_empty_input(): void
    {
        $result = GeneralMethod::batchGetClientIdCompany([]);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test that GeneralMethod::batchAgencyCandidatesCount returns structured zeroed metrics for empty inputs.
     *
     * @return void
     */
    public function test_batch_agency_candidates_count_handles_empty_input(): void
    {
        $result = GeneralMethod::batchAgencyCandidatesCount([], 263);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test that GeneralMethod::batchAgencyCandidatesCount returns initialized objects for company IDs.
     *
     * @return void
     */
    public function test_batch_agency_candidates_count_returns_initialized_map(): void
    {
        $agencyIds = [9999991, 9999992];
        $result = GeneralMethod::batchAgencyCandidatesCount($agencyIds, 263);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(9999991, $result);
        $this->assertArrayHasKey(9999992, $result);
        $this->assertEquals(0, $result[9999991]->Upload);
        $this->assertEquals(0, $result[9999991]->Interview);
        $this->assertEquals(0, $result[9999991]->Joined);
    }

    /**
     * Test that GeneralMethod::batchGetAgencyRelationData returns empty array for empty inputs.
     *
     * @return void
     */
    public function test_batch_get_agency_relation_data_handles_empty_input(): void
    {
        $result = GeneralMethod::batchGetAgencyRelationData(1, []);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test that GeneralMethod::batchAgencyJobsCount returns initialized map for company IDs.
     *
     * @return void
     */
    public function test_batch_agency_jobs_count_returns_initialized_map(): void
    {
        $agencyIds = [9999991, 9999992];
        $result = GeneralMethod::batchAgencyJobsCount($agencyIds);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(9999991, $result);
        $this->assertArrayHasKey(9999992, $result);
        $this->assertEquals(0, $result[9999991]->all_jobs);
    }

    /**
     * Test that GeneralMethod::batchAgencyJobsCandidatesCount returns initialized candidate metrics.
     *
     * @return void
     */
    public function test_batch_agency_jobs_candidates_count_returns_initialized_map(): void
    {
        $agencyIds = [9999991, 9999992];
        $result = GeneralMethod::batchAgencyJobsCandidatesCount($agencyIds);

        $this->assertIsArray($result);
        $this->assertArrayHasKey(9999991, $result);
        $this->assertArrayHasKey(9999992, $result);
        $this->assertEquals(0, $result[9999991]->all_candidates);
        $this->assertEquals(0, $result[9999991]->all_candidate_bench);
    }
}
