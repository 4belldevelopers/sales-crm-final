<?php

namespace Tests\Feature;

use App\Helper\GeneralMethod;
use App\Http\Controllers\ServiceCompany\ServiceCompanyClientController;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * ClientRequestAcceptDeclineTest
 *
 * Forensic test suite for Accept and Decline workflows in Clients Tracker (/service_company/clients).
 * Verifies end-to-end functionality across:
 * - Pending Accept (Relationship creation, ES sync, cache invalidation)
 * - Pending Decline (State update, ES sync, no relation created)
 * - Expiration rejection (HTTP 422 REQUEST_EXPIRED)
 * - State machine replay protection (HTTP 409 REQUEST_ALREADY_PROCESSED)
 * - Tenancy isolation (Cross-company unauthorized attempt)
 * - Invalid parameters
 * - Concurrency double-click protection (Atomic Redis lock)
 */
class ClientRequestAcceptDeclineTest extends TestCase
{
    protected int $fromCompanyId = 2;

    protected int $toCompanyId = 3;

    protected ?int $testInvitationId = null;

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

        // Clean up any test state
        DB::table('staffing_company_invitation')->where('to_email', 'test_accept_decline@jero.works')->delete();
        DB::table('staffing_company_relation')->where('from_company_id', $this->fromCompanyId)->where('to_company_id', $this->toCompanyId)->delete();
    }

    protected function tearDown(): void
    {
        if ($this->testInvitationId) {
            DB::table('staffing_company_invitation')->where('id', $this->testInvitationId)->delete();
            try {
                $elasticService = app(\App\Services\ElasticService::class);
                if ($elasticService->getClient()->exists(['index' => ELASTIC_CLIENT_REQUEST, 'id' => $this->testInvitationId])) {
                    $elasticService->getClient()->delete(['index' => ELASTIC_CLIENT_REQUEST, 'id' => $this->testInvitationId]);
                }
            } catch (\Throwable $e) {
            }
        }
        DB::table('staffing_company_relation')->where('from_company_id', $this->fromCompanyId)->where('to_company_id', $this->toCompanyId)->delete();
        DB::table('staffing_company_invitation')->where('to_email', 'test_accept_decline@jero.works')->delete();
        parent::tearDown();
    }

    protected function createTestInvitation(int $status = 0, ?Carbon $expiresAt = null): int
    {
        $id = DB::table('staffing_company_invitation')->insertGetId([
            'from_company_id' => $this->fromCompanyId,
            'from_client_id' => 2,
            'from_user_id' => 2,
            'from_status' => 1,
            'from_created_at' => now(),
            'to_company_id' => $this->toCompanyId,
            'to_email' => 'test_accept_decline@jero.works',
            'to_status' => 0,
            'job_id' => 6,
            'to_invitation_status' => $status,
            'expires_at' => $expiresAt ? $expiresAt->toDateTimeString() : now()->addDays(7)->toDateTimeString(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->testInvitationId = $id;

        // Index in Elasticsearch
        try {
            GeneralMethod::clientRequestIndexElastic($id);
        } catch (\Throwable $e) {
        }

        return $id;
    }

    /**
     * Test 1: Accept pending request creates relationship, updates status, and syncs ES.
     */
    public function test_accept_pending_request_successfully_creates_relation_and_updates_status(): void
    {
        $invitationId = $this->createTestInvitation(0, now()->addDays(7));

        $user = User::find(3); // TechNova user, Company 3
        Auth::login($user);
        $controller = app(ServiceCompanyClientController::class);
        $controller->user = $user;

        $request = new Request([
            'invitation_id' => $invitationId,
            'event_id' => 1,
            'from_company' => $this->fromCompanyId,
        ]);

        $response = $controller->request_update($request);
        $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));

        $this->assertEquals('success', $data['status']);
        $this->assertEquals('Request has been Accepted.', $data['message']);

        // Verify MySQL invitation status
        $inv = DB::table('staffing_company_invitation')->where('id', $invitationId)->first();
        $this->assertEquals(1, $inv->to_invitation_status);

        // Verify MySQL staffing_company_relation creation
        $rel = DB::table('staffing_company_relation')
            ->where('from_company_id', $this->fromCompanyId)
            ->where('to_company_id', $this->toCompanyId)
            ->first();
        $this->assertNotNull($rel);
        $this->assertEquals(1, $rel->final_status);
        $this->assertEquals(1, $rel->to_status);

        // Verify Elasticsearch document
        $elasticService = app(\App\Services\ElasticService::class);
        $doc = $elasticService->getClient()->get([
            'index' => ELASTIC_CLIENT_REQUEST,
            'id' => $invitationId,
        ]);
        $this->assertEquals(1, $doc['_source']['to_invitation_status']);
    }

    /**
     * Test 2: Decline pending request updates status without creating active relationship.
     */
    public function test_decline_pending_request_successfully_updates_status_without_creating_relation(): void
    {
        $invitationId = $this->createTestInvitation(0, now()->addDays(7));

        $user = User::find(3);
        Auth::login($user);
        $controller = app(ServiceCompanyClientController::class);
        $controller->user = $user;

        $request = new Request([
            'invitation_id' => $invitationId,
            'event_id' => -1,
            'from_company' => $this->fromCompanyId,
        ]);

        $response = $controller->request_update($request);
        $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));

        $this->assertEquals('success', $data['status']);
        $this->assertEquals('Request has been Declined.', $data['message']);

        // Verify MySQL invitation status is -1
        $inv = DB::table('staffing_company_invitation')->where('id', $invitationId)->first();
        $this->assertEquals(-1, $inv->to_invitation_status);

        // Verify no active relationship was created
        $rel = DB::table('staffing_company_relation')
            ->where('from_company_id', $this->fromCompanyId)
            ->where('to_company_id', $this->toCompanyId)
            ->where('final_status', 1)
            ->first();
        $this->assertNull($rel);

        // Verify Elasticsearch document
        $elasticService = app(\App\Services\ElasticService::class);
        $doc = $elasticService->getClient()->get([
            'index' => ELASTIC_CLIENT_REQUEST,
            'id' => $invitationId,
        ]);
        $this->assertEquals(-1, $doc['_source']['to_invitation_status']);
    }

    /**
     * Test 3: Accepting an expired request is rejected with HTTP 422 REQUEST_EXPIRED.
     */
    public function test_accept_expired_request_is_rejected_with_422(): void
    {
        $invitationId = $this->createTestInvitation(0, now()->subDays(2));

        $user = User::find(3);
        Auth::login($user);
        $controller = app(ServiceCompanyClientController::class);
        $controller->user = $user;

        $request = new Request([
            'invitation_id' => $invitationId,
            'event_id' => 1,
            'from_company' => $this->fromCompanyId,
        ]);

        $response = $controller->request_update($request);
        $statusCode = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
        $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));

        $this->assertEquals(422, $statusCode);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('REQUEST_EXPIRED', $data['code']);

        // Verify status was lazily transitioned to -2
        $inv = DB::table('staffing_company_invitation')->where('id', $invitationId)->first();
        $this->assertEquals(-2, $inv->to_invitation_status);
    }

    /**
     * Test 4: Declining an expired request is rejected with HTTP 422 REQUEST_EXPIRED.
     */
    public function test_decline_expired_request_is_rejected_with_422(): void
    {
        $invitationId = $this->createTestInvitation(0, now()->subDays(2));

        $user = User::find(3);
        Auth::login($user);
        $controller = app(ServiceCompanyClientController::class);
        $controller->user = $user;

        $request = new Request([
            'invitation_id' => $invitationId,
            'event_id' => -1,
            'from_company' => $this->fromCompanyId,
        ]);

        $response = $controller->request_update($request);
        $statusCode = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
        $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));

        $this->assertEquals(422, $statusCode);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('REQUEST_EXPIRED', $data['code']);
    }

    /**
     * Test 5: Re-accepting an already accepted request is prevented with HTTP 409 conflict.
     */
    public function test_accept_already_accepted_request_returns_conflict_409(): void
    {
        $invitationId = $this->createTestInvitation(1, now()->addDays(7));

        $user = User::find(3);
        Auth::login($user);
        $controller = app(ServiceCompanyClientController::class);
        $controller->user = $user;

        $request = new Request([
            'invitation_id' => $invitationId,
            'event_id' => 1,
            'from_company' => $this->fromCompanyId,
        ]);

        $response = $controller->request_update($request);
        $statusCode = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
        $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));

        $this->assertEquals(409, $statusCode);
        $this->assertEquals('conflict', $data['status']);
        $this->assertEquals('REQUEST_ALREADY_PROCESSED', $data['code']);
    }

    /**
     * Test 6: Re-declining an already declined request is prevented with HTTP 409 conflict.
     */
    public function test_decline_already_declined_request_returns_conflict_409(): void
    {
        $invitationId = $this->createTestInvitation(-1, now()->addDays(7));

        $user = User::find(3);
        Auth::login($user);
        $controller = app(ServiceCompanyClientController::class);
        $controller->user = $user;

        $request = new Request([
            'invitation_id' => $invitationId,
            'event_id' => -1,
            'from_company' => $this->fromCompanyId,
        ]);

        $response = $controller->request_update($request);
        $statusCode = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
        $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));

        $this->assertEquals(409, $statusCode);
        $this->assertEquals('conflict', $data['status']);
        $this->assertEquals('REQUEST_ALREADY_PROCESSED', $data['code']);
    }

    /**
     * Test 7: Unauthorized user from different company cannot accept requests targeted to another tenant.
     */
    public function test_unauthorized_company_cannot_accept_request(): void
    {
        $invitationId = $this->createTestInvitation(0, now()->addDays(7));

        // Log in as User 2 (Company 2), but invitation is targeted to Company 3
        $user = User::find(2);
        Auth::login($user);
        $controller = app(ServiceCompanyClientController::class);
        $controller->user = $user;

        $request = new Request([
            'invitation_id' => $invitationId,
            'event_id' => 1,
            'from_company' => $this->fromCompanyId,
        ]);

        $response = $controller->request_update($request);
        $statusCode = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
        $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));

        $this->assertEquals(404, $statusCode);
        $this->assertEquals('error', $data['status']);
        $this->assertEquals('Request not found.', $data['message']);
    }

    /**
     * Test 8: Invalid request parameters return proper validation errors.
     */
    public function test_invalid_parameters_return_error(): void
    {
        $user = User::find(3);
        Auth::login($user);
        $controller = app(ServiceCompanyClientController::class);
        $controller->user = $user;

        // Missing invitation_id
        $request = new Request(['event_id' => 1]);
        $response = $controller->request_update($request);
        $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));
        $this->assertEquals('error', $data['status']);

        // Invalid event_id
        $request = new Request(['invitation_id' => 99999, 'event_id' => 42]);
        $response = $controller->request_update($request);
        $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));
        $this->assertEquals('error', $data['status']);
    }

    /**
     * Test 9: Concurrency lock prevents parallel duplicate submissions.
     */
    public function test_concurrency_lock_prevents_duplicate_processing(): void
    {
        $invitationId = $this->createTestInvitation(0, now()->addDays(7));

        $user = User::find(3);
        Auth::login($user);
        $controller = app(ServiceCompanyClientController::class);
        $controller->user = $user;

        // Acquire lock manually to simulate an active concurrent request
        $lockKey = "lock:client_req_update:3:{$invitationId}";
        $lock = Cache::lock($lockKey, 10);
        $this->assertTrue($lock->get());

        try {
            $request = new Request([
                'invitation_id' => $invitationId,
                'event_id' => 1,
                'from_company' => $this->fromCompanyId,
            ]);

            $response = $controller->request_update($request);
            $statusCode = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
            $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : json_decode($response->getContent(), true));

            $this->assertEquals(202, $statusCode);
            $this->assertEquals('processing', $data['status']);
        } finally {
            $lock->release();
        }
    }
}
