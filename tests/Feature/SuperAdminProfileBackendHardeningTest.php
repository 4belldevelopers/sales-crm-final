<?php

namespace Tests\Feature;

use App\Helper\Files;
use App\Http\Requests\SuperAdmin\UpdateSuperAdminProfileRequest;
use App\Jobs\ProcessSuperAdminProfileOutboxEventJob;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminProfileCacheManager;
use App\Services\SuperAdmin\SuperAdminProfileCircuitBreaker;
use App\Services\SuperAdmin\SuperAdminProfileMutationService;
use App\Services\SuperAdmin\SuperAdminProfileReadService;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SuperAdminProfileBackendHardeningTest
 *
 * Automated verification suite for Super Admin Profile God-Level Backend Architecture:
 * - CQRS Read Path: 0% MySQL reads on warm page loads (Rule 1 & Rule 2)
 * - Two-Tier Idempotency: Replay caching and 422 payload conflict rejection
 * - Security & Privacy: Passwords, hashes, and session secrets strictly excluded from Elasticsearch
 * - Optimistic Concurrency Control (OCC): Stale aggregate versions strictly rejected
 * - Two-Phase Compensation: Avatar orphan-file cleanup on transaction rollback
 * - Circuit-Breaker Resilience: State transitions, fallback metrics, and recovery
 * - Cache Priming & Read-After-Write Consistency: 0ms stale window
 * - Outbox Recovery & Automated Reconciliation: Drift detection and healing
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic restoration in tearDown().
 */
class SuperAdminProfileBackendHardeningTest extends TestCase
{
    protected User $superAdmin;

    protected ?array $originalUserRow = null;

    protected array $createdOutboxIds = [];

    protected array $createdIdempotencyKeys = [];

    protected array $createdFilePaths = [];

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

        // Backup original database row
        $row = DB::table('users')->where('id', $this->superAdmin->id)->first();
        if ($row) {
            $this->originalUserRow = (array) $row;
        }

