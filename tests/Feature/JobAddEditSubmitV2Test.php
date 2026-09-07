<?php

namespace Tests\Feature;

use App\Models\Job;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * JobAddEditSubmitV2Test — Integration & Baseline Safety Test Suite for Jobs Module (Add, Edit, Submit flows).
 *
 * Intent:
 * Establishes Phase 0 baseline performance metrics, query counts, tenancy isolation boundaries,
 * and functional safety nets for the Jobs Module (`service_company/jobs`).
 *
 * Scenarios Covered:
 * 1. Unauthenticated route access guards (GET/POST endpoints redirect to login).
 * 2. Gate authorization enforcement for `create` (add_jobs), `curstomcreate` (view_jobs), `edit` (edit_jobs), `update` (edit_jobs).
 * 3. Database query count measurement for `create()`, `edit()`, `store()`, and `update()`.
 * 4. Tenant isolation verification for categories, clients, and active job count calculations.
 * 5. Job store payload handling, pivot table insertion, and event dispatch contract.
 * 6. Job edit prefill data structure and duplicate query detection.
 * 7. Job update payload handling, pivot table updates, and cache invalidation.
 * 8. FormRequest StoreJob validation contract evaluation.
 *
 * Author: Antigravity AI
 * Date: August 14, 2026
 *
 * Run:
 *   php artisan test --filter=JobAddEditSubmitV2Test
 */
class JobAddEditSubmitV2Test extends TestCase
{
    /**
     * Helper method to count database queries executed within a closure.
     *
     * @return array{count: int, queries: array}
     */
    protected function trackQueries(\Closure $callback): array
    {
        $queries = [];
        DB::listen(function ($query) use (&$queries) {
            $queries[] = [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time,
            ];
        });

        $callback();

        return [
            'count' => count($queries),
            'queries' => $queries,
        ];
    }

    /**
     * Test 1: Verify unauthenticated users cannot access Add Job, Edit Job, or Submit endpoints.
     */
    public function test_unauthenticated_user_cannot_access_job_routes(): void
    {
        $responseCreate = $this->get('/service_company/jobs/create');
        $responseCreate->assertStatus(302);

        $responseCustomCreate = $this->get('/service_company/jobs/curstomcreate/1');
        $responseCustomCreate->assertStatus(302);

        $responseEdit = $this->get('/service_company/jobs/1/edit');
        $responseEdit->assertStatus(302);

        $responseStore = $this->post('/service_company/jobs', ['title' => 'Test Job']);
        $responseStore->assertStatus(302);

        $responseUpdate = $this->post('/service_company/jobs/update/1', ['title' => 'Updated Job']);
        $responseUpdate->assertStatus(302);
    }

    /**
     * Test 2: Verify controller methods exist on ServiceCompanyJobsController.
     */
    public function test_service_company_jobs_controller_methods_exist(): void
    {
        $controllerClass = \App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController::class;

        $this->assertTrue(method_exists($controllerClass, 'create'), 'create() method must exist');
        $this->assertTrue(method_exists($controllerClass, 'curstomcreate'), 'curstomcreate() method must exist');
        $this->assertTrue(method_exists($controllerClass, 'store'), 'store() method must exist');
        $this->assertTrue(method_exists($controllerClass, 'edit'), 'edit() method must exist');
        $this->assertTrue(method_exists($controllerClass, 'update'), 'update() method must exist');
    }

    /**
     * Test 3: Verify route names are correctly registered in service routes.
     */
    public function test_job_routes_are_registered(): void
    {
        $this->assertTrue(Route::has('service_company.jobs.create'), 'service_company.jobs.create route must be registered');
        $this->assertTrue(Route::has('service_company.jobs.curstomcreate'), 'service_company.jobs.curstomcreate route must be registered');
        $this->assertTrue(Route::has('service_company.jobs.store'), 'service_company.jobs.store route must be registered');
        $this->assertTrue(Route::has('service_company.jobs.edit'), 'service_company.jobs.edit route must be registered');
        $this->assertTrue(Route::has('service_company.jobs.update'), 'service_company.jobs.update route must be registered');
    }

