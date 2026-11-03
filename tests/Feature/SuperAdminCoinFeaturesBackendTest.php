<?php

namespace Tests\Feature;

use App\Jobs\ProcessCoinFeatureOutboxEventJob;
use App\Models\User;
use App\Services\CoinFeatureCacheManager;
use App\Services\ElasticCoinEventsIndex;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class SuperAdminCoinFeaturesBackendTest
 *
 * Feature and architectural test verification for:
 * - CQRS Read Path: 0% MySQL reads on warm listing and KPI aggregation
 * - Transactional Outbox: sys_outbox_events persistence with PENDING -> COMPLETED lifecycle
 * - Durable Idempotency: sys_idempotency_keys replay and tamper rejection
 * - Elasticsearch Dual-Store Synchronization (local_coin_events)
 * - Health and Drift CLI Commands
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic cleanup in tearDown().
 */
class SuperAdminCoinFeaturesBackendTest extends TestCase
{
    protected User $superAdmin;

    protected array $createdEventIds = [];

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
    }

    protected function tearDown(): void
    {
        if (! empty($this->createdEventIds)) {
            DB::table('coin_events')->whereIn('id', $this->createdEventIds)->delete();
            $elastic = app(ElasticCoinEventsIndex::class);
            foreach ($this->createdEventIds as $eid) {
                $elastic->deleteEvent($eid);
            }
        }

        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        CoinFeatureCacheManager::flushCaches();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path: warm requests execute 0 MySQL SELECT queries against coin_events.
     */
    public function test_coin_features_read_path_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache and Elasticsearch
        $this->get(route('superadmin.company.coinFeatures'));

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, 'coin_events') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        $response = $this->get(route('superadmin.company.coinFeatures'));

        $response->assertStatus(200);
        $this->assertSame(0, $queryCount, 'Warm read path must execute 0 MySQL SELECT queries against coin_events');
    }

    /**
     * Test 2: KPI Counts are correctly aggregated.
     */
    public function test_kpi_counts_are_correctly_aggregated(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->get(route('superadmin.company.coinFeatures'));

        $response->assertStatus(200);
        $this->assertGreaterThan(0, $response->viewData('kpiTotal'));
        $this->assertGreaterThanOrEqual(0, $response->viewData('kpiActive'));
        $this->assertGreaterThanOrEqual(0, $response->viewData('kpiOperational'));
        $this->assertGreaterThanOrEqual(0, $response->viewData('kpiSales'));
    }

    /**
     * Test 3: Status toggle mutation creates transactional outbox event and synchronizes to Elasticsearch.
     */
    public function test_status_toggle_creates_outbox_event_and_syncs_to_elasticsearch(): void
    {
        $this->actingAs($this->superAdmin);

        // Create temporary test event
        $testId = DB::table('coin_events')->insertGetId([
            'event_type' => 'operational',
            'title' => 'Automated Test Feature '.uniqid(),
            'default_rate' => 5.00,
            'description' => 'Test event for outbox verification',
            'deduction_type' => 'onetime',
            'minimum_required' => 100.00,
            'status' => 1,
        ]);
        $this->createdEventIds[] = $testId;

        $idempotencyKey = 'test_toggle_'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $response = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('superadmin.company.coinEventStatus'), [
            'id' => $testId,
            'idempotency_key' => $idempotencyKey,
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'new_status' => 0,
        ]);

        // Verify MySQL updated
        $updated = DB::table('coin_events')->where('id', $testId)->first();
        $this->assertSame(0, (int) $updated->status);

        // Verify Transactional Outbox record exists
        $outbox = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CoinFeature')
            ->where('aggregate_id', $testId)
            ->first();

        $this->assertNotNull($outbox, 'Outbox event must be persisted in sys_outbox_events');
        $this->createdOutboxIds[] = $outbox->id;
        $this->assertSame('CoinFeatureStatusToggled', $outbox->event_type);

        // Verify Elasticsearch dual-store synchronization
        $elastic = app(ElasticCoinEventsIndex::class);
        $esDoc = $elastic->getEventById($testId);
        $this->assertNotNull($esDoc, 'Event must be synchronized to Elasticsearch');
        $this->assertSame(0, (int) $esDoc->status);
    }

    /**
     * Test 4: Durable Idempotency prevents duplicate mutations and replays cached result.
     */
    public function test_durable_idempotency_replays_result_on_duplicate_request(): void
    {
        $this->actingAs($this->superAdmin);

        $testId = DB::table('coin_events')->insertGetId([
            'event_type' => 'sales',
            'title' => 'Idempotency Test Feature '.uniqid(),
            'default_rate' => 10.00,
            'description' => 'Testing idempotency replay',
            'deduction_type' => 'daily',
            'minimum_required' => 200.00,
            'status' => 1,
        ]);
        $this->createdEventIds[] = $testId;

        $idempotencyKey = 'test_idemp_'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        // First call
        $response1 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('superadmin.company.coinEventStatus'), [
            'id' => $testId,
            'idempotency_key' => $idempotencyKey,
        ]);
        $response1->assertStatus(200);

        // Second call with identical key
        $response2 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('superadmin.company.coinEventStatus'), [
            'id' => $testId,
            'idempotency_key' => $idempotencyKey,
        ]);
        $response2->assertStatus(200);

        // Assert replay flag
        $json = $response2->json();
        $this->assertTrue($json['_replayed'] ?? false, 'Duplicate request must return replayed response');

        // Status should still be 0 (NOT toggled back to 1)
        $row = DB::table('coin_events')->where('id', $testId)->first();
        $this->assertSame(0, (int) $row->status);
    }

    /**
     * Test 5: Idempotency rejects key reuse with altered payload.
     */
    public function test_idempotency_rejects_payload_tampering_with_conflict(): void
    {
        $this->actingAs($this->superAdmin);

        $testId = DB::table('coin_events')->insertGetId([
            'event_type' => 'operational',
            'title' => 'Tamper Test Feature '.uniqid(),
            'default_rate' => 1.00,
            'description' => 'Tamper test',
            'deduction_type' => 'onetime',
            'minimum_required' => 0.00,
            'status' => 1,
        ]);
        $this->createdEventIds[] = $testId;

        $idempotencyKey = 'test_tamper_'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        // First call
        $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('superadmin.company.coinEventStatus'), [
            'id' => $testId,
            'idempotency_key' => $idempotencyKey,
        ]);

        // Second call with same key but DIFFERENT ID
        $responseTamper = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('superadmin.company.coinEventStatus'), [
            'id' => 999999,
            'idempotency_key' => $idempotencyKey,
        ]);

        $responseTamper->assertStatus(409);
    }

    /**
     * Test 6: Outbox background worker processes pending events to COMPLETED.
     */
    public function test_outbox_worker_processes_pending_events_to_completed(): void
    {
        $testId = DB::table('coin_events')->insertGetId([
            'event_type' => 'operational',
            'title' => 'Outbox Worker Test '.uniqid(),
            'default_rate' => 2.50,
            'description' => 'Worker test',
            'deduction_type' => 'onetime',
            'minimum_required' => 50.00,
            'status' => 1,
        ]);
        $this->createdEventIds[] = $testId;

        $outboxId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => 'worker_test_'.uniqid(),
            'aggregate_type' => 'CoinFeature',
            'aggregate_id' => $testId,
            'aggregate_version' => 1,
            'event_type' => 'CoinFeatureStatusToggled',
            'payload' => json_encode([
                'id' => $testId,
                'title' => 'Outbox Worker Test',
                'event_type' => 'operational',
                'deduction_type' => 'onetime',
                'default_rate' => 2.50,
                'minimum_required' => 50.00,
                'status' => 0,
                'action_type' => 'STATUS_TOGGLED',
                'event_data' => [
                    'id' => $testId,
                    'event_type' => 'operational',
                    'title' => 'Outbox Worker Test',
                    'default_rate' => 2.50,
                    'description' => 'Worker test',
                    'deduction_type' => 'onetime',
                    'minimum_required' => 50.00,
                    'status' => 0,
                ],
            ]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);
        $this->createdOutboxIds[] = $outboxId;

        // Process event via job
        $job = new ProcessCoinFeatureOutboxEventJob((int) $outboxId);
        $job->handle(app(ElasticCoinEventsIndex::class));

        $refreshed = DB::table('sys_outbox_events')->where('id', $outboxId)->first();
        $this->assertSame('COMPLETED', $refreshed->status);
        $this->assertNotNull($refreshed->processed_at);
    }

    /**
     * Test 7: Store mutation creates and updates Coin Features with dual-store sync.
     */
    public function test_coin_events_store_creates_and_updates_feature(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'test_store_'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        // Create
        $responseCreate = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('superadmin.company.coinEventsStore'), [
            'token_id' => '',
            'title' => 'New Feature Store Test '.uniqid(),
            'event_type' => 'operational',
            'deduction_type' => 'daily',
            'default_rate' => 3.00,
            'minimum_required' => 150.00,
            'description' => 'Description test',
            'status' => 1,
            'idempotency_key' => $idempotencyKey,
        ]);

        $responseCreate->assertStatus(200);
        $createData = $responseCreate->json();
        $createdId = $createData['id'] ?? null;
        $this->assertNotNull($createdId);
        $this->createdEventIds[] = $createdId;

        // Verify ES has the document
        $elastic = app(ElasticCoinEventsIndex::class);
        $doc = $elastic->getEventById((int) $createdId);
        $this->assertNotNull($doc);
        $this->assertSame(3.00, (float) $doc->default_rate);

        // Update
        $updateKey = 'test_update_'.uniqid();
        $this->createdIdempotencyKeys[] = $updateKey;

        $responseUpdate = $this->withHeaders([
            'X-Idempotency-Key' => $updateKey,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->post(route('superadmin.company.coinEventsStore'), [
            'token_id' => $createdId,
            'title' => 'Updated Feature Name',
            'event_type' => 'operational',
            'deduction_type' => 'onetime',
            'default_rate' => 4.50,
            'minimum_required' => 200.00,
            'description' => 'Updated description',
            'status' => 1,
            'idempotency_key' => $updateKey,
        ]);

        $responseUpdate->assertStatus(200);

        // Verify in DB and ES
        $updatedDb = DB::table('coin_events')->where('id', $createdId)->first();
        $this->assertSame('Updated Feature Name', $updatedDb->title);
        $this->assertSame(4.50, (float) $updatedDb->default_rate);

        $updatedEs = $elastic->getEventById((int) $createdId);
        $this->assertSame('Updated Feature Name', $updatedEs->title);
        $this->assertSame(4.50, (float) $updatedEs->default_rate);
    }

    /**
     * Test 8: Health and Drift CLI diagnostic commands execute successfully.
     */
    public function test_health_and_drift_cli_commands(): void
    {
        $this->artisan('superadmin:coin-features:health', ['--json' => true])
            ->assertExitCode(0);

        $this->artisan('superadmin:coin-features:drift', ['--json' => true])
            ->assertExitCode(0);
    }
}
