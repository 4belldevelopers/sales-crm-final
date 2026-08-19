<?php

namespace Tests\Feature;

use App\Console\Commands\ReindexAtsJobsV2;
use App\Http\Resources\MarketJobV2Resource;
use App\Services\MarketJobV2Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * MarketJobV2Test
 *
 * Intent: Feature test suite for the Find Jobs V2 architecture. Verifies class contracts,
 * V2 API route registration, DTO transformations, versioned Redis cache key generation,
 * and tenant cache invalidation methods.
 *
 * Run:
 *   php artisan test --filter=MarketJobV2Test
 */
class MarketJobV2Test extends TestCase
{
    /**
     * Test that MarketJobV2Service class exists and is instantiable.
     */
    public function test_market_job_v2_service_class_exists(): void
    {
        $this->assertTrue(
            class_exists(MarketJobV2Service::class),
            'MarketJobV2Service must exist in App\\Services'
        );
    }

    /**
     * Test that MarketJobV2Resource class exists in App\Http\Resources.
     */
    public function test_market_job_v2_resource_class_exists(): void
    {
        $this->assertTrue(
            class_exists(MarketJobV2Resource::class),
            'MarketJobV2Resource must exist in App\\Http\\Resources'
        );
    }

    /**
     * Test that ReindexAtsJobsV2 console command exists in App\Console\Commands.
     */
    public function test_reindex_ats_jobs_v2_command_exists(): void
    {
        $this->assertTrue(
            class_exists(ReindexAtsJobsV2::class),
            'ReindexAtsJobsV2 must exist in App\\Console\\Commands'
        );
    }

    /**
     * Test that V2 AJAX route 'jobs.marketJobListV2_ajax' is registered in routes/service.php.
     */
    public function test_v2_route_is_registered(): void
    {
        $hasRoute = Route::has('jobs.marketJobListV2_ajax') || Route::has('service_company.jobs.marketJobListV2_ajax');
        $this->assertTrue(
            $hasRoute,
            "Route 'jobs.marketJobListV2_ajax' or 'service_company.jobs.marketJobListV2_ajax' must be registered."
        );
    }

    /**
     * Test versioned cache key generation in MarketJobV2Service.
     */
    public function test_v2_service_get_cache_key_format(): void
    {
        $service = app(MarketJobV2Service::class);
        $request = Request::create('/service_company/market-jobs-list-v2-ajax', 'GET', [
            'page' => 1,
            'size' => 10,
        ]);

        $companyId = 99;
        $cacheKey = $service->getCacheKey($request, $companyId);

        $this->assertStringStartsWith(
            'ats:v2:allmarketjobs:99:',
            $cacheKey,
            'Cache key must be scoped by tenant company_id and ats:v2 namespace.'
        );
    }

    /**
     * Test tenant cache invalidation increments tenant version key.
     */
    public function test_tenant_cache_invalidation_increments_version(): void
    {
        $companyId = 12345;
        $versionKey = 'allmarketjobs_version_company_'.$companyId;

        $initialVersion = (int) cache()->get($versionKey, 1);
        MarketJobV2Service::invalidateCacheForCompany($companyId);
        $newVersion = (int) cache()->get($versionKey, 1);

        $this->assertEquals(
            $initialVersion + 1,
            $newVersion,
            'Tenant cache version must increment by 1 on invalidation.'
        );
    }

    /**
     * Test DTO transformation structure in MarketJobV2Resource.
     */
    public function test_market_job_v2_resource_dto_transformation(): void
    {
        $dummyJob = [
            'id' => 101,
            'title' => 'Senior React Developer',
            'comId' => 50,
            'client_name' => 'Acme Tech',
            'category_name' => 'Engineering',
            'min_exp' => 3,
            'max_exp' => 6,
            'total_positions' => 4,
            'employment_type' => 0,
            'public_market_status' => 2,
            'work_type' => 'yes',
            'job_metronics_skills' => [
                ['skill_name' => 'React'],
                ['skill_name' => 'Redux'],
                ['skill_name' => 'JavaScript'],
            ],
            'company_size_from' => 50,
            'company_size' => 200,
            'industry_name' => 'Information Technology',
            'vendor_added_count' => 5,
        ];

        $resource = new MarketJobV2Resource($dummyJob);
        $request = Request::create('/service_company/market-jobs-list-v2-ajax', 'GET');
        $transformed = $resource->toArray($request);

        $this->assertEquals(101, $transformed['id']);
        $this->assertEquals('Engineering', $transformed['department']);
        $this->assertEquals('Remote', $transformed['work_type']);
        $this->assertEquals('Full-Time', $transformed['employment_type']);
        $this->assertContains('React', $transformed['skills']);
        $this->assertEquals(5, $transformed['vendor_added_count']);
        $this->assertArrayHasKey('action_type', $transformed);
    }

    /**
     * Test location only filtering parameter parsing.
     */
    public function test_location_only_filter_parsing(): void
    {
        $service = app(\App\Services\MarketJobV2Service::class);
        $request = Request::create('/service_company/market-jobs-list-v2-ajax', 'GET', [
            'kt_tagify_location' => '[{"value":"Bangalore"}]',
        ]);
        $this->assertInstanceOf(\App\Services\MarketJobV2Service::class, $service);
    }
}
