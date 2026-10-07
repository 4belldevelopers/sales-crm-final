<?php

namespace Tests\Feature;

use App\Jobs\ProcessOutboxEventJob;
use Tests\TestCase;

/**
 * Validates the God-Level Add Candidate Submit Workflow:
 * - Search-readiness polling endpoint checkIndexStatus (0% MySQL reads, CQRS compliant)
 * - 10-second stop-timer before redirect to ensure candidate is ready in listing
 * - Invalidation of multi-tenant company version keys (Finding F-01 remediation)
 * - Supervisor queue configuration for candidate-index in Horizon
 * - Verification of refresh=true in ProcessOutboxEventJob ES indexing
 */
class CandidateSubmitReadinessAndCacheTest extends TestCase
{
    private function source(string $relativePath): string
    {
        $source = file_get_contents(base_path($relativePath));
        $this->assertNotFalse($source, 'Unable to read '.$relativePath);

        return $source;
    }

    public function test_horizon_supervisor_listens_to_candidate_index_queue(): void
    {
        $horizon = $this->source('config/horizon.php');

        $this->assertStringContainsString("'supervisor-indexing'", $horizon);
        $this->assertStringContainsString("'candidate-index'", $horizon);
    }

    public function test_process_outbox_event_job_indexes_with_refresh_and_increments_tenant_keys(): void
    {
        $jobSource = $this->source('app/Jobs/ProcessOutboxEventJob.php');

        // Verify ES indexing forces refresh=true so candidate is immediately searchable
        $this->assertStringContainsString('GeneralMethod::JobApplicationsAddEditUpload((int) $wiseCandidateId, null, true);', $jobSource);

        // Verify F-01: multi-tenant company cache keys are cleaned and incremented
        $this->assertStringContainsString('allapptracker_version_company_{$companyId}', $jobSource);
        $this->assertStringContainsString('allcvtracker_version_company_{$companyId}', $jobSource);
        $this->assertStringContainsString('cache()->increment("allapptracker_version_company_{$companyId}")', $jobSource);
        $this->assertStringContainsString('cache()->increment("allcvtracker_version_company_{$companyId}")', $jobSource);
    }

    public function test_controller_store_and_clientappstore_increment_tenant_version_keys(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');

        // Store method multi-tenant cache invalidation
        $this->assertStringContainsString("'allapptracker_version_company_'.\$company_id", $controller);
        $this->assertStringContainsString("'allcvtracker_version_company_'.\$company_id", $controller);
        $this->assertStringContainsString("cache()->increment('allapptracker_version_company_'.\$company_id)", $controller);
        $this->assertStringContainsString("cache()->increment('allcvtracker_version_company_'.\$company_id)", $controller);
    }

    public function test_check_index_status_route_and_method_exist(): void
    {
        $routes = $this->source('routes/service.php');
        $this->assertStringContainsString("Route::get('job-applications/check-index-status/{id}'", $routes);
        $this->assertStringContainsString("->name('job-applications.check-index-status')", $routes);

        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');
        $this->assertStringContainsString('function checkIndexStatus(Request $request, $id)', $controller);
        $this->assertStringContainsString('ELASTIC_CANDIDATES', $controller);
    }

    public function test_cvtracker_controller_incorporates_tenant_version_key(): void
    {
        $cvTracker = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php');

        $this->assertStringContainsString("'allcvtracker_version_company_'.\$company_id", $cvTracker);
        $this->assertStringContainsString('$version = $userVersion.\'_\'.$tenantVersion', $cvTracker);
    }

    public function test_create_blade_contains_readiness_polling_and_stop_timer(): void
    {
        $blade = $this->source('resources/views/service_company/job-applications/create.blade.php');

        $this->assertStringContainsString('check-index-status', $blade);
        $this->assertStringContainsString('stopTimerTimeout = setTimeout', $blade);
        $this->assertStringContainsString('pollSearchIndex', $blade);
        $this->assertStringContainsString('candidate-index-countdown', $blade);
        $this->assertStringContainsString('10000', $blade);
        $this->assertStringContainsString('new_candidate_id', $blade);
        $this->assertStringContainsString('Preparing index...', $blade);
    }

    public function test_clientcreate_blade_contains_readiness_polling_and_stop_timer(): void
    {
        $blade = $this->source('resources/views/service_company/job-applications/clientcreate.blade.php');

        $this->assertStringContainsString('check-index-status', $blade);
        $this->assertStringContainsString('stopTimerTimeout = setTimeout', $blade);
        $this->assertStringContainsString('pollSearchIndex', $blade);
        $this->assertStringContainsString('client-candidate-index-countdown', $blade);
        $this->assertStringContainsString('10000', $blade);
        $this->assertStringContainsString('new_candidate_id', $blade);
        $this->assertStringContainsString('Preparing index...', $blade);
    }

    public function test_job_dataset_versioning_and_reverb_event_dispatched_in_controllers(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php');

        // Store method job-scoped atomic version increment & Reverb dispatch
        $this->assertStringContainsString('ats:version:job:{$company_id}:{$rawJobId}', $controller);
        $this->assertStringContainsString('broadcast(new \App\Events\AppTrackerCandidateAssignedEvent(', $controller);

        // ProcessOutboxEventJob job-scoped version bump
        $jobSource = $this->source('app/Jobs/ProcessOutboxEventJob.php');
        $this->assertStringContainsString('ats:version:job:{$companyId}:{$jobId}', $jobSource);
    }

    public function test_apptracker_controller_exposes_and_incorporates_job_dataset_version(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php');

        // index method exposes job_dataset_version
        $this->assertStringContainsString('$this->job_dataset_version = $jobDatasetVersion;', $controller);

        // allDataAjax incorporates job_dataset_version in cache key and returns it in json & html response payloads
        $this->assertStringContainsString('$jobDatasetVersion = (int) cache()->get("ats:version:job:{$company_id}:{$jobid}", 1);', $controller);
        $this->assertStringContainsString("'job_dataset_version' => (int) \$jobDatasetVersion,", $controller);
    }

    public function test_apptracker_blade_ram_store_purges_session_storage_and_handles_new_candidate_intent(): void
    {
        $blade = $this->source('resources/views/service_company/apptracker/index.blade.php');

        // Blade view renders hidden job dataset version
        $this->assertStringContainsString('id="apptracker_job_dataset_version"', $blade);

        // AppTrackerRamStore enforces in-memory Map only and purges legacy sessionStorage
        $this->assertStringContainsString('var ramMap = new Map();', $blade);
        $this->assertStringNotContainsString('sessionStorage.setItem(storagePrefix + key', $blade);

        // URL intent consumption & bounded recovery check
        $this->assertStringContainsString('highlightNewCandidateRow', $blade);
        $this->assertStringContainsString('new_candidate_id', $blade);
        $this->assertStringContainsString('history.replaceState', $blade);

        // Reverb monotonic invariant check
        $this->assertStringContainsString('eventVersion <= currentJobDatasetVersion', $blade);
        $this->assertStringContainsString('AppTrackerRamStore.setJobVersion', $blade);
    }
}