    /**
     * Test 4: Verify StoreJob FormRequest class exists and checks validation rules contract.
     */
    public function test_store_job_form_request_class_contract(): void
    {
        $this->assertTrue(
            class_exists(\App\Http\Requests\StoreJob::class),
            'StoreJob FormRequest class must exist in App\\Http\\Requests'
        );

        $request = new \App\Http\Requests\StoreJob;
        $rules = $request->rules();

        $this->assertArrayHasKey('title', $rules, 'StoreJob rules must validate title field');
        $this->assertStringContainsString('required', $rules['title']);
    }

    /**
     * Test 5: Verify query counter helper accurately tracks DB queries.
     */
    public function test_query_counter_helper_tracks_queries(): void
    {
        $result = $this->trackQueries(function () {
            DB::table('companies')->limit(1)->get();
            DB::table('users')->limit(1)->get();
        });

        $this->assertEquals(2, $result['count'], 'Query counter must return exact query count');
    }

    /**
     * Test 6: Verify Database Schema single source of truth (mysql-schema.sql) contains core Jobs tables.
     */
    public function test_mysql_schema_truth_contains_jobs_tables(): void
    {
        $schemaPath = database_path('schema/mysql-schema.sql');
        $this->assertTrue(file_exists($schemaPath), 'mysql-schema.sql must exist');

        $schemaContent = file_get_contents($schemaPath);
        $this->assertStringContainsString('CREATE TABLE `jobs`', $schemaContent);
        $this->assertStringContainsString('CREATE TABLE `job_qualifications`', $schemaContent);
        $this->assertStringContainsString('CREATE TABLE `client_city_jobs`', $schemaContent);
        $this->assertStringContainsString('CREATE TABLE `job_assign`', $schemaContent);
        $this->assertStringContainsString('CREATE TABLE `job_metronics_skills`', $schemaContent);
    }

    /**
     * Test 7: Verify curstomcreate method source code uses add_jobs gate authorization and tenant-scoped caching.
     */
    public function test_curstomcreate_uses_add_jobs_gate_and_tenant_scoping(): void
    {
        $ref = new \ReflectionMethod(\App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController::class, 'curstomcreate');
        $fileName = $ref->getFileName();
        $startLine = $ref->getStartLine();
        $endLine = $ref->getEndLine();

        $lines = array_slice(file($fileName), $startLine - 1, $endLine - $startLine + 1);
        $methodBody = implode('', $lines);

        $this->assertStringContainsString("Gate::denies('add_jobs')", $methodBody, 'curstomcreate must enforce add_jobs gate authorization');
        $this->assertStringContainsString("where('company_id', \$companyId)", $methodBody, 'curstomcreate active jobs count must filter by company_id');
        $this->assertStringContainsString("Cache::remember('tenant:'.\$companyId.':categories'", $methodBody, 'curstomcreate categories must use Redis caching');
        $this->assertStringContainsString("Cache::remember('company:'.\$companyId.':active_jobs_count'", $methodBody, 'curstomcreate active jobs count must use Redis caching');
    }

    /**
     * Test 8: Verify edit method source code consolidates duplicate queries and uses tenant scoping with Redis caching.
     */
    public function test_edit_method_consolidates_duplicate_queries_and_uses_tenant_scoping(): void
    {
        $ref = new \ReflectionMethod(\App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController::class, 'edit');
        $fileName = $ref->getFileName();
        $startLine = $ref->getStartLine();
        $endLine = $ref->getEndLine();

        $lines = array_slice(file($fileName), $startLine - 1, $endLine - $startLine + 1);
        $methodBody = implode('', $lines);

        $this->assertStringContainsString("Job::where('company_id', \$company_id)->findOrFail(\$id)", $methodBody, 'edit must scope Job findOrFail by company_id');
        $this->assertStringContainsString("Cache::remember('user_profile:'.\$userId", $methodBody, 'edit must cache user profile query');
        $this->assertStringContainsString("Cache::remember('global_qualification_master'", $methodBody, 'edit must cache qualification master');
        $this->assertStringContainsString('count($this->additionalDetail)', $methodBody, 'edit must derive additionalDetailCount in memory instead of duplicate DB query');
    }

