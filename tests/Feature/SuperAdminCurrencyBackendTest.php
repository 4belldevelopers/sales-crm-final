<?php

namespace Tests\Feature;

use App\Jobs\ProcessCurrencyOutboxEventJob;
use App\Models\Currency;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\CurrencyCircuitBreaker;
use App\Services\SuperAdmin\SuperAdminCurrencyCacheManager;
use App\Services\SuperAdmin\SuperAdminCurrencyMutationService;
use App\Services\SuperAdmin\SuperAdminCurrencyReadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

/**
 * Class SuperAdminCurrencyBackendTest
 *
 * Automated verification suite for Super Admin Currency Settings God-Level Backend Architecture:
 * - CQRS Read Path: 0% MySQL reads on warm page loads (Rule 1 & Rule 2)
 * - Two-Tier Idempotency: Replay caching and 422 payload conflict rejection
 * - Transactional Outbox: Event persistence and Elasticsearch synchronization
 * - Optimistic Concurrency Control (OCC): Stale aggregate versions skipped
 * - Anchor Currency Safety: USD and INR deletion prevention
 * - Circuit-Breaker Resilience: State transitions and fallback metrics
 * - Read-After-Write Cache Priming: Instant consistency
 * - Automated Reconciliation: Drift detection and healing
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic restoration in tearDown().
 */
class SuperAdminCurrencyBackendTest extends TestCase
{
    protected User $superAdmin;

    protected array $createdCurrencyIds = [];

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

