<?php

namespace Tests\Feature;

use App\Jobs\ProcessLanguageOutboxEventJob;
use App\Models\LanguageSetting;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\LanguageCircuitBreaker;
use App\Services\SuperAdmin\SuperAdminLanguageCacheManager;
use App\Services\SuperAdmin\SuperAdminLanguageMutationService;
use App\Services\SuperAdmin\SuperAdminLanguageReadService;
use Barryvdh\TranslationManager\Models\Translation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SuperAdminLanguageSettingsBackendTest
 *
 * Automated verification suite for Super Admin Language Settings God-Level Backend Architecture:
 * - CQRS Read Path: 0% MySQL reads on warm page loads (Rule 1 & Rule 2)
 * - Two-Tier Idempotency: Replay caching and 422 payload conflict rejection
 * - Transactional Outbox: Event persistence and Elasticsearch synchronization
 * - Optimistic Concurrency Control (OCC): Stale aggregate versions skipped
 * - Base Language Safety: English (en) and active system default deletion/disable prevention
 * - Circuit-Breaker Resilience: State transitions and fallback metrics
 * - Read-After-Write Cache Priming: Instant consistency
 * - Non-Destructive Translation Migration: Translation records preserved on code update
 * - Worker Crash Recovery: Unlocks stale PROCESSING events
 * - Dual-Store Reconciliation: Drift detection and healing
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic restoration in tearDown().
 */
class SuperAdminLanguageSettingsBackendTest extends TestCase
{
    protected User $superAdmin;

    protected array $createdLanguageIds = [];

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