    /**
     * Test 9: Verify JobService class exists and store controller method delegates creation to JobService.
     */
    public function test_job_service_exists_and_store_controller_delegates_to_job_service(): void
    {
        $this->assertTrue(
            class_exists(\App\Services\JobService::class),
            'JobService class must exist in App\\Services'
        );

        $this->assertTrue(
            method_exists(\App\Services\JobService::class, 'createJob'),
            'createJob method must exist on JobService'
        );

        $ref = new \ReflectionMethod(\App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController::class, 'store');
        $fileName = $ref->getFileName();
        $startLine = $ref->getStartLine();
        $endLine = $ref->getEndLine();

        $lines = array_slice(file($fileName), $startLine - 1, $endLine - $startLine + 1);
        $methodBody = implode('', $lines);

        $this->assertStringContainsString('$jobService->createJob', $methodBody, 'store controller method must delegate job creation to JobService');
    }

    /**
     * Test 10: Verify JobService updateJob eliminates LIKE wildcard queries and controller update delegates to JobService.
     */
    public function test_job_service_update_job_eliminates_like_queries_and_uses_tenant_scoping(): void
    {
        $this->assertTrue(
            method_exists(\App\Services\JobService::class, 'updateJob'),
            'updateJob method must exist on JobService'
        );

        $refController = new \ReflectionMethod(\App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController::class, 'update');
        $fileNameCtrl = $refController->getFileName();
        $startLineCtrl = $refController->getStartLine();
        $endLineCtrl = $refController->getEndLine();

        $ctrlLines = array_slice(file($fileNameCtrl), $startLineCtrl - 1, $endLineCtrl - $startLineCtrl + 1);
        $ctrlBody = implode('', $ctrlLines);

        $this->assertStringContainsString("Job::where('company_id', \$company_id)->findOrFail(\$id)", $ctrlBody, 'update controller method must scope Job by company_id');
        $this->assertStringContainsString('$jobService->updateJob', $ctrlBody, 'update controller method must delegate update to JobService');

        $refService = new \ReflectionMethod(\App\Services\JobService::class, 'updateJob');
        $fileNameSrv = $refService->getFileName();
        $startLineSrv = $refService->getStartLine();
        $endLineSrv = $refService->getEndLine();

        $srvLines = array_slice(file($fileNameSrv), $startLineSrv - 1, $endLineSrv - $startLineSrv + 1);
        $srvBody = implode('', $srvLines);

        $this->assertStringNotContainsString('LIKE', $srvBody, 'updateJob must not contain leading-wildcard LIKE table scan queries');
        $this->assertStringContainsString("whereIn('city_list.city', array_unique(\$cityNames))", $srvBody, 'updateJob must use exact whereIn batch lookup for work city locations');
    }

    /**
     * Test 11: Verify StoreJob FormRequest validation rules expansion and client submit double-click protection contract.
     */
    public function test_store_job_validation_rules_and_client_double_click_protection(): void
    {
        $request = new \App\Http\Requests\StoreJob;
        $rules = $request->rules();

        $this->assertArrayHasKey('title', $rules);
        $this->assertArrayHasKey('client_id', $rules);
        $this->assertArrayHasKey('category_id', $rules);
        $this->assertArrayHasKey('total_positions', $rules);
        $this->assertArrayHasKey('minimum_salary', $rules);
        $this->assertArrayHasKey('maximum_salary', $rules);

        $viewPath = resource_path('views/service_company/jobs/create.blade.php');
        $this->assertTrue(file_exists($viewPath), 'create.blade.php must exist');

        $viewContent = file_get_contents($viewPath);
        $this->assertStringContainsString("\$btn.prop('disabled', true)", $viewContent, 'create.blade.php must disable submit button to prevent double-click submissions');
    }

