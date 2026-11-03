<?php

namespace Tests\Unit;

use App\Enums\InvitationStatus;
use App\Exceptions\DomainProtectionException;
use App\Exceptions\TenantStateCorruptedException;
use App\Models\Company;
use App\Models\TeamInvitation;
use App\Services\CorporateDomainProtectionService;
use App\Services\RegistrationIdempotencyHandler;
use App\Services\TeamInvitationService;
use App\Services\TenantVersioningService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Phase 0 Unit & Domain Verification Test Suite
 *
 * Verifies core security boundaries, state machines, database constraints,
 * idempotency protocols, and domain protection shields.
 */
class TeamInvitationPhase0Test extends TestCase
{
    use DatabaseTransactions;

    protected int $testCompanyId = 1;

    protected int $otherCompanyId = 2;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'dbmaster',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        // Ensure test companies exist
        DB::table('companies')->updateOrInsert(
            ['id' => 1],
            ['company_name' => 'Acme Corp', 'domain_key' => 'acmecorp.com', 'status' => 'active']
        );
        DB::table('companies')->updateOrInsert(
            ['id' => 2],
            ['company_name' => 'TechNova Inc', 'domain_key' => 'technova.com', 'status' => 'active']
        );

        // Ensure roles exist
        DB::table('roles')->updateOrInsert(
            ['id' => 1],
            ['name' => 'admin', 'display_name' => 'Admin', 'company_id' => 1]
        );

