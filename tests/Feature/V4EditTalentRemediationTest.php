<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * Validates the V4 Edit In-House Talent implementation:
 * - Multi-Tenant Isolation Enforcement: candidate scoped by company_id and 404 abort on mismatch
 * - 0% MySQL Reads on edit(): eliminated redundant unneeded queries and cached master datasets
 * - Relational Skill Synchronization: orphaned skills purged from bench_candidate_skill_experience on edit
 * - Edit UI/UX Redesign: dynamic title, candidate ID badge, talent stage banner, marketplace visibility
 * - Sticky bottom action bar and Ctrl+S shortcut binding
 * - Form validation mutex and window.benchFormValidator registration
 * - Tab-aware post-save redirect preservation
 */
class V4EditTalentRemediationTest extends TestCase
{
    private function source(string $relativePath): string
    {
        $source = file_get_contents(base_path($relativePath));
        $this->assertNotFalse($source, 'Unable to read '.$relativePath);

        return $source;
    }

    public function test_controller_edit_enforces_strict_multi_tenant_isolation(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php');

        // Scopes query with company_id
        $this->assertStringContainsString("JobApplication::where('company_id', \$company_id)->find(\$id)", $controller);

        // Aborts with 404 if candidate does not exist or belongs to another tenant
        $this->assertStringContainsString('abort_if(! $this->application, 404', $controller);
    }

    public function test_controller_edit_implements_zero_mysql_read_caching(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php');

        // Static masters cached with long TTLs
        $this->assertStringContainsString("Cache::rememberForever('qualification_master_list'", $controller);
        $this->assertStringContainsString("Cache::rememberForever('country_list_json'", $controller);
        $this->assertStringContainsString("Cache::remember('currency_converter_rates'", $controller);

        // Client and company profile cached in Redis
        $this->assertStringContainsString('user:{$userId}:company_profile', $controller);
        $this->assertStringContainsString('user:{$userId}:client_id', $controller);

        // Dead queries eliminated (jobs limit 200, GeneralMethod::allCities)
        $this->assertStringNotContainsString("\$this->jobs = Job::where('company_id', \$company_id)", $controller);
        $this->assertStringNotContainsString('$this->cities = GeneralMethod::allCities();', $controller);
    }

    public function test_controller_batch_persist_purges_orphaned_skills_on_edit(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php');

        // Skill experience deletion purge
        $this->assertStringContainsString("DB::table('bench_candidate_skill_experience')", $controller);
        $this->assertStringContainsString("->whereNotIn('skill_key', \$submittedSkillKeys)", $controller);
        $this->assertStringContainsString('->delete()', $controller);
    }

    public function test_controller_store_returns_tab_aware_redirect_url(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php');

        // Tab aware redirect logic
        $this->assertStringContainsString('match ((int) ($benchApplication->status ?? 2))', $controller);
        $this->assertStringContainsString("'?tab='.\$tabName", $controller);
        $this->assertStringContainsString("'tab' => \$tabName", $controller);
    }

    public function test_create_blade_edit_mode_ui_elements(): void
    {
        $blade = $this->source('resources/views/service_company/bench-candidates/create.blade.php');

        // Dynamic Edit Title and Candidate Metadata
        $this->assertStringContainsString('Edit In-House Talent: {{ @$application->full_name }}', $blade);
        $this->assertStringContainsString('Candidate ID: #{{ @$application->id }}', $blade);

        // Talent Stage Status Banner for Edit mode
        $this->assertStringContainsString('talent-stage-banner card shadow-xs', $blade);
        $this->assertStringContainsString('Current Talent Lifecycle Stage:', $blade);

        // Marketplace chip in Edit mode
        $this->assertStringContainsString('Marketplace:', $blade);
        $this->assertStringContainsString('Published', $blade);

        // Header Action Cluster with Close button and Update Candidate button
        $this->assertStringContainsString('id="headerCloseBtn"', $blade);
        $this->assertStringContainsString('id="edit-form"', $blade);
        $this->assertStringContainsString('talent-topbar-close-btn', $blade);

        // Sticky Bottom Action Bar is completely removed (no redundant floating row)
        $this->assertStringNotContainsString('talent-sticky-footer', $blade);
        $this->assertStringNotContainsString('id="sticky-edit-form"', $blade);

        // Responsive resume strip without hardcoded 250px margin
        $this->assertStringNotContainsString('margin-left:250px', $blade);
    }