        SuperAdminProfileCircuitBreaker::reset();
    }

    protected function tearDown(): void
    {
        // Restore original database records to MySQL and Elasticsearch
        if ($this->originalUserRow !== null) {
            DB::table('users')->where('id', $this->superAdmin->id)->update($this->originalUserRow);

            $esService = app(ElasticService::class);
            $readService = app(SuperAdminProfileReadService::class);
            $doc = $readService->buildProfileDocument((object) $this->originalUserRow);
            $esService->getClient()->index([
                'index' => $readService->getIndexName(),
                'id' => (string) $this->superAdmin->id,
                'body' => $doc,
                'refresh' => 'wait_for',
            ]);
        }

        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        foreach ($this->createdFilePaths as $filePath) {
            if (File::exists($filePath)) {
                File::delete($filePath);
            }
        }

        SuperAdminProfileCacheManager::flushCaches($this->superAdmin->id);
        SuperAdminProfileCircuitBreaker::reset();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path executes 0 MySQL reads against users on warm cache.
     */
    public function test_profile_read_path_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $readService = app(SuperAdminProfileReadService::class);

        // Warm up cache
        $readService->getSuperAdminProfile($this->superAdmin->id);

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, '`users`') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        // Execute warm read
        $cachedProfile = $readService->getSuperAdminProfile($this->superAdmin->id);

        $this->assertEquals(0, $queryCount, 'Warm profile read issued unbuffered MySQL query.');
        $this->assertEquals($this->superAdmin->id, $cachedProfile->id);
    }

    /**
     * Test 2: Idempotency with same key and identical payload replays stored response.
     */
    public function test_idempotency_same_key_identical_payload_replays_response(): void
    {
        $mutationService = app(SuperAdminProfileMutationService::class);
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $requestData = [
            'name' => 'Hardened Admin',
            'email' => $this->superAdmin->email,
            'calling_code' => '+1',
            'mobile' => '5550199',
            'whatsapp_number' => '+15550199',
            'linkedin' => 'https://linkedin.com/in/hardenedadmin',
            'idempotency_key' => $idempotencyKey,
        ];

        $request1 = new UpdateSuperAdminProfileRequest();
        $request1->merge($requestData);

        $response1 = $mutationService->updateProfile($request1, $this->superAdmin->id, $idempotencyKey);
        $this->assertEquals('success', $response1['status']);

        // Duplicate submission with same key and identical payload
        $request2 = new UpdateSuperAdminProfileRequest();
        $request2->merge($requestData);

        $response2 = $mutationService->updateProfile($request2, $this->superAdmin->id, $idempotencyKey);
        $this->assertTrue($response2['_replayed'] ?? false, 'Duplicate idempotency request did not set _replayed flag.');
        $this->assertEquals($response1['status'], $response2['status']);
    }

    /**
     * Test 3: Idempotency with same key but different payload aborts with HTTP 422.
     */
    public function test_idempotency_same_key_different_payload_rejects_with_422(): void
    {
        $mutationService = app(SuperAdminProfileMutationService::class);
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $request1 = new UpdateSuperAdminProfileRequest();
        $request1->merge([
            'name' => 'Original Payload',
            'email' => $this->superAdmin->email,
            'mobile' => '1111111111',
            'idempotency_key' => $idempotencyKey,
        ]);

        $mutationService->updateProfile($request1, $this->superAdmin->id, $idempotencyKey);

        // Conflicting request with same idempotency key but different mobile number
        $request2 = new UpdateSuperAdminProfileRequest();
        $request2->merge([
            'name' => 'Different Payload Name',
            'email' => $this->superAdmin->email,
            'mobile' => '9999999999',
            'idempotency_key' => $idempotencyKey,
        ]);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $mutationService->updateProfile($request2, $this->superAdmin->id, $idempotencyKey);
    }

    /**
     * Test 4: Passwords, hashes, and session secrets strictly excluded from Elasticsearch.
     */
    public function test_passwords_and_secrets_never_enter_elasticsearch(): void
    {
        $readService = app(SuperAdminProfileReadService::class);
        $elasticService = app(ElasticService::class);

        $indexName = $readService->getIndexName();
        $this->assertTrue($elasticService->indexExists($indexName));

        $esDoc = $elasticService->getClient()->get([
            'index' => $indexName,
            'id' => (string) $this->superAdmin->id,
        ]);

        $source = $esDoc['_source'] ?? [];

        $forbiddenKeys = [
            'password',
            'super_password',
            'super_admin_login_code',
            'remember_token',
            'session_id',
            'active_session',
            'session_time',
            'email_verification_code',
        ];

        foreach ($forbiddenKeys as $forbiddenKey) {
            $this->assertArrayNotHasKey(
                $forbiddenKey,
                $source,
                "Security vulnerability: Sensitive authentication key '{$forbiddenKey}' was found in Elasticsearch projection!"
            );
        }
    }

    /**
     * Test 5: Optimistic Concurrency Control (OCC) rejects stale aggregate versions.
     */
    public function test_optimistic_concurrency_control_rejects_stale_aggregate_version(): void
    {
        $elasticService = app(ElasticService::class);
        $readService = app(SuperAdminProfileReadService::class);
        $indexName = $readService->getIndexName();

        // Seed a document with future aggregate version 999999999
        $higherVersion = 999999999;
        $doc = $readService->buildProfileDocument((object) [
            'id' => $this->superAdmin->id,
            'company_id' => null,
            'name' => 'Future Version Admin',
            'email' => $this->superAdmin->email,
            'calling_code' => '+1',
            'mobile' => '1234567890',
            'mobile_verified' => 1,
            'whatsapp_number' => null,
            'linkedin' => null,
            'image' => null,
            'is_superadmin' => 1,
            'status' => 'active',
            'updated_at' => now()->toIso8601String(),
        ]);
        $doc['aggregate_version'] = $higherVersion;

        $elasticService->getClient()->index([
            'index' => $indexName,
            'id' => (string) $this->superAdmin->id,
            'body' => $doc,
            'refresh' => 'wait_for',
        ]);

        // Insert a simulated delayed outbox event with older version
        $staleVersion = 500;
        $eventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => (string) Str::uuid(),
            'aggregate_type' => 'SuperAdminProfile',
            'aggregate_id' => $this->superAdmin->id,
            'aggregate_version' => $staleVersion,
            'event_type' => 'SUPERADMIN_PROFILE_UPDATED',
            'payload' => json_encode([
                'aggregate_id' => $this->superAdmin->id,
                'attributes' => [
                    'id' => $this->superAdmin->id,
                    'name' => 'Stale Overwrite Attempt',
                    'email' => $this->superAdmin->email,
                ],
            ]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);
        $this->createdOutboxIds[] = $eventId;

        $job = new ProcessSuperAdminProfileOutboxEventJob($eventId);
        $job->handle($elasticService);

        // Verify that the Elasticsearch document was NOT overwritten by the stale event
        $currentDoc = $elasticService->getClient()->get([
            'index' => $indexName,
            'id' => (string) $this->superAdmin->id,
        ]);

        $this->assertEquals($higherVersion, (int) $currentDoc['_source']['aggregate_version']);
        $this->assertEquals('Future Version Admin', $currentDoc['_source']['name']);
    }

    /**
     * Test 6: Two-Phase Avatar Upload Compensation purges newly created orphan files on transaction rollback.
     */
    public function test_avatar_upload_two_phase_compensation_purges_orphan_file_on_rollback(): void
    {
        $mutationService = app(SuperAdminProfileMutationService::class);

        // Create a temporary mock image file
        $tempImageName = 'test_compensation_' . Str::random(8) . '.png';
        $tempPath = public_path('user-uploads/profile/' . $tempImageName);
        File::ensureDirectoryExists(public_path('user-uploads/profile'));
        File::put($tempPath, 'fake_png_binary_data');
        $this->createdFilePaths[] = $tempPath;

        $uploadedFile = new UploadedFile($tempPath, $tempImageName, 'image/png', null, true);

        // Request with invalid current password to force transaction rollback
        $request = new UpdateSuperAdminProfileRequest();
        $request->merge([
            'name' => 'Failed Admin',
            'email' => $this->superAdmin->email,
            'current_password' => 'absolutely_wrong_password_12345',
            'password' => 'NewStrongPassword123#',
            'password_confirmation' => 'NewStrongPassword123#',
        ]);
        $request->files->set('image', $uploadedFile);

        $response = $mutationService->updateProfile($request, $this->superAdmin->id, (string) Str::uuid());

        $this->assertEquals('fail', $response['status']);
        $this->assertEquals('current_password', $response['error_name']);

        // Check if any orphan files were created in public/user-uploads/profile
        // In the compensation pattern, newly created upload should have been removed
        $leftoverFiles = File::glob(public_path('user-uploads/profile/*test_compensation*'));
        $this->assertEmpty($leftoverFiles, 'Orphan avatar file was left on disk after transaction rollback.');
    }

    /**
     * Test 7: Circuit Breaker transitions states and records fallback read metrics.
     */
    public function test_circuit_breaker_state_transitions_and_fallback_metrics(): void
    {
        SuperAdminProfileCircuitBreaker::reset();
        $this->assertEquals(SuperAdminProfileCircuitBreaker::STATE_CLOSED, SuperAdminProfileCircuitBreaker::getState());
        $this->assertTrue(SuperAdminProfileCircuitBreaker::isAvailable());

        // Simulate 3 failures
        $dummyException = new \RuntimeException('Elasticsearch cluster timed out');
        SuperAdminProfileCircuitBreaker::recordFailure($dummyException);
        SuperAdminProfileCircuitBreaker::recordFailure($dummyException);
        SuperAdminProfileCircuitBreaker::recordFailure($dummyException);

        $this->assertEquals(SuperAdminProfileCircuitBreaker::STATE_OPEN, SuperAdminProfileCircuitBreaker::getState());
        $this->assertFalse(SuperAdminProfileCircuitBreaker::isAvailable());

        // Record a fallback read
        SuperAdminProfileCircuitBreaker::recordFallbackRead();
        $this->assertEquals(1, SuperAdminProfileCircuitBreaker::getFallbackReadCount());

        // Reset
        SuperAdminProfileCircuitBreaker::reset();
        $this->assertEquals(SuperAdminProfileCircuitBreaker::STATE_CLOSED, SuperAdminProfileCircuitBreaker::getState());
    }

    /**
     * Test 8: Read-after-write cache priming ensures instant consistency without stale reads.
     */
    public function test_read_after_write_cache_priming_instant_consistency(): void
    {
        $mutationService = app(SuperAdminProfileMutationService::class);
        $readService = app(SuperAdminProfileReadService::class);

        $updatedName = 'Immediate Consistent Admin ' . Str::random(6);

        $request = new UpdateSuperAdminProfileRequest();
        $request->merge([
            'name' => $updatedName,
            'email' => $this->superAdmin->email,
            'calling_code' => '+91',
            'mobile' => '9876543210',
            'idempotency_key' => (string) Str::uuid(),
        ]);

        $mutationService->updateProfile($request, $this->superAdmin->id);

        // Immediate read right after write
        $readProfile = $readService->getSuperAdminProfile($this->superAdmin->id);

        $this->assertEquals($updatedName, $readProfile->name);
    }

    /**
     * Test 9: Automated reconciliation command executes without errors.
     */
    public function test_automated_reconciliation_command(): void
    {
        $exitCode = \Illuminate\Support\Facades\Artisan::call('superadmin-profile:process-outbox', [
            '--reconcile' => true,
        ]);

        $this->assertEquals(0, $exitCode);
    }
}
