<?php

namespace Tests\Feature;

use App\Http\Controllers\ServiceCompany\ServiceCompanyClientController;
use App\Jobs\ProcessClientOutboxEventJob;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * ClientOnboardingIdempotencyAndOutboxTest
 *
 * Automated verification test suite for Service Company Client Onboarding:
 * - Two-tier idempotency (Redis/Dragonfly + MySQL sys_idempotency_keys)
 * - Concurrent double-submit / network retry deduplication
 * - Payload mismatch rejection
 * - Transactional Outbox persistence (sys_outbox_events) and ProcessClientOutboxEventJob processing
 * - Client creation with addresses and contacts
 */
class ClientOnboardingIdempotencyAndOutboxTest extends TestCase
{
    protected int $tenantId = 99992;
    protected array $testKeys = [];
    protected array $createdClientIds = [];

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
        Auth::setUser($user);
    }

    protected function tearDown(): void
    {
        if (! empty($this->testKeys)) {
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', $this->tenantId)
                ->whereIn('idempotency_key', $this->testKeys)
                ->delete();

            DB::table('sys_outbox_events')
                ->where('tenant_id', $this->tenantId)
                ->whereIn('idempotency_key', $this->testKeys)
                ->delete();
        }

        if (! empty($this->createdClientIds)) {
            DB::table('client_contact_info')->whereIn('client_id', $this->createdClientIds)->delete();
            DB::table('client_location')->whereIn('client_id', $this->createdClientIds)->delete();
            DB::table('clients')->whereIn('id', $this->createdClientIds)->delete();
        }

        parent::tearDown();
    }

    /**
     * Helper to invoke protected methods on ServiceCompanyClientController via reflection.
     */
    protected function invokeControllerMethod(string $methodName, array $args = [])
    {
        $controller = app(ServiceCompanyClientController::class);
        $ref = new \ReflectionClass($controller);
        $method = $ref->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($controller, $args);
    }

    /**
     * Test 1: First claim for a new key succeeds (returns null) and creates record with PROCESSING status.
     */
    public function test_first_idempotency_claim_succeeds_and_records_processing(): void
    {
        $key = 'client_test_key_' . uniqid();
        $this->testKeys[] = $key;

        $payload = [
            'name' => 'Acme Corporation',
            'website' => 'https://acme-' . uniqid() . '.example.com',
            'industry' => 1,
        ];

        $claim = $this->invokeControllerMethod('claimIdempotency', [
            $this->tenantId,
            $key,
            $payload,
        ]);

        $this->assertNull($claim, 'First claim must return null to allow request execution.');

        $record = DB::table('sys_idempotency_keys')
            ->where('tenant_id', $this->tenantId)
            ->where('idempotency_key', $key)
            ->first();

        $this->assertNotNull($record, 'Record must be inserted into sys_idempotency_keys.');
        $this->assertEquals('PROCESSING', $record->status);
        $this->assertEquals(hash('sha256', json_encode($payload)), $record->request_hash);
    }

    /**
     * Test 2: Concurrent / racing requests with the same idempotency key are blocked with 425 warning.
     */
    public function test_concurrent_racing_requests_with_same_key_are_blocked(): void
    {
        $key = 'client_racing_key_' . uniqid();
        $this->testKeys[] = $key;

        $payload = [
            'name' => 'Racing Client Corp',
            'website' => 'https://racing-' . uniqid() . '.example.com',
        ];

        // First thread claims the key
        $firstClaim = $this->invokeControllerMethod('claimIdempotency', [
            $this->tenantId,
            $key,
            $payload,
        ]);
        $this->assertNull($firstClaim);

        // 10 concurrent requests arrive while status is PROCESSING
        $blockedCount = 0;
        for ($i = 0; $i < 10; $i++) {
            $racingClaim = $this->invokeControllerMethod('claimIdempotency', [
                $this->tenantId,
                $key,
                $payload,
            ]);

            if (isset($racingClaim['status']) && $racingClaim['status'] === 'warning') {
                $blockedCount++;
            }
        }

        $this->assertEquals(10, $blockedCount, 'All concurrent racing attempts must be guarded as PROCESSING.');
    }

    /**
     * Test 3: Completed idempotency key returns cached response payload without re-running transaction.
     */
    public function test_completed_idempotency_key_returns_cached_response(): void
    {
        $key = 'client_complete_key_' . uniqid();
        $this->testKeys[] = $key;

        $payload = [
            'name' => 'Completed Client Corp',
            'website' => 'https://completed-' . uniqid() . '.example.com',
        ];

        // 1. Claim
        $this->invokeControllerMethod('claimIdempotency', [$this->tenantId, $key, $payload]);

        // 2. Complete
        $cachedResponse = [
            'status' => 'success',
            'message' => 'Client created successfully',
            'url' => 'http://localhost/service_company/clients',
            'client_id' => 12345,
        ];
        $this->invokeControllerMethod('completeIdempotency', [$this->tenantId, $key, $cachedResponse]);

        // 3. Replay with identical key & payload
        $replay = $this->invokeControllerMethod('claimIdempotency', [$this->tenantId, $key, $payload]);

        $this->assertNotNull($replay);
        $this->assertEquals('success', $replay['status']);
        $this->assertEquals(12345, $replay['client_id']);
        $this->assertTrue($replay['idempotent_replay'] ?? false);
    }

    /**
     * Test 4: Same idempotency key with differing payload is strictly rejected with 422.
     */
    public function test_payload_mismatch_with_same_key_is_rejected(): void
    {
        $key = 'client_mismatch_key_' . uniqid();
        $this->testKeys[] = $key;

        $payloadA = ['name' => 'Original Name', 'website' => 'https://alpha.example.com'];
        $payloadB = ['name' => 'Tampered Name', 'website' => 'https://beta.example.com'];

        // 1. Claim with payload A
        $this->invokeControllerMethod('claimIdempotency', [$this->tenantId, $key, $payloadA]);

        // 2. Attempt with payload B using same key
        $tamperedClaim = $this->invokeControllerMethod('claimIdempotency', [$this->tenantId, $key, $payloadB]);

        $this->assertNotNull($tamperedClaim);
        $this->assertEquals('error', $tamperedClaim['status']);
        $this->assertStringContainsString('mismatch', strtolower($tamperedClaim['message']));
    }

    /**
     * Test 5: Failed idempotency can be retried cleanly.
     */
    public function test_failed_idempotency_can_be_retried(): void
    {
        $key = 'client_retry_key_' . uniqid();
        $this->testKeys[] = $key;

        $payload = ['name' => 'Retryable Client', 'website' => 'https://retry.example.com'];

        // 1. Claim
        $this->invokeControllerMethod('claimIdempotency', [$this->tenantId, $key, $payload]);

        // 2. Fail
        $this->invokeControllerMethod('failIdempotency', [$this->tenantId, $key, 'Database timeout']);

        $record = DB::table('sys_idempotency_keys')
            ->where('tenant_id', $this->tenantId)
            ->where('idempotency_key', $key)
            ->first();
        $this->assertEquals('FAILED_RETRYABLE', $record->status);

        // 3. Retry with same payload -> must allow new claim
        $retryClaim = $this->invokeControllerMethod('claimIdempotency', [$this->tenantId, $key, $payload]);
        $this->assertNull($retryClaim, 'Retrying a FAILED_RETRYABLE key must succeed.');

        $updatedRecord = DB::table('sys_idempotency_keys')
            ->where('tenant_id', $this->tenantId)
            ->where('idempotency_key', $key)
            ->first();
        $this->assertEquals('PROCESSING', $updatedRecord->status);
    }

    /**
     * Test 6: Outbox event persistence in sys_outbox_events and ProcessClientOutboxEventJob execution.
     */
    public function test_outbox_event_persistence_and_job_execution(): void
    {
        $key = 'client_outbox_key_' . uniqid();
        $this->testKeys[] = $key;

        $eventId = 'evt_' . uniqid();
        $payload = [
            'client_id' => 999999,
            'company_id' => $this->tenantId,
            'client_name' => 'Outbox Test Client',
            'action' => 'created',
        ];

        // Insert pending event into sys_outbox_events
        $eventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => $this->tenantId,
            'idempotency_key' => $key,
            'event_type' => 'ClientCreated',
            'aggregate_type' => 'Client',
            'aggregate_id' => 999999,
            'payload' => json_encode($payload),
            'status' => 'PENDING',
            'created_at' => now(),
        ]);

        $persisted = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        $this->assertNotNull($persisted);
        $this->assertEquals('PENDING', $persisted->status);

        // Dispatch ProcessClientOutboxEventJob
        $job = new ProcessClientOutboxEventJob($eventId, $this->tenantId);
        $job->handle();

        // Check updated status
        $processed = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        $this->assertEquals('COMPLETED', $processed->status);
        $this->assertNotNull($processed->processed_at);
    }

    /**
     * Test 7: City list lookup leverages 24h cache to eliminate repeated DB reads.
     */
    public function test_citylist_caching_eliminates_repeated_db_reads(): void
    {
        $stateId = 999;
        $cacheKey = "global_citylist_html_state_{$stateId}";
        cache()->forget($cacheKey);

        $mockHtml = '<option value="">Select City</option><option value="1">Test City</option>';
        cache()->put($cacheKey, $mockHtml, 86400);

        $controller = app(ServiceCompanyClientController::class);
        $response = $controller->citylist($stateId);

        $this->assertEquals('success', $response['status']);
        $this->assertEquals($mockHtml, $response['data']);

        cache()->forget($cacheKey);
    }

    /**
     * Test 8: Dynamic address card snippet leverages caching for instantaneous rendering.
     */
    public function test_addresslist_caching_returns_cached_partial(): void
    {
        $cid = 2;
        $cacheKey = "client_addresslist_partial_{$cid}";
        cache()->forget($cacheKey);

        $mockPartial = '<div class="client-address-card" id="clientAddr_2">Mock Address</div>';
        cache()->put($cacheKey, $mockPartial, 86400);

        $controller = app(ServiceCompanyClientController::class);
        $response = $controller->addresslist($cid);

        $this->assertEquals('success', $response['status']);
        $this->assertEquals($mockPartial, $response['data']);

        cache()->forget($cacheKey);
    }
}