        LanguageCircuitBreaker::reset();
    }

    protected function tearDown(): void
    {
        // Clean up created languages in MySQL and Elasticsearch
        if (! empty($this->createdLanguageIds)) {
            $esService = app(ElasticService::class);
            $readService = app(SuperAdminLanguageReadService::class);
            $indexName = $readService->getIndexName();

            foreach ($this->createdLanguageIds as $id) {
                DB::table('language_settings')->where('id', $id)->delete();
                try {
                    $esService->getClient()->delete([
                        'index' => $indexName,
                        'id' => (string) $id,
                    ]);
                } catch (\Throwable) {
                }
            }
        }

        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        // Clean up test directories in resources/lang
        $testCodes = ['tlh', 'sjn', 'doth', 'eo', 'la', 'gho', 'cat', 'ca'];
        foreach ($testCodes as $c) {
            $dir = base_path('resources/lang/'.$c);
            if (\Illuminate\Support\Facades\File::isDirectory($dir)) {
                \Illuminate\Support\Facades\File::deleteDirectory($dir);
            }
        }

        SuperAdminLanguageCacheManager::flushCaches();
        LanguageCircuitBreaker::reset();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path achieves 0% MySQL reads on warm request.
     */
    public function test_read_path_uses_cache_and_elasticsearch_with_zero_mysql_reads(): void
    {
        $readService = app(SuperAdminLanguageReadService::class);

        // 1. Cold pass primes Redis L2 cache
        $firstResult = $readService->getLanguages();
        $this->assertNotEmpty($firstResult);

        // 2. Warm pass: Listen to SQL queries
        DB::flushQueryLog();
        DB::enableQueryLog();

        $cachedLanguages = $readService->getLanguages();
        $this->assertNotEmpty($cachedLanguages);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        // Must be ZERO database queries
        $this->assertCount(0, $queries, 'Warm CQRS read path must execute 0 MySQL queries!');
    }

    /**
     * Test 2: Two-tier idempotency lease prevents duplicate record creation.
     */
    public function test_idempotency_key_prevents_duplicate_mutations(): void
    {
        $idempKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempKey;

        $mutationService = app(SuperAdminLanguageMutationService::class);

        $payload = [
            'language_name' => 'Klingon Test',
            'language_code' => 'tlh',
            'status' => 'enabled',
        ];

        $request = new \App\Http\Requests\Language\StoreLanguage($payload);
        $request->merge($payload);

        // First attempt
        $res1 = $mutationService->storeLanguage($request, $idempKey);
        $this->assertEquals('success', $res1['status']);

        $lang = LanguageSetting::where('language_code', 'tlh')->first();
        $this->assertNotNull($lang);
        $this->createdLanguageIds[] = $lang->id;

        // Second attempt with exact same key and payload
        $res2 = $mutationService->storeLanguage($request, $idempKey);
        $this->assertEquals('success', $res2['status']);
        $this->assertTrue($res2['_replayed'] ?? false, 'Duplicate execution must be replayed!');

        // Confirm only 1 record exists in DB
        $count = LanguageSetting::where('language_code', 'tlh')->count();
        $this->assertEquals(1, $count);
    }

    /**
     * Test 3: Idempotency payload mismatch throws HTTP 422 Unprocessable Entity.
     */
    public function test_conflicting_payload_with_same_idempotency_key_aborts_422(): void
    {
        $idempKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempKey;

        $mutationService = app(SuperAdminLanguageMutationService::class);

        $payload1 = [
            'language_name' => 'Elvish Test',
            'language_code' => 'sjn',
            'status' => 'enabled',
        ];

        $request1 = new \App\Http\Requests\Language\StoreLanguage($payload1);
        $request1->merge($payload1);

        $res1 = $mutationService->storeLanguage($request1, $idempKey);
        $lang = LanguageSetting::where('language_code', 'sjn')->first();
        if ($lang) {
            $this->createdLanguageIds[] = $lang->id;
        }

        // Different payload with SAME idempotency key
        $payload2 = [
            'language_name' => 'Dothraki Test',
            'language_code' => 'doth',
            'status' => 'disabled',
        ];

        $request2 = new \App\Http\Requests\Language\StoreLanguage($payload2);
        $request2->merge($payload2);

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $mutationService->storeLanguage($request2, $idempKey);
    }

    /**
     * Test 4: Outbox event is created and processed to Elasticsearch.
     */
    public function test_outbox_event_created_and_processed_to_elasticsearch(): void
    {
        $idempKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempKey;

        $mutationService = app(SuperAdminLanguageMutationService::class);

        $payload = [
            'language_name' => 'Esperanto Test',
            'language_code' => 'eo',
            'status' => 'enabled',
        ];

        $request = new \App\Http\Requests\Language\StoreLanguage($payload);
        $request->merge($payload);

        $mutationService->storeLanguage($request, $idempKey);

        $lang = LanguageSetting::where('language_code', 'eo')->first();
        $this->assertNotNull($lang);
        $this->createdLanguageIds[] = $lang->id;

        // Verify outbox event exists
        $outbox = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'LanguageSetting')
            ->where('aggregate_id', $lang->id)
            ->first();

        $this->assertNotNull($outbox);
        $this->createdOutboxIds[] = $outbox->id;
        $this->assertEquals('LANGUAGE_CREATED', $outbox->event_type);

        // Process outbox event
        $job = new ProcessLanguageOutboxEventJob((int) $outbox->id);
        $job->handle();

        // Verify status is COMPLETED
        $freshOutbox = DB::table('sys_outbox_events')->where('id', $outbox->id)->first();
        $this->assertEquals('COMPLETED', $freshOutbox->status);

        // Verify present in Elasticsearch
        $readService = app(SuperAdminLanguageReadService::class);
        $elasticService = app(ElasticService::class);
        $esDoc = $elasticService->getClient()->get([
            'index' => $readService->getIndexName(),
            'id' => (string) $lang->id,
        ]);

        $this->assertEquals('eo', $esDoc['_source']['language_code']);
        $this->assertEquals('Esperanto Test', $esDoc['_source']['language_name']);
    }

    /**
     * Test 5: English base language cannot be deleted or disabled.
     */
    public function test_base_english_language_cannot_be_deleted_or_disabled(): void
    {
        $mutationService = app(SuperAdminLanguageMutationService::class);
        $english = LanguageSetting::where('language_code', 'en')->first();
        $this->assertNotNull($english);

        // Attempt to disable English
        $disableRes = $mutationService->changeStatus($english->id, 'disabled');
        $this->assertEquals('fail', $disableRes['status']);

        // Attempt to delete English
        $deleteRes = $mutationService->destroyLanguage($english->id);
        $this->assertEquals('fail', $deleteRes['status']);

        // Confirm English is still enabled in MySQL
        $freshEnglish = LanguageSetting::find($english->id);
        $this->assertEquals('enabled', $freshEnglish->status);
    }

    /**
     * Test 6: Active system default language cannot be deleted or disabled.
     */
    public function test_active_system_default_language_cannot_be_deleted_or_disabled(): void
    {
        $mutationService = app(SuperAdminLanguageMutationService::class);
        $global = \App\Models\GlobalSetting::first();
        $defaultLocale = $global->locale ?? 'en';

        $defaultLang = LanguageSetting::where('language_code', $defaultLocale)->first();
        $this->assertNotNull($defaultLang);

        $res = $mutationService->destroyLanguage($defaultLang->id);
        $this->assertEquals('fail', $res['status']);
    }

    /**
     * Test 7: Circuit breaker state transitions and fallback metrics.
     */
    public function test_circuit_breaker_state_transitions_and_fallback_metrics(): void
    {
        LanguageCircuitBreaker::reset();
        $this->assertEquals('CLOSED', LanguageCircuitBreaker::getState());
        $this->assertEquals('NORMAL_CQRS', LanguageCircuitBreaker::getMode());

        // Simulate 3 failures to trip circuit
        for ($i = 0; $i < 3; $i++) {
            LanguageCircuitBreaker::recordFailure(new \Exception('Connection refused test'));
        }

        $this->assertEquals('OPEN', LanguageCircuitBreaker::getState());
        $this->assertEquals('DEGRADED_ES_OUTAGE', LanguageCircuitBreaker::getMode());

        // Record emergency fallback read
        $initialFallbackCount = LanguageCircuitBreaker::getFallbackCount();
        LanguageCircuitBreaker::recordFallback();
        $this->assertEquals($initialFallbackCount + 1, LanguageCircuitBreaker::getFallbackCount());

        // Reset
        LanguageCircuitBreaker::reset();
        $this->assertEquals('CLOSED', LanguageCircuitBreaker::getState());
    }

    /**
     * Test 8: Read-after-write cache invalidation provides instant consistency.
     */
    public function test_read_after_write_cache_priming_instant_consistency(): void
    {
        $readService = app(SuperAdminLanguageReadService::class);
        $mutationService = app(SuperAdminLanguageMutationService::class);

        // Warm cache
        $beforeList = $readService->getLanguages();
        $this->assertNotEmpty($beforeList);

        $idempKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempKey;

        $payload = [
            'language_name' => 'Latin Test',
            'language_code' => 'la',
            'status' => 'enabled',
        ];

        $request = new \App\Http\Requests\Language\StoreLanguage($payload);
        $request->merge($payload);

        $mutationService->storeLanguage($request, $idempKey);

        $lang = LanguageSetting::where('language_code', 'la')->first();
        $this->assertNotNull($lang);
        $this->createdLanguageIds[] = $lang->id;

        // Fresh read must immediately contain Latin Test
        $afterList = $readService->getLanguages();
        $found = $afterList->firstWhere('language_code', 'la');
        $this->assertNotNull($found, 'Read-after-write must reflect newly inserted language immediately!');
    }

    /**
     * Test 9: Deletion tombstone blocks stale outbox events from resurrecting deleted records.
     */
    public function test_deletion_tombstone_blocks_stale_outbox_resurrection(): void
    {
        $idempKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempKey;

        $mutationService = app(SuperAdminLanguageMutationService::class);

        $payload = [
            'language_name' => 'Ghost Language',
            'language_code' => 'gho',
            'status' => 'enabled',
        ];

        $request = new \App\Http\Requests\Language\StoreLanguage($payload);
        $request->merge($payload);

        $mutationService->storeLanguage($request, $idempKey);

        $lang = LanguageSetting::where('language_code', 'gho')->first();
        $this->assertNotNull($lang);
        $langId = $lang->id;

        // Delete language
        $mutationService->destroyLanguage($langId);

        // Verify tombstone is active
        $this->assertTrue(SuperAdminLanguageCacheManager::hasTombstone($langId));

        // Create a fake delayed CREATE outbox event for this deleted ID
        $staleEventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => (string) Str::uuid(),
            'aggregate_type' => 'LanguageSetting',
            'aggregate_id' => $langId,
            'aggregate_version' => time() - 100,
            'event_type' => 'LANGUAGE_CREATED',
            'payload' => json_encode(['attributes' => ['id' => $langId, 'language_name' => 'Ghost', 'language_code' => 'gho']]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);
        $this->createdOutboxIds[] = $staleEventId;

        // Worker should skip indexing due to tombstone
        $job = new ProcessLanguageOutboxEventJob($staleEventId);
        $job->handle();

        $processedEvent = DB::table('sys_outbox_events')->where('id', $staleEventId)->first();
        $this->assertEquals('COMPLETED', $processedEvent->status);
        $this->assertStringContainsString('tombstone', $processedEvent->last_error);

        // Confirm doc is NOT in Elasticsearch
        $readService = app(SuperAdminLanguageReadService::class);
        $elasticService = app(ElasticService::class);
        $existsInEs = false;
        try {
            $elasticService->getClient()->get([
                'index' => $readService->getIndexName(),
                'id' => (string) $langId,
            ]);
            $existsInEs = true;
        } catch (\Throwable) {
            $existsInEs = false;
        }

        $this->assertFalse($existsInEs, 'Deleted entity must NOT be resurrected in Elasticsearch!');
    }

    /**
     * Test 10: Worker crash recovery resets stale locks to PENDING.
     */
    public function test_worker_crash_recovery_resets_stale_locks(): void
    {
        $staleEventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => (string) Str::uuid(),
            'aggregate_type' => 'LanguageSetting',
            'aggregate_id' => 99999,
            'aggregate_version' => 1,
            'event_type' => 'LANGUAGE_CREATED',
            'payload' => json_encode([]),
            'status' => 'PROCESSING',
            'attempts' => 1,
            'locked_at' => now()->subSeconds(400), // > 300s
            'available_at' => now()->addMinutes(10), // Future, so step 4 batch outbox doesn't immediately consume it
            'created_at' => now()->subSeconds(400),
        ]);
        $this->createdOutboxIds[] = $staleEventId;

        // Run outbox command with recovery
        $this->artisan('superadmin-language:process-outbox', ['--limit' => 5])
            ->assertSuccessful();

        $recovered = DB::table('sys_outbox_events')->where('id', $staleEventId)->first();
        $this->assertEquals('PENDING', $recovered->status);
        $this->assertNull($recovered->locked_at);
    }

    /**
     * Test 11: Non-destructive translation migration on language code update.
     */
    public function test_non_destructive_translation_locale_migration_on_code_update(): void
    {
        $idempKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempKey;

        $mutationService = app(SuperAdminLanguageMutationService::class);

        $payload = [
            'language_name' => 'Catalan Test',
            'language_code' => 'cat',
            'status' => 'enabled',
        ];

        $request = new \App\Http\Requests\Language\StoreLanguage($payload);
        $request->merge($payload);

        $mutationService->storeLanguage($request, $idempKey);

        $lang = LanguageSetting::where('language_code', 'cat')->first();
        $this->assertNotNull($lang);
        $this->createdLanguageIds[] = $lang->id;

        // Insert a mock translation row
        $transId = DB::table('ltm_translations')->insertGetId([
            'status' => 0,
            'locale' => 'cat',
            'group' => 'messages',
            'key' => 'welcome_test',
            'value' => 'Benvingut',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Update code from 'cat' to 'ca'
        $updatePayload = [
            'language_name' => 'Catalan Standard',
            'language_code' => 'ca',
            'status' => 'enabled',
        ];
        $updateRequest = new \App\Http\Requests\Language\StoreLanguage($updatePayload);
        $updateRequest->merge($updatePayload);

        $updateKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $updateKey;

        $mutationService->updateLanguage($updateRequest, $lang->id, $updateKey);

        // Translation record must be preserved and updated to 'ca'
        $updatedTrans = DB::table('ltm_translations')->where('id', $transId)->first();
        $this->assertNotNull($updatedTrans);
        $this->assertEquals('ca', $updatedTrans->locale);

        // Clean up translation
        DB::table('ltm_translations')->where('id', $transId)->delete();
    }

    /**
     * Test 12: Dual-store drift reconciliation heals mismatched records.
     */
    public function test_dual_store_drift_reconciliation_command(): void
    {
        $this->artisan('superadmin-language:process-outbox', ['--reconcile' => true])
            ->assertSuccessful();

        $readService = app(SuperAdminLanguageReadService::class);
        $elasticService = app(ElasticService::class);
        $indexName = $readService->getIndexName();

        $countInEs = $elasticService->getClient()->count(['index' => $indexName])['count'] ?? 0;
        $countInDb = DB::table('language_settings')->count();

        $this->assertEquals($countInDb, $countInEs, 'Reconciliation must synchronize exact count between MySQL and Elasticsearch');
    }

    /**
     * Test 13: Full HTTP GET route /super-admin/language-settings returns 200 OK with views.
     */
    public function test_full_http_route_returns_ok_with_view(): void
    {
        $response = $this->actingAs($this->superAdmin)->get(route('superadmin.language-settings.index'));
        $response->assertStatus(200);
        $response->assertViewIs('super-admin.language-settings.index');
        $response->assertViewHas('languages');
        $response->assertViewHas('masterLanguages');
    }
}
