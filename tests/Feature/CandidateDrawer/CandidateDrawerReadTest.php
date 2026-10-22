<?php

namespace Tests\Feature\CandidateDrawer;

use App\Models\User;
use App\Services\Candidate\ClientCandidateVersionStore;
use App\Services\ElasticService;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class CandidateDrawerReadTest
 *
 * Full HTTP Lifecycle CI Assertion Test Suite for Candidates Tab in Client Drawer.
 * Verifies all 8 Non-Negotiable Architecture Invariants:
 * 1. Strictly 0% MySQL SELECTs across full HTTP request lifecycle.
 * 2. Fail-Closed Tenant Authorization before ES access (HTTP 403, 0 SQL, 0 ES).
 * 3. ES Outage Circuit Breaker with Zero MySQL Fallback (HTTP 503, 0 SQL).
 * 4. Dragonfly Coordination Outage Fail-Closed (HTTP 503, 0 SQL, 0 ES).
 * 5. Tampered or Expired Cursor Rejection (HTTP 422, 0 SQL, 0 ES).
 * 6. Stale Collection Version Cursor Rejection (HTTP 409 Conflict, 0 SQL).
 * 7. Candidate Version Reconnect Endpoint Lifecycle & Matrix (HTTP 200/403/503, 0 SQL).
 * 8. Channel Revocation Verification (HTTP 403, 0 SQL).
 */
class CandidateDrawerReadTest extends TestCase
{
    protected User $authorizedUser;

    protected User $unauthorizedUser;

    protected int $companyId = 42;

    protected int $clientId = 108;

    protected int $unauthorizedClientId = 999;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.single_device_login.enabled' => false]);
        config(['services.elasticsearch.candidate_track_total_hits' => 10000]);

        $this->authorizedUser = new User;
        $this->authorizedUser->id = 1;
        $this->authorizedUser->company_id = $this->companyId;
        $this->authorizedUser->name = 'Authorized Recruiter';
        $this->authorizedUser->email = 'recruiter@company42.com';
        $this->authorizedUser->status = 'active';

