<?php

namespace Tests\Feature;

use App\Http\Controllers\ServiceCompany\ServiceCompanyJobApplicationController;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

/**
 * Class StrictSkillRatingValidationTest
 *
 * MANDATORY ATS RULE:
 * A candidate WITHOUT a valid skill rating must NEVER be moved from InReview (8) to Sourcing (1).
 *
 * Architecture Compliance:
 * - Rule 1 & 2: CQRS MySQL writes & authoritative validation.
 * - Rule 5: Zero Stale Data in ATS.
 * - Rule 7 & 17: Zero module regressions & page isolation.
 */
class StrictSkillRatingValidationTest extends TestCase
{
    protected ServiceCompanyJobApplicationController $controller;

    protected function setUp(): void
    {
        parent::setUp();

        Cache::flush();

        foreach (['global_settings', 'sms_settings', 'theme_settings'] as $tbl) {
            if (! Schema::hasTable($tbl)) {
                Schema::create($tbl, function ($t) {
                    $t->increments('id');
                    $t->integer('company_id')->nullable();
                    $t->string('company_name')->nullable();
                    $t->timestamps();
                });
            }
        }

        DB::table('global_settings')->insertOrIgnore(['id' => 1, 'company_name' => 'Jero Test']);

        if (! Schema::hasTable('job_applications')) {
            Schema::create('job_applications', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('job_id')->nullable();
                $table->string('full_name')->nullable();
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->unsignedBigInteger('city_id')->nullable();
                $table->string('qualification')->nullable();
                $table->string('nationality')->nullable();
                $table->string('gender')->nullable();
                $table->string('total_exp')->nullable();
                $table->string('rel_exp')->nullable();
                $table->string('current_designation')->nullable();
                $table->string('current_company')->nullable();
                $table->string('ctc')->nullable();
                $table->string('ectc')->nullable();
                $table->double('ctc_yearly', 15, 2)->nullable();
                $table->double('ctc_monthly', 15, 2)->nullable();
                $table->double('ctc_hourly', 15, 2)->nullable();
                $table->double('ectc_yearly', 15, 2)->nullable();
                $table->double('ectc_monthly', 15, 2)->nullable();
                $table->double('ectc_hourly', 15, 2)->nullable();
                $table->double('pay_rate_amount', 15, 2)->nullable();
                $table->double('bill_rate_amount', 15, 2)->nullable();
                $table->string('np_id')->nullable();
                $table->string('wm')->nullable();
                $table->text('recruiter_note')->nullable();
                $table->string('resume')->nullable();
                $table->text('careersummary')->nullable();
                $table->string('rating')->nullable();
                $table->text('skills')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('add_job_wise_condidates')) {
            Schema::create('add_job_wise_condidates', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('job_id');
                $table->unsignedBigInteger('job_application_id');
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('submitted_by')->nullable();
                $table->unsignedBigInteger('master_status_id')->nullable();
            });
        }

        if (! Schema::hasTable('job_metronics_skills')) {
            Schema::create('job_metronics_skills', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('company_id')->nullable();
                $table->unsignedBigInteger('job_id');
                $table->string('skill_name');
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('candidate_job_skill_exp')) {
            Schema::create('candidate_job_skill_exp', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('job_skill_id');
                $table->text('skill_value')->nullable();
                $table->unsignedBigInteger('job_id');
                $table->unsignedBigInteger('candidate_id');
                $table->integer('rating')->nullable();
            });
        }

        if (! Schema::hasTable('bench_candidate_skill_experience')) {
            Schema::create('bench_candidate_skill_experience', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('candidate_id');
                $table->string('skill_key');
                $table->integer('rating')->nullable();
            });
        }

        DB::table('bench_candidate_skill_experience')->delete();
        DB::table('candidate_job_skill_exp')->delete();
        DB::table('job_metronics_skills')->delete();
        DB::table('add_job_wise_condidates')->delete();
        DB::table('job_applications')->delete();

