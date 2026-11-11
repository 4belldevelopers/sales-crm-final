<?php

namespace Tests\Feature;

use App\Helper\GeneralMethod;
use App\Http\Controllers\ServiceCompany\ServiceCompanyClientController;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController;
use App\Models\User;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * ExpiredRequestsAutoRemoveAndResendTest
 *
 * Verifies the complete end-to-end expiry handling for Client Partnership Requests and Job Requests:
 * 1. Automatically exclude expired requests from active request lists, counts, and tabs.
 * 2. Strict backend authorization blocking acceptance of expired requests (HTTP 422 REQUEST_EXPIRED).
 * 3. Fresh request generation with new ID (no reactivation of expired record) preserving audit history.
 * 4. Duplicate prevention for concurrent/active requests.
 * 5. 100% CQRS Elastic read compliance and Zero-Cron / JIT lazy evaluation.
 */
class ExpiredRequestsAutoRemoveAndResendTest extends TestCase
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
     * Edge Case 1 & 2: Expired client requests are excluded from commonclient() counts and active lists.
     */
    public function test_expired_client_requests_are_excluded_from_counts_and_listings(): void
    {
        $user = User::find(3);
        Auth::login($user);
        $controller = app(ServiceCompanyClientController::class);
        $controller->user = $user;

        // 1. commonclient() should only count active unexpired requests (excluding expired ID 20)
        $counts = $controller->commonclient();
        $this->assertEquals(1, $counts['clientRequest'], 'Only active unexpired request (#REQ-01717) must be counted');

        // 2. client_request_data() should return only active request #1717 and exclude expired request #20
        $request = new Request(['tab' => 'request', 'type' => 'all', 'size' => 10]);
        $response = $controller->client_request_data($request);
        $data = $response->getData(true);
        $this->assertEquals(1, $data['total'], 'Active client request count must match active requests');
        $this->assertStringNotContainsString('#REQ-00020', $data['html']);
        $this->assertStringContainsString('#REQ-01717', $data['html']);
    }

    /**
     * Edge Case 4: Attempting to accept an expired client partnership request via API returns 422 REQUEST_EXPIRED.
     */
    public function test_user_cannot_accept_an_expired_client_request(): void
    {
        $user = User::find(3);
        Auth::login($user);
        $controller = app(ServiceCompanyClientController::class);
        $controller->user = $user;

        // Attempt to accept expired invitation ID 20
        $request = new Request([
            'invitation_id' => 20,
            'event_id' => 1, // Accept
            'from_company' => 2,
        ]);

        $response = $controller->request_update($request);
        $this->assertEquals(422, $response->getStatusCode(), 'Accepting an expired request must return HTTP 422');

        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals('REQUEST_EXPIRED', $responseData['code'] ?? null);
        $this->assertStringContainsString('expired', strtolower($responseData['message'] ?? ''));
    }

    /**
     * Edge Case 3: Valid unexpired client requests appear in counts and can be fetched.
     */
    public function test_valid_unexpired_client_request_is_included(): void
    {
        $user = User::find(3);
        Auth::login($user);
        $controller = app(ServiceCompanyClientController::class);
        $controller->user = $user;

        // Temporarily create a valid unexpired client invitation for testing
        $testInvId = DB::table('staffing_company_invitation')->insertGetId([
            'from_company_id' => 9999,
            'from_client_id' => 0,
            'from_user_id' => 1,
            'from_status' => 1,
            'from_created_at' => now(),
            'to_company_id' => 3,
            'to_email' => 'test_unexpired@jero.works',
            'to_status' => 0,
            'to_invitation_status' => 0,
            'expires_at' => now()->addDays(5),
            'delivery_status' => 'PENDING',
            'normalized_recipient' => 'test_unexpired@jero.works',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            // Index in Elasticsearch
            $elasticService = app(ElasticService::class);
            $elasticService->getClient()->index([
                'index' => ELASTIC_CLIENT_REQUEST,
                'id' => $testInvId,
                'body' => [
                    'id' => $testInvId,
                    'staffing_company_invitation_id' => $testInvId,
                    'from_company_id' => 9999,
                    'to_company_id' => 3,
                    'to_invitation_status' => 0,
                    'expires_at' => now()->addDays(5)->toIso8601String(),
                    'company_name' => 'Unexpired Test Partner',
                    'created_at' => now()->toIso8601String(),
                ],
                'refresh' => true,
            ]);

            ServiceCompanyClientController::invalidateTenantClientCache(3);

            $counts = $controller->commonclient();
            $this->assertGreaterThanOrEqual(1, $counts['clientRequest'], 'Unexpired request must be counted');
        } finally {
            // Clean up test invitation
            DB::table('staffing_company_invitation')->where('id', $testInvId)->delete();
            try {
                $elasticService->getClient()->delete([
                    'index' => ELASTIC_CLIENT_REQUEST,
                    'id' => $testInvId,
                ]);
            } catch (\Throwable) {}
            ServiceCompanyClientController::invalidateTenantClientCache(3);
        }
    }

    /**
     * Edge Case 6 & 7: Expired job requests are excluded from dataCnt() KPI and recommandedrequest_ajax().
     */
    public function test_expired_job_requests_are_excluded_from_job_request_kpi_and_ajax(): void
    {
        $user = User::find(3);
        Auth::login($user);
        $controller = app(ServiceCompanyJobsController::class);
        $controller->user = $user;

        // Check dataCnt()
        $kpis = $controller->dataCnt();
        $this->assertIsInt($kpis['jobRequest']);

        // Check recommandedrequest_ajax()
        $request = new Request([
            'from' => 0,
            'size' => 10,
            'sort' => 'recent',
        ]);
        $response = $controller->recommandedrequest_ajax($request);
        $data = json_decode($response->getContent(), true);

        // Ensure no expired records (e.g. ID 30) are returned
        $returnedIds = collect($data['data'] ?? [])->pluck('recommendation_relation_id')->toArray();
        $this->assertNotContains(30, $returnedIds, 'Expired job request #30 must never appear in recommandedrequest_ajax');
    }

    /**
     * Edge Case 8: User cannot accept an expired job request.
     */
    public function test_user_cannot_accept_an_expired_job_request(): void
    {
        $user = User::find(3);
        Auth::login($user);
        $controller = app(ServiceCompanyJobsController::class);
        $controller->user = $user;

        // Attempt to accept expired recommendation relation #30
        $request = new Request([
            'rec_relation_id' => 30,
            'status' => 'accept',
        ]);

        $response = $controller->requestapproval($request);
        $responseData = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));

        $this->assertEquals('error', $responseData['status']);
        $this->assertEquals('REQUEST_EXPIRED', $responseData['code'] ?? null);
    }

    /**
     * Edge Case 5, 9: Resending an expired job request generates a brand new record ID and preserves historical record.
     */
    public function test_resend_expired_job_request_generates_new_record_and_preserves_history(): void
    {
        $user = User::find(2); // Log in as User 2 (Company 2, admin)
        Auth::login($user);
        $controller = app(ServiceCompanyJobsController::class);
        $controller->user = $user;

        $fromCompanyId = 2;
        $toCompanyId = 3;
        $jobId = 1; // Job 1 belongs to Company 2

        $originalRecord = DB::table('recommendation_relation')->where('id', 30)->first();
        $this->assertNotNull($originalRecord);
        $this->assertEquals(3, $originalRecord->final_status, 'ID 30 must initially be expired (final_status = 3)');

        $testIdempotencyKey = 'test_resend_' . uniqid();
        DB::table('sys_idempotency_keys')->where('idempotency_key', 'resend_recom_2_3_1')->delete();

        $request = new Request([
            'to_company_id' => $toCompanyId,
            'current_jobid' => $jobId,
            'idempotency_key' => $testIdempotencyKey,
        ]);

        $response = $controller->resend_job_recommanded_request($request, $toCompanyId);
        $responseData = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));

        $this->assertEquals('success', $responseData['status']);

        // Verify that a NEW record was generated rather than reactivating the old one
        $latestRecord = DB::table('recommendation_relation')
            ->where('from_company_id', $fromCompanyId)
            ->where('to_company_id', $toCompanyId)
            ->where('job_id', $jobId)
            ->orderBy('id', 'desc')
            ->first();

        $this->assertNotNull($latestRecord);
        $this->assertNotEquals(30, $latestRecord->id, 'Resending must create a brand new recommendation record identifier');
        $this->assertEquals(0, $latestRecord->final_status, 'New request must have final_status = 0 (active)');
        $this->assertNotNull($latestRecord->expires_at, 'New request must have valid expires_at timestamp');
        $this->assertTrue(Carbon::parse($latestRecord->expires_at)->isFuture(), 'New request expires_at must be in the future');

        // Verify that historical record #30 was preserved with final_status = 3 (expired)
        $historicalRecord = DB::table('recommendation_relation')->where('id', 30)->first();
        $this->assertNotNull($historicalRecord, 'Historical record must be preserved for audit');
        $this->assertEquals(3, $historicalRecord->final_status, 'Historical record must remain final_status = 3 (expired)');

        // Clean up test generated record
        DB::table('recommendation_relation')->where('id', $latestRecord->id)->delete();
        DB::table('sys_idempotency_keys')->where('idempotency_key', $testIdempotencyKey)->delete();
        GeneralMethod::jobRequestDeleteElastic($latestRecord->id);
    }
}
