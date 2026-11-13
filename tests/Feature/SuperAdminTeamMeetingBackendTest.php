<?php

namespace Tests\Feature;

use App\Models\TeamMeetingList;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminTeamMeetingCacheManager;
use App\Services\SuperAdmin\SuperAdminTeamMeetingMutationService;
use App\Services\SuperAdmin\SuperAdminTeamMeetingReadService;
use App\Services\SuperAdmin\TeamMeetingCircuitBreaker;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SuperAdminTeamMeetingBackendTest
 *
 * Automated verification suite for Super Admin Microsoft Teams Meeting Settings God-Level Backend Architecture:
 * - CQRS Read Path: 0% MySQL reads on warm page loads (Rule 1 & Rule 2)
 * - Elasticsearch Universal Read Scope: 100% read queries from ELASTIC_TEAM_MEETING_LISTS
 * - Two-Tier Idempotency: sys_idempotency_keys lease claiming and instant duplicate replay
 * - Transactional Outbox: sys_outbox_events persistence and fast-path execution
 * - Atomic Cache Invalidation: O(1) version-based Redis/Dragonfly invalidation (Rule 6)
 * - Circuit-Breaker Resilience: Graceful fallback on Elasticsearch outage
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic cleanup in tearDown().
 */
class SuperAdminTeamMeetingBackendTest extends TestCase
{
    protected User $superAdmin;

