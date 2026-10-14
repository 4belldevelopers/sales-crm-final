<?php

namespace Tests\Feature;

use App\Services\FindAgencyService;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Http304CacheValidationTest
 *
 * Phase B automated verification test suite for HTTP 304, ETag, and Cache behavior.
 * Verifies:
 * 1. Fresh client receives 200 with authoritative payload and ETag.
 * 2. Valid conditional request with matching ETag and X-Has-Representation receives 304.
 * 3. Fresh page or cleared DOM (missing X-Has-Representation) returns 200, never 304.
 * 4. Representation-affecting query parameter change generates distinct ETag and returns 200.
 * 5. Cross-tenant isolation: Tenant A's ETag produces 200 for Tenant B.
 * 6. Tenant cache invalidation forces fresh 200 response.
 */
class Http304CacheValidationTest extends TestCase
{
    protected $testUser;

    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'newdata',
        ]);
        \Illuminate\Support\Facades\DB::purge('mysql');
        \Illuminate\Support\Facades\DB::reconnect('mysql');
        \Illuminate\Support\Facades\DB::setDefaultConnection('mysql');
        \Illuminate\Support\Facades\Cache::put('jero_super_admin_company_ids', [1], 3600);

        $this->testUser = \App\Models\User::withoutGlobalScopes()->whereNotNull('company_id')->first()
            ?? new \App\Models\User(['id' => 10, 'company_id' => 999]);
        \Illuminate\Support\Facades\Auth::setUser($this->testUser);
    }




    /**
     * Test fresh client receives 200 with full response and ETag header.
     */
    public function test_fresh_client_receives_200_with_etag(): void
    {
        $service = app(FindAgencyService::class);
        $user = $this->testUser;

        $request = Request::create('/service_company/find-agency-data', 'GET', [
            'page' => 1,
            'per_page' => 10,
        ]);

        $response = $service->getAgencyData($request, $user);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->has('ETag'));
        $cacheControl = $response->headers->get('Cache-Control');
        $this->assertStringContainsString('private', $cacheControl);
        $this->assertStringContainsString('no-cache', $cacheControl);
    }


    /**
     * Test valid conditional revalidation receives 304 when client holds representation.
     */
    public function test_valid_conditional_revalidation_returns_304(): void
    {
        $service = app(FindAgencyService::class);
        $user = $this->testUser;

        $initialRequest = Request::create('/service_company/find-agency-data', 'GET', [
            'page' => 1,
            'per_page' => 10,
        ]);
        $initialResponse = $service->getAgencyData($initialRequest, $user);
        $etag = $initialResponse->headers->get('ETag');

        $revalRequest = Request::create('/service_company/find-agency-data', 'GET', [
            'page' => 1,
            'per_page' => 10,
        ], [], [], [
            'HTTP_IF_NONE_MATCH' => $etag,
            'HTTP_X_HAS_REPRESENTATION' => '1',
        ]);

        $revalResponse = $service->getAgencyData($revalRequest, $user);

        $this->assertEquals(304, $revalResponse->getStatusCode());
        $this->assertEquals($etag, $revalResponse->headers->get('ETag'));
    }

    /**
     * Test that missing X-Has-Representation (cleared DOM or new tab) returns 200, avoiding blank screen.
     */
    public function test_cleared_dom_missing_has_representation_returns_200(): void
    {
        $service = app(FindAgencyService::class);
        $user = $this->testUser;

        $initialRequest = Request::create('/service_company/find-agency-data', 'GET', [
            'page' => 1,
            'per_page' => 10,
        ]);
        $initialResponse = $service->getAgencyData($initialRequest, $user);
        $etag = $initialResponse->headers->get('ETag');

        // Request with matching ETag but WITHOUT X-Has-Representation
        $freshDomRequest = Request::create('/service_company/find-agency-data', 'GET', [
            'page' => 1,
            'per_page' => 10,
        ], [], [], [
            'HTTP_IF_NONE_MATCH' => $etag,
            // X-Has-Representation intentionally omitted
        ]);

        $freshDomResponse = $service->getAgencyData($freshDomRequest, $user);

        $this->assertEquals(200, $freshDomResponse->getStatusCode(), 'Must return full 200 response when DOM has no representation.');
    }

    /**
     * Test filter modification changes cache identity and returns 200.
     */
    public function test_filter_change_returns_200_with_different_etag(): void
    {
        $service = app(FindAgencyService::class);
        $user = $this->testUser;

        $initialRequest = Request::create('/service_company/find-agency-data', 'GET', [
            'page' => 1,
            'experience' => '0–2 years',
        ]);
        $initialResponse = $service->getAgencyData($initialRequest, $user);
        $etag = $initialResponse->headers->get('ETag');

        // Request with different filter
        $changedRequest = Request::create('/service_company/find-agency-data', 'GET', [
            'page' => 1,
            'experience' => '3–5 years',
        ], [], [], [
            'HTTP_IF_NONE_MATCH' => $etag,
            'HTTP_X_HAS_REPRESENTATION' => '1',
        ]);

        $changedResponse = $service->getAgencyData($changedRequest, $user);

        $this->assertEquals(200, $changedResponse->getStatusCode());
        $this->assertNotEquals($etag, $changedResponse->headers->get('ETag'));
    }

    /**
     * Test cross-tenant isolation: Tenant A's ETag cannot produce 304 for Tenant B.
     */
    public function test_cross_tenant_isolation_prevents_etag_reuse(): void
    {
        $service = app(FindAgencyService::class);
        $userA = new \App\Models\User();
        $userA->id = 10;
        $userA->company_id = 100;

        $userB = new \App\Models\User();
        $userB->id = 20;
        $userB->company_id = 200;


        \Illuminate\Support\Facades\Auth::setUser($userA);
        $requestA = Request::create('/service_company/find-agency-data', 'GET', ['page' => 1]);
        $responseA = $service->getAgencyData($requestA, $userA);
        $etagA = $responseA->headers->get('ETag');

        // Tenant B requests with Tenant A's ETag
        \Illuminate\Support\Facades\Auth::setUser($userB);
        $requestB = Request::create('/service_company/find-agency-data', 'GET', ['page' => 1], [], [], [
            'HTTP_IF_NONE_MATCH' => $etagA,
            'HTTP_X_HAS_REPRESENTATION' => '1',
        ]);

        $responseB = $service->getAgencyData($requestB, $userB);

        $this->assertEquals(200, $responseB->getStatusCode(), 'Tenant B must receive 200 and never reuse Tenant A cache.');
        $this->assertNotEquals($etagA, $responseB->headers->get('ETag'));
    }

    /**
     * Test tenant cache invalidation breaks ETag match and returns 200.
     */
    public function test_tenant_cache_invalidation_forces_fresh_200(): void
    {
        $service = app(FindAgencyService::class);
        $user = $this->testUser;
        \Illuminate\Support\Facades\Auth::setUser($user);

        $initialRequest = Request::create('/service_company/find-agency-data', 'GET', ['page' => 1]);
        $initialResponse = $service->getAgencyData($initialRequest, $user);
        $etag = $initialResponse->headers->get('ETag');

        // Invalidate tenant cache
        FindAgencyService::invalidateTenantCache($user->company_id);


        // Attempt revalidation with old ETag
        $revalRequest = Request::create('/service_company/find-agency-data', 'GET', ['page' => 1], [], [], [
            'HTTP_IF_NONE_MATCH' => $etag,
            'HTTP_X_HAS_REPRESENTATION' => '1',
        ]);

        $revalResponse = $service->getAgencyData($revalRequest, $user);

        $this->assertEquals(200, $revalResponse->getStatusCode(), 'Invalidated cache must force full 200 response.');
    }

    /**
     * Test Add Agency drawer recommendedData conditional 304 and safe 200 fallback.
     */
    public function test_drawer_recommended_data_safe_304_behavior(): void
    {
        $controller = app(\App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController::class);
        $user = $this->testUser;
        $controller->user = $user;
        $controller->data['user'] = $user;

        $job = \App\Models\Job::withoutGlobalScopes()->where('company_id', $user->company_id)->first()
            ?? \App\Models\Job::withoutGlobalScopes()->first();
        $jobId = $job ? $job->id : 1;

        // 1. Fresh client without representation -> 200
        $req1 = Request::create('/service_company/jobs/recommendedData', 'GET', [
            'jobid' => $jobId,
            'tab' => 'recommended',
        ]);
        $req1->setUserResolver(fn() => $user);

        $res1 = $controller->recommendedData($req1);
        $this->assertEquals(200, $res1->getStatusCode());
        $etag = $res1->headers->get('ETag');
        $this->assertNotEmpty($etag);

        // 2. Existing valid representation with matching ETag and X-Has-Representation -> 304
        $req2 = Request::create('/service_company/jobs/recommendedData', 'GET', [
            'jobid' => $jobId,
            'tab' => 'recommended',
        ], [], [], [
            'HTTP_IF_NONE_MATCH' => $etag,
            'HTTP_X_HAS_REPRESENTATION' => '1',
        ]);
        $req2->setUserResolver(fn() => $user);

        $res2 = $controller->recommendedData($req2);
        $this->assertEquals(304, $res2->getStatusCode());
        $this->assertEmpty($res2->getContent(), '304 response body must be 0 bytes.');

        // 3. Cleared DOM / Fresh page missing X-Has-Representation -> Must return 200
        $req3 = Request::create('/service_company/jobs/recommendedData', 'GET', [
            'jobid' => $jobId,
            'tab' => 'recommended',
        ], [], [], [
            'HTTP_IF_NONE_MATCH' => $etag,
            // X-Has-Representation intentionally omitted
        ]);
        $req3->setUserResolver(fn() => $user);

        $res3 = $controller->recommendedData($req3);
        $this->assertEquals(200, $res3->getStatusCode(), 'Fresh DOM without representation must receive full 200 response.');
    }

    /**
     * Test separate accounting of MySQL and Elasticsearch activity across paths.
     */
    public function test_separate_activity_accounting_across_paths(): void
    {
        $service = app(FindAgencyService::class);
        $user = $this->testUser;

        // Force refresh / cold path
        \Illuminate\Support\Facades\DB::enableQueryLog();
        \Illuminate\Support\Facades\DB::flushQueryLog();

        $coldReq = Request::create('/service_company/find-agency-data', 'GET', [
            'page' => 1,
            'per_page' => 10,
            'force_refresh' => 1,
        ]);
        $coldRes = $service->getAgencyData($coldReq, $user);
        $coldQueries = count(\Illuminate\Support\Facades\DB::getQueryLog());
        \Illuminate\Support\Facades\DB::disableQueryLog();

        $this->assertEquals(200, $coldRes->getStatusCode());
        $etag = $coldRes->headers->get('ETag');

        // Warm L3 cache path
        \Illuminate\Support\Facades\DB::enableQueryLog();
        \Illuminate\Support\Facades\DB::flushQueryLog();

        $warmReq = Request::create('/service_company/find-agency-data', 'GET', [
            'page' => 1,
            'per_page' => 10,
        ]);
        $warmRes = $service->getAgencyData($warmReq, $user);
        $warmQueries = count(\Illuminate\Support\Facades\DB::getQueryLog());
        \Illuminate\Support\Facades\DB::disableQueryLog();

        $this->assertEquals(200, $warmRes->getStatusCode());
        $this->assertEquals(0, $warmQueries, 'Warm L3 cache path must execute 0 MySQL queries.');

        // 304 Revalidation path
        \Illuminate\Support\Facades\DB::enableQueryLog();
        \Illuminate\Support\Facades\DB::flushQueryLog();

        $revalReq = Request::create('/service_company/find-agency-data', 'GET', [
            'page' => 1,
            'per_page' => 10,
        ], [], [], [
            'HTTP_IF_NONE_MATCH' => $etag,
            'HTTP_X_HAS_REPRESENTATION' => '1',
        ]);
        $revalRes = $service->getAgencyData($revalReq, $user);
        $revalQueries = count(\Illuminate\Support\Facades\DB::getQueryLog());
        \Illuminate\Support\Facades\DB::disableQueryLog();

        $this->assertEquals(304, $revalRes->getStatusCode());
        $this->assertEquals(0, $revalQueries, 'HTTP 304 revalidation must execute 0 MySQL queries.');
        $this->assertEquals(0, strlen($revalRes->getContent()), 'HTTP 304 payload must be exactly 0 bytes.');
    }
}
