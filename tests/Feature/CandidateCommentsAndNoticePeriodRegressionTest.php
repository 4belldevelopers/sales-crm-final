<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Protects independent candidate comments and the Notice Period component across
 * persistence, indexing, AJAX refresh, and Candidate Drawer rendering.
 */
class CandidateCommentsAndNoticePeriodRegressionTest extends TestCase
{
    public function test_schema_keeps_salary_and_recruiter_comments_in_separate_columns(): void
    {
        $schema = $this->source('database/schema/mysql-schema.sql');
        $jobApplications = $this->between($schema, 'CREATE TABLE `job_applications`', 'DROP TABLE IF EXISTS `job_assign`');

        $this->assertStringContainsString('`comment` varchar(255) DEFAULT NULL', $jobApplications);
        $this->assertStringContainsString('`recruiter_note` mediumtext DEFAULT NULL', $jobApplications);
    }

    public function test_candidate_creation_and_editing_map_comments_independently(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
        $store = $this->between($controller, 'public function store(Request $request)', 'public function googleSheetCandidateData');
        $update = $this->between($controller, 'public function update(Request $request, $id)', 'public function formatInterviewDateTime');

        foreach ([$store, $update] as $path) {
            $this->assertStringContainsString('$jobApplication->comment =', $path);
            $this->assertStringContainsString('$request->comment', $path);
            $this->assertStringContainsString('$jobApplication->recruiter_note = $request->recruiter_note;', $path);
        }

        $this->assertStringNotContainsString('$jobApplication->recruiter_note = $request->comment', $controller);
        $this->assertStringNotContainsString('$jobApplication->comment = $request->recruiter_note', $controller);
    }

    public function test_salary_update_queue_indexes_both_comment_fields_without_cross_mapping(): void
    {
        $job = $this->source('app/Jobs/ProcessCandidateUpdateJob.php');

        $this->assertStringContainsString("'comment' => \$applications_up->comment", $job);
        $this->assertStringContainsString("'recruiter_note' => \$applications_up->recruiter_note", $job);
        $this->assertStringNotContainsString("'recruiter_note' => \$applications_up->comment", $job);
        $this->assertStringNotContainsString("'comment' => \$applications_up->recruiter_note", $job);
    }

    public function test_page_and_drawer_refresh_render_each_comment_from_its_own_field(): void
    {
        $form = $this->source('resources/views/service_company/job-applications/create.blade.php');
        $drawer = $this->source('resources/views/service_company/job-applications/showdetail.blade.php');
        $forwardResume = $this->source('resources/views/service_company/cvtracker/_forward_resume_engine.blade.php');

        $this->assertStringContainsString('name="comment" id="comment"', $form);
        $this->assertStringContainsString('{{ @$application->comment }}', $form);
        $this->assertStringContainsString('name="recruiter_note"', $form);
        $this->assertStringContainsString('{{ @$application->recruiter_note }}', $form);
        $this->assertStringContainsString('Salary Details Comments:', $drawer);
        $this->assertStringContainsString('{!! $application->recruiter_note !!}', $drawer);
        $this->assertStringNotContainsString('@$application->recruiter_note ?: @$application->comment', $drawer);
        $this->assertStringNotContainsString('candidate.recruiter_note || candidate.comment', $forwardResume);
    }

    public function test_recruiter_comment_ajax_update_changes_only_recruiter_note_and_refreshes_drawer_cache(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
        $ajax = $this->between($controller, 'public function recruiterNoteUpdate(Request $request)', 'public function clientCandidateList');

        $this->assertStringContainsString("'recruiter_note' => \$recruiterNote", $ajax);
        $this->assertStringContainsString('ctx._source.recruiter_note = params.note', $ajax);
        $this->assertStringContainsString("cache()->forget('tenant:'", $ajax);
        $this->assertStringNotContainsString("'comment' =>", $ajax);
        $this->assertStringNotContainsString('ctx._source.comment', $ajax);
    }

    public function test_notice_period_ui_and_indexes_use_one_state_with_one_active_sub_field(): void
    {
        $form = $this->source('resources/views/service_company/job-applications/create.blade.php');
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
        $indexJob = $this->source('app/Jobs/ProcessCandidateUpdateJob.php');

        $this->assertStringContainsString('aria-label="Notice Period Selection"', $form);
        $this->assertStringContainsString('Notice Period <span class="candidate-optional-label">(Optional)</span>', $form);
        $this->assertStringContainsString('<option value="" {{ $snp_mode === null ? \'selected\' : \'\' }}>Choose Notice Period</option>', $form);
        $this->assertStringContainsString("'serving_notice_period' => ['nullable', 'integer', 'in:0,1,2']", $this->source('app/Services/CandidateNoticePeriodState.php'));
        $this->assertStringContainsString('id="candidateNoticeDateField"', $form);
        $this->assertStringContainsString('id="candidateNoticeDaysField"', $form);
        $this->assertStringContainsString('noticeDateField.hidden = !usesDate', $form);
        $this->assertStringContainsString("noticeDaysField.hidden = mode !== '0'", $form);
        $this->assertStringContainsString('noticeDateInput.disabled = !usesDate', $form);
        $this->assertStringContainsString("noticeDaysSelect.disabled = mode !== '0'", $form);
        $this->assertStringContainsString("noticeDateInput.value = ''", $form);
        $this->assertStringContainsString("noticeDaysSelect.value = ''", $form);
        $this->assertStringNotContainsString('id="text_display"', $form);
        $this->assertStringNotContainsString('id="snp-immediate"', $form);
        $this->assertStringContainsString('CandidateNoticePeriodState::fromRequest($request)', $controller);
        $this->assertStringContainsString("'last_working_date' => \$applications_up->last_working_date", $indexJob);
    }

    private function source(string $relativePath): string
    {
        $source = file_get_contents(base_path($relativePath));
        $this->assertNotFalse($source, 'Unable to read '.$relativePath);

        return $source;
    }

    private function between(string $source, string $startMarker, string $endMarker): string
    {
        $start = strpos($source, $startMarker);
        $this->assertNotFalse($start, 'Missing start marker: '.$startMarker);
        $end = strpos($source, $endMarker, $start + strlen($startMarker));
        $this->assertNotFalse($end, 'Missing end marker: '.$endMarker);

        return substr($source, $start, $end - $start);
    }
}
