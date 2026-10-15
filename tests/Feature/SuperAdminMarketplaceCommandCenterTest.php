<?php

namespace Tests\Feature;

use App\Http\Controllers\SuperAdmin\SuperAdminCompanyController;
use App\Services\MarketplaceModerationService;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * SuperAdminMarketplaceCommandCenterTest
 *
 * Verifies God-Level Super Admin Marketplace Command Center:
 * - Route registration & HTTP methods
 * - Controller methods & contracts
 * - 3-Panel Moderation Cockpit Drawer views
 * - Risk engine & circumvention integration
 * - Sidebar navigation contracts
 */
class SuperAdminMarketplaceCommandCenterTest extends TestCase
{
    /**
     * Test all Super Admin Marketplace routes are registered.
     */
    public function test_superadmin_marketplace_routes_are_registered(): void
    {
        $this->assertTrue(
            Route::has('superadmin.company.job-public-market'),
            "Route 'superadmin.company.job-public-market' must be registered."
        );

        $this->assertTrue(
            Route::has('superadmin.company.marketshow-job-details'),
            "Route 'superadmin.company.marketshow-job-details' must be registered."
        );

        $this->assertTrue(
            Route::has('superadmin.company.job-approval'),
            "Route 'superadmin.company.job-approval' must be registered."
        );

        $this->assertTrue(
            Route::has('superadmin.company.market-auto-approval'),
            "Route 'superadmin.company.market-auto-approval' must be registered."
        );
    }

    /**
     * Test SuperAdminCompanyController contains all required methods.
     */
    public function test_superadmin_controller_has_moderation_methods(): void
    {
        $this->assertTrue(
            method_exists(SuperAdminCompanyController::class, 'jobPublicMarket'),
            'SuperAdminCompanyController must implement jobPublicMarket().'
        );

        $this->assertTrue(
            method_exists(SuperAdminCompanyController::class, 'marketshowJobDetails'),
            'SuperAdminCompanyController must implement marketshowJobDetails().'
        );

        $this->assertTrue(
            method_exists(SuperAdminCompanyController::class, 'jobApproval'),
            'SuperAdminCompanyController must implement jobApproval().'
        );

        $this->assertTrue(
            method_exists(SuperAdminCompanyController::class, 'marketautoApproval'),
            'SuperAdminCompanyController must implement marketautoApproval().'
        );
    }

    /**
     * Test Command Center blade contains top KPI deck, filter tabs, bulk toolbar, and drawer.
     */
    public function test_command_center_blade_contains_god_level_architecture(): void
    {
        $bladePath = resource_path('views/super-admin/jobs/index.blade.php');
        $this->assertFileExists($bladePath);

        $content = file_get_contents($bladePath);

        // Header & KPI deck
        $this->assertStringContainsString('Marketplace Command Center', $content);
        $this->assertStringContainsString('chkAutoApproval', $content);
        $this->assertStringContainsString('Total Market Jobs', $content);
        $this->assertStringContainsString('Pending Review', $content);
        $this->assertStringContainsString('Live Approved', $content);
        $this->assertStringContainsString('Bench Sharing', $content);
        $this->assertStringContainsString('Vendor Limit', $content);

        // Filter tabs
        $this->assertStringContainsString("route('superadmin.company.job-public-market', 'pending')", $content);
        $this->assertStringContainsString("route('superadmin.company.job-public-market', 'approve')", $content);
        $this->assertStringContainsString("route('superadmin.company.job-public-market', 'bench')", $content);
        $this->assertStringContainsString("route('superadmin.company.job-public-market', 'hold')", $content);
        $this->assertStringContainsString("route('superadmin.company.job-public-market', 'reject')", $content);
        $this->assertStringContainsString("route('superadmin.company.job-public-market', 'all')", $content);

        // Bulk actions & 3-panel drawer
        $this->assertStringContainsString('bulkActionsToolbar', $content);
        $this->assertStringContainsString('kt_activitiesMarketplaceJob', $content);
        $this->assertStringContainsString('marketplace_job_cockpit_body', $content);
        $this->assertStringContainsString('marketplaceRejectModal', $content);
    }

