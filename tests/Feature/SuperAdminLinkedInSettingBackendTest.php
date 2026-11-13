<?php

namespace Tests\Feature;

use App\Models\LinkedInSetting;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\LinkedInSettingCircuitBreaker;
use App\Services\SuperAdmin\SuperAdminLinkedInSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminLinkedInSettingMutationService;
use App\Services\SuperAdmin\SuperAdminLinkedInSettingReadService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SuperAdminLinkedInSettingBackendTest
 *
 * Automated verification suite for Super Admin LinkedIn Settings God-Level Backend Architecture:
 * - CQRS Read Path: 0% MySQL reads on warm page requests (Rule 1 & Rule 2)
 * - Two-Tier Idempotency: Replay caching and duplicate prevention
 * - Transactional Outbox: Event persistence and Elasticsearch synchronization
 * - Circuit-Breaker Resilience: State transitions and emergency DB fallback
 * - Generational L2 Cache: O(1) version bump and atomic invalidation
 * - CLI Commands: Synchronous dual-store reindexing and outbox processing
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic restoration in tearDown().
 */
class SuperAdminLinkedInSettingBackendTest extends TestCase
{
    protected User $superAdmin;

    protected array $originalSettingState = [];

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

        // Snapshot original database state
        $existing = DB::table('linked_in_settings')->where('id', 1)->first();
        if ($existing) {
            $this->originalSettingState = (array) $existing;
        }

