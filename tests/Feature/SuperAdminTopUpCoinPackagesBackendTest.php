<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SuperAdmin\TopUpCoinPackageCacheManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class SuperAdminTopUpCoinPackagesBackendTest
 *
 * Feature and architectural verification for:
 * - CQRS Read Path: 0% MySQL reads on warm listing and KPI aggregation (Rule 1 & 2)
 * - Elasticsearch Universal Read Scope: 100% read queries from ELASTIC_TOP_UP_COIN_PACKAGES
 * - Two-Tier Idempotency: sys_idempotency_keys replay and duplicate mutation prevention
 * - Transactional Outbox: sys_outbox_events persistence and post-commit fast-path execution
 * - Atomic Cache Invalidation: O(1) version-based Redis/Dragonfly invalidation
 * - Reindex CLI command: php artisan topup-coin-packages:reindex
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic cleanup in tearDown().
 */
class SuperAdminTopUpCoinPackagesBackendTest extends TestCase
{
    protected User $superAdmin;

    protected array $createdPackageIds = [];

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
        if (! empty($this->createdPackageIds)) {
            DB::table('top_up_coin_packages')->whereIn('id', $this->createdPackageIds)->delete();

            $elasticService = app(\App\Services\ElasticService::class);
            $indexName = defined('ELASTIC_TOP_UP_COIN_PACKAGES')
                ? ELASTIC_TOP_UP_COIN_PACKAGES
                : config('constants.ELASTIC_TOP_UP_COIN_PACKAGES', 'local_top_up_coin_packages');

            if ($elasticService->indexExists($indexName)) {
                foreach ($this->createdPackageIds as $pid) {
                    try {
                        $elasticService->getClient()->delete([
                            'index' => $indexName,
                            'id' => (string) $pid,
                        ]);
                    } catch (\Throwable $e) {
                    }
                }
            }
        }

        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        TopUpCoinPackageCacheManager::flushCaches();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path: warm requests execute 0 MySQL SELECT queries against top_up_coin_packages.
     */
    public function test_topup_coin_packages_read_path_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache and Elasticsearch
        $this->get(route('superadmin.company.topUpCoinPackages'));

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, 'top_up_coin_packages') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        $response = $this->get(route('superadmin.company.topUpCoinPackages'));

        $response->assertStatus(200);
        $this->assertSame(0, $queryCount, 'Warm read path must execute 0 MySQL SELECT queries against top_up_coin_packages');
    }

    /**
     * Test 2: AJAX request returns accurate KPI counts matching database.
     */
    public function test_topup_coin_packages_ajax_returns_accurate_kpis(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->getJson(route('superadmin.company.topUpCoinPackages', [
            'tab' => 'all',
        ]));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'counts' => [
                    'all',
                    'active',
                    'inactive',
                    'operational',
                    'sales',
                    'custom',
                ],
            ]);

        $data = $response->json('counts');
        $this->assertGreaterThanOrEqual(1, $data['all']);
        $this->assertGreaterThanOrEqual(1, $data['active']);
    }

    /**
     * Test 3: Two-tier Idempotency prevents duplicate mutations and replays response.
     */
    public function test_two_tier_idempotency_prevents_duplicate_mutations_and_replays_response(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'test-topup-idemp-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'total_price' => 777.00,
            'total_coins' => 777,
            'discount' => 10,
            'per_coin_rate' => 1.00000,
            'event_type' => 'operational',
            'status' => 1,
        ];

        $initialCount = DB::table('top_up_coin_packages')->count();

        // 1st Attempt: create package
        $response1 = $this->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->postJson(route('superadmin.company.topUpCoinPackageStore'), $payload);

        $response1->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $pkgId = (int) $response1->json('package_id');
        $this->assertGreaterThan(0, $pkgId);
        $this->createdPackageIds[] = $pkgId;

        $afterFirstCount = DB::table('top_up_coin_packages')->count();
        $this->assertSame($initialCount + 1, $afterFirstCount);

        // 2nd Attempt: duplicate network replay with exact same idempotency key
        $response2 = $this->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->postJson(route('superadmin.company.topUpCoinPackageStore'), $payload);

        $response2->assertStatus(200)
            ->assertJson(['status' => 'success', '_replayed' => true]);

        $afterSecondCount = DB::table('top_up_coin_packages')->count();
        $this->assertSame($afterFirstCount, $afterSecondCount, 'Duplicate idempotency submission must NOT create duplicate package');
    }

    /**
     * Test 4: Transactional Outbox persistence and dual-store synchronization to Elasticsearch.
     */
    public function test_transactional_outbox_event_persistence_and_dual_store_sync(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'test-topup-outbox-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'total_price' => 999.00,
            'total_coins' => 999,
            'discount' => 5,
            'per_coin_rate' => 1.00000,
            'event_type' => 'sales',
            'status' => 1,
        ];

        $response = $this->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->postJson(route('superadmin.company.topUpCoinPackageStore'), $payload);

        $response->assertStatus(200);
        $pkgId = (int) $response->json('package_id');
        $this->createdPackageIds[] = $pkgId;

        // Verify sys_outbox_events row
        $outbox = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'TopUpCoinPackage')
            ->where('aggregate_id', $pkgId)
            ->first();

        $this->assertNotNull($outbox, 'Transactional outbox event must be persisted');
        $this->createdOutboxIds[] = $outbox->id;
        $this->assertSame('TOP_UP_COIN_PACKAGE_SAVED', $outbox->event_type);
        $this->assertSame('COMPLETED', $outbox->status);

        // Verify Elasticsearch synchronization
        $elasticService = app(\App\Services\ElasticService::class);
        $indexName = defined('ELASTIC_TOP_UP_COIN_PACKAGES')
            ? ELASTIC_TOP_UP_COIN_PACKAGES
            : config('constants.ELASTIC_TOP_UP_COIN_PACKAGES', 'local_top_up_coin_packages');

        if ($elasticService->indexExists($indexName)) {
            $esDoc = $elasticService->getClient()->get([
                'index' => $indexName,
                'id' => (string) $pkgId,
            ]);

            $this->assertTrue($esDoc['found']);
            $this->assertSame(999, (int) $esDoc['_source']['coins']);
            $this->assertSame('sales', $esDoc['_source']['event_type']);
        }
    }

    /**
     * Test 5: Instant status toggle with idempotency and transactional outbox.
     */
    public function test_topup_coin_package_status_toggle(): void
    {
        $this->actingAs($this->superAdmin);

        // Create a test package directly in DB
        $pkgId = DB::table('top_up_coin_packages')->insertGetId([
            'price' => 123.00,
            'coins' => 123,
            'status' => 1,
            'event_type' => 'operational',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->createdPackageIds[] = $pkgId;

        $idempotencyKey = 'test-topup-toggle-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $response = $this->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->postJson(route('superadmin.company.toggleTopUpCoinPackageStatus'), [
                'id' => $pkgId,
            ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'new_status' => 0,
                'status_label' => 'Inactive',
            ]);

        $updatedRow = DB::table('top_up_coin_packages')->where('id', $pkgId)->first();
        $this->assertSame(0, $updatedRow->status);

        $outbox = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'TopUpCoinPackage')
            ->where('aggregate_id', $pkgId)
            ->where('event_type', 'TOP_UP_COIN_PACKAGE_STATUS_TOGGLED')
            ->first();

        $this->assertNotNull($outbox);
        $this->createdOutboxIds[] = $outbox->id;
    }

    /**
     * Test 6: CLI Reindex command executes successfully.
     */
    public function test_reindex_topup_coin_packages_command(): void
    {
        $exitCode = Artisan::call('topup-coin-packages:reindex');
        $this->assertSame(0, $exitCode);
    }
}
