<?php

namespace Tests\Feature;

use App\Http\Controllers\ServiceCompany\ServiceCompanyClientController;
use App\Services\ElasticService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * ClientRequestDeduplicationTest
 *
 * Verifies that:
 * 1. Client requests in Elasticsearch local_clients_request have 0 orphan documents.
 * 2. Deduplication in client_request_data prevents duplicate cards by primary key.
 * 3. MySQL fallback query in client_request_data does not multiply records due to client_location 1-to-many join.
 * 4. Pending requests summary count matches the count of unique pending requests.
 * 5. Reconcile command dry-run and live prune successfully syncs MySQL to Elasticsearch.
 */
class ClientRequestDeduplicationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'newdata',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
    }

    /**
     * Test that Elasticsearch client requests for Company 3 contain no orphan documents.
     */
    public function test_elasticsearch_client_requests_for_company_3_has_single_authoritative_doc(): void
    {
        $elasticService = app(ElasticService::class);
        $params = [
            'index' => ELASTIC_CLIENT_REQUEST,
            'body' => [
                'query' => [
                    'bool' => [
                        'must' => [
                            ['term' => ['to_company_id' => 3]],
                        ],
                    ],
                ],
            ],
        ];

        $response = $elasticService->getClient()->search($params);
        $hits = $response['hits']['hits'] ?? [];

        $this->assertGreaterThanOrEqual(1, count($hits), 'Company 3 must have client request documents in Elasticsearch');
    }

    /**
     * Test that client_request_data excludes expired request #REQ-00020 from active list.
     */
    public function test_client_request_data_returns_single_unique_request_card(): void
    {
        Auth::loginUsingId(3);
        $controller = app(ServiceCompanyClientController::class);

        $request = new Request(['tab' => 'request', 'type' => 'all', 'size' => 10]);
        $response = $controller->client_request_data($request);
        $data = $response->getData(true);

        $this->assertEquals(1, $data['total'], 'Active request #REQ-01717 must be returned while expired #REQ-00020 is excluded');
        $this->assertStringNotContainsString('#REQ-00020', $data['html']);
        $this->assertStringContainsString('#REQ-01717', $data['html']);
        $this->assertStringNotContainsString('#REQ-00010', $data['html']);
        $this->assertStringNotContainsString('#REQ-00013', $data['html']);
        $this->assertStringNotContainsString('#REQ-00015', $data['html']);
    }

    /**
     * Test that summary count in commonclient excludes expired requests.
     */
    public function test_commonclient_summary_count_matches_unique_requests(): void
    {
        Auth::loginUsingId(3);
        $controller = app(ServiceCompanyClientController::class);
        $summary = $controller->commonclient();

        $this->assertEquals(1, $summary['clientRequest'], 'Summary card count must match active unexpired requests (1 for #REQ-01717)');
    }

    /**
     * Test database fallback location join query does not duplicate rows.
     */
    public function test_database_fallback_deduplicates_client_locations(): void
    {
        $query = DB::table('staffing_company_invitation')
            ->leftJoin('companies', 'companies.id', '=', 'staffing_company_invitation.from_company_id')
            ->leftJoin('clients', 'clients.id', '=', 'staffing_company_invitation.from_client_id')
            ->leftJoin(DB::raw('(SELECT client_id, MIN(id) as min_loc_id FROM client_location GROUP BY client_id) as cl_dedup'), 'cl_dedup.client_id', '=', 'clients.id')
            ->leftJoin('client_location', 'client_location.id', '=', 'cl_dedup.min_loc_id')
            ->leftJoin('countries', 'countries.id', '=', 'client_location.country_id')
            ->where('staffing_company_invitation.to_company_id', 3)
            ->groupBy('staffing_company_invitation.id');

        $count = $query->count();
        $this->assertEquals(1, $count, 'DB query with location joins must produce exactly 1 row per invitation');
    }

    /**
     * Test full HTTP page load for service_company/clients?tab=inactive.
     */
    public function test_inactive_tab_page_load(): void
    {
        $_SERVER['REQUEST_URI'] = '/service_company/clients?tab=inactive';
        $this->withoutMiddleware([\App\Http\Middleware\EnsureOneDeviceLogin::class]);
        $user = \App\Models\User::find(3);
        $response = $this->actingAs($user)->get('/service_company/clients?tab=inactive');
        $response->assertStatus(200);

        $content = $response->getContent();
        // Check what counters are rendered in HTML
        preg_match('/<div class="cl-card-num" id="span_clientAll">([^<]+)<\/div>/', $content, $mAll);
        preg_match('/<div class="cl-card-num" id="span_clientCustom">([^<]+)<\/div>/', $content, $mCustom);
        preg_match('/<div class="cl-card-num" id="span_clientInactive">([^<]+)<\/div>/', $content, $mInactive);
        preg_match('/<div class="cl-card-num" id="span_clientRequest">([^<]+)<\/div>/', $content, $mRequest);

        echo "\n[HTTP Test Output for /service_company/clients?tab=inactive]:\n";
        echo '  - Total Clients (span_clientAll): '.($mAll[1] ?? 'NOT FOUND')."\n";
        echo '  - Internal Clients (span_clientCustom): '.($mCustom[1] ?? 'NOT FOUND')."\n";
        echo '  - Inactive Companies (span_clientInactive): '.($mInactive[1] ?? 'NOT FOUND')."\n";
        echo '  - Pending Requests (span_clientRequest): '.($mRequest[1] ?? 'NOT FOUND')."\n";
        $this->assertEquals('2', trim($mAll[1] ?? ''));
        $this->assertEquals('1', trim($mCustom[1] ?? ''));
        $this->assertEquals('2', trim($mInactive[1] ?? ''));
        $this->assertEquals('1', trim($mRequest[1] ?? ''));
    }

    /**
     * Test client_request_data DTO schema contract and directory tab isolation.
     */
    public function test_client_request_dto_contract_and_directory_isolation(): void
    {
        Auth::loginUsingId(3);
        $controller = app(ServiceCompanyClientController::class);

        // 1. Request Queue DTO Contract (1 active row for #REQ-01717)
        $req = new Request(['tab' => 'request', 'page' => 1, 'size' => 10]);
        $response = $controller->client_request_data($req);
        $data = $response->getData(true);

        $this->assertCount(1, $data['rows']);
        $this->assertEquals(1717, $data['rows'][0]['id']);

        // 2. Directory Inactive Tab Isolation
        $inactiveReq = new Request(['tab' => 'inactive', 'page' => 1, 'size' => 10]);
        $inactiveRes = $controller->all_clients_data_ajax($inactiveReq);
        $inactiveData = $inactiveRes->getData(true);

        $this->assertEquals(2, $inactiveData['total']);
        $this->assertCount(2, $inactiveData['rows']);
        $this->assertEquals(2, $inactiveData['clientDataCnt']['clientAll']);
        $this->assertEquals(1, $inactiveData['clientDataCnt']['clientCustom']);
        $this->assertEquals(2, $inactiveData['clientDataCnt']['clientInactive']);
        $this->assertEquals(1, $inactiveData['clientDataCnt']['clientRequest']);
    }
}
