<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use App\Services\RegistrationContextResolver;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * UnifiedRegistrationOption2Test
 *
 * Verifies the Unified Registration Architecture for Option 2:
 * 1. Legacy URL redirection to common registration (/register?agency_code=...)
 * 2. RegistrationContextResolver dynamic context resolution for AGENCY
 * 3. Scenario B: New agency self-registration with automated partnership relation
 * 4. Scenario A: Existing authenticated agency 1-click CAS partnership connection
 * 5. Scenario C: Existing email collision handling & self-partnership prevention
 */
class UnifiedRegistrationOption2Test extends TestCase
{
    use DatabaseTransactions;

    protected int $invitingCompanyId = 2;

    protected User $clientUser;

    protected User $existingAgencyUser;

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

        $this->withoutMiddleware([\App\Http\Middleware\EnsureOneDeviceLogin::class]);

        $clientUser = User::withoutGlobalScopes()
            ->where('company_id', $this->invitingCompanyId)
            ->where('root_user', 1)
            ->first();

        if (! $clientUser) {
            $clientUser = User::withoutGlobalScopes()->where('company_id', $this->invitingCompanyId)->first();
        }

        $this->clientUser = $clientUser;

        // Existing agency user for Scenario A tests
        $agencyUser = User::withoutGlobalScopes()
            ->where('company_id', '!=', $this->invitingCompanyId)
            ->where('root_user', 1)
            ->first();