    /**
     * Test 3-Panel Moderation Cockpit Drawer blade contracts.
     */
    public function test_moderation_cockpit_blade_contains_3_panels(): void
    {
        $cockpitPath = resource_path('views/super-admin/jobs/showdetail.blade.php');
        $this->assertFileExists($cockpitPath);

        $content = file_get_contents($cockpitPath);

        // Panel 1: Tenancy & Architecture
        $this->assertStringContainsString('Tenant & Recruiter Profile', $content);
        $this->assertStringContainsString('Job Specifications', $content);

        // Panel 2: Anti-Circumvention Inspector & JD
        $this->assertStringContainsString('Anti-Circumvention', $content);
        $this->assertStringContainsString('Job Description Content', $content);
        $this->assertStringContainsString('Partner Agency Claims & Proposals', $content);

        // Panel 3: Moderation Engine & Decision Matrix
        $this->assertStringContainsString('Moderation Risk Engine', $content);
        $this->assertStringContainsString('Calculated Risk Index', $content);
        $this->assertStringContainsString('Approve Outsource & Bench', $content);
        $this->assertStringContainsString('Approve Bench Sharing Only', $content);
        $this->assertStringContainsString('btn-cockpit-reject', $content);
        $this->assertStringContainsString('Moderation History', $content);
    }

    /**
     * Test Sidebar Navigation contains updated Marketplace menu links.
     */
    public function test_sidebar_contains_command_center_and_market_links(): void
    {
        $sidebarPath = resource_path('views/sections/superadmin-left-sidebar.blade.php');
        $this->assertFileExists($sidebarPath);

        $content = file_get_contents($sidebarPath);
        $this->assertStringContainsString('Command Center', $content);
        $this->assertStringContainsString('Pending Review', $content);
        $this->assertStringContainsString('Live Approved Jobs', $content);
        $this->assertStringContainsString('Bench Sharing Jobs', $content);
    }

    /**
     * Test automated risk evaluation engine flags circumvention.
     */
    public function test_risk_engine_accurately_detects_circumvention_for_superadmin(): void
    {
        $moderationService = app(MarketplaceModerationService::class);

        // Clean JD with sufficient length (>400 chars)
        $cleanResult = $moderationService->evaluateRisk([
            'title' => 'Senior Laravel Backend Engineer',
            'job_description' => 'We are seeking an experienced Senior Laravel Developer to join our core engineering platform team. The candidate will design scalable APIs, optimize Eloquent queries, architect microservices, implement asynchronous queue workers, and maintain high standards of test coverage using PHPUnit. Ideal candidates should possess in-depth knowledge of database indexing, caching strategies with Redis, and containerized deployment workflows.',
            'job_requirement' => 'Minimum 5+ years of production experience in PHP and Laravel frameworks. Strong background with MySQL performance tuning and relational database schema design. Familiarity with CI/CD pipelines, Docker containers, and cloud infrastructure on AWS or GCP.',
            'min_salary' => 1500000,
            'max_salary' => 2500000,
            'company_id' => 10,
        ]);
        $this->assertLessThan(0.20, $cleanResult['risk_score']);
        $this->assertFalse($cleanResult['hard_blocked']);
        $this->assertEmpty($cleanResult['flags']);

        // Circumvention JD with phone and email
        $dirtyResult = $moderationService->evaluateRisk([
            'title' => 'Urgent React Dev',
            'job_description' => 'Contact recruiter directly at hr@external-firm.com or call 9876543210 immediately.',
            'job_requirement' => 'React and Redux.',
            'min_salary' => 500000,
            'max_salary' => 1000000,
            'company_id' => 10,
        ]);
        $this->assertGreaterThanOrEqual(0.35, $dirtyResult['risk_score']);
        $this->assertNotEmpty($dirtyResult['flags']);
    }
}
