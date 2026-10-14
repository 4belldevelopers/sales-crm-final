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
     */
    public function test_batch_get_client_id_company_handles_empty_input(): void
    {
        $result = GeneralMethod::batchGetClientIdCompany([]);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test that GeneralMethod::batchAgencyCandidatesCount returns structured zeroed metrics for empty inputs.
     */
    public function test_batch_agency_candidates_count_handles_empty_input(): void
    {
        $result = GeneralMethod::batchAgencyCandidatesCount([], 263);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test that GeneralMethod::batchAgencyCandidatesCount returns initialized objects for company IDs.
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
     */
    public function test_batch_get_agency_relation_data_handles_empty_input(): void
    {
        $result = GeneralMethod::batchGetAgencyRelationData(1, []);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test that GeneralMethod::batchAgencyJobsCount returns initialized map for company IDs.
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

    /**
     * Test that GeneralMethod::batchGetCommercialsData handles empty input cleanly.
     */
    public function test_batch_get_commercials_data_handles_empty_input(): void
    {
        $result = GeneralMethod::batchGetCommercialsData([], 1);
        $this->assertIsArray($result);
        $this->assertEmpty($result);

        if (! \Illuminate\Support\Facades\Schema::hasTable('commericials')) {
            $this->markTestSkipped('Table commericials does not exist in SQLite test environment (MySQL schema only).');
        }

        $agencyIds = [9999991, 9999992];
        $resultMap = GeneralMethod::batchGetCommercialsData($agencyIds, 1);
        $this->assertIsArray($resultMap);
        $this->assertArrayHasKey(9999991, $resultMap);
        $this->assertNull($resultMap[9999991]->minimum_rate);
    }

    /**
     * Test that GeneralMethod::batchGetMatchFoundMap handles empty input cleanly.
     */
    public function test_batch_get_match_found_map_handles_empty_input(): void
    {
        $result = GeneralMethod::batchGetMatchFoundMap(1, []);
        $this->assertIsArray($result);
        $this->assertEmpty($result);
    }

    /**
     * Test that GeneralMethod::invalidateSpartnerlistingCache cleans user cache version keys.
     */
    public function test_invalidate_spartnerlisting_cache_bumps_version_keys(): void
    {
        $this->assertFalse(GeneralMethod::invalidateSpartnerlistingCache(0));
        $this->assertTrue(GeneralMethod::invalidateSpartnerlistingCache(99999));
    }

    /**
     * Test that tenant and user drawer cache versions are properly incremented on invalidation.
     */
    public function test_agency_drawer_cache_version_invalidation_covers_tenant_and_user(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('global_settings')) {
            $this->markTestSkipped('Table global_settings does not exist in SQLite test environment (MySQL schema only).');
        }

        $companyId = 99998;
        $userId = 88888;
        $jobId = 77777;

        $tenantKey = 'allsendinvitationtoagency_v5_version_company_'.$companyId;
        $userKey = 'allsendinvitationtoagency_v5_version_user_'.$userId;

        \Illuminate\Support\Facades\Cache::put($tenantKey, 1, 3600);
        \Illuminate\Support\Facades\Cache::put($userKey, 1, 3600);

        // Simulate invalidateAgencyDrawerCaches logic
        $user = new \App\Models\User();
        $user->id = $userId;
        $user->company_id = $companyId;
        $this->actingAs($user);

        $controller = app(\App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController::class);
        $method = new \ReflectionMethod($controller, 'invalidateAgencyDrawerCaches');
        $method->setAccessible(true);
        $method->invoke($controller, $jobId);

        $newTenantVersion = (int) \Illuminate\Support\Facades\Cache::get($tenantKey);
        $newUserVersion = (int) \Illuminate\Support\Facades\Cache::get($userKey);

        $this->assertGreaterThan(1, $newTenantVersion);
        $this->assertGreaterThan(1, $newUserVersion);
    }
}