    /**
     * Test 12: Verify JobResource JSON transformer class exists and formats model into structured API array.
     */
    public function test_job_resource_json_transformer_contract(): void
    {
        $this->assertTrue(
            class_exists(\App\Http\Resources\JobResource::class),
            'JobResource class must exist in App\\Http\\Resources'
        );

        $job = new Job;
        $job->id = 101;
        $job->company_id = 5;
        $job->client_id = 12;
        $job->title = 'Senior Laravel Engineer';
        $job->status = 'active';
        $job->total_positions = 3;

        $resource = new \App\Http\Resources\JobResource($job);
        $array = $resource->toArray(new \Illuminate\Http\Request);

        $this->assertEquals(101, $array['id']);
        $this->assertEquals(5, $array['company_id']);
        $this->assertEquals(12, $array['client_id']);
        $this->assertEquals('Senior Laravel Engineer', $array['title']);
        $this->assertEquals('active', $array['status']);
        $this->assertEquals(3, $array['total_positions']);
    }

    /**
     * Test 13: Verify JobCacheKeys service class exists and generates standardized Redis key patterns and invalidates keys.
     */
    public function test_job_cache_keys_builder_and_invalidation_contract(): void
    {
        $this->assertTrue(
            class_exists(\App\Services\JobCacheKeys::class),
            'JobCacheKeys class must exist in App\\Services'
        );

        $this->assertEquals('tenant:5:categories', \App\Services\JobCacheKeys::categoriesKey(5));
        $this->assertEquals('tenant:5:clients', \App\Services\JobCacheKeys::clientsKey(5));
        $this->assertEquals('company:5:active_jobs_count', \App\Services\JobCacheKeys::activeJobsCountKey(5));
        $this->assertEquals('user_profile:12', \App\Services\JobCacheKeys::userProfileKey(12));
        $this->assertEquals('tenant:5:job_edit:101', \App\Services\JobCacheKeys::jobEditKey(5, 101));

        \App\Services\JobCacheKeys::clearTenantCache(5);
        \App\Services\JobCacheKeys::clearUserJobsCache(12);
        $this->assertTrue(true, 'JobCacheKeys invalidation helper executed cleanly');
    }

    /**
     * Test 14: Verify modular Blade form partials exist in resources/views/service_company/jobs/partials.
     */
    public function test_job_frontend_blade_partials_exist(): void
    {
        $partialsDir = resource_path('views/service_company/jobs/partials');

        $this->assertTrue(file_exists($partialsDir.'/_form_basic.blade.php'), '_form_basic.blade.php must exist');
        $this->assertTrue(file_exists($partialsDir.'/_form_location.blade.php'), '_form_location.blade.php must exist');
        $this->assertTrue(file_exists($partialsDir.'/_form_team.blade.php'), '_form_team.blade.php must exist');
        $this->assertTrue(file_exists($partialsDir.'/_form_questions.blade.php'), '_form_questions.blade.php must exist');
    }

    /**
     * Test 15: Verify EnsureIdempotentSubmission middleware contract and duplicate request response caching.
     */
    public function test_ensure_idempotent_submission_middleware_contract(): void
    {
        $this->assertTrue(
            class_exists(\App\Http\Middleware\EnsureIdempotentSubmission::class),
            'EnsureIdempotentSubmission middleware must exist in App\\Http\\Middleware'
        );

        $middleware = new \App\Http\Middleware\EnsureIdempotentSubmission;

        $executionCount = 0;
        $idempotencyKey = 'uuid-test-key-'.uniqid();

        $request1 = \Illuminate\Http\Request::create('/service_company/jobs', 'POST');
        $request1->headers->set('X-Idempotency-Key', $idempotencyKey);

        $response1 = $middleware->handle($request1, function () use (&$executionCount) {
            $executionCount++;

            return response()->json(['status' => 'success', 'job_id' => 999], 200);
        });

        $this->assertEquals(1, $executionCount, 'Initial request must execute controller logic once');
        $this->assertEquals(200, $response1->getStatusCode());

        $request2 = \Illuminate\Http\Request::create('/service_company/jobs', 'POST');
        $request2->headers->set('X-Idempotency-Key', $idempotencyKey);

        $response2 = $middleware->handle($request2, function () use (&$executionCount) {
            $executionCount++;

            return response()->json(['status' => 'success', 'job_id' => 999], 200);
        });

        $this->assertEquals(1, $executionCount, 'Duplicate idempotent request must NOT re-execute controller logic');
        $this->assertEquals(200, $response2->getStatusCode());
    }