        // Ensure tenant state versions exist
        DB::table('tenant_state_versions')->updateOrInsert(
            ['company_id' => 1, 'domain' => 'team'],
            ['version' => 1, 'updated_at' => now()]
        );
    }

    protected function tearDown(): void
    {
        DB::table('team_invitations')->where('email', 'like', '%@gmail.com')->delete();
        parent::tearDown();
    }

    /**
     * Test 1: Inclusivity - Any company can invite personal webmail (Gmail, Yahoo, Outlook, etc.)
     */
    public function test_personal_emails_are_universally_allowed_for_any_company(): void
    {
        $personalEmails = [
            'recruiter.freelance@gmail.com',
            'sourcer.candidate@yahoo.com',
            'talent.partner@outlook.com',
            'intern@hotmail.com',
            'consultant@icloud.com',
            'agent@zoho.com',
        ];

        foreach ($personalEmails as $email) {
            // Must NOT throw any exception
            CorporateDomainProtectionService::validateTeamInvitationDomain(1, $email);
            $this->assertTrue(true, "Email {$email} was cleanly allowed.");
        }
    }

    /**
     * Test 2: Disposable temporary emails are strictly blocked.
     */
    public function test_disposable_emails_are_blocked(): void
    {
        $this->expectException(DomainProtectionException::class);
        $this->expectExceptionCode(422);

        CorporateDomainProtectionService::validateTeamInvitationDomain(1, 'spammer@mailinator.com');
    }

    /**
     * Test 3: Cross-Tenant proprietary corporate domain shield.
     * Company 1 cannot invite an email matching Company 2's registered domain_key.
     */
    public function test_cross_tenant_corporate_domain_is_blocked(): void
    {
        $this->expectException(DomainProtectionException::class);
        $this->expectExceptionCode(403);

        // TechNova Inc is company 2 (technova.com). Company 1 attempts to invite employee@technova.com
        CorporateDomainProtectionService::validateTeamInvitationDomain(1, 'employee@technova.com');
    }

    /**
     * Test 4: Same-tenant corporate domain or unreserved corporate domain is allowed.
     */
    public function test_same_tenant_or_unreserved_corporate_domain_is_allowed(): void
    {
        // Same company domain
        CorporateDomainProtectionService::validateTeamInvitationDomain(1, 'colleague@acmecorp.com');
        $this->assertTrue(true);

        // Unreserved corporate domain (not registered by another tenant)
        CorporateDomainProtectionService::validateTeamInvitationDomain(1, 'contractor@randomagencyconsulting.io');
        $this->assertTrue(true);
    }

    /**
     * Test 5: State Machine - isAcceptable() authority check.
     */
    public function test_invitation_acceptable_state_machine(): void
    {
        $invitation = new TeamInvitation;
        $invitation->status = InvitationStatus::PENDING;
        $invitation->expires_at = now()->addHours(24);
        $this->assertTrue($invitation->isAcceptable());

        $invitation->status = InvitationStatus::LINK_ACCESSED;
        $this->assertTrue($invitation->isAcceptable());

        $invitation->status = InvitationStatus::ACTIVATED;
        $this->assertFalse($invitation->isAcceptable());

        $invitation->status = InvitationStatus::EXPIRED;
        $this->assertFalse($invitation->isAcceptable());

        $invitation->status = InvitationStatus::REVOKED;
        $this->assertFalse($invitation->isAcceptable());

        // Even if PENDING, if revoked_at is set, it is not acceptable
        $invitation->status = InvitationStatus::PENDING;
        $invitation->revoked_at = now();
        $this->assertFalse($invitation->isAcceptable());

        // Even if PENDING, if expired, it is not acceptable
        $invitation->revoked_at = null;
        $invitation->expires_at = now()->subMinute();
        $this->assertFalse($invitation->isAcceptable());
    }

    /**
     * Test 6: Database-enforced active invitation uniqueness (INV-03).
     */
    public function test_database_enforced_active_invitation_uniqueness(): void
    {
        $email = 'concurrency.'.uniqid().'@gmail.com';
        $tokenHash1 = hash('sha256', Str::random(32));
        $tokenHash2 = hash('sha256', Str::random(32));

        // Insert first active invitation
        DB::table('team_invitations')->insert([
            'uuid' => (string) Str::uuid(),
            'company_id' => 1,
            'email' => $email,
            'token_hash' => $tokenHash1,
            'role_id' => 1,
            'status' => 'PENDING',
            'delivery_status' => 'QUEUED',
            'invited_by' => 1,
            'expires_at' => now()->addHours(72),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attempting to insert a second active invitation for the same email must throw MySQL 1062
        $this->expectException(\Illuminate\Database\QueryException::class);

        DB::table('team_invitations')->insert([
            'uuid' => (string) Str::uuid(),
            'company_id' => 1,
            'email' => $email,
            'token_hash' => $tokenHash2,
            'role_id' => 1,
            'status' => 'PENDING',
            'delivery_status' => 'QUEUED',
            'invited_by' => 1,
            'expires_at' => now()->addHours(72),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Test 7: Universal Tenant State Versioning monotonic increments (INV-11 & INV-12).
     */
    public function test_tenant_state_versioning_monotonic_increment(): void
    {
        $initial = TenantVersioningService::getCurrentVersion(1, 'team');
        $next = TenantVersioningService::allocateNextVersion(1, 'team');

        $this->assertEquals($initial + 1, $next);
        $this->assertEquals($next, TenantVersioningService::getCurrentVersion(1, 'team'));
    }

    /**
     * Test 8: Versioning fails hard if tenant state version row is missing.
     */
    public function test_tenant_versioning_fails_hard_on_missing_record(): void
    {
        $this->expectException(TenantStateCorruptedException::class);

        TenantVersioningService::allocateNextVersion(99999, 'team');
    }

    /**
     * Test 9: Idempotency Protocol - IDEMPOTENCY-01: Hash Mismatch Detection.
     */
    public function test_idempotency_rejects_altered_payload_under_same_key(): void
    {
        $invitationId = DB::table('team_invitations')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'company_id' => 1,
            'email' => 'idem.hash.'.uniqid().'@gmail.com',
            'token_hash' => hash('sha256', Str::random(32)),
            'role_id' => 1,
            'status' => 'PENDING',
            'delivery_status' => 'QUEUED',
            'invited_by' => 1,
            'expires_at' => now()->addHours(72),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $handler = new RegistrationIdempotencyHandler;

        $key = 'TEST_IDEM_KEY_'.uniqid();

        // Request 1
        $request1 = Request::create('/register/activate', 'POST', ['name' => 'Alice']);
        $request1->headers->set('Idempotency-Key', $key);

        $res1 = $handler->handle($request1, 1, $invitationId, function () {
            return new JsonResponse(['status' => 'success', 'user_id' => 101], 201);
        });

        $this->assertEquals(201, $res1->getStatusCode());

        // Request 2 with same key but DIFFERENT payload (Bob instead of Alice)
        $request2 = Request::create('/register/activate', 'POST', ['name' => 'Bob']);
        $request2->headers->set('Idempotency-Key', $key);

        $res2 = $handler->handle($request2, 1, $invitationId, function () {
            return new JsonResponse(['status' => 'success', 'user_id' => 102], 201);
        });

        $this->assertEquals(409, $res2->getStatusCode());
        $data2 = $res2->getData(true);
        $this->assertEquals('IDEMPOTENCY_KEY_REUSED_WITH_DIFFERENT_REQUEST', $data2['error']);
    }

    /**
     * Test 10: Idempotency Protocol - IDEMPOTENCY-01: In-Progress Concurrency Guard (409 + Retry-After).
     */
    public function test_idempotency_returns_409_retry_after_when_request_is_in_progress(): void
    {
        $invitationId = DB::table('team_invitations')->insertGetId([
            'uuid' => (string) Str::uuid(),
            'company_id' => 1,
            'email' => 'idem.progress.'.uniqid().'@gmail.com',
            'token_hash' => hash('sha256', Str::random(32)),
            'role_id' => 1,
            'status' => 'PENDING',
            'delivery_status' => 'QUEUED',
            'invited_by' => 1,
            'expires_at' => now()->addHours(72),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $key = 'PROGRESS_KEY_'.uniqid();
        $canonicalPayload = json_encode(['name' => 'Concurrent']);
        $hash = hash('sha256', $canonicalPayload);

        // Pre-insert an IN_PROGRESS record
        DB::table('registration_idempotency_keys')->insert([
            'tenant_id' => 1,
            'invitation_id' => $invitationId,
            'idempotency_key' => $key,
            'endpoint' => 'register/activate',
            'request_hash' => $hash,
            'status' => 'IN_PROGRESS',
            'created_at' => now(),
            'expires_at' => now()->addHours(24),
        ]);

        $handler = new RegistrationIdempotencyHandler;
        $request = Request::create('/register/activate', 'POST', ['name' => 'Concurrent']);
        $request->headers->set('Idempotency-Key', $key);

        $response = $handler->handle($request, 1, $invitationId, function () {
            return new JsonResponse(['status' => 'should_not_run'], 200);
        });

        $this->assertEquals(409, $response->getStatusCode());
        $this->assertEquals('2', $response->headers->get('Retry-After'));
        $this->assertEquals('REQUEST_IN_PROGRESS', $response->getData(true)['error']);
    }

    /**
     * Test 11: Nonce Single-Use Burn (SESSION-03).
     */
    public function test_session_nonce_atomic_burn_prevents_replay(): void
    {
        $service = new TeamInvitationService;
        $nonce = bin2hex(random_bytes(16));
        $request = Request::create('/register/activate', 'POST');

        // First burn succeeds
        $service->burnNonceAndRotateSession($nonce, $request);
        $this->assertTrue(true);

        // Second burn must abort with 401
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $service->burnNonceAndRotateSession($nonce, $request);
    }
}
