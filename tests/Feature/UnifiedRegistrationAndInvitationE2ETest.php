<?php

namespace Tests\Feature;

use App\Enums\InvitationStatus;
use App\Models\Company;
use App\Models\User;
use App\Services\RegistrationContextResolver;
use App\Services\TeamInvitationService;
use App\Services\TenantVersioningService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

/**
 * UnifiedRegistrationAndInvitationE2ETest
 *
 * Full End-to-End Architectural Verification across all 4 Boundaries:
 * - Boundary 1: Invitation Security, Opaque Tokens & URL Scrubbing.
 * - Boundary 2: Context Resolution & Declarative Schema Manifest.
 * - Boundary 3: Activation Authority, Pessimistic Row Lock & Idempotency.
 * - Boundary 4: Tenant Data Integrity, Versioning, Delta Reconciliation & Marketplace Assignments.
 */
class UnifiedRegistrationAndInvitationE2ETest extends TestCase
{
    use DatabaseTransactions;

    protected int $tenantCompanyId = 1;

    protected int $adminUserId = 1;

    protected int $roleId = 1;

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

        // Seed tenant company
        DB::table('companies')->updateOrInsert(
            ['id' => 1],
            ['company_name' => 'Acme Recruiting Corp', 'domain_key' => 'acmerecruiting.com', 'status' => 'active']
        );

        // Seed admin user
        DB::table('users')->updateOrInsert(
            ['id' => 1],
            ['name' => 'Admin User', 'email' => 'admin@acmerecruiting.com', 'company_id' => 1, 'status' => 'active', 'password' => Hash::make('secret123')]
        );

        // Seed role
        DB::table('roles')->updateOrInsert(
            ['id' => 1],
            ['name' => 'recruiter', 'display_name' => 'Lead Recruiter', 'company_id' => 1]
        );