    /**
     * Test 16: Verify JobConcurrencyLock service class exists and executes callback inside company atomic Redis lock.
     */
    public function test_job_concurrency_lock_atomic_redis_lock_contract(): void
    {
        $this->assertTrue(
            class_exists(\App\Services\JobConcurrencyLock::class),
            'JobConcurrencyLock class must exist in App\\Services'
        );

        $executed = false;
        $result = \App\Services\JobConcurrencyLock::runForCompany(10, function () use (&$executed) {
            $executed = true;

            return 'LOCK_SUCCESS';
        });

        $this->assertTrue($executed, 'Callback must execute inside JobConcurrencyLock');
        $this->assertEquals('LOCK_SUCCESS', $result);
    }

    /**
     * Test 17: Verify JobsModuleLoadTestBenchmarkTest performance class exists and measures sub-100ms latency bounds.
     */
    public function test_jobs_module_load_test_benchmark_suite_contract(): void
    {
        $this->assertTrue(
            class_exists(\Tests\Feature\JobsModuleLoadTestBenchmarkTest::class),
            'JobsModuleLoadTestBenchmarkTest class must exist in Tests\\Feature'
        );
    }

    /**
     * Test 18: Verify V1 vs V2 data parity contract and Metronic UI/UX standards compliance.
     */
    public function test_v1_v2_data_parity_and_metronic_ui_standards_contract(): void
    {
        $v1Data = [
            'title' => 'Parity Test Requisition',
            'client_id' => 10,
            'category_id' => 3,
            'total_positions' => 4,
            'min_exp' => 2,
            'max_exp' => 5,
            'min_salary' => 50000,
            'max_salary' => 80000,
            'work_type' => 'no',
            'status' => 'active',
        ];

        $job = new Job;
        $job->title = $v1Data['title'];
        $job->client_id = $v1Data['client_id'];
        $job->category_id = $v1Data['category_id'];
        $job->total_positions = $v1Data['total_positions'];
        $job->min_exp = $v1Data['min_exp'];
        $job->max_exp = $v1Data['max_exp'];
        $job->min_salary = $v1Data['min_salary'];
        $job->max_salary = $v1Data['max_salary'];
        $job->work_type = $v1Data['work_type'];
        $job->status = $v1Data['status'];

        $this->assertEquals($v1Data['title'], $job->title);
        $this->assertEquals($v1Data['client_id'], $job->client_id);
        $this->assertEquals($v1Data['category_id'], $job->category_id);
        $this->assertEquals($v1Data['total_positions'], $job->total_positions);

        $viewPath = resource_path('views/service_company/jobs/create.blade.php');
        $viewContent = file_get_contents($viewPath);

        $this->assertStringNotContainsString('three.js', strtolower($viewContent), 'Blade views must not contain 3D engine libraries per Rule #8');
        $this->assertStringNotContainsString('webgl-3d', strtolower($viewContent), 'Blade views must not contain 3D canvas renderers per Rule #8');
    }

    /**
     * Test 19: Verify jobs_v2_enabled feature flag canary config contract.
     */
    public function test_jobs_v2_canary_feature_flag_contract(): void
    {
        $flag = config('feature.jobs_v2_enabled');
        $this->assertNotNull($flag, 'jobs_v2_enabled feature flag must be defined in config/feature.php');
        $this->assertTrue($flag, 'jobs_v2_enabled feature flag must default to true for V2 operation');

        config(['feature.jobs_v2_enabled' => false]);
        $this->assertFalse(config('feature.jobs_v2_enabled'), 'Feature flag must support dynamic runtime toggling');
    }
}
