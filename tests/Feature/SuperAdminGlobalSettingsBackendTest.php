<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminGlobalSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminGlobalSettingReadService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class SuperAdminGlobalSettingsBackendTest
 *
 * Automated verification suite for Super Admin Global Settings God-Level Backend Architecture:
 * - CQRS Read Path: 0% MySQL reads on warm page loads (Rule 1 & Rule 2)
 * - Elasticsearch Universal Read Scope: 100% read queries from ELASTIC_GLOBAL_SETTINGS & ELASTIC_SOLD_BY_ADDRESS
 * - Two-Tier Idempotency: sys_idempotency_keys lease claiming and instant duplicate replay
 * - Transactional Outbox: sys_outbox_events persistence and post-commit fast-path execution
 * - Atomic Cache Invalidation: O(1) version-based Redis/Dragonfly invalidation (Rule 6)
 * - Circuit-Breaker Resilience: Graceful fallback on Elasticsearch outage
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic restoration in tearDown().
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminGlobalSettingsBackendTest extends TestCase
{
    protected User $superAdmin;

    protected array $originalSettingRow = [];

    protected array $originalSoldByRow = [];

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

        // Backup original database rows
        $gs = DB::table('global_settings')->where('id', 1)->first();
        if ($gs) {
            $this->originalSettingRow = (array) $gs;
        }

        $sba = DB::table('sold_by_address')->where('id', 1)->first();
        if ($sba) {
            $this->originalSoldByRow = (array) $sba;
        }
    }

    protected function tearDown(): void
    {
        // Restore original database records
        if (! empty($this->originalSettingRow)) {
            DB::table('global_settings')->where('id', 1)->update($this->originalSettingRow);
        }

        if (! empty($this->originalSoldByRow)) {
            DB::table('sold_by_address')->where('id', 1)->update($this->originalSoldByRow);
        }

        // Restore Elasticsearch synchronized documents
        $readService = app(SuperAdminGlobalSettingReadService::class);
        $elasticService = app(ElasticService::class);
        if ($elasticService->indexExists($readService->getIndexName()) && ! empty($this->originalSettingRow)) {
            $currencyRow = DB::table('currencies')->where('id', $this->originalSettingRow['currency_id'] ?? 1)->first();
            $gsDoc = $readService->buildGlobalSettingDocument($this->originalSettingRow, $currencyRow);
            $elasticService->getClient()->index([
                'index' => $readService->getIndexName(),
                'id' => '1',
                'body' => $gsDoc,
                'refresh' => 'wait_for',
            ]);
        }
        if ($elasticService->indexExists($readService->getSoldByIndexName()) && ! empty($this->originalSoldByRow)) {
            $sbaDoc = $readService->buildSoldByAddressDocument($this->originalSoldByRow);
            $elasticService->getClient()->index([
                'index' => $readService->getSoldByIndexName(),
                'id' => '1',
                'body' => $sbaDoc,
                'refresh' => 'wait_for',
            ]);
        }

        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        SuperAdminGlobalSettingCacheManager::flushCaches();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path executes 0 MySQL SELECT queries on warm cache.
     */
    public function test_global_settings_read_path_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache and Elasticsearch
        $controller = app(\App\Http\Controllers\SuperAdmin\GlobalSettingController::class);
        $controller->index();

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, 'select') !== false) {
                // Ignore session, jobs or auth queries if any
                if (stripos($query->sql, 'global_settings') !== false
                    || stripos($query->sql, 'sold_by_address') !== false
                    || stripos($query->sql, 'currencies') !== false
                    || stripos($query->sql, 'language_settings') !== false
                    || stripos($query->sql, 'countries') !== false) {
                    $queryCount++;
                }
            }
        });

        $controller->index();

        $this->assertSame(0, $queryCount, 'CQRS Read Path Violation: MySQL SELECT queries executed on warm cache!');
    }

    /**
     * Test 2: Elasticsearch Universal Read Scope serves global settings and sold-by address documents.
     */
    public function test_elasticsearch_serves_global_settings_and_sold_by_address(): void
    {
        $readService = app(SuperAdminGlobalSettingReadService::class);

        $global = $readService->getGlobalSettings();
        $this->assertNotNull($global);
        $this->assertEquals(1, $global->id);
        $this->assertNotEmpty($global->company_name);
        $this->assertNotNull($global->logo_url);
        $this->assertNotNull($global->currency);
        $this->assertEquals('USD', $global->currency->currency_code);

        $soldBy = $readService->getSoldByAddress();
        $this->assertNotNull($soldBy);
        $this->assertEquals(1, $soldBy->id);
        $this->assertNotEmpty($soldBy->organization);
        $this->assertNotEmpty($soldBy->gstin);
    }

    /**
     * Test 3: Two-Tier Idempotency lease claim and instant response replay.
     */
    public function test_idempotency_lease_claim_and_response_replay(): void
    {
        $this->actingAs($this->superAdmin);
        $idempotencyKey = 'test-global-idemp-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'company_name' => 'JERO Architectural Test',
            'company_email' => 'test@jero.works',
            'company_phone' => '1234567890',
            'currency_id' => 1,
            'locale' => 'en',
            'hoursDays' => 'day',
            'delete_account_in' => 30,
            'first_name' => 'TestFirst',
            'last_name' => 'TestLast',
            'organization' => 'JERO Org Test',
        ];

        // 1st request: executes transaction and creates idempotency record
        $response1 = $this->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->putJson('/super-admin/global-settings/1', $payload);

        $response1->assertStatus(200);
        $response1->assertJson(['status' => 'success']);

        $idempRow = DB::table('sys_idempotency_keys')
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        $this->assertNotNull($idempRow);
        $this->assertEquals('COMPLETED', $idempRow->status);

        // 2nd request with exact same idempotency key: must replay cached response without re-executing
        $response2 = $this->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->putJson('/super-admin/global-settings/1', $payload);

        $response2->assertStatus(200);
        $data2 = $response2->json();
        $this->assertTrue($data2['_replayed'] ?? false, 'Duplicate request was not replayed from idempotency cache!');
    }

    /**
     * Test 4: Transactional Outbox event registration and post-commit fast-path Elasticsearch sync.
     */
    public function test_transactional_outbox_event_persistence_and_es_sync(): void
    {
        $this->actingAs($this->superAdmin);

        $payload = [
            'company_name' => 'JERO Outbox Verified',
            'company_email' => 'verified@jero.works',
            'company_phone' => '9876543210',
            'currency_id' => 1,
            'locale' => 'en',
            'hoursDays' => 'day',
            'delete_account_in' => 45,
            'first_name' => 'FastPath',
            'last_name' => 'Outbox',
            'organization' => 'JERO Outbox Org',
        ];

        $response = $this->putJson('/super-admin/global-settings/1', $payload);
        $response->assertStatus(200);

        $outboxId = $response->json('outbox_id') ?? $response->json('data.outbox_id');
        $this->assertNotNull($outboxId);
        $this->createdOutboxIds[] = (int) $outboxId;

        $event = DB::table('sys_outbox_events')->where('id', $outboxId)->first();
        $this->assertNotNull($event);
        $this->assertEquals('GlobalSetting', $event->aggregate_type);
        $this->assertEquals(1, $event->aggregate_id);
        $this->assertEquals('COMPLETED', $event->status);

        // Verify Elasticsearch was synchronized with new name
        $readService = app(SuperAdminGlobalSettingReadService::class);
        $freshSetting = $readService->getGlobalSettings();
        $this->assertEquals('JERO Outbox Verified', $freshSetting->company_name);
    }

    /**
     * Test 5: Atomic cache invalidation increments generation version and flushes multi-tenant keys.
     */
    public function test_atomic_cache_invalidation_increments_version(): void
    {
        $v1 = SuperAdminGlobalSettingCacheManager::getVersion();

        $this->actingAs($this->superAdmin);
        $payload = [
            'company_name' => 'JERO Cache Invalidation Test',
            'company_email' => 'cache@jero.works',
            'company_phone' => '1122334455',
            'currency_id' => 1,
            'locale' => 'en',
            'hoursDays' => 'day',
            'delete_account_in' => 15,
            'first_name' => 'Cache',
            'last_name' => 'Test',
            'organization' => 'JERO Cache Org',
        ];

        $response = $this->putJson('/super-admin/global-settings/1', $payload);
        $response->assertStatus(200);

        $outboxId = $response->json('outbox_id') ?? $response->json('data.outbox_id');
        if ($outboxId) {
            $this->createdOutboxIds[] = (int) $outboxId;
        }

        $v2 = SuperAdminGlobalSettingCacheManager::getVersion();
        $this->assertGreaterThan($v1, $v2, 'Cache generation version was not incremented upon mutation!');
    }

    /**
     * Test 6: Circuit breaker resilience gracefully falls back to MySQL when Elasticsearch is unreachable.
     */
    public function test_circuit_breaker_resilience_when_es_fails(): void
    {
        $mockElastic = $this->createMock(ElasticService::class);
        $mockElastic->method('indexExists')->willThrowException(new \Exception('Connection refused to Elasticsearch'));

        $readService = new SuperAdminGlobalSettingReadService($mockElastic);
        SuperAdminGlobalSettingCacheManager::flushCaches();

        $setting = $readService->getGlobalSettings();
        $this->assertNotNull($setting);
        $this->assertEquals(1, $setting->id);
        $this->assertNotEmpty($setting->company_name);

        $soldBy = $readService->getSoldByAddress();
        $this->assertNotNull($soldBy);
        $this->assertEquals(1, $soldBy->id);
    }
}
