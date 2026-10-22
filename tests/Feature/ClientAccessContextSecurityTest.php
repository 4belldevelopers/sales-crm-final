<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ServiceCompany\ClientJobsReadService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * ClientAccessContextSecurityTest
 *
 * Verifies Multi-Tenant Security & The Triple Attack Vector Security Suite (P0):
 * - Test A: Cross-tenant forged client ID -> 403 Forbidden -> 0 ES / ReadService calls.
 * - Test B: Cross-partner direct client spoofing -> 403 Forbidden -> 0 ES / ReadService calls.
 * - Test C: Unauthorized / unauthenticated requisition isolation via defense-in-depth boundary.
 *
 * Tenancy: Scoped to authenticated user company boundary.
 */
class ClientAccessContextSecurityTest extends TestCase
{
    use DatabaseTransactions;

    protected User $testUser;

    protected function setUp(): void
    {
        parent::setUp();

        config(['auth.single_device_login.enabled' => false]);
        Gate::define('view_jobs', fn () => true);

        if (! Schema::hasTable('users')) {
            Schema::create('users', function ($t) {
                $t->increments('id');
                $t->unsignedInteger('company_id')->nullable();
                $t->string('name', 191)->nullable();
                $t->string('email', 191)->nullable();
                $t->timestamp('email_verified_at')->nullable();
                $t->string('password', 191)->nullable();
                $t->string('role', 100)->nullable();
                $t->string('status', 32)->default('active');
                $t->tinyInteger('root_user')->default(1);
                $t->rememberToken();
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('permissions')) {
            Schema::create('permissions', function ($t) {
                $t->increments('id');
                $t->string('name', 191);
                $t->string('guard_name', 191)->default('web');
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('roles')) {
            Schema::create('roles', function ($t) {
                $t->increments('id');
                $t->string('name', 191);
                $t->string('guard_name', 191)->default('web');
                $t->timestamps();
            });
        }

        if (! Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function ($t) {
                $t->unsignedInteger('permission_id');
                $t->string('model_type', 191);
                $t->unsignedInteger('model_id');
            });
        }

        if (! Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function ($t) {
                $t->unsignedInteger('role_id');
                $t->string('model_type', 191);
                $t->unsignedInteger('model_id');
            });
        }

        $user = new User();
        $user->id = 8888;
        $user->company_id = 100;
        $user->name = 'Security Test Recruiter';
        $user->email = 'security-test@jero.works';
        $user->status = 'active';
        $user->root_user = 1;
        $user->save();
        $this->testUser = $user;
    }

    /**
     * Test A: Forged client_id outside tenant boundary must return HTTP 403
     * and MUST NEVER invoke Elasticsearch or construct query.
     */
    public function test_attack_vector_a_forged_client_id_returns_403_and_zero_es_calls(): void
    {
        // Mock Read Service to verify it is NEVER invoked on 403
        $mockReadService = $this->createMock(ClientJobsReadService::class);
        $mockReadService->expects($this->never())->method('getJobs');
        $this->app->instance(ClientJobsReadService::class, $mockReadService);

        // Pre-set unauthorized status in auth cache for client 999999
        $authCacheKey = "ats:auth_context:internal:{$this->testUser->company_id}:999999";
        Redis::setex($authCacheKey, 60, 0);

        $response = $this->withoutMiddleware([
            \App\Http\Middleware\EnsureOneDeviceLogin::class,
            \App\Http\Middleware\CustomPackage::class,
        ])
            ->actingAs($this->testUser)
            ->getJson('/service_company/jobs_side_bar_job_ajax/data?client_id=999999&custom=true');

        $response->assertStatus(403);
    }

    /**
     * Test B: Cross-Partner Direct Client Spoofing must return HTTP 403.
     */
    public function test_attack_vector_b_cross_partner_spoofing_returns_403(): void
    {
        $mockReadService = $this->createMock(ClientJobsReadService::class);
        $mockReadService->expects($this->never())->method('getJobs');
        $this->app->instance(ClientJobsReadService::class, $mockReadService);

        // Client belongs to Partner D, but request claims Partner B
        $spoofedClientId = 88888;
        $unauthorizedPartnerId = 77777;
        $authCacheKey = "ats:auth_context:direct:{$this->testUser->company_id}:{$unauthorizedPartnerId}:{$spoofedClientId}";
        Redis::setex($authCacheKey, 60, 0);

        $response = $this->withoutMiddleware([
            \App\Http\Middleware\EnsureOneDeviceLogin::class,
            \App\Http\Middleware\CustomPackage::class,
        ])
            ->actingAs($this->testUser)
            ->getJson("/service_company/jobs_side_bar_job_ajax/data?client_id={$spoofedClientId}&partner_company_id={$unauthorizedPartnerId}&custom=false");

        $response->assertStatus(403);
    }

    /**
     * Test C: Unauthenticated request must return 401 or 403.
     */
    public function test_attack_vector_c_unauthenticated_request_is_blocked(): void
    {
        $response = $this->getJson('/service_company/jobs_side_bar_job_ajax/data?client_id=123&custom=true');

        $this->assertContains($response->status(), [401, 403]);
    }
}