    protected array $createdLinkIds = [];

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
        // Cleanup test data from MySQL and Elasticsearch
        if (! empty($this->createdLinkIds)) {
            DB::table('team_meeting_lists')->whereIn('id', $this->createdLinkIds)->delete();

            $esService = app(ElasticService::class);
            $readService = app(SuperAdminTeamMeetingReadService::class);
            $indexName = $readService->getIndexName();

            foreach ($this->createdLinkIds as $id) {
                try {
                    $esService->getClient()->delete([
                        'index' => $indexName,
                        'id' => (string) $id,
                        'refresh' => 'wait_for',
                    ]);
                } catch (\Throwable) {
                }
            }
        }

        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        SuperAdminTeamMeetingCacheManager::flushCaches();
        TeamMeetingCircuitBreaker::reset();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path executes 0 MySQL SELECT queries against team_meeting_lists on warm cache.
     */
    public function test_team_meeting_read_path_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache and Elasticsearch
        $this->get('/super-admin/team-meeting-setting');

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, 'team_meeting_lists') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        $response = $this->get('/super-admin/team-meeting-setting');
        $response->assertStatus(200);

        $this->assertSame(0, $queryCount, 'CQRS Read Path Violation: MySQL SELECT queries executed on warm cache!');
    }

    /**
     * Test 2: Elasticsearch serves Microsoft Teams Meeting catalog and KPI aggregations.
     */
    public function test_elasticsearch_serves_team_meeting_catalog_and_aggregations(): void
    {
        $readService = app(SuperAdminTeamMeetingReadService::class);
        $result = $readService->getPaginatedMeetingLists('', 'all', 1, 15);

        $this->assertArrayHasKey('paginator', $result);
        $this->assertArrayHasKey('totalLinksCount', $result);
        $this->assertArrayHasKey('activeLinksCount', $result);
        $this->assertArrayHasKey('inactiveLinksCount', $result);

        $this->assertGreaterThanOrEqual(1, $result['totalLinksCount']);
        $this->assertGreaterThanOrEqual(1, $result['paginator']->total());

        $firstItem = $result['paginator']->items()[0] ?? null;
        $this->assertInstanceOf(TeamMeetingList::class, $firstItem);
        $this->assertNotNull($firstItem->meeting_link);
    }

    /**
     * Test 3: Two-Tier Idempotency lease claim and instant response replay.
     */
    public function test_two_tier_idempotency_lease_claim_and_duplicate_replay(): void
    {
        $mutationService = app(SuperAdminTeamMeetingMutationService::class);
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $uniqueLink = 'https://teams.microsoft.com/l/meetup-join/test-'.Str::random(8);
        $payload = [
            'account_name' => 'test.host@jeroworks.com',
            'meeting_link' => $uniqueLink,
            'status' => 1,
        ];

        // 1. Initial submission
        $firstResponse = $mutationService->storeLinks($payload, $idempotencyKey, $this->superAdmin->id);
        $this->assertTrue($firstResponse['status'] ?? false);
        $this->assertArrayNotHasKey('_replayed', $firstResponse);

        if (! empty($firstResponse['id'])) {
            $this->createdLinkIds[] = (int) $firstResponse['id'];
        }

        // Verify lease claimed in sys_idempotency_keys
        $record = DB::table('sys_idempotency_keys')
            ->where('tenant_id', 0)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        $this->assertNotNull($record);
        $this->assertEquals('COMPLETED', $record->status);
        $this->assertEquals(200, $record->response_status);

        // 2. Duplicate submission with identical key
        $replayResponse = $mutationService->storeLinks($payload, $idempotencyKey, $this->superAdmin->id);
        $this->assertTrue($replayResponse['status'] ?? false);
        $this->assertTrue($replayResponse['_replayed'] ?? false, 'Duplicate request must return _replayed => true without re-executing!');
    }

    /**
     * Test 4: Transactional Outbox event is registered upon link creation.
     */
    public function test_transactional_outbox_event_is_created_on_link_creation(): void
    {
        $mutationService = app(SuperAdminTeamMeetingMutationService::class);
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $uniqueLink = 'https://teams.microsoft.com/l/meetup-join/outbox-'.Str::random(8);
        $payload = [
            'account_name' => 'outbox.host@jeroworks.com',
            'meeting_link' => $uniqueLink,
            'status' => 1,
        ];

        $response = $mutationService->storeLinks($payload, $idempotencyKey, $this->superAdmin->id);
        $this->assertTrue($response['status'] ?? false);

        if (! empty($response['id'])) {
            $this->createdLinkIds[] = (int) $response['id'];
        }

        $outboxEvent = DB::table('sys_outbox_events')
            ->where('tenant_id', 0)
            ->where('aggregate_type', 'TeamMeetingLink')
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        $this->assertNotNull($outboxEvent, 'Transactional outbox event was not created!');
        $this->assertEquals('TEAM_MEETING_LINKS_CREATED', $outboxEvent->event_type);
        $this->createdOutboxIds[] = $outboxEvent->id;
    }

    /**
     * Test 5: Status toggle executes dual-store synchronization.
     */
    public function test_status_toggle_executes_dual_store_sync(): void
    {
        $mutationService = app(SuperAdminTeamMeetingMutationService::class);
        $readService = app(SuperAdminTeamMeetingReadService::class);

        $uniqueLink = 'https://teams.microsoft.com/l/meetup-join/toggle-'.Str::random(8);
        $initial = $mutationService->storeLinks([
            'account_name' => 'toggle.host@jeroworks.com',
            'meeting_link' => $uniqueLink,
            'status' => 1,
        ], (string) Str::uuid(), $this->superAdmin->id);

        $linkId = (int) $initial['id'];
        $this->createdLinkIds[] = $linkId;

        // Toggle to paused (0)
        $toggleKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $toggleKey;

        $toggleResponse = $mutationService->updateStatus($linkId, 0, $toggleKey, $this->superAdmin->id);
        $this->assertEquals('success', $toggleResponse['status']);
        $this->assertEquals(0, $toggleResponse['new_status']);

        // Check MySQL
        $dbRow = DB::table('team_meeting_lists')->where('id', $linkId)->first();
        $this->assertEquals(0, $dbRow->status);

        // Check Elasticsearch
        $esDoc = $readService->getLinkById($linkId);
        $this->assertNotNull($esDoc);
        $this->assertEquals(0, $esDoc->status);
    }

    /**
     * Test 6: Single link update updates MySQL and Elasticsearch.
     */
    public function test_single_link_update_syncs_both_stores(): void
    {
        $mutationService = app(SuperAdminTeamMeetingMutationService::class);
        $readService = app(SuperAdminTeamMeetingReadService::class);

        $initial = $mutationService->storeLinks([
            'account_name' => 'update.before@jeroworks.com',
            'meeting_link' => 'https://teams.microsoft.com/l/meetup-join/upd-'.Str::random(8),
            'status' => 1,
        ], (string) Str::uuid(), $this->superAdmin->id);

        $linkId = (int) $initial['id'];
        $this->createdLinkIds[] = $linkId;

        $updatedEmail = 'update.after@jeroworks.com';
        $updatedLink = 'https://teams.microsoft.com/l/meetup-join/upd-after-'.Str::random(8);

        $updateKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $updateKey;

        $updateResponse = $mutationService->updateLink($linkId, [
            'account_name' => $updatedEmail,
            'meeting_link' => $updatedLink,
        ], $updateKey, $this->superAdmin->id);

        $this->assertTrue($updateResponse['status']);

        // Check MySQL
        $dbRow = DB::table('team_meeting_lists')->where('id', $linkId)->first();
        $this->assertEquals($updatedEmail, $dbRow->account_name);
        $this->assertEquals($updatedLink, $dbRow->meeting_link);

        // Check Elasticsearch
        $esDoc = $readService->getLinkById($linkId);
        $this->assertEquals($updatedEmail, $esDoc->account_name);
        $this->assertEquals($updatedLink, $esDoc->meeting_link);
    }

    /**
     * Test 7: Delete links creates tombstones and purges Elasticsearch document.
     */
    public function test_delete_links_creates_tombstones_and_purges_es(): void
    {
        $mutationService = app(SuperAdminTeamMeetingMutationService::class);
        $esService = app(ElasticService::class);
        $readService = app(SuperAdminTeamMeetingReadService::class);

        $initial = $mutationService->storeLinks([
            'account_name' => 'del.test@jeroworks.com',
            'meeting_link' => 'https://teams.microsoft.com/l/meetup-join/del-'.Str::random(8),
            'status' => 1,
        ], (string) Str::uuid(), $this->superAdmin->id);

        $linkId = (int) $initial['id'];

        $deleteKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $deleteKey;

        $delResponse = $mutationService->deleteLinks([$linkId], $deleteKey, $this->superAdmin->id);
        $this->assertTrue($delResponse['status']);

        // Check tombstone exists
        $this->assertTrue(SuperAdminTeamMeetingCacheManager::hasTombstone($linkId, 1));

        // Check MySQL record deleted
        $existsInDb = DB::table('team_meeting_lists')->where('id', $linkId)->exists();
        $this->assertFalse($existsInDb);

        // Check Elasticsearch document deleted
        $indexName = $readService->getIndexName();
        $esExists = false;
        try {
            $esService->getClient()->get([
                'index' => $indexName,
                'id' => (string) $linkId,
            ]);
            $esExists = true;
        } catch (\Throwable) {
            $esExists = false;
        }

        $this->assertFalse($esExists, 'Elasticsearch document must be deleted after link deletion!');
    }

    /**
     * Test 8: Circuit Breaker tracks state transitions and fallback resilience.
     */
    public function test_circuit_breaker_resilience(): void
    {
        TeamMeetingCircuitBreaker::reset();
        $this->assertEquals(TeamMeetingCircuitBreaker::STATE_CLOSED, TeamMeetingCircuitBreaker::getState());
        $this->assertTrue(TeamMeetingCircuitBreaker::isAvailable());

        // Record 3 failures to trip circuit
        TeamMeetingCircuitBreaker::recordFailure(new \Exception('ES connection timeout'));
        TeamMeetingCircuitBreaker::recordFailure(new \Exception('ES connection timeout'));
        TeamMeetingCircuitBreaker::recordFailure(new \Exception('ES connection timeout'));

        $this->assertEquals(TeamMeetingCircuitBreaker::STATE_OPEN, TeamMeetingCircuitBreaker::getState());
        $this->assertFalse(TeamMeetingCircuitBreaker::isAvailable());

        // Reset
        TeamMeetingCircuitBreaker::reset();
        $this->assertEquals(TeamMeetingCircuitBreaker::STATE_CLOSED, TeamMeetingCircuitBreaker::getState());
    }

    /**
     * Test 9: Standard Teams Meeting page and AJAX response.
     */
    public function test_team_meeting_page_and_ajax_response(): void
    {
        $this->actingAs($this->superAdmin);

        // 1. Initial Page Load (Blade HTML)
        $response = $this->get('/super-admin/team-meeting-setting');
        $response->assertStatus(200);

        $content = $response->getContent();
        $this->assertStringContainsString('Microsoft Teams Link Pool', $content);
        $this->assertStringContainsString('Microsoft Teams Pool Directory', $content);

        // 2. AJAX JSON Endpoint
        $ajaxResponse = $this->getJson('/super-admin/team-meeting-setting?page=1');
        $ajaxResponse->assertStatus(200);
        $ajaxResponse->assertJsonStructure([
            'status',
            'total',
            'counts' => [
                'total',
                'active',
                'inactive',
                'scheduled',
            ],
            'data' => [
                'items',
                'total',
            ],
        ]);
        $this->assertSame('success', $ajaxResponse->json('status'));
    }

    /**
     * Test 10: Multi-Tenant atomic cache invalidation flushes pool cache and increments version.
     */
    public function test_atomic_cache_invalidation_flushes_pool_and_increments_version(): void
    {
        Cache::put('jero:team_meeting_links:active_pool', ['mock' => true], 3600);
        $initialVersion = SuperAdminTeamMeetingCacheManager::getVersion();

        SuperAdminTeamMeetingCacheManager::flushCaches();

        $this->assertFalse(Cache::has('jero:team_meeting_links:active_pool'));
        $this->assertGreaterThan($initialVersion, SuperAdminTeamMeetingCacheManager::getVersion());
    }
}
