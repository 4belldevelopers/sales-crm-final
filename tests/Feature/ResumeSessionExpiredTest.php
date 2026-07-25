<?php

namespace Tests\Feature;

use App\Models\ResumeUpload;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Phase 9.2 — show() returns 404 when expires_at is in the past.
 *
 * Why: the localStorage session cache can rehydrate a stale
 * session UUID long after the server has expired it. If we
 * returned 200 for an expired row, the submit-side recovery modal
 * would read linked=false (or even linked=true with a stale
 * candidate_id) and either:
 *   - force a re-submit → duplicate candidate (no linked)
 *   - mis-redirect to a stale candidate (linked)
 *
 * The contract: expired = 404, indistinguishable from "never
 * existed" or "wrong tenant".
 */
class ResumeSessionExpiredTest extends TestCase
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
            // expires_at is a datetime; cast on the model.
            $t->timestamp('expires_at')->nullable();
            $t->timestamps();
        });
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

    private function seedSession(int $companyId, ?\Carbon\Carbon $expiresAt = null): ResumeUpload
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
        if ($expiresAt instanceof \Carbon\Carbon) {
            $row->expires_at = $expiresAt;
        }
        $row->save();
        return $row;
    }

    public function test_show_returns_404_when_expires_at_is_in_past(): void
    {
        $user = $this->seedUser(7, 'expired@example.com');
        // saved is 1 hour in the past.
        $row = $this->seedSession(7, Carbon::now()->subHour());

        $response = $this->actingAs($user, 'web')
            ->getJson('/service_company/api/resume-sessions/' . $row->uuid);

        $response->assertStatus(404);
    }

    public function test_show_returns_200_when_expires_at_is_in_future(): void
    {
        $user = $this->seedUser(7, 'fresh@example.com');
        // saved is 1 hour in the future.
        $row = $this->seedSession(7, Carbon::now()->addHour());

        $response = $this->actingAs($user, 'web')
            ->getJson('/service_company/api/resume-sessions/' . $row->uuid);

        $response->assertStatus(200);
        $response->assertJsonPath('session.session_uuid', $row->uuid);
    }

    public function test_show_returns_200_when_expires_at_is_null(): void
    {
        $user = $this->seedUser(7, 'no-expiry@example.com');
        // No expires_at — schema accepts null. Status Parsed but
        // no expiry clock should still resolve to a recoverable
        // session.
        $row = $this->seedSession(7, null);

        $response = $this->actingAs($user, 'web')
            ->getJson('/service_company/api/resume-sessions/' . $row->uuid);

        $response->assertStatus(200);
    }
}
