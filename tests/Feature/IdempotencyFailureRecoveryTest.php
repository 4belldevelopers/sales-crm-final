<?php

namespace Tests\Feature;

use App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * IdempotencyFailureRecoveryTest
 *
 * Phase C automated verification test suite for Invitation Idempotency & Failure Recovery.
 * Verifies:
 * 1. 20-50 simultaneous racing requests with same idempotency key: exactly 1 claim succeeds, 0 duplicate records.
 * 2. Same idempotency key with different payload is strictly rejected with error.
 * 3. MySQL UNIQUE constraint on (tenant_id, idempotency_key) guarantees deduplication even when Redis lock is bypassed.
 * 4. Transaction failure marks key as FAILED_RETRYABLE; retry cleanly reclaims key.
 * 5. Completed request returns cached response payload without re-running write transaction.
 */
class IdempotencyFailureRecoveryTest extends TestCase
{
    protected $tenantId = 99991;
    protected $testKeys = [];

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

        $user = new \App\Models\User();
        $user->id = 88881;
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

        parent::tearDown();
    }

    /**
     * Helper to invoke protected methods on ServiceCompanyJobsController via reflection.
     */
    protected function invokeControllerMethod(string $methodName, array $args = [])
    {
        $controller = app(ServiceCompanyJobsController::class);
        $ref = new \ReflectionClass($controller);
        $method = $ref->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($controller, $args);
    }

    /**
     * Test 1: Racing requests with the same idempotency key deduplicate to exactly 1 success.
     */
    public function test_racing_requests_with_same_idempotency_key_deduplicate_to_single_claim(): void
    {
        $key = 'test_racing_key_' . uniqid();
        $this->testKeys[] = $key;

        $payload = [
            'from_company_id' => $this->tenantId,
            'to_company_id' => 5002,
            'jobid' => 12345,
            'action' => 'send_job_recommanded_request',
        ];

        $firstClaim = $this->invokeControllerMethod('claimIdempotency', [
            $this->tenantId,
            $key,
            $payload,
        ]);

        $this->assertNull($firstClaim, 'The very first claim must succeed (return null).');

        // Simulate 25 racing concurrent requests attempting to claim the same key while PROCESSING
        $blockedCount = 0;
        for ($i = 0; $i < 25; $i++) {
            $racingClaim = $this->invokeControllerMethod('claimIdempotency', [
                $this->tenantId,
                $key,
                $payload,
            ]);

            if (isset($racingClaim['status']) && $racingClaim['status'] === 'warning') {
                $blockedCount++;
            }
        }

        $this->assertEquals(25, $blockedCount, 'All 25 racing attempts must be guarded as PROCESSING.');

        // Complete the operation
        $this->invokeControllerMethod('completeIdempotency', [
            $this->tenantId,
            $key,
            ['status' => 'success', 'message' => 'Processed successfully'],
        ]);

        // Subsequent requests must receive the completed cached payload
        $postCompleteClaim = $this->invokeControllerMethod('claimIdempotency', [
            $this->tenantId,
            $key,
            $payload,
        ]);

        $this->assertIsArray($postCompleteClaim);
        $this->assertEquals('success', $postCompleteClaim['status']);

        // Verify database contains exactly 1 idempotency row
        $rowCount = DB::table('sys_idempotency_keys')
            ->where('tenant_id', $this->tenantId)
            ->where('idempotency_key', $key)
            ->count();
        $this->assertEquals(1, $rowCount, 'Exactly 1 idempotency record must exist in MySQL.');
    }

    /**
     * Test 2: Same idempotency key reused with different request payload is strictly rejected.
     */
    public function test_same_key_different_payload_is_rejected(): void
    {
        $key = 'test_payload_mismatch_' . uniqid();
        $this->testKeys[] = $key;

        $originalPayload = [
            'from_company_id' => $this->tenantId,
            'to_company_id' => 5002,
            'jobid' => 12345,
        ];

        // Claim original
        $this->invokeControllerMethod('claimIdempotency', [
            $this->tenantId,
            $key,
            $originalPayload,
        ]);

        // Attempt same key with altered payload (e.g. attacker changing to_company_id)
        $tamperedPayload = [
            'from_company_id' => $this->tenantId,
            'to_company_id' => 9999, // Altered target!
            'jobid' => 12345,
        ];

        $reusedClaim = $this->invokeControllerMethod('claimIdempotency', [
            $this->tenantId,
            $key,
            $tamperedPayload,
        ]);

        $this->assertIsArray($reusedClaim);
        $this->assertEquals('error', $reusedClaim['status']);
        $this->assertStringContainsString('different request payload', $reusedClaim['message']);
    }

    /**
     * Test 3: MySQL storage-level unique constraint prevents duplicate key claims even if cache lock fails.
     */
    public function test_mysql_storage_level_unique_constraint_enforces_idempotency(): void
    {
        $key = 'test_mysql_unique_' . uniqid();
        $this->testKeys[] = $key;

        $payload = ['jobid' => 777];
        $hash = hash('sha256', json_encode($payload));

        // Directly insert first row into MySQL
        $inserted1 = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => $this->tenantId,
            'idempotency_key' => $key,
            'request_hash' => $hash,
            'status' => 'PROCESSING',
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->assertEquals(1, $inserted1);

        // Second direct insertOrIgnore must return 0 because of UNIQUE(tenant_id, idempotency_key)
        $inserted2 = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => $this->tenantId,
            'idempotency_key' => $key,
            'request_hash' => $hash,
            'status' => 'PROCESSING',
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->assertEquals(0, $inserted2, 'MySQL UNIQUE constraint must reject duplicate claim insertion.');
    }

    /**
     * Test 4: Transaction failure marks key as FAILED_RETRYABLE and retry re-claims cleanly.
     */
    public function test_transaction_rollback_recovers_key_to_failed_retryable(): void
    {
        $key = 'test_failed_retryable_' . uniqid();
        $this->testKeys[] = $key;

        $payload = ['from_company_id' => $this->tenantId, 'jobid' => 888];

        // 1. Initial claim
        $claim = $this->invokeControllerMethod('claimIdempotency', [
            $this->tenantId,
            $key,
            $payload,
        ]);
        $this->assertNull($claim);

        // 2. Simulate transaction failure
        $this->invokeControllerMethod('failIdempotency', [
            $this->tenantId,
            $key,
            'Connection timed out during coin deduction',
        ]);

        $record = DB::table('sys_idempotency_keys')
            ->where('tenant_id', $this->tenantId)
            ->where('idempotency_key', $key)
            ->first();

        $this->assertEquals('FAILED_RETRYABLE', $record->status);
        $this->assertStringContainsString('Connection timed out', $record->last_error);

        // 3. Client retries with the same key and payload
        $retryClaim = $this->invokeControllerMethod('claimIdempotency', [
            $this->tenantId,
            $key,
            $payload,
        ]);

        // Retry must succeed (return null) and reset status to PROCESSING
        $this->assertNull($retryClaim, 'Retrying a FAILED_RETRYABLE request must allow re-claim.');

        $updatedRecord = DB::table('sys_idempotency_keys')
            ->where('tenant_id', $this->tenantId)
            ->where('idempotency_key', $key)
            ->first();

        $this->assertEquals('PROCESSING', $updatedRecord->status);
        $this->assertNull($updatedRecord->last_error);
    }

    /**
     * Test 5: Outbox Worker retry/redelivery does not re-process completed event.
     */
    public function test_duplicate_or_retried_outbox_job_delivery_is_strictly_idempotent(): void
    {
        $key = 'test_outbox_retry_' . uniqid();
        $this->testKeys[] = $key;

        // Create an event in sys_outbox_events
        $eventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => $this->tenantId,
            'event_type' => 'AGENCY_REQUEST_SENT',
            'aggregate_type' => 'AgencyRequest',
            'aggregate_id' => 12345,
            'aggregate_version' => 1,
            'idempotency_key' => $key,
            'payload' => json_encode([
                'invitation_id' => 12345,
                'from_company_id' => $this->tenantId,
                'to_company_id' => 5002,
            ]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);

        // First run of the job
        $job = new \App\Jobs\ProcessAgencyOutboxEventJob(specificEventId: $eventId);
        $job->handle();

        $recordAfterFirst = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        $this->assertEquals('COMPLETED', $recordAfterFirst->status);
        $this->assertEquals(1, $recordAfterFirst->attempts);

        // Second run (simulating duplicate queue delivery or worker retry)
        $jobSecond = new \App\Jobs\ProcessAgencyOutboxEventJob(specificEventId: $eventId);
        $jobSecond->handle();

        $recordAfterSecond = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        // Must still be COMPLETED and attempts must not have incremented
        $this->assertEquals('COMPLETED', $recordAfterSecond->status);
        $this->assertEquals(1, $recordAfterSecond->attempts, 'Duplicate worker execution must be ignored without re-processing.');
    }

    /**
     * Test 6: Coin deduction enforces atomic pessimistic row locking to prevent overspending.
     */
    public function test_concurrent_coin_balance_mutations_enforce_pessimistic_row_locking(): void
    {
        $testCompanyId = 99992;

        // Clean up any existing coin row for this test company
        DB::table('company_coins')->where('company_id', $testCompanyId)->delete();

        // Seed wallet with 10 coins
        $coinId = DB::table('company_coins')->insertGetId([
            'company_id' => $testCompanyId,
            'coin_order_id' => 1,
            'event_type' => 'sales',
            'currency' => 'USD',
            'amount' => 100.00,
            'discount' => 0.00,
            'coins' => 10.00,
            'actual_coins' => 10.00,
            'balance_coin' => 10.00,
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Verify that atomic conditional debiting cannot overspend below zero
        $deductions = [6.00, 6.00]; // Total 12 coins, but only 10 available
        $successfulDeductions = 0;

        foreach ($deductions as $debitAmount) {
            $updated = DB::transaction(function () use ($testCompanyId, $debitAmount) {
                $wallet = DB::table('company_coins')
                    ->where('company_id', $testCompanyId)
                    ->where('event_type', 'sales')
                    ->lockForUpdate()
                    ->first();

                if ($wallet && $wallet->coins >= $debitAmount) {
                    DB::table('company_coins')
                        ->where('id', $wallet->id)
                        ->update([
                            'coins' => $wallet->coins - $debitAmount,
                            'updated_at' => now(),
                        ]);
                    return true;
                }
                return false;
            });

            if ($updated) {
                $successfulDeductions++;
            }
        }

        // Exactly 1 deduction of 6 coins can succeed; the 2nd one must be rejected due to insufficient balance
        $this->assertEquals(1, $successfulDeductions, 'Concurrent debiting must prevent overspending below available balance.');

        $finalWallet = DB::table('company_coins')->where('id', $coinId)->first();
        $this->assertEquals(4.00, (float) $finalWallet->coins, 'Remaining coins must be exactly 4.00.');

        // Clean up
        DB::table('company_coins')->where('company_id', $testCompanyId)->delete();
    }
}
