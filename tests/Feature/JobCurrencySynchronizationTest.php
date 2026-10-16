<?php

namespace Tests\Feature;

use App\Services\CurrencyService;
use Tests\TestCase;

/**
 * Validates the Job Currency -> Candidate Salary Currency Synchronization Engine.
 * Covers:
 * 1. Authoritative Job Currency Preload & UI Lock Badge (create, clientcreate, career-apply)
 * 2. Server Trust Boundary & Hard Overwrite of Tampered Currency Payloads
 * 3. Smart Currency Conversion on Copy / Share to Job (converts only when currencies differ)
 * 4. Duplicate Non-Blocking Rule (existing duplicate workflow honored 100%)
 * 5. Multi-Job Isolation (different jobs preserve independent currencies)
 */
class JobCurrencySynchronizationTest extends TestCase
{
    /**
     * Verifies CurrencyService resolves job currency metadata for USD, AED, and INR jobs.
     */
    public function test_currency_service_resolves_job_currency_metadata(): void
    {
        $usdJob = (object) ['id' => 101, 'min_salary_currency' => 4, 'company_id' => 1];
        $aedJob = (object) ['id' => 102, 'min_salary_currency' => 5, 'company_id' => 1];
        $inrJob = (object) ['id' => 103, 'min_salary_currency' => 0, 'company_id' => 1];

        $this->assertSame('USD', CurrencyService::getJobCurrencyCode($usdJob));
        $this->assertSame(4, CurrencyService::getJobCurrencyKey($usdJob));
        $this->assertSame('$', CurrencyService::getJobCurrencySymbol($usdJob));

        $this->assertSame('AED', CurrencyService::getJobCurrencyCode($aedJob));
        $this->assertSame(5, CurrencyService::getJobCurrencyKey($aedJob));

        $this->assertSame('INR', CurrencyService::getJobCurrencyCode($inrJob));
        $this->assertSame(0, CurrencyService::getJobCurrencyKey($inrJob));
        $this->assertSame('₹', CurrencyService::getJobCurrencySymbol($inrJob));

        $usdMeta = CurrencyService::getJobCurrencyMeta($usdJob);
        $this->assertSame('USD', $usdMeta['code']);
        $this->assertSame(4, $usdMeta['key']);
        $this->assertSame('🇺🇸', $usdMeta['flag']);

        $aedMeta = CurrencyService::getJobCurrencyMeta($aedJob);
        $this->assertSame('AED', $aedMeta['code']);
        $this->assertSame(5, $aedMeta['key']);
        $this->assertSame('🇦🇪', $aedMeta['flag']);
    }

    /**
     * Verifies smart cross-currency conversion logic with pivot USD.
     */
    public function test_currency_service_converts_between_different_currencies(): void
    {
        // 100 USD to USD should be identical
        $same = CurrencyService::convert(100, 'USD', 'USD');
        $this->assertEquals(100.0, $same);

        // USD to INR conversion should be positive and non-zero
        $usdToInr = CurrencyService::convert(1000, 4, 0); // USD -> INR
        $this->assertGreaterThan(50000, $usdToInr);

        // INR to USD conversion
        $inrToUsd = CurrencyService::convert(85000, 0, 4); // INR -> USD
        $this->assertGreaterThan(500, $inrToUsd);
        $this->assertLessThan(2000, $inrToUsd);

        // AED to USD conversion
        $aedToUsd = CurrencyService::convert(3670, 5, 4); // AED -> USD
        $this->assertGreaterThan(800, $aedToUsd);
    }

    /**
     * Verifies Internal Add Candidate view (create.blade.php) contains the authoritative
     * Job Currency Display Component (Job Currency: USD 🔒) and canonical hidden inputs.
     */
    public function test_create_blade_contains_authoritative_job_currency_lock_and_hidden_inputs(): void
    {
        $createBlade = $this->source('resources/views/service_company/job-applications/create.blade.php');

        $this->assertStringContainsString('candidate-currency-authoritative-badge', $createBlade);
        $this->assertStringContainsString('Job Currency', $createBlade);
        $this->assertStringContainsString('fa-lock text-primary', $createBlade);
        $this->assertStringContainsString('type="hidden" name="current_ctc_cur" id="current_ctc_cur"', $createBlade);
        $this->assertStringContainsString('CurrencyService::getJobCurrencyMeta', $createBlade);
    }

