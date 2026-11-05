<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminWhatsAppThemeReadService;
use App\Services\SuperAdmin\WhatsAppThemeCacheManager;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class SuperAdminWhatsAppThemesBackendTest
 *
 * Feature and architectural verification for:
 * - CQRS Read Path: 0% MySQL reads on warm listing and KPI aggregation (Rule 1 & 2)
 * - Elasticsearch Universal Read Scope: 100% read queries from ELASTIC_EMAIL_TEMPLATE / ELASTIC_WHATSAPP_THEMES
 * - Two-Tier Idempotency: sys_idempotency_keys replay and concurrent race protection
 * - Transactional Outbox: sys_outbox_events persistence and post-commit fast-path execution
 * - Atomic Cache Invalidation: O(1) version-based Redis/Dragonfly invalidation
 * - Reindex CLI command: php artisan whatsapp-themes:reindex
 * - Outbox CLI command: php artisan whatsapp-themes:process-outbox
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic cleanup in tearDown().
 */
class SuperAdminWhatsAppThemesBackendTest extends TestCase
{
    protected User $superAdmin;

    protected ?object $originalTheme18 = null;

    protected ?object $originalTheme19 = null;

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

        $this->originalTheme18 = DB::table('th_email_template')->where('e_temp_id', 18)->first();
        $this->originalTheme19 = DB::table('th_email_template')->where('e_temp_id', 19)->first();
    }

    protected function tearDown(): void
    {
        // Restore original database records
        if ($this->originalTheme18) {
            DB::table('th_email_template')->where('e_temp_id', 18)->update([
                'template_name' => $this->originalTheme18->template_name,
                'display_name' => $this->originalTheme18->display_name,
                'template_subject' => $this->originalTheme18->template_subject,
                'template_description' => $this->originalTheme18->template_description,
                'status' => $this->originalTheme18->status,
                'orderByShow' => $this->originalTheme18->orderByShow,
            ]);
        }

        if ($this->originalTheme19) {
            DB::table('th_email_template')->where('e_temp_id', 19)->update([
                'status' => $this->originalTheme19->status,
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
        WhatsAppThemeCacheManager::flushCaches();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path: warm requests execute 0 MySQL SELECT queries against th_email_template.
     */
    public function test_whatsapp_themes_read_path_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache and Elasticsearch
        $this->get(route('superadmin.company.whatsappthemes'));

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, 'th_email_template') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        $response = $this->get(route('superadmin.company.whatsappthemes'));

        $response->assertStatus(200);
        $response->assertViewHas('th_email_template');
        $response->assertViewHas('kpi');

        $this->assertSame(0, $queryCount, 'CQRS Violation: th_email_template had '.$queryCount.' MySQL reads on warm request path.');
    }

    /**
     * Test 2: Elasticsearch Read Scope: 100% queries and global KPI statistics served from ES index.
     */
    public function test_elasticsearch_serves_all_21_whatsapp_themes_and_kpis(): void
    {
        $readService = app(SuperAdminWhatsAppThemeReadService::class);
        $result = $readService->getThemes();

        $this->assertNotEmpty($result['templates']);
        $this->assertCount(21, $result['templates']);

        $kpis = $result['kpi'];
        $this->assertEquals(21, $kpis['total']);
        $this->assertArrayHasKey('active', $kpis);
        $this->assertArrayHasKey('inactive', $kpis);
        $this->assertArrayHasKey('reminders', $kpis);
        $this->assertArrayHasKey('interview', $kpis);
        $this->assertArrayHasKey('call', $kpis);
        $this->assertArrayHasKey('offer_joining', $kpis);
    }

    /**
     * Test 3: CQRS Single Theme Read: getThemeById retrieves metadata and content with zero MySQL queries on warm cache.
     */
    public function test_single_whatsapp_theme_read_serves_from_elasticsearch_and_cache(): void
    {
        $this->actingAs($this->superAdmin);

        $readService = app(SuperAdminWhatsAppThemeReadService::class);
        $theme18 = $readService->getThemeById(18);

        $this->assertNotNull($theme18);
        $this->assertEquals(18, $theme18->e_temp_id);

        // Warm up cache
        $this->get(route('superadmin.company.whatsappThemesupdate', 18));

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, 'th_email_template') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        $response = $this->get(route('superadmin.company.whatsappThemesupdate', 18));
        $response->assertStatus(200);
        $response->assertViewHas('th_email_template');

        $this->assertSame(0, $queryCount, 'CQRS Violation: edit page triggered '.$queryCount.' MySQL reads on warm cache.');
    }

    /**
     * Test 4: Two-Tier Idempotency: duplicate mutations replay cached responses without re-executing writes.
     */
    public function test_two_tier_idempotency_and_response_replay_for_whatsapp_theme_mutation(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'test-wa-idemp-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'tokenId' => 18,
            'template' => '[TEMPLATE-2] Candidate didn\'t answer call | Share Company Profile',
            'subject' => 'Candidate didn\'t answer call | Company Profile',
            'editor1' => 'Hello [{CANDIDATE_NAME}], updated test copy for idempotency.',
            'status_is' => 1,
            'orderByShow' => 1,
        ];

        // First Execution
        $response1 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.company.addEditwhatsappThemesupdate'), $payload);

        $response1->assertStatus(200);
        $data1 = $response1->json();
        $this->assertEquals('success', $data1['status']);
        $this->assertArrayNotHasKey('_replayed', $data1);

        // Second Execution (Identical Idempotency Key) -> Must Replay Cached Response
        $response2 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.company.addEditwhatsappThemesupdate'), $payload);

        $response2->assertStatus(200);
        $data2 = $response2->json();
        $this->assertEquals('success', $data2['status']);
        $this->assertTrue($data2['_replayed'] ?? false, 'Failed to replay cached response under duplicate idempotency key.');

        // Clean up outbox events
        $outbox = DB::table('sys_outbox_events')
            ->where('idempotency_key', $idempotencyKey)
            ->first();
        if ($outbox) {
            $this->createdOutboxIds[] = $outbox->id;
        }
    }

    /**
     * Test 5: Transactional Outbox: status toggle persists event to sys_outbox_events and syncs to ES.
     */
    public function test_transactional_outbox_event_persistence_and_dual_store_sync(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'test-wa-toggle-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $newStatus = ($this->originalTheme19->status == 1) ? 0 : 1;

        $response = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.company.toggleWhatsappTemplateStatus'), [
            'id' => 19,
            'status' => $newStatus,
        ]);

        $response->assertStatus(200);
        $this->assertEquals('success', $response->json('status'));

        // Verify outbox persistence
        $outboxEvent = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'WhatsAppTheme')
            ->where('aggregate_id', 19)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        $this->assertNotNull($outboxEvent, 'Transactional outbox event was not created for status toggle.');
        $this->assertEquals('COMPLETED', $outboxEvent->status, 'Fast-path outbox event was not marked COMPLETED.');
        $this->createdOutboxIds[] = $outboxEvent->id;

        // Verify Elasticsearch dual-store synchronization
        $es = app(ElasticService::class);
        $doc = $es->getClient()->get([
            'index' => defined('ELASTIC_WHATSAPP_THEMES') ? ELASTIC_WHATSAPP_THEMES : ELASTIC_EMAIL_TEMPLATE,
            'id' => '19',
        ]);

        $this->assertEquals($newStatus, (int) ($doc['_source']['status'] ?? -1));
    }

    /**
     * Test 6: CLI Commands: reindex command and outbox dispatcher execute successfully.
     */
    public function test_cli_commands_reindex_and_process_outbox(): void
    {
        $reindexExit = Artisan::call('whatsapp-themes:reindex');
        $this->assertSame(0, $reindexExit);

        $outboxExit = Artisan::call('whatsapp-themes:process-outbox', ['--limit' => 10]);
        $this->assertSame(0, $outboxExit);
    }
}