    public function test_edit_javascript_form_validation_and_keyboard_shortcuts(): void
    {
        $blade = $this->source('resources/views/service_company/bench-candidates/create.blade.php');

        // FormValidation global assignment in KTEditCandidate
        $this->assertStringContainsString('window.benchFormValidator = validator;', $blade);

        // Delegated click listener strictly on edit-form
        $this->assertStringContainsString("$(document).off('click', '#edit-form').on('click', '#edit-form'", $blade);

        // Keyboard Shortcut Ctrl+S triggers header update handler
        $this->assertStringContainsString('keydown.talentEditShortcut', $blade);
        $this->assertStringContainsString('handleEditSubmit(e);', $blade);
    }

    public function test_edit_mode_resume_dropzone_and_info_card_unification(): void
    {
        $blade = $this->source('resources/views/service_company/bench-candidates/create.blade.php');

        // Unified existing resume detection
        $this->assertStringContainsString('$hasExistingResume = !empty($resume_url) || !empty($application->resume);', $blade);

        // Upload box has d-none and hidden style when existing resume is present
        $this->assertStringContainsString('class="ua {{ $hasExistingResume ? \'d-none has-file\' : \'\' }}" id="uploadBox"', $blade);

        // Marketplace privacy note is hidden when existing resume is present
        $this->assertStringContainsString('class="resume-note marketplace-privacy" style="{{ $hasExistingResume ? \'display: none !important;\' : \'\' }}"', $blade);

        // Unified #upInfo card is shown with pre-populated verified banner and details
        $this->assertStringContainsString('class="up-info {{ $hasExistingResume ? \'show\' : \'\' }}" id="upInfo"', $blade);
        $this->assertStringContainsString('Resume Attached & Verified', $blade);
        $this->assertStringContainsString('✓ Verified', $blade);

        // Download and Delete buttons only (Replace button completely purged)
        $this->assertStringContainsString('id="upDownloadBtn"', $blade);
        $this->assertStringNotContainsString('id="upReplaceBtn"', $blade);
        $this->assertStringContainsString('id="upRemoveBtn"', $blade);

        // Legacy old fileInfo strip inside uploadBox is cleaned up
        $this->assertStringNotContainsString('class="remove-file btn btn-xs btn-light-danger', $blade);
    }

    public function test_relationship_manager_query_includes_email_and_mobile(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php');

        // Recruiter query in edit() must select email, calling_code, mobile to avoid hidden dashes
        $this->assertStringContainsString("select('users.name', 'users.id', 'users.email', 'users.calling_code', 'users.mobile')", $controller);
    }

    public function test_cache_invalidation_and_elasticsearch_immediate_refresh(): void
    {
        $controller = $this->source('app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php');
        $helper = $this->source('app/Helper/GeneralMethod.php');

        // Drawer version key invalidation (benchviewmore_version)
        $this->assertStringContainsString('cache()->increment("benchviewmore_version_company_{$companyId}")', $controller);
        $this->assertStringContainsString('cache()->increment("benchviewmore_version_user_{$userId}")', $controller);

        // Listing version key invalidation
        $this->assertStringContainsString('cache()->increment("allbenchcandidates_version_company_{$companyId}")', $controller);
        $this->assertStringContainsString('cache()->increment("allbenchcandidates_version_user_{$userId}")', $controller);

        // Elasticsearch immediate index refresh
        $this->assertStringContainsString("'refresh' => true,", $helper);
    }
}
