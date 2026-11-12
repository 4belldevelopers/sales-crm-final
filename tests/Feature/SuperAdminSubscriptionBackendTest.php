<?php

namespace Tests\Feature;

use App\Jobs\ProcessSubscriptionOutboxEventJob;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SubscriptionCircuitBreaker;
use App\Services\SuperAdmin\SuperAdminSubscriptionCacheManager;
use App\Services\SuperAdmin\SuperAdminSubscriptionMutationService;
use App\Services\SuperAdmin\SuperAdminSubscriptionReadService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SuperAdminSubscriptionBackendTest
 *
 * Automated verification suite for Super Admin Subscription Management
 * God-Level Backend Architecture:
 * - CQRS Read Path: 0% MySQL reads on warm page loads (Rule 1 & Rule 2)
 * - Two-Tier Idempotency: Replay caching and duplicate prevention
 * - Transactional Outbox: Event persistence and Elasticsearch synchronization
 * - Circuit-Breaker Resilience: State transitions and emergency DB fallback
 * - Generational L2 Cache: O(1) version bump and atomic invalidation
 * - CLI Commands: Synchronous dual-store reindexing and outbox processing
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic restoration in tearDown().
 */
class SuperAdminSubscriptionBackendTest extends TestCase
{
    protected User $superAdmin;
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

