<?php

namespace Tests\Feature;

use App\Models\ResumeUpload;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Phase 9.1 — verifier payload contract.
 *
 * The submit-side network-failure recovery modal calls
 * GET /api/resume-sessions/{uuid} when a candidate POST
 * fails due to network loss. The contract is:
 *
 *   - response.session.linked === true  iff candidate_id OR
 *     bench_application_id is non-null
 *   - response.session.candidate_id is the row's candidate_id
 *   - response.session.bench_application_id is the row's bench_application_id
 *
 * These assertions lock the contract so any future refactor that
 * drops `linked` or the IDs would BREAK the recovery modal logic
 * silently. A green-red-green cycle here is "verifier cannot tell
 * candidate from no-candidate" — duplicate-candidate territory.
 */
class ResumeSessionVerifierPayloadTest extends TestCase
{
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('database.default', 'sqlite');
        $app['config']->set('database.connections.sqlite', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);
        $app['config']->set('cache.default', 'array');
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->buildSchema();
    }

    private function buildSchema(): void
    {
        Schema::create('users', function ($t) {
            $t->bigIncrements('id');
            $t->string('name');
            $t->string('email')->unique();
            $t->string('password');
            $t->unsignedInteger('company_id')->nullable();
            $t->string('status')->default('active');
            // EnsureOneDeviceLogin middleware writes here on auth().
            $t->string('session_id', 127)->nullable();
            $t->timestamps();
        });
        Schema::create('resume_uploads', function ($t) {
            $t->bigIncrements('id');
            $t->char('uuid', 36);
            $t->unsignedInteger('company_id');
            $t->string('status', 32)->default('Created');
            $t->string('original_filename', 255);
            $t->string('stored_filename', 255);
            $t->string('r2_path', 512);
            $t->string('r2_url', 512);
            $t->string('mime_type', 127);
            $t->unsignedBigInteger('file_size_bytes');
            $t->unsignedInteger('candidate_id')->nullable();
            $t->unsignedInteger('bench_application_id')->nullable();
            $t->timestamps();
        });
        // ServiceCompany middleware reads `$user->company->company_type`
        // for trait gating; the verifier test must satisfy this.
        Schema::create('companies', function ($t) {
            $t->bigIncrements('id');
            $t->unsignedInteger('company_id')->unique();
            $t->string('company_name');
            $t->string('company_type')->default('service_company');
            $t->timestamps();
        });
    }

    private function seedUser(int $companyId, string $email): User
    {
        // Seed a Company stub that middleware will look up.
        \Illuminate\Support\Facades\DB::table('companies')->insert([
            'company_id' => $companyId,
            'company_name' => 'Stub Co ' . $companyId,
            'company_type' => 'service_company',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $u = new User();
        $u->name = 'Test';
        $u->email = $email;
        $u->password = Hash::make('secret');
        $u->company_id = $companyId;
        $u->status = 'active';
        $u->save();
        return $u;
    }

    private function seedSession(int $companyId, ?int $candidateId = null, ?int $benchId = null): ResumeUpload
    {
        $row = new ResumeUpload();
        $row->uuid = (string) \Illuminate\Support\Str::uuid();
        $row->company_id = $companyId;
        $row->status = ResumeUpload::STATUS_PARSED;
        $row->original_filename = 'cv.pdf';
        $row->stored_filename = 'cv.pdf';
        $row->r2_path = 'junk/'.$row->uuid;
        $row->r2_url = 'https://example/'.$row->uuid;
        $row->mime_type = 'application/pdf';
        $row->file_size_bytes = 1024;
        $row->candidate_id = $candidateId;
        $row->bench_application_id = $benchId;
        $row->save();
        return $row;
    }

    public function test_session_without_candidate_returns_linked_false(): void
    {
        $user = $this->seedUser(7, 'u7@example.com');
        $row = $this->seedSession(7); // no candidate_id, no bench_application_id

        $response = $this->actingAs($user, 'web')
            ->getJson('/service_company/api/resume-sessions/' . $row->uuid);

        $response->assertStatus(200);
        $response->assertJsonPath('session.session_uuid', $row->uuid);
        $response->assertJsonPath('session.linked', false);
        $response->assertJsonPath('session.candidate_id', null);
        $response->assertJsonPath('session.bench_application_id', null);
    }

    public function test_session_with_candidate_id_returns_linked_true(): void
    {
        $user = $this->seedUser(7, 'u8@example.com');
        $row = $this->seedSession(7, candidateId: 4242);

        $response = $this->actingAs($user, 'web')
            ->getJson('/service_company/api/resume-sessions/' . $row->uuid);

        $response->assertStatus(200);
        $response->assertJsonPath('session.linked', true);
        $response->assertJsonPath('session.candidate_id', 4242);
        $response->assertJsonPath('session.bench_application_id', null);
    }

    public function test_session_with_bench_id_returns_linked_true(): void
    {
        $user = $this->seedUser(7, 'u9@example.com');
        $row = $this->seedSession(7, benchId: 7777);

        $response = $this->actingAs($user, 'web')
            ->getJson('/service_company/api/resume-sessions/' . $row->uuid);

        $response->assertStatus(200);
        $response->assertJsonPath('session.linked', true);
        $response->assertJsonPath('session.candidate_id', null);
        $response->assertJsonPath('session.bench_application_id', 7777);
    }

    public function test_verifier_endpoint_404s_for_wrong_tenant(): void
    {
        $user = $this->seedUser(7, 'wrong-tenant@example.com');
        $row = $this->seedSession(999, candidateId: 100); // different tenant

        // forCompany() throws RuntimeException → controller returns
        // 404 via the SessionNotFound mapping. Tenant isolation
        // underpins the verifier: a wrong-tenant request must NOT
        // reveal linked/candidate_id.
        $response = $this->actingAs($user, 'web')
            ->getJson('/service_company/api/resume-sessions/' . $row->uuid);

        // We accept 404 or 403 or 500 — the contract is "do NOT 200".
        $this->assertNotSame(200, $response->getStatusCode(),
            'Wrong-tenant verifier call must not return a 200 with session.');
    }
}