        // Seed tenant state versions for all 5 domains
        foreach (['team', 'jobs', 'clients', 'commercials', 'candidates'] as $d) {
            DB::table('tenant_state_versions')->updateOrInsert(
                ['company_id' => 1, 'domain' => $d],
                ['version' => 1, 'updated_at' => now()]
            );
        }
    }

    protected function tearDown(): void
    {
        DB::table('team_invitations')->where('company_id', 1)->delete();
        DB::table('users')->where('email', 'like', 'e2e.%@gmail.com')->delete();
        parent::tearDown();
    }

    /**
     * Test Complete Flow: Issue -> Scrub -> Resolve Context -> Idempotent Activation -> Version Bump.
     */
    public function test_complete_unified_invitation_and_activation_lifecycle(): void
    {
        $service = app(TeamInvitationService::class);
        $email = 'e2e.'.uniqid().'@gmail.com';

        // 1. Issue Secure Invitation (Boundary 1)
        $result = $service->createInvitation(1, 1, $email, 1, 72);
        $rawToken = $result['raw_token'];
        $invitation = $result['invitation'];

        $this->assertNotEmpty($rawToken);
        $this->assertEquals(InvitationStatus::PENDING, $invitation->status);
        $this->assertEquals(1, $invitation->active_token_slot);

        // 2. Visit /register?token=<rawSecret> -> URL Token Scrubbing (INV-02)
        $scrubResponse = $this->get('/register?token='.$rawToken);
        $scrubResponse->assertStatus(302);
        $scrubResponse->assertRedirect('/register');
        $scrubResponse->assertCookie(TeamInvitationService::COOKIE_NAME);

        // Extract raw cookie (bypass Laravel's internal test decryption prefix slicer)
        $cookie = $scrubResponse->getCookie(TeamInvitationService::COOKIE_NAME, false);
        $rawCookieValue = $cookie->getValue();

        // 3. Visit clean /register with Cookie -> Declarative Context Resolution (Boundary 2)
        $contextResponse = $this->call('GET', '/register', [], [TeamInvitationService::COOKIE_NAME => $rawCookieValue]);
        $contextResponse->assertStatus(200);
        $context = $contextResponse->original->getData()['registrationContext'] ?? null;

        $this->assertNotNull($context);
        $this->assertEquals(RegistrationContextResolver::CONTEXT_TEAM, $context['context']);
        $this->assertEquals($email, $context['member']['email']);
        $this->assertFalse($context['schema']['fields']['email']['editable']); // Server immutable (INV-06)

        // 4. Submit Activation POST /register/activate (Boundary 3)
        $idempotencyKey = 'IDEM_'.uniqid();
        $activationPayload = [
            'name' => 'God Level Recruiter',
            'password' => 'StrongPassword123!',
            'password_confirmation' => 'StrongPassword123!',
            'toc' => 1,
            'marketplace_channels' => [
                'client' => true,
                'agency' => true,
            ],
        ];

        $activationResponse = $this->call(
            'POST',
            '/register/activate',
            $activationPayload,
            [TeamInvitationService::COOKIE_NAME => $rawCookieValue],
            [],
            $this->transformHeadersToServerVars([
                'Idempotency-Key' => $idempotencyKey,
                'Accept' => 'application/json',
            ])
        );

        $activationResponse->assertStatus(201);
        $activationData = $activationResponse->json();

        $this->assertEquals('success', $activationData['status']);
        $createdUserId = $activationData['user_id'];
        $this->assertGreaterThan(1, $createdUserId);

        // Verify User was created correctly in DB
        $dbUser = DB::table('users')->where('id', $createdUserId)->first();
        $this->assertEquals('God Level Recruiter', $dbUser->name);
        $this->assertEquals($email, $dbUser->email);
        $this->assertEquals(1, $dbUser->company_id);
        $this->assertEquals('active', $dbUser->status);
        $this->assertEquals(1, $dbUser->working_status);

        // Verify Invitation was marked ACTIVATED and slot was cleared (INV-03 & INV-04)
        $dbInvitation = DB::table('team_invitations')->where('id', $invitation->id)->first();
        $this->assertEquals('ACTIVATED', $dbInvitation->status);
        $this->assertNotNull($dbInvitation->used_at);
        $this->assertNull($dbInvitation->active_token_slot); // Virtual generated column becomes NULL!

        // Verify Universal Tenant State Version was monotonically incremented (INV-11 & INV-12)
        $currentVersion = TenantVersioningService::getCurrentVersion(1, 'team');
        $this->assertGreaterThan(1, $currentVersion);

        // Verify Marketplace Assignments were recorded (Phase 3 & Boundary 4)
        $clientRep = DB::table('company_marketplace_contact_assignments')
            ->where('company_id', 1)
            ->where('user_id', $createdUserId)
            ->where('channel', 'client')
            ->first();
        $this->assertNotNull($clientRep);
        $this->assertEquals('public', $clientRep->visibility);

        // 5. Test Idempotency Replay (INV-08 & INV-16)
        // Submitting exact same Idempotency-Key returns cached 201 snapshot
        Auth::logout();
        $replayResponse = $this->call(
            'POST',
            '/register/activate',
            $activationPayload,
            [TeamInvitationService::COOKIE_NAME => $rawCookieValue],
            [],
            $this->transformHeadersToServerVars([
                'Idempotency-Key' => $idempotencyKey,
                'Accept' => 'application/json',
            ])
        );

        $replayResponse->assertStatus(201);
        $this->assertTrue($replayResponse->headers->has('X-Idempotent-Replay'));

        // 6. Test Idempotency Altered Payload Mismatch (INV-16 IDEMPOTENCY-01)
        Auth::logout();
        $alteredPayload = $activationPayload;
        $alteredPayload['name'] = 'Altered Name Attack';

        $conflictResponse = $this->call(
            'POST',
            '/register/activate',
            $alteredPayload,
            [TeamInvitationService::COOKIE_NAME => $rawCookieValue],
            [],
            $this->transformHeadersToServerVars([
                'Idempotency-Key' => $idempotencyKey,
                'Accept' => 'application/json',
            ])
        );

        $conflictResponse->assertStatus(409);
        $this->assertEquals('IDEMPOTENCY_KEY_REUSED_WITH_DIFFERENT_REQUEST', $conflictResponse->json()['error']);
    }

    /**
     * Test Bounded Delta Reconciliation Endpoint (INV-13).
     */
    public function test_team_state_delta_reconciliation_circuit_breaker(): void
    {
        $user = User::where('id', 1)->first();
        Auth::login($user);

        // 1. Version 0 or uninitialized triggers snapshot fallback
        $response = $this->actingAs($user)->getJson('/api/team/state/delta?from_version=0');
        $response->assertStatus(200);
        $this->assertEquals('snapshot', $response->json()['status']);
        $this->assertEquals('GAP_EXCEEDS_CIRCUIT_BREAKER', $response->json()['reason']);
        $this->assertArrayHasKey('members', $response->json()['data']);

        // 2. Bump versions and test delta
        TenantVersioningService::allocateNextVersion(1, 'team'); // version 2
        TenantVersioningService::allocateNextVersion(1, 'team'); // version 3
        $currentVersion = TenantVersioningService::getCurrentVersion(1, 'team'); // 3

        $deltaResponse = $this->actingAs($user)->getJson('/api/team/state/delta?from_version=2');
        $deltaResponse->assertStatus(200);
        $this->assertEquals('delta', $deltaResponse->json()['status']);
    }
}
