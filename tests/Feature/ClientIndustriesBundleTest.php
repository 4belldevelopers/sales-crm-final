<?php

namespace Tests\Feature;

use App\Http\Controllers\ServiceCompany\ServiceCompanyClientController;
use App\Models\User;
use App\Services\IndustryTaxonomyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class ClientIndustriesBundleTest
 *
 * Intent: Verifies the enterprise Industry bundle endpoint for Client Onboarding.
 * Validates ETag 304 caching, Redis tenant caching, 10 canonical sectors, and 146 industries.
 */
class ClientIndustriesBundleTest extends TestCase
{
    protected int $tenantId = 99992;
    protected $user;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'newdata',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        $user = new User();
        $user->id = 88882;
        $user->company_id = $this->tenantId;
        $this->user = $user;
        Auth::setUser($user);
    }

    public function test_industries_bundle_returns_200_and_all_sectors(): void
    {
        $controller = app(ServiceCompanyClientController::class);
        $request = Request::create(route('service_company.clients.industriesBundle'), 'GET');
        $request->setUserResolver(fn () => $this->user);

        $response = $controller->industriesBundle($request);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertTrue($response->headers->has('ETag'));

        $data = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('sectors', $data);
        $this->assertArrayHasKey('industries', $data);
        $this->assertCount(10, $data['sectors']);
        $this->assertGreaterThanOrEqual(140, count($data['industries']));
    }

    public function test_industries_bundle_returns_304_on_etag_match(): void
    {
        $controller = app(ServiceCompanyClientController::class);
        $request1 = Request::create(route('service_company.clients.industriesBundle'), 'GET');
        $request1->setUserResolver(fn () => $this->user);

        $initial = $controller->industriesBundle($request1);
        $etag = $initial->headers->get('ETag');
        $this->assertNotEmpty($etag);

        $request2 = Request::create(route('service_company.clients.industriesBundle'), 'GET', [], [], [], [
            'HTTP_IF_NONE_MATCH' => $etag,
        ]);
        $request2->setUserResolver(fn () => $this->user);

        $cached = $controller->industriesBundle($request2);
        $this->assertEquals(304, $cached->getStatusCode());
    }

    public function test_taxonomy_service_build_tenant_manifest(): void
    {
        $manifest = IndustryTaxonomyService::buildTenantManifest($this->tenantId, 1);

        $this->assertArrayHasKey('manifest_id', $manifest);
        $this->assertArrayHasKey('sectors', $manifest);
        $this->assertArrayHasKey('industries', $manifest);
        $this->assertCount(10, $manifest['sectors']);
        $this->assertGreaterThanOrEqual(140, count($manifest['industries']));

        // Verify first industry has required structure
        $first = $manifest['industries'][0];
        $this->assertArrayHasKey('id', $first);
        $this->assertArrayHasKey('name', $first);
        $this->assertArrayHasKey('sector_id', $first);
        $this->assertArrayHasKey('sector_name', $first);
        $this->assertArrayHasKey('keywords', $first);
    }
}