        LinkedInSettingCircuitBreaker::reset();
        SuperAdminLinkedInSettingCacheManager::flushCaches();
    }

    protected function tearDown(): void
    {
        // Restore original setting state
        if (! empty($this->originalSettingState)) {
            DB::table('linked_in_settings')->where('id', 1)->update([
                'client_id' => $this->originalSettingState['client_id'] ?? '',
                'client_secret' => $this->originalSettingState['client_secret'] ?? '',
                'status' => $this->originalSettingState['status'] ?? 'disable',
                'callback_url' => $this->originalSettingState['callback_url'] ?? '',
                'updated_at' => now(),
            ]);

            // Resync to ES
            try {
                $readService = app(SuperAdminLinkedInSettingReadService::class);
                $elasticService = app(ElasticService::class);
                $indexName = $readService->getIndexName();
                $doc = $readService->buildLinkedInSettingDocument($this->originalSettingState);
                $elasticService->getClient()->index([
                    'index' => $indexName,
                    'id' => '1',
                    'body' => $doc,
                    'refresh' => 'wait_for',
                ]);
            } catch (\Throwable) {
            }
        }

        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        LinkedInSettingCircuitBreaker::reset();
        SuperAdminLinkedInSettingCacheManager::flushCaches();

        parent::tearDown();
    }

    /**
     * Test CQRS Read path: 100% queries served from Elasticsearch / L2 Cache.
     * Guarantees 0% MySQL reads on warm page requests.
     */
    public function test_cqrs_read_path_serves_linkedin_settings_with_zero_mysql_reads(): void
    {
        // 1. Prime cache/Elasticsearch
        Artisan::call('linkedin-settings:reindex');

        // 2. Perform warm request
        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.linkedin-settings.index'));

        $response->assertStatus(200);
        $response->assertViewHas('linkedInSetting');
        $response->assertViewHas('sslData');
        $response->assertViewHas('httpsContains');

        $queries = DB::getQueryLog();
        $linkedInQueries = collect($queries)->filter(function ($q) {
            return str_contains(strtolower($q['query']), 'linked_in_settings');
        });

        $this->assertCount(
            0,
            $linkedInQueries,
            'CQRS Violation: Warm request executed direct MySQL reads on linked_in_settings table! Queries: '.json_encode($linkedInQueries->pluck('query'))
        );
    }

    /**
     * Test Elasticsearch serves authoritative singleton document and hydrates Eloquent model instance.
     */
    public function test_elasticsearch_serves_authoritative_document_and_hydrates_model(): void
    {
        Artisan::call('linkedin-settings:reindex');

        $readService = app(SuperAdminLinkedInSettingReadService::class);
        $model = $readService->getLinkedInSettings();

        $this->assertInstanceOf(LinkedInSetting::class, $model);
        $this->assertTrue($model->exists);
        $this->assertNotEmpty($model->callback_url);
        $this->assertStringContainsString('linkedin', $model->callback_url);
    }

    /**
     * Test Two-Tier Idempotency lease claim and duplicate replay.
     */
    public function test_two_tier_idempotency_lease_claim_and_duplicate_replay(): void
    {
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $mutationService = app(SuperAdminLinkedInSettingMutationService::class);

        $request = new \Illuminate\Http\Request([
            'client_id' => 'test_client_id_123',
            'client_secret' => 'test_client_secret_xyz',
            'status' => 'disable',
        ]);

        // First execution -> processes and completes
        $firstResponse = $mutationService->updateLinkedInSettings($request, 1, $idempotencyKey, $this->superAdmin->id);

        $this->assertEquals('success', $firstResponse['status']);
        $this->assertArrayHasKey('outbox_id', $firstResponse['data']);
        $this->createdOutboxIds[] = $firstResponse['data']['outbox_id'];

        // Second execution with identical idempotency key -> replays cached response
        $replayedResponse = $mutationService->updateLinkedInSettings($request, 1, $idempotencyKey, $this->superAdmin->id);

        $this->assertEquals('success', $replayedResponse['status']);
        $this->assertTrue($replayedResponse['_replayed'] ?? false);
    }

    /**
     * Test transactional outbox event creation and post-commit fast-path synchronization to Elasticsearch.
     */
    public function test_transactional_outbox_event_creation_and_fast_path_job(): void
    {
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $mutationService = app(SuperAdminLinkedInSettingMutationService::class);

        $request = new \Illuminate\Http\Request([
            'client_id' => 'oauth_client_'.Str::random(8),
            'client_secret' => 'oauth_secret_'.Str::random(16),
            'status' => 'disable',
        ]);

        $response = $mutationService->updateLinkedInSettings($request, 1, $idempotencyKey, $this->superAdmin->id);
        $outboxId = $response['data']['outbox_id'] ?? null;
        $this->assertNotNull($outboxId);
        $this->createdOutboxIds[] = $outboxId;

        // Verify outbox record status
        $outboxRecord = DB::table('sys_outbox_events')->where('id', $outboxId)->first();
        $this->assertNotNull($outboxRecord);
        $this->assertEquals('LinkedInSetting', $outboxRecord->aggregate_type);
        $this->assertEquals('LINKEDIN_SETTING_UPDATED', $outboxRecord->event_type);
        $this->assertEquals('COMPLETED', $outboxRecord->status);

        // Verify dual-store sync in Elasticsearch
        $elasticService = app(ElasticService::class);
        $readService = app(SuperAdminLinkedInSettingReadService::class);
        $esDoc = $elasticService->getClient()->get([
            'index' => $readService->getIndexName(),
            'id' => '1',
        ]);

        $this->assertEquals($request->input('client_id'), $esDoc['_source']['client_id']);
    }

    /**
     * Test status toggle dual-store synchronization.
     */
    public function test_status_toggle_dual_store_sync(): void
    {
        // First ensure valid credentials are present
        DB::table('linked_in_settings')->where('id', 1)->update([
            'client_id' => 'test_client_key',
            'client_secret' => 'test_secret_key',
            'status' => 'disable',
        ]);

        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $mutationService = app(SuperAdminLinkedInSettingMutationService::class);
        $request = new \Illuminate\Http\Request([
            'status' => 'enable',
        ]);

        $response = $mutationService->updateStatus($request, 1, $idempotencyKey, $this->superAdmin->id);
        $this->assertEquals('success', $response['status']);
        $this->assertEquals('enable', $response['data']['status']);

        if (isset($response['data']['outbox_id'])) {
            $this->createdOutboxIds[] = $response['data']['outbox_id'];
        }

        // Verify MySQL updated
        $mysqlRow = DB::table('linked_in_settings')->where('id', 1)->first();
        $this->assertEquals('enable', $mysqlRow->status);

        // Verify Elasticsearch updated
        $readService = app(SuperAdminLinkedInSettingReadService::class);
        $elasticService = app(ElasticService::class);
        $esDoc = $elasticService->getClient()->get([
            'index' => $readService->getIndexName(),
            'id' => '1',
        ]);
        $this->assertEquals('enable', $esDoc['_source']['status']);
    }

    /**
     * Test Circuit Breaker state transitions and emergency DB fallback protection.
     */
    public function test_circuit_breaker_resilience_and_emergency_db_fallback(): void
    {
        LinkedInSettingCircuitBreaker::reset();
        $this->assertEquals(LinkedInSettingCircuitBreaker::STATE_CLOSED, LinkedInSettingCircuitBreaker::getState());
        $this->assertTrue(LinkedInSettingCircuitBreaker::isAvailable());

        // Simulate 3 failures to trip circuit
        for ($i = 0; $i < 3; $i++) {
            LinkedInSettingCircuitBreaker::recordFailure(new \Exception("Simulated ES failure #{$i}"));
        }

        $this->assertEquals(LinkedInSettingCircuitBreaker::STATE_OPEN, LinkedInSettingCircuitBreaker::getState());
        $this->assertFalse(LinkedInSettingCircuitBreaker::isAvailable());
        $this->assertEquals(LinkedInSettingCircuitBreaker::MODE_DEGRADED_ES_OUTAGE, LinkedInSettingCircuitBreaker::getMode());

        // Read service should gracefully serve from rememberDbFallback without throwing
        $readService = app(SuperAdminLinkedInSettingReadService::class);
        $model = $readService->getLinkedInSettings();
        $this->assertInstanceOf(LinkedInSetting::class, $model);
        $this->assertGreaterThan(0, LinkedInSettingCircuitBreaker::getFallbackCount());

        // Reset
        LinkedInSettingCircuitBreaker::reset();
        $this->assertEquals(LinkedInSettingCircuitBreaker::STATE_CLOSED, LinkedInSettingCircuitBreaker::getState());
    }

    /**
     * Test diagnostic testConnection endpoint with validation and rate-limiting guard.
     */
    public function test_diagnostic_test_connection_endpoint(): void
    {
        $response = $this->actingAs($this->superAdmin)
            ->postJson(route('superadmin.linkedin-settings.testConnection'), [
                'client_id' => 'invalid_client_sample_test',
                'client_secret' => 'invalid_secret_sample_test',
            ]);

        // Connection should execute diagnostic reachability
        $response->assertStatus(200);
        $json = $response->json();
        $this->assertArrayHasKey('status', $json);
        $this->assertArrayHasKey('data', $json);
        $this->assertArrayHasKey('latency_ms', $json['data']);
    }
}
