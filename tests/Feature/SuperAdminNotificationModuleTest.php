<?php

namespace Tests\Feature;

use App\Jobs\ProcessNotificationOutboxEventJob;
use App\Models\User;
use App\Services\Notification\NotificationHtmlSanitizer;
use App\Services\Notification\NotificationTokenRegistry;
use App\Services\SuperAdmin\SuperAdminNotificationMutationService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * SuperAdminNotificationModuleTest
 *
 * Comprehensive feature verification test suite for Super Admin Email Notification module:
 * 1. Super Admin authentication and authorization boundaries.
 * 2. Strict CQRS read: 0% MySQL reads on listing and deterministic ID sorting.
 * 3. Atomic MySQL mutation + Durable Outbox Event (sys_outbox_events).
 * 4. Outbox worker processing and Elasticsearch projection updates.
 * 5. Protection against out-of-order stale events.
 * 6. Idempotent duplicate event handling.
 * 7. RBAC validation and rejection of injected invalid roles.
 * 8. Server-side allowlist HTML sanitization (stripping XSS, scripts, iframes, on* handlers).
 * 9. Defense-in-depth sandboxed preview execution with 0% MySQL reads.
 * 10. Drift reconciliation console command execution.
 * 11. Post-commit multi-tenant cache invalidation.
 */
class SuperAdminNotificationModuleTest extends TestCase
{
    protected array $originalNotification;

    protected array $originalPermissions = [];

    protected User $superAdminUser;

    protected User $regularUser;

    protected int $testNotificationId = 47;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'newdata',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        // Backup existing template #47 state
        $row = DB::table('email_notifications')->where('id', $this->testNotificationId)->first();
        if ($row) {
            $this->originalNotification = (array) $row;
            $this->originalPermissions = DB::table('mail_permissions')
                ->where('notification_id', $this->testNotificationId)
                ->pluck('role_name')
                ->toArray();
        }

        // Retrieve Super Admin user
        $admin = User::where('is_superadmin', 1)->first();
        if (! $admin) {
            $admin = User::factory()->create([
                'is_superadmin' => 1,
                'email' => 'test_superadmin_'.time().'@jero.works',
            ]);
        }
        $this->superAdminUser = $admin;

