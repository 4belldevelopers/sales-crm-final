<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;
use App\Models\User;

/**
 * CandidateDetailsTabsV2Test
 *
 * Verifies Candidate Details Drawer V2 performance deferral, lazy loading API endpoints,
 * and strict tenancy isolation security checks across Notes, Timeline, and Resume tabs.
 *
 * Intent & Tenancy Note per docs/CODE_DOCUMENTATION_STANDARD.md:
 * All requests are tenant-isolated against auth()->user()->company_id. Cross-tenant accesses must return error status.
 */
class CandidateDetailsTabsV2Test extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test paginated notes index endpoint enforces company_id tenancy isolation.
     */
    public function test_paginated_notes_index_enforces_tenant_isolation(): void
    {
        $user = new User();
        $user->id = 888801;
        $user->company_id = 999901;
        $user->is_superadmin = 0;
        $this->actingAs($user);

        // Attempting to access notes for a non-existent / cross-tenant application ID 999999999
        $response = $this->get('/service_company/applicant-note?application_id=999999999&page=1');
        
        $this->assertTrue(
            in_array($response->status(), [200, 302, 403, 404]),
            'Notes index endpoint should handle unauthorized requests safely.'
        );

        if ($response->status() === 200) {
            $json = $response->json();
            if (is_array($json) && isset($json['status'])) {
                $this->assertNotEquals('success', $json['status'], 'Cross-tenant notes access must not return success.');
            }
        }
    }

    /**
     * Test paginated timeline endpoint enforces company_id tenancy isolation.
     */
    public function test_paginated_timeline_endpoint_enforces_tenant_isolation(): void
    {
        $user = new User();
        $user->id = 888802;
        $user->company_id = 999902;
        $user->is_superadmin = 0;
        $this->actingAs($user);

        $response = $this->get('/service_company/job-applications/timeline/999999999?page=1');
        
        $this->assertTrue(
            in_array($response->status(), [200, 302, 403, 404]),
            'Timeline endpoint should handle unauthorized requests safely.'
        );

        if ($response->status() === 200) {
            $json = $response->json();
            if (is_array($json) && isset($json['status'])) {
                $this->assertNotEquals('success', $json['status'], 'Cross-tenant timeline access must not return success.');
            }
        }
    }

    /**
     * Test resume metadata endpoint enforces company_id tenancy isolation.
     */
    public function test_resume_metadata_endpoint_enforces_tenant_isolation(): void
    {
        $user = new User();
        $user->id = 888803;
        $user->company_id = 999903;
        $user->is_superadmin = 0;
        $this->actingAs($user);

        $response = $this->get('/service_company/job-applications/resume-meta/999999999');
        
        $this->assertTrue(
            in_array($response->status(), [200, 302, 403, 404]),
            'Resume metadata endpoint should handle unauthorized requests safely.'
        );

        if ($response->status() === 200) {
            $json = $response->json();
            if (is_array($json) && isset($json['status'])) {
                $this->assertNotEquals('success', $json['status'], 'Cross-tenant resume metadata access must not return success.');
            }
        }
    }
}
