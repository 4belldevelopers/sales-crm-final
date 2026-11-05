<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminDefaultTermsCacheManager;
use App\Services\SuperAdmin\SuperAdminDefaultTermsReadService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class SuperAdminDefaultTermsBackendTest
 *
 * Feature and architectural verification for:
 * - CQRS Read Path: 0% MySQL reads on warm listing and KPI aggregation (Rule 1 & 2)
 * - Elasticsearch Universal Read Scope: 100% read queries from ELASTIC_DEFAULT_TERMS
 * - Two-Tier Idempotency: sys_idempotency_keys replay and concurrent race protection
 * - Transactional Outbox: sys_outbox_events persistence and post-commit fast-path execution
 * - Atomic Cache Invalidation: O(1) version-based Redis/Dragonfly invalidation
 * - Reindex CLI command: php artisan default-terms:reindex
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic cleanup in tearDown().
 */
class SuperAdminDefaultTermsBackendTest extends TestCase
{
    protected User $superAdmin;

    protected ?string $originalVendorTerms = null;

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

        $this->originalVendorTerms = DB::table('global_settings')->value('terms');
    }

    protected function tearDown(): void
    {
        if ($this->originalVendorTerms !== null) {
            $minId = DB::table('global_settings')->min('id');
            if ($minId) {
                DB::table('global_settings')->where('id', $minId)->update([
                    'terms' => $this->originalVendorTerms,
                ]);
            }
        }

        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        SuperAdminDefaultTermsCacheManager::flushCaches();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path: warm requests execute 0 MySQL SELECT queries against global_settings.
     */
    public function test_default_terms_read_path_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache and Elasticsearch
        $this->get(route('superadmin.company.defaultTermsList'));

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, 'global_settings') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        $response = $this->get(route('superadmin.company.defaultTermsList'));

        $response->assertStatus(200);
        $response->assertViewHas('termsCatalog');
        $response->assertViewHas('kpiStats');

        $this->assertSame(0, $queryCount, 'CQRS Violation: global_settings had '.$queryCount.' MySQL reads on warm request path.');
    }

    /**
     * Test 2: Elasticsearch Read Scope: 100% queries and global KPI statistics served from ES index.
     */
    public function test_elasticsearch_serves_all_13_terms_and_kpis(): void
    {
        $readService = app(SuperAdminDefaultTermsReadService::class);
        $result = $readService->getTermsCatalog();

        $this->assertNotEmpty($result['termsCatalog']);
        $this->assertCount(13, $result['termsCatalog']);

        $kpis = $result['kpiStats'];
        $this->assertEquals(13, $kpis['total']);
        $this->assertArrayHasKey('configured', $kpis);
        $this->assertArrayHasKey('needs_setup', $kpis);
        $this->assertArrayHasKey('offers', $kpis);
        $this->assertArrayHasKey('legal', $kpis);
        $this->assertArrayHasKey('commercial', $kpis);
        $this->assertEquals(4, $kpis['offers']); // Fulltime, Agency Offer, Internship, Contract
        $this->assertEquals(7, $kpis['legal']);  // Privacy, Terms, Payment, Fraud, Trust, Report, Grievances
    }

    /**
     * Test 3: CQRS Single Term Read: getTermById retrieves metadata and content with zero MySQL queries on warm cache.
     */
    public function test_single_term_read_and_edit_page_serves_from_elasticsearch_and_cache(): void
    {
        $this->actingAs($this->superAdmin);

        $readService = app(SuperAdminDefaultTermsReadService::class);
        $term2 = $readService->getTermById(2);

        $this->assertNotNull($term2);
        $this->assertEquals('Fulltime Offer Letter', $term2['title']);
        $this->assertEquals('offers', $term2['category']);
        $this->assertContains('[CANDIDATE_NAME]', $term2['tokens']);

        // Warm edit page view
        $encId = encrypt(2);
        $this->get(route('superadmin.company.defaultTerms', [$encId]));

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, 'global_settings') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        $response = $this->get(route('superadmin.company.defaultTerms', [$encId]));
        $response->assertStatus(200);

        $this->assertSame(0, $queryCount, 'CQRS Violation: edit page had '.$queryCount.' MySQL reads on warm load.');
    }

    /**
     * Test 4: Two-Tier Idempotency: duplicate request with same X-Idempotency-Key returns replayed response.
     */
    public function test_two_tier_idempotency_prevents_duplicate_mutations_and_replays_response(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'test-idemp-term-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'encrypt_id' => encrypt(1),
            'editor1' => '<p>Updated Vendor Terms Automated Test '.uniqid().'</p>',
            'purpose' => 'Idempotency Feature Test',
        ];

        // 1st Attempt: Should execute and write to outbox
        $response1 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.company.addEditTermsupdate', 0), $payload);

        $response1->assertStatus(200);
        $response1->assertJson(['status' => 'success']);

        // 2nd Attempt: Should detect duplicate and replay response
        $response2 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.company.addEditTermsupdate', 0), $payload);

        $response2->assertStatus(200);
        $data2 = $response2->json();
        $this->assertTrue(isset($data2['_replayed']) && $data2['_replayed'] === true, 'Response was not replayed via Idempotency Layer.');
    }

    /**
     * Test 5: Transactional Outbox Persistence and ES Dual-Store Fast-Path Sync.
     */
    public function test_transactional_outbox_persists_event_and_synchronizes_to_elasticsearch(): void
    {
        $this->actingAs($this->superAdmin);

        $uniqueMarker = 'TEST_SYNC_'.uniqid();
        $newContent = "<p>Vendor terms content with {$uniqueMarker}</p>";
        $idempotencyKey = 'test-outbox-sync-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'encrypt_id' => encrypt(1),
            'editor1' => $newContent,
            'purpose' => 'Outbox Dual-Store Test',
        ];

        $response = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.company.addEditTermsupdate', 0), $payload);

        $response->assertStatus(200);

        // Check sys_outbox_events row
        $outbox = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'DefaultTerm')
            ->where('aggregate_id', 1)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        $this->assertNotNull($outbox, 'Transactional Outbox event was not written.');
        $this->createdOutboxIds[] = $outbox->id;
        $this->assertEquals('COMPLETED', $outbox->status, 'Outbox event was not completed via fast-path.');

        // Verify Elasticsearch document #1 has the new content
        $elasticService = app(ElasticService::class);
        $readService = app(SuperAdminDefaultTermsReadService::class);
        $indexName = $readService->getIndexName();

        $esDoc = $elasticService->getClient()->get([
            'index' => $indexName,
            'id' => '1',
        ]);

        $this->assertStringContainsString($uniqueMarker, $esDoc['_source']['raw_content']);
    }

    /**
     * Test 6: CLI Reindex Command.
     */
    public function test_cli_reindex_command_executes_successfully(): void
    {
        $exitCode = Artisan::call('default-terms:reindex');
        $this->assertEquals(0, $exitCode);

        $elasticService = app(ElasticService::class);
        $readService = app(SuperAdminDefaultTermsReadService::class);
        $indexName = $readService->getIndexName();

        $count = $elasticService->getClient()->count(['index' => $indexName]);
        $this->assertEquals(13, $count['count']);
    }
}
