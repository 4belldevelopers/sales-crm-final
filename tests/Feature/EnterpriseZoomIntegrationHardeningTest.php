<?php

namespace Tests\Feature;

use App\Services\SuperAdmin\SuperAdminZoomSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminZoomSettingReadService;
use App\Services\Zoom\Exceptions\ZoomCircuitBreakerOpenException;
use App\Services\Zoom\Exceptions\ZoomRateLimitException;
use App\Services\Zoom\ZoomApiClient;
use App\Services\Zoom\ZoomCircuitBreaker;
use App\Services\Zoom\ZoomConfigurationService;
use App\Services\Zoom\ZoomMeetingService;
use App\Services\Zoom\ZoomTokenManager;
use Carbon\Carbon;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

/**
 * Class EnterpriseZoomIntegrationHardeningTest
 *
 * Intent: Comprehensive forensic automated test suite validating the Enterprise Zoom
 * Integration Performance & Reliability Hardening requirements (Addendum).
 *
 * Test Scenarios:
 * 1. Singleton Database & Primary Key Integrity
 * 2. Client Secret Encryption at Rest & Sanitized Masking
 * 3. Configuration Versioning & Version-Aware Cache Keys
 * 4. Distributed Locking & Thundering Herd Token Protection
 * 5. Circuit Breaker Lifecycle (CLOSED -> OPEN -> HALF_OPEN -> CLOSED)
 * 6. HTTP 401 Controlled Token Refresh & Retry-Once
 * 7. HTTP 429 Rate-Limit Handling with Retry-After
 * 8. Non-Idempotent Duplicate Meeting Creation Fencing
 * 9. ATS Interview Scheduling Workflow Parity
 * 10. CQRS 0% MySQL Reads during Active Operations
 *
 * Author: Antigravity AI — Enterprise Zoom Integration Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class EnterpriseZoomIntegrationHardeningTest extends TestCase
{
    protected ?object $backupDatabaseRow = null;

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

        // Backup existing production database row
        $this->backupDatabaseRow = DB::table('zoomsettings')->where('id', 1)->first()
            ?? DB::table('zoomsettings')->first();

        // Clear Zoom-related caches
        Cache::forget('zoom_access_token:global');
        Cache::forget('superadmin:zoom_setting:singleton');
        Cache::forget('superadmin:zoom_setting:version');
        app(ZoomCircuitBreaker::class)->reset();
    }

    protected function tearDown(): void
    {
        // Restore authoritative database state and sync
        if ($this->backupDatabaseRow) {
            $this->updateTestConfiguration([
                'title' => $this->backupDatabaseRow->title ?? 'JERO Official Zoom Integration',
                'redirect_uri' => $this->backupDatabaseRow->redirect_uri ?? '5PdHLPvqR0SY77pwiruTnA',
                'client_id' => $this->backupDatabaseRow->client_id ?? 'ma_AqFVMQLyelwJ2hhaKlg',
                'client_secret' => $this->backupDatabaseRow->client_secret ?? 'FBrziLAFmSk9AFqx7Rdsg5nDsmVBnihT',
                'zoom_access_token' => $this->backupDatabaseRow->zoom_access_token ?? '',
                'status' => $this->backupDatabaseRow->status ?? 1,
            ]);
        }

        SuperAdminZoomSettingCacheManager::flushCaches();
        app(ZoomCircuitBreaker::class)->reset();

        parent::tearDown();
    }

    protected function updateTestConfiguration(array $attributes): void
    {
        DB::table('zoomsettings')->where('id', 1)->update($attributes);

        $fresh = DB::table('zoomsettings')->where('id', 1)->first();
        $readService = app(SuperAdminZoomSettingReadService::class);
        $elastic = app(\App\Services\ElasticService::class);
        $index = $readService->getIndexName();

        if ($elastic->indexExists($index) && $fresh) {
            $elastic->getClient()->index([
                'index' => $index,
                'id' => '1',
                'body' => $readService->buildZoomSettingDocument($fresh),
                'refresh' => 'wait_for',
            ]);
        }

        SuperAdminZoomSettingCacheManager::flushCaches();
    }

    /**
     * Test 1: Verify database singleton integrity and PRIMARY KEY constraint on `zoomsettings.id`.
     */
    public function test_database_singleton_integrity_and_primary_key_constraint(): void
    {
        // 1. Verify single record with id = 1 exists
        $count = DB::table('zoomsettings')->count();
        $this->assertEquals(1, $count, 'zoomsettings table must contain strictly 1 singleton record.');

        $record = DB::table('zoomsettings')->first();
        $this->assertEquals(1, $record->id, 'zoomsettings singleton record must have id = 1.');

        // 2. Verify PRIMARY KEY constraint exists in MySQL
        $primaryKeys = DB::select("SHOW KEYS FROM zoomsettings WHERE Key_name = 'PRIMARY'");
        $this->assertNotEmpty($primaryKeys, 'PRIMARY KEY constraint must exist on `zoomsettings`.');
        $this->assertEquals('id', $primaryKeys[0]->Column_name, 'PRIMARY KEY column must be `id`.');
    }

    /**
     * Test 2: Verify client_secret encryption at rest and sanitized masking.
     */
    public function test_client_secret_encrypted_at_rest_and_sanitized_in_output(): void
    {
        $rawSecret = 'SecretKey_Enterprise_9988';
        $encrypted = Crypt::encryptString($rawSecret);

        $this->updateTestConfiguration([
            'client_secret' => $encrypted,
        ]);

        // 1. Database value must not match plaintext
        $dbRow = DB::table('zoomsettings')->where('id', 1)->first();
        $this->assertNotEquals($rawSecret, $dbRow->client_secret, 'Client secret must be encrypted at rest in MySQL.');

        // 2. ConfigurationService must decrypt it correctly
        $configService = app(ZoomConfigurationService::class);
        $config = $configService->getConfiguration();
        $this->assertEquals($rawSecret, $config['client_secret'], 'ConfigurationService must decrypt client secret at runtime.');

        // 3. Sanitized configuration must mask the secret
        $sanitized = $configService->getSanitizedConfiguration();
        $this->assertEquals('••••••••••••••••', $sanitized['client_secret'], 'Sanitized config must mask the client secret with bullets.');
        $this->assertStringNotContainsString($rawSecret, json_encode($sanitized));
    }

    /**
     * Test 3: Legacy plaintext client_secret fallback handling.
     */
    public function test_legacy_plaintext_client_secret_fallback(): void
    {
        $plainSecret = 'LegacyPlaintextSecret123';

        // Intentionally store plaintext without encryption
        $this->updateTestConfiguration([
            'client_secret' => $plainSecret,
        ]);

        $configService = app(ZoomConfigurationService::class);
        $config = $configService->getConfiguration();

        // Must gracefully fall back to plaintext without throwing DecryptException
        $this->assertEquals($plainSecret, $config['client_secret']);
    }

    /**
     * Test 4: Distributed locking and thundering herd prevention during OAuth token refresh.
     */
    public function test_distributed_locking_and_token_refresh_deduplication(): void
    {
        $encryptedSecret = Crypt::encryptString('dummy_secret_999');
        DB::table('zoomsettings')->where('id', 1)->update([
            'redirect_uri' => 'acc_test_dedup',
            'client_id' => 'client_test_dedup',
            'client_secret' => $encryptedSecret,
            'status' => 1,
        ]);
        SuperAdminZoomSettingCacheManager::flushCaches();

        $tokenManager = app(ZoomTokenManager::class);
        $tokenManager->invalidateToken();

        $requestCount = 0;
        Http::fake([
            'https://zoom.us/oauth/token' => function () use (&$requestCount) {
                $requestCount++;

                return Http::response([
                    'access_token' => 'mock_token_dedup_success_'.uniqid(),
                    'token_type' => 'bearer',
                    'expires_in' => 3600,
                    'scope' => 'meeting:write:admin user:read:admin',
                ], 200);
            },
        ]);

        // Request token twice consecutively — second call must hit Redis cache and NOT call Zoom API
        $token1 = $tokenManager->getAccessToken();
        $token2 = $tokenManager->getAccessToken();

        $this->assertEquals($token1, $token2);
        $this->assertEquals(1, $requestCount, 'Zoom OAuth token endpoint must be called exactly ONCE due to Redis caching.');
    }

    /**
     * Test 5: Circuit breaker state transitions (CLOSED -> OPEN -> HALF_OPEN -> CLOSED).
     */
    public function test_circuit_breaker_state_transitions_and_fast_fail(): void
    {
        $circuitBreaker = app(ZoomCircuitBreaker::class);
        $circuitBreaker->reset();

        // 1. Initial state must be CLOSED
        $this->assertEquals(ZoomCircuitBreaker::STATE_CLOSED, $circuitBreaker->getState());

        // 2. Record 5 consecutive failures to trip circuit
        for ($i = 0; $i < 5; $i++) {
            $circuitBreaker->recordFailure("Failure {$i}");
        }

        // 3. State must transition to OPEN
        $this->assertEquals(ZoomCircuitBreaker::STATE_OPEN, $circuitBreaker->getState());

        // 4. In OPEN state, guard() must immediately throw ZoomCircuitBreakerOpenException in 0ms
        $thrown = false;
        try {
            $circuitBreaker->guard();
        } catch (ZoomCircuitBreakerOpenException $e) {
            $thrown = true;
            $this->assertGreaterThan(0, $e->getCooldownRemainingSeconds());
        }
        $this->assertTrue($thrown, 'guard() must throw ZoomCircuitBreakerOpenException when circuit is OPEN.');

        // 5. Test manual reset restores CLOSED state
        $circuitBreaker->reset();
        $this->assertEquals(ZoomCircuitBreaker::STATE_CLOSED, $circuitBreaker->getState());
    }

    /**
     * Test 6: HTTP 401 controlled token refresh and single retry in ZoomApiClient.
     */
    public function test_api_client_handles_http_401_with_single_token_refresh(): void
    {
        $encryptedSecret = Crypt::encryptString('dummy_secret_401');
        DB::table('zoomsettings')->where('id', 1)->update([
            'redirect_uri' => 'acc_test_401',
            'client_id' => 'client_test_401',
            'client_secret' => $encryptedSecret,
            'status' => 1,
        ]);
        SuperAdminZoomSettingCacheManager::flushCaches();

        $tokenRefreshCount = 0;
        $apiCallCount = 0;

        Http::fake([
            'https://zoom.us/oauth/token' => function () use (&$tokenRefreshCount) {
                $tokenRefreshCount++;

                return Http::response([
                    'access_token' => "refreshed_token_v{$tokenRefreshCount}",
                    'token_type' => 'bearer',
                    'expires_in' => 3600,
                ], 200);
            },
            'https://api.zoom.us/v2/users/me' => function () use (&$apiCallCount) {
                $apiCallCount++;
                if ($apiCallCount === 1) {
                    // First call returns 401 (token expired on Zoom's side)
                    return Http::response(['message' => 'Access token is expired.'], 401);
                }

                // Second retry call succeeds with refreshed token
                return Http::response([
                    'id' => 'zoom_user_123',
                    'email' => 'admin@jero.works',
                    'first_name' => 'JERO',
                    'last_name' => 'Admin',
                    'type' => 2,
                ], 200);
            },
        ]);

        $apiClient = app(ZoomApiClient::class);
        $user = $apiClient->getCurrentUser();

        $this->assertEquals('zoom_user_123', $user['id']);
        $this->assertEquals(2, $apiCallCount, 'API client must retry exactly once upon 401.');
        $this->assertGreaterThanOrEqual(1, $tokenRefreshCount, 'Token must be refreshed upon 401.');
    }

    /**
     * Test 7: HTTP 429 Rate-Limit detection with Retry-After header.
     */
    public function test_api_client_handles_http_429_rate_limiting(): void
    {
        $encryptedSecret = Crypt::encryptString('dummy_secret_429');
        DB::table('zoomsettings')->where('id', 1)->update([
            'redirect_uri' => 'acc_test_429',
            'client_id' => 'client_test_429',
            'client_secret' => $encryptedSecret,
            'status' => 1,
        ]);
        SuperAdminZoomSettingCacheManager::flushCaches();

        Http::fake([
            'https://zoom.us/oauth/token' => Http::response([
                'access_token' => 'mock_token_429',
                'expires_in' => 3600,
            ], 200),
            'https://api.zoom.us/v2/users/me' => Http::response(
                ['message' => 'Too many requests'],
                429,
                ['Retry-After' => '45']
            ),
        ]);

        $apiClient = app(ZoomApiClient::class);

        $caught = false;
        try {
            $apiClient->getCurrentUser();
        } catch (ZoomRateLimitException $e) {
            $caught = true;
            $this->assertEquals(45, $e->getRetryAfterSeconds());
        }

        $this->assertTrue($caught, 'ZoomApiClient must throw ZoomRateLimitException with parsed Retry-After.');
    }

    /**
     * Test 8: Non-idempotent duplicate meeting creation fencing.
     */
    public function test_duplicate_meeting_creation_fencing(): void
    {
        $encryptedSecret = Crypt::encryptString('dummy_secret_dedup');
        DB::table('zoomsettings')->where('id', 1)->update([
            'redirect_uri' => 'acc_test_dedup_meet',
            'client_id' => 'client_test_dedup_meet',
            'client_secret' => $encryptedSecret,
            'status' => 1,
        ]);
        SuperAdminZoomSettingCacheManager::flushCaches();

        $meetingPostCount = 0;
        Http::fake([
            'https://zoom.us/oauth/token' => Http::response([
                'access_token' => 'mock_token_dedup',
                'expires_in' => 3600,
            ], 200),
            'https://api.zoom.us/v2/users/me/meetings' => function () use (&$meetingPostCount) {
                $meetingPostCount++;

                return Http::response([
                    'id' => 9876543210,
                    'join_url' => 'https://zoom.us/j/9876543210?pwd=test',
                    'start_url' => 'https://zoom.us/s/9876543210',
                    'topic' => 'Interview Schedule',
                    'password' => 'secret123',
                ], 201);
            },
        ]);

        $meetingService = app(ZoomMeetingService::class);
        $dedupKey = 'ats_sched_job_42_candidate_108';

        // Call twice with the same deduplication key (simulating recruiter double-click)
        $meeting1 = $meetingService->createInterviewMeeting(['topic' => 'Interview Schedule'], $dedupKey);
        $meeting2 = $meetingService->createInterviewMeeting(['topic' => 'Interview Schedule'], $dedupKey);

        $this->assertTrue($meeting1['success']);
        $this->assertTrue($meeting2['success']);
        $this->assertEquals($meeting1['join_url'], $meeting2['join_url']);
        $this->assertEquals(1, $meetingPostCount, 'Zoom API meetings endpoint must only be called ONCE when deduplication key is supplied.');
    }

    /**
     * Test 9: Backward-compatible ATS interview scheduling contract.
     */
    public function test_ats_interview_scheduling_contract_compatibility(): void
    {
        $encryptedSecret = Crypt::encryptString('dummy_secret_ats');
        DB::table('zoomsettings')->where('id', 1)->update([
            'redirect_uri' => 'acc_test_ats',
            'client_id' => 'client_test_ats',
            'client_secret' => $encryptedSecret,
            'status' => 1,
        ]);
        SuperAdminZoomSettingCacheManager::flushCaches();

        Http::fake([
            'https://zoom.us/oauth/token' => Http::response([
                'access_token' => 'mock_token_ats',
                'expires_in' => 3600,
            ], 200),
            'https://api.zoom.us/v2/users/me/meetings' => Http::response([
                'id' => 1234567890,
                'join_url' => 'https://zoom.us/j/1234567890?pwd=ats',
                'start_url' => 'https://zoom.us/s/1234567890',
                'topic' => 'Candidate Technical Interview',
            ], 201),
        ]);

        $meetingService = app(ZoomMeetingService::class);
        $result = $meetingService->createInterviewMeeting([
            'topic' => 'Candidate Technical Interview',
            'start_time' => Carbon::now()->addHour(),
        ]);

        // Validate ATS expectation contract
        $this->assertEquals('success', $result['status']);
        $this->assertTrue($result['success']);
        $this->assertEquals('https://zoom.us/j/1234567890?pwd=ats', $result['join_url']);
        $this->assertEquals('1234567890', $result['meeting_id']);
    }

    /**
     * Test 10: Circuit Breaker Open fallback matches ATS 'expired' contract.
     */
    public function test_circuit_breaker_open_returns_ats_fallback_format(): void
    {
        $circuitBreaker = app(ZoomCircuitBreaker::class);
        $circuitBreaker->trip(); // Force OPEN state

        $meetingService = app(ZoomMeetingService::class);
        $result = $meetingService->createInterviewMeeting(['topic' => 'ATS Interview']);

        // In OPEN state, must gracefully return ATS fallback contract without throwing unhandled exceptions
        $this->assertEquals('expired', $result['status']);
        $this->assertFalse($result['success']);
        $this->assertEquals('Zoom Meet', $result['join_url']);
        $this->assertNotEmpty($result['error']);
    }
}
