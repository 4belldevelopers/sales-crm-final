<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Protects the Add/Edit Candidate salary card's single synchronized frequency
 * across presentation, persistence, and Candidate Drawer rendering.
 */
class CandidateSalarySynchronizationTest extends TestCase
{
    public function test_current_salary_frequency_is_the_only_interactive_frequency_control(): void
    {
        $form = $this->source('resources/views/service_company/job-applications/create.blade.php');

        $this->assertSame(3, substr_count($form, 'data-salary-group="current" data-salary-period='));
        $this->assertSame(3, substr_count($form, 'data-salary-group="expected" data-salary-period='));
        $this->assertSame(3, substr_count($form, 'data-salary-group="expected" data-salary-period="annual" disabled')
            + substr_count($form, 'data-salary-group="expected" data-salary-period="monthly" disabled')
            + substr_count($form, 'data-salary-group="expected" data-salary-period="hourly" disabled'));
        $this->assertStringContainsString(
            'button[data-salary-group="current"]',
            $form
        );
        $this->assertStringContainsString('function syncSalaryPeriod(period)', $form);
        $this->assertStringContainsString('currentMode.value = modeByPeriod[normalizedPeriod]', $form);
        $this->assertStringContainsString('expectedMode.value = modeByPeriod[normalizedPeriod]', $form);
    }

    public function test_summary_uses_one_frequency_and_calculates_difference_and_increase(): void
    {
        $form = $this->source('resources/views/service_company/job-applications/create.blade.php');

        $this->assertStringContainsString('var period = activeSalaryPeriod();', $form);
        $this->assertStringContainsString('salaryFieldMap.current[period]', $form);
        $this->assertStringContainsString('salaryFieldMap.expected[period]', $form);
        $this->assertStringContainsString('id="candidateSalaryDifferenceSummary"', $form);
        $this->assertStringContainsString('var difference = expectedAmount - currentAmount;', $form);
        $this->assertStringContainsString('(difference / currentAmount) * 100', $form);
    }

    public function test_hike_percentage_is_not_user_editable_or_rendered(): void
    {
        $form = $this->source('resources/views/service_company/job-applications/create.blade.php');
        $drawer = $this->source('resources/views/service_company/job-applications/showdetail.blade.php');

        $this->assertStringNotContainsString('name="percentage_hike"', $form);
        $this->assertStringNotContainsString('id="percentage_hike"', $form);
        $this->assertStringNotContainsString('Hike Percentage', $form);
        $this->assertStringNotContainsString('Hike Percentage', $drawer);
        $this->assertStringNotContainsString('function percentage_hike()', $form);
    }

    public function test_create_and_edit_persist_one_normalized_mode_and_calculated_increase(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
        $store = $this->between($controller, 'public function store(Request $request)', 'public function googleSheetCandidateData');
        $update = $this->between($controller, 'public function update(Request $request, $id)', 'public function formatInterviewDateTime');

        foreach ([$store, $update] as $path) {
            $this->assertStringContainsString('$salaryMode = $this->normalizedCandidateSalaryMode', $path);
            $this->assertStringContainsString('$jobApplication->current_ctc_mode = $salaryMode;', $path);
            $this->assertStringContainsString('$jobApplication->current_ectc_mode = $salaryMode;', $path);
            $this->assertStringContainsString('$this->calculatedCandidateSalaryIncrease(', $path);
            $this->assertStringNotContainsString('percentage_hike = $request->percentage_hike', $path);
            $this->assertStringNotContainsString('percentage_hike = @$request->percentage_hike', $path);
        }

        $this->assertStringContainsString('in_array($normalizedMode, [0, 1, 2], true)', $controller);
        $this->assertStringContainsString('(($expected - $current) / $current) * 100', $controller);
    }

    public function test_salary_helpers_normalize_modes_and_calculate_increase(): void
    {
        $controller = (new \ReflectionClass(
            \App\Http\Controllers\ServiceCompany\ServiceCompanyJobApplicationController::class
        ))->newInstanceWithoutConstructor();
        $reflection = new \ReflectionObject($controller);
        $modeMethod = $reflection->getMethod('normalizedCandidateSalaryMode');
        $increaseMethod = $reflection->getMethod('calculatedCandidateSalaryIncrease');

        $this->assertSame(0, $modeMethod->invoke($controller, null));
        $this->assertSame(1, $modeMethod->invoke($controller, '1'));
        $this->assertSame(2, $modeMethod->invoke($controller, 2));
        $this->assertSame(0, $modeMethod->invoke($controller, 99));
        $this->assertSame(20.0, $increaseMethod->invoke($controller, 1000000, 1200000));
        $this->assertNull($increaseMethod->invoke($controller, 0, 1200000));
        $this->assertNull($increaseMethod->invoke($controller, 1000000, 0));
    }

    public function test_candidate_drawer_uses_the_saved_candidate_frequency(): void
    {
        $drawer = $this->source('resources/views/service_company/job-applications/showdetail.blade.php');

        $this->assertStringContainsString("\$isDirectCandidate = (@\$application->condidate_type === 'other');", $drawer);
        $this->assertStringContainsString(
            '$jobMode = $isDirectCandidate ? (int) (@$application->current_ctc_mode ?? 0)',
            $drawer
        );
        $this->assertStringContainsString('$salaryMode[$current_ctc_mode ?? 0]', $drawer);
        $this->assertStringContainsString('$salaryMode[$current_ectc_mode ?? 0]', $drawer);
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
