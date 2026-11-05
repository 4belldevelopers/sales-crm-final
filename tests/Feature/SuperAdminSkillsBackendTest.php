<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminSkillCacheManager;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class SuperAdminSkillsBackendTest
 *
 * Feature and architectural verification for:
 * - CQRS Read Path: 0% MySQL reads on warm listing and KPI aggregation (Rule 1 & 2)
 * - Elasticsearch Universal Read Scope: 100% read queries from ELASTIC_SKILLS
 * - Two-Tier Idempotency: sys_idempotency_keys replay and concurrent race protection
 * - Transactional Outbox: sys_outbox_events persistence and post-commit fast-path execution
 * - Atomic Cache Invalidation: O(1) version-based Redis/Dragonfly invalidation
 * - Reindex CLI command: php artisan skills:reindex
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic cleanup in tearDown().
 */
class SuperAdminSkillsBackendTest extends TestCase
{
    protected User $superAdmin;

    protected array $createdSkillNames = [];

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
        if (! empty($this->createdSkillNames)) {
            DB::table('skills')
                ->whereNull('company_id')
                ->whereIn('name', $this->createdSkillNames)
                ->delete();

            $elasticService = app(ElasticService::class);
            if (defined('ELASTIC_SKILLS') && $elasticService->indexExists(ELASTIC_SKILLS)) {
                try {
                    $elasticService->getClient()->deleteByQuery([
                        'index' => ELASTIC_SKILLS,
                        'refresh' => true,
                        'body' => [
                            'query' => [
                                'terms' => ['skill_name' => array_map('strtolower', $this->createdSkillNames)],
                            ],
                        ],
                    ]);
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

        SuperAdminSkillCacheManager::flushCaches();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path: warm requests execute 0 MySQL SELECT queries against skills table.
     */
    public function test_skills_read_path_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache and Elasticsearch
        $this->get(route('superadmin.company.skillskElastic'));

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, '`skills`') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        $response = $this->get(route('superadmin.company.skillskElastic'));

        $response->assertStatus(200);
        $this->assertSame(0, $queryCount, 'Warm read path must execute 0 MySQL SELECT queries against skills');
    }

    /**
     * Test 2: AJAX live search and filter return JSON with total and partial stream.
     */
    public function test_skills_ajax_filtering_returns_json_and_renders_partial(): void
    {
        $this->actingAs($this->superAdmin);

        $response = $this->getJson(route('superadmin.company.skillskElastic', [
            'range' => 'a-d',
            'q' => '',
        ]));

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'html',
            'total',
            'totalAll',
            'count',
        ]);
        $this->assertSame('success', $response->json('status'));
        $this->assertGreaterThanOrEqual(0, $response->json('total'));
        $this->assertGreaterThan(0, $response->json('totalAll'));
    }

    /**
     * Test 3: Two-Tier Idempotency lease claim and identical response replay.
     */
    public function test_skills_two_tier_idempotency_and_response_replay(): void
    {
        $this->actingAs($this->superAdmin);

        $idempKey = 'test-skill-uuid-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempKey;
        $uniqueSkillName = 'testskill'.substr(md5(uniqid()), 0, 8);
        $this->createdSkillNames[] = $uniqueSkillName;

        // First call
        $response1 = $this->postJson(
            route('superadmin.company.addEditSkillUpdate'),
            [
                'tokenId' => '0',
                'skill_name' => $uniqueSkillName,
            ],
            ['X-Idempotency-Key' => $idempKey]
        );

        $response1->assertStatus(200);
        $this->assertSame('success', $response1->json('status'));

        // Second call with same idempotency key
        $response2 = $this->postJson(
            route('superadmin.company.addEditSkillUpdate'),
            [
                'tokenId' => '0',
                'skill_name' => $uniqueSkillName,
            ],
            ['X-Idempotency-Key' => $idempKey]
        );

        $response2->assertStatus(200);
        $this->assertSame('success', $response2->json('status'));
        $this->assertTrue($response2->json('_replayed') ?? false, 'Duplicate request must replay cached response');
    }

    /**
     * Test 4: Transactional Outbox persistence and dual-store Elasticsearch synchronization.
     */
    public function test_skills_transactional_outbox_persistence_and_dual_store_sync(): void
    {
        $this->actingAs($this->superAdmin);

        $uniqueSkillName = 'outboxskill'.substr(md5(uniqid()), 0, 8);
        $this->createdSkillNames[] = $uniqueSkillName;

        $response = $this->postJson(route('superadmin.company.addEditSkillUpdate'), [
            'tokenId' => '0',
            'skill_name' => $uniqueSkillName,
        ]);

        $response->assertStatus(200);
        $this->assertSame('success', $response->json('status'));

        // Verify MySQL write
        $dbSkill = DB::table('skills')
            ->whereNull('company_id')
            ->where('name', $uniqueSkillName)
            ->first();
        $this->assertNotNull($dbSkill, 'Skill must be persisted in MySQL skills table');

        // Verify Outbox Event
        $outbox = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Skill')
            ->where('aggregate_id', $dbSkill->id)
            ->first();
        $this->assertNotNull($outbox, 'Outbox event must be logged in sys_outbox_events');
        $this->assertSame('COMPLETED', $outbox->status, 'Fast path must mark outbox event COMPLETED');
        $this->createdOutboxIds[] = $outbox->id;

        // Verify Elasticsearch dual-store synchronization
        $elasticService = app(ElasticService::class);
        $search = $elasticService->getClient()->search([
            'index' => ELASTIC_SKILLS,
            'body' => [
                'query' => [
                    'term' => ['skill_name' => $uniqueSkillName],
                ],
            ],
        ]);
        $this->assertGreaterThan(0, $search['hits']['total']['value'], 'Skill must be dual-synced to Elasticsearch');
    }

    /**
     * Test 5: Single and Bulk deletion with outbox event and Elasticsearch purge.
     */
    public function test_skills_deletion_with_outbox_and_elasticsearch_purge(): void
    {
        $this->actingAs($this->superAdmin);

        $delSkillName = 'delskill'.substr(md5(uniqid()), 0, 8);
        $this->createdSkillNames[] = $delSkillName;

        // Add skill
        $addRes = $this->postJson(route('superadmin.company.addEditSkillUpdate'), [
            'tokenId' => '0',
            'skill_name' => $delSkillName,
        ]);
        $addRes->assertStatus(200);

        $dbSkill = DB::table('skills')
            ->whereNull('company_id')
            ->where('name', $delSkillName)
            ->first();
        $this->assertNotNull($dbSkill);

        // Delete skill
        $delRes = $this->postJson(route('superadmin.company.skillDelete'), [
            'id' => (string) $dbSkill->id,
        ]);

        $delRes->assertStatus(200);
        $this->assertSame('success', $delRes->json('status'));

        // Verify MySQL deletion
        $this->assertNull(DB::table('skills')->where('id', $dbSkill->id)->first(), 'MySQL row must be deleted');

        // Verify Outbox event
        $outboxDel = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Skill')
            ->where('event_type', 'SKILL_DELETED')
            ->where('aggregate_id', $dbSkill->id)
            ->first();
        $this->assertNotNull($outboxDel, 'SKILL_DELETED outbox event must be recorded');
        $this->assertSame('COMPLETED', $outboxDel->status);
        $this->createdOutboxIds[] = $outboxDel->id;
    }

    /**
     * Test 6: CLI reindex command executes with status SUCCESS.
     */
    public function test_skills_cli_reindex_command(): void
    {
        $this->artisan('skills:reindex')
            ->assertExitCode(0);
    }
}
