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
        $this->assertStringContainsString('--hdr-btn-solid-bg: linear-gradient(135deg, #047857 0%, #065f46 100%);', $indexView);
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
        $this->assertStringContainsString('--hdr-btn-solid-bg: linear-gradient(135deg, #047857 0%, #065f46 100%);', $clientAppView);
        $this->assertStringContainsString('justify-content: space-between;', $clientAppView);
        $this->assertStringContainsString('height: 38px !important;', $clientAppView);
    }
}
