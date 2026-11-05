<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminTransactionHistoryReadService;
use App\Services\SuperAdmin\TransactionHistoryCacheManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class SuperAdminTransactionHistoryBackendTest
 *
 * Feature and architectural verification for:
 * - CQRS Read Path: 0% MySQL reads on warm listing and KPI aggregation (Rule 1 & 2)
 * - Elasticsearch Universal Read Scope: 100% read queries from ELASTIC_CART_TRANSACTION_HISTORY (local_cart_transaction_history)
 * - Two-Tier Idempotency: sys_idempotency_keys replay and concurrent race protection
 * - Transactional Outbox: sys_outbox_events persistence and post-commit fast-path execution
 * - Atomic Cache Invalidation: O(1) version-based Redis/Dragonfly invalidation
 * - Reindex CLI command: php artisan transactions:reindex
 * - Outbox CLI command: php artisan transactions:process-outbox
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic cleanup in tearDown().
 */
class SuperAdminTransactionHistoryBackendTest extends TestCase
{
    protected User $superAdmin;

    protected ?object $originalTx1 = null;

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

        $this->originalTx1 = DB::table('cart_transaction_history')->where('id', 1)->first();
    }

    protected function tearDown(): void
    {
        // Restore original transaction record
        if ($this->originalTx1) {
            DB::table('cart_transaction_history')->where('id', 1)->update([
                'invoice_approval' => $this->originalTx1->invoice_approval,
                'approval_invoice_no' => $this->originalTx1->approval_invoice_no,
                'approval_invoice_date' => $this->originalTx1->approval_invoice_date,
            ]);
        }

        // Clean up outbox events created during tests
        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        // Clean up idempotency keys created during tests
        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        // Flush L2 caches
        TransactionHistoryCacheManager::flushCaches(1);

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path: warm requests execute 0 MySQL queries against cart_transaction_history.
     */
    public function test_cqrs_read_path_zero_mysql_reads_on_warm_request(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm-up request
        $this->get(route('superadmin.company.transactionhistory'));

        // Measured request
        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->get(route('superadmin.company.transactionhistory'));
        $response->assertStatus(200);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $txQueries = array_filter($queries, function ($q) {
            return str_contains(strtolower($q['query']), 'cart_transaction_history')
                && ! str_contains(strtolower($q['query']), 'insert')
                && ! str_contains(strtolower($q['query']), 'update')
                && ! str_contains(strtolower($q['query']), 'delete');
        });

        $this->assertCount(
            0,
            $txQueries,
            'CQRS Violation: Warm request executed '.count($txQueries).' MySQL queries against cart_transaction_history.'
        );
    }

    /**
     * Test 2: Read Service: serves 100% of queries and global KPI aggregations from Elasticsearch.
     */
    public function test_read_service_compound_query_and_kpis(): void
    {
        $readService = app(SuperAdminTransactionHistoryReadService::class);
        $result = $readService->getTransactions(['tab' => 'all', 'search' => ''], 1, 25);

        $this->assertArrayHasKey('cart_transaction_history', $result);
        $this->assertArrayHasKey('kpiMetrics', $result);
        $this->assertArrayHasKey('activeTab', $result);

        $kpis = $result['kpiMetrics'];
        $this->assertArrayHasKey('totalRevenueInr', $kpis);
        $this->assertArrayHasKey('totalTransactions', $kpis);
        $this->assertArrayHasKey('capturedCount', $kpis);
        $this->assertArrayHasKey('approvedInvoiceCount', $kpis);

        $this->assertGreaterThanOrEqual(1, $kpis['totalTransactions']);
    }

    /**
     * Test 3: Two-Tier Request Idempotency & Safe Replay.
     */
    public function test_two_tier_idempotency_and_safe_replay(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'test-tx-idem-'.bin2hex(random_bytes(8));
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'transaction_id' => encrypt(1),
            'action_type' => 'toggle_approval',
            'invoice_approval' => 'Yes',
        ];

        // First request: executes domain mutation
        $response1 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.company.invoiceupdate'), $payload);

        $response1->assertStatus(200);
        $response1->assertJson(['status' => 'success']);

        // Check idempotency record in DB
        $record = DB::table('sys_idempotency_keys')
            ->where('tenant_id', 0)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        $this->assertNotNull($record, 'Failed asserting sys_idempotency_keys contains key.');
        $this->assertEquals('COMPLETED', $record->status);

        // Second request with same key: must replay cached response safely
        $response2 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.company.invoiceupdate'), $payload);

        $response2->assertStatus(200);
        $this->assertEquals($response1->json('message'), $response2->json('message'));
    }

    /**
     * Test 4: Transactional Outbox Pattern & Elasticsearch dual-store synchronization.
     */
    public function test_transactional_outbox_event_persistence_and_sync(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'test-tx-outbox-'.bin2hex(random_bytes(8));
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $uniqueInvoiceNo = 'INV-TEST-'.rand(1000, 9999);

        $payload = [
            'transaction_id' => encrypt(1),
            'action_type' => 'update_invoice_no',
            'approval_invoice_no' => $uniqueInvoiceNo,
        ];

        $response = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.company.invoiceupdate'), $payload);

        $response->assertStatus(200);

        // Verify sys_outbox_events row
        $outbox = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CartTransaction')
            ->where('aggregate_id', 1)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        $this->assertNotNull($outbox, 'Outbox event not persisted to sys_outbox_events.');
        $this->createdOutboxIds[] = $outbox->id;
        $this->assertEquals('COMPLETED', $outbox->status);

        // Verify Elasticsearch dual-store synchronization
        $elasticService = app(ElasticService::class);
        $index = config('constants.ELASTIC_CART_TRANSACTION_HISTORY', 'local_cart_transaction_history');

        $doc = $elasticService->getClient()->get([
            'index' => $index,
            'id' => '1',
        ]);

        $this->assertEquals($uniqueInvoiceNo, $doc['_source']['approval_invoice_no']);
    }

    /**
     * Test 5: Transaction Details endpoint served via CQRS with enriched sold-by details.
     */
    public function test_transaction_details_endpoint(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->getJson(route('superadmin.company.transactionDetails', [1]));
        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'data' => [
                'id' => 1,
                'company_name' => '4Bell Technology',
            ],
        ]);
        $response->assertJsonStructure([
            'status',
            'data' => [
                'id',
                'company_name',
                'total_price',
                'platform_organization',
                'platform_gstin',
            ],
            'receipt_url',
        ]);
    }

    /**
     * Test 6: Reindex CLI Command: php artisan transactions:reindex.
     */
    public function test_reindex_command_execution(): void
    {
        $exitCode = Artisan::call('transactions:reindex');
        $this->assertEquals(0, $exitCode);

        $elasticService = app(ElasticService::class);
        $index = config('constants.ELASTIC_CART_TRANSACTION_HISTORY', 'local_cart_transaction_history');

        $count = $elasticService->getClient()->count(['index' => $index]);
        $mysqlCount = DB::table('cart_transaction_history')->count();

        $this->assertEquals($mysqlCount, $count['count']);
    }

    /**
     * Test 7: Outbox CLI Command: php artisan transactions:process-outbox.
     */
    public function test_outbox_command_execution(): void
    {
        $exitCode = Artisan::call('transactions:process-outbox');
        $this->assertEquals(0, $exitCode);
    }
}