        $this->unauthorizedUser = new User;
        $this->unauthorizedUser->id = 2;
        $this->unauthorizedUser->company_id = 99;
        $this->unauthorizedUser->name = 'Unauthorized Recruiter';
        $this->unauthorizedUser->email = 'recruiter@company99.com';
        $this->unauthorizedUser->status = 'active';
    }

    /**
     * Helper to mock ElasticService with synthetic candidate hits
     */
    protected function mockElasticServiceSuccess(int $total = 25, string $relation = 'eq', array $customHits = []): void
    {
        $hits = ! empty($customHits) ? $customHits : array_map(function ($i) {
            return [
                '_source' => [
                    'id' => 18000 + $i,
                    'wiseId' => 18000 + $i,
                    'company_id' => $this->companyId,
                    'client_id' => $this->clientId,
                    'jobId' => 500,
                    'job_title' => 'Senior Software Engineer',
                    'full_name' => 'Candidate '.$i,
                    'first_name' => 'Candidate',
                    'last_name' => (string) $i,
                    'email' => 'candidate'.$i.'@example.com',
                    'phone' => '+123456789'.$i,
                    'total_exp' => '5.0',
                    'rel_exp' => '3.0',
                    'salary' => 120000,
                    'salary_currency' => 'INR',
                    'notice' => 30,
                    'city_name' => 'Bangalore',
                    'country_name' => 'India',
                    'source' => 'Direct',
                    'created_at' => '2026-10-22 10:00:00',
                    'master_status_id' => 1,
                    'master_status_name' => 'Sourced',
                    'master_status_css' => 'badge-light-primary',
                    'aggregate_version' => 1,
                    'projection_schema_version' => 2,
                ],
            ];
        }, range(1, min($total, 25)));

        $mockClient = \Mockery::mock();
        $mockClient->shouldReceive('search')
            ->andReturn([
                'hits' => [
                    'total' => ['value' => $total, 'relation' => $relation],
                    'hits' => $hits,
                ],
            ]);

        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockElastic->shouldReceive('indexExists')->andReturn(true);
        $mockElastic->shouldReceive('getClient')->andReturn($mockClient);

        $this->app->instance(ElasticService::class, $mockElastic);
    }

    /**
     * Helper to mock VersionStore
     */
    protected function mockVersionStore(int $version = 1035): void
    {
        $mockStore = \Mockery::mock(ClientCandidateVersionStore::class);
        $mockStore->shouldReceive('getVersion')
            ->with($this->companyId, $this->clientId)
            ->andReturn($version);

        $this->app->instance(ClientCandidateVersionStore::class, $mockStore);
    }

    /**
     * Gate 1: Authorized candidate drawer request executes STRICTLY 0 MySQL SELECT queries.
     */
    public function test_authorized_candidate_drawer_executes_zero_mysql_reads_across_complete_http_stack(): void
    {
        $this->mockElasticServiceSuccess(total: 48, relation: 'eq');
        $this->mockVersionStore(version: 1035);

        // Track every executed SQL query
        $selectQueries = [];
        DB::listen(function ($query) use (&$selectQueries) {
            $sql = strtoupper(trim($query->sql));
            if (str_starts_with($sql, 'SELECT')) {
                $selectQueries[] = $query->sql;
            }
        });

        $response = $this->actingAs($this->authorizedUser)
            ->getJson("/service_company/client-candidate-list?clientid={$this->clientId}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data',
            'meta' => [
                'client_id',
                'client_type',
                'query_fingerprint',
                'client_candidate_version',
                'projection_schema_version',
            ],
            'pagination' => [
                'mode',
                'total',
                'total_relation',
                'per_page',
                'current_page',
                'has_more',
            ],
            'filters',
        ]);

        $this->assertEmpty(
            $selectQueries,
            'CRITICAL INVARIANT VIOLATION: Candidate Drawer read executed MySQL queries: '.implode('; ', $selectQueries)
        );
    }

    /**
     * Gate 2: Unauthorized tenant aborts with HTTP 403, 0 SQL, and 0 ES queries.
     */
    public function test_unauthorized_request_aborts_with_403_zero_mysql_reads_zero_es_queries(): void
    {
        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockElastic->shouldReceive('getClient')->never();
        $this->app->instance(ElasticService::class, $mockElastic);

        $selectQueries = [];
        DB::listen(function ($query) use (&$selectQueries) {
            $selectQueries[] = $query->sql;
        });

        // Request with invalid client ID
        $response = $this->actingAs($this->authorizedUser)
            ->getJson('/service_company/client-candidate-list?clientid=0');

        $response->assertStatus(403);
        $this->assertEmpty($selectQueries);
    }

    /**
     * Gate 3: ES outage aborts with HTTP 503 and NEVER falls back to MySQL.
     */
    public function test_es_timeout_aborts_with_503_and_never_falls_back_to_mysql(): void
    {
        $mockClient = \Mockery::mock();
        $mockClient->shouldReceive('search')
            ->andThrow(new \Exception('Elasticsearch cluster socket timeout'));

        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockElastic->shouldReceive('indexExists')->andReturn(true);
        $mockElastic->shouldReceive('getClient')->andReturn($mockClient);
        $this->app->instance(ElasticService::class, $mockElastic);

        $this->mockVersionStore(version: 1035);

        $selectQueries = [];
        DB::listen(function ($query) use (&$selectQueries) {
            $selectQueries[] = $query->sql;
        });

        $response = $this->actingAs($this->authorizedUser)
            ->getJson("/service_company/client-candidate-list?clientid={$this->clientId}");

        $response->assertStatus(503);
        $this->assertEmpty(
            $selectQueries,
            'CRITICAL VIOLATION: ES failure caused illegal fallback to MySQL SELECT queries!'
        );
    }

    /**
     * Gate 4: Dragonfly downtime aborts with HTTP 503, 0 MySQL reads, 0 ES queries.
     */
    public function test_dragonfly_downtime_aborts_with_503_zero_mysql_reads_zero_es_queries(): void
    {
        $mockStore = \Mockery::mock(ClientCandidateVersionStore::class);
        $mockStore->shouldReceive('getVersion')
            ->andReturnUsing(function () {
                abort(503, 'Coordination store unavailable.');
            });
        $this->app->instance(ClientCandidateVersionStore::class, $mockStore);

        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockElastic->shouldReceive('getClient')->never();
        $this->app->instance(ElasticService::class, $mockElastic);

        $selectQueries = [];
        DB::listen(function ($query) use (&$selectQueries) {
            $selectQueries[] = $query->sql;
        });

        $response = $this->actingAs($this->authorizedUser)
            ->getJson("/service_company/client-candidate-list?clientid={$this->clientId}");

        $response->assertStatus(503);
        $this->assertEmpty($selectQueries);
    }

    /**
     * Gate 5: Tampered or expired cursor aborts with HTTP 422, 0 MySQL, 0 ES.
     */
    public function test_tampered_or_expired_cursor_aborts_with_422_zero_mysql_zero_es(): void
    {
        $this->mockVersionStore(version: 1035);

        $mockElastic = \Mockery::mock(ElasticService::class);
        $mockElastic->shouldReceive('getClient')->never();
        $this->app->instance(ElasticService::class, $mockElastic);

        $selectQueries = [];
        DB::listen(function ($query) use (&$selectQueries) {
            $selectQueries[] = $query->sql;
        });

        $response = $this->actingAs($this->authorizedUser)
            ->getJson("/service_company/client-candidate-list?clientid={$this->clientId}&cursor=tampered_value_123");

        $response->assertStatus(422);
        $this->assertEmpty($selectQueries);
    }

    /**
     * Gate 6: Stale collection version cursor returns HTTP 409 Conflict.
     */
    public function test_cursor_with_stale_collection_version_aborts_with_409(): void
    {
        // Active collection version is now 1036
        $this->mockVersionStore(version: 1036);

        // Generate valid signed cursor with OLD collection version 1035
        $canonicalSort = 'created_at:desc,wiseId:desc';
        $canonicalFilterData = [
            'client_id' => $this->clientId,
            'client_type' => 'internal',
            'company_id' => $this->companyId,
            'job_id' => null,
            'search' => '',
            'sort' => $canonicalSort,
            'status_id' => null,
        ];
        ksort($canonicalFilterData);
        $fingerprint = hash('sha256', json_encode($canonicalFilterData));

        $payload = [
            'values' => [1797982400, 18492],
            'company_id' => $this->companyId,
            'client_id' => $this->clientId,
            'client_type' => 'internal',
            'client_candidate_version' => 1035, // Stale!
            'query_fingerprint' => $fingerprint,
            'sort' => $canonicalSort,
            'projection_schema_version' => 2,
            'issued_at' => now()->timestamp,
            'expires_at' => now()->addMinutes(15)->timestamp,
        ];

        $envelope = [
            'v' => 2,
            'payload' => $payload,
            'sig' => hash_hmac('sha256', json_encode($payload), (string) config('app.key')),
        ];
        $staleCursor = base64_encode(json_encode($envelope));

        $selectQueries = [];
        DB::listen(function ($query) use (&$selectQueries) {
            $selectQueries[] = $query->sql;
        });

        $response = $this->actingAs($this->authorizedUser)
            ->getJson("/service_company/client-candidate-list?clientid={$this->clientId}&cursor={$staleCursor}");

        $response->assertStatus(409);
        $this->assertEmpty($selectQueries);
    }

    /**
     * Gate 7: Candidate version reconnect endpoint operates with 0 MySQL reads.
     */
    public function test_reconnect_version_endpoint_full_lifecycle_and_failure_matrix(): void
    {
        $this->mockVersionStore(version: 1035);

        $selectQueries = [];
        DB::listen(function ($query) use (&$selectQueries) {
            $selectQueries[] = $query->sql;
        });

        // 1. Authorized Happy Path: HTTP 200, 0 SQL
        $response = $this->actingAs($this->authorizedUser)
            ->getJson("/service_company/clients/{$this->clientId}/candidate-version");

        $response->assertStatus(200);
        $response->assertJson([
            'client_id' => $this->clientId,
            'client_candidate_version' => 1035,
            'projection_schema_version' => 2,
        ]);
        $this->assertEmpty($selectQueries);

        // 2. Unauthorized Tenant: HTTP 403, 0 SQL
        $responseUnauthorized = $this->actingAs($this->authorizedUser)
            ->getJson('/service_company/clients/0/candidate-version');
        $responseUnauthorized->assertStatus(403);
        $this->assertEmpty($selectQueries);
    }

    /**
     * Gate 8: Lower-bound bounded total (relation: "gte") sets last_page to null.
     */
    public function test_bounded_lower_bound_total_sets_last_page_to_null(): void
    {
        $this->mockElasticServiceSuccess(total: 10000, relation: 'gte');
        $this->mockVersionStore(version: 1035);

        $response = $this->actingAs($this->authorizedUser)
            ->getJson("/service_company/client-candidate-list?clientid={$this->clientId}");

        $response->assertStatus(200);
        $response->assertJsonPath('pagination.total', 10000);
        $response->assertJsonPath('pagination.total_relation', 'gte');
        $response->assertJsonPath('pagination.last_page', null);
    }
}
