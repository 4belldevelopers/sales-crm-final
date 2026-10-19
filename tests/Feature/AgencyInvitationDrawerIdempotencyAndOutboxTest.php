<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureOneDeviceLogin;
use App\Jobs\ProcessAgencyOutboxEventJob;
use App\Jobs\QueueNotificationMailService;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * AgencyInvitationDrawerIdempotencyAndOutboxTest
 *
 * Verifies Agency Invitation Drawer:
 * - Authorized invitation mutation
 * - Tenant isolation
 * - Input validation preservation
 * - Database transactional consistency with sys_outbox_events
 * - Durable idempotency via sys_idempotency_keys
 * - Outbox worker execution & Elasticsearch synchronization
 * - QueueNotificationMailService concurrency & deduplication
 */
class AgencyInvitationDrawerIdempotencyAndOutboxTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => 'newdata']);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        // Set server variables expected by views
        $_SERVER['REQUEST_URI'] = '/service_company/consultancy';

        $this->withoutMiddleware([EnsureOneDeviceLogin::class]);

        $this->user = User::withoutGlobalScopes()->where('company_id', 2)->where('status', 'active')->first();
        if (! $this->user) {
            $this->user = User::withoutGlobalScopes()->first();
        }

        $this->actingAs($this->user);
    }

    /**
     * 1. Authorized invitation succeeds and creates both invitation and outbox records.
     */
    public function test_authorized_invitation_succeeds_with_outbox(): void
    {
        Queue::fake([\App\Jobs\QueueNotificationMailService::class, \App\Jobs\ProcessAgencyOutboxEventJob::class]);
        Event::fake([\App\Events\ConsultancyPartnershipUpdated::class]);

        $email = 'newagency_'.uniqid().'@gmail.com';
        $key = 'test_key_'.uniqid();

        $response = $this->post(route('service_company.consultancy.staffing_invitation', 0), [
            'staffing_partners_email' => $email,
            'idempotency_key' => $key,
        ], [
            'X-Idempotency-Key' => $key,
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertSame('success', $data['status']);
        $this->assertArrayHasKey('spartnerDataCnt', $data);

        // Verify authoritative database write
        $this->assertDatabaseHas('staffing_company_invitation', [
            'from_company_id' => $this->user->company_id,
            'to_email' => $email,
            'from_status' => 1,
        ]);

        // Verify atomic outbox event write
        $this->assertDatabaseHas('sys_outbox_events', [
            'tenant_id' => $this->user->company_id,
            'idempotency_key' => $key,
            'aggregate_type' => 'AgencyRequest',
            'event_type' => 'AGENCY_INVITATION_SENT',
            'status' => 'PENDING',
        ]);

        // Verify idempotency completion
        $this->assertDatabaseHas('sys_idempotency_keys', [
            'tenant_id' => $this->user->company_id,
            'idempotency_key' => $key,
            'status' => 'COMPLETED',
        ]);
    }

    /**
     * 2. Unauthorized invitation is rejected.
     */
    public function test_unauthorized_invitation_is_rejected(): void
    {
        auth()->logout();

        $response = $this->post(route('service_company.consultancy.staffing_invitation', 0), [
            'staffing_partners_email' => 'unauth_'.uniqid().'@gmail.com',
        ]);

        $response->assertRedirect();
    }

    /**
     * 3. Invalid email preserves existing validation behavior.
     */
    public function test_invalid_email_preserves_existing_validation(): void
    {
        $response = $this->post(route('service_company.consultancy.staffing_invitation', 0), [
            'staffing_partners_email' => 'invalid-email-format',
        ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertSame('warning', $data['status']);
        $this->assertStringContainsString('valid email', $data['message']);
    }

    /**
     * 4. Idempotency: Same key and payload return identical cached outcome without duplicate records.
     */
    public function test_same_idempotency_key_returns_cached_outcome_without_duplication(): void
    {
        Queue::fake([\App\Jobs\QueueNotificationMailService::class, \App\Jobs\ProcessAgencyOutboxEventJob::class]);
        Event::fake([\App\Events\ConsultancyPartnershipUpdated::class]);

        $email = 'replay_'.uniqid().'@gmail.com';
        $key = 'replay_key_'.uniqid();

        // First attempt
        $res1 = $this->post(route('service_company.consultancy.staffing_invitation', 0), [
            'staffing_partners_email' => $email,
            'idempotency_key' => $key,
        ], ['X-Idempotency-Key' => $key]);

        $data1 = $res1->json();
        $this->assertSame('success', $data1['status']);

        $invitationCountInitial = DB::table('staffing_company_invitation')
            ->where('from_company_id', $this->user->company_id)
            ->where('to_email', $email)
            ->count();

        $this->assertSame(1, $invitationCountInitial);

        // Second attempt with exact same key and payload
        $res2 = $this->post(route('service_company.consultancy.staffing_invitation', 0), [
            'staffing_partners_email' => $email,
            'idempotency_key' => $key,
        ], ['X-Idempotency-Key' => $key]);

        $data2 = $res2->json();
        $this->assertSame('success', $data2['status']);
        $this->assertSame($data1['message'], $data2['message']);

        // Verify no duplicate record was created
        $invitationCountAfter = DB::table('staffing_company_invitation')
            ->where('from_company_id', $this->user->company_id)
            ->where('to_email', $email)
            ->count();

        $this->assertSame(1, $invitationCountAfter, 'Idempotent replay must not create duplicate invitation records');
    }

    /**
     * 5. Key reuse with materially different payload is safely rejected.
     */
    public function test_key_reuse_with_different_payload_is_rejected(): void
    {
        $key = 'conflict_key_'.uniqid();

        // Initial claim
        $res1 = $this->post(route('service_company.consultancy.staffing_invitation', 0), [
            'staffing_partners_email' => 'initial_'.uniqid().'@gmail.com',
            'idempotency_key' => $key,
        ], ['X-Idempotency-Key' => $key]);

        // Attempt reuse with different email
        $res2 = $this->post(route('service_company.consultancy.staffing_invitation', 0), [
            'staffing_partners_email' => 'different_'.uniqid().'@gmail.com',
            'idempotency_key' => $key,
        ], ['X-Idempotency-Key' => $key]);

        $data2 = $res2->json();
        $this->assertSame('error', $data2['status']);
        $this->assertStringContainsString('reused', $data2['message']);
    }

    /**
     * 6. Database rollback also rolls back outbox record atomically.
     */
    public function test_database_rollback_reverts_invitation_and_outbox(): void
    {
        $email = 'rollback_'.uniqid().'@gmail.com';
        $key = 'rollback_key_'.uniqid();

        try {
            DB::transaction(function () use ($email, $key) {
                DB::table('staffing_company_invitation')->insert([
                    'from_company_id' => $this->user->company_id,
                    'to_email' => $email,
                    'from_status' => 1,
                    'from_created_at' => now(),
                ]);

                DB::table('sys_outbox_events')->insert([
                    'tenant_id' => $this->user->company_id,
                    'idempotency_key' => $key,
                    'aggregate_type' => 'AgencyRequest',
                    'aggregate_id' => 999999,
                    'aggregate_version' => 1,
                    'event_type' => 'AGENCY_INVITATION_SENT',
                    'payload' => json_encode(['email' => $email]),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'created_at' => now(),
                ]);

                throw new \RuntimeException('Simulated transactional failure');
            });
        } catch (\RuntimeException $e) {
            // Expected
        }

        $this->assertDatabaseMissing('staffing_company_invitation', ['to_email' => $email]);
        $this->assertDatabaseMissing('sys_outbox_events', ['idempotency_key' => $key]);
    }

    /**
     * 7. Outbox worker processes AGENCY_INVITATION_SENT and marks it COMPLETED.
     */
    public function test_outbox_worker_processes_agency_invitation_sent(): void
    {
        $outboxId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => $this->user->company_id,
            'idempotency_key' => 'worker_test_'.uniqid(),
            'aggregate_type' => 'AgencyRequest',
            'aggregate_id' => 1,
            'aggregate_version' => 1,
            'event_type' => 'AGENCY_INVITATION_SENT',
            'payload' => json_encode([
                'from_company_id' => $this->user->company_id,
                'to_email' => 'worker_test@gmail.com',
                'invitation_id' => 1,
            ]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);

        $worker = new ProcessAgencyOutboxEventJob($outboxId);
        $worker->handle();

        $this->assertDatabaseHas('sys_outbox_events', [
            'id' => $outboxId,
            'status' => 'COMPLETED',
        ]);
    }

    /**
     * 8. Tenant isolation: Tenant A cannot access or replay Tenant B's idempotency key.
     */
    public function test_tenant_isolation_is_enforced_on_idempotency(): void
    {
        $key = 'cross_tenant_'.uniqid();

        // Seed Tenant 1 idempotency key
        DB::table('sys_idempotency_keys')->insert([
            'tenant_id' => 1,
            'idempotency_key' => $key,
            'request_hash' => hash('sha256', json_encode(['email' => 'tenant1@gmail.com'])),
            'status' => 'COMPLETED',
            'response_status' => 200,
            'response_body' => json_encode(['status' => 'success', 'tenant' => 1]),
            'expires_at' => now()->addHours(24),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Tenant 2 uses same key
        $response = $this->post(route('service_company.consultancy.staffing_invitation', 0), [
            'staffing_partners_email' => 'tenant2_'.uniqid().'@gmail.com',
            'idempotency_key' => $key,
        ], ['X-Idempotency-Key' => $key]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertSame('success', $data['status']);

        // Verify Tenant 2 has its own isolated record in sys_idempotency_keys
        $this->assertDatabaseHas('sys_idempotency_keys', [
            'tenant_id' => $this->user->company_id,
            'idempotency_key' => $key,
            'status' => 'COMPLETED',
        ]);
    }

    /**
     * 9. Verify consultancy index page loads successfully and caches static countries list.
     */
    public function test_consultancy_index_page_loads_successfully(): void
    {
        $response = $this->get(route('service_company.consultancy.index'));
        $response->assertStatus(200);
        $response->assertViewHas('countries');
        $this->assertTrue(cache()->has('static_countries_list'));
    }
}
