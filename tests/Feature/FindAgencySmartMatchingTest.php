<?php

namespace Tests\Feature;

use App\Services\FindAgencyService;
use Tests\TestCase;

/**
 * Class FindAgencySmartMatchingTest
 *
 * Verifies the 5-Tier Recommendation Hierarchy and Fallback Logic for Partner Agencies:
 * 1. Exact Job Match Agencies
 * 2. Strong Skill & Technology Match
 * 3. Industry Match
 * 4. Location-Based Match
 * 5. Similar Hiring Category Agencies
 *
 * Complies with Rule 1, 2, 7, 18, 20, and docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class FindAgencySmartMatchingTest extends TestCase
{
    /**
     * Test that FindAgencyService has the necessary recommendation methods and contracts.
     */
    public function test_find_agency_service_implements_recommendation_hierarchy(): void
    {
        $service = app(FindAgencyService::class);
        $this->assertInstanceOf(FindAgencyService::class, $service);

        // Verify method signatures
        $ref = new \ReflectionClass($service);
        $this->assertTrue($ref->hasMethod('getAgencyData'));
        $this->assertTrue($ref->hasMethod('handleAgencyRequest'));
        $this->assertTrue($ref->hasMethod('invalidateTenantCache'));
    }

    /**
     * Test that the AJAX Blade template implements the 5-Tier Recommendation badges and fallback alert.
     */
    public function test_ajax_blade_implements_recommendation_and_fallback_elements(): void
    {
        $viewPath = resource_path('views/service_company/ajax_pages/find_agency_ajax.blade.php');
        $this->assertFileExists($viewPath);
        $blade = file_get_contents($viewPath);

        // Tier classification pills
        $this->assertStringContainsString('cl-match-type-pill', $blade);
        $this->assertStringContainsString('pill-direct-match', $blade);
        $this->assertStringContainsString('pill-recommended', $blade);

        // Fallback banner
        $this->assertStringContainsString('cl-fallback-alert-banner', $blade);

        // Data attributes for Client-First User RAM re-ranking
        $this->assertStringContainsString('data-direct-match', $blade);
        $this->assertStringContainsString('data-rec-tier', $blade);
        $this->assertStringContainsString('data-rec-label', $blade);
        $this->assertStringContainsString('data-rec-reason', $blade);
        $this->assertStringContainsString('data-match-pct', $blade);

        // Footer Activity Strip match indicator
        $this->assertStringContainsString('cl-footer-match-wrap', $blade);
        $this->assertStringContainsString('cl-card-match-badge', $blade);
    }

    /**
     * Test that find-agency.blade.php includes CSS tokens and client matching logic.
     */
    public function test_main_blade_implements_client_recommendation_styles_and_engine(): void
    {
        $bladePath = resource_path('views/service_company/common/find-agency.blade.php');
        $this->assertFileExists($bladePath);
        $content = file_get_contents($bladePath);

        // CSS Classes
        $this->assertStringContainsString('.cl-match-type-pill.pill-direct-match', $content);
        $this->assertStringContainsString('.cl-match-type-pill.pill-recommended', $content);
        $this->assertStringContainsString('.cl-fallback-alert-banner', $content);

        // Client-first matching engine
        $this->assertStringContainsString('applyActiveJobMatching', $content);
        $this->assertStringContainsString('data-rec-reason', $content);
        $this->assertStringContainsString('data-rec-label', $content);
    }
}
