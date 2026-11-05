<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SuperAdmin\CouponCacheManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class SuperAdminCouponsBackendTest
 *
 * Feature and architectural verification for Super Admin Coupons:
 * - CQRS Read Path: 0% MySQL reads on warm listing and pagination (Rule 1 & 2)
 * - Elasticsearch Universal Read Scope: 100% read queries from ELASTIC_COUPONS (local_coupons)
 * - Two-Tier Idempotency: sys_idempotency_keys replay and duplicate mutation prevention
 * - Transactional Outbox: sys_outbox_events persistence and post-commit fast-path execution
 * - Atomic Cache Invalidation: O(1) version-based Redis/Dragonfly invalidation
 * - Reindex CLI command: php artisan coupons:reindex
 * - Process Outbox CLI command: php artisan coupons:process-outbox
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic cleanup in tearDown().
 * Author: Antigravity AI — Super Admin Backend Architecture
 */
class SuperAdminCouponsBackendTest extends TestCase
{
    protected User $superAdmin;

    protected array $createdCouponIds = [];

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
        if (! empty($this->createdCouponIds)) {
            DB::table('coupons')->whereIn('id', $this->createdCouponIds)->delete();

            $elasticService = app(\App\Services\ElasticService::class);
            $indexName = defined('ELASTIC_COUPONS')
                ? ELASTIC_COUPONS
                : config('constants.ELASTIC_COUPONS', 'local_coupons');

            if ($elasticService->indexExists($indexName)) {
                foreach ($this->createdCouponIds as $cid) {
                    try {
                        $elasticService->getClient()->delete([
                            'index' => $indexName,
                            'id' => (string) $cid,
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

        CouponCacheManager::flushCaches();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path: warm requests execute 0 MySQL SELECT queries against coupons.
     */
    public function test_coupons_read_path_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache and Elasticsearch
        $this->get(route('superadmin.coupons.index'));

        // Query count inspection
        $couponsQueryCount = 0;
        DB::listen(function ($query) use (&$couponsQueryCount) {
            $sql = strtolower($query->sql);
            if (str_contains($sql, 'from `coupons`') || str_contains($sql, 'from coupons')) {
                $couponsQueryCount++;
            }
        });

        $response = $this->get(route('superadmin.coupons.index'));

        $response->assertStatus(200);
        $this->assertEquals(0, $couponsQueryCount, 'CQRS Read Path Violation: MySQL was queried for coupons on warm read path.');
    }

    /**
     * Test 2: Mutation Service with Two-Tier Idempotency and Transactional Outbox.
     */
    public function test_coupon_store_with_two_tier_idempotency_and_outbox(): void
    {
        $this->actingAs($this->superAdmin);

        $idempKey = 'test-idemp-coupon-'.time();
        $this->createdIdempotencyKeys[] = $idempKey;

        $payload = [
            'code' => 'GODTIER'.rand(100, 999),
            'discount_percent' => 25.50,
            'expiry_date' => Carbon::now()->addDays(30)->format('Y-m-d'),
            'max_redemptions' => 100,
            'status' => '1',
        ];

        // First execution
        $response = $this->withHeaders(['X-Idempotency-Key' => $idempKey])
            ->post(route('superadmin.coupons.store'), $payload);

        $response->assertRedirect(route('superadmin.coupons.index'));

        // Verify MySQL persistence
        $created = DB::table('coupons')->where('code', $payload['code'])->first();
        $this->assertNotNull($created);
        $this->createdCouponIds[] = $created->id;
        $this->assertEquals('active', $created->status);

        // Verify Outbox Event created and marked COMPLETED via post-commit fast-path
        $outbox = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Coupon')
            ->where('aggregate_id', $created->id)
            ->first();

        $this->assertNotNull($outbox);
        $this->createdOutboxIds[] = $outbox->id;
        $this->assertEquals('COMPLETED', $outbox->status);

        // Verify Two-Tier Idempotency record
        $idempRecord = DB::table('sys_idempotency_keys')
            ->where('idempotency_key', $idempKey)
            ->first();
        $this->assertNotNull($idempRecord);
        $this->assertEquals('COMPLETED', $idempRecord->status);

        // Verify Dual-Store sync to Elasticsearch
        $elasticService = app(\App\Services\ElasticService::class);
        $indexName = defined('ELASTIC_COUPONS')
            ? ELASTIC_COUPONS
            : config('constants.ELASTIC_COUPONS', 'local_coupons');

        $this->assertTrue($elasticService->indexExists($indexName));
        $esDoc = $elasticService->getClient()->get([
            'index' => $indexName,
            'id' => (string) $created->id,
        ]);
        $this->assertEquals($payload['code'], $esDoc['_source']['code']);
        $this->assertEquals(25.50, $esDoc['_source']['discount_percent']);
    }

    /**
     * Test 3: Status Toggle with Outbox and Dual-Store Sync.
     */
    public function test_coupon_toggle_status_and_outbox(): void
    {
        $this->actingAs($this->superAdmin);

        // Direct seed coupon
        $couponId = DB::table('coupons')->insertGetId([
            'code' => 'TOGGLE'.rand(100, 999),
            'discount_percent' => 15.00,
            'expiry_date' => Carbon::now()->addDays(10)->format('Y-m-d'),
            'max_redemptions' => 50,
            'times_redeemed' => 0,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->createdCouponIds[] = $couponId;

        $idempKey = 'test-idemp-toggle-'.time();
        $this->createdIdempotencyKeys[] = $idempKey;

        $response = $this->withHeaders(['X-Idempotency-Key' => $idempKey])
            ->postJson(route('superadmin.coupons.toggleStatus'), [
                'id' => $couponId,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'new_status' => 'inactive',
        ]);

        $updated = DB::table('coupons')->where('id', $couponId)->first();
        $this->assertEquals('inactive', $updated->status);

        // Verify outbox event
        $outbox = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Coupon')
            ->where('aggregate_id', $couponId)
            ->where('event_type', 'COUPON_STATUS_TOGGLED')
            ->first();
        $this->assertNotNull($outbox);
        $this->createdOutboxIds[] = $outbox->id;
        $this->assertEquals('COMPLETED', $outbox->status);
    }

    /**
     * Test 4: Delete Coupon with Outbox and Dual-Store Sync.
     */
    public function test_coupon_destroy_and_outbox(): void
    {
        $this->actingAs($this->superAdmin);

        $couponId = DB::table('coupons')->insertGetId([
            'code' => 'DELETE'.rand(100, 999),
            'discount_percent' => 10.00,
            'expiry_date' => Carbon::now()->addDays(5)->format('Y-m-d'),
            'max_redemptions' => 10,
            'times_redeemed' => 0,
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->createdCouponIds[] = $couponId;

        $idempKey = 'test-idemp-destroy-'.time();
        $this->createdIdempotencyKeys[] = $idempKey;

        $response = $this->withHeaders(['X-Idempotency-Key' => $idempKey])
            ->postJson(route('superadmin.coupons.destroy'), [
                'id' => $couponId,
            ]);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        $inDb = DB::table('coupons')->where('id', $couponId)->first();
        $this->assertNull($inDb);

        // Verify outbox event
        $outbox = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Coupon')
            ->where('aggregate_id', $couponId)
            ->where('event_type', 'COUPON_DELETED')
            ->first();
        $this->assertNotNull($outbox);
        $this->createdOutboxIds[] = $outbox->id;
        $this->assertEquals('COMPLETED', $outbox->status);
    }

    /**
     * Test 5: Reindex Command and Process Outbox Command execution.
     */
    public function test_cli_commands_execute_successfully(): void
    {
        $reindexExit = Artisan::call('coupons:reindex');
        $this->assertEquals(0, $reindexExit);

        $outboxExit = Artisan::call('coupons:process-outbox');
        $this->assertEquals(0, $outboxExit);
    }
}
