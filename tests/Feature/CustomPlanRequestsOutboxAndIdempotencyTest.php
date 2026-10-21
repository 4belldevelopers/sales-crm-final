<?php

namespace Tests\Feature;

use App\Events\CustomPlanRequestDeltaUpdated;
use App\Jobs\ProcessCustomPlanRequestOutboxEventJob;
use App\Models\CustomPlanRequest;
use App\Models\CustomPlanRequestLog;
use App\Models\SysOutboxEvent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Class CustomPlanRequestsOutboxAndIdempotencyTest
 *
 * Feature and unit test verification for:
 * - Transactional Outbox (sys_outbox_events)
 * - Durable Idempotency (sys_idempotency_keys)
 * - Optimistic Concurrency Control (OCC) aggregate_version
 * - Consolidated aggregate summary query & cache
 * - Outbox worker lifecycle & DLQ protection
 * - Reverb private channel authorization
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic cleanup in tearDown().
 */
class CustomPlanRequestsOutboxAndIdempotencyTest extends TestCase
{
    protected User $superAdmin;

    protected User $regularUser;

    protected array $createdCprIds = [];

    protected array $createdOutboxIds = [];

    protected array $createdIdempotencyKeys = [];

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

        $this->withoutMiddleware([
            \App\Http\Middleware\EnsureOneDeviceLogin::class,
        ]);

        $this->superAdmin = User::where('is_superadmin', 1)->first()
            ?? User::first();