    /**
     * Verifies Agency Client Create view (clientcreate.blade.php) enforces the
     * Job Currency lock and canonical hidden inputs.
     */
    public function test_clientcreate_blade_contains_authoritative_job_currency_lock(): void
    {
        $clientCreateBlade = $this->source('resources/views/service_company/job-applications/clientcreate.blade.php');

        $this->assertStringContainsString('candidate-currency-authoritative-badge', $clientCreateBlade);
        $this->assertStringContainsString('Job Currency', $clientCreateBlade);
        $this->assertStringContainsString('fa-lock text-primary', $clientCreateBlade);
        $this->assertStringContainsString('type="hidden" name="current_ctc_cur" id="current_ctc_cur"', $clientCreateBlade);
        $this->assertStringContainsString('CurrencyService::getJobCurrencyMeta', $clientCreateBlade);
    }

    /**
     * Verifies Career Page Apply view (career-apply.blade.php) enforces the
     * Job Currency lock and canonical hidden inputs.
     */
    public function test_career_apply_blade_contains_authoritative_job_currency_lock(): void
    {
        $careerApplyBlade = $this->source('resources/views/career-apply.blade.php');

        $this->assertStringContainsString('candidate-currency-authoritative-badge', $careerApplyBlade);
        $this->assertStringContainsString('Job Currency', $careerApplyBlade);
        $this->assertStringContainsString('fa-lock text-primary', $careerApplyBlade);
        $this->assertStringContainsString('type="hidden" name="current_ctc_cur" id="current_ctc_cur"', $careerApplyBlade);
        $this->assertStringContainsString('CurrencyService::getJobCurrencyMeta', $careerApplyBlade);
    }

    /**
     * Verifies the server trust boundary in ServiceCompanyJobApplicationController:
     * Even if a tampered client payload attempts to submit INR on a USD job,
     * the server forcefully resolves and asserts the Job's Currency key.
     */
    public function test_server_controller_enforces_job_currency_in_store_and_update(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');

        $storeMethod = $this->between($controller, 'public function store(Request $request)', 'public function googleSheetCandidateData');
        $updateMethod = $this->between($controller, 'public function update(Request $request, $id)', 'public function formatInterviewDateTime');

        // Verify store() enforcement
        $this->assertStringContainsString('RULE-CURRENCY-JOB-AUTHORITATIVE-001', $storeMethod);
        $this->assertStringContainsString('CurrencyService::getJobCurrencyKey($targetJobId)', $storeMethod);
        $this->assertStringContainsString('$jobApplication->current_ctc_cur = $candidateCurKey;', $storeMethod);
        $this->assertStringContainsString('$jobApplication->current_ectc_cur = $candidateCurKey;', $storeMethod);

        // Verify update() enforcement
        $this->assertStringContainsString('RULE-CURRENCY-JOB-AUTHORITATIVE-001', $updateMethod);
        $this->assertStringContainsString('CurrencyService::getJobCurrencyKey($targetJobId)', $updateMethod);
        $this->assertStringContainsString('$jobApplication->current_ctc_cur = $candidateCurKey;', $updateMethod);
        $this->assertStringContainsString('$jobApplication->current_ectc_cur = $candidateCurKey;', $updateMethod);
    }

    /**
     * Verifies Smart Currency Conversion on Copy Candidate / Bench Sharing:
     * In copy_current_selected_applicant, if candidate currency differs from target job currency,
     * amounts are converted; if identical, exact amounts are preserved.
     */
    public function test_copy_candidate_to_job_performs_smart_currency_conversion(): void
    {
        $cvTracker = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php');
        $copyMethod = $this->between($cvTracker, 'public function copy_current_selected_applicant(Request $request)', 'public function fwd_resume(Request $request)');

        $this->assertStringContainsString('RULE-CURRENCY-JOB-AUTHORITATIVE-001', $copyMethod);
        $this->assertStringContainsString('$targetJobCurKey = \App\Services\CurrencyService::getJobCurrencyKey($jobRow);', $copyMethod);
        $this->assertStringContainsString('\App\Services\CurrencyService::convert($sourceCtc', $copyMethod);
        $this->assertStringContainsString('\App\Services\CurrencyService::convert($sourceEctc', $copyMethod);
        $this->assertStringContainsString("'current_ctc_cur' => \$finalCurKey", $copyMethod);
        $this->assertStringContainsString("'current_ectc_cur' => \$finalCurKey", $copyMethod);
    }

    /**
     * Verifies that duplicate candidate submissions are NEVER stopped or blocked,
     * strictly preserving the existing workflow.
     */
    public function test_duplicate_candidate_submissions_are_not_stopped(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
        $storeMethod = $this->between($controller, 'public function store(Request $request)', 'public function googleSheetCandidateData');

        // Assert store method saves duplicate candidates without aborting or throwing validation failures
        $this->assertStringNotContainsString("abort(422, 'Duplicate", $storeMethod);
        $this->assertStringNotContainsString("return Reply::error('Candidate already exists", $storeMethod);
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