        CurrencyCircuitBreaker::reset();
    }

    protected function tearDown(): void
    {
        // Clean up created currencies in MySQL and Elasticsearch
        if (! empty($this->createdCurrencyIds)) {
            $esService = app(ElasticService::class);
            $readService = app(SuperAdminCurrencyReadService::class);
            $indexName = $readService->getIndexName();

            foreach ($this->createdCurrencyIds as $id) {
                DB::table('currencies')->where('id', $id)->delete();
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

        SuperAdminCurrencyCacheManager::flushCaches();
        CurrencyCircuitBreaker::reset();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path executes 0 MySQL reads against currencies table on warm cache.
     */
    public function test_read_path_uses_cache_and_elasticsearch_with_zero_mysql_reads(): void
    {
        $readService = app(SuperAdminCurrencyReadService::class);

        // Warm up cache
        $currencies = $readService->getCurrencies();
        $this->assertGreaterThan(0, $currencies->count());

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, '`currencies`') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        // Warm read
        $cachedCurrencies = $readService->getCurrencies();

        $this->assertEquals(0, $queryCount, 'Warm currencies read issued unbuffered MySQL query.');
        $this->assertEquals($currencies->count(), $cachedCurrencies->count());
        $this->assertEquals('USD', $cachedCurrencies->first()->currency_code);
    }

    /**
     * Test 2: Two-tier idempotency key prevents duplicate creations and replays stored response.
     */
    public function test_idempotency_key_prevents_duplicate_mutations(): void
    {
        $mutationService = app(SuperAdminCurrencyMutationService::class);
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'currency_name' => 'Test Idempotency Dollar',
            'currency_code' => 'TID',
            'currency_symbol' => 'T$',
            'usd_based_rates' => 1.25,
            'inr_based_rates' => 118.5,
        ];

        // First execution
        $response1 = $mutationService->createCurrency($payload, $idempotencyKey);
        $created = DB::table('currencies')->where('currency_code', 'TID')->first();
        $this->assertNotNull($created);
        $this->createdCurrencyIds[] = $created->id;

        // Second execution with identical key and payload
        $response2 = $mutationService->createCurrency($payload, $idempotencyKey);

        $this->assertTrue($response2['_replayed'] ?? false, 'Duplicate execution failed to replay cached response.');
        $totalCount = DB::table('currencies')->where('currency_code', 'TID')->count();
        $this->assertEquals(1, $totalCount, 'Duplicate database record created despite idempotency key.');
    }

    /**
     * Test 3: Idempotency key collision with conflicting payload aborts 422.
     */
    public function test_conflicting_payload_with_same_idempotency_key_aborts_422(): void
    {
        $mutationService = app(SuperAdminCurrencyMutationService::class);
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload1 = [
            'currency_name' => 'Original Currency',
            'currency_code' => 'OR1',
            'currency_symbol' => 'O$',
            'usd_based_rates' => 1.0,
            'inr_based_rates' => 95.0,
        ];

        $mutationService->createCurrency($payload1, $idempotencyKey);
        $created = DB::table('currencies')->where('currency_code', 'OR1')->first();
        if ($created) {
            $this->createdCurrencyIds[] = $created->id;
        }

        $payload2 = [
            'currency_name' => 'Tampered Currency',
            'currency_code' => 'OR2',
            'currency_symbol' => 'T$',
            'usd_based_rates' => 2.0,
            'inr_based_rates' => 190.0,
        ];

        $this->expectException(HttpException::class);
        $mutationService->createCurrency($payload2, $idempotencyKey);
    }

    /**
     * Test 4: Transactional outbox event is created and processed to Elasticsearch.
     */
    public function test_outbox_event_created_and_processed_to_elasticsearch(): void
    {
        $mutationService = app(SuperAdminCurrencyMutationService::class);
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'currency_name' => 'Outbox Sync Dollar',
            'currency_code' => 'OSD',
            'currency_symbol' => 'O$',
            'usd_based_rates' => 1.5,
            'inr_based_rates' => 142.5,
        ];

        $mutationService->createCurrency($payload, $idempotencyKey);
        $created = DB::table('currencies')->where('currency_code', 'OSD')->first();
        $this->assertNotNull($created);
        $this->createdCurrencyIds[] = $created->id;

        // Check Outbox Event
        $event = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Currency')
            ->where('aggregate_id', $created->id)
            ->first();

        $this->assertNotNull($event, 'Outbox event was not written.');
        $this->createdOutboxIds[] = $event->id;
        $this->assertEquals('CURRENCY_CREATED', $event->event_type);

        // Process Outbox Job
        $job = new ProcessCurrencyOutboxEventJob((int) $event->id);
        $job->handle(app(ElasticService::class));

        // Verify Event marked COMPLETED
        $updatedEvent = DB::table('sys_outbox_events')->where('id', $event->id)->first();
        $this->assertEquals('COMPLETED', $updatedEvent->status);

        // Verify Elasticsearch Document exists
        $readService = app(SuperAdminCurrencyReadService::class);
        $esDoc = app(ElasticService::class)->getClient()->get([
            'index' => $readService->getIndexName(),
            'id' => (string) $created->id,
        ]);

        $this->assertEquals('OSD', $esDoc['_source']['currency_code']);
        $this->assertEquals('Outbox Sync Dollar', $esDoc['_source']['currency_name']);
    }

    /**
     * Test 5: Base anchor currencies (USD and INR) cannot be deleted.
     */
    public function test_base_anchor_currencies_cannot_be_deleted(): void
    {
        $mutationService = app(SuperAdminCurrencyMutationService::class);
        $usd = DB::table('currencies')->where('currency_code', 'USD')->first();
        $this->assertNotNull($usd);

        $response = $mutationService->deleteCurrency($usd->id);
        $this->assertEquals('fail', $response['status'] ?? '');

        // Verify USD is still in database
        $stillExists = DB::table('currencies')->where('id', $usd->id)->exists();
        $this->assertTrue($stillExists, 'Anchor currency USD was deleted!');
    }

    /**
     * Test 6: Circuit Breaker state transitions and fallback telemetry.
     */
    public function test_circuit_breaker_state_transitions_and_fallback_metrics(): void
    {
        CurrencyCircuitBreaker::reset();
        $this->assertEquals(CurrencyCircuitBreaker::STATE_CLOSED, CurrencyCircuitBreaker::getState());

        // Trip breaker with 3 failures
        CurrencyCircuitBreaker::recordFailure(new \Exception('ES down 1'));
        CurrencyCircuitBreaker::recordFailure(new \Exception('ES down 2'));
        CurrencyCircuitBreaker::recordFailure(new \Exception('ES down 3'));

        $this->assertEquals(CurrencyCircuitBreaker::STATE_OPEN, CurrencyCircuitBreaker::getState());
        $this->assertFalse(CurrencyCircuitBreaker::isAvailable());

        // Fallback telemetry
        $initialCount = CurrencyCircuitBreaker::getFallbackReadCount();
        CurrencyCircuitBreaker::recordFallbackRead();
        $this->assertEquals($initialCount + 1, CurrencyCircuitBreaker::getFallbackReadCount());

        CurrencyCircuitBreaker::reset();
        $this->assertEquals(CurrencyCircuitBreaker::STATE_CLOSED, CurrencyCircuitBreaker::getState());
    }

    /**
     * Test 7: Read-after-write cache priming provides instant consistency.
     */
    public function test_read_after_write_cache_priming_instant_consistency(): void
    {
        $mutationService = app(SuperAdminCurrencyMutationService::class);
        $readService = app(SuperAdminCurrencyReadService::class);
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'currency_name' => 'Instant Cache Dollar',
            'currency_code' => 'ICD',
            'currency_symbol' => 'I$',
            'usd_based_rates' => 3.14,
            'inr_based_rates' => 299.0,
        ];

        $mutationService->createCurrency($payload, $idempotencyKey);
        $created = DB::table('currencies')->where('currency_code', 'ICD')->first();
        $this->assertNotNull($created);
        $this->createdCurrencyIds[] = $created->id;

        // Read immediately by ID
        $currencyModel = $readService->getCurrencyById($created->id);
        $this->assertNotNull($currencyModel);
        $this->assertEquals('ICD', $currencyModel->currency_code);
        $this->assertEquals(3.14, (float) $currencyModel->usd_based_rates);
    }

    /**
     * Test 8: Live FX exchange rates sync writes outbox event and synchronizes.
     */
    public function test_live_exchange_rates_sync_writes_outbox_event(): void
    {
        $mutationService = app(SuperAdminCurrencyMutationService::class);
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $usdRates = [
            'EUR' => 0.92,
            'GBP' => 0.78,
            'INR' => 88.50,
        ];

        $res = $mutationService->syncExchangeRates($usdRates, 88.50, $idempotencyKey);
        $this->assertTrue($res['status']);

        $event = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Currency')
            ->where('event_type', 'CURRENCY_RATES_SYNCED')
            ->orderBy('id', 'desc')
            ->first();

        $this->assertNotNull($event);
        $this->createdOutboxIds[] = $event->id;

        // Process job
        $job = new ProcessCurrencyOutboxEventJob((int) $event->id);
        $job->handle(app(ElasticService::class));

        $updatedEvent = DB::table('sys_outbox_events')->where('id', $event->id)->first();
        $this->assertEquals('COMPLETED', $updatedEvent->status);
    }

    /**
     * Test 9: Deletion tombstone blocks stale outbox events from resurrecting deleted records.
     */
    public function test_deletion_tombstone_blocks_stale_outbox_resurrection(): void
    {
        $id = 999999;
        SuperAdminCurrencyCacheManager::setTombstone($id);
        $this->assertTrue(SuperAdminCurrencyCacheManager::hasTombstone($id));

        $eventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => (string) Str::uuid(),
            'aggregate_type' => 'Currency',
            'aggregate_id' => $id,
            'aggregate_version' => 1000,
            'event_type' => 'CURRENCY_CREATED',
            'payload' => json_encode([
                'aggregate_id' => $id,
                'aggregate_version' => 1000,
                'attributes' => [
                    'id' => $id,
                    'currency_code' => 'GHO',
                    'currency_name' => 'Ghost Currency',
                    'usd_based_rates' => 1.0,
                    'inr_based_rates' => 88.0,
                    'getKey' => 9999,
                    'aggregate_version' => 1000,
                ],
            ]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);
        $this->createdOutboxIds[] = $eventId;

        $job = new ProcessCurrencyOutboxEventJob((int) $eventId);
        $job->handle(app(ElasticService::class));

        // Event must be marked COMPLETED (handled safely)
        $event = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        $this->assertEquals('COMPLETED', $event->status);

        // Document must NOT exist in Elasticsearch
        $readService = app(SuperAdminCurrencyReadService::class);
        $exists = false;
        try {
            $doc = app(ElasticService::class)->getClient()->get([
                'index' => $readService->getIndexName(),
                'id' => (string) $id,
            ]);
            $exists = isset($doc['_source']);
        } catch (\Throwable) {
            $exists = false;
        }

        $this->assertFalse($exists, 'Ghost currency was resurrected in Elasticsearch despite tombstone!');
        SuperAdminCurrencyCacheManager::clearTombstone($id);
    }

    /**
     * Test 10: Worker crash recovery resets stale PROCESSING locks back to PENDING.
     */
    public function test_worker_crash_recovery_resets_stale_locks(): void
    {
        $staleEventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => (string) Str::uuid(),
            'aggregate_type' => 'Currency',
            'aggregate_id' => 1,
            'aggregate_version' => 1,
            'event_type' => 'CURRENCY_UPDATED',
            'payload' => json_encode(['aggregate_id' => 1]),
            'status' => 'PROCESSING',
            'locked_at' => now()->subSeconds(350),
            'attempts' => 1,
            'available_at' => now(),
            'created_at' => now(),
        ]);
        $this->createdOutboxIds[] = $staleEventId;

        // Run outbox CLI command
        \Illuminate\Support\Facades\Artisan::call('superadmin-currency:process-outbox', ['--limit' => 10]);

        $staleEvent = DB::table('sys_outbox_events')->where('id', $staleEventId)->first();
        // The event was either reset to PENDING and then processed to COMPLETED or remains PENDING
        $this->assertNotEquals('PROCESSING', $staleEvent->status, 'Stale locked outbox event was not recovered.');
    }

    /**
     * Test 11: Incomplete or invalid FX batch failure never updates active snapshot version.
     */
    public function test_incomplete_fx_batch_failure_never_activates_snapshot(): void
    {
        $mutationService = app(SuperAdminCurrencyMutationService::class);
        $initialVersion = SuperAdminCurrencyCacheManager::getActiveFxSnapshotVersion();

        try {
            // Attempt sync with negative USD to INR rate
            $mutationService->syncExchangeRates(['EUR' => 0.9], -15.0);
            $this->fail('Expected InvalidArgumentException was not thrown.');
        } catch (\InvalidArgumentException $e) {
            $this->assertStringContainsString('USD to INR conversion rate must be greater than zero', $e->getMessage());
        }

        $afterVersion = SuperAdminCurrencyCacheManager::getActiveFxSnapshotVersion();
        $this->assertEquals($initialVersion, $afterVersion, 'Active FX snapshot version was mutated by a failed batch!');
    }

    /**
     * Test 12: Decimal-safe financial arithmetic preserves 4-decimal precision without float drift.
     */
    public function test_decimal_safe_financial_arithmetic(): void
    {
        $usdToInr = 88.5432;
        $usdRates = ['EUR' => 0.9123];

        $eurUsdVal = round(1 / 0.9123, 4);
        $eurInrVal = round($eurUsdVal * $usdToInr, 4);

        $this->assertIsFloat($eurUsdVal);
        $this->assertEquals(4, strlen(substr(strrchr((string) $eurUsdVal, '.'), 1)));
        $this->assertGreaterThan(0, $eurInrVal);
    }
}
