<?php

namespace Tests\Feature;

use App\Helper\GeneralMethod;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController;
use App\Jobs\ProcessMarketJobOutboxEventJob;
use App\Services\MarketJobV2Service;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * AgencyMarketplacePublishTest
 *
 * Intent: Comprehensive test suite for GOD-LEVEL Publish / Unpublish Agency Marketplace actions.
 * Verifies route registration, HTTP method support, multi-tenant cache invalidation,
 * transactional outbox processing, and frontend UI contracts.
 *
 * Run:
 *   php artisan test --filter=AgencyMarketplacePublishTest
 */
class AgencyMarketplacePublishTest extends TestCase
{
    /**
     * Test that auction_send_job_request route is registered and accepts both GET and POST.
     */
    public function test_auction_send_job_request_route_registered_and_supports_post(): void
    {
        $hasRoute = Route::has('service_company.jobs.auction_send_job_request')
            || Route::has('jobs.auction_send_job_request');

        $this->assertTrue($hasRoute, "Route 'jobs.auction_send_job_request' must be registered.");

        $route = Route::getRoutes()->getByName('service_company.jobs.auction_send_job_request')
            ?? Route::getRoutes()->getByName('jobs.auction_send_job_request');

        $this->assertNotNull($route);
        $methods = $route->methods();
        $this->assertContains('GET', $methods, 'Route must support GET for backward compatibility.');
        $this->assertContains('POST', $methods, 'Route must support POST for secure mutations.');
    }

    /**
     * Test that ServiceCompanyJobsController has auction_send_job_request method.
     */
    public function test_controller_has_auction_send_job_request_method(): void
    {
        $this->assertTrue(
            method_exists(ServiceCompanyJobsController::class, 'auction_send_job_request'),
            'ServiceCompanyJobsController must contain auction_send_job_request method.'
        );
    }

    /**
     * Test multi-tenant cache invalidation for both user and company scopes.
     */
    public function test_multi_tenant_atomic_cache_invalidation(): void
    {
        $userId = 7788;
        $companyId = 9911;

        $userVersionKey = 'allsendinvitationtoagency_v5_version_user_'.$userId;
        $companyVersionKey = 'allsendinvitationtoagency_v5_version_company_'.$companyId;

        cache()->put($userVersionKey, 5, 3600);
        cache()->put($companyVersionKey, 10, 3600);

        $res = GeneralMethod::invalidateSpartnerlistingCache($userId, $companyId);
        $this->assertTrue($res);

        // GeneralMethod::cacheClean increments the integer version
        $newUserVer = (int) cache()->get($userVersionKey, 1);
        $newCompVer = (int) cache()->get($companyVersionKey, 1);

        $this->assertGreaterThan(5, $newUserVer, 'User version must be incremented on cache invalidation.');
        $this->assertGreaterThan(10, $newCompVer, 'Tenant company version must be incremented on cache invalidation.');
    }

    /**
     * Test MarketJobV2Service global and company cache invalidation methods.
     */
    public function test_market_job_v2_cache_invalidation_methods(): void
    {
        $companyId = 8844;
        $versionKey = 'allmarketjobs_version_company_'.$companyId;
        $globalKey = 'allmarketjobs_global_version';

        $initialComp = (int) cache()->get($versionKey, 1);
        $initialGlobal = (int) cache()->get($globalKey, 1);

        MarketJobV2Service::invalidateCacheForCompany($companyId);
        MarketJobV2Service::invalidateGlobalCache();

        $this->assertEquals($initialComp + 1, (int) cache()->get($versionKey, 1));
        $this->assertEquals($initialGlobal + 1, (int) cache()->get($globalKey, 1));
    }

    /**
     * Test ProcessMarketJobOutboxEventJob class contracts and queue specification.
     */
    public function test_process_market_job_outbox_event_job_contracts(): void
    {
        $this->assertTrue(
            class_exists(ProcessMarketJobOutboxEventJob::class),
            'ProcessMarketJobOutboxEventJob must exist in App\\Jobs'
        );

        $jobInstance = new ProcessMarketJobOutboxEventJob(specificEventId: 123);
        $this->assertEquals('market-outbox', $jobInstance->queue);
        $this->assertEquals(3, $jobInstance->tries);
        $this->assertEquals(10, $jobInstance->backoff);
    }

    /**
     * Test that spartnerlisting.blade.php contains loading feedback, mutex lock, and POST CSRF.
     */
    public function test_blade_contains_optimized_publish_unpublish_contracts(): void
    {
        $bladePath = resource_path('views/service_company/jobs/spartnerlisting.blade.php');
        $this->assertFileExists($bladePath);

        $content = file_get_contents($bladePath);

        // Verify mutually exclusive state boxes and buttons exist
        $this->assertStringContainsString('id="marketplace_unpublished_box"', $content);
        $this->assertStringContainsString('id="marketplace_live_box"', $content);
        $this->assertStringContainsString('id="sp_list_one"', $content);
        $this->assertStringContainsString('id="sp_list_two"', $content);

        // Verify mutex and loading indicators are present
        $this->assertStringContainsString('market-action-running', $content);
        $this->assertStringContainsString('spinner-border', $content);
        $this->assertStringContainsString('X-CSRF-TOKEN', $content);
        $this->assertStringContainsString('X-Idempotency-Key', $content);
        $this->assertStringContainsString("type: 'POST'", $content);
    }
}
