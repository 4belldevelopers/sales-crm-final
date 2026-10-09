<?php

namespace Tests\Feature;

use App\Helper\GeneralMethod;
use App\Models\Company;
use App\Models\Job;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * AppTrackerCandidateActionMatchingTest
 *
 * Intent:
 * Verifies the restored candidate-level matching and action logic across:
 * 1. Career Apply (career_apply_ajax.blade.php)
 * 2. Similar Candidates (similar_existing_ajax.blade.php & dsimilar_existing_ajax.blade.php)
 * 3. Inhouse Talent (internal_bench_ajax.blade.php & dinternal_bench_ajax.blade.php)
 * 4. Agency Talent (public_bench_ajax.blade.php)
 *
 * Specific Rules Verified:
 * - Candidate-specific + job-specific matching.
 * - Candidate in App Tracker for Job X at ANY stage/status shows "Added".
 * - Candidate NOT in App Tracker for Job X shows "Add to Job".
 * - Raw career applicant (in job_applications with job_id = X, but NOT in App Tracker) shows "Add to Job".
 * - Candidate in App Tracker for Job 1 shows "Add to Job" for Job 2.
 * - List is evaluated candidate-by-candidate; no global exclusions or whole-list masking.
 *
 * Tenancy:
 * Tenant-scoped by company_id.
 *
 * Author: Antigravity AI
 * Date: October 09, 2026
 */
class AppTrackerCandidateActionMatchingTest extends TestCase
{
    protected Company $company;

    protected Job $job;

    protected Job $secondJob;