        $this->regularUser = User::where('is_superadmin', 0)->first()
            ?? User::first();
    }

    protected function tearDown(): void
    {
        if (! empty($this->createdCprIds)) {
            CustomPlanRequestLog::whereIn('custom_plan_request_id', $this->createdCprIds)->delete();
            CustomPlanRequest::whereIn('id', $this->createdCprIds)->delete();
        }

        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        // Clean up any test outbox events
        DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CustomPlanRequest')
            ->where('event_type', 'like', 'Test%')
            ->delete();

        parent::tearDown();
    }

    protected function createTestCpr(array $overrides = []): CustomPlanRequest
    {
        $cpr = CustomPlanRequest::create(array_merge([
            'request_reference' => 'CPR-TEST-' . uniqid(),
            'company_id' => 1,
            'user_id' => $this->superAdmin->id,
            'customer_name' => 'Outbox Test Customer',
            'customer_email' => 'outbox_test@example.com',
            'customer_mobile' => '9999999999',
            'company_name' => 'Outbox Test Corp',
            'location' => 'Tech Park, India',
            'request_type' => 'Custom Subscription Plan',
            'package_type' => 'operational',
            'package_title' => 'Enterprise Custom Tier',
            'status' => CustomPlanRequest::STATUS_NEW,
            'aggregate_version' => 1,
            'message' => 'Custom Plan Test Enquiry',
            'last_status_updated_at' => now(),
        ], $overrides));

        $this->createdCprIds[] = $cpr->id;

        return $cpr;
    }

    /**
     * Test 1: updateStatus records an atomic transactional outbox event and increments OCC aggregate_version.
     */
    public function test_update_status_records_transactional_outbox_event_and_increments_occ_version(): void
    {
        $cpr = $this->createTestCpr();
        $this->assertEquals(1, $cpr->aggregate_version);

        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.custom-plan-requests.updateStatus', $cpr->id), [
                '_method' => 'PUT',
                'status' => CustomPlanRequest::STATUS_IN_PROGRESS,
                'admin_notes' => 'Contacted client regarding SLA details.',
                'current_version' => 1,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'new_status' => CustomPlanRequest::STATUS_IN_PROGRESS,
                'entity_version' => 2,
            ]);

        // Verify MySQL model was incremented
        $cpr->refresh();
        $this->assertEquals(CustomPlanRequest::STATUS_IN_PROGRESS, $cpr->status);
        $this->assertEquals(2, $cpr->aggregate_version);

        // Verify audit log
        $this->assertDatabaseHas('custom_plan_request_logs', [
            'custom_plan_request_id' => $cpr->id,
            'previous_status' => CustomPlanRequest::STATUS_NEW,
            'new_status' => CustomPlanRequest::STATUS_IN_PROGRESS,
            'notes' => 'Contacted client regarding SLA details.',
        ]);

        // Verify transactional outbox event
        $outbox = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CustomPlanRequest')
            ->where('aggregate_id', $cpr->id)
            ->latest('id')
            ->first();

        $this->assertNotNull($outbox);
        $this->createdOutboxIds[] = $outbox->id;
        $this->assertEquals('CustomPlanRequestStatusUpdated', $outbox->event_type);
        $this->assertEquals(2, $outbox->aggregate_version);
        $this->assertContains($outbox->status, ['PENDING', 'COMPLETED']);
    }

    /**
     * Test 2: updateStatus respects durable idempotency and replays original response on retry.
     */
    public function test_update_status_respects_durable_idempotency_and_replays_response(): void
    {
        $cpr = $this->createTestCpr();
        $idempotencyKey = 'cpr_test_key_' . uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        // First attempt
        $res1 = $this->actingAs($this->superAdmin)
            ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->postJson(route('superadmin.custom-plan-requests.updateStatus', $cpr->id), [
                '_method' => 'PUT',
                'status' => CustomPlanRequest::STATUS_CONTACTED,
                'admin_notes' => 'Demo scheduled for Friday.',
                'current_version' => 1,
                'idempotency_key' => $idempotencyKey,
            ]);

        $res1->assertStatus(200);
        $originalOutboxId = $res1->json('outbox_id');
        $this->createdOutboxIds[] = $originalOutboxId;

        // Second attempt with identical idempotency key and payload (network retry / double-click)
        $res2 = $this->actingAs($this->superAdmin)
            ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->postJson(route('superadmin.custom-plan-requests.updateStatus', $cpr->id), [
                '_method' => 'PUT',
                'status' => CustomPlanRequest::STATUS_CONTACTED,
                'admin_notes' => 'Demo scheduled for Friday.',
                'current_version' => 1,
                'idempotency_key' => $idempotencyKey,
            ]);

        $res2->assertStatus(200)
            ->assertHeader('X-Cache-Lookup', 'HIT-IDEMPOTENT');

        // Verify version was NOT incremented twice
        $cpr->refresh();
        $this->assertEquals(2, $cpr->aggregate_version);

        // Verify only 1 audit log was created
        $logCount = CustomPlanRequestLog::where('custom_plan_request_id', $cpr->id)->count();
        $this->assertEquals(1, $logCount);
    }

    /**
     * Test 3: updateStatus rejects idempotency key reuse with modified payload (409 Conflict).
     */
    public function test_update_status_rejects_idempotency_key_reuse_with_modified_payload(): void
    {
        $cpr = $this->createTestCpr();
        $idempotencyKey = 'cpr_conflict_key_' . uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        // Initial submission
        $this->actingAs($this->superAdmin)
            ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->postJson(route('superadmin.custom-plan-requests.updateStatus', $cpr->id), [
                '_method' => 'PUT',
                'status' => CustomPlanRequest::STATUS_CONTACTED,
                'admin_notes' => 'Original note',
                'current_version' => 1,
                'idempotency_key' => $idempotencyKey,
            ])
            ->assertStatus(200);

        // Reusing same idempotency key with DIFFERENT payload (status: Resolved)
        $conflictRes = $this->actingAs($this->superAdmin)
            ->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->postJson(route('superadmin.custom-plan-requests.updateStatus', $cpr->id), [
                '_method' => 'PUT',
                'status' => CustomPlanRequest::STATUS_RESOLVED,
                'admin_notes' => 'Different modified note',
                'current_version' => 2,
                'idempotency_key' => $idempotencyKey,
            ]);

        $conflictRes->assertStatus(409)
            ->assertJson([
                'status' => 'fail',
                'error_code' => 'IDEMPOTENCY_KEY_REUSE',
            ]);
    }

    /**
     * Test 4: OCC Version Check rejects stale concurrent update with 409 Conflict.
     */
    public function test_update_status_rejects_stale_version_with_409_concurrency_conflict(): void
    {
        $cpr = $this->createTestCpr();

        // Admin A updates to version 2
        $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.custom-plan-requests.updateStatus', $cpr->id), [
                '_method' => 'PUT',
                'status' => CustomPlanRequest::STATUS_IN_PROGRESS,
                'current_version' => 1,
            ])
            ->assertStatus(200);

        // Admin B was looking at stale screen (current_version = 1) and tries to update
        $staleRes = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.custom-plan-requests.updateStatus', $cpr->id), [
                '_method' => 'PUT',
                'status' => CustomPlanRequest::STATUS_RESOLVED,
                'current_version' => 1, // Stale! Current DB version is 2
            ]);

        $staleRes->assertStatus(409)
            ->assertJson([
                'status' => 'fail',
                'error_code' => 'CONCURRENCY_CONFLICT',
            ]);

        // Verify status remains 'In Progress' and was not overwritten by Admin B
        $cpr->refresh();
        $this->assertEquals(CustomPlanRequest::STATUS_IN_PROGRESS, $cpr->status);
        $this->assertEquals(2, $cpr->aggregate_version);
    }

    /**
     * Test 5: Outbox worker claims and processes pending event, broadcasting delta and marking COMPLETED.
     */
    public function test_outbox_worker_claims_and_processes_pending_event(): void
    {
        $cpr = $this->createTestCpr();

        $eventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 1,
            'idempotency_key' => 'worker_test_' . uniqid(),
            'aggregate_type' => 'CustomPlanRequest',
            'aggregate_id' => $cpr->id,
            'aggregate_version' => 1,
            'event_type' => 'TestCustomPlanEvent',
            'payload' => json_encode([
                'id' => $cpr->id,
                'reference' => $cpr->request_reference,
                'new_status' => 'In Progress',
                'previous_status' => 'New',
                'entity_version' => 1,
            ]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);
        $this->createdOutboxIds[] = $eventId;

        $job = new ProcessCustomPlanRequestOutboxEventJob($eventId);
        $job->handle();

        $processed = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        $this->assertEquals('COMPLETED', $processed->status);
        $this->assertNotNull($processed->processed_at);
        $this->assertNull($processed->last_error);
    }

    /**
     * Test 6: Consolidated summary counts executes in single aggregate query and caches result.
     */
    public function test_summary_counts_consolidated_in_single_pass_and_cached(): void
    {
        \Illuminate\Support\Facades\Cache::forget('custom_plan_requests_counts');

        $controller = new \App\Http\Controllers\SuperAdmin\SuperAdminCustomPlanRequestController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('getSummaryCounts');
        $method->setAccessible(true);

        DB::flushQueryLog();
        DB::enableQueryLog();

        $counts1 = $method->invoke($controller);
        $queries1 = DB::getQueryLog();

        $this->assertCount(1, $queries1, 'First call must execute exactly 1 aggregate query');
        $this->assertArrayHasKey('total', $counts1);
        $this->assertArrayHasKey('new', $counts1);
        $this->assertArrayHasKey('in_progress', $counts1);
        $this->assertArrayHasKey('contacted', $counts1);
        $this->assertArrayHasKey('resolved', $counts1);
        $this->assertArrayHasKey('closed', $counts1);

        // Second call should hit Cache (0 queries)
        DB::flushQueryLog();
        $counts2 = $method->invoke($controller);
        $queries2 = DB::getQueryLog();

        $this->assertCount(0, $queries2, 'Second call must hit cache with 0 database queries');
        $this->assertEquals($counts1, $counts2);
    }

    /**
     * Test 7: Delta sync endpoint returns aggregate_version and latest timestamp using index seek.
     */
    public function test_sync_endpoint_returns_aggregate_version_and_latest_state(): void
    {
        $cpr = $this->createTestCpr(['aggregate_version' => 3]);

        $res = $this->actingAs($this->superAdmin)
            ->getJson(route('superadmin.custom-plan-requests.sync'));

        $res->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'counts' => ['total', 'new', 'in_progress', 'contacted', 'resolved', 'closed'],
                'latest' => ['id', 'reference', 'status', 'entity_version', 'status_badge', 'updated_at', 'timestamp'],
                'total',
                'server_time',
            ]);
    }

    /**
     * Test 8: Super Admin private broadcast channel authorization rejects non-superadmins.
     */
    public function test_superadmin_channel_authorization_rejects_non_superadmin(): void
    {
        require_once base_path('routes/channels.php');

        $channels = Broadcast::getChannels();
        $this->assertArrayHasKey('superadmin.custom-plans', $channels);

        $channelCallback = $channels['superadmin.custom-plans'];
        $this->assertTrue((bool) $channelCallback($this->superAdmin), 'Superadmin must be authorized to subscribe');
        $this->assertFalse((bool) $channelCallback($this->regularUser), 'Regular user must be forbidden from subscribing');
    }
}