        $this->controller = app(ServiceCompanyJobApplicationController::class);
    }

    /**
     * Test getCandidateSkillRatingStatus detects unrated required skills.
     */
    public function test_get_candidate_skill_rating_status_detects_unrated_skills(): void
    {
        $appId = DB::table('job_applications')->insertGetId([
            'full_name' => 'Test Candidate',
            'email' => 'candidate@test.com',
            'phone' => '9999999999',
            'skills' => 'Java',
        ]);

        $wiseId = DB::table('add_job_wise_condidates')->insertGetId([
            'job_id' => 10,
            'job_application_id' => $appId,
            'company_id' => 1,
            'master_status_id' => 8,
        ]);

        // Job has 2 required skills: Java and Microservices
        DB::table('job_metronics_skills')->insert([
            ['job_id' => 10, 'skill_name' => 'Java'],
            ['job_id' => 10, 'skill_name' => 'Microservices'],
        ]);

        $candidatePayload = (object) [
            'candidate_id' => $appId,
            'jobId' => 10,
            'job_wise_condidates_id' => $wiseId,
        ];

        $status = $this->controller->getCandidateSkillRatingStatus($candidatePayload);
        $this->assertFalse($status['has_valid']);
        $this->assertEquals(['Java', 'Microservices'], $status['unrated']);
    }

    /**
     * Test partial rating (e.g. 1 out of 2 rated) remains invalid.
     */
    public function test_partial_rating_fails_validation(): void
    {
        $appId = DB::table('job_applications')->insertGetId([
            'full_name' => 'Test Candidate',
            'email' => 'candidate@test.com',
            'phone' => '9999999999',
            'skills' => 'Java',
        ]);

        $skill1 = DB::table('job_metronics_skills')->insertGetId(['job_id' => 10, 'skill_name' => 'Java']);
        $skill2 = DB::table('job_metronics_skills')->insertGetId(['job_id' => 10, 'skill_name' => 'Microservices']);

        // Only rate Java (rating = 4)
        DB::table('candidate_job_skill_exp')->insert([
            'job_skill_id' => $skill1,
            'skill_value' => '3 Years',
            'job_id' => 10,
            'candidate_id' => $appId,
            'rating' => 4,
        ]);

        $candidatePayload = (object) [
            'candidate_id' => $appId,
            'jobId' => 10,
        ];

        $status = $this->controller->getCandidateSkillRatingStatus($candidatePayload);
        $this->assertFalse($status['has_valid']);
        $this->assertEquals(['Microservices'], $status['unrated']);
        $this->assertFalse($this->controller->checkCandidateHasValidSkillRating($candidatePayload));
    }

    /**
     * Test full rating (all skills rated 1-5) passes validation.
     */
    public function test_full_rating_passes_validation(): void
    {
        $appId = DB::table('job_applications')->insertGetId([
            'full_name' => 'Test Candidate',
            'email' => 'candidate@test.com',
            'phone' => '9999999999',
            'skills' => 'Java',
        ]);

        $skill1 = DB::table('job_metronics_skills')->insertGetId(['job_id' => 10, 'skill_name' => 'Java']);
        $skill2 = DB::table('job_metronics_skills')->insertGetId(['job_id' => 10, 'skill_name' => 'Microservices']);

        DB::table('candidate_job_skill_exp')->insert([
            [
                'job_skill_id' => $skill1,
                'skill_value' => '3 Years',
                'job_id' => 10,
                'candidate_id' => $appId,
                'rating' => 4,
            ],
            [
                'job_skill_id' => $skill2,
                'skill_value' => '2 Years',
                'job_id' => 10,
                'candidate_id' => $appId,
                'rating' => 5,
            ],
        ]);

        $candidatePayload = (object) [
            'candidate_id' => $appId,
            'jobId' => 10,
        ];

        $status = $this->controller->getCandidateSkillRatingStatus($candidatePayload);
        $this->assertTrue($status['has_valid']);
        $this->assertEmpty($status['unrated']);
        $this->assertTrue($this->controller->checkCandidateHasValidSkillRating($candidatePayload));
    }

    /**
     * Test checkCandidateSourcingMissingFields injects Skill Rating when unrated.
     */
    public function test_check_candidate_sourcing_missing_fields_includes_skill_rating(): void
    {
        $appId = DB::table('job_applications')->insertGetId([
            'full_name' => 'Test Candidate',
            'email' => 'candidate@test.com',
            'phone' => '9999999999',
            'city_id' => 1,
            'qualification' => 'B.Tech',
            'nationality' => 'Indian',
            'gender' => 'Male',
            'skills' => 'Java',
            'total_exp' => '0', // Fresher
            'rel_exp' => '0',
            'wm' => 'Remote',
            'recruiter_note' => 'Good candidate',
            'resume' => 'resume.pdf',
            'careersummary' => 'Summary of experience',
        ]);

        DB::table('job_metronics_skills')->insert([
            ['job_id' => 10, 'skill_name' => 'Java'],
            ['job_id' => 10, 'skill_name' => 'Spring Boot'],
        ]);

        $candidatePayload = (object) [
            'candidate_id' => $appId,
            'jobId' => 10,
            'full_name' => 'Test Candidate',
            'email' => 'candidate@test.com',
            'phone' => '9999999999',
            'city_id' => 1,
            'qualification' => 'B.Tech',
            'nationality' => 'Indian',
            'gender' => 'Male',
            'skills' => 'Java',
            'total_exp' => '0',
            'rel_exp' => '0',
            'wm' => 'Remote',
            'recruiter_note' => 'Good candidate',
            'resume' => 'resume.pdf',
            'careersummary' => 'Summary of experience',
        ];

        $ref = new \ReflectionMethod($this->controller, 'checkCandidateSourcingMissingFields');
        $ref->setAccessible(true);
        $missing = $ref->invoke($this->controller, $candidatePayload);

        $this->assertContains('Skill Rating (Java, Spring Boot)', $missing);
    }

    /**
     * Test checkCandidateSourcingMissingFields accepts salary when defined in ctc_yearly and ectc_yearly
     * even if legacy ctc column is 0 or null.
     */
    public function test_salary_details_pass_when_yearly_ctc_is_provided(): void
    {
        $appId = DB::table('job_applications')->insertGetId([
            'full_name' => 'Aanchal Giri',
            'email' => 'aanchal@test.com',
            'phone' => '9999999999',
            'city_id' => 1,
            'qualification' => 'B.Tech',
            'nationality' => 'Indian',
            'gender' => 'Female',
            'skills' => 'Java',
            'total_exp' => '3.0', // Non-fresher
            'rel_exp' => '2.0',
            'current_designation' => 'Java Developer',
            'current_company' => 'Tech Corp',
            'ctc' => '0', // Legacy ctc column is 0
            'ctc_yearly' => 500000.00,
            'ectc' => '35',
            'ectc_yearly' => 800000.00,
            'np_id' => '1',
            'wm' => 'Remote',
            'recruiter_note' => 'Good candidate',
            'resume' => 'resume.pdf',
            'careersummary' => 'Summary of experience',
        ]);

        $skill1 = DB::table('job_metronics_skills')->insertGetId(['job_id' => 34, 'skill_name' => 'Java']);
        DB::table('candidate_job_skill_exp')->insert([
            'job_skill_id' => $skill1,
            'skill_value' => '3 Years',
            'job_id' => 34,
            'candidate_id' => $appId,
            'rating' => 4,
        ]);

        $candidatePayload = (object) [
            'candidate_id' => $appId,
            'jobId' => 34,
            'full_name' => 'Aanchal Giri',
            'email' => 'aanchal@test.com',
            'phone' => '9999999999',
            'city_id' => 1,
            'qualification' => 'B.Tech',
            'nationality' => 'Indian',
            'gender' => 'Female',
            'skills' => 'Java',
            'total_exp' => '3.0',
            'rel_exp' => '2.0',
            'current_designation' => 'Java Developer',
            'current_company' => 'Tech Corp',
            'ctc' => 0, // Legacy 0 in ES
            'ctc_yearly' => 500000.00,
            'ectc' => 35,
            'ectc_yearly' => 800000.00,
            'np_id' => '1',
            'wm' => 'Remote',
            'recruiter_note' => 'Good candidate',
            'resume' => 'resume.pdf',
            'careersummary' => 'Summary of experience',
        ];

        $ref = new \ReflectionMethod($this->controller, 'checkCandidateSourcingMissingFields');
        $ref->setAccessible(true);
        $missing = $ref->invoke($this->controller, $candidatePayload);

        $this->assertNotContains('Salary Details (Current CTC)', $missing);
        $this->assertNotContains('Salary Details (Expected CTC)', $missing);
    }

    /**
     * Test checkCandidateSourcingMissingFields flags Salary Details when non-fresher has 0/null in all salary fields.
     */
    public function test_salary_details_fail_when_both_ctc_are_empty(): void
    {
        $appId = DB::table('job_applications')->insertGetId([
            'full_name' => 'Unpaid Candidate',
            'email' => 'unpaid@test.com',
            'phone' => '9999999999',
            'city_id' => 1,
            'qualification' => 'B.Tech',
            'nationality' => 'Indian',
            'gender' => 'Male',
            'skills' => 'Java',
            'total_exp' => '4.0', // Non-fresher
            'rel_exp' => '3.0',
            'current_designation' => 'Developer',
            'current_company' => 'Some Corp',
            'ctc' => null,
            'ctc_yearly' => null,
            'ectc' => null,
            'ectc_yearly' => null,
            'np_id' => '1',
            'wm' => 'Remote',
            'recruiter_note' => 'Good candidate',
            'resume' => 'resume.pdf',
            'careersummary' => 'Summary',
        ]);

        $skill1 = DB::table('job_metronics_skills')->insertGetId(['job_id' => 40, 'skill_name' => 'Java']);
        DB::table('candidate_job_skill_exp')->insert([
            'job_skill_id' => $skill1,
            'skill_value' => '3 Years',
            'job_id' => 40,
            'candidate_id' => $appId,
            'rating' => 5,
        ]);

        $candidatePayload = (object) [
            'candidate_id' => $appId,
            'jobId' => 40,
            'full_name' => 'Unpaid Candidate',
            'email' => 'unpaid@test.com',
            'phone' => '9999999999',
            'city_id' => 1,
            'qualification' => 'B.Tech',
            'nationality' => 'Indian',
            'gender' => 'Male',
            'skills' => 'Java',
            'total_exp' => '4.0',
            'rel_exp' => '3.0',
            'current_designation' => 'Developer',
            'current_company' => 'Some Corp',
            'ctc' => null,
            'ctc_yearly' => null,
            'ectc' => null,
            'ectc_yearly' => null,
            'np_id' => '1',
            'wm' => 'Remote',
            'recruiter_note' => 'Good candidate',
            'resume' => 'resume.pdf',
            'careersummary' => 'Summary',
        ];

        $ref = new \ReflectionMethod($this->controller, 'checkCandidateSourcingMissingFields');
        $ref->setAccessible(true);
        $missing = $ref->invoke($this->controller, $candidatePayload);

        $this->assertContains('Salary Details (Current CTC)', $missing);
        $this->assertContains('Salary Details (Expected CTC)', $missing);
    }

    /**
     * Test job skills with zero experience years pass validation as long as proficiency rating is provided,
     * verifying that Experience (Years) field is optional on edit profile.
     */
    public function test_candidate_job_skills_with_zero_years_experience_passes_validation_when_rated(): void
    {
        $appId = DB::table('job_applications')->insertGetId([
            'full_name' => 'Zero Years Candidate',
            'email' => 'zeroyears@test.com',
            'phone' => '9999999999',
            'skills' => 'Laravel, PHP',
        ]);

        $skill1 = DB::table('job_metronics_skills')->insertGetId(['job_id' => 50, 'skill_name' => 'Laravel']);
        $skill2 = DB::table('job_metronics_skills')->insertGetId(['job_id' => 50, 'skill_name' => 'PHP']);

        // Experience year is 0 (or '0.0 Year'), but proficiency rating is rated
        DB::table('candidate_job_skill_exp')->insert([
            [
                'job_skill_id' => $skill1,
                'skill_value' => '0.0 Year',
                'job_id' => 50,
                'candidate_id' => $appId,
                'rating' => 4,
            ],
            [
                'job_skill_id' => $skill2,
                'skill_value' => '0.0 Year',
                'job_id' => 50,
                'candidate_id' => $appId,
                'rating' => 5,
            ],
        ]);

        $candidatePayload = (object) [
            'candidate_id' => $appId,
            'jobId' => 50,
        ];

        $status = $this->controller->getCandidateSkillRatingStatus($candidatePayload);
        $this->assertTrue($status['has_valid'], 'Job skills with 0 experience years must pass validation when rated.');
        $this->assertEmpty($status['unrated'], 'No unrated skills should be reported.');
        $this->assertTrue($this->controller->checkCandidateHasValidSkillRating($candidatePayload));
    }
}