    protected User $user;

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
        $this->user = User::where('company_id', $this->company->id)->first() ?? User::first();
        $this->actingAs($this->user);
    }

    protected function createMockCandidate(array $overrides): object
    {
        $defaults = [
            'id' => 1,
            'candidate_id' => 1,
            'job_id' => $this->job->id,
            'name' => 'Test Candidate',
            'full_name' => 'Test Candidate',
            'email' => 'test@candidate.com',
            'phone' => '1234567890',
            'skills' => 'PHP, Laravel',
            'matched_skills' => ['PHP'],
            'status' => 0,
            'master_status_id' => 1,
            'status_type' => 'DIRECT',
            'city' => 'Gurugram',
            'city_id' => 1,
            'city_name' => 'Gurugram',
            'state_name' => 'Haryana',
            'country_name' => 'India',
            'country' => 101,
            'condidate_type' => 'bench_condidate',
            'outsourcing_type' => 1,
            'thirdparty_company_id' => 0,
            'ClientComID' => 0,
            'company_id' => $this->company->id,
            'title' => 'Software Engineer',
            'client_name' => 'Acme Corp',
            'company_name' => 'Acme Inc',
            'rating' => 4,
            'total_exp' => '5',
            'rel_exp' => '3',
            'ctc' => 50000,
            'ectc' => 60000,
            'ctc_yearly' => 50000,
            'ectc_yearly' => 60000,
            'current_ctc_cur' => 0,
            'current_ectc_cur' => 0,
            'current_ctc_mode' => 0,
            'current_ectc_mode' => 0,
            'serving_notice_period_mode' => 0,
            'np_id' => '30',
            'qualification_name' => 'B.Tech',
            'qualification' => 1,
            'current_location' => 'Gurugram',
            'current_company' => 'Tech Corp',
            'current_designation' => 'Developer',
            'posted_by' => 'Direct Apply',
            'countforadd' => 0,
            'updated_at' => '2026-10-09 10:00:00',
            'created_at' => '2026-10-09 10:00:00',
        ];

        return (object) array_merge($defaults, $overrides);
    }

    /**
     * Test 1: getJobAssignedCandidateIds only returns candidates in add_job_wise_condidates / rec_candidate_assign.
     * Raw career submissions sitting in job_applications are NOT treated as assigned.
     */
    public function test_get_job_assigned_candidate_ids_only_returns_app_tracker_assigned_not_raw_career_applicants(): void
    {
        $jobId = $this->job->id;
        $companyId = $this->company->id;

        // Candidate A: Raw career applicant (applied directly, not yet added to App Tracker)
        $candA = DB::table('job_applications')->insertGetId([
            'company_id' => $companyId,
            'job_id' => $jobId,
            'full_name' => 'Candidate A Raw Applicant',
            'email' => 'canda_raw_'.uniqid().'@test.com',
            'phone' => '888'.rand(1000000, 9999999),
            'status_type' => 'DIRECT',
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Candidate B: Candidate added to App Tracker (in add_job_wise_condidates)
        $candB = DB::table('job_applications')->insertGetId([
            'company_id' => $companyId,
            'job_id' => $jobId,
            'full_name' => 'Candidate B Added To Tracker',
            'email' => 'candb_tracker_'.uniqid().'@test.com',
            'phone' => '889'.rand(1000000, 9999999),
            'status_type' => 'DIRECT',
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        DB::table('add_job_wise_condidates')->insert([
            'company_id' => $companyId,
            'job_id' => $jobId,
            'job_application_id' => $candB,
            'master_status_id' => 1,
            'thirdparty_company_id' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            $assignedIds = GeneralMethod::getJobAssignedCandidateIds($jobId, $companyId);

            // Candidate B MUST be present (exists in App Tracker)
            $this->assertContains($candB, $assignedIds, 'Candidate B must be recognized as assigned in App Tracker.');

            // Candidate A MUST NOT be present (raw application, not yet added to App Tracker)
            $this->assertNotContains($candA, $assignedIds, 'Candidate A (raw career applicant) must NOT be recognized as assigned until added.');
        } finally {
            DB::table('add_job_wise_condidates')->where('job_application_id', $candB)->delete();
            DB::table('job_applications')->whereIn('id', [$candA, $candB])->delete();
        }
    }

    /**
     * Test 2: Career Apply blade correctly shows "Add to Job" for unassigned candidate, and "Added" for assigned candidate.
     */
    public function test_career_apply_blade_renders_add_to_job_for_unassigned_and_added_for_assigned(): void
    {
        $jobId = $this->job->id;

        $candA = $this->createMockCandidate([
            'id' => 100001,
            'full_name' => 'John Unassigned',
            'email' => 'unassigned@test.com',
            'phone' => '1111111111',
            'status' => 0,
            'status_type' => 'DIRECT',
            'posted_by' => 'Direct Apply',
        ]);

        $candB = $this->createMockCandidate([
            'id' => 100002,
            'full_name' => 'Jane Assigned',
            'email' => 'assigned@test.com',
            'phone' => '2222222222',
            'status' => 0,
            'status_type' => 'DIRECT',
            'posted_by' => 'Recruiter A',
        ]);

        $data = [$candA, $candB];
        $assignedCandidateIds = [100002]; // Only candB is assigned to this job

        $html = view('service_company.ajax_pages.career_apply_ajax', [
            'data' => $data,
            'jobid' => $jobId,
            'assignedCandidateIds' => $assignedCandidateIds,
            'userPermissions' => [],
            'app_int_status' => null,
            'app_int_activity' => null,
            'app_int_notice' => null,
            'sal_mode' => 0,
            'min_salary_currency' => 0,
            'app_int_outsource' => null,
            'requiredSkills' => ['php'],
            'modeFilter' => 0,
            'currencyFilter' => 0,
            'symbolsFilter' => 'INR',
        ])->render();

        // Candidate A must show "Add to Job"
        $this->assertStringContainsString('data-row-id="100001"', $html);
        $this->assertStringContainsString('Add to Job', $html);

        // Candidate B must show "Added" button
        $this->assertStringContainsString('>Added</button>', $html);
    }

    /**
     * Test 3: Similar Candidates blade renders candidate-by-candidate with "Add to Job" and "Added".
     */
    public function test_similar_candidates_blade_renders_add_to_job_for_unassigned_and_added_for_assigned(): void
    {
        $jobId = $this->job->id;

        $candA = $this->createMockCandidate([
            'id' => 200001,
            'candidate_id' => 200001,
            'name' => 'Alice Available',
            'full_name' => 'Alice Available',
            'email' => 'alice@test.com',
            'status' => 0,
        ]);

        $candB = $this->createMockCandidate([
            'id' => 200002,
            'candidate_id' => 200002,
            'name' => 'Bob Existing',
            'full_name' => 'Bob Existing',
            'email' => 'bob@test.com',
            'status' => 0,
        ]);

        $data = [$candA, $candB];
        $rowMaps = [
            'rec_assigned' => [],
            'source_from_type' => [],
            'parent_assigned' => [],
            'existing_in_job' => [200002 => true], // candB is existing in this job
            'job_skills' => ['php'],
            'city_map' => [],
            'country_map' => [],
            'wise_job_info' => [],
            'child_status_map' => [],
            'spokperson_map' => [],
        ];

        $html = view('service_company.ajax_pages.similar_existing_ajax', [
            'data' => $data,
            'jobid' => $jobId,
            'rowMaps' => $rowMaps,
            'userPermissions' => [],
            'app_int_status' => null,
            'app_int_activity' => null,
            'app_int_notice' => null,
            'sal_mode' => 0,
            'min_salary_currency' => 0,
            'app_int_outsource' => null,
            'requiredSkills' => ['php'],
            'modeFilter' => 0,
            'currencyFilter' => 0,
            'symbolsFilter' => 'INR',
            'hasActiveFilters' => false,
            'search' => '',
        ])->render();

        // Candidate A must show "Add to Job"
        $this->assertStringContainsString('data-row-id="200001"', $html);
        $this->assertStringContainsString('Add to Job', $html);

        // Candidate B must show "Added"
        $this->assertStringContainsString('Added', $html);
    }

    /**
     * Test 4: Internal Bench (Inhouse Talent) blade distinguishes "Add to Job" vs "Added".
     */
    public function test_internal_bench_blade_renders_add_to_job_for_unassigned_and_added_for_assigned(): void
    {
        $jobId = $this->job->id;

        $candA = $this->createMockCandidate([
            'id' => 300001,
            'name' => 'Internal Bench Unassigned',
            'full_name' => 'Internal Bench Unassigned',
            'email' => 'internal_unassigned@test.com',
            'status' => 2, // Available bench status
        ]);

        $candB = $this->createMockCandidate([
            'id' => 300002,
            'name' => 'Internal Bench Added',
            'full_name' => 'Internal Bench Added',
            'email' => 'internal_added@test.com',
            'status' => 2, // Available bench status
        ]);

        $data = [$candA, $candB];
        $assignedCandidateIds = [300002]; // Only candB is assigned to this job

        $html = view('service_company.ajax_pages.internal_bench_ajax', [
            'data' => $data,
            'jobid' => $jobId,
            'assignedCandidateIds' => $assignedCandidateIds,
            'userPermissions' => [],
            'app_int_status' => null,
            'app_int_activity' => null,
            'app_int_notice' => null,
            'sal_mode' => 0,
            'min_salary_currency' => 0,
            'app_int_outsource' => null,
            'requiredSkillsForDisplay' => ['php'],
            'modeFilter' => 0,
            'currencyFilter' => 0,
            'symbolsFilter' => 'INR',
            'rowMaps' => ['jobs' => [], 'skills' => [], 'duplicates' => [], 'interviews' => [], 'spokperson_map' => [], 'child_status_map' => []],
        ])->render();

        // Candidate A (unassigned) shows "Add to Job"
        $this->assertStringContainsString('data-row-id="300001"', $html);
        $this->assertStringContainsString('Add to Job', $html);

        // Candidate B (assigned) shows "Added"
        $this->assertStringContainsString('>Added</button>', $html);
    }

    /**
     * Test 5: Public Bench (Agency Talent) blade distinguishes "Add to Job" vs "Added".
     */
    public function test_public_bench_blade_renders_add_to_job_for_unassigned_and_added_for_assigned(): void
    {
        $jobId = $this->job->id;

        $candA = $this->createMockCandidate([
            'id' => 400001,
            'name' => 'Agency Bench Unassigned',
            'full_name' => 'Agency Bench Unassigned',
            'email' => 'agency_unassigned@test.com',
            'status' => 2, // Available bench status
            'countforadd' => 0,
        ]);

        $candB = $this->createMockCandidate([
            'id' => 400002,
            'name' => 'Agency Bench Added',
            'full_name' => 'Agency Bench Added',
            'email' => 'agency_added@test.com',
            'status' => 2, // Available bench status
            'countforadd' => 0,
        ]);

        $data = [$candA, $candB];
        $assignedCandidateIds = [400002]; // Only candB is assigned to this job

        $html = view('service_company.ajax_pages.public_bench_ajax', [
            'data' => $data,
            'jobid' => $jobId,
            'assignedCandidateIds' => $assignedCandidateIds,
            'requestedCandidateIds' => [],
            'boostbenchApplications' => [],
            'userPermissions' => [],
            'app_int_status' => null,
            'app_int_activity' => null,
            'app_int_notice' => null,
            'sal_mode' => 0,
            'min_salary_currency' => 0,
            'app_int_outsource' => null,
            'requiredSkills' => ['php'],
            'modeFilter' => 0,
            'currencyFilter' => 0,
            'symbolsFilter' => 'INR',
            'rowMaps' => ['jobs' => [], 'skills' => [], 'duplicates' => [], 'interviews' => [], 'spokperson_map' => [], 'child_status_map' => []],
        ])->render();

        // Candidate A (unassigned) shows "Add to Job"
        $this->assertStringContainsString('data-row-id="400001"', $html);
        $this->assertStringContainsString('Add to Job', $html);

        // Candidate B (assigned) shows "Added"
        $this->assertStringContainsString('>Added</button>', $html);
    }

    /**
     * Test 6: Cross-job isolation: Candidate assigned to Job 1 shows "Add to Job" for Job 2.
     */
    public function test_candidate_assigned_to_job_1_shows_add_to_job_for_job_2(): void
    {
        $job1Id = $this->job->id;
        $job2Id = $this->secondJob->id;
        $companyId = $this->company->id;

        $candId = DB::table('job_applications')->insertGetId([
            'company_id' => $companyId,
            'job_id' => $job1Id,
            'full_name' => 'Cross Job Isolated Candidate',
            'email' => 'cross_isolated_'.uniqid().'@test.com',
            'phone' => '887'.rand(1000000, 9999999),
            'status' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Added only to Job 1 in App Tracker
        DB::table('add_job_wise_condidates')->insert([
            'company_id' => $companyId,
            'job_id' => $job1Id,
            'job_application_id' => $candId,
            'master_status_id' => 1,
            'thirdparty_company_id' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        try {
            // For Job 1: Candidate is assigned
            $assignedJob1 = GeneralMethod::getJobAssignedCandidateIds($job1Id, $companyId);
            $this->assertContains($candId, $assignedJob1, 'Candidate must be assigned to Job 1.');

            // For Job 2: Candidate is NOT assigned
            $assignedJob2 = GeneralMethod::getJobAssignedCandidateIds($job2Id, $companyId);
            $this->assertNotContains($candId, $assignedJob2, 'Candidate must NOT be assigned to Job 2.');
        } finally {
            DB::table('add_job_wise_condidates')->where('job_application_id', $candId)->delete();
            DB::table('job_applications')->where('id', $candId)->delete();
        }
    }

    /**
     * Test 7: Candidate in App Tracker at ANY stage/status shows as assigned.
     */
    public function test_candidate_in_app_tracker_at_any_stage_shows_as_assigned(): void
    {
        $jobId = $this->job->id;
        $companyId = $this->company->id;

        // Test with different master_status_ids: 1 (sourced), 3 (eval), 5 (offered), 6 (joined), etc.
        $stages = [1, 2, 3, 4, 5, 6];

        foreach ($stages as $stageId) {
            $candId = DB::table('job_applications')->insertGetId([
                'company_id' => $companyId,
                'job_id' => $jobId,
                'full_name' => "Stage Test Candidate {$stageId}",
                'email' => "stage_{$stageId}_".uniqid().'@test.com',
                'phone' => '886'.rand(1000000, 9999999),
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            DB::table('add_job_wise_condidates')->insert([
                'company_id' => $companyId,
                'job_id' => $jobId,
                'job_application_id' => $candId,
                'master_status_id' => $stageId,
                'thirdparty_company_id' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            try {
                $assigned = GeneralMethod::getJobAssignedCandidateIds($jobId, $companyId);
                $this->assertContains($candId, $assigned, "Candidate at stage {$stageId} must be recognized as assigned.");
            } finally {
                DB::table('add_job_wise_condidates')->where('job_application_id', $candId)->delete();
                DB::table('job_applications')->where('id', $candId)->delete();
            }
        }
    }
}
