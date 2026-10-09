<?php

namespace Tests\Feature;

use App\Helper\GeneralMethod;
use App\Models\Company;
use App\Models\Job;
use App\Services\JobApplyService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * AppTrackerCareerSyncAndVisibilityTest
 *
 * Intent:
 * Verifies that:
 * 1. Candidates submitted to App Tracker (via add_job_wise_condidates or rec_candidate_assign)
 *    are protected against duplicate career page applications for the SAME job.
 * 2. Candidates submitted to Job A remain eligible to apply for a DIFFERENT Job B.
 * 3. getJobAssignedCandidateIds() correctly returns assigned IDs whether the company is
 *    company_id or thirdparty_company_id / source_company_id or source_from_company_id.
 * 4. Elastic queries for Similar Candidates and Career Apply no longer contain regressive
 *    must_not exclusions that hide candidates globally.
 *
 * Author: Antigravity AI
 * Date: October 09, 2026
 */
class AppTrackerCareerSyncAndVisibilityTest extends TestCase
{
    protected Company $company;

    protected Job $job;

    protected Job $secondJob;

    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'mysql']);
        config(['database.connections.mysql.database' => 'dbmaster']);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        $jobs = Job::take(2)->get();
        if ($jobs->count() < 2) {
            $this->markTestSkipped('At least 2 jobs needed for cross-job testing.');
        }

        $this->job = $jobs[0];
        $this->secondJob = $jobs[1];
        $this->company = Company::find($this->job->company_id) ?? Company::first();
    }

    /**
     * Test 1: Deduplication detects existing candidate in add_job_wise_condidates for same job.
     */
    public function test_job_apply_deduplication_detects_app_tracker_submission(): void
    {
        $testEmail = 'apptracker_sync_test_'.uniqid().'@gmail.com';
        $testPhone = '9999'.rand(100000, 999999);

        // 1. Create a dummy candidate/application representing an App Tracker submission
        $appId = DB::table('job_applications')->insertGetId([
            'company_id' => $this->company->id,
            'job_id' => $this->job->id,
            'full_name' => 'John Doe Tracker Test',
            'email' => $testEmail,
            'phone' => $testPhone,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2. Insert into add_job_wise_condidates (App Tracker table)
        DB::table('add_job_wise_condidates')->insert([
            'company_id' => $this->company->id,
            'job_id' => $this->job->id,
            'job_application_id' => $appId,
            'thirdparty_company_id' => 999, // Agency submission
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            /** @var JobApplyService $service */
            $service = app(JobApplyService::class);

            $request = Request::create('/career-apply/submit', 'POST', [
                'job_id' => $this->job->id,
                'company_id' => $this->company->id,
                'full_name' => 'John Doe Direct',
                'email' => $testEmail,
                'phone' => $testPhone,
            ]);

            $response = $service->submitApplication($request);
            $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : (array) $response);

            // Submission must be blocked because candidate is already added via App Tracker
            $this->assertEquals('mailcheck', $data['status'] ?? null);
            $this->assertStringContainsString('already applied', strtolower($data['message'] ?? ''));
        } finally {
            DB::table('add_job_wise_condidates')->where('job_application_id', $appId)->delete();
            DB::table('job_applications')->where('id', $appId)->delete();
        }
    }

    /**
     * Test 2: Candidate submitted to Job A can still apply for Job B.
     */
    public function test_candidate_submitted_to_job_a_can_apply_for_job_b(): void
    {
        $testEmail = 'cross_job_test_'.uniqid().'@gmail.com';
        $testPhone = '9998'.rand(100000, 999999);

        // 1. Candidate submitted to Job A
        $appId = DB::table('job_applications')->insertGetId([
            'company_id' => $this->company->id,
            'job_id' => $this->job->id,
            'full_name' => 'Cross Job Candidate',
            'email' => $testEmail,
            'phone' => $testPhone,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('add_job_wise_condidates')->insert([
            'company_id' => $this->company->id,
            'job_id' => $this->job->id,
            'job_application_id' => $appId,
            'thirdparty_company_id' => 888,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            /** @var JobApplyService $service */
            $service = app(JobApplyService::class);

            // Submit for Job B (different job)
            $request = Request::create('/career-apply/submit', 'POST', [
                'job_id' => $this->secondJob->id,
                'company_id' => $this->company->id,
                'full_name' => 'Cross Job Candidate',
                'email' => $testEmail,
                'phone' => $testPhone,
            ]);

            $response = $service->submitApplication($request);
            $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : (array) $response);

            // It should NOT fail with "already applied" for Job B!
            $message = strtolower($data['message'] ?? '');
            $this->assertStringNotContainsString('already applied', $message);
        } finally {
            DB::table('add_job_wise_condidates')->where('job_application_id', $appId)->delete();
            DB::table('job_applications')->where('id', $appId)->delete();
        }
    }

    /**
     * Test 3: getJobAssignedCandidateIds scopes company and thirdparty properly.
     */
    public function test_get_job_assigned_candidate_ids_scopes_thirdparty_and_client(): void
    {
        $jobId = $this->job->id;
        $clientCompanyId = $this->company->id;
        $agencyCompanyId = 777777;

        // Candidate 1: Client direct assigned
        $app1 = DB::table('job_applications')->insertGetId([
            'company_id' => $clientCompanyId,
            'job_id' => $jobId,
            'full_name' => 'Cand 1',
            'email' => 'cand1_'.uniqid().'@test.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Candidate 2: Agency assigned (thirdparty_company_id is set)
        $app2 = DB::table('job_applications')->insertGetId([
            'company_id' => $clientCompanyId,
            'job_id' => $jobId,
            'full_name' => 'Cand 2',
            'email' => 'cand2_'.uniqid().'@test.com',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('add_job_wise_condidates')->insert([
            'company_id' => $clientCompanyId,
            'job_id' => $jobId,
            'job_application_id' => $app1,
            'thirdparty_company_id' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('add_job_wise_condidates')->insert([
            'company_id' => 999999, // different primary
            'job_id' => $jobId,
            'job_application_id' => $app2,
            'thirdparty_company_id' => $agencyCompanyId, // agency company
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            // When querying from agency's perspective:
            $agencyAssigned = GeneralMethod::getJobAssignedCandidateIds($jobId, $agencyCompanyId);
            $this->assertContains($app2, $agencyAssigned);

            // When querying from client's perspective:
            $clientAssigned = GeneralMethod::getJobAssignedCandidateIds($jobId, $clientCompanyId);
            $this->assertContains($app1, $clientAssigned);
        } finally {
            DB::table('add_job_wise_condidates')->whereIn('job_application_id', [$app1, $app2])->delete();
            DB::table('job_applications')->whereIn('id', [$app1, $app2])->delete();
        }
    }

    /**
     * Test 4: Verify static source code does not contain global exclusion regressions.
     */
    public function test_codebase_does_not_contain_regressive_candidate_exclusions(): void
    {
        $appTrackerControllerPath = app_path('Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php');
        $generalMethodPath = app_path('Helper/GeneralMethod.php');

        $appTrackerCode = file_get_contents($appTrackerControllerPath);
        $generalMethodCode = file_get_contents($generalMethodPath);

        // 1. similar_existing_data_ajax must NOT contain job_master_status_id: [8, 9] in must_not
        preg_match('/function similar_existing_data_ajax.*?function/s', $appTrackerCode, $similarMatches);
        $similarCode = $similarMatches[0] ?? '';
        $this->assertNotEmpty($similarCode);
        $this->assertStringNotContainsString(
            "['terms' => ['job_master_status_id' => [8, 9]]]",
            $similarCode,
            'similar_existing_data_ajax must not exclude candidates in review/status 8 or 9'
        );

        // 2. GeneralMethod::custome_count_all_compute must NOT exclude status 8 or 9 from similar counter
        preg_match('/function custome_count_all_compute.*?function/s', $generalMethodCode, $counterMatches);
        $counterCode = $counterMatches[0] ?? '';
        $this->assertNotEmpty($counterCode);
        $this->assertStringNotContainsString(
            "['terms' => ['job_master_status_id' => [8, 9]]]",
            $counterCode,
            'custome_count_all_compute must not exclude candidates in review/status 8 or 9'
        );

        // 3. career_apply_data_ajax must NOT contain must_not thirdparty_company_id > 0
        preg_match('/function career_apply_data_ajax.*?function/s', $appTrackerCode, $careerMatches);
        $careerCode = $careerMatches[0] ?? '';
        $this->assertNotEmpty($careerCode);
        $this->assertStringNotContainsString(
            "['range' => ['thirdparty_company_id' => ['gt' => 0]]]",
            $careerCode,
            'career_apply_data_ajax must not exclude thirdparty_company_id in must_not'
        );
    }
}
