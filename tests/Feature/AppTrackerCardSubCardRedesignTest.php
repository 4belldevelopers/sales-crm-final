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
        $this->assertStringContainsString('stage_sub_stage', $html);
        $this->assertStringContainsString('jw-row-match-badge', $html);
        $this->assertStringContainsString('data-candidate-id="101"', $html);
        $this->assertStringContainsString('data-wise-id="5001"', $html);
        $this->assertStringContainsString('Disha Gupta', $html);
        $this->assertStringContainsString('Matches a required skill', $html);
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
        $this->assertStringContainsString('client_stage_sub_stage', $html);
        $this->assertStringContainsString('client_stage_locked_notice', $html);
        $this->assertStringContainsString('Client Managed', $html);
        $this->assertStringContainsString('show-application-detail', $html);
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
        $this->assertStringContainsString('<strong>7 Y</strong> Rel', $appHtml);
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
        $this->assertStringContainsString('<strong>7 Y</strong> Rel', $clientHtml);
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

        $this->assertStringContainsString('id="candidatetList" class="caf-cand-stream"', $clientAppView);
        $this->assertStringContainsString('id="btnCafSearchClear"', $clientAppView);
        $this->assertStringContainsString('id="btnCafRefreshList"', $clientAppView);
        $this->assertStringContainsString('caf-stream-bar', $clientAppView);
        $this->assertStringContainsString('_cafSearchTimer', $clientAppView);
        $this->assertStringContainsString('_cafListXhr.abort()', $clientAppView);
        $this->assertStringNotContainsString('<table class="table align-middle table-row-dashed', $clientAppView);
        $this->assertStringNotContainsString('aptk-cand-card', $clientAppView);
        $this->assertStringContainsString("'color' => '#0891b2', 'bg' => '#ecfeff'", $clientAppView);
    }
}
