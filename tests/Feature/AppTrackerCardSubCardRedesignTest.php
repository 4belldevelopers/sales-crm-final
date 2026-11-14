<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * AppTrackerCardSubCardRedesignTest
 *
 * Intent: Verifies that both AppTracker (service_company.apptracker.index +
 * ajax_pages.all_apptracker) and ClientAppTracker (service_company.apptracker.clientapp +
 * ajax_pages.clientappdata_ajax) adhere to the Rule 20 Modern Card + Sub-Card
 * Enterprise Design System while preserving 100% of interactive triggers,
 * permissions, CQRS contracts, and Rule 17 page isolation.
 */
class AppTrackerCardSubCardRedesignTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Cache::put('global_feedback_status', [
            1 => ['id' => 1, 'name' => 'Recommended', 'classname' => 'badge-light-success'],
            2 => ['id' => 2, 'name' => 'Not Recommended', 'classname' => 'badge-light-danger'],
        ], 86400);

        Cache::forever('ref:interview_sub_status:all', collect([]));
        Cache::forever('ref_country_name:101', [['id' => 101, 'name' => 'India', 'sortname' => 'IN']]);
    }

    private function makeCandidateRow(array $overrides = []): array
    {
        return array_merge([
            'id' => 101,
            'candidate_id' => 101,
            'wiseId' => 5001,
            'jobId' => 117,
            'job_id' => 117,
            'company_id' => 1,
            'ClientComID' => 1,
            'full_name' => 'Disha Gupta',
            'email' => 'disha@example.com',
            'phone' => '9999999999',
            'condidate_type' => 'normal',
            'photo' => '',
            'skills' => 'PHP, Laravel, MySQL',
            'current_designation' => 'Lead Full Stack PHP Engineer',
            'current_company' => '4Bell Tech',
            'apl_company_name' => '4Bell Tech',
            'total_exp' => '5.6',
            'rel_exp' => '4.0',
            'ctc' => 120000,
            'ectc' => 150000,
            'ctc_yearly' => 1200000,
            'ectc_yearly' => 1500000,
            'current_ctc_mode' => 0,
            'current_ctc_cur' => 0,
            'outsourcing_type' => 1,
            'np_id' => '30',
            'serving_notice_period_mode' => 0,
            'city_name' => 'Noida',
            'country_name' => 'India',
            'country' => 101,
            'duplicate' => 0,
            'qualification_name' => 'mba/pgdm',
            'posted_by' => 'Vibhanshu',
            'post_company_name' => '4Bell Technology',
            'company_name' => '4Bell Technology',
            'job_master_status_id' => 8,
            'childStatus' => 0,
            'css_style' => 'badge-light-warning',
            'name' => 'InReview',
            'status' => 2,
        ], $overrides);
    }

    private function baseRowMaps(int $jobId = 117): array
    {
        return [
            'jobs' => [
                $jobId => (object) [
                    'id' => $jobId,
                    'max_sal_mode' => 0,
                    'max_salary_currency' => 0,
                ],
            ],
            'skills' => [
                $jobId => [
                    (object) ['job_id' => $jobId, 'skill_name' => 'PHP'],
                    (object) ['job_id' => $jobId, 'skill_name' => 'Laravel'],
                ],
            ],
            'duplicates' => [],
            'interviews' => [],
            'spokperson_map' => [],
            'child_status_map' => [],
            'rec_assigned' => [],
            'source_from_type' => [],
            'parent_assigned' => [],
            'existing_in_job' => [],
        ];
    }

    private function actingUser(): User
    {
        $user = new User;
        $user->id = 1;
        $user->company_id = 1;
        $company = new Company;
        $company->id = 1;
        $company->app_duplicate = 0;
        $company->app_duplicate_days = 0;
        $user->setRelation('company', $company);
        $this->actingAs($user);

        return $user;
    }

    public function test_all_apptracker_renders_rule_20_card_subcard_layout_without_raw_tables(): void
    {
        $this->actingUser();

        $html = View::make('service_company.ajax_pages.all_apptracker', [
            'data' => [$this->makeCandidateRow()],
            'userPermissions' => ['view_job_applications', 'salary_view'],
            'rowMaps' => $this->baseRowMaps(),
        ])->render();

        $this->assertStringContainsString('aptk-cand-card', $html);
        $this->assertStringContainsString('aptk-cand-grid', $html);
        $this->assertStringContainsString('aptk-cand-subcard', $html);
        $this->assertStringContainsString('aptk-cand-bottom', $html);
        $this->assertStringContainsString('aptk-edu-uppercase', $html);
        $this->assertStringContainsString('show-application-detail', $html);
        $this->assertStringContainsString('aptk-subcard-stage stage_sub_stage', $html);
        $this->assertStringContainsString('jw-row-match-badge', $html);
        $this->assertStringContainsString('data-candidate-id="101"', $html);
        $this->assertStringContainsString('data-wise-id="5001"', $html);
        $this->assertStringContainsString('Disha Gupta', $html);
        $this->assertStringContainsString('NP: 30 Days', $html);
        $this->assertStringContainsString('Matches a required skill', $html);
        $this->assertStringNotContainsString('aptk-view-profile-btn', $html);
        $this->assertStringNotContainsString('View Profile', $html);
        $this->assertStringNotContainsString('<tr', $html);
        $this->assertStringNotContainsString('<td', $html);
    }

    public function test_all_apptracker_hides_salary_when_salary_view_permission_absent_and_renders_empty_state(): void
    {
        $this->actingUser();

        $htmlNoSal = View::make('service_company.ajax_pages.all_apptracker', [
            'data' => [$this->makeCandidateRow()],
            'userPermissions' => ['view_job_applications'],
            'rowMaps' => $this->baseRowMaps(),
        ])->render();

        $this->assertStringContainsString('<span class="aptk-sal-main text-muted">N/A</span>', $htmlNoSal);

        $htmlEmpty = View::make('service_company.ajax_pages.all_apptracker', [
            'data' => [],
            'userPermissions' => ['view_job_applications'],
            'rowMaps' => $this->baseRowMaps(),
        ])->render();

        $this->assertStringContainsString('aptk-empty-card', $htmlEmpty);
        $this->assertStringContainsString('No Candidates Found', $htmlEmpty);
    }

    public function test_clientappdata_ajax_renders_rule_20_card_subcard_with_editable_and_locked_stages(): void
    {
        $this->actingUser();

        $inReviewCand = $this->makeCandidateRow([
            'id' => 201,
            'candidate_id' => 201,
            'wiseId' => 6001,
            'job_master_status_id' => 8,
            'name' => 'InReview',
        ]);
        $sourcedCand = $this->makeCandidateRow([
            'id' => 202,
            'candidate_id' => 202,
            'wiseId' => 6002,
            'full_name' => 'Harish Kumar',
            'job_master_status_id' => 1,
            'name' => 'Sourcing',
        ]);

        $html = View::make('service_company.ajax_pages.clientappdata_ajax', [
            'data' => [$inReviewCand, $sourcedCand],
            'userPermissions' => ['view_job_applications'],
            'rowMaps' => $this->baseRowMaps(),
            'citiesMap' => [],
            'jobid' => 117,
            'company_id' => 1,
        ])->render();

        $this->assertStringContainsString('caf-cand-card', $html);
        $this->assertStringContainsString('caf-cand-grid', $html);
        $this->assertStringContainsString('caf-cand-subcard', $html);
        $this->assertStringContainsString('caf-cand-bottom', $html);
        $this->assertStringContainsString('caf-edu-uppercase', $html);
        $this->assertStringContainsString('caf-subcard-stage client_stage_sub_stage', $html);
        $this->assertStringContainsString('caf-subcard-stage client_stage_locked_notice', $html);
        $this->assertStringContainsString('Client Managed', $html);
        $this->assertStringContainsString('show-application-detail', $html);
        $this->assertStringContainsString('NP: 30 Days', $html);
        $this->assertStringNotContainsString('caf-view-profile-btn', $html);
        $this->assertStringNotContainsString('View Profile', $html);
        $this->assertStringContainsString('#0891b2', $html);
        $this->assertStringContainsString('background:#ecfeff;color:#0e7490;border-color:#a5f3fc;', $html);
        $this->assertStringNotContainsString('<tr', $html);
        $this->assertStringNotContainsString('<td', $html);

        $htmlEmpty = View::make('service_company.ajax_pages.clientappdata_ajax', [
            'data' => [],
            'userPermissions' => ['view_job_applications'],
            'rowMaps' => $this->baseRowMaps(),
            'citiesMap' => [],
            'jobid' => 117,
            'company_id' => 1,
        ])->render();

        $this->assertStringContainsString('caf-empty-card', $htmlEmpty);
    }

    public function test_compensation_card_renders_clean_currency_glyph_and_trims_trailing_experience_dot(): void
    {
        $this->actingUser();

        $legacyMap = [
            0 => '<i class="fa fa-inr" aria-hidden="true"></i>',
            1 => '<i class="fa fa-eur" aria-hidden="true"></i>',
            4 => '<i class="fa fa-usd" aria-hidden="true"></i>',
            5 => '<span class="fw-bold">AED </span>',
        ];
        $this->assertSame('₹', \App\Services\CurrencyService::resolvePlainSymbolFromLegacy(0, $legacyMap));
        $this->assertSame('€', \App\Services\CurrencyService::resolvePlainSymbolFromLegacy(1, $legacyMap));
        $this->assertSame('$', \App\Services\CurrencyService::resolvePlainSymbolFromLegacy(4, $legacyMap));
        $this->assertSame('AED ', \App\Services\CurrencyService::resolvePlainSymbolFromLegacy(5, $legacyMap));

        $row = $this->makeCandidateRow([
            'total_exp' => '10.',
            'rel_exp' => '7.',
        ]);

        $appHtml = View::make('service_company.ajax_pages.all_apptracker', [
            'data' => [$row],
            'userPermissions' => ['view_job_applications', 'salary_view'],
            'rowMaps' => $this->baseRowMaps(),
        ])->render();

        $this->assertStringNotContainsString('&lt;i class=&quot;fa fa-', $appHtml);
        $this->assertStringNotContainsString('<i class="fa fa-inr', $appHtml);
        $this->assertStringContainsString('₹', $appHtml);
        $this->assertStringContainsString('<strong>10 Y</strong> Total', $appHtml);
        $this->assertStringContainsString('(Rel <strong>7 Y</strong>)', $appHtml);
        $this->assertStringNotContainsString('10. Y', $appHtml);
        $this->assertStringNotContainsString('7. Y', $appHtml);

        $clientHtml = View::make('service_company.ajax_pages.clientappdata_ajax', [
            'data' => [$row],
            'userPermissions' => ['view_job_applications', 'salary_view'],
            'rowMaps' => $this->baseRowMaps(),
            'citiesMap' => [],
            'jobid' => 117,
            'company_id' => 1,
        ])->render();

        $this->assertStringNotContainsString('&lt;i class=&quot;fa fa-', $clientHtml);
        $this->assertStringNotContainsString('<i class="fa fa-inr', $clientHtml);
        $this->assertStringContainsString('₹', $clientHtml);
        $this->assertStringContainsString('<strong>10 Y</strong> Total', $clientHtml);
        $this->assertStringContainsString('(Rel <strong>7 Y</strong>)', $clientHtml);
        $this->assertStringNotContainsString('10. Y', $clientHtml);
        $this->assertStringNotContainsString('7. Y', $clientHtml);
    }

    public function test_host_views_enforce_card_subcard_stream_and_rule_17_isolation(): void
    {
        $indexView = file_get_contents(resource_path('views/service_company/apptracker/index.blade.php'));
        $clientAppView = file_get_contents(resource_path('views/service_company/apptracker/clientapp.blade.php'));

        $this->assertStringContainsString('id="candidatetList" class="aptk-cand-stream"', $indexView);
        $this->assertStringContainsString('id="btnAptkSearchClear"', $indexView);
        $this->assertStringContainsString('id="btnAptkRefreshList"', $indexView);
        $this->assertStringContainsString('aptk-stream-bar', $indexView);
        $this->assertStringNotContainsString('<table class="table align-middle table-row-dashed', $indexView);
        $this->assertStringNotContainsString('caf-cand-card', $indexView);
        $this->assertStringContainsString("'color' => '#0891b2', 'bg' => '#ecfeff'", $indexView);
        $this->assertStringContainsString('#bg_all.hp-total.hp-org-my.bg-total-active', $indexView);
        $this->assertStringContainsString('#fde047', $indexView);
        $this->assertStringContainsString('#bg_all.hp-total.hp-org-internal.bg-total-active', $indexView);
        $this->assertStringContainsString('#1e3a8a', $indexView);
        $this->assertStringContainsString('#bg_all.hp-total.hp-org-direct.bg-total-active', $indexView);
        $this->assertStringContainsString('#a7f3d0', $indexView);
        $this->assertStringContainsString('#064e3b', $indexView);
        $this->assertStringContainsString('.aptk-linkedin-pill', $indexView);
        $this->assertStringContainsString('#ecfdf5', $indexView);
        $this->assertStringContainsString('#15803d', $indexView);

        $this->assertStringContainsString('id="candidatetList" class="caf-cand-stream"', $clientAppView);
        $this->assertStringContainsString('id="btnCafSearchClear"', $clientAppView);
        $this->assertStringContainsString('id="btnCafRefreshList"', $clientAppView);
        $this->assertStringContainsString('caf-stream-bar', $clientAppView);
        $this->assertStringContainsString('_cafSearchTimer', $clientAppView);
        $this->assertStringContainsString('_cafListXhr.abort()', $clientAppView);
        $this->assertStringNotContainsString('<table class="table align-middle table-row-dashed', $clientAppView);
        $this->assertStringNotContainsString('aptk-cand-card', $clientAppView);
        $this->assertStringContainsString("'color' => '#0891b2', 'bg' => '#ecfeff'", $clientAppView);
        $this->assertStringContainsString('#bg_all.hp-total.hp-org-my.bg-total-active', $clientAppView);
        $this->assertStringContainsString('#fde047', $clientAppView);
        $this->assertStringContainsString('#bg_all.hp-total.hp-org-internal.bg-total-active', $clientAppView);
        $this->assertStringContainsString('#1e3a8a', $clientAppView);
        $this->assertStringContainsString('#bg_all.hp-total.hp-org-direct.bg-total-active', $clientAppView);
        $this->assertStringContainsString('#a7f3d0', $clientAppView);
        $this->assertStringContainsString('#064e3b', $clientAppView);
        $this->assertStringContainsString('.caf-linkedin-pill', $clientAppView);
        $this->assertStringContainsString('#ecfdf5', $clientAppView);
        $this->assertStringContainsString('#15803d', $clientAppView);
    }

    public function test_resolve_job_org_class_distinguishes_my_org_internal_and_direct_client(): void
    {
        $myOrgJob = (object) ['company_id' => 10, 'self_company' => 1, 'client_id' => 1];
        $internalClientJob = (object) ['company_id' => 10, 'self_company' => 0, 'client_id' => 2];
        $directClientJob = (object) ['company_id' => 99, 'self_company' => 0, 'client_id' => 3];

        $this->assertSame(
            'hp-org-my',
            \App\Http\Controllers\ServiceCompany\ServiceCompanyAppTrackerController::resolveJobOrgClass($myOrgJob, 10)
        );
        $this->assertSame(
            'hp-org-internal',
            \App\Http\Controllers\ServiceCompany\ServiceCompanyAppTrackerController::resolveJobOrgClass($internalClientJob, 10)
        );
        $this->assertSame(
            'hp-org-direct',
            \App\Http\Controllers\ServiceCompany\ServiceCompanyAppTrackerController::resolveJobOrgClass($directClientJob, 10)
        );
    }

    public function test_candidate_card_enhancements_display_provided_fields_and_dash_placeholders_when_missing(): void
    {
        $this->actingUser();

        $richRow = $this->makeCandidateRow([
            'current_company' => 'Acme Systems',
            'apl_company_name' => 'Fallback Agency Name',
            'wm' => 'remote',
            'np_id' => '9',
            'serving_notice_period_mode' => 0,
            'created_at' => now()->subDays(20)->toIso8601String(),
            'linkedin' => 'https://www.linkedin.com/in/disha-gupta',
            'visastatu' => 'H-1B',
            'country_name' => 'United States',
        ]);

        $richAppHtml = View::make('service_company.ajax_pages.all_apptracker', [
            'data' => [$richRow],
            'userPermissions' => ['view_job_applications', 'salary_view'],
            'rowMaps' => $this->baseRowMaps(),
        ])->render();

        $this->assertStringContainsString('Acme Systems', $richAppHtml);
        $this->assertStringContainsString('Location &amp; Degree', $richAppHtml);
        $this->assertStringContainsString('Remote', $richAppHtml);
        $this->assertStringContainsString('Ownership &amp; Recruiter', $richAppHtml);
        $this->assertStringContainsString('Submitted: 20 days ago', $richAppHtml);
        $this->assertStringContainsString('NP: 9 Days', $richAppHtml);
        $this->assertStringContainsString('LinkedIn Verified', $richAppHtml);
        $this->assertStringContainsString('https://www.linkedin.com/in/disha-gupta', $richAppHtml);
        $this->assertStringContainsString('(US, H-1B)', $richAppHtml);
        $this->assertStringNotContainsString('View Profile', $richAppHtml);

        $richClientHtml = View::make('service_company.ajax_pages.clientappdata_ajax', [
            'data' => [$richRow],
            'userPermissions' => ['view_job_applications'],
            'rowMaps' => $this->baseRowMaps(),
            'citiesMap' => [],
            'jobid' => 117,
            'company_id' => 1,
        ])->render();

        $this->assertStringContainsString('LinkedIn Verified', $richClientHtml);
        $this->assertStringContainsString('https://www.linkedin.com/in/disha-gupta', $richClientHtml);
        $this->assertStringContainsString('(US, H-1B)', $richClientHtml);
        $this->assertStringContainsString('caf-visa-pill', $richClientHtml);
        $this->assertStringNotContainsString('View Profile', $richClientHtml);

        $minimalRow = $this->makeCandidateRow([
            'current_company' => '',
            'apl_company_name' => 'Agency Only Name',
            'current_designation' => '',
            'city_name' => '',
            'country_name' => '',
            'country' => null,
            'wm' => '',
            'job_mode' => '',
            'qualification_name' => '',
            'np_id' => '',
            'serving_notice_period_mode' => 0,
            'linkedin' => '',
            'visastatu' => '',
            'posted_by' => '',
            'post_company_name' => '',
            'company_name' => '',
            'created_at' => null,
        ]);

        $minimalClientHtml = View::make('service_company.ajax_pages.clientappdata_ajax', [
            'data' => [$minimalRow],
            'userPermissions' => ['view_job_applications'],
            'rowMaps' => $this->baseRowMaps(),
            'citiesMap' => [],
            'jobid' => 117,
            'company_id' => 1,
        ])->render();

        $this->assertStringNotContainsString('Agency Only Name', $minimalClientHtml);
        $this->assertStringContainsString('caf-cand-company', $minimalClientHtml);
        $this->assertStringContainsString('caf-cand-desig', $minimalClientHtml);
        $this->assertStringContainsString('caf-loc-city', $minimalClientHtml);
        $this->assertStringContainsString('caf-loc-mode', $minimalClientHtml);
        $this->assertStringContainsString('caf-loc-edu', $minimalClientHtml);
        $this->assertStringContainsString('caf-val-notice', $minimalClientHtml);
        $this->assertStringContainsString('caf-owner-name', $minimalClientHtml);
        $this->assertStringContainsString('caf-owner-company', $minimalClientHtml);
        $this->assertStringContainsString('caf-owner-submitted', $minimalClientHtml);
        $this->assertStringContainsString('>–<', $minimalClientHtml);
        $this->assertStringContainsString('Ownership &amp; Recruiter', $minimalClientHtml);
        $this->assertStringNotContainsString('LinkedIn Verified', $minimalClientHtml);
        $this->assertStringNotContainsString('caf-visa-pill', $minimalClientHtml);

        $minimalAppHtml = View::make('service_company.ajax_pages.all_apptracker', [
            'data' => [$minimalRow],
            'userPermissions' => ['view_job_applications', 'salary_view'],
            'rowMaps' => $this->baseRowMaps(),
        ])->render();

        $this->assertStringNotContainsString('Agency Only Name', $minimalAppHtml);
        $this->assertStringContainsString('aptk-cand-company', $minimalAppHtml);
        $this->assertStringContainsString('aptk-cand-desig', $minimalAppHtml);
        $this->assertStringContainsString('aptk-loc-city', $minimalAppHtml);
        $this->assertStringContainsString('aptk-loc-mode', $minimalAppHtml);
        $this->assertStringContainsString('aptk-loc-edu', $minimalAppHtml);
        $this->assertStringContainsString('aptk-val-notice', $minimalAppHtml);
        $this->assertStringContainsString('aptk-owner-submitted', $minimalAppHtml);
        $this->assertStringContainsString('>–<', $minimalAppHtml);

        $indexView = file_get_contents(resource_path('views/service_company/apptracker/index.blade.php'));
        $clientAppView = file_get_contents(resource_path('views/service_company/apptracker/clientapp.blade.php'));
        $this->assertStringContainsString("$(document).on('click', '.aptk-cand-card'", $indexView);
        $this->assertStringContainsString("$(document).on('click', '.caf-cand-card'", $clientAppView);
        $this->assertStringContainsString('.aptk-int-schedule-btn', $indexView);
        $this->assertStringContainsString('.caf-int-schedule-btn', $clientAppView);
    }

    public function test_interview_card_schedule_button_hides_when_scheduled_and_reactivates_on_next_stage(): void
    {
        $this->actingUser();

        $stage8Candidate = $this->makeCandidateRow([
            'id' => 301,
            'candidate_id' => 301,
            'wiseId' => 7001,
            'jobId' => 117,
            'job_id' => 117,
            'job_master_status_id' => 8,
            'name' => 'InReview',
        ]);

        // 1. No interview scheduled yet at Stage 8 -> Schedule Interview CTA rendered
        $noIntMaps = $this->baseRowMaps();
        $noIntMaps['stage_interviews'] = [];

        $appNoIntHtml = View::make('service_company.ajax_pages.all_apptracker', [
            'data' => [$stage8Candidate],
            'userPermissions' => ['view_job_applications', 'add_schedule'],
            'rowMaps' => $noIntMaps,
        ])->render();

        $clientNoIntHtml = View::make('service_company.ajax_pages.clientappdata_ajax', [
            'data' => [$stage8Candidate],
            'userPermissions' => ['view_job_applications', 'add_schedule'],
            'rowMaps' => $noIntMaps,
            'citiesMap' => [],
            'jobid' => 117,
            'company_id' => 1,
        ])->render();

        $this->assertStringContainsString('aptk-int-schedule-btn app_interview_schedule', $appNoIntHtml);
        $this->assertStringContainsString('Schedule Interview', $appNoIntHtml);
        $this->assertStringContainsString('data-wise-id="7001"', $appNoIntHtml);

        $this->assertStringContainsString('caf-int-schedule-btn app_interview_schedule', $clientNoIntHtml);
        $this->assertStringContainsString('Schedule Interview', $clientNoIntHtml);
        $this->assertStringContainsString('data-wise-id="7001"', $clientNoIntHtml);

        // 2. Active interview scheduled at Stage 8 -> Schedule Interview CTA hidden
        $stage8Scheduled = (object) [
            'id' => 9901,
            'job_application_id' => 301,
            'interview_job_wise_con_id' => 7001,
            'interview_job_id' => 117,
            'stage' => '8',
            'status' => 'pending',
            'schedule_date' => '2026-11-15 10:30:00',
            'schedule_to_time' => '10:30 AM',
            'timezone_abbreviation' => 'IST',
            'interview_mode' => 2,
        ];
        $stage8Maps = $this->baseRowMaps();
        $stage8Maps['interviews'] = [
            '301_117' => $stage8Scheduled,
            'w_7001_117' => $stage8Scheduled,
        ];
        $stage8Maps['stage_interviews'] = [
            '301_117_8' => $stage8Scheduled,
            'w_7001_117_8' => $stage8Scheduled,
        ];

        $appScheduledHtml = View::make('service_company.ajax_pages.all_apptracker', [
            'data' => [$stage8Candidate],
            'userPermissions' => ['view_job_applications', 'add_schedule'],
            'rowMaps' => $stage8Maps,
        ])->render();

        $clientScheduledHtml = View::make('service_company.ajax_pages.clientappdata_ajax', [
            'data' => [$stage8Candidate],
            'userPermissions' => ['view_job_applications', 'add_schedule'],
            'rowMaps' => $stage8Maps,
            'citiesMap' => [],
            'jobid' => 117,
            'company_id' => 1,
        ])->render();

        $this->assertStringNotContainsString('aptk-int-schedule-btn', $appScheduledHtml);
        $this->assertStringNotContainsString('Schedule Interview', $appScheduledHtml);
        $this->assertStringContainsString('15 Nov, 2026', $appScheduledHtml);

        $this->assertStringNotContainsString('caf-int-schedule-btn', $clientScheduledHtml);
        $this->assertStringNotContainsString('Schedule Interview', $clientScheduledHtml);
        $this->assertStringContainsString('15 Nov, 2026', $clientScheduledHtml);

        // 3. Candidate moves to next stage (Stage 2) where no interview is scheduled yet -> CTA re-activates
        $nextStageCandidate = array_merge($stage8Candidate, [
            'job_master_status_id' => 2,
            'name' => 'Screening',
        ]);

        $appNextStageHtml = View::make('service_company.ajax_pages.all_apptracker', [
            'data' => [$nextStageCandidate],
            'userPermissions' => ['view_job_applications', 'add_schedule'],
            'rowMaps' => $stage8Maps,
        ])->render();

        $clientNextStageHtml = View::make('service_company.ajax_pages.clientappdata_ajax', [
            'data' => [$nextStageCandidate],
            'userPermissions' => ['view_job_applications', 'add_schedule'],
            'rowMaps' => $stage8Maps,
            'citiesMap' => [],
            'jobid' => 117,
            'company_id' => 1,
        ])->render();

        $this->assertStringContainsString('aptk-int-schedule-btn app_interview_schedule', $appNextStageHtml);
        $this->assertStringContainsString('Schedule Interview', $appNextStageHtml);

        $this->assertStringContainsString('caf-int-schedule-btn app_interview_schedule', $clientNextStageHtml);
        $this->assertStringContainsString('Schedule Interview', $clientNextStageHtml);
    }

    public function test_job_header_card_subcard_design_with_org_light_backgrounds_and_matching_buttons(): void
    {
        $toolbarView = file_get_contents(resource_path('views/sections/service_company/toolbar.blade.php'));
        $indexView = file_get_contents(resource_path('views/service_company/apptracker/index.blade.php'));
        $clientAppView = file_get_contents(resource_path('views/service_company/apptracker/clientapp.blade.php'));

        // Opt-in flat light-tinted header bar in toolbar keeps non-AppTracker views isolated (Rule 17)
        $this->assertStringContainsString('@if(!empty($apptrackerHeaderCard))', $toolbarView);
        $this->assertStringContainsString('{{ $hdrPrefix }}-main {{ $resolvedOrgClass }}', $toolbarView);
        $this->assertStringContainsString('{{ $hdrPrefix }}-col {{ $hdrPrefix }}-col--identity show-detail-open', $toolbarView);
        $this->assertStringContainsString('{{ $hdrPrefix }}-col {{ $hdrPrefix }}-col--status show-detail-open', $toolbarView);
        $this->assertStringContainsString('{{ $hdrPrefix }}-col {{ $hdrPrefix }}-col--comp', $toolbarView);
        $this->assertStringContainsString('{{ $hdrPrefix }}-col {{ $hdrPrefix }}-col--loc', $toolbarView);
        $this->assertStringContainsString('{{ $hdrPrefix }}-col {{ $hdrPrefix }}-col--meta', $toolbarView);
        $this->assertStringContainsString('{{ $hdrPrefix }}-sep', $toolbarView);
        $this->assertStringNotContainsString('{{ $hdrPrefix }}-subcard', $toolbarView);
        $this->assertStringContainsString("'hp-org-my'       => ['label' => 'My Organization'", $toolbarView);
        $this->assertStringContainsString("'hp-org-internal' => ['label' => 'Internal Client'", $toolbarView);
        $this->assertStringContainsString("'hp-org-direct'   => ['label' => 'Direct Client'", $toolbarView);
        $this->assertStringContainsString('data-kt-swapper="true"', $toolbarView);

        // AppTracker host view: Flat light-tinted white header strip + matching org button colors
        $this->assertStringContainsString("'apptrackerHeaderCard' => true", $indexView);
        $this->assertStringContainsString("'headerPrefix' => 'aptk-hdr'", $indexView);
        $this->assertStringContainsString('<div class="toolbar aptk-job-toolbar', $indexView);
        $this->assertStringContainsString('<div class="aptk-hdr-card', $indexView);
        $this->assertStringContainsString('class="aptk-hdr-actions"', $indexView);
        $this->assertStringContainsString('aptk-hdr-btn-outline', $indexView);
        $this->assertStringContainsString('aptk-hdr-btn-solid', $indexView);
        $this->assertStringContainsString('.aptk-hdr-card.hp-org-my', $indexView);
        $this->assertStringContainsString('.aptk-hdr-card.hp-org-internal', $indexView);
        $this->assertStringContainsString('.aptk-hdr-card.hp-org-direct', $indexView);
        $this->assertStringContainsString('#fffbeb', $indexView);
        $this->assertStringContainsString('#eff6ff', $indexView);
        $this->assertStringContainsString('#ecfdf5', $indexView);
        $this->assertStringContainsString('--kt-toolbar-height: 62px', $indexView);
        $this->assertStringContainsString('padding-left: 0 !important;', $indexView);
        $this->assertStringContainsString('border-radius: 0 !important;', $indexView);
        $this->assertStringContainsString('--hdr-btn-solid-bg: linear-gradient(135deg, #fde047 0%, #eab308 100%);', $indexView);
        $this->assertStringContainsString('--hdr-btn-solid-color: #422006;', $indexView);
        $this->assertStringContainsString('--hdr-btn-solid-bg: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);', $indexView);
        $this->assertStringContainsString('--hdr-btn-solid-bg: linear-gradient(135deg, #a7f3d0 0%, #6ee7b7 100%);', $indexView);
        $this->assertStringContainsString('--hdr-btn-solid-color: #064e3b;', $indexView);
        $this->assertStringContainsString('justify-content: space-between;', $indexView);
        $this->assertStringContainsString('height: 38px !important;', $indexView);

        // ClientAppTracker host view: page-isolated caf-hdr flat header strip & data-job-status preservation
        $this->assertStringContainsString("'apptrackerHeaderCard' => true", $clientAppView);
        $this->assertStringContainsString("'headerPrefix' => 'caf-hdr'", $clientAppView);
        $this->assertStringContainsString('<div class="toolbar caf-job-toolbar', $clientAppView);
        $this->assertStringContainsString('<div class="caf-hdr-card', $clientAppView);
        $this->assertStringContainsString('class="caf-hdr-actions"', $clientAppView);
        $this->assertStringContainsString('caf-hdr-btn-outline', $clientAppView);
        $this->assertStringContainsString('caf-hdr-btn-solid', $clientAppView);
        $this->assertStringContainsString('id="addServiceCandidate" data-job-status="{{ $job->status }}"', $clientAppView);
        $this->assertStringContainsString('.caf-hdr-card.hp-org-my', $clientAppView);
        $this->assertStringContainsString('.caf-hdr-card.hp-org-internal', $clientAppView);
        $this->assertStringContainsString('.caf-hdr-card.hp-org-direct', $clientAppView);
        $this->assertStringContainsString('--kt-toolbar-height: 62px', $clientAppView);
        $this->assertStringContainsString('padding-left: 0 !important;', $clientAppView);
        $this->assertStringContainsString('border-radius: 0 !important;', $clientAppView);
        $this->assertStringContainsString('--hdr-btn-solid-bg: linear-gradient(135deg, #fde047 0%, #eab308 100%);', $clientAppView);
        $this->assertStringContainsString('--hdr-btn-solid-color: #422006;', $clientAppView);
        $this->assertStringContainsString('--hdr-btn-solid-bg: linear-gradient(135deg, #1d4ed8 0%, #1e3a8a 100%);', $clientAppView);
        $this->assertStringContainsString('--hdr-btn-solid-bg: linear-gradient(135deg, #a7f3d0 0%, #6ee7b7 100%);', $clientAppView);
        $this->assertStringContainsString('--hdr-btn-solid-color: #064e3b;', $clientAppView);
        $this->assertStringContainsString('justify-content: space-between;', $clientAppView);
        $this->assertStringContainsString('height: 38px !important;', $clientAppView);
    }

    public function test_extended_channel_host_views_enforce_approved_header_and_rule_20_card_stream(): void
    {
        $channels = [
            'similar' => ['path' => resource_path('views/service_company/apptracker/similar_candidates.blade.php'),  'pfx' => 'simf'],
            'dsimilar' => ['path' => resource_path('views/service_company/apptracker/dsimilar_candidates.blade.php'), 'pfx' => 'dsf'],
            'internal_bench' => ['path' => resource_path('views/service_company/apptracker/internal_bench.blade.php'),     'pfx' => 'ibf'],
            'dinternal_bench' => ['path' => resource_path('views/service_company/apptracker/dinternal_bench.blade.php'),    'pfx' => 'dib'],
            'public_bench' => ['path' => resource_path('views/service_company/apptracker/public_bench.blade.php'),       'pfx' => 'abf'],
            'candidates_req' => ['path' => resource_path('views/service_company/apptracker/candidates_request.blade.php'),  'pfx' => 'brf'],
            'career_apply' => ['path' => resource_path('views/service_company/apptracker/career_apply.blade.php'),       'pfx' => 'wcf'],
        ];

        foreach ($channels as $key => $conf) {
            $view = file_get_contents($conf['path']);
            $pfx = $conf['pfx'];

            // 1. Approved header toolbar inclusion
            $this->assertStringContainsString("'apptrackerHeaderCard' => true", $view, "Missing apptrackerHeaderCard in $key");
            $this->assertStringContainsString("'headerPrefix' => '{$pfx}-hdr'", $view, "Missing headerPrefix in $key");
            $this->assertStringContainsString("class=\"toolbar {$pfx}-job-toolbar", $view, "Missing toolbar class in $key");

            // 2. Toolbar action buttons & org color combinations
            $this->assertStringContainsString("{$pfx}-hdr-btn-outline", $view, "Missing outline button in $key");
            $this->assertStringContainsString(".{$pfx}-hdr-card.hp-org-my", $view, "Missing hp-org-my in $key");
            $this->assertStringContainsString(".{$pfx}-hdr-card.hp-org-internal", $view, "Missing hp-org-internal in $key");
            $this->assertStringContainsString(".{$pfx}-hdr-card.hp-org-direct", $view, "Missing hp-org-direct in $key");

            // 3. Search subcard & live controls
            $this->assertStringContainsString('id="btn'.ucfirst($pfx).'SearchClear"', $view, "Missing search clear in $key");
            $this->assertStringContainsString('id="btn'.ucfirst($pfx).'RefreshList"', $view, "Missing refresh in $key");

            // 4. Stream wrapper & legacy table absence
            $this->assertStringContainsString("class=\"{$pfx}-stream-wrapper\"", $view, "Missing stream-wrapper in $key");
            $this->assertStringNotContainsString('<table class="table align-middle table-row-dashed', $view, "Legacy table present in $key");
        }
    }

    public function test_extended_channel_ajax_partials_render_rule_20_subcards_and_preserve_action_handlers(): void
    {
        $this->actingUser();

        $row = $this->makeCandidateRow([
            'id' => 881,
            'candidate_id' => 881,
            'wiseId' => 991,
            'current_company' => 'Acme Labs',
            'current_designation' => 'Senior Developer',
            'total_exp' => '5.',
            'rel_exp' => '3.',
            'wm' => 'hybrid',
            'np_id' => '15',
            'serving_notice_period_mode' => 0,
            'status' => 2,
            'is_inreview' => 0,
            'job_master_status_id' => 8,
        ]);

        $baseParams = [
            'data' => [$row],
            'userPermissions' => ['view_job_applications', 'salary_view'],
            'rowMaps' => $this->baseRowMaps(),
            'jobid' => 117,
            'job' => (object) ['id' => 117, 'status' => 'active'],
        ];

        // 1. Similar Candidates (AppTracker)
        $simHtml = View::make('service_company.ajax_pages.similar_existing_ajax', $baseParams)->render();
        $this->assertStringContainsString('simf-cand-card', $simHtml);
        $this->assertStringContainsString('show-application-detail', $simHtml);
        $this->assertStringContainsString('add_to_inreview', $simHtml);
        $this->assertStringContainsString('Acme Labs', $simHtml);

        // 2. Client Similar Candidates (ClientAppTracker)
        $dsimHtml = View::make('service_company.ajax_pages.dsimilar_existing_ajax', $baseParams)->render();
        $this->assertStringContainsString('dsf-cand-card', $dsimHtml);
        $this->assertStringContainsString('client-show-application-detail', $dsimHtml);
        $this->assertStringContainsString('add_to_inreview', $dsimHtml);

        // 3. Inhouse Bench Talent (AppTracker)
        $ibHtml = View::make('service_company.ajax_pages.internal_bench_ajax', $baseParams)->render();
        $this->assertStringContainsString('ibf-cand-card', $ibHtml);
        $this->assertStringContainsString('bench_show_application_detail', $ibHtml);
        $this->assertStringContainsString('add_to_inreview', $ibHtml);

        // 4. Client Inhouse Bench Talent (ClientAppTracker)
        $dibHtml = View::make('service_company.ajax_pages.dinternal_bench_ajax', $baseParams)->render();
        $this->assertStringContainsString('dib-cand-card', $dibHtml);
        $this->assertStringContainsString('bench_show_application_detail', $dibHtml);
        $this->assertStringContainsString('add_to_inreview', $dibHtml);

        // 5. Agency Bench Talent (AppTracker)
        $pbHtml = View::make('service_company.ajax_pages.public_bench_ajax', array_merge($baseParams, [
            'boostbenchApplications' => [],
        ]))->render();
        $this->assertStringContainsString('abf-cand-card', $pbHtml);
        $this->assertStringContainsString('pbench_show_application_detail', $pbHtml);
        $this->assertStringContainsString('data-row-source_from_type="2"', $pbHtml);

        // 6. Bench Talent Requests (AppTracker)
        $brHtml = View::make('service_company.ajax_pages.candidates_request_ajax', $baseParams)->render();
        $this->assertStringContainsString('brf-cand-card', $brHtml);
        $this->assertStringContainsString('pbench_show_application_detail', $brHtml);
        $this->assertStringContainsString('add_to_list', $brHtml);

        // 7. Career Pages (AppTracker / ClientAppTracker)
        $wcHtml = View::make('service_company.ajax_pages.career_apply_ajax', $baseParams)->render();
        $this->assertStringContainsString('wcf-cand-card', $wcHtml);
        $this->assertStringContainsString('website-show-application-detail', $wcHtml);
        $this->assertStringContainsString('data-row-source_from_type="3"', $wcHtml);

        // Verify empty state templates
        $emptyParams = array_merge($baseParams, ['data' => []]);
        $this->assertStringContainsString('simf-empty-card', View::make('service_company.ajax_pages.similar_existing_ajax', $emptyParams)->render());
        $this->assertStringContainsString('dsf-empty-card', View::make('service_company.ajax_pages.dsimilar_existing_ajax', $emptyParams)->render());
        $this->assertStringContainsString('ibf-empty-card', View::make('service_company.ajax_pages.internal_bench_ajax', $emptyParams)->render());
        $this->assertStringContainsString('dib-empty-card', View::make('service_company.ajax_pages.dinternal_bench_ajax', $emptyParams)->render());
        $this->assertStringContainsString('abf-empty-card', View::make('service_company.ajax_pages.public_bench_ajax', array_merge($emptyParams, ['boostbenchApplications' => []]))->render());
        $this->assertStringContainsString('brf-empty-card', View::make('service_company.ajax_pages.candidates_request_ajax', $emptyParams)->render());
        $this->assertStringContainsString('wcf-empty-card', View::make('service_company.ajax_pages.career_apply_ajax', $emptyParams)->render());
    }

    public function test_controller_sets_hp_org_class_and_card_v7_cache_version_for_all_channels(): void
    {
        $controllerFile = file_get_contents(app_path('Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php'));

        // All 7 host actions set $this->hpOrgClass
        $this->assertStringContainsString('$this->hpOrgClass = self::resolveJobOrgClass($this->job, (int) $company_id);', $controllerFile);
        $this->assertGreaterThanOrEqual(8, substr_count($controllerFile, '$this->hpOrgClass = self::resolveJobOrgClass($this->job, (int) $company_id);'));

        // All AJAX actions pass card_v12 in $cachePayload
        $this->assertStringContainsString("'ui_v' => 'card_v12'", $controllerFile);
        $this->assertGreaterThanOrEqual(8, substr_count($controllerFile, "'ui_v' => 'card_v12'"));
    }

    public function test_matched_job_subcard_and_restored_work_mode_and_add_candidate_button(): void
    {
        $baseParams = [
            'data' => [$this->makeCandidateRow()],
            'userPermissions' => ['view_jobs', 'add_job_applications'],
            'app_int_status' => null,
            'app_int_activity' => null,
            'app_int_notice' => null,
            'sal_mode' => 0,
            'min_salary_currency' => 0,
            'app_int_outsource' => null,
            'jobid' => 117,
            'requiredSkills' => ['php'],
            'modeFilter' => 0,
            'currencyFilter' => 0,
            'symbolsFilter' => 'INR',
            'hasActiveFilters' => false,
            'search' => '',
            'rowMaps' => array_merge($this->baseRowMaps(117), [
                'wise_job_info' => [
                    101 => (object) [
                        'wise_id' => 101,
                        'company_name' => '4Bell Technology',
                        'client_name' => 'TechNova Solutions',
                        'self_company' => 0,
                        'matched_job_id' => 81,
                        'title' => 'PHP / Laravel Developer',
                        'jobStatus' => 'active',
                        'companyIdCheck' => 1,
                        'employment_type' => 0,
                        'category_name' => 'Technology',
                    ],
                ],
                'work_mode_map' => [
                    101 => 'office',
                ],
            ]),
            'assignedCandidateIds' => [],
        ];

        // 1. Similar candidates renders Job Details, INTERNAL CLIENT, Work Mode, bracketed Rel exp, resume badge in footer, and NO icon box or HIRING tag
        $simHtml = View::make('service_company.ajax_pages.similar_existing_ajax', $baseParams)->render();
        $this->assertStringContainsString('Job Details', $simHtml);
        $this->assertStringContainsString('Internal Client', $simHtml);
        $this->assertStringContainsString('type-internal', $simHtml);
        $this->assertStringNotContainsString('simf-cand-subcard--internal', $simHtml);
        $this->assertStringContainsString('fa-building', $simHtml);
        $this->assertStringContainsString('simf-bottom-right', $simHtml);
        $this->assertStringNotContainsString('simf-req-icon-box', $simHtml);
        $this->assertStringNotContainsString('simf-status-pill', $simHtml);
        $this->assertStringContainsString('(Rel 4.0 Y)', $simHtml);
        $this->assertStringContainsString('No CV', $simHtml);
        $this->assertStringContainsString('#81', $simHtml);
        $this->assertStringContainsString('Technology', $simHtml);
        $this->assertStringContainsString('simf-loc-mode', $simHtml);
        $this->assertStringNotContainsString('<i class="fa fa-inr"', $simHtml);

        // 2. Client Similar candidates renders Job Details, INTERNAL CLIENT, Work Mode, bracketed Rel exp, resume badge in footer, and NO icon box or HIRING tag
        $dsimHtml = View::make('service_company.ajax_pages.dsimilar_existing_ajax', $baseParams)->render();
        $this->assertStringContainsString('Job Details', $dsimHtml);
        $this->assertStringContainsString('Internal Client', $dsimHtml);
        $this->assertStringContainsString('type-internal', $dsimHtml);
        $this->assertStringNotContainsString('dsf-cand-subcard--internal', $dsimHtml);
        $this->assertStringContainsString('fa-building', $dsimHtml);
        $this->assertStringContainsString('dsf-bottom-right', $dsimHtml);
        $this->assertStringNotContainsString('dsf-req-icon-box', $dsimHtml);
        $this->assertStringNotContainsString('dsf-status-pill', $dsimHtml);
        $this->assertStringContainsString('(Rel 4.0 Y)', $dsimHtml);
        $this->assertStringContainsString('No CV', $dsimHtml);
        $this->assertStringContainsString('#81', $dsimHtml);
        $this->assertStringContainsString('Technology', $dsimHtml);
        $this->assertStringContainsString('dsf-loc-mode', $dsimHtml);
        $this->assertStringNotContainsString('<i class="fa fa-inr"', $dsimHtml);

        // 2b. Test My Organization classification highlight and icon per Document Color Standard (Neutral Subcard, Tag Highlight Only)
        $myOrgParams = $baseParams;
        $myOrgParams['rowMaps']['wise_job_info'][101] = (object) [
            'wise_id' => 101,
            'company_name' => '4Bell Technology',
            'client_name' => '',
            'self_company' => 1,
            'matched_job_id' => 54,
            'title' => 'Senior Java Developer',
            'jobStatus' => 'active',
            'companyIdCheck' => 1,
            'employment_type' => 0,
            'category_name' => 'IT (Information Technology)',
        ];
        $myOrgSimHtml = View::make('service_company.ajax_pages.similar_existing_ajax', $myOrgParams)->render();
        $this->assertStringContainsString('My Organization', $myOrgSimHtml);
        $this->assertStringContainsString('type-my-org', $myOrgSimHtml);
        $this->assertStringNotContainsString('simf-cand-subcard--my-org', $myOrgSimHtml);
        $this->assertStringContainsString('background:#fef3c7', $myOrgSimHtml);
        $this->assertStringContainsString('border:1px solid #fcd34d', $myOrgSimHtml);
        $this->assertStringContainsString('color:#92400e', $myOrgSimHtml);
        $this->assertStringNotContainsString('background:#fffbeb', $myOrgSimHtml);
        $this->assertStringContainsString('fa-id-badge', $myOrgSimHtml);
        $this->assertStringContainsString('#54', $myOrgSimHtml);
        $this->assertStringContainsString('IT (Information Technology)', $myOrgSimHtml);

        $myOrgDsimHtml = View::make('service_company.ajax_pages.dsimilar_existing_ajax', $myOrgParams)->render();
        $this->assertStringContainsString('My Organization', $myOrgDsimHtml);
        $this->assertStringContainsString('type-my-org', $myOrgDsimHtml);
        $this->assertStringNotContainsString('dsf-cand-subcard--my-org', $myOrgDsimHtml);
        $this->assertStringContainsString('background:#fef3c7', $myOrgDsimHtml);
        $this->assertStringContainsString('border:1px solid #fcd34d', $myOrgDsimHtml);
        $this->assertStringContainsString('color:#92400e', $myOrgDsimHtml);
        $this->assertStringContainsString('fa-id-badge', $myOrgDsimHtml);
        $this->assertStringContainsString('#54', $myOrgDsimHtml);
        $this->assertStringContainsString('IT (Information Technology)', $myOrgDsimHtml);

        // 2c. Test Direct Client classification highlight and icon
        $directParams = $baseParams;
        $directParams['rowMaps']['wise_job_info'][101] = (object) [
            'wise_id' => 101,
            'company_name' => 'TechNova Solutions',
            'client_name' => 'TechNova Solutions',
            'self_company' => 0,
            'matched_job_id' => 2,
            'title' => 'Chief Java Architect',
            'jobStatus' => 'active',
            'companyIdCheck' => 999, // foreign company => direct client
            'employment_type' => 0,
            'category_name' => 'Architecture',
        ];
        $directSimHtml = View::make('service_company.ajax_pages.similar_existing_ajax', $directParams)->render();
        $this->assertStringContainsString('Direct Client', $directSimHtml);
        $this->assertStringContainsString('type-direct', $directSimHtml);
        $this->assertStringNotContainsString('simf-cand-subcard--direct', $directSimHtml);
        $this->assertStringContainsString('background:#ecfdf5', $directSimHtml);
        $this->assertStringContainsString('fa-shield-alt', $directSimHtml);

        $directDsimHtml = View::make('service_company.ajax_pages.dsimilar_existing_ajax', $directParams)->render();
        $this->assertStringContainsString('Direct Client', $directDsimHtml);
        $this->assertStringContainsString('type-direct', $directDsimHtml);
        $this->assertStringNotContainsString('dsf-cand-subcard--direct', $directDsimHtml);
        $this->assertStringContainsString('background:#ecfdf5', $directDsimHtml);
        $this->assertStringContainsString('fa-shield-alt', $directDsimHtml);

        // 2d. Test All AppTracker and Client App Tracker footer strip has resume/no cv
        $aptkHtml = View::make('service_company.ajax_pages.all_apptracker', $baseParams)->render();
        $this->assertStringContainsString('aptk-bottom-right', $aptkHtml);
        $this->assertStringContainsString('aptk-nocv-pill', $aptkHtml);

        $cafHtml = View::make('service_company.ajax_pages.clientappdata_ajax', $baseParams)->render();
        $this->assertStringContainsString('caf-bottom-right', $cafHtml);
        $this->assertStringContainsString('caf-nocv-pill', $cafHtml);

        // 3. Location & Degree in channel views restores Work Mode
        $ibHtml = View::make('service_company.ajax_pages.internal_bench_ajax', $baseParams)->render();
        $this->assertStringContainsString('ibf-loc-mode', $ibHtml);

        $dibHtml = View::make('service_company.ajax_pages.dinternal_bench_ajax', $baseParams)->render();
        $this->assertStringContainsString('dib-loc-mode', $dibHtml);

        $abHtml = View::make('service_company.ajax_pages.public_bench_ajax', array_merge($baseParams, [
            'boostbenchApplications' => [],
        ]))->render();
        $this->assertStringContainsString('abf-loc-mode', $abHtml);

        $brHtml = View::make('service_company.ajax_pages.candidates_request_ajax', $baseParams)->render();
        $this->assertStringContainsString('brf-loc-mode', $brHtml);

        $wcHtml = View::make('service_company.ajax_pages.career_apply_ajax', $baseParams)->render();
        $this->assertStringContainsString('wcf-loc-mode', $wcHtml);
    }

    public function test_resume_pill_click_triggers_candidate_drawer_resume_tab_across_all_candidate_lists(): void
    {
        // 1. Verify global layout lifecycle function and click handler
        $layoutContent = file_get_contents(resource_path('views/layouts/service_company_layout.blade.php'));
        $this->assertStringContainsString('function activateCandidateDrawerTab(targetTab)', $layoutContent);
        $this->assertStringContainsString('window.activateCandidateDrawerTab = activateCandidateDrawerTab;', $layoutContent);
        $this->assertStringContainsString('window.__pendingCandidateDrawerTab = targetTab;', $layoutContent);
        $this->assertStringContainsString('jw-open-resume-tab', $layoutContent);

        // 2. Mock candidate with resume
        $candWithResume = (object) [
            'id' => 999,
            'candidate_id' => 999,
            'job_application_id' => 999,
            'full_name' => 'Resume Candidate',
            'designation' => 'Full Stack Developer',
            'city_name' => 'Noida',
            'country_name' => 'India',
            'qualification_name' => 'B.Tech',
            'total_exp' => '5.0',
            'rel_exp' => '4.5',
            'status' => 1,
            'master_status_id' => 1,
            'childStatusId' => 0,
            'rec_candidate_assign_id' => 0,
            'skills' => 'PHP, Laravel, Vue.js',
            'salary' => 1299,
            'salary_mode' => 2,
            'current_salary' => 1039,
            'current_salary_mode' => 2,
            'ectc_yearly' => 15588,
            'ctc_yearly' => 12468,
            'rate' => 15,
            'notice' => 15,
            'np_id' => '15',
            'serving_notice_period_mode' => 0,
            'current_company' => '4Bell Tech',
            'resume' => 'uploads/resumes/sample_candidate_resume.pdf',
            'wm' => 'Remote',
            'linkedin_url' => 'https://linkedin.com/in/test-verified',
            'visa' => 'H1B',
            'job_id' => 117,
            'jobId' => 117,
            'company_id' => 1,
            'ClientComID' => 0,
            'created_at' => '2026-11-12 10:00:00',
            'updated_at' => '2026-11-12 12:00:00',
        ];

        $params = [
            'data' => [$candWithResume],
            'userPermissions' => ['view_jobs', 'add_job_applications'],
            'app_int_status' => null,
            'app_int_activity' => null,
            'app_int_notice' => null,
            'sal_mode' => 0,
            'min_salary_currency' => 0,
            'app_int_outsource' => null,
            'jobid' => 117,
            'requiredSkills' => ['php'],
            'modeFilter' => 0,
            'currencyFilter' => 0,
            'symbolsFilter' => 'EUR',
            'hasActiveFilters' => false,
            'search' => '',
            'salary_hide' => 0,
            'rowMaps' => array_merge($this->baseRowMaps(117), [
                'skills' => [117 => [(object) ['job_id' => 117, 'skill_name' => 'PHP']]],
                'resume_map' => [999 => 'uploads/resumes/sample_candidate_resume.pdf'],
                'work_mode_map' => [999 => 'Remote'],
                'interviews' => [999 => null],
                'jobs' => [117 => (object) [
                    'id' => 117,
                    'title' => 'Lead Full Stack PHP Engineer (V7)',
                    'max_sal_mode' => 0,
                    'max_salary_currency' => 0,
                ]],
                'wise_job_info' => [
                    999 => (object) [
                        'wise_id' => 999,
                        'company_name' => '4Bell Technology',
                        'client_name' => '4Bell Technology',
                        'self_company' => 0,
                        'matched_job_id' => 81,
                        'title' => 'PHP / Laravel Developer',
                        'jobStatus' => 'active',
                        'companyIdCheck' => 999,
                        'employment_type' => 1,
                        'category_name' => 'Technology',
                    ],
                ],
            ]),
            'boostbenchApplications' => [],
        ];

        // 1. App Tracker Total Candidates
        $aptkHtml = View::make('service_company.ajax_pages.all_apptracker', $params)->render();
        $this->assertStringContainsString('aptk-resume-pill jw-open-resume-tab cursor-pointer', $aptkHtml);
        $this->assertStringContainsString('data-open-tab="resume_cv"', $aptkHtml);
        $this->assertStringContainsString('data-row-id="999"', $aptkHtml);

        // 2. Client App Tracker Total Candidates
        $cafHtml = View::make('service_company.ajax_pages.clientappdata_ajax', $params)->render();
        $this->assertStringContainsString('caf-resume-pill jw-open-resume-tab cursor-pointer', $cafHtml);
        $this->assertStringContainsString('data-open-tab="resume_cv"', $cafHtml);
        $this->assertStringContainsString('data-row-id="999"', $cafHtml);

        // 3. Similar Candidates
        $simHtml = View::make('service_company.ajax_pages.similar_existing_ajax', $params)->render();
        $this->assertStringContainsString('simf-resume-pill jw-open-resume-tab cursor-pointer', $simHtml);
        $this->assertStringContainsString('data-open-tab="resume_cv"', $simHtml);
        $this->assertStringContainsString('data-row-id="999"', $simHtml);

        // 4. Client Similar Candidates
        $dsimHtml = View::make('service_company.ajax_pages.dsimilar_existing_ajax', $params)->render();
        $this->assertStringContainsString('dsf-resume-pill jw-open-resume-tab cursor-pointer', $dsimHtml);
        $this->assertStringContainsString('data-open-tab="resume_cv"', $dsimHtml);
        $this->assertStringContainsString('data-row-id="999"', $dsimHtml);

        // 5. CV Tracker
        $cvParams = array_merge($params, [
            'cardMeta' => [
                'visa_display' => 'H1B',
                'visa_raw' => 'H1B',
                'linkedin_url' => 'https://linkedin.com/in/test-verified',
            ],
            'percentage' => '100%',
            'matchScore' => 100,
            'matchBg' => '#dcfce7',
            'matchColor' => '#15803d',
            'matchedSkillsArr' => ['php'],
            'unmatchedSkillsArr' => [],
            'wiseId' => 999,
            'candAppId' => 999,
        ]);
        $cvHtml = View::make('service_company.ajax_pages.all_candidates', $cvParams)->render();
        $this->assertStringContainsString('jw-open-resume-tab cursor-pointer', $cvHtml);
        $this->assertStringContainsString('data-open-tab="resume_cv"', $cvHtml);
        $this->assertStringContainsString('data-row-id="999"', $cvHtml);
    }
}