        $this->existingAgencyUser = $agencyUser;
    }

    /**
     * Test 1: Legacy route /company-registration-invitation/{code} redirects 302 to /register?agency_code={code}
     */
    public function test_legacy_option_2_url_redirects_to_common_registration(): void
    {
        $code = base64_encode((string) $this->invitingCompanyId);

        $response = $this->get('/company-registration-invitation/'.$code);

        $response->assertStatus(302);
        $response->assertRedirect(route('register', ['agency_code' => $code]));
    }

    /**
     * Test 2: Legacy route /invitation-company-register/{code} redirects 302 to /register?agency_code={code}
     */
    public function test_legacy_invitation_url_redirects_to_common_registration(): void
    {
        $code = base64_encode((string) $this->invitingCompanyId);

        $response = $this->get('/invitation-company-register/'.$code);

        $response->assertStatus(302);
        $response->assertRedirect(route('register', ['agency_code' => $code]));
    }

    /**
     * Test 3: RegistrationContextResolver resolves agency_code to CONTEXT_AGENCY with locked company_type=1
     */
    public function test_registration_context_resolver_resolves_agency_code(): void
    {
        $code = base64_encode((string) $this->invitingCompanyId);
        $request = \Illuminate\Http\Request::create('/register', 'GET', ['agency_code' => $code]);

        $resolver = app(RegistrationContextResolver::class);
        $context = $resolver->resolve($request);

        $this->assertEquals(RegistrationContextResolver::CONTEXT_AGENCY, $context['context']);
        $this->assertEquals($code, $context['agency_code']);
        $this->assertEquals($this->invitingCompanyId, $context['inviting_company']['id']);
        $this->assertEquals(1, $context['schema']['company_type']);
    }

    /**
     * Test 4: Common registration page renders with Agency Partnership Invitation banner and locked company type
     */
    public function test_common_registration_page_renders_agency_invitation_state(): void
    {
        $code = base64_encode((string) $this->invitingCompanyId);

        $response = $this->get('/register?agency_code='.$code);

        $response->assertStatus(200);
        $response->assertSee('Agency Partnership');
        $response->assertSee('Recruitment or Staffing Agency');
        $response->assertSee('name="agency_code"', false);
    }

    /**
     * Test 5: Scenario B — New Agency Registration establishes partnership relation and invitation record
     */
    public function test_scenario_b_new_agency_registration_establishes_partnership(): void
    {
        $code = base64_encode((string) $this->invitingCompanyId);
        $uniqueEmail = 'new-agency-'.uniqid().'@testagency.com';

        $payload = [
            'name' => 'John Agency Partner',
            'email' => $uniqueEmail,
            'password' => 'SecurePass123!@#',
            'contact_number' => '9876543210',
            'contact_code' => '+1',
            'company_name' => 'Apex Talent Staffing',
            'company_type' => 1,
            'agency_code' => $code,
        ];

        $response = $this->postJson(route('company-register'), $payload);

        $response->assertStatus(200);
        $response->assertJson(['status' => 'success']);

        // Verify company created as Staffing Agency (Type 1)
        $newCompany = Company::where('company_email', $uniqueEmail)->first();
        $this->assertNotNull($newCompany);
        $this->assertEquals(1, $newCompany->company_type);

        // Verify user created
        $newUser = User::where('email', $uniqueEmail)->first();
        $this->assertNotNull($newUser);

        // Verify staffing_company_invitation created with accepted status
        $invitation = DB::table('staffing_company_invitation')
            ->where('from_company_id', $this->invitingCompanyId)
            ->where('to_company_id', $newCompany->id)
            ->first();

        $this->assertNotNull($invitation);
        $this->assertEquals(1, (int) $invitation->to_invitation_status);
        $this->assertEquals(1, (int) $invitation->to_status);

        // Verify staffing_company_relation established with final_status = 1
        $relation = DB::table('staffing_company_relation')
            ->where('from_company_id', $this->invitingCompanyId)
            ->where('to_company_id', $newCompany->id)
            ->first();

        $this->assertNotNull($relation);
        $this->assertEquals(1, (int) $relation->final_status);
    }

    /**
     * Test 6: Scenario A — Authenticated Agency User opens Option 2 link and accepts partnership via 1-click CAS
     */
    public function test_scenario_a_authenticated_agency_1_click_cas_connection(): void
    {
        $code = base64_encode((string) $this->invitingCompanyId);

        // Remove existing relation if any for clean test
        DB::table('staffing_company_relation')
            ->where('from_company_id', $this->invitingCompanyId)
            ->where('to_company_id', $this->existingAgencyUser->company_id)
            ->delete();

        // 1. GET /register?agency_code={code} shows CAS connect card
        $response = $this->actingAs($this->existingAgencyUser)
            ->get('/register?agency_code='.$code);

        $response->assertStatus(200);
        $response->assertSee('kt_agency_connect_card');
        $response->assertSee('Accept & Connect Agency');

        // 2. POST /register/agency-connect establishes the relationship
        $connectResponse = $this->actingAs($this->existingAgencyUser)
            ->postJson(route('register.agency-connect'), [
                'agency_code' => $code,
            ]);

        $connectResponse->assertStatus(200);
        $connectResponse->assertJson(['status' => 'success']);

        // Verify relation exists
        $relation = DB::table('staffing_company_relation')
            ->where('from_company_id', $this->invitingCompanyId)
            ->where('to_company_id', $this->existingAgencyUser->company_id)
            ->first();

        $this->assertNotNull($relation);
        $this->assertEquals(1, (int) $relation->final_status);

        // 3. Repeated connection is idempotent
        $idempotentResponse = $this->actingAs($this->existingAgencyUser)
            ->postJson(route('register.agency-connect'), [
                'agency_code' => $code,
            ]);

        $idempotentResponse->assertStatus(200);
        $idempotentResponse->assertJson(['status' => 'success']);
    }

    /**
     * Test 7: Scenario C — User attempts to register with already-registered email returns 409 existing_account
     */
    public function test_scenario_c_existing_email_returns_409_with_login_prompt(): void
    {
        $code = base64_encode((string) $this->invitingCompanyId);

        $payload = [
            'name' => 'Existing User Attempt',
            'email' => $this->existingAgencyUser->email,
            'password' => 'SecurePass123!@#',
            'contact_number' => '9876543210',
            'contact_code' => '+1',
            'company_name' => 'Existing Agency Corp',
            'company_type' => 1,
            'agency_code' => $code,
        ];

        $response = $this->postJson(route('company-register'), $payload);

        $response->assertStatus(409);
        $response->assertJson([
            'status' => 'existing_account',
            'email' => $this->existingAgencyUser->email,
        ]);
    }

    /**
     * Test 8: Self-partnership prevention — Inviting company cannot partner with itself
     */
    public function test_self_partnership_is_prevented(): void
    {
        $code = base64_encode((string) $this->invitingCompanyId);

        $response = $this->actingAs($this->clientUser)
            ->postJson(route('register.agency-connect'), [
                'agency_code' => $code,
            ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status' => 'error',
            'message' => 'You cannot establish a partnership with your own organization.',
        ]);
    }
}
