<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\InternalClientDuplicateService;
use App\Services\InternalClientReadinessService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * InternalClientDuplicationAndReadinessTest
 *
 * Comprehensive feature test suite verifying:
 * 1. Absolute Domain Isolation: Direct Client data NEVER blocks Internal Client creation.
 * 2. Mandatory Business Acceptance Scenarios (Examples 1 through 6, Scenario B, Scenario C).
 * 3. Canonical Domain, Company Name, Email, and Phone Normalizers.
 * 4. Internal Client Readiness Probe & Countdown State Progression.
 *
 * Architecture Compliance:
 * - 0% MySQL reads on search path.
 * - Multi-tenant isolation scoped strictly to `company_id` and `self_company = 0`.
 * - DatabaseTransactions ensures 100% database hygiene with zero test side-effects.
 *
 * Author: Antigravity AI
 * Date: October 2026
 */
class InternalClientDuplicationAndReadinessTest extends TestCase
{
    use DatabaseTransactions;

    protected int $tenantId = 99881;

    protected User $testUser;

    protected InternalClientDuplicateService $dupService;

    protected InternalClientReadinessService $readinessService;

    protected function setUp(): void
    {
        parent::setUp();

        // Switch to MySQL connection for real schema access with transaction rollback
        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'dbmaster',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        // Establish tenant user context
        $this->testUser = new User;
        $this->testUser->id = 88771;
        $this->testUser->company_id = $this->tenantId;
        $this->testUser->is_superadmin = 0;
        $this->testUser->status = 'active';
        Auth::setUser($this->testUser);

        $this->dupService = app(InternalClientDuplicateService::class);
        $this->readinessService = app(InternalClientReadinessService::class);
    }

    /**
     * Test 1: Canonical domain normalizer resolves all URL formats to bare domain.
     */
    public function test_canonicalize_domain_normalizes_all_formats(): void
    {
        $testCases = [
            'https://example.com' => 'example.com',
            'http://example.com' => 'example.com',
            'https://www.example.com' => 'example.com',
            'http://www.example.com/' => 'example.com',
            'www.example.com' => 'example.com',
            'example.com/' => 'example.com',
            'EXAMPLE.COM' => 'example.com',
            'https://www.Example.com/' => 'example.com',
            'https://sub.domain.co.uk/careers?ref=ats' => 'sub.domain.co.uk',
            'http://portal.tech.io:8080/home' => 'portal.tech.io',
        ];

        foreach ($testCases as $input => $expected) {
            $actual = InternalClientDuplicateService::canonicalizeDomain($input);
            $this->assertEquals(
                $expected,
                $actual,
                "Canonical domain for [{$input}] must normalize to [{$expected}], got [{$actual}]."
            );
        }
    }

    /**
     * Test 2: Company name normalizer standardizes whitespace and casing without aggressive corruption.
     */
    public function test_normalize_company_name(): void
    {
        $this->assertEquals(
            'abc technologies',
            InternalClientDuplicateService::normalizeCompanyName(' ABC Technologies ')
        );

        $this->assertEquals(
            'abc technologies',
            InternalClientDuplicateService::normalizeCompanyName('abc   technologies')
        );

        $this->assertEquals(
            'global recruiting solutions',
            InternalClientDuplicateService::normalizeCompanyName("Global   Recruiting\t\nSolutions")
        );
    }

    /**
     * Test 3: Email normalizer lowercases and trims.
     */
    public function test_normalize_email(): void
    {
        $this->assertEquals(
            'john.doe@example.com',
            InternalClientDuplicateService::normalizeEmail('  John.Doe@Example.COM ')
        );
    }

    /**
     * Test 4: Phone normalizer strips non-digits.
     */
    public function test_normalize_phone(): void
    {
        $this->assertEquals(
            '15551234567',
            InternalClientDuplicateService::normalizePhone('+1 (555) 123-4567')
        );
    }