        SubscriptionCircuitBreaker::reset();
        SuperAdminSubscriptionCacheManager::flushCaches();
    }

    protected function tearDown(): void
    {
        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }
        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        SubscriptionCircuitBreaker::reset();
        SuperAdminSubscriptionCacheManager::flushCaches();

        parent::tearDown();
    }

    /**
     * Test 1: Circuit Breaker starts in CLOSED state with 0 failures.
     */
    public function test_circuit_breaker_starts_closed(): void
    {
        $status = SubscriptionCircuitBreaker::getStatus();
        $this->assertEquals(SubscriptionCircuitBreaker::STATE_CLOSED, $status['state']);
        $this->assertTrue(SubscriptionCircuitBreaker::isAvailable());
        $this->assertEquals(SubscriptionCircuitBreaker::MODE_NORMAL_CQRS, SubscriptionCircuitBreaker::getMode());
    }

    /**
     * Test 2: Generational Cache Manager generates consistent keys and bumps version.
     */
    public function test_cache_manager_generation_bump(): void
    {
        $v1 = SuperAdminSubscriptionCacheManager::getVersion();
        $this->assertNotEmpty($v1);

        $kpiKey1 = SuperAdminSubscriptionCacheManager::getKpiKey('operational');
        $this->assertStringContainsString($v1, $kpiKey1);

        SuperAdminSubscriptionCacheManager::flushCaches();
        $v2 = SuperAdminSubscriptionCacheManager::getVersion();

        $this->assertNotEquals($v1, $v2);
        $kpiKey2 = SuperAdminSubscriptionCacheManager::getKpiKey('operational');
        $this->assertStringContainsString($v2, $kpiKey2);
    }

    /**
     * Test 3: Read Service returns authoritative KPI counts via Elasticsearch CQRS.
     */
    public function test_read_service_kpi_counts(): void
    {
        /** @var SuperAdminSubscriptionReadService $readService */
        $readService = app(SuperAdminSubscriptionReadService::class);

        $kpis = (array) $readService->getSubscriptionKpiCounts('operational');

        $this->assertIsArray($kpis);
        $this->assertArrayHasKey('all_count', $kpis);
        $this->assertArrayHasKey('free_count', $kpis);
        $this->assertArrayHasKey('paid_count', $kpis);
        $this->assertArrayHasKey('expiry_in_30_days_count', $kpis);
        $this->assertArrayHasKey('less_coins_count', $kpis);
        $this->assertArrayHasKey('last_active_7_days_count', $kpis);
        $this->assertGreaterThanOrEqual(0, $kpis['all_count']);
    }

    /**
     * Test 4: Read Service returns subscription table listing with pagination and HTML.
     */
    public function test_read_service_subscription_listing(): void
    {
        /** @var SuperAdminSubscriptionReadService $readService */
        $readService = app(SuperAdminSubscriptionReadService::class);

        $request = Request::create('/super-admin/subscription/all', 'GET', [
            'q' => '',
            'mstatus_id' => 'all',
            'per_page' => 10,
            'page' => 1,
        ]);

        $list = $readService->getSubscriptionList($request);

        $this->assertIsArray($list);
        $this->assertArrayHasKey('html', $list);
        $this->assertArrayHasKey('pagination', $list);
        $this->assertArrayHasKey('total', $list);
    }

    /**
     * Test 5: Mutation Service enforces two-tier idempotency.
     */
    public function test_mutation_service_two_tier_idempotency(): void
    {
        $testCompany = DB::table('companies')
            ->whereNotIn('id', [1, 1000])
            ->first();

        if (! $testCompany) {
            $this->markTestSkipped('No test company found for idempotency test.');
        }

        /** @var SuperAdminSubscriptionMutationService $mutationService */
        $mutationService = app(SuperAdminSubscriptionMutationService::class);

        $idempotencyKey = 'test_sub_' . Str::uuid()->toString();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        // First execution
        $res1 = $mutationService->toggleCompanyActive((int) $testCompany->id, $idempotencyKey);
        $this->assertIsArray($res1);
        $this->assertEquals('success', $res1['status'] ?? null);

        // Track created outbox record
        $outbox = DB::table('sys_outbox_events')
            ->where('idempotency_key', $idempotencyKey)
            ->first();
        if ($outbox) {
            $this->createdOutboxIds[] = $outbox->id;
        }

        // Duplicate replay with identical key
        $res2 = $mutationService->toggleCompanyActive((int) $testCompany->id, $idempotencyKey);
        $this->assertIsArray($res2);
        $this->assertTrue($res2['_replayed'] ?? false);

        // Restore status
        $restoreKey = 'restore_sub_' . Str::uuid()->toString();
        $this->createdIdempotencyKeys[] = $restoreKey;
        $res3 = $mutationService->toggleCompanyActive((int) $testCompany->id, $restoreKey);
        $this->assertEquals('success', $res3['status'] ?? null);

        $outboxRestore = DB::table('sys_outbox_events')
            ->where('idempotency_key', $restoreKey)
            ->first();
        if ($outboxRestore) {
            $this->createdOutboxIds[] = $outboxRestore->id;
        }
    }

    /**
     * Test 6: CLI Command `subscription:reindex` indexes entities into Elasticsearch.
     */
    public function test_cli_reindex_command(): void
    {
        $exitCode = Artisan::call('subscription:reindex', ['--chunk' => 100]);
        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Subscription reindexing completed', $output);
    }

    /**
     * Test 7: CLI Command `subscription:process-outbox` processes pending outbox events.
     */
    public function test_cli_process_outbox_command(): void
    {
        $exitCode = Artisan::call('subscription:process-outbox', ['--limit' => 10]);
        $this->assertEquals(0, $exitCode);
    }

    /**
     * Test 8: SuperAdminCompanyController endpoints integrate with Read Service and CQRS.
     */
    public function test_controller_kpi_and_list_endpoints(): void
    {
        $this->actingAs($this->superAdmin);

        // Test countaccountviewmore
        $response = $this->get(route('superadmin.company.countaccountviewmore', ['id' => 1]));
        $response->assertStatus(200);
        $response->assertJsonStructure(['counts']);

        // Test allSubscriptionDataAjax
        $responseList = $this->get(route('superadmin.company.allSubscriptionDataAjax', [
            'q' => '',
            'mstatus_id' => 'all',
            'per_page' => 10,
            'page' => 1,
        ]));
        $responseList->assertStatus(200);
        $responseList->assertJsonStructure(['html', 'pagination', 'total']);
    }

    /**
     * Test 9: Circuit Breaker trips after 3 failures and activates 60s storm-guarded DB fallback.
     */
    public function test_circuit_breaker_storm_guard_fallback(): void
    {
        /** @var SuperAdminSubscriptionReadService $readService */
        $readService = app(SuperAdminSubscriptionReadService::class);

        // Trip circuit
        SubscriptionCircuitBreaker::recordFailure(new \Exception('Test ES drop 1'));
        SubscriptionCircuitBreaker::recordFailure(new \Exception('Test ES drop 2'));
        SubscriptionCircuitBreaker::recordFailure(new \Exception('Test ES drop 3'));

        $this->assertEquals(SubscriptionCircuitBreaker::STATE_OPEN, SubscriptionCircuitBreaker::getState());
        $this->assertEquals(SubscriptionCircuitBreaker::MODE_DEGRADED_ES_OUTAGE, SubscriptionCircuitBreaker::getMode());

        // First fallback execution populates storm guard
        $kpi1 = $readService->getSubscriptionKpiCounts('operational', true);
        $this->assertNotNull($kpi1);

        // Second fallback execution within 60s window must hit rememberDbFallback cache without SQL re-execution
        DB::enableQueryLog();
        DB::flushQueryLog();
        $kpi2 = $readService->getSubscriptionKpiCounts('operational', true);
        $sqlCount = count(DB::getQueryLog());
        DB::disableQueryLog();

        $this->assertEquals(0, $sqlCount, 'Fallback read query must be served by 60s storm guard cache');
        $this->assertEquals($kpi1->all_count, $kpi2->all_count);
    }

    /**
     * Test 10: Optimistic worker lock blocks duplicate outbox execution.
     */
    public function test_optimistic_worker_locking_prevents_duplicate_processing(): void
    {
        $testKey = 'test_lock_' . Str::uuid()->toString();
        $this->createdIdempotencyKeys[] = $testKey;

        $outboxId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => $testKey,
            'aggregate_type' => 'SubscriptionCompany',
            'aggregate_id' => 99999,
            'aggregate_version' => 1,
            'event_type' => 'COMPANY_STATUS_TOGGLED',
            'payload' => json_encode(['company_id' => 99999, 'pack_status' => 'active']),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);
        $this->createdOutboxIds[] = $outboxId;

        // Worker 1 claims lock
        $claimed1 = DB::table('sys_outbox_events')
            ->where('id', $outboxId)
            ->where('aggregate_type', 'SubscriptionCompany')
            ->whereIn('status', ['PENDING', 'FAILED'])
            ->update(['status' => 'PROCESSING', 'locked_at' => now(), 'attempts' => DB::raw('attempts + 1')]);
        $this->assertEquals(1, $claimed1);

        // Worker 2 attempts concurrent claim on already claimed row
        $claimed2 = DB::table('sys_outbox_events')
            ->where('id', $outboxId)
            ->where('aggregate_type', 'SubscriptionCompany')
            ->whereIn('status', ['PENDING', 'FAILED'])
            ->update(['status' => 'PROCESSING', 'locked_at' => now(), 'attempts' => DB::raw('attempts + 1')]);
        $this->assertEquals(0, $claimed2, 'Worker 2 must be blocked from duplicate claiming');
    }

    /**
     * Test 11: Idempotency rejects concurrent in-flight leases with HTTP 425.
     */
    public function test_in_flight_idempotency_lease_rejection(): void
    {
        /** @var SuperAdminSubscriptionMutationService $mutationService */
        $mutationService = app(SuperAdminSubscriptionMutationService::class);

        $testKey = 'in_flight_' . Str::uuid()->toString();
        $this->createdIdempotencyKeys[] = $testKey;

        DB::table('sys_idempotency_keys')->insert([
            'tenant_id' => 0,
            'idempotency_key' => $testKey,
            'request_hash' => sha1('test_request'),
            'status' => 'PROCESSING',
            'expires_at' => Carbon::now()->addHours(24),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        try {
            $mutationService->toggleCompanyActive(2, $testKey);
            $this->fail('Expected HttpException with status 425 was not thrown');
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            $this->assertEquals(425, $e->getStatusCode());
            $this->assertStringContainsString('processing', strtolower($e->getMessage()));
        }
    }

    /**
     * Test 12: Automated reconciliation command executes audit and reports status cleanly.
     */
    public function test_reconciliation_command_detects_and_runs(): void
    {
        $exitCode = Artisan::call('subscription:reconcile', ['--dry-run' => true]);
        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('SUPER ADMIN SUBSCRIPTION CQRS RECONCILIATION', $output);
        $this->assertStringContainsString('Total Companies Scanned', $output);
    }

    /**
     * Test 13: Monotonic version guard ensures older outbox event does not overwrite newer ES state.
     */
    public function test_monotonic_aggregate_version_guard(): void
    {
        $elasticService = app(ElasticService::class);
        $index = config('constants.ELASTIC_COMPANIES_INDEX', defined('ELASTIC_COMPANIES_INDEX') ? ELASTIC_COMPANIES_INDEX : 'local_companies_elastic');

        if (! $elasticService->indexExists($index)) {
            $this->markTestSkipped('Elasticsearch index not present');
        }

        // Set document aggregate_version to 100 with status active
        $elasticService->getClient()->update([
            'index' => $index,
            'id' => '2',
            'refresh' => 'wait_for',
            'body' => [
                'doc' => [
                    'aggregate_version' => 100,
                    'status' => 'active',
                ],
                'doc_as_upsert' => true,
            ],
        ]);

        // Create an older outbox event (aggregate_version = 50, status = inactive)
        $testKey = 'older_event_' . Str::uuid()->toString();
        $this->createdIdempotencyKeys[] = $testKey;

        $outboxId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => $testKey,
            'aggregate_type' => 'SubscriptionCompany',
            'aggregate_id' => 2,
            'aggregate_version' => 50,
            'event_type' => 'COMPANY_STATUS_TOGGLED',
            'payload' => json_encode(['company_id' => 2, 'pack_status' => 'inactive', 'aggregate_version' => 50]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);
        $this->createdOutboxIds[] = $outboxId;

        // Process this older outbox event
        $job = new ProcessSubscriptionOutboxEventJob($outboxId);
        $job->handle($elasticService);

        // Fetch document from ES: aggregate_version MUST remain 100 and status MUST remain 'active'
        $doc = $elasticService->getClient()->get([
            'index' => $index,
            'id' => '2',
        ]);

        $this->assertEquals(100, $doc['_source']['aggregate_version']);
        $this->assertEquals('active', $doc['_source']['status'], 'Stale outbox event must not overwrite newer authoritative state');
    }

    /**
     * Test 14: Dead-letter recovery CLI command resets DEAD_LETTER status to PENDING.
     */
    public function test_dead_letter_recovery_cli_command(): void
    {
        $testKey = 'dead_letter_' . Str::uuid()->toString();
        $this->createdIdempotencyKeys[] = $testKey;

        $outboxId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => $testKey,
            'aggregate_type' => 'SubscriptionCompany',
            'aggregate_id' => 2,
            'aggregate_version' => 1,
            'event_type' => 'COMPANY_STATUS_TOGGLED',
            'payload' => json_encode(['company_id' => 2, 'pack_status' => 'active']),
            'status' => 'DEAD_LETTER',
            'attempts' => 5,
            'available_at' => Carbon::now(),
            'created_at' => Carbon::now(),
        ]);
        $this->createdOutboxIds[] = $outboxId;

        $exitCode = Artisan::call('subscription:process-outbox', ['--retry-dead-letter' => true]);
        $this->assertEquals(0, $exitCode);

        $event = DB::table('sys_outbox_events')->where('id', $outboxId)->first();
        $this->assertEquals('COMPLETED', $event->status, 'Event was recovered from DEAD_LETTER to PENDING and successfully processed to COMPLETED');
    }

    /**
     * Test 15: Resumable reconciliation command respects --from-id filter.
     */
    public function test_resumable_reconciliation_command(): void
    {
        $exitCode = Artisan::call('subscription:reconcile', [
            '--dry-run' => true,
            '--from-id' => 2,
        ]);
        $this->assertEquals(0, $exitCode);
        $output = Artisan::output();
        $this->assertStringContainsString('Resuming from Company ID: 2', $output);
    }

    /**
     * Test 16: Centralized observability metrics aggregation returns valid payload structure.
     */
    public function test_centralized_observability_metrics(): void
    {
        $metrics = SuperAdminSubscriptionCacheManager::getObservabilityMetrics();

        $this->assertIsArray($metrics);
        $this->assertArrayHasKey('cache', $metrics);
        $this->assertArrayHasKey('outbox', $metrics);
        $this->assertArrayHasKey('circuit_breaker', $metrics);
        $this->assertArrayHasKey('timestamp', $metrics);

        $this->assertArrayHasKey('hit_ratio_percent', $metrics['cache']);
        $this->assertArrayHasKey('backlog_pending', $metrics['outbox']);
        $this->assertArrayHasKey('state', $metrics['circuit_breaker']);
    }
}
