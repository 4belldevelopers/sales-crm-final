<?php

namespace Tests\Feature;

use App\Services\JobApplyService;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * Verifies that on the public career application page (career-apply.blade.php),
 * Notice Period is mandatory for all direct applicants (both frontend markup/JS and backend service).
 */
class CandidateCareerApplyNoticePeriodTest extends TestCase
{
    public function test_career_apply_blade_renders_notice_period_as_unconditionally_mandatory(): void
    {
        $source = file_get_contents(base_path('resources/views/career-apply.blade.php'));
        $this->assertNotFalse($source);

        // 1. Notice Period label has unconditional red asterisk
        $this->assertStringContainsString('<label>Notice Period <span class="rq">*</span></label>', $source);

        // 2. LWD label has unconditional red asterisk
        $this->assertStringContainsString("Last Working Date (LWD)' }} <span class=\"rq\">*</span></label>", $source);

        // 3. JS validation checks Notice Period unconditionally
        $this->assertStringContainsString("// 17. Notice Period & Joining Details * (MANDATORY for all direct candidate applications on Career Page)", $source);
        $this->assertStringContainsString("addMissing('np_id', 'Notice Period & Joining Details', '.candidate-notice-options'", $source);

        // 4. syncFresherUIState does not clear Notice Period errors when choosing Fresher
        $fresherFnStart = strpos($source, 'function syncFresherUIState()');
        $this->assertNotFalse($fresherFnStart);
        $fresherFnEnd = strpos($source, 'window.syncFresherUIState = syncFresherUIState;', $fresherFnStart);
        $this->assertNotFalse($fresherFnEnd);
        $fresherFnBody = substr($source, $fresherFnStart, $fresherFnEnd - $fresherFnStart);
        $this->assertStringNotContainsString("clearCandidateFieldValidationErrors('np_id');", $fresherFnBody);
    }

    public function test_job_apply_service_enforces_notice_period_as_mandatory(): void
    {
        /** @var JobApplyService $service */
        $service = app(JobApplyService::class);

        // Request with valid basic applicant data but missing serving_notice_period
        $request = Request::create('/careerStore', 'POST', [
            'company_id' => 1,
            'job_id' => 1,
            'full_name' => 'Jane Candidate',
            'email' => 'jane.candidate.testing@gmail.com',
            'phone' => '9876543210',
            'gender' => 'female',
            'dob' => '1995-05-15',
            'nationality' => 'Indian',
            'location_city_id' => 10,
            'resume_session_uuid' => 'valid-uuid-for-test',
            'qualification' => 1,
            'careersummary' => 'Experienced software engineer',
            'employmentType' => 'Full Time',
            'skills' => json_encode(['PHP', 'Laravel']),
            'total_exp_year' => '0', // Even for a fresher
            'rel_exp_year' => '0',
            'wm' => 'office',
            'serving_notice_period' => '',
        ]);

        $response = $service->submitApplication($request);
        $data = is_array($response) ? $response : (method_exists($response, 'getData') ? $response->getData(true) : (array) $response);

        $this->assertEquals('mailcheck', $data['status'] ?? null);
        $this->assertStringContainsString('Please select a notice period option.', $data['message'] ?? '');
    }
}
