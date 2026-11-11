<?php

namespace Tests\Feature;

use App\Services\CandidateNoticePeriodState;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

/**
 * Verifies that:
 * 1. Notice Period is optional when submitting a candidate in the InReview stage.
 * 2. Editing or updating an existing candidate's profile preserves the candidate's existing pipeline stage
 *    and NEVER resets the candidate back to InReview (master status 8).
 */
class CandidateStagePreservationAndNoticePeriodValidationTest extends TestCase
{
    public function test_notice_period_is_optional_when_submitting_in_review_candidate(): void
    {
        // 1. InReview candidate: missing serving_notice_period passes validation
        $rules = array_merge([
            'full_name' => ['required', 'string', 'min:2'],
            'phone' => ['required', 'string', 'min:6'],
        ], CandidateNoticePeriodState::validationRules(null, false));

        $messages = CandidateNoticePeriodState::validationMessages();

        $v1 = Validator::make([
            'full_name' => 'John Doe',
            'phone' => '9876543210',
            'serving_notice_period' => '',
        ], $rules, $messages);

        $this->assertFalse($v1->fails(), 'Notice period must be optional for InReview candidate submissions.');

        // 2. When a mode is selected (mode 0), sub-field ddl_np_id is required
        $rulesMode0 = array_merge([
            'full_name' => ['required', 'string', 'min:2'],
            'phone' => ['required', 'string', 'min:6'],
        ], CandidateNoticePeriodState::validationRules(0, false));

        $v2 = Validator::make([
            'full_name' => 'John Doe',
            'phone' => '9876543210',
            'serving_notice_period' => 0,
            'ddl_np_id' => '',
        ], $rulesMode0, $messages);

        $this->assertTrue($v2->fails());
        $this->assertSame('Please select the candidate notice period.', $v2->errors()->first('ddl_np_id'));

        // 3. Select Notice Period (mode 0) with valid days passes
        $v3 = Validator::make([
            'full_name' => 'John Doe',
            'phone' => '9876543210',
            'serving_notice_period' => 0,
            'ddl_np_id' => 30,
        ], $rulesMode0, $messages);

        $this->assertFalse($v3->fails());

        // 4. When required is explicitly requested (e.g. for higher stages), missing option fails
        $rulesMandatory = CandidateNoticePeriodState::validationRules(null, true);
        $v4 = Validator::make(['serving_notice_period' => ''], $rulesMandatory, $messages);
        $this->assertTrue($v4->fails());
        $this->assertSame('Please select a notice period status.', $v4->errors()->first('serving_notice_period'));
    }

    public function test_candidate_update_preserves_existing_pipeline_stage_and_prevents_in_review_reset(): void
    {
        $controllerSource = file_get_contents(base_path('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php'));

        // Extract update() method
        $startPos = strpos($controllerSource, 'public function update(Request $request, $id)');
        $this->assertNotFalse($startPos);
        $endPos = strpos($controllerSource, 'public function formatInterviewDateTime', $startPos);
        $updateBody = substr($controllerSource, $startPos, $endPos - $startPos);

        // 1. Authoritative stage lookup from add_job_wise_condidates
        $this->assertStringContainsString('$existingWiseCandidate = DB::table(\'add_job_wise_condidates\')', $updateBody);
        $this->assertStringContainsString('$existingStage = $existingWiseCandidate && ! empty($existingWiseCandidate->master_status_id)', $updateBody);

        // 2. Setting jobApplication status to existingStage
        $this->assertStringContainsString('$jobApplication->status_id = $existingStage;', $updateBody);
        $this->assertStringContainsString('$jobApplication->master_status_id = $existingStage;', $updateBody);

        // 3. Ensuring add_job_wise_condidates master_status_id is NOT overwritten by user input in update()
        $this->assertStringNotContainsString('add_job_wise_condidates\')->where(\'job_application_id\', $jobApplication->id)->update([\'status_id\' => $targetStage', $updateBody);
        $this->assertStringNotContainsString('add_job_wise_condidates\')->where(\'job_application_id\', $jobApplication->id)->update([', $updateBody);
    }

    public function test_candidate_edit_and_create_endpoints_resolve_active_job_wise_status(): void
    {
        $controllerSource = file_get_contents(base_path('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php'));

        // 1. edit($id) resolves $candidateCurrentStage from $AddJobWiseCondidate->master_status_id
        $this->assertStringContainsString('$candidateCurrentStage = (int) ($AddJobWiseCondidate->master_status_id ?:', $controllerSource);
        $this->assertStringContainsString('$this->application->master_status_id = $candidateCurrentStage;', $controllerSource);
        $this->assertStringContainsString('$this->application->status_id = $candidateCurrentStage;', $controllerSource);

        // 2. create($jobId, $appId) and clientcreate($jobId, $appId) resolve existing stage when $appId is given
        $this->assertStringContainsString('$this->application->master_status_id = (int) $wiseCandidate->master_status_id;', $controllerSource);
    }

    public function test_frontend_blades_keep_notice_period_optional_for_in_review_stage(): void
    {
        $blades = [
            'resources/views/service_company/job-applications/create.blade.php',
            'resources/views/service_company/job-applications/clientcreate.blade.php',
        ];

        foreach ($blades as $bladePath) {
            $source = file_get_contents(base_path($bladePath));

            // Optional label in notice period
            $this->assertStringContainsString('Notice Period <span class="candidate-optional-label">(Optional)</span>', $source);

            // InReview stage exits before any notice period check
            $inReviewExitPos = strpos($source, 'if (activeStageId === 8) {');
            $snpCheckPos = strpos($source, 'var snpMode = ($(\'#serving_notice_period\').val() || \'\').trim();');
            $this->assertNotFalse($inReviewExitPos, "Missing activeStageId === 8 check in {$bladePath}");
            $this->assertNotFalse($snpCheckPos, "Missing snpMode check in {$bladePath}");
            $this->assertLessThan($snpCheckPos, $inReviewExitPos, "InReview stage must exit before notice period validation in {$bladePath}");
        }
    }
}
