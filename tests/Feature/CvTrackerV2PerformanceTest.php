<?php

namespace Tests\Feature;

/**
 * Class CvTrackerV2PerformanceTest
 *
 * INTENT: Automated performance and feature test for CV Tracker V2 listing endpoint.
 * Asserts permission gate security, response structure, zero-DB-query read path, and latency benchmarks.
 */

use App\Models\Company;
use App\Models\User;
use App\Services\CvTrackerService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class CvTrackerV2PerformanceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['auth.single_device_login.enabled' => false]);
        $this->buildSchema();
    }

    private function buildSchema(): void
    {
        if (! Schema::hasTable('companies')) {
            Schema::create('companies', function ($t) {
                $t->increments('id');
                $t->string('company_name', 255)->nullable();
                $t->string('company_type', 50)->default('service');
                $t->string('status', 32)->default('active');
                $t->timestamps();
            });
        }

        Company::forceCreate([
            'id' => 1,
            'company_name' => 'Acme Test',
            'company_type' => 'service',
            'status' => 'active',
        ]);

        if (! Schema::hasTable('users')) {
            Schema::create('users', function ($t) {
                $t->increments('id');
                $t->unsignedInteger('company_id')->nullable();
                $t->string('name', 191)->nullable();
                $t->string('email', 191)->nullable();
                $t->string('status', 32)->default('active');
                $t->integer('root_user')->default(0);
                $t->integer('working_status')->default(0);
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

        if (! Schema::hasTable('currencies')) {
            Schema::create('currencies', function ($t) {
                $t->increments('id');
                $t->string('currency_code', 10);
                $t->string('currency_symbol', 10);
                $t->decimal('inr_based_rates', 10, 4)->default(1.0);
                $t->decimal('usd_based_rates', 10, 4)->default(1.0);
                $t->integer('getKey')->default(0);
                $t->timestamps();
            });
            DB::table('currencies')->insert([
                'currency_code' => 'INR',
                'currency_symbol' => '₹',
                'inr_based_rates' => 1.0,
                'usd_based_rates' => 0.012,
                'getKey' => 0,
            ]);
        }
        if (! Schema::hasTable('countries')) {
            Schema::create('countries', function ($t) {
                $t->increments('id');
                $t->string('country_name', 100);
                $t->string('country_code', 10)->nullable();
                $t->timestamps();
            });
            DB::table('countries')->insert([
                'id' => 101,
                'country_name' => 'India',
                'country_code' => 'IN',
            ]);
        }

        \Illuminate\Support\Facades\Cache::forever('currency_codesww', [0 => 'INR']);
        \Illuminate\Support\Facades\Cache::forever('currency_symbols_all', [0 => '₹', 'INR' => '₹']);
        \Illuminate\Support\Facades\Cache::forever('ref:interview_sub_status:all', []);
        \Illuminate\Support\Facades\Cache::forever('ref_country_name:101', [
            ['id' => 101, 'name' => 'India', 'sortname' => 'IN'],
        ]);
        \Illuminate\Support\Facades\Cache::forever('ref_job_client_comp:1_301', 'Acme Corp');
        \Illuminate\Support\Facades\Cache::forever('currencyConverter_all', [
            'getKey' => [0 => 'INR'],
            'inr_based_rates' => ['INR' => 1.0],
            'usd_based_rates' => ['INR' => 0.012],
            'currencies' => ['INR' => '₹'],
        ]);
    }

    /**
     * Test V2 candidate listing endpoint returns 403 Forbidden when view_job_applications gate is denied.
     */
    public function test_v2_endpoint_requires_permission_gate(): void
    {
        Gate::define('view_job_applications', fn () => false);

        $user = User::forceCreate([
            'id' => 1,
            'company_id' => 1,
            'name' => 'Recruiter 1',
            'email' => 'recruiter1@test.com',
            'status' => 'active',
        ]);

        $response = $this->actingAs($user)->getJson(route('service_company.cvtracker.v2.allcandidates'));

        $response->assertStatus(403);
    }

    /**
     * Test V2 candidate listing endpoint executes ZERO read database queries when data is cached/served from ES.
     */
    public function test_v2_endpoint_executes_zero_mysql_queries(): void
    {
        Gate::define('view_job_applications', fn () => true);

        $user = User::forceCreate([
            'id' => 2,
            'company_id' => 1,
            'name' => 'Recruiter 2',
            'email' => 'recruiter2@test.com',
            'status' => 'active',
            'root_user' => 1,
        ]);

        $mockService = \Mockery::mock(CvTrackerService::class);
        $mockService->shouldReceive('fetchCandidates')
            ->atLeast()
            ->once()
            ->andReturn([
                'data' => [
                    [
                        'candidate_id' => 101,
                        'wiseId' => 201,
                        'jobId' => 301,
                        'job_max_sal_mode' => 0,
                        'job_max_salary_currency' => 0,
                        'job_skills' => ['php', 'laravel'],
                        'full_name' => 'John Doe',
                        'skills' => 'PHP, Laravel',
                        'total_exp' => 5,
                        'rel_exp' => 3,
                        'current_designation' => 'Lead Engineer',
                        'current_company' => 'Tech Corp',
                        'qualification_name' => 'B.Tech',
                        'ctc_yearly' => 1200000,
                        'ectc_yearly' => 1500000,
                        'current_ctc_mode' => 0,
                        'current_ctc_cur' => 0,
                        'job_master_status_id' => 1,
                        'name' => 'Sourcing',
                        'css_style' => 'badge-light-primary',
                        'child_status_name' => 'Shortlisted',
                        'childStatus' => 1,
                        'title' => 'Software Engineer',
                        'client_name' => 'Acme Corp',
                        'client_comapny_id' => 1,
                        'city_name' => 'Mumbai',
                        'city' => 'Mumbai',
                        'city_id' => 1,
                        'country' => 101,
                        'posted_by' => 'Jane Recruiter',
                        'ClientComID' => 0,
                        'rec_candidate_assign_id' => 0,
                        'final_status' => 0,
                        'recommendation_id' => 0,
                        'resume' => 'resume.pdf',
                        'photo' => '',
                        'np_id' => 'Immediate',
                        'serving_notice_period_mode' => 0,
                        'duplicate' => 0,
                        'condidate_type' => 0,
                        'to_company_id' => 0,
                        'company_name' => 'Acme',
                        'apl_company_name' => '',
                        'post_company_name' => '',
                        'submitted_by_company_name' => '',
                        'spokperson_name' => '',
                        'posted_by_image' => '',
                        'thirdparty_company_id' => 0,
                        'job_status' => 'active',
                    ],
                ],
                'total' => 1,
                'current_page' => 1,
                'last_page' => 1,
            ]);
        $this->app->instance(CvTrackerService::class, $mockService);

        // Pre-warm framework permission and currency static registries
        $this->app->make(\Spatie\Permission\PermissionRegistrar::class)->getPermissions();
        \App\Helper\GeneralMethod::currencyConverter();

        // Warm up blade view compilation
        $this->actingAs($user)->getJson(route('service_company.cvtracker.v2.allcandidates', [
            'page' => 1,
            'size' => 20,
            'sortingfilter' => 1,
        ]));

        DB::flushQueryLog();
        DB::enableQueryLog();

        $startTime = microtime(true);
        $response = $this->actingAs($user)->getJson(route('service_company.cvtracker.v2.allcandidates', [
            'page' => 1,
            'size' => 20,
            'sortingfilter' => 1,
        ]));
        $executionTimeMs = (microtime(true) - $startTime) * 1000;

        $queryLog = DB::getQueryLog();

        // Assert response status and structure
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'meta' => ['total', 'current_page', 'last_page'],
            'data',
        ]);

        // Assert read-path query count is 0
        $this->assertCount(0, $queryLog, 'V2 read path must execute 0 MySQL database queries.');

        // Assert latency benchmark (< 100ms)
        $this->assertLessThan(100, $executionTimeMs, 'V2 candidate listing warm response time must be under 100ms.');
    }
}
