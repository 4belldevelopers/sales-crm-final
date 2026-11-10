<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminBenchRmNoteCacheManager;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class SuperAdminBenchRmNoteBackendTest
 *
 * Feature and architectural verification for:
 * - CQRS Read Path: 0% MySQL reads on warm listing and single lookup (Rule 1 & 2)
 * - Two-Tier Idempotency: sys_idempotency_keys lease claim, deduplication, and cached replay
 * - Transactional Outbox: sys_outbox_events persistence and post-commit fast-path execution
 * - Multi-Tenant Atomic Cache Invalidation: O(1) version-based Redis/Dragonfly invalidation
 * - Status Toggle & Duplication: Atomic state transitions
 * - Console Commands: php artisan bench-rm-notes:reindex and bench-rm-notes:process-outbox
 * - Delete Template: Atomic MySQL removal and outbox dispatch
 * - Circuit Breaker: Fallback to database
 * - Bounded Idempotency Key: Safe SHA-256 fingerprinting for keys > 64 chars
 * - AJAX JSON response for client-side background sync
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic cleanup in tearDown().
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminBenchRmNoteBackendTest extends TestCase
{
    protected User $superAdmin;

    protected array $createdTemplateIds = [];

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

        $this->purgeTestArtifacts();
    }

    protected function tearDown(): void
    {
        $this->purgeTestArtifacts();

        parent::tearDown();
    }

    protected function purgeTestArtifacts(): void
    {
        $testIds = DB::table('bench_rm_note_master')
            ->whereNull('company_id')
            ->where(function ($q) {
                if (! empty($this->createdTemplateIds)) {
                    $q->whereIn('id', $this->createdTemplateIds);
                }
                $q->orWhere('title', 'like', 'Deployment Readiness Rubric%')
                    ->orWhere('title', 'like', 'Bench Screening Rubric%')
                    ->orWhere('title', 'like', 'Elastic Dual Store Rubric%')
                    ->orWhere('title', 'like', 'Toggle Status Rubric%')
                    ->orWhere('title', 'like', 'Master Rubric%')
                    ->orWhere('title', 'like', 'CB Bench RM Note%')
                    ->orWhere('title', 'like', 'Long Key RM Rubric%')
                    ->orWhere('title', 'like', 'Original RM Payload Title%')
                    ->orWhere('title', 'like', 'Tampered RM Title Attack%')
                    ->orWhere('title', 'like', 'Enterprise Version Test%');
            })
            ->pluck('id')
            ->toArray();

        if (! empty($testIds)) {
            DB::table('bench_rm_note_master')->whereIn('id', $testIds)->delete();
            DB::table('sys_outbox_events')
                ->where('aggregate_type', 'BenchRmNoteTemplate')
                ->whereIn('aggregate_id', $testIds)
                ->delete();

            $elasticService = app(ElasticService::class);
            $indexName = defined('ELASTIC_BENCH_RM_NOTE_MASTER')
                ? ELASTIC_BENCH_RM_NOTE_MASTER
                : config('constants.ELASTIC_BENCH_RM_NOTE_MASTER', 'local_bench_rm_note_master');

            if ($elasticService->indexExists($indexName)) {
                foreach ($testIds as $tId) {
                    try {
                        if ($elasticService->getClient()->exists(['index' => $indexName, 'id' => (string) $tId])) {
                            $elasticService->getClient()->delete([
                                'index' => $indexName,
                                'id' => (string) $tId,
                                'refresh' => true,
                            ]);
                        }
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

        SuperAdminBenchRmNoteCacheManager::flushCaches();
    }

    /**
     * Test 1: CQRS Read Path: warm requests execute 0 MySQL SELECT queries against bench_rm_note_master table.
     */
    public function test_bench_rm_note_read_path_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache
        $firstResponse = $this->get(route('superadmin.rm-note.index'));
        $firstResponse->assertStatus(200);

        // Second request should be fully served from L2 Dragonfly/Redis cache with 0 MySQL queries to bench_rm_note_master
        DB::flushQueryLog();
        DB::enableQueryLog();

        $secondResponse = $this->get(route('superadmin.rm-note.index'));
        $secondResponse->assertStatus(200);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $benchRmNoteQueries = array_filter($queries, function ($q) {
            return str_contains(strtolower($q['query']), 'bench_rm_note_master')
                && str_starts_with(strtolower(trim($q['query'])), 'select');
        });

        $this->assertCount(
            0,
            $benchRmNoteQueries,
            'CQRS Violation: Warm listing page must execute zero MySQL SELECT queries against bench_rm_note_master table.'
        );
    }

    /**
     * Test 2: Two-Tier Idempotency: Duplicate submissions return cached response with _replayed flag.
     */
    public function test_two_tier_idempotency_fences_duplicates_and_replays_cached_response(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'test-brmnote-idemp-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'title' => 'Deployment Readiness Rubric '.uniqid(),
            'content' => '<p>Candidate possesses strong distributed caching background and immediate availability.</p>',
            'status' => 1,
        ];

        // First attempt (initial execution)
        $res1 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.rm-note.store'), $payload);

        $res1->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $templateId = $res1->json('template.id');
        $this->assertNotNull($templateId);
        $this->createdTemplateIds[] = (int) $templateId;

        // Second attempt with exact same idempotency key (must be replayed)
        $res2 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.rm-note.store'), $payload);

        $res2->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('_replayed', true);

        // Ensure database did not insert a duplicate row
        $count = DB::table('bench_rm_note_master')
            ->whereNull('company_id')
            ->where('title', $payload['title'])
            ->count();

        $this->assertEquals(1, $count, 'Idempotency failure: duplicate template inserted into MySQL.');
    }

    /**
     * Test 3: Transactional Outbox: State mutations create atomic outbox events in sys_outbox_events.
     */
    public function test_mutation_creates_transactional_outbox_event(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'test-brmnote-outbox-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $title = 'Bench Screening Rubric '.uniqid();
        $payload = [
            'title' => $title,
            'content' => '<p>Evaluate talent on rate flexibility, communication proficiency, and visa authorization status.</p>',
            'status' => 1,
        ];

        $response = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.rm-note.store'), $payload);

        $response->assertStatus(200);
        $templateId = (int) $response->json('template.id');
        $this->createdTemplateIds[] = $templateId;

        // Assert outbox event was recorded
        $outboxEvent = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'BenchRmNoteTemplate')
            ->where('aggregate_id', $templateId)
            ->where('event_type', 'BENCH_RM_NOTE_TEMPLATE_CREATED')
            ->first();

        $this->assertNotNull($outboxEvent, 'Outbox event was not recorded in sys_outbox_events.');
        $this->createdOutboxIds[] = $outboxEvent->id;
        $this->assertEquals('COMPLETED', $outboxEvent->status, 'Fast-path worker should have marked outbox event COMPLETED.');
    }

    /**
     * Test 4: Dual-Store Synchronization: Created template is available in Elasticsearch index.
     */
    public function test_template_is_synchronized_to_elasticsearch(): void
    {
        $this->actingAs($this->superAdmin);

        $payload = [
            'title' => 'Elastic Dual Store Rubric '.uniqid(),
            'content' => '<p>Candidate must match commercial margins and bill rate expectations.</p>',
            'status' => 1,
        ];

        $response = $this->postJson(route('superadmin.rm-note.store'), $payload);
        $response->assertStatus(200);

        $templateId = (int) $response->json('template.id');
        $this->createdTemplateIds[] = $templateId;

        $elasticService = app(ElasticService::class);
        $indexName = defined('ELASTIC_BENCH_RM_NOTE_MASTER')
            ? ELASTIC_BENCH_RM_NOTE_MASTER
            : config('constants.ELASTIC_BENCH_RM_NOTE_MASTER', 'local_bench_rm_note_master');

        if ($elasticService->indexExists($indexName)) {
            $exists = $elasticService->getClient()->exists([
                'index' => $indexName,
                'id' => (string) $templateId,
            ]);

            $this->assertTrue($exists, "Elasticsearch document #{$templateId} was not created in index {$indexName}.");

            $doc = $elasticService->getClient()->get([
                'index' => $indexName,
                'id' => (string) $templateId,
            ]);

            $this->assertEquals($payload['title'], $doc['_source']['title']);
        }
    }

    /**
     * Test 5: 1-Click Status Toggle: Inverts status, creates outbox event, and flushes cache.
     */
    public function test_status_toggle_inverts_state_and_synchronizes_outbox(): void
    {
        $this->actingAs($this->superAdmin);

        $insertId = DB::table('bench_rm_note_master')->insertGetId([
            'company_id' => null,
            'title' => 'Toggle Status Rubric '.uniqid(),
            'content' => '<p>Initial state active.</p>',
            'status' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $this->createdTemplateIds[] = $insertId;

        $toggleResponse = $this->postJson(route('superadmin.rm-note.toggleStatus', ['id' => $insertId]));
        $toggleResponse->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('template_status', 0);

        $dbRow = DB::table('bench_rm_note_master')->where('id', $insertId)->first();
        $this->assertEquals(0, (int) $dbRow->status);

        $outboxEvent = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'BenchRmNoteTemplate')
            ->where('aggregate_id', $insertId)
            ->where('event_type', 'BENCH_RM_NOTE_TEMPLATE_STATUS_TOGGLED')
            ->first();

        $this->assertNotNull($outboxEvent);
        $this->createdOutboxIds[] = $outboxEvent->id;
    }

    /**
     * Test 6: 1-Click Duplicate: Clones template as inactive draft copy with (Copy) suffix.
     */
    public function test_duplicate_creates_inactive_draft_copy(): void
    {
        $this->actingAs($this->superAdmin);

        $sourceId = DB::table('bench_rm_note_master')->insertGetId([
            'company_id' => null,
            'title' => 'Master Rubric '.uniqid(),
            'content' => '<p>Evaluation for principal architect talent.</p>',
            'status' => 1,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);
        $this->createdTemplateIds[] = $sourceId;

        $dupResponse = $this->postJson(route('superadmin.rm-note.duplicate', ['id' => $sourceId]));
        $dupResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $clonedId = (int) $dupResponse->json('template.id');
        $this->assertGreaterThan(0, $clonedId);
        $this->createdTemplateIds[] = $clonedId;

        $clonedRow = DB::table('bench_rm_note_master')->where('id', $clonedId)->first();
        $this->assertNotNull($clonedRow);
        $this->assertEquals(0, (int) $clonedRow->status, 'Cloned template must default to Inactive status.');
        $this->assertStringContainsString('(Copy)', $clonedRow->title);
    }

    /**
     * Test 7: Delete template removes from MySQL and creates outbox event.
     */
    public function test_delete_template_removes_from_mysql_and_creates_outbox_event(): void
    {
        $this->actingAs($this->superAdmin);

        $id = DB::table('bench_rm_note_master')->insertGetId([
            'company_id' => null,
            'title' => 'Delete Rubric '.uniqid(),
            'content' => '<p>Rubric to be deleted.</p>',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->createdTemplateIds[] = $id;

        $response = $this->deleteJson(route('superadmin.rm-note.destroy', ['id' => $id]));
        $response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        // Verify removed from MySQL
        $exists = DB::table('bench_rm_note_master')->where('id', $id)->exists();
        $this->assertFalse($exists, 'Template was not deleted from MySQL bench_rm_note_master.');

        // Verify outbox event
        $outbox = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'BenchRmNoteTemplate')
            ->where('aggregate_id', $id)
            ->where('event_type', 'BENCH_RM_NOTE_TEMPLATE_DELETED')
            ->first();

        $this->assertNotNull($outbox, 'Outbox event for deletion was not created.');
        $this->createdOutboxIds[] = (int) $outbox->id;
    }

    /**
     * Test 8: CLI Commands: bench-rm-notes:reindex and bench-rm-notes:process-outbox.
     */
    public function test_cli_commands_execute_cleanly(): void
    {
        $this->artisan('bench-rm-notes:reindex')
            ->assertExitCode(0);

        $this->artisan('bench-rm-notes:process-outbox', ['--limit' => 10])
            ->assertExitCode(0);
    }

    /**
     * Test 9: Circuit breaker database fallback functions seamlessly if ES encounters exception.
     */
    public function test_bench_rm_note_circuit_breaker_database_fallback(): void
    {
        $this->actingAs($this->superAdmin);

        $cbTitle = 'CB Bench RM Note '.substr(md5(uniqid()), 0, 8);

        $now = now();
        $dbId = DB::table('bench_rm_note_master')->insertGetId([
            'company_id' => null,
            'title' => $cbTitle,
            'content' => '<p>Circuit breaker fallback test rubric content.</p>',
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->createdTemplateIds[] = $dbId;

        $readService = app(\App\Services\SuperAdmin\SuperAdminBenchRmNoteReadService::class);
        $reflection = new \ReflectionClass($readService);
        $fallbackMethod = $reflection->getMethod('fallbackDatabaseRead');
        $fallbackMethod->setAccessible(true);

        $result = $fallbackMethod->invokeArgs($readService, [
            $cbTitle, // search
            'active', // status
        ]);

        $this->assertGreaterThanOrEqual(1, $result['totalCount']);
        $this->assertSame($cbTitle, $result['hits'][0]['title']);
    }

    /**
     * Test 10: Idempotency keys exceeding 64 chars are safely clamped without throwing DB exceptions.
     */
    public function test_bench_rm_note_idempotency_key_length_safety(): void
    {
        $this->actingAs($this->superAdmin);

        $longKey = 'test-brmnote-very-long-idempotency-key-that-exceeds-sixty-four-characters-'.uniqid();
        $this->createdIdempotencyKeys[] = substr($longKey, 0, 64);
        $uniqueTitle = 'Long Key RM Rubric '.substr(md5(uniqid()), 0, 8);

        $response = $this->withHeaders([
            'X-Idempotency-Key' => $longKey,
        ])->postJson(route('superadmin.rm-note.store'), [
            'title' => $uniqueTitle,
            'content' => '<p>Content for long idempotency key test.</p>',
            'status' => 1,
        ]);

        $response->assertStatus(200);
        $this->assertSame('success', $response->json('status'));

        $templateId = (int) $response->json('template.id');
        $this->createdTemplateIds[] = $templateId;

        // Verify stored key in DB is deterministically fingerprinted to <= 64 chars
        $fingerprintedKey = 'idemp_'.substr(hash('sha256', $longKey), 0, 58);
        $this->createdIdempotencyKeys[] = $fingerprintedKey;

        $storedKey = DB::table('sys_idempotency_keys')
            ->where('tenant_id', 0)
            ->where('idempotency_key', $fingerprintedKey)
            ->first();
        $this->assertNotNull($storedKey);
        $this->assertTrue(strlen($storedKey->idempotency_key) <= 64);
        $this->assertSame('COMPLETED', $storedKey->status);
    }

    /**
     * Test 11: AJAX request to index returns structured JSON for real-time synchronization.
     */
    public function test_bench_rm_note_ajax_index_json_response(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->getJson(route('superadmin.rm-note.index'), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'data' => [
                    'templates',
                    'totalCount',
                    'activeCount',
                    'inactiveCount',
                ],
            ]);
    }

    /**
     * Test 12: Standard Super Admin Pagination: returns rendered pagination controls matching other pages.
     */
    public function test_bench_rm_note_pagination_structure_and_controls(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Initial full page load contains pagination bar and stream container
        $webResponse = $this->get(route('superadmin.rm-note.index', ['page' => 1, 'per_page' => 5]));
        $webResponse->assertStatus(200);
        $webResponse->assertSee('brmnotePaginationContainer', false);

        // 2. AJAX pagination endpoint returns JSON with rendered HTML, pagination bar, and metadata
        $ajaxResponse = $this->getJson(route('superadmin.rm-note.index', ['page' => 1, 'per_page' => 5]), [
            'X-Requested-With' => 'XMLHttpRequest',
        ]);

        $ajaxResponse->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure([
                'status',
                'html',
                'pagination',
                'total',
                'counts' => ['total', 'active', 'inactive'],
                'data' => [
                    'templates',
                    'totalCount',
                    'activeCount',
                    'inactiveCount',
                    'html',
                    'pagination',
                    'paginationData',
                ],
            ]);

        $json = $ajaxResponse->json();
        $this->assertLessThanOrEqual(5, count($json['data']['templates']));
        if ($json['total'] > 0) {
            $this->assertStringContainsString('sa-pagination-bar', $json['pagination']);
            $this->assertStringContainsString('sa-pagination-controls', $json['pagination']);
            $this->assertStringContainsString('page-link', $json['pagination']);
        }
    }
}
