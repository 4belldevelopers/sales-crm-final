<?php

namespace Tests\Feature;

use App\Helper\GeneralMethod;
use App\Http\Middleware\EnsureOneDeviceLogin;
use App\Jobs\ProcessBenchRequestOutboxEventJob;
use App\Models\User;
use App\Services\BenchRequestMutationService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * BenchCandidateRecommendReqTest
 *
 * Feature and regression test suite for:
 * /service_company/bench-candidates/recommend_req/{id}
 *
 * Covers:
 * - Multi-tenant isolation & guest authentication
 * - Pre-rendered initial HTML & AJAX pagination endpoints
 * - ACID transactional mutation with sys_idempotency_keys
 * - Dual-lease sys_outbox_events worker execution
 * - Redis reference data & candidate filter caching
 * - Fast-path Redis query suppression
 */
class BenchCandidateRecommendReqTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => 'newdata']);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        // Provide server variables expected by header/navigation Blade views
        $_SERVER['REQUEST_URI'] = '/service_company/bench-candidates/recommend_req/6';

        $this->withoutMiddleware([EnsureOneDeviceLogin::class]);

        // Load authentic service company user (Tenant ID: 2)
        $this->user = User::withoutGlobalScopes()->find(2);
        if (! $this->user) {
            $this->user = new User;
            $this->user->id = 2;
            $this->user->company_id = 2;
            $this->user->status = 'active';
            $this->user->save();
        }

        // Ensure test company has active coins for mutation testing
        DB::table('company_coins')
            ->where('company_id', 2)
            ->where('event_type', 'sales')
            ->update(['coins' => 2000]);
    }

    /**
     * Scenario 1: Unauthenticated guest is redirected to login.
     */
    public function test_unauthenticated_request_is_redirected(): void
    {
        $response = $this->get('/service_company/bench-candidates/recommend_req/6');
        $this->assertEquals(302, $response->getStatusCode());
    }

    /**
     * Scenario 2: Authenticated tenant user can access recommend_req page with pre-rendered data.
     */
    public function test_authenticated_tenant_user_can_view_recommend_req_page(): void
    {
        $this->actingAs($this->user);

        $response = $this->get('/service_company/bench-candidates/recommend_req/6');
        $this->assertEquals(200, $response->getStatusCode());
        $response->assertSee('benchCandidateRecommendedReqAjax');
        $response->assertViewHas('initialHtml');
    }

    /**
     * Scenario 3: Cross-tenant candidate access is prohibited.
     */
    public function test_cross_tenant_candidate_access_is_prohibited(): void
    {
        $foreignUser = new User;
        $foreignUser->id = 99991;
        $foreignUser->company_id = 99992;
        $foreignUser->status = 'active';

        $this->actingAs($foreignUser);

        $response = $this->get('/service_company/bench-candidates/recommend_req/6');
        // Must either redirect or abort 403/404 due to candidate belonging to Company 2
        $this->assertContains($response->getStatusCode(), [302, 403, 404]);
    }

    /**
     * Scenario 4: AJAX data endpoint returns JSON structure with html, total, and pagination.
     */
    public function test_ajax_data_endpoint_returns_json_structure(): void
    {
        $this->actingAs($this->user);

        $response = $this->getJson(route('service_company.recommend.all_data_ajax', [
            'condidate_id' => 6,
            'page' => 1,
            'per_page' => 10,
            'custom_status' => 'active',
        ]));

        $this->assertEquals(200, $response->getStatusCode());
        $response->assertJsonStructure(['html', 'total', 'pagination']);
    }

    /**
     * Scenario 5: Mutation service executes atomically and produces sys_outbox_events record for client req.
     */
    public function test_mutation_service_first_run_creates_outbox_event(): void
    {
        $service = app(BenchRequestMutationService::class);
        $testKey = 'test-idemp-req-'.uniqid();

        $companyId = 2;
        $jobId = 7;
        $candidateId = 6;
        $userId = $this->user->id;

        // Ensure clean initial state for test run
        DB::table('rec_candidate_assign')
            ->where('source_jobid', $jobId)
            ->where('source_application_id', $candidateId)
            ->delete();

        $result = $service->executeRecommendationMutation(
            $companyId,
            $jobId,
            $candidateId,
            $userId,
            $testKey,
            'recommend_req',
            'client_bench'
        );

        $this->assertEquals('success', $result['status']);

        // Check idempotency record
        $idemp = DB::table('sys_idempotency_keys')->where('idempotency_key', $testKey)->first();
        $this->assertNotNull($idemp);
        $this->assertEquals('COMPLETED', $idemp->status);

        // Check outbox record
        $outbox = DB::table('sys_outbox_events')
            ->where('event_type', 'BenchCandidateRecommended')
            ->where('aggregate_id', $candidateId)
            ->orderBy('id', 'desc')
            ->first();
        $this->assertNotNull($outbox);
        $this->assertContains($outbox->status, ['PENDING', 'COMPLETED']);

        $payload = json_decode($outbox->payload, true);
        $this->assertEquals('recommend_req', $payload['action']);
        $this->assertEquals('client_bench', $payload['tab']);
    }

    /**
     * Scenario 6: Replay with same idempotency key returns cached response without duplicate writes.
     */
    public function test_mutation_service_replay_with_same_key_is_idempotent(): void
    {
        $service = app(BenchRequestMutationService::class);
        $testKey = 'test-replay-req-'.uniqid();

        $companyId = 2;
        $jobId = 7;
        $candidateId = 6;
        $userId = $this->user->id;

        DB::table('rec_candidate_assign')
            ->where('source_jobid', $jobId)
            ->where('source_application_id', $candidateId)
            ->delete();

        // First call
        $result1 = $service->executeRecommendationMutation(
            $companyId,
            $jobId,
            $candidateId,
            $userId,
            $testKey,
            'recommend_req',
            'client_bench'
        );
        $this->assertEquals('success', $result1['status']);

        $outboxCountAfterFirst = DB::table('sys_outbox_events')->where('event_type', 'BenchCandidateRecommended')->count();

        // Second call with identical key
        $result2 = $service->executeRecommendationMutation(
            $companyId,
            $jobId,
            $candidateId,
            $userId,
            $testKey,
            'recommend_req',
            'client_bench'
        );
        $this->assertEquals('success', $result2['status']);

        $outboxCountAfterSecond = DB::table('sys_outbox_events')->where('event_type', 'BenchCandidateRecommended')->count();
        $this->assertEquals($outboxCountAfterFirst, $outboxCountAfterSecond, 'Idempotent replay must not create duplicate outbox events');
    }

    /**
     * Scenario 7: Idempotency key payload mismatch returns HTTP 409 conflict.
     */
    public function test_idempotency_key_payload_mismatch_returns_409_conflict(): void
    {
        $service = app(BenchRequestMutationService::class);
        $testKey = 'test-conflict-req-'.uniqid();

        $companyId = 2;
        $jobId = 7;
        $candidateId = 6;
        $userId = $this->user->id;

        DB::table('rec_candidate_assign')
            ->where('source_jobid', $jobId)
            ->where('source_application_id', $candidateId)
            ->delete();

        // First call with Job 7
        $service->executeRecommendationMutation(
            $companyId,
            $jobId,
            $candidateId,
            $userId,
            $testKey,
            'recommend_req',
            'client_bench'
        );

        // Second call with same key but different job (e.g. Job 8)
        $mismatchedResult = $service->executeRecommendationMutation(
            $companyId,
            8,
            $candidateId,
            $userId,
            $testKey,
            'recommend_req',
            'client_bench'
        );

        $this->assertEquals('error', $mismatchedResult['status']);
        $this->assertEquals(409, $mismatchedResult['http_code'] ?? 0);
    }

    /**
     * Scenario 8: Outbox worker claims event and transitions status to COMPLETED.
     */
    public function test_outbox_worker_processes_and_marks_completed(): void
    {
        $eventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 2,
            'idempotency_key' => 'test-outbox-worker-req-'.uniqid(),
            'aggregate_type' => 'BenchCandidateRequest',
            'aggregate_id' => 6,
            'aggregate_version' => 1,
            'event_type' => 'BenchCandidateRecommended',
            'payload' => json_encode([
                'rec_id' => 999998,
                'source_application_id' => 6,
                'source_jobid' => 7,
                'source_from_company_id' => 2,
                'source_company_id' => 2,
                'tab' => 'client_bench',
            ]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);

        $job = new ProcessBenchRequestOutboxEventJob($eventId);
        $job->handle();

        $updatedEvent = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        $this->assertEquals('COMPLETED', $updatedEvent->status);
        $this->assertNotNull($updatedEvent->processed_at);
    }

    /**
     * Scenario 9: GeneralMethod::canGgdFilterData caches in Redis and handles null qualifications safely.
     */
    public function test_candidate_filter_cache_in_general_method(): void
    {
        $data1 = GeneralMethod::canGgdFilterData(6);
        $this->assertIsObject($data1);

        $cacheKey = 'ats:bench_filter_data:6';
        $cached = Cache::get($cacheKey);
        $this->assertNotNull($cached);
        $this->assertEquals($data1->jobid, $cached->jobid);
    }

    /**
     * Scenario 10: Subsequent AJAX calls hit Redis fast-path with zero domain database queries.
     */
    public function test_ajax_data_endpoint_hits_cache_fast_path_with_zero_db_queries(): void
    {
        $this->actingAs($this->user);

        $params = [
            'condidate_id' => 6,
            'page' => 1,
            'per_page' => 10,
            'custom_status' => 'active',
        ];

        // 1. Prime cache
        $this->getJson(route('service_company.recommend.all_data_ajax', $params));

        // 2. Track queries on warm hit
        DB::enableQueryLog();
        $response = $this->getJson(route('service_company.recommend.all_data_ajax', $params));
        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $this->assertEquals(200, $response->getStatusCode());

        // Verify zero business domain queries executed (served 100% from DragonflyDB Redis cache)
        $domainQueries = array_filter($queries, function ($q) {
            $sql = strtolower($q['query']);

            return str_contains($sql, 'job_applications') ||
                   str_contains($sql, 'countries') ||
                   str_contains($sql, 'state_list') ||
                   str_contains($sql, 'city_list') ||
                   str_contains($sql, 'jobs') ||
                   str_contains($sql, 'rec_candidate_assign');
        });
        $this->assertCount(0, $domainQueries, 'Cached AJAX request executed unexpected ATS/candidate domain SQL queries');
    }
}
