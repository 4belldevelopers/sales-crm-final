<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\ElasticService;
use App\Services\ForwardResumeAuthorizationService;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Class ForwardResumeRecipientAuthorizationTest
 *
 * Intent: Comprehensive unit and regression test suite verifying the Job-Context-Aware
 * Contact Visibility & Access Rules for the Forward Resume Add Email Recipient Drawer.
 *
 * Rules Verified:
 * 1. Job Posted Under "My Organization" -> ONLY My Organization contacts, ZERO client contacts.
 * 2. Job Posted Under "Internal Client" -> My Organization + ONLY that specific internal client's contacts.
 * 3. Job Posted Under "Direct Client" -> My Organization + STRICTLY ONLY Hiring Manager and Agency Added By.
 * 4. Zero Cross-Job / Cross-Client Contact Leakage.
 */
class ForwardResumeRecipientAuthorizationTest extends TestCase
{
    protected ForwardResumeAuthorizationService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildSchema();
        $elasticMock = $this->createMock(ElasticService::class);
        $this->service = new ForwardResumeAuthorizationService($elasticMock);
    }

    private function buildSchema(): void
    {
        if (! Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $t) {
                $t->id();
                $t->integer('company_id')->default(0);
                $t->string('name')->nullable();
                $t->string('email')->nullable();
                $t->string('image')->nullable();
                $t->string('password')->nullable();
                $t->string('status')->default('active');
                $t->integer('working_status')->default(1);
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('users_profile')) {
            Schema::create('users_profile', function (Blueprint $t) {
                $t->id();
                $t->integer('user_id');
                $t->string('designation')->nullable();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $t) {
                $t->id();
                $t->string('name')->nullable();
                $t->string('display_name')->nullable();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('role_user')) {
            Schema::create('role_user', function (Blueprint $t) {
                $t->integer('user_id');
                $t->integer('role_id');
            });
        }

        if (! Schema::hasTable('companies')) {
            Schema::create('companies', function (Blueprint $t) {
                $t->id();
                $t->string('company_name')->nullable();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('clients')) {
            Schema::create('clients', function (Blueprint $t) {
                $t->id();
                $t->integer('company_id')->default(0);
                $t->integer('self_company')->default(0);
                $t->string('name')->nullable();
                $t->string('website')->nullable();
                $t->string('email')->nullable();
                $t->string('contact_person_name')->nullable();
                $t->string('contact_number')->nullable();
                $t->string('commission')->nullable();
                $t->string('payout')->nullable();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('client_contact_info')) {
            Schema::create('client_contact_info', function (Blueprint $t) {
                $t->id();
                $t->integer('client_id');
                $t->string('contact_person_name')->nullable();
                $t->string('contact_person_email')->nullable();
                $t->string('contact_person_designation')->nullable();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('client_contact_info_jobs')) {
            Schema::create('client_contact_info_jobs', function (Blueprint $t) {
                $t->id();
                $t->integer('company_id')->nullable();
                $t->integer('job_id');
                $t->integer('client_contact_info_id');
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('jobs')) {
            Schema::create('jobs', function (Blueprint $t) {
                $t->id();
                $t->integer('company_id');
                $t->integer('client_id')->nullable();
                $t->string('title')->nullable();
                $t->text('job_description')->nullable();
                $t->text('job_requirement')->nullable();
                $t->integer('total_positions')->default(1);
                $t->timestamp('start_date')->nullable();
                $t->timestamp('end_date')->nullable();
                $t->string('status')->default('active');
                $t->text('required_columns')->nullable();
                $t->text('meta_details')->nullable();
                $t->text('section_visibility')->nullable();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('recommendation_relation')) {
            Schema::create('recommendation_relation', function (Blueprint $t) {
                $t->id();
                $t->integer('from_company_id')->nullable();
                $t->integer('from_user_id')->nullable();
                $t->integer('to_company_id')->nullable();
                $t->integer('job_id')->nullable();
                $t->integer('final_status')->nullable();
                $t->timestamps();
            });
        }
    }

    public function test_unassociated_job_defaults_to_my_organization_with_zero_client_contacts(): void
    {
        $testCompanyId = 99881;
        $user = new User;
        $user->id = 9988101;
        $user->company_id = $testCompanyId;

        $result = $this->service->resolveAuthorizedRecipients($user, null, []);

        $this->assertSame('my_organization', $result['job_type']);
        $this->assertNull($result['job_id']);
        $this->assertIsArray($result['internal_team']);
        $this->assertEmpty($result['client_contacts']);
        $this->assertEmpty($result['default_to_emails']);
    }

    public function test_my_organization_job_returns_only_my_org_and_zero_client_contacts(): void
    {
        $testCompanyId = 99882;
        $user = new User;
        $user->id = 9988201;
        $user->company_id = $testCompanyId;

        // Create test client with self_company = 1 (My Organization client)
        $selfClientId = DB::table('clients')->insertGetId([
            'company_id' => $testCompanyId,
            'self_company' => 1,
            'name' => 'Internal Org Client',
            'website' => 'https://org.example.com',
            'email' => 'org@example.com',
            'contact_person_name' => 'Org Rep',
            'contact_number' => '1234567890',
            'commission' => '0',
            'payout' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Create job under My Organization
        $jobId = DB::table('jobs')->insertGetId([
            'company_id' => $testCompanyId,
            'client_id' => $selfClientId,
            'title' => 'Software Engineer My Org',
            'job_description' => 'Test Desc',
            'job_requirement' => 'Test Req',
            'total_positions' => 1,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active',
            'required_columns' => '[]',
            'meta_details' => '[]',
            'section_visibility' => '[]',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $result = $this->service->resolveAuthorizedRecipients($user, $jobId, []);

            $this->assertSame('my_organization', $result['job_type']);
            $this->assertSame($jobId, $result['job_id']);
            $this->assertEmpty($result['client_contacts'], 'My Organization job must strictly return 0 client contacts.');
            $this->assertEmpty($result['default_to_emails']);
        } finally {
            DB::table('jobs')->where('id', $jobId)->delete();
            DB::table('clients')->where('id', $selfClientId)->delete();
        }
    }

    public function test_internal_client_job_returns_strictly_associated_client_contacts(): void
    {
        $testCompanyId = 99883;
        $user = new User;
        $user->id = 9988301;
        $user->company_id = $testCompanyId;

        // Client A (Job associated)
        $clientAId = DB::table('clients')->insertGetId([
            'company_id' => $testCompanyId,
            'self_company' => 0,
            'name' => 'Client Alpha',
            'website' => 'https://alpha.example.com',
            'email' => 'alpha@example.com',
            'contact_person_name' => 'Alpha Rep',
            'contact_number' => '1111111111',
            'commission' => '0',
            'payout' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Client B (Unrelated client of the same company)
        $clientBId = DB::table('clients')->insertGetId([
            'company_id' => $testCompanyId,
            'self_company' => 0,
            'name' => 'Client Beta',
            'website' => 'https://beta.example.com',
            'email' => 'beta@example.com',
            'contact_person_name' => 'Beta Rep',
            'contact_number' => '2222222222',
            'commission' => '0',
            'payout' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Contact A for Client Alpha
        $contactAId = DB::table('client_contact_info')->insertGetId([
            'client_id' => $clientAId,
            'contact_person_name' => 'Alice Alpha',
            'contact_person_email' => 'alice@alpha.example.com',
            'contact_person_designation' => 'Lead Recruiter',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Contact B for Client Beta (MUST NOT APPEAR)
        $contactBId = DB::table('client_contact_info')->insertGetId([
            'client_id' => $clientBId,
            'contact_person_name' => 'Bob Beta',
            'contact_person_email' => 'bob@beta.example.com',
            'contact_person_designation' => 'HR Manager',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Job for Client Alpha
        $jobId = DB::table('jobs')->insertGetId([
            'company_id' => $testCompanyId,
            'client_id' => $clientAId,
            'title' => 'Backend Dev Internal Client',
            'job_description' => 'Test Desc',
            'job_requirement' => 'Test Req',
            'total_positions' => 1,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active',
            'required_columns' => '[]',
            'meta_details' => '[]',
            'section_visibility' => '[]',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $result = $this->service->resolveAuthorizedRecipients($user, $jobId, []);

            $this->assertSame('internal_client', $result['job_type']);
            $this->assertSame($jobId, $result['job_id']);

            $clientEmails = collect($result['client_contacts'])->pluck('email')->toArray();
            $this->assertContains('alice@alpha.example.com', $clientEmails);
            $this->assertNotContains('bob@beta.example.com', $clientEmails, 'Unrelated client contacts must never leak into drawer.');
        } finally {
            DB::table('jobs')->where('id', $jobId)->delete();
            DB::table('client_contact_info')->whereIn('id', [$contactAId, $contactBId])->delete();
            DB::table('clients')->whereIn('id', [$clientAId, $clientBId])->delete();
        }
    }

    public function test_direct_client_job_returns_strictly_only_hiring_manager_and_agency_adder(): void
    {
        $agencyCompanyId = 99884;
        $directClientCompanyId = 77884;

        $user = new User;
        $user->id = 9988401;
        $user->company_id = $agencyCompanyId;

        // User who added the agency
        $adderUserId = DB::table('users')->insertGetId([
            'company_id' => $directClientCompanyId,
            'name' => 'Agency Adder Person',
            'email' => 'adder@directclient.example.com',
            'password' => bcrypt('password'),
            'status' => 'active',
            'working_status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Direct Client record
        $clientId = DB::table('clients')->insertGetId([
            'company_id' => $directClientCompanyId,
            'self_company' => 0,
            'name' => 'Direct Client Corp',
            'website' => 'https://directclient.example.com',
            'email' => 'info@directclient.example.com',
            'contact_person_name' => 'Client Rep',
            'contact_number' => '3333333333',
            'commission' => '0',
            'payout' => '0',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Contact 1: Designated Hiring Manager
        $hmContactId = DB::table('client_contact_info')->insertGetId([
            'client_id' => $clientId,
            'contact_person_name' => 'Harry HiringManager',
            'contact_person_email' => 'harry.hm@directclient.example.com',
            'contact_person_designation' => 'Engineering Manager',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Contact 2: Other Direct Client Employee (MUST NOT APPEAR)
        $unrelatedContactId = DB::table('client_contact_info')->insertGetId([
            'client_id' => $clientId,
            'contact_person_name' => 'Other Employee',
            'contact_person_email' => 'other.emp@directclient.example.com',
            'contact_person_designation' => 'HR Staff',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Direct Client Job
        $jobId = DB::table('jobs')->insertGetId([
            'company_id' => $directClientCompanyId,
            'client_id' => $clientId,
            'title' => 'Senior Architect Direct Client',
            'job_description' => 'Test Desc',
            'job_requirement' => 'Test Req',
            'total_positions' => 1,
            'start_date' => now(),
            'end_date' => now()->addDays(30),
            'status' => 'active',
            'required_columns' => '[]',
            'meta_details' => '[]',
            'section_visibility' => '[]',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign Hiring Manager to Job in client_contact_info_jobs
        $hmJobId = DB::table('client_contact_info_jobs')->insertGetId([
            'company_id' => $directClientCompanyId,
            'client_contact_info_id' => $hmContactId,
            'job_id' => $jobId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Assign Job to Agency in recommendation_relation
        $recRelId = DB::table('recommendation_relation')->insertGetId([
            'from_company_id' => $directClientCompanyId,
            'from_user_id' => $adderUserId,
            'to_company_id' => $agencyCompanyId,
            'job_id' => $jobId,
            'final_status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $result = $this->service->resolveAuthorizedRecipients($user, $jobId, []);

            $this->assertSame('direct_client', $result['job_type']);
            $this->assertSame($jobId, $result['job_id']);

            $clientEmails = collect($result['client_contacts'])->pluck('email')->toArray();
            $this->assertContains('harry.hm@directclient.example.com', $clientEmails);
            $this->assertContains('adder@directclient.example.com', $clientEmails);
            $this->assertNotContains('other.emp@directclient.example.com', $clientEmails, 'Other direct client employees must be strictly excluded.');
            $this->assertCount(2, $result['client_contacts'], 'Direct Client job must return strictly only Hiring Manager + Agency Added By.');

            // Check role tags
            $hmItem = collect($result['client_contacts'])->firstWhere('email', 'harry.hm@directclient.example.com');
            $this->assertSame('Hiring Manager', $hmItem->role_tag);

            $adderItem = collect($result['client_contacts'])->firstWhere('email', 'adder@directclient.example.com');
            $this->assertSame('Agency Added By', $adderItem->role_tag);
        } finally {
            DB::table('recommendation_relation')->where('id', $recRelId)->delete();
            DB::table('client_contact_info_jobs')->where('id', $hmJobId)->delete();
            DB::table('jobs')->where('id', $jobId)->delete();
            DB::table('client_contact_info')->whereIn('id', [$hmContactId, $unrelatedContactId])->delete();
            DB::table('clients')->where('id', $clientId)->delete();
            DB::table('users')->where('id', $adderUserId)->delete();
        }
    }
}
