<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Protects the Add/Edit Candidate page's interactive controls and ensures
 * fresher selection does not remove or disable professional and salary fields.
 */
class CandidateCreateInteractiveControlsTest extends TestCase
{
    public function test_primary_skills_support_dropdown_selection_and_resume_population(): void
    {
        $form = $this->source('resources/views/service_company/job-applications/create.blade.php');

        $this->assertStringContainsString("document.querySelector('#kt_tagify_2')", $form);
        $this->assertStringContainsString('/skill-suggestions', $form);
        $this->assertStringContainsString('fetchCandidateSkillSuggestions', $form);
        $this->assertStringContainsString('renderCandidateSkillSuggestions', $form);
        $this->assertStringContainsString('addCandidatePrimarySkill', $form);
        $this->assertStringContainsString('tagifyskill.addTags', $form);
        $this->assertStringNotContainsString('$.noConflict();', $form);
        $this->assertStringNotContainsString('}else if(cvalue ==2)', $form);
    }

    public function test_current_location_supports_remote_suggestions_and_resume_population(): void
    {
        $form = $this->source('resources/views/service_company/job-applications/create.blade.php');
        $css = $this->source('demo/css/job-application-create.css');

        $this->assertStringContainsString("document.querySelector('#kt_tagify_work_location')", $form);
        $this->assertStringContainsString("mode: 'select'", $form);
        $this->assertStringContainsString('maxTags: 1', $form);
        $this->assertStringContainsString("classname: 'candidate-location-dropdown'", $form);
        $this->assertStringContainsString("classList.add('candidate-location-tagify')", $form);
        $this->assertStringContainsString('/city-job-suggestions', $form);
        $this->assertStringContainsString('tagifylocation.dropdown.show(query)', $form);
        $this->assertStringContainsString('initialLocations.push(defaultLocation)', $form);
        $this->assertStringContainsString('tagifylocation.settings.whitelist.unshift(normalizedValue)', $form);
        $this->assertStringContainsString('resolveAndSetCandidateLocation', $form);
        $this->assertStringContainsString('.candidate-location-tagify.tagify--hasMaxTags .tagify__input', $css);
        $this->assertStringContainsString('.tagify__dropdown.candidate-location-dropdown', $css);
    }

    public function test_all_three_availability_slots_initialize_date_and_time_pickers(): void
    {
        $form = $this->source('resources/views/service_company/job-applications/create.blade.php');

        $this->assertStringContainsString('function initializeCandidateAvailabilityPickers()', $form);
        foreach (['One', 'Two', 'Three'] as $slot) {
            $this->assertStringContainsString("{ id: 'interviewDate{$slot}'", $form);
            $this->assertStringContainsString("'interviewTime{$slot}'", $form);
        }

        $this->assertStringContainsString('window.flatpickr(field, {', $form);
        $this->assertStringContainsString("field.type = 'date'", $form);
        $this->assertStringContainsString("field.type = 'time'", $form);
        $this->assertStringContainsString(
            'name="interviewTimeTwo" id="interviewTimeTwo" value="{{ (@$application->interviewDateTwo)',
            $form
        );
        $this->assertStringContainsString(
            'name="interviewTimeThree" id="interviewTimeThree" value="{{ (@$application->interviewDateThree)',
            $form
        );
    }

    public function test_fresher_selection_does_not_hide_professional_profile_or_lock_current_salary(): void
    {
        $form = $this->source('resources/views/service_company/job-applications/create.blade.php');
        $css = $this->source('demo/css/job-application-create.css');

        $this->assertStringContainsString(
            'class="fs pd-card candidate-card professional-profile-card" id="candidateProfessionalProfile"',
            $form
        );
        $this->assertStringNotContainsString('is-fresher-hidden', $form);
        $this->assertStringNotContainsString('is-fresher-locked', $form);
        $this->assertStringNotContainsString('syncFresherDependentSections', $form);
        $this->assertStringNotContainsString('candidate-salary-lock-info', $form);

        foreach (['pay_annual', 'pay_monthly', 'ctc'] as $fieldId) {
            $this->assertMatchesRegularExpression(
                '/<input type="number" name="[^"]+" id="'.preg_quote($fieldId, '/').'"(?![^>]*readonly)[^>]*>/',
                $form
            );
        }

        $this->assertStringNotContainsString('is-fresher-hidden', $css);
        $this->assertStringNotContainsString('is-fresher-locked', $css);
        $this->assertStringNotContainsString('candidate-salary-lock-tooltip', $css);
    }

    private function source(string $relativePath): string
    {
        $source = file_get_contents(base_path($relativePath));
        $this->assertNotFalse($source, 'Unable to read '.$relativePath);

        return $source;
    }
}
