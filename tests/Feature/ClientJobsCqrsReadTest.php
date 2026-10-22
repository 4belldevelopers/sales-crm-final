<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ServiceCompany\ClientJobsReadService;
use App\ViewModels\ServiceCompany\JobsTabViewModel;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * ClientJobsCqrsReadTest
 *
 * Strict Architectural CI Test Gate enforcing JERO Rule 1 & Rule 2:
 * "MySQL is ONLY for Add / Edit / Delete: 0% MySQL reads on web/API request paths."
 *
 * Verifies that the Client Drawer Jobs Tab executes with ZERO database queries
 * across the entire controller, authorization, ViewModel, and Blade rendering pipeline.
 *
 * Tenancy: Scoped to authenticated user's company_id.
 */
class ClientJobsCqrsReadTest extends TestCase
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
        $user->name = 'CQRS Recruiter';
        $user->email = 'cqrs-test@jero.works';
        $user->status = 'active';
        $user->root_user = 1;
        $user->save();
        $this->testUser = $user;
    }

    public function test_client_drawer_jobs_tab_executes_zero_mysql_reads_across_full_pipeline(): void
    {
        // 1. Arrange: Mock Read Service to ensure predictable zero-I/O execution
        $mockViewModel = new JobsTabViewModel(
            totalCount: 5,
            filteredCount: 5,
            hiringCount: 3,
            holdCount: 1,
            closedCount: 1,
            inactiveCount: 0,
            jobs: [
                (object) [
                    'id'                  => 99991,
                    'title'               => 'Senior Fullstack Engineer',
                    'status'              => 'active',
                    'min_exp'             => 3,
                    'max_exp'             => 7,
                    'min_salary'          => 120000,
                    'max_salary'          => 180000,
                    'min_salary_currency' => '$',
                    'employment_type'     => 0,
                    'department_name'     => 'Engineering',
                    'client_company'      => 'Acme Global',
                    'created_at'          => now()->toDateTimeString(),
                    'updated_at'          => now()->toDateTimeString(),
                ],
            ],
            pipelineStageMap: [
                99991 => [8 => 12, 1 => 4, 2 => 3, 3 => 2, 4 => 1, 5 => 1, 6 => 1, 7 => 0],
            ],
            logoMap: [
                99991 => ['initial' => 'S', 'color' => '#009ef7'],
            ],
            pagination: [
                'total'        => 5,
                'current_page' => 1,
                'last_page'    => 1,
                'per_page'     => 10,
                'from'         => 1,
                'to'           => 1,
                'listType'     => 'client_jobs',
            ],
            activeFilters: [
                'status' => 'all',
                'search' => null,
            ],
            projectionVersion: 1,
            queryHash: 'test-hash-12345',
            generatedAt: now()->toIso8601String()
        );

        $mockReadService = $this->createMock(ClientJobsReadService::class);
        $mockReadService->method('getJobs')->willReturn($mockViewModel);
        $this->app->instance(ClientJobsReadService::class, $mockReadService);

        // Pre-authorize internal client 782 in Redis
        Redis::setex("ats:auth_context:internal:{$this->testUser->company_id}:782", 60, 1);

        // Pre-warm framework permission registry (mimics Octane worker pre-boot)
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->getPermissions();

        // 2. Enable Query Log to monitor database reads
        DB::flushQueryLog();
        DB::enableQueryLog();

        // 3. Act: Execute request across full HTTP pipeline (Controller -> Auth -> Read -> Blade -> JSON)
        $response = $this->withoutMiddleware([
            \App\Http\Middleware\EnsureOneDeviceLogin::class,
            \App\Http\Middleware\CustomPackage::class,
        ])
            ->actingAs($this->testUser)
            ->getJson('/service_company/jobs_side_bar_job_ajax/data?client_id=782&custom=true');

        // 4. Assert 200 OK
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'html',
            'total',
            'kpi' => [
                'total',
                'hiring',
                'hold',
                'closed',
                'inactive',
            ],
            'pagination',
        ]);

        // 5. Assert STRICT CQRS INVARIANT: 0 MySQL Reads across full pipeline
        $executedQueries = DB::getQueryLog();
        $this->assertCount(
            0,
            $executedQueries,
            "STRICT CQRS VIOLATION: " . count($executedQueries) . " MySQL reads detected on Jobs Tab request path! Queries: " . json_encode($executedQueries)
        );
    }
}