    /**
     * Scenario 1: Same Company Name Exists in Direct Client (self_company = 1) -> ALLOW.
     * The Direct Client record must have ZERO impact on Internal Client creation.
     */
    public function test_scenario_1_same_company_in_direct_client_allows_internal_client(): void
    {
        // Insert Direct Client record (self_company = 1)
        DB::table('clients')->insert([
            'company_id' => $this->tenantId,
            'name' => 'ABC Technologies Direct',
            'website' => 'abctechdirect.com',
            'self_company' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attempt duplicate check for Internal Client with same company name
        $dup = $this->dupService->checkCompanyNameDuplicate($this->tenantId, 'ABC Technologies Direct');

        $this->assertNull(
            $dup,
            'Direct Client company name must NEVER block Internal Client creation (Scenario 1 must ALLOW).'
        );
    }

    /**
     * Scenario 2: Same Company Name Already Exists in Internal Client (self_company = 0) -> BLOCK.
     */
    public function test_scenario_2_same_company_in_internal_client_blocks_duplicate(): void
    {
        // Insert existing Internal Client record (self_company = 0)
        DB::table('clients')->insert([
            'company_id' => $this->tenantId,
            'name' => 'ABC Technologies Internal',
            'website' => 'abctechinternal.com',
            'self_company' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attempt duplicate check for new Internal Client with same name (ignoring casing/spaces)
        $dup = $this->dupService->checkCompanyNameDuplicate($this->tenantId, '  abc technologies internal  ');

        $this->assertNotNull(
            $dup,
            'Duplicate company name within Internal Client domain must be BLOCKED (Scenario 2).'
        );
        $this->assertTrue($dup['duplicate']);
        $this->assertEquals('name', $dup['field']);
        $this->assertStringContainsString(
            'This client is already registered internally.',
            $dup['message']
        );
    }

    /**
     * Scenario 3: Same Website Exists in Direct Client (self_company = 1) -> ALLOW.
     * The Direct Client website must have ZERO impact.
     */
    public function test_scenario_3_same_website_in_direct_client_allows_internal_client(): void
    {
        // Insert Direct Client record (self_company = 1)
        DB::table('clients')->insert([
            'company_id' => $this->tenantId,
            'name' => 'Direct Portal Company',
            'website' => 'https://abcdirect.com',
            'self_company' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Check duplicate website for new Internal Client
        $dup = $this->dupService->checkDomainDuplicate($this->tenantId, 'https://abcdirect.com');

        $this->assertNull(
            $dup,
            'Direct Client website must NEVER block Internal Client creation (Scenario 3 must ALLOW).'
        );
    }

    /**
     * Scenario 4: Same Website Already Exists in Internal Client (self_company = 0) -> BLOCK.
     * Even with variations like protocol, casing, www, and trailing slashes.
     */
    public function test_scenario_4_same_website_in_internal_client_blocks_duplicate(): void
    {
        // Insert existing Internal Client with bare canonical domain
        DB::table('clients')->insert([
            'company_id' => $this->tenantId,
            'name' => 'Internal Alpha Corp',
            'website' => 'alphainternalcorp.com',
            'self_company' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attempt duplicate check with variations
        $dup = $this->dupService->checkDomainDuplicate($this->tenantId, 'https://www.AlphaInternalCorp.com/');

        $this->assertNotNull(
            $dup,
            'Duplicate website within Internal Client domain must be BLOCKED regardless of casing or protocol (Scenario 4).'
        );
        $this->assertTrue($dup['duplicate']);
        $this->assertEquals('website', $dup['field']);
        $this->assertStringContainsString(
            'This website/domain is already registered internally.',
            $dup['message']
        );
    }

    /**
     * Scenario 5: Same Contact Exists in Direct Client -> ALLOW.
     * The Direct Client contact must have ZERO impact.
     */
    public function test_scenario_5_same_contact_in_direct_client_allows_internal_client(): void
    {
        // Create Direct Client (self_company = 1)
        $directClientId = DB::table('clients')->insertGetId([
            'company_id' => $this->tenantId,
            'name' => 'Direct Client Org',
            'website' => 'directorgspecial.com',
            'self_company' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attach contact to Direct Client
        DB::table('client_contact_info')->insert([
            'client_id' => $directClientId,
            'contact_person_name' => 'John Doe',
            'contact_person_email' => 'john.direct@abcdirect.com',
            'contact_person_number' => '1234567890',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Check contact duplicate for new Internal Client
        $dup = $this->dupService->checkContactDuplicate($this->tenantId, 'john.direct@abcdirect.com', '1234567890');

        $this->assertNull(
            $dup,
            'Direct Client contact must NEVER block Internal Client creation (Scenario 5 must ALLOW).'
        );
    }

    /**
     * Scenario 6: Same Contact Already Exists in Another Internal Client -> BLOCK (Scenario C).
     */
    public function test_scenario_6_same_contact_in_internal_client_blocks_duplicate(): void
    {
        // Create existing Internal Client (self_company = 0)
        $internalClientId = DB::table('clients')->insertGetId([
            'company_id' => $this->tenantId,
            'name' => 'First Internal Client Alpha',
            'website' => 'internalonealpha.com',
            'self_company' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Attach contact to First Internal Client
        DB::table('client_contact_info')->insert([
            'client_id' => $internalClientId,
            'contact_person_name' => 'Jane Smith',
            'contact_person_email' => 'jane.smith@internalonealpha.com',
            'contact_person_number' => '9876543210',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Check duplicate for new Internal Client
        $dup = $this->dupService->checkContactDuplicate($this->tenantId, 'Jane.Smith@internalonealpha.com', null);

        $this->assertNotNull(
            $dup,
            'Duplicate contact email in another Internal Client must be BLOCKED (Scenario 6 / Scenario C).'
        );
        $this->assertTrue($dup['duplicate']);
        $this->assertEquals('contact_email', $dup['field']);
        $this->assertStringContainsString(
            'This contact is already registered with another internal company.',
            $dup['message']
        );
    }

    /**
     * Scenario B: Contact already exists in current company draft -> BLOCK.
     */
    public function test_scenario_b_duplicate_contact_within_current_draft(): void
    {
        $payload = [
            'name' => 'Unique Brand New Client Ltd',
            'website' => 'uniquebrandnewclient.com',
            'cp_name' => ['Alice Primary', 'Alice Duplicate'],
            'cp_email' => ['alice@uniquecompany.com', 'ALICE@uniquecompany.com'], // Identical email
            'cp_number' => ['1111111111', '2222222222'],
        ];

        $dup = $this->dupService->validateSubmissionDuplicates($this->tenantId, $payload);

        $this->assertNotNull($dup);
        $this->assertTrue($dup['duplicate']);
        $this->assertEquals('contact_email', $dup['field']);
        $this->assertStringContainsString(
            'This contact is already registered with the current company.',
            $dup['message']
        );
    }

    /**
     * Test Internal Client Readiness Lifecycle:
     * - Transitions from PROCESSING -> SYNCHRONIZING -> READY.
     * - Fast path probe returns ready state.
     */
    public function test_readiness_service_lifecycle_and_probe(): void
    {
        $tempClientId = 776655;

        // Mark processing
        $this->readinessService->markProcessing($this->tenantId, $tempClientId);
        $probeProcessing = $this->readinessService->checkReadiness($this->tenantId, $tempClientId);
        $this->assertFalse($probeProcessing['ready']);
        $this->assertEquals('PROCESSING', $probeProcessing['state']);

        // Mark synchronizing
        $this->readinessService->markSynchronizing($this->tenantId, $tempClientId);
        $probeSync = $this->readinessService->checkReadiness($this->tenantId, $tempClientId);
        $this->assertFalse($probeSync['ready']);
        $this->assertEquals('SYNCHRONIZING', $probeSync['state']);

        // Mark ready
        $this->readinessService->markReady($this->tenantId, $tempClientId);
        $probeReady = $this->readinessService->checkReadiness($this->tenantId, $tempClientId);
        $this->assertTrue($probeReady['ready']);
        $this->assertEquals('READY', $probeReady['state']);

        // Clean up cache keys
        Cache::forget("internal_client:ready:{$this->tenantId}:{$tempClientId}");
        Cache::forget("internal_client:lifecycle:{$this->tenantId}:{$tempClientId}");
    }

    /**
     * Test AJAX endpoint for immediate website duplicate validation:
     * - Direct client website returns valid: true (allowed).
     * - Internal client website returns valid: false & duplicate: true (blocked).
     */
    public function test_ajax_validate_internal_field_website(): void
    {
        $this->withoutMiddleware();
        \Illuminate\Support\Facades\Gate::define('add_clients', fn () => true);
        \Illuminate\Support\Facades\Gate::define('edit_clients', fn () => true);

        // Seed Direct Client (self_company = 1)
        DB::table('clients')->insert([
            'company_id' => $this->tenantId,
            'name' => 'Direct Only Company',
            'website' => 'https://www.direct-only.com',
            'self_company' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed Internal Client (self_company = 0)
        DB::table('clients')->insert([
            'company_id' => $this->tenantId,
            'name' => 'Internal Existing Company',
            'website' => 'https://www.internal-existing.com',
            'self_company' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Test 1: Direct Client website -> ALLOW (valid: true)
        $respDirect = $this->actingAs($this->testUser)->postJson(route('service_company.clients.validateInternalField'), [
            'field' => 'website',
            'website' => 'https://direct-only.com/',
        ]);
        $respDirect->assertOk();
        $respDirect->assertJson(['status' => 'success', 'valid' => true]);

        // Test 2: Internal Client website -> BLOCK (valid: false, duplicate: true)
        $respInternal = $this->actingAs($this->testUser)->postJson(route('service_company.clients.validateInternalField'), [
            'field' => 'website',
            'website' => 'https://internal-existing.com/',
        ]);
        $respInternal->assertOk();
        $respInternal->assertJson(['status' => 'duplicate', 'valid' => false, 'duplicate' => true]);
        $this->assertStringContainsString('This website/domain is already registered internally', $respInternal->json('message'));
    }

    /**
     * Test AJAX endpoint for immediate contact duplicate validation:
     * - Direct client contact returns valid: true (allowed).
     * - Internal client contact returns valid: false & duplicate: true (blocked).
     */
    public function test_ajax_validate_internal_field_contact(): void
    {
        $this->withoutMiddleware();
        \Illuminate\Support\Facades\Gate::define('add_clients', fn () => true);
        \Illuminate\Support\Facades\Gate::define('edit_clients', fn () => true);

        // Seed Direct Client with contact
        $directClientId = DB::table('clients')->insertGetId([
            'company_id' => $this->tenantId,
            'name' => 'Direct Client Contact Corp',
            'self_company' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('client_contact_info')->insert([
            'client_id' => $directClientId,
            'contact_person_name' => 'Direct Contact',
            'contact_person_email' => 'direct.contact@testcorp.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Seed Internal Client with contact
        $internalClientId = DB::table('clients')->insertGetId([
            'company_id' => $this->tenantId,
            'name' => 'Internal Client Contact Corp',
            'self_company' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('client_contact_info')->insert([
            'client_id' => $internalClientId,
            'contact_person_name' => 'Internal Contact',
            'contact_person_email' => 'internal.contact@testcorp.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Test 1: Direct Client contact email -> ALLOW (valid: true)
        $respDirect = $this->actingAs($this->testUser)->postJson(route('service_company.clients.validateInternalField'), [
            'field' => 'contact',
            'email' => 'direct.contact@testcorp.com',
        ]);
        $respDirect->assertOk();
        $respDirect->assertJson(['status' => 'success', 'valid' => true]);

        // Test 2: Internal Client contact email -> BLOCK (valid: false, duplicate: true)
        $respInternal = $this->actingAs($this->testUser)->postJson(route('service_company.clients.validateInternalField'), [
            'field' => 'contact',
            'email' => 'internal.contact@testcorp.com',
        ]);
        $respInternal->assertOk();
        $respInternal->assertJson(['status' => 'duplicate', 'valid' => false, 'duplicate' => true]);
        $this->assertStringContainsString('This contact is already registered with another internal company', $respInternal->json('message'));

        // Test 3: Batch check for Step 4 navigation guard
        $respBatch = $this->actingAs($this->testUser)->postJson(route('service_company.clients.validateInternalField'), [
            'field' => 'all',
            'cp_name' => ['Fresh Person', 'Duplicated Person'],
            'cp_email' => ['fresh.person@brandnewcorp99.com', 'internal.contact@testcorp.com'],
            'cp_number' => ['5550192837', '5550192838'],
            'calling_code' => ['+91', '+91'],
        ]);
        $respBatch->assertOk();
        $respBatch->assertJson(['status' => 'duplicate', 'valid' => false, 'duplicate' => true, 'index' => 1]);
    }

    /**
     * Test Main Account website duplicate check:
     * - Website matching the Main Account cannot be added to Internal Clients.
     * - Returns a clear duplicate message indicating registration with Main Account.
     */
    public function test_main_account_website_blocks_internal_client_duplicate(): void
    {
        $this->withoutMiddleware();
        \Illuminate\Support\Facades\Gate::define('add_clients', fn () => true);
        \Illuminate\Support\Facades\Gate::define('edit_clients', fn () => true);

        // Seed / Update Main Account company record for this tenant
        DB::table('companies')->updateOrInsert(
            ['id' => $this->tenantId],
            [
                'company_name' => 'Main Account Enterprises',
                'company_email' => 'contact@main-account-ent.com',
                'website' => 'https://www.main-account-ent.com',
                'status' => 'active',
                'updated_at' => now(),
            ]
        );

        // 1. Direct Service probe
        $dup = $this->dupService->checkDomainDuplicate($this->tenantId, 'https://main-account-ent.com/');
        $this->assertNotNull($dup);
        $this->assertTrue($dup['duplicate']);
        $this->assertEquals('SCENARIO_MAIN_ACCOUNT_WEBSITE', $dup['scenario']);
        $this->assertStringContainsString('already registered with your Main Account', $dup['message']);

        // 2. Point-of-entry AJAX probe (Step 1 debounced/blur validation)
        $resp = $this->actingAs($this->testUser)->postJson(route('service_company.clients.validateInternalField'), [
            'field' => 'website',
            'website' => 'https://main-account-ent.com/',
        ]);
        $resp->assertOk();
        $resp->assertJson([
            'status' => 'duplicate',
            'valid' => false,
            'duplicate' => true,
        ]);
        $this->assertStringContainsString('already registered with your Main Account', $resp->json('message'));
    }

    /**
     * Test Main Account and Tenant Administrator contact/email duplicate check:
     * - Contact email matching Main Account company email or Tenant Administrator user email cannot be added.
     * - Returns clear duplicate message indicating registration with Main Account.
     */
    public function test_main_account_and_tenant_administrator_contact_blocks_internal_client_duplicate(): void
    {
        $this->withoutMiddleware();
        \Illuminate\Support\Facades\Gate::define('add_clients', fn () => true);
        \Illuminate\Support\Facades\Gate::define('edit_clients', fn () => true);

        // Seed / Update Main Account company record
        DB::table('companies')->updateOrInsert(
            ['id' => $this->tenantId],
            [
                'company_name' => 'Main Account HQ',
                'company_email' => 'hq.main@main-corp-system.com',
                'company_phone' => '9988776655',
                'status' => 'active',
                'updated_at' => now(),
            ]
        );

        // Seed Tenant Administrator user (root_user = 1)
        DB::table('users')->updateOrInsert(
            ['id' => 999111],
            [
                'company_id' => $this->tenantId,
                'root_user' => 1,
                'name' => 'Tenant Administrator Admin',
                'email' => 'tenant.admin@main-corp-system.com',
                'mobile' => '9988776656',
                'password' => bcrypt('secret'),
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // 1. Direct Service probe for Company Main Email
        $dupCompanyEmail = $this->dupService->checkContactDuplicate($this->tenantId, 'HQ.MAIN@main-corp-system.com');
        $this->assertNotNull($dupCompanyEmail);
        $this->assertTrue($dupCompanyEmail['duplicate']);
        $this->assertEquals('SCENARIO_MAIN_ACCOUNT_CONTACT', $dupCompanyEmail['scenario']);
        $this->assertStringContainsString('already registered with your Main Account', $dupCompanyEmail['message']);

        // 2. Direct Service probe for Tenant Administrator user email
        $dupAdminEmail = $this->dupService->checkContactDuplicate($this->tenantId, 'tenant.admin@main-corp-system.com');
        $this->assertNotNull($dupAdminEmail);
        $this->assertTrue($dupAdminEmail['duplicate']);
        $this->assertEquals('SCENARIO_MAIN_ACCOUNT_CONTACT', $dupAdminEmail['scenario']);
        $this->assertStringContainsString('already registered with your Main Account (Tenant Administrator)', $dupAdminEmail['message']);

        // 3. Point-of-entry AJAX probe (Step 4 contact drawer email validation)
        $respAjax = $this->actingAs($this->testUser)->postJson(route('service_company.clients.validateInternalField'), [
            'field' => 'contact',
            'email' => 'tenant.admin@main-corp-system.com',
        ]);
        $respAjax->assertOk();
        $respAjax->assertJson([
            'status' => 'duplicate',
            'valid' => false,
            'duplicate' => true,
        ]);
        $this->assertStringContainsString('already registered with your Main Account', $respAjax->json('message'));

        // 4. Batch validation probe (Step 4 navigation guard)
        $respBatch = $this->actingAs($this->testUser)->postJson(route('service_company.clients.validateInternalField'), [
            'field' => 'all',
            'cp_name' => ['Fresh Person', 'Tenant Admin Person'],
            'cp_email' => ['completely.fresh@brandnew.com', 'tenant.admin@main-corp-system.com'],
            'cp_number' => ['5550192837', '5550192838'],
            'calling_code' => ['+91', '+91'],
        ]);
        $respBatch->assertOk();
        $respBatch->assertJson([
            'status' => 'duplicate',
            'valid' => false,
            'duplicate' => true,
            'index' => 1,
        ]);
        $this->assertStringContainsString('already registered with your Main Account', $respBatch->json('message'));
    }
}
