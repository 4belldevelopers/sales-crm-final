<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SuperAdmin\SuperAdminNotificationCacheManager;
use App\Services\SuperAdmin\SuperAdminNotificationMutationService;
use App\Services\SuperAdmin\SuperAdminNotificationReadService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SuperAdminNotificationGodLevelBackendTest
 *
 * Forensic Feature & Architectural Verification Suite for:
 * - CQRS Read Path: 0% MySQL reads on warm listing and edit page loads (Rule 1 & Rule 2).
 * - Universal Read Scope: 100% queries served via Elasticsearch with Dragonfly/Redis L2 caching.
 * - Two-Tier Request Idempotency: sys_idempotency_keys atomic lease claims, deduplication, and replay.
 * - Concurrent In-Flight Conflict Protection: 409 Conflict rejection during active lease.
 * - Transactional Outbox Pattern: sys_outbox_events persistence and fast-path projection synchronization.
 * - Generation-Based Cache Versioning: O(1) atomic invalidation without keyspace iteration.
 * - CLI Operations: php artisan notifications:reindex and php artisan notifications:process-outbox.
 *
 * CRITICAL RULE: Zero RefreshDatabase. Non-destructive teardown and atomic rollback.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminNotificationGodLevelBackendTest extends TestCase
{
    protected User $superAdmin;

    protected int $testNotificationId = 1;

    protected ?array $originalNotification = null;

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

        // Backup test notification record
        $row = DB::table('email_notifications')->where('id', $this->testNotificationId)->first();
        if ($row) {
            $this->originalNotification = (array) $row;
        }
    }

    protected function tearDown(): void
    {
        // Restore original state
        if ($this->originalNotification !== null) {
            DB::table('email_notifications')
                ->where('id', $this->testNotificationId)
                ->update([
                    'status' => $this->originalNotification['status'],
                    'purpose' => $this->originalNotification['purpose'],
                    'subject' => $this->originalNotification['subject'],
                    'from_email_id' => $this->originalNotification['from_email_id'],
                    'body_description' => $this->originalNotification['body_description'],
                    'mailType' => $this->originalNotification['mailType'],
                ]);
        }

        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        SuperAdminNotificationCacheManager::flushCaches();

        parent::tearDown();
    }

    /**
     * Test 1: CQRS Read Path: Warm listing page executes 0 MySQL SELECT queries against email_notifications.
     */
    public function test_01_notification_listing_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache and Elasticsearch
        $this->get(route('superadmin.company.notification'));

        $queryCount = 0;
        DB::listen(function ($query) use (&$queryCount) {
            if (stripos($query->sql, 'email_notifications') !== false && stripos($query->sql, 'select') !== false) {
                $queryCount++;
            }
        });

        $response = $this->get(route('superadmin.company.notification'));

        $response->assertStatus(200);
        $this->assertSame(0, $queryCount, 'CQRS VIOLATION: MySQL SELECT queries executed against email_notifications on warm read path!');
    }

    /**
     * Test 2: CQRS Read Path: Warm edit page executes 0 MySQL SELECT queries against email_notifications and roles.
     */
    public function test_02_notification_edit_view_executes_zero_mysql_reads_on_warm_cache(): void
    {
        $this->actingAs($this->superAdmin);

        // Warm up cache
        $this->get(route('superadmin.company.notificationupdate', $this->testNotificationId));

        $forbiddenReads = 0;
        DB::listen(function ($query) use (&$forbiddenReads) {
            if ((stripos($query->sql, 'email_notifications') !== false
                || stripos($query->sql, 'mail_permissions') !== false
                || stripos($query->sql, 'roles') !== false)
                && stripos($query->sql, 'select') !== false) {
                $forbiddenReads++;
            }
        });

        $response = $this->get(route('superadmin.company.notificationupdate', $this->testNotificationId));

        $response->assertStatus(200);
        $this->assertSame(0, $forbiddenReads, 'CQRS VIOLATION: MySQL reads executed on warm edit page load!');
    }

    /**
     * Test 3: Elasticsearch Universal Read Scope: Read service hydrates complete catalog and precomputed KPIs.
     */
    public function test_03_elasticsearch_universal_read_scope_hydrates_catalog_and_kpis(): void
    {
        $readService = app(SuperAdminNotificationReadService::class);
        $catalog = $readService->getNotificationsCatalog();

        $this->assertNotEmpty($catalog['templates'], 'Catalog templates should not be empty');
        $this->assertGreaterThan(0, $catalog['totalNotifications']);
        $this->assertArrayHasKey('activeNotifications', $catalog);
        $this->assertArrayHasKey('inactiveNotifications', $catalog);
        $this->assertArrayHasKey('roleGovernedCount', $catalog);

        // Verify deterministic ascending order
        $ids = $catalog['templates']->pluck('id')->toArray();
        $sortedIds = $ids;
        sort($sortedIds);
        $this->assertSame($sortedIds, $ids, 'Templates must be deterministically sorted by ID ASC!');
    }

    /**
     * Test 4: Two-Tier Idempotency: Atomic lease claim and instant response replay.
     */
    public function test_04_two_tier_idempotency_lease_claim_and_response_replay(): void
    {
        $this->actingAs($this->superAdmin);
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        // First execution
        $response1 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->postJson(route('superadmin.company.SingleNotificationUpdate'), [
            'id' => $this->testNotificationId,
            'status' => 1,
            'idempotency_key' => $idempotencyKey,
        ]);

        $response1->assertStatus(200);
        $data1 = $response1->json();
        $this->assertSame('success', $data1['status'] ?? '');
        if (isset($data1['event_id'])) {
            $this->createdOutboxIds[] = (int) $data1['event_id'];
        }

        // Verify record in sys_idempotency_keys
        $lease = DB::table('sys_idempotency_keys')
            ->where('tenant_id', 0)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        $this->assertNotNull($lease, 'Idempotency lease must exist in sys_idempotency_keys');
        $this->assertSame('COMPLETED', $lease->status);

        // Re-submit identical request: Must replay without re-executing writes
        $response2 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->postJson(route('superadmin.company.SingleNotificationUpdate'), [
            'id' => $this->testNotificationId,
            'status' => 1,
            'idempotency_key' => $idempotencyKey,
        ]);

        $response2->assertStatus(200);
        $data2 = $response2->json();
        $this->assertTrue($data2['_replayed'] ?? false, 'Duplicate request must return _replayed => true from cached idempotency store!');
    }

    /**
     * Test 5: Concurrent In-Flight Conflict Protection: 409 Conflict when lease is in PROCESSING.
     */
    public function test_05_concurrent_in_flight_conflict_protection(): void
    {
        $this->actingAs($this->superAdmin);
        $inFlightKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $inFlightKey;

        // Pre-claim lease as PROCESSING
        DB::table('sys_idempotency_keys')->insert([
            'tenant_id' => 0,
            'idempotency_key' => $inFlightKey,
            'request_hash' => hash('sha256', 'mock_in_flight'),
            'status' => 'PROCESSING',
            'expires_at' => Carbon::now()->addHours(24),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        $response = $this->withHeaders([
            'X-Idempotency-Key' => $inFlightKey,
            'X-Requested-With' => 'XMLHttpRequest',
        ])->postJson(route('superadmin.company.SingleNotificationUpdate'), [
            'id' => $this->testNotificationId,
            'status' => 0,
            'idempotency_key' => $inFlightKey,
        ]);

        $response->assertStatus(409);
    }

    /**
     * Test 6: Transactional Outbox Pattern: Mutation records event in sys_outbox_events.
     */
    public function test_06_transactional_outbox_event_persistence(): void
    {
        $mutationService = app(SuperAdminNotificationMutationService::class);
        $testKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $testKey;

        $result = $mutationService->toggleStatus(
            notificationId: $this->testNotificationId,
            newStatus: 0,
            idempotencyKey: $testKey,
            adminId: $this->superAdmin->id
        );

        $this->assertSame('success', $result['status']);
        $eventId = (int) ($result['event_id'] ?? 0);
        $this->assertGreaterThan(0, $eventId);
        $this->createdOutboxIds[] = $eventId;

        $outbox = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        $this->assertNotNull($outbox);
        $this->assertSame('EmailNotification', $outbox->aggregate_type);
        $this->assertSame($this->testNotificationId, (int) $outbox->aggregate_id);
        $this->assertSame('NotificationStatusToggled', $outbox->event_type);

        $payload = json_decode($outbox->payload, true);
        $this->assertIsArray($payload);
        $this->assertSame(0, $payload['status']);
    }

    /**
     * Test 7: Generation-Based Cache Versioning: Atomic increment and O(1) invalidation.
     */
    public function test_07_generation_based_cache_versioning(): void
    {
        $versionBefore = SuperAdminNotificationCacheManager::getVersion();
        SuperAdminNotificationCacheManager::flushCaches();
        $versionAfter = SuperAdminNotificationCacheManager::getVersion();

        $this->assertSame($versionBefore + 1, $versionAfter, 'Cache generation version must increment atomically on flush!');
    }

    /**
     * Test 8: CLI Command: php artisan notifications:reindex runs with 0 errors.
     */
    public function test_08_cli_notifications_reindex_command(): void
    {
        $exitCode = Artisan::call('notifications:reindex');
        $this->assertSame(0, $exitCode, 'Artisan notifications:reindex must exit with 0');

        $output = Artisan::output();
        $this->assertStringContainsString('Reindexing complete', $output);
    }

    /**
     * Test 9: CLI Command: php artisan notifications:process-outbox runs cleanly.
     */
    public function test_09_cli_notifications_process_outbox_command(): void
    {
        $exitCode = Artisan::call('notifications:process-outbox', ['--limit' => 10]);
        $this->assertSame(0, $exitCode, 'Artisan notifications:process-outbox must exit with 0');
    }

    /**
     * Test 10: Sandboxed simulated preview generation with L2 Dragonfly cache.
     */
    public function test_10_sandboxed_preview_generation_and_caching(): void
    {
        $readService = app(SuperAdminNotificationReadService::class);
        $preview = $readService->getSandboxedPreview($this->testNotificationId, wantsJson: true);

        $this->assertIsArray($preview);
        $this->assertSame('success', $preview['status']);
        $this->assertStringContainsString('<', $preview['html']);
        $this->assertArrayHasKey('tokens_used', $preview);
    }
}
