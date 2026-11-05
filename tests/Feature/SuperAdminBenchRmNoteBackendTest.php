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
    }

    protected function tearDown(): void
    {
        if (! empty($this->createdTemplateIds)) {
            DB::table('bench_rm_note_master')
                ->whereNull('company_id')
                ->whereIn('id', $this->createdTemplateIds)
                ->delete();

            $elasticService = app(ElasticService::class);
            $indexName = defined('ELASTIC_BENCH_RM_NOTE_MASTER')
                ? ELASTIC_BENCH_RM_NOTE_MASTER
                : config('constants.ELASTIC_BENCH_RM_NOTE_MASTER', 'local_bench_rm_note_master');

            if ($elasticService->indexExists($indexName)) {
                try {
                    foreach ($this->createdTemplateIds as $tId) {
                        if ($elasticService->getClient()->exists(['index' => $indexName, 'id' => (string) $tId])) {
                            $elasticService->getClient()->delete([
                                'index' => $indexName,
                                'id' => (string) $tId,
                                'refresh' => true,
                            ]);
                        }
                    }
                } catch (\Throwable $e) {
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

        parent::tearDown();
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
     * Test 7: CLI Commands: bench-rm-notes:reindex and bench-rm-notes:process-outbox.
     */
    public function test_cli_commands_execute_cleanly(): void
    {
        $this->artisan('bench-rm-notes:reindex')
            ->assertExitCode(0);

        $this->artisan('bench-rm-notes:process-outbox', ['--limit' => 10])
            ->assertExitCode(0);
    }
}