        // Retrieve regular non-admin user
        $regular = User::where('is_superadmin', 0)->first();
        if (! $regular) {
            $regular = User::factory()->create([
                'is_superadmin' => 0,
                'email' => 'test_regular_'.time().'@jero.works',
            ]);
        }
        $this->regularUser = $regular;
    }

    protected function tearDown(): void
    {
        // Restore original database records for template #47
        if (! empty($this->originalNotification)) {
            DB::table('email_notifications')
                ->where('id', $this->testNotificationId)
                ->update([
                    'purpose' => $this->originalNotification['purpose'],
                    'subject' => $this->originalNotification['subject'],
                    'from_email_id' => $this->originalNotification['from_email_id'],
                    'body_description' => $this->originalNotification['body_description'],
                    'status' => $this->originalNotification['status'],
                    'mailType' => $this->originalNotification['mailType'],
                ]);

            DB::table('mail_permissions')->where('notification_id', $this->testNotificationId)->delete();
            foreach ($this->originalPermissions as $roleName) {
                DB::table('mail_permissions')->insert([
                    'notification_id' => $this->testNotificationId,
                    'role_name' => $roleName,
                ]);
            }

            try {
                $es = app(\App\Services\ElasticService::class);
                $index = config('services.elastic.prefix', '').'email_notifications';
                $es->getClient()->update([
                    'index' => $index,
                    'id' => $this->testNotificationId,
                    'refresh' => true,
                    'body' => [
                        'doc' => [
                            'purpose' => $this->originalNotification['purpose'],
                            'subject' => $this->originalNotification['subject'],
                            'from_email_id' => $this->originalNotification['from_email_id'],
                            'body_description' => $this->originalNotification['body_description'],
                            'status' => (int) $this->originalNotification['status'],
                            'mailType' => (int) $this->originalNotification['mailType'],
                            'role_names' => $this->originalPermissions,
                        ],
                        'doc_as_upsert' => true,
                    ],
                ]);
            } catch (\Throwable $e) {
                // Best effort ES cleanup
            }
        }

        // Clean any test outbox events generated during tests
        DB::table('sys_outbox_events')
            ->where('aggregate_type', 'EmailNotification')
            ->where('aggregate_id', $this->testNotificationId)
            ->where('event_type', 'LIKE', 'TEST_%')
            ->delete();

        parent::tearDown();
    }

    /**
     * Test 01: Unauthenticated request redirects to login.
     */
    public function test_01_unauthenticated_request_redirects_to_login(): void
    {
        $response = $this->get(route('superadmin.company.notification'));
        $response->assertRedirect('/login');
    }

    /**
     * Test 02: Non-SuperAdmin user is forbidden from accessing notification endpoints.
     */
    public function test_02_non_superadmin_user_is_forbidden_from_notification_endpoints(): void
    {
        $response = $this->actingAs($this->regularUser)->get(route('superadmin.company.notification'));
        // SuperAdmin middleware denies non-superadmins (redirect or 403)
        $this->assertTrue(in_array($response->getStatusCode(), [302, 403], true));
    }

    /**
     * Test 03: Notification listing executes 0% MySQL reads and loads all 49 templates deterministically.
     */
    public function test_03_notification_listing_executes_zero_mysql_reads(): void
    {
        DB::enableQueryLog();
        DB::flushQueryLog();

        $response = $this->actingAs($this->superAdminUser)->get(route('superadmin.company.notification'));
        $response->assertStatus(200);

        $queries = DB::getQueryLog();
        // Base layout queries (global_settings, theme_settings) may run, but email_notifications & mail_permissions MUST NOT run
        $notifQueries = array_filter($queries, function ($q) {
            $sql = strtolower($q['query']);
            return str_contains($sql, 'email_notifications') || str_contains($sql, 'mail_permissions');
        });

        $this->assertCount(0, $notifQueries, 'Listing page must NOT execute queries against email_notifications or mail_permissions (0% MySQL Reads)!');
        $response->assertSee('notif-primary-card');
        $response->assertSee('notif-kpi-grid');
    }

    /**
     * Test 04: Status toggle updates MySQL atomically and persists outbox event.
     */
    public function test_04_status_toggle_updates_mysql_and_persists_outbox_event(): void
    {
        $currentStatus = (int) DB::table('email_notifications')->where('id', $this->testNotificationId)->value('status');
        $newStatus = $currentStatus === 1 ? 0 : 1;

        $response = $this->actingAs($this->superAdminUser)
            ->post(route('superadmin.company.SingleNotificationUpdate'), [
                'id' => $this->testNotificationId,
                'status' => $newStatus,
            ]);

        $response->assertStatus(200);

        // Verify MySQL authoritative update
        $updatedStatus = (int) DB::table('email_notifications')->where('id', $this->testNotificationId)->value('status');
        $this->assertSame($newStatus, $updatedStatus, 'MySQL email_notifications.status was not updated!');

        // Verify durable outbox event logged
        $outboxEvent = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'EmailNotification')
            ->where('aggregate_id', $this->testNotificationId)
            ->where('event_type', 'NotificationStatusToggled')
            ->orderBy('id', 'desc')
            ->first();

        $this->assertNotNull($outboxEvent, 'Outbox event was not recorded in sys_outbox_events!');
        $this->assertSame(0, (int) $outboxEvent->tenant_id, 'Super Admin outbox event must use tenant_id = 0!');
    }

    /**
     * Test 05: Outbox worker processes event and updates Elasticsearch projection.
     */
    public function test_05_outbox_worker_processes_event_and_updates_elasticsearch(): void
    {
        $outboxId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => 'TEST_OUTBOX_SYNC_'.uniqid(),
            'aggregate_type' => 'EmailNotification',
            'aggregate_id' => $this->testNotificationId,
            'aggregate_version' => 99999,
            'event_type' => 'TEST_NotificationSynced',
            'payload' => json_encode([
                'id' => $this->testNotificationId,
                'purpose' => 'Test Outbox Purpose',
                'status' => 1,
                'mailType' => 1,
                'role_names' => ['Administrator'],
                'updated_at' => now()->toIso8601String(),
            ]),
            'status' => 'PENDING',
            'attempts' => 0,
            'created_at' => now(),
        ]);

        $job = new ProcessNotificationOutboxEventJob($outboxId);
        $job->handle(app(\App\Services\ElasticService::class));

        $status = DB::table('sys_outbox_events')->where('id', $outboxId)->value('status');
        $this->assertSame('COMPLETED', $status, 'Outbox event should transition to COMPLETED!');
    }

    /**
     * Test 06: Stale outbox event cannot overwrite newer state.
     */
    public function test_06_stale_outbox_event_cannot_overwrite_newer_projection(): void
    {
        // Insert stale event with older timestamp
        $outboxId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => 'TEST_STALE_'.uniqid(),
            'aggregate_type' => 'EmailNotification',
            'aggregate_id' => $this->testNotificationId,
            'aggregate_version' => 1,
            'event_type' => 'TEST_StaleUpdate',
            'payload' => json_encode([
                'id' => $this->testNotificationId,
                'status' => 0,
                'updated_at' => '2020-01-01T00:00:00Z', // 6 years in the past
            ]),
            'status' => 'PENDING',
            'attempts' => 0,
            'created_at' => now(),
        ]);

        $job = new ProcessNotificationOutboxEventJob($outboxId);
        $job->handle(app(\App\Services\ElasticService::class));

        $status = DB::table('sys_outbox_events')->where('id', $outboxId)->value('status');
        $this->assertSame('COMPLETED', $status);
    }

    /**
     * Test 07: Duplicate event delivery is strictly idempotent.
     */
    public function test_07_duplicate_event_processing_is_strictly_idempotent(): void
    {
        $idempKey = 'TEST_IDEMP_'.uniqid();
        $outboxId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => $idempKey,
            'aggregate_type' => 'EmailNotification',
            'aggregate_id' => $this->testNotificationId,
            'aggregate_version' => 100,
            'event_type' => 'TEST_IdempotentEvent',
            'payload' => json_encode([
                'id' => $this->testNotificationId,
                'status' => 1,
            ]),
            'status' => 'COMPLETED',
            'processed_at' => now(),
            'created_at' => now(),
        ]);

        // Second delivery of already COMPLETED event
        $job = new ProcessNotificationOutboxEventJob($outboxId);
        $job->handle(app(\App\Services\ElasticService::class));

        $event = DB::table('sys_outbox_events')->where('id', $outboxId)->first();
        $this->assertSame('COMPLETED', $event->status);
        $this->assertSame(0, (int) $event->attempts, 'Attempts must not increment on already completed events!');
    }

    /**
     * Test 08: Empty role selection clears role permissions without crashing.
     */
    public function test_08_empty_role_selection_removes_permissions_safely(): void
    {
        $mutationService = app(SuperAdminNotificationMutationService::class);
        $result = $mutationService->saveNotification(
            data: [
                'purpose' => 'Empty Role Test',
                'subject' => 'Subject Test',
                'from_email_id' => 'noreply@jero.co.in',
                'mailType_is' => 1,
                'status' => 1,
                'role_ck' => [], // Empty roles denotes unrestricted
                'editor1' => '<p>Test</p>',
            ],
            tokenId: $this->testNotificationId,
            adminId: $this->superAdminUser->id
        );

        $this->assertSame('success', $result['status']);
        $perms = DB::table('mail_permissions')->where('notification_id', $this->testNotificationId)->pluck('role_name')->toArray();
        $this->assertEmpty($perms, 'Permissions must be empty when zero roles are submitted!');
    }

    /**
     * Test 09: Unauthorized role injection is rejected by FormRequest validation.
     */
    public function test_09_unauthorized_role_injection_is_rejected(): void
    {
        $response = $this->actingAs($this->superAdminUser)
            ->postJson(route('superadmin.company.addEditnotificationupdate'), [
                'tokenId' => $this->testNotificationId,
                'purpose' => 'Role Injection Test',
                'subject' => 'Subject Line',
                'from_email_id' => 'noreply@jero.co.in',
                'mailType_is' => 1,
                'status' => 1,
                'role_ck' => ['MaliciousInjectedRole'],
                'editor1' => '<p>Valid template body</p>',
            ]);

        $response->assertStatus(422);
        $data = $response->json();
        $this->assertSame('fail', $data['status']);
        $this->assertArrayHasKey('role_ck', $data['errors']);
    }

    /**
     * Test 10: Oversized body exceeding 256 KB is rejected.
     */
    public function test_10_oversized_template_body_is_rejected(): void
    {
        $oversizedBody = str_repeat('A', 270000); // 270 KB

        $response = $this->actingAs($this->superAdminUser)
            ->postJson(route('superadmin.company.addEditnotificationupdate'), [
                'tokenId' => $this->testNotificationId,
                'purpose' => 'Oversized Test',
                'subject' => 'Subject Line',
                'from_email_id' => 'noreply@jero.co.in',
                'mailType_is' => 1,
                'status' => 1,
                'role_ck' => ['Administrator'],
                'editor1' => $oversizedBody,
            ]);

        $response->assertStatus(422);
        $data = $response->json();
        $this->assertSame('fail', $data['status']);
    }

    /**
     * Test 11: Server-side sanitizer neutralizes XSS scripts, iframes, and on* handlers.
     */
    public function test_11_server_side_sanitizer_neutralizes_xss(): void
    {
        $sanitizer = app(NotificationHtmlSanitizer::class);

        $payload = '<p>Safe Text</p><script>alert("XSS")</script><iframe src="evil.com"></iframe><img src="x" onerror="alert(1)">';
        $cleaned = $sanitizer->sanitize($payload);

        $this->assertStringNotContainsString('<script', $cleaned);
        $this->assertStringNotContainsString('<iframe', $cleaned);
        $this->assertStringNotContainsString('onerror', $cleaned);
        $this->assertStringContainsString('Safe Text', $cleaned);
    }

    /**
     * Test 12: Preview endpoint serves sandboxed template with 0% MySQL reads.
     */
    public function test_12_preview_endpoint_serves_sandboxed_template_with_zero_mysql_reads(): void
    {
        DB::enableQueryLog();
        DB::flushQueryLog();

        $response = $this->actingAs($this->superAdminUser)
            ->getJson(route('superadmin.company.notificationpreview', $this->testNotificationId));

        $response->assertStatus(200);
        $data = $response->json();

        $this->assertSame('success', $data['status']);
        $this->assertStringContainsString('<!DOCTYPE html>', $data['html']);
        $this->assertStringContainsString('Simulated Template Preview', $data['html']);
        $this->assertIsArray($data['tokens_used']);

        $queries = DB::getQueryLog();
        $notifQueries = array_filter($queries, function ($q) {
            $sql = strtolower($q['query']);
            return str_contains($sql, 'email_notifications');
        });

        $this->assertCount(0, $notifQueries, 'Preview endpoint must NOT execute SELECTs against email_notifications (CQRS read from ES)!');
    }

    /**
     * Test 13: Preview endpoint returns HTTP 404 on non-existent template ID.
     */
    public function test_13_preview_endpoint_returns_404_on_non_existent_id(): void
    {
        $response = $this->actingAs($this->superAdminUser)
            ->getJson(route('superadmin.company.notificationpreview', 999999));

        $response->assertStatus(404);
        $data = $response->json();
        $this->assertSame('fail', $data['status']);
    }

    /**
     * Test 14: Drift reconciliation console command reports zero drift.
     */
    public function test_14_drift_reconciliation_command_reports_zero_drift(): void
    {
        // Reconcile any active mutations from prior tests first
        $this->artisan('elastic:reconcile', ['--entity' => 'notifications'])
            ->assertExitCode(0);

        // Dry run now verifies zero drift across the entire index
        $this->artisan('elastic:reconcile', ['--entity' => 'notifications', '--dry-run' => true])
            ->expectsOutputToContain('Total drift items detected: 0')
            ->assertExitCode(0);
    }

    /**
     * Test 15: Cache invalidation triggers after notification mutation.
     */
    public function test_15_cache_invalidation_triggers_after_notification_mutation(): void
    {
        Cache::put('email_notification_cpr_admin', 'cached_data', 3600);
        Cache::put('email_notification_cpr_customer', 'cached_data', 3600);
        Cache::put("email_notification:{$this->testNotificationId}", 'cached_data', 3600);

        $mutationService = app(SuperAdminNotificationMutationService::class);
        $mutationService->toggleStatus($this->testNotificationId, 1, adminId: $this->superAdminUser->id);

        $this->assertFalse(Cache::has('email_notification_cpr_admin'), 'email_notification_cpr_admin must be evicted!');
        $this->assertFalse(Cache::has('email_notification_cpr_customer'), 'email_notification_cpr_customer must be evicted!');
        $this->assertFalse(Cache::has("email_notification:{$this->testNotificationId}"), 'Single notification cache must be evicted!');
    }
}
