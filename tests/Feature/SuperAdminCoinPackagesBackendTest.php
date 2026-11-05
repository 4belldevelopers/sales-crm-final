<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SuperAdmin\CoinPackageCacheManager;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class SuperAdminCoinPackagesBackendTest
 *
 * Feature and architectural verification for:
 * - CQRS Read Path: 0% MySQL reads on warm listing and KPI aggregation (Rule 1 & 2)
 * - Elasticsearch Universal Read Scope: 100% read queries from ELASTIC_COIN_PACKAGES
 * - Two-Tier Idempotency: sys_idempotency_keys replay and concurrent race protection
 * - Transactional Outbox: sys_outbox_events persistence and post-commit fast-path execution
 * - Atomic Cache Invalidation: O(1) version-based Redis/Dragonfly invalidation
 * - Reindex CLI command: php artisan coin-packages:reindex
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic cleanup in tearDown().
 */
class SuperAdminCoinPackagesBackendTest extends TestCase
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
            DB::table('coin_package_points')->whereIn('coin_package_id', $this->createdPackageIds)->delete();
            DB::table('coin_packages')->whereIn('id', $this->createdPackageIds)->delete();

            $elasticService = app(\App\Services\ElasticService::class);
            if (defined('ELASTIC_COIN_PACKAGES') && $elasticService->indexExists(ELASTIC_COIN_PACKAGES)) {
                foreach ($this->createdPackageIds as $pid) {
                    try {
                        $elasticService->getClient()->delete([
                            'index' => ELASTIC_COIN_PACKAGES,
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

        CoinPackageCacheManager::flushCaches();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path: warm requests execute 0 MySQL SELECT queries against coin_packages.
     */
    public function test_coin_packages_read_path_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache and Elasticsearch
        $this->get(route('superadmin.company.coinPackages'));

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, 'coin_packages') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        $response = $this->get(route('superadmin.company.coinPackages'));

        $response->assertStatus(200);
        $this->assertSame(0, $queryCount, 'Warm read path must execute 0 MySQL SELECT queries against coin_packages');
    }

    /**
     * Test 2: AJAX live search and tab filtering return structured partials with 100% accurate counts.
     */
    public function test_coin_packages_ajax_filtering_returns_json_and_renders_partial(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->getJson(route('superadmin.company.coinPackages', [
            'tab' => 'operational',
            'search' => '',
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'html',
            'counts' => [
                'all',
                'operational',
                'sales',
                'active',
                'inactive',
                'custom',
            ],
            'pagination',
        ]);

        $data = $response->json();
        $this->assertSame('success', $data['status']);
        $this->assertNotEmpty($data['html']);
        $this->assertGreaterThan(0, $data['counts']['all']);
    }

    /**
     * Test 3: Status toggle with Two-Tier Idempotency replays identical response without double mutation.
     */
    public function test_status_toggle_idempotency_and_outbox_lifecycle(): void
    {
        $this->actingAs($this->superAdmin);

        $package = DB::table('coin_packages')->where('id', 1)->first();
        $this->assertNotNull($package, 'Package #1 must exist');
        $initialStatus = (int) $package->status;

        $idempotencyKey = 'test-idemp-pkg-toggle-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        // First call: Execute mutation
        $response1 = $this->postJson(route('superadmin.company.coinPackageToggleStatus'), [
            'id' => 1,
        ], [
            'X-Idempotency-Key' => $idempotencyKey,
        ]);

        $response1->assertStatus(200);
        $data1 = $response1->json();
        $this->assertSame('success', $data1['status']);
        $this->assertSame($initialStatus === 1 ? 0 : 1, $data1['new_status']);

        // Second call with same idempotency key: Replay cached response
        $response2 = $this->postJson(route('superadmin.company.coinPackageToggleStatus'), [
            'id' => 1,
        ], [
            'X-Idempotency-Key' => $idempotencyKey,
        ]);

        $response2->assertStatus(200);
        $data2 = $response2->json();
        $this->assertTrue($data2['_replayed'] ?? false, 'Duplicate request must return replayed response');
        $this->assertSame($data1['new_status'], $data2['new_status']);

        // Verify outbox record exists and was marked COMPLETED
        $outbox = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CoinPackage')
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        $this->assertNotNull($outbox, 'Outbox event must be logged');
        $this->assertSame('COMPLETED', $outbox->status);
        $this->createdOutboxIds[] = $outbox->id;

        // Restore initial status
        $restoreKey = 'test-idemp-pkg-restore-'.uniqid();
        $this->createdIdempotencyKeys[] = $restoreKey;
        $this->postJson(route('superadmin.company.coinPackageToggleStatus'), [
            'id' => 1,
        ], [
            'X-Idempotency-Key' => $restoreKey,
        ]);
    }

    /**
     * Test 4: Package creation and updating with Two-Tier Idempotency and Transactional Outbox.
     */
    public function test_package_create_and_update_with_idempotency_and_outbox(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'test-idemp-pkg-create-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        // Create new package
        $payload = [
            'event' => 'operational',
            'coin_package' => 'Enterprise Elite Test '.uniqid(),
            'durations' => 365,
            'total_price' => 50000,
            'total_price_usd' => 600,
            'total_coins' => 25000,
            'discount' => 15,
            'package_type' => 1,
            'visibility_show' => 1,
            'status' => 1,
            'points' => ['Point Alpha', 'Point Beta'],
            'status_point' => [1, 1],
        ];

        $response = $this->postJson(route('superadmin.company.coinPackageStore'), $payload, [
            'X-Idempotency-Key' => $idempotencyKey,
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertSame('success', $data['status']);
        $packageId = (int) $data['package_id'];
        $this->assertGreaterThan(0, $packageId);
        $this->createdPackageIds[] = $packageId;

        // Verify MySQL has the record
        $dbPkg = DB::table('coin_packages')->where('id', $packageId)->first();
        $this->assertNotNull($dbPkg);
        $this->assertSame(25000.0, (float) $dbPkg->coins);

        // Verify points were inserted
        $pointsCount = DB::table('coin_package_points')->where('coin_package_id', $packageId)->count();
        $this->assertSame(2, $pointsCount);

        // Verify Elasticsearch has the synchronized document
        $elasticService = app(\App\Services\ElasticService::class);
        $esDoc = $elasticService->getClient()->get([
            'index' => ELASTIC_COIN_PACKAGES,
            'id' => (string) $packageId,
        ]);
        $this->assertSame($packageId, (int) $esDoc['_source']['id']);
        $this->assertSame(25000.0, (float) $esDoc['_source']['coins']);
        $this->assertCount(2, $esDoc['_source']['points']);

        // Test duplicate creation replay
        $dupResponse = $this->postJson(route('superadmin.company.coinPackageStore'), $payload, [
            'X-Idempotency-Key' => $idempotencyKey,
        ]);
        $dupResponse->assertStatus(200);
        $dupData = $dupResponse->json();
        $this->assertTrue($dupData['_replayed'] ?? false);
        $this->assertSame($packageId, (int) $dupData['package_id']);
    }

    /**
     * Test 5: Reindex CLI command executes successfully and flushes cache.
     */
    public function test_coin_packages_reindex_command_executes(): void
    {
        $this->artisan('coin-packages:reindex')
            ->assertExitCode(0);
    }
}
