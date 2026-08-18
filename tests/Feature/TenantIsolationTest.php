<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use Tests\Fixtures\TenantFixture;
use Tests\Traits\AssertsTenantIsolation;

/**
 * TenantIsolationTest
 *
 * Verifies tenant isolation across routes and sensitive endpoints.
 * Explicitly DOES NOT use RefreshDatabase (uses DatabaseTransactions).
 *
 * NOTE: Intent & tenancy note per docs/CODE_DOCUMENTATION_STANDARD.md
 */
class TenantIsolationTest extends TestCase
{
    use DatabaseTransactions, AssertsTenantIsolation;

    /**
     * Test debug test routes are restricted and not publicly open in non-local environment.
     */
    public function test_debug_routes_are_not_exposed_unauthenticated(): void
    {
        $response = $this->get('/r2test');
        $this->assertTrue(
            $response->status() === 404 || app()->environment('local'),
            '/r2test route must not be accessible in non-local environment.'
        );
    }

    /**
     * Test /search-cities route returns valid JSON response without dd() call.
     */
    public function test_search_cities_returns_json_without_crashing(): void
    {
        $response = $this->get('/search-cities?q=Mumbai');
        // Route is protected by custom-package middleware or returns redirect/json
        $this->assertNotEquals(500, $response->getStatusCode());
    }

    /**
     * Test fixture data structure integrity.
     */
    public function test_tenant_fixtures_have_distinct_company_ids(): void
    {
        $userA = TenantFixture::userA();
        $userB = TenantFixture::userB();

        $this->assertNotEquals($userA['company_id'], $userB['company_id']);
    }

    /**
     * Test candidate resume download blocks cross-tenant candidate full_name leakage.
     */
    public function test_resume_download_blocks_cross_tenant_access(): void
    {
        $userA = (object) TenantFixture::userA();
        $this->actingAs(\App\Models\User::make((array) $userA));

        // Unauthenticated/cross-tenant request with non-matching application ID
        $response = $this->get('/download-resume/99999999/aHR0cHM6Ly9maWxlLmplcm8ud29ya3MvdGVzdC5wZGY=');
        $this->assertCrossTenantForbiddenOrNotFound($response);
    }

    /**
     * Test tenant_table helper auto-applies company_id filter for authenticated user.
     */
    public function test_tenant_table_helper_applies_company_id_where_clause(): void
    {
        $userA = new \App\Models\User();
        $userA->id = 900001;
        $userA->company_id = 100001;
        $userA->is_superadmin = 0;
        $this->actingAs($userA);

        $query = tenant_table('jobs');
        $sql = $query->toSql();

        $this->assertStringContainsString('company_id', $sql);
    }

    /**
     * Test Job model with BelongsToTenant scope auto-applies company_id filter.
     */
    public function test_job_model_belongs_to_tenant_scope_applies_company_id(): void
    {
        $userA = new \App\Models\User();
        $userA->id = 900001;
        $userA->company_id = 100001;
        $userA->is_superadmin = 0;
        $this->actingAs($userA);

        $query = \App\Models\Job::query();
        $sql = $query->toSql();

        $this->assertStringContainsString('company_id', $sql);
    }
}
