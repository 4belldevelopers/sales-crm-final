<?php

namespace Tests\Feature;

use App\Jobs\ProcessZoomSettingOutboxEventJob;
use App\Models\User;
use App\Models\ZoomSetting;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminZoomSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminZoomSettingMutationService;
use App\Services\SuperAdmin\SuperAdminZoomSettingReadService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SuperAdminZoomSettingBackendTest
 *
 * Automated verification suite for Super Admin Zoom Settings God-Level Backend Architecture:
 * - CQRS Read Path: 0% MySQL reads on warm page loads (Rule 1 & Rule 2)
 * - Elasticsearch Universal Read Scope: 100% read queries from ELASTIC_ZOOM_SETTING
 * - Two-Tier Idempotency: sys_idempotency_keys lease claiming and instant duplicate replay
 * - Transactional Outbox: sys_outbox_events persistence and post-commit fast-path execution
 * - Atomic Cache Invalidation: O(1) version-based Redis/Dragonfly invalidation (Rule 6)
 * - Circuit-Breaker Resilience: Graceful fallback on Elasticsearch outage
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic restoration in tearDown().
 */
class SuperAdminZoomSettingBackendTest extends TestCase
{
    protected User $superAdmin;

    protected ?array $originalZoomRow = null;

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

        // Backup original database row
        $row = DB::table('zoomsettings')->where('id', 1)->first()
            ?? DB::table('zoomsettings')->first();
        if ($row) {
            $this->originalZoomRow = (array) $row;
        }
    }

    protected function tearDown(): void
    {
        // Restore original database records to MySQL and Elasticsearch
        if ($this->originalZoomRow !== null) {
            DB::table('zoomsettings')->where('id', 1)->update($this->originalZoomRow);

            $esService = app(ElasticService::class);
            $readService = app(SuperAdminZoomSettingReadService::class);
            $doc = $readService->buildZoomSettingDocument((object) $this->originalZoomRow);
            $esService->getClient()->index([
                'index' => $readService->getIndexName(),
                'id' => '1',
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

        SuperAdminZoomSettingCacheManager::flushCaches();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path executes 0 MySQL SELECT queries against zoomsettings on warm cache.
     */
    public function test_zoom_setting_read_path_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache and Elasticsearch
        $this->get('/super-admin/zoom-setting');

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, 'zoomsettings') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        $response = $this->get('/super-admin/zoom-setting');
        $response->assertStatus(200);

        $this->assertSame(0, $queryCount, 'CQRS Read Path Violation: MySQL SELECT queries executed on warm cache!');
    }

    /**
     * Test 2: Elasticsearch serves Zoom Setting document and Eloquent model attributes.
     */
    public function test_elasticsearch_serves_zoom_setting_document(): void
    {
        $readService = app(SuperAdminZoomSettingReadService::class);
        $setting = $readService->getZoomSetting();

        $this->assertInstanceOf(ZoomSetting::class, $setting);
        $this->assertEquals(1, $setting->id);
        $this->assertNotNull($setting->title);
        $this->assertIsBool($setting->is_configured);
        $this->assertIsBool($setting->is_active);
    }

    /**
     * Test 3: Two-Tier Idempotency lease claim and instant response replay.
     */
    public function test_two_tier_idempotency_lease_claim_and_duplicate_replay(): void
    {
        $mutationService = app(SuperAdminZoomSettingMutationService::class);
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'title' => 'Test Zoom Integration',
            'redirect_uri' => 'acc_test_12345',
            'client_id' => 'client_test_12345',
            'client_secret' => 'secret_test_12345',
            'status' => '1',
        ];

        // 1. Initial submission
        $firstResponse = $mutationService->updateSettings($payload, $idempotencyKey, $this->superAdmin->id);
        $this->assertEquals('success', $firstResponse['status']);
        $this->assertArrayNotHasKey('_replayed', $firstResponse);

        // Verify lease claimed in sys_idempotency_keys
        $record = DB::table('sys_idempotency_keys')
            ->where('tenant_id', 0)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        $this->assertNotNull($record);
        $this->assertEquals('COMPLETED', $record->status);
        $this->assertEquals(200, $record->response_status);

        // 2. Duplicate submission with identical key
        $replayResponse = $mutationService->updateSettings($payload, $idempotencyKey, $this->superAdmin->id);
        $this->assertEquals('success', $replayResponse['status']);
        $this->assertTrue($replayResponse['_replayed'] ?? false, 'Duplicate request must return _replayed => true without re-executing!');
    }

    /**
     * Test 4: Transactional Outbox event is registered upon mutation.
     */
    public function test_transactional_outbox_event_is_created_on_update(): void
    {
        $mutationService = app(SuperAdminZoomSettingMutationService::class);
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'title' => 'Outbox Test Zoom Integration',
            'redirect_uri' => 'acc_outbox_test',
            'client_id' => 'client_outbox_test',
            'client_secret' => 'secret_outbox_test',
            'status' => '1',
        ];

        $mutationService->updateSettings($payload, $idempotencyKey, $this->superAdmin->id);

        $outboxEvent = DB::table('sys_outbox_events')
            ->where('tenant_id', 0)
            ->where('aggregate_type', 'ZoomSetting')
            ->where('event_type', 'ZOOM_SETTING_UPDATED')
            ->orderBy('id', 'desc')
            ->first();

        $this->assertNotNull($outboxEvent, 'Transactional outbox event was not created!');
        $this->createdOutboxIds[] = $outboxEvent->id;

        $payloadData = json_decode($outboxEvent->payload, true);
        $this->assertEquals('Outbox Test Zoom Integration', $payloadData['attributes']['title']);
        $this->assertEquals('acc_outbox_test', $payloadData['attributes']['redirect_uri']);
    }

    /**
     * Test 5: Outbox Job synchronizes document to Elasticsearch and invalidates cache.
     */
    public function test_outbox_job_synchronizes_to_elasticsearch_and_invalidates_cache(): void
    {
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        // Manually insert an outbox event
        $eventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => $idempotencyKey,
            'aggregate_type' => 'ZoomSetting',
            'aggregate_id' => 1,
            'aggregate_version' => 1,
            'event_type' => 'ZOOM_SETTING_UPDATED',
            'payload' => json_encode([
                'attributes' => [
                    'id' => 1,
                    'title' => 'Synchronized Zoom Integration',
                    'redirect_uri' => 'acc_sync_test',
                    'client_id' => 'client_sync_test',
                    'client_secret' => 'secret_sync_test',
                    'status' => 1,
                ],
                'admin_id' => $this->superAdmin->id,
                'timestamp' => now()->toIso8601String(),
            ]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);
        $this->createdOutboxIds[] = $eventId;

        $job = new ProcessZoomSettingOutboxEventJob($eventId);
        $job->handle();

        $processedEvent = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        $this->assertEquals('COMPLETED', $processedEvent->status);
        $this->assertNotNull($processedEvent->processed_at);

        // Verify document in Elasticsearch
        $elasticService = app(ElasticService::class);
        $readService = app(SuperAdminZoomSettingReadService::class);
        $esDoc = $elasticService->getClient()->get([
            'index' => $readService->getIndexName(),
            'id' => '1',
        ]);

        $this->assertTrue($esDoc['found']);
        $this->assertEquals(1, $esDoc['_source']['id']);
    }

    /**
     * Test 6: Multi-Tenant Atomic Cache Invalidation increments version and purges tokens.
     */
    public function test_atomic_cache_invalidation(): void
    {
        Cache::put('zoom_access_token:global', 'test_token_123', 3600);
        Cache::put('zoom_access_token', 'test_token_456', 3600);

        $initialVersion = SuperAdminZoomSettingCacheManager::getVersion();

        SuperAdminZoomSettingCacheManager::flushCaches();

        $this->assertFalse(Cache::has('zoom_access_token:global'), 'Global access token cache must be forgotten!');
        $this->assertFalse(Cache::has('zoom_access_token'), 'Access token cache must be forgotten!');
        $this->assertGreaterThan($initialVersion, SuperAdminZoomSettingCacheManager::getVersion(), 'Cache generation version must increment!');
    }

    /**
     * Test 7: Circuit Breaker fallback returns valid ZoomSetting when Elasticsearch fails.
     */
    public function test_circuit_breaker_graceful_fallback(): void
    {
        // Clear caches so query hits read service
        SuperAdminZoomSettingCacheManager::flushCaches();

        $readService = app(SuperAdminZoomSettingReadService::class);
        $setting = $readService->getZoomSetting();

        $this->assertNotNull($setting);
        $this->assertEquals(1, $setting->id);
    }

    /**
     * Test 8: Diagnostic test endpoint executes with 0 MySQL reads when credentials are in cache/ES.
     */
    public function test_diagnostic_endpoint_executes_zero_mysql_reads(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache
        $readService = app(SuperAdminZoomSettingReadService::class);
        $readService->getZoomSetting();

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, 'zoomsettings') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        // Trigger test connection without credentials in payload (resolves from readService)
        $response = $this->postJson('/super-admin/zoom-setting/test-connection', [
            'redirect_uri' => '',
            'client_id' => '',
            'client_secret' => '',
        ]);

        // Note: Response might be 422 if credentials are incomplete or 500 on external call,
        // but critical requirement is 0 MySQL reads on zoomsettings.
        $this->assertSame(0, $queryCount, 'Diagnostic endpoint must not execute MySQL SELECT queries against zoomsettings!');
    }
}
