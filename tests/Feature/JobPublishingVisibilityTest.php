<?php

namespace Tests\Feature;

use App\Http\Middleware\EnsureOneDeviceLogin;
use App\Models\User;
use App\Services\JobService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * JobPublishingVisibilityTest
 *
 * Feature and view integrity tests verifying:
 * 1. Removal of "Job Type" from Step 4 Basic Information card.
 * 2. Perfect functionality of all 3 Publishing Visibility options:
 *    - visibility_career (Company Career Page)
 *    - visibility_marketplace (Agency Marketplace)
 *    - visibility_internal (Internal Only)
 * 3. Accurate backend mapping in JobService (carrier_website & public_market_status).
 */
class JobPublishingVisibilityTest extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected int $companyId;

    protected function setUp(): void
    {
        parent::setUp();

        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => 'dbmaster']);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        $this->withoutMiddleware([EnsureOneDeviceLogin::class]);

        $this->user = User::withoutGlobalScopes()->where('company_id', 2)->where('status', 'active')->first();
        if (! $this->user) {
            $this->user = User::withoutGlobalScopes()->first();
        }
        $this->companyId = (int) $this->user->company_id;
    }

    /**
     * Test 1: Blade template has completely removed Job Type from Basic Information Card.
     */
    public function test_basic_information_card_does_not_contain_job_type(): void
    {
        $bladePath = resource_path('views/service_company/jobs/create.blade.php');
        $this->assertFileExists($bladePath);

        $content = file_get_contents($bladePath);

        // Assert rvJobType is not present
        $this->assertStringNotContainsString('id="rvJobType"', $content, 'Step 4 Basic Information must not contain #rvJobType');
        $this->assertStringNotContainsString("setText('rvJobType'", $content, 'populateReview must not attempt to set #rvJobType');
    }

    /**
     * Test 2: Blade template contains all 3 Publishing Visibility options with proper mutual exclusivity handlers.
     */
    public function test_publishing_visibility_options_and_scripts_exist_in_blade(): void
    {
        $bladePath = resource_path('views/service_company/jobs/create.blade.php');
        $content = file_get_contents($bladePath);

        $this->assertStringContainsString('name="visibility_career"', $content);
        $this->assertStringContainsString('name="visibility_marketplace"', $content);
        $this->assertStringContainsString('name="visibility_internal"', $content);

        $this->assertStringContainsString('handleVisibilityChange', $content);
        $this->assertStringContainsString('syncVisibilityUI', $content);
        $this->assertStringContainsString('rv_visibility_info_text', $content);
    }

    /**
     * Test 3: JobService createJob with internal visibility sets carrier_website=0 and public_market_status=0.
     */
    public function test_job_service_internal_visibility_sets_zero_for_career_and_marketplace(): void
    {
        $jobService = app(JobService::class);

        $payload = [
            'title' => 'Internal Security Auditor',
            'client_id' => null,
            'category_id' => 1,
            'work_shifts' => 'Day Shift',
            'required_columns' => '[]',
            'minimum_salary' => 0,
            'maximum_salary' => 0,
            'employment_type' => 1,
            'total_positions' => 1,
            'visibility_internal' => 1,
            'visibility_career' => 0,
            'visibility_marketplace' => 0,
            'operation_id' => 'test_internal_'.uniqid(),
        ];

        $job = $jobService->createJob($payload, $this->user);

        $this->assertEquals(0, (int) $job->carrier_website, 'carrier_website must be 0 for internal only');
        $this->assertEquals(0, (int) $job->public_market_status, 'public_market_status must be 0 for internal only');
    }

    /**
     * Test 4: JobService createJob with career page visibility sets carrier_website=1 and public_market_status=0.
     */
    public function test_job_service_career_visibility_sets_carrier_website_1(): void
    {
        $jobService = app(JobService::class);

        $payload = [
            'title' => 'Career Page Fullstack Engineer',
            'client_id' => null,
            'category_id' => 1,
            'work_shifts' => 'Day Shift',
            'required_columns' => '[]',
            'minimum_salary' => 0,
            'maximum_salary' => 0,
            'employment_type' => 1,
            'total_positions' => 1,
            'visibility_internal' => 0,
            'visibility_career' => 1,
            'visibility_marketplace' => 0,
            'operation_id' => 'test_career_'.uniqid(),
        ];

        $job = $jobService->createJob($payload, $this->user);

        $this->assertEquals(1, (int) $job->carrier_website, 'carrier_website must be 1 for career page');
        $this->assertEquals(0, (int) $job->public_market_status, 'public_market_status must be 0 when marketplace not selected');
    }

    /**
     * Test 5: JobService createJob with marketplace visibility sets public_market_status correctly.
     */
    public function test_job_service_marketplace_visibility_sets_public_market_status(): void
    {
        $jobService = app(JobService::class);

        $payload = [
            'title' => 'Marketplace Agency Recruiter Position',
            'client_id' => null,
            'category_id' => 1,
            'work_shifts' => 'Day Shift',
            'required_columns' => '[]',
            'minimum_salary' => 0,
            'maximum_salary' => 0,
            'employment_type' => 1,
            'total_positions' => 1,
            'visibility_internal' => 0,
            'visibility_career' => 0,
            'visibility_marketplace' => 1,
            'operation_id' => 'test_market_'.uniqid(),
        ];

        $job = $jobService->createJob($payload, $this->user);

        $this->assertEquals(0, (int) $job->carrier_website, 'carrier_website must be 0 when career page not selected');
        $this->assertGreaterThan(0, (int) $job->public_market_status, 'public_market_status must be > 0 (1 pending or 2 approved) for marketplace');
    }

    /**
     * Test 6: JobService createJob with both career and marketplace sets both.
     */
    public function test_job_service_both_career_and_marketplace_active(): void
    {
        $jobService = app(JobService::class);

        $payload = [
            'title' => 'Dual Visibility Senior Architect',
            'client_id' => null,
            'category_id' => 1,
            'work_shifts' => 'Day Shift',
            'required_columns' => '[]',
            'minimum_salary' => 0,
            'maximum_salary' => 0,
            'employment_type' => 1,
            'total_positions' => 1,
            'visibility_internal' => 0,
            'visibility_career' => 1,
            'visibility_marketplace' => 1,
            'operation_id' => 'test_dual_'.uniqid(),
        ];

        $job = $jobService->createJob($payload, $this->user);

        $this->assertEquals(1, (int) $job->carrier_website, 'carrier_website must be 1');
        $this->assertGreaterThan(0, (int) $job->public_market_status, 'public_market_status must be > 0 (1 or 2)');
    }
}
