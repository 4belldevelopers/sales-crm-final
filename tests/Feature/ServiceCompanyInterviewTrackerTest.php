<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * ServiceCompanyInterviewTrackerTest
 *
 * Intent: Verifies that the Service Company Interview Tracker views render cleanly
 * under the Rule 20 Modern Card + Sub-Card Enterprise Design System, enforce
 * permission gating, escape XSS payloads, and preserve interactive DOM hooks.
 */
class ServiceCompanyInterviewTrackerTest extends TestCase
{
    public function test_interview_tracker_views_exist(): void
    {
        $this->assertTrue(View::exists('service_company.interview-schedule.table'));
        $this->assertTrue(View::exists('service_company.ajax_pages.interview-tracker-ajax'));
    }

    public function test_interview_tracker_ajax_renders_rule_20_card_and_subcards(): void
    {
        $sampleData = [
            [
                'id' => 101,
                'wiseID' => 501,
                'job_application_id' => 701,
                'jobId' => 86,
                'job_id' => 86,
                'job_company_id' => 2,
                'full_name' => 'uujaiswal',
                'current_designation' => 'RPA Developer',
                'current_company' => 'Infosys BPM',
                'city_name' => 'noida',
                'country_name' => 'india',
                'email' => 'uujaiswal@example.com',
                'job_title' => 'RPA Developer',
                'employment_type' => 0,
                'self_company' => 0,
                'client_name' => 'TESLA',
                'company_name' => '4Bell Technology',
                'uc_company_name' => '4Bell Technology',
                'recommendation_id' => 0,
                'to_company_id' => 0,
                'thirdparty_company_id' => 0,
                'ClientComID' => 2,
                'schedule_date' => '2026-10-29 10:30:00',
                'schedule_to_time' => '10:30',
                'timezone_abbreviation' => 'IST',
                'interview_mode' => 2,
                'mode_name' => 'Video',
                'mode_lavel_name' => 'Google Meet',
                'interview_type' => 1,
                'interview_mention_link' => 'https://meet.google.com/abc-defg-hij',
                'interviewers' => '',
                'posted_by' => 'vibhanshu jaiswal',
                'submitted_at' => now()->subHours(9)->toIso8601String(),
                'created_at' => now()->subHours(9)->toIso8601String(),
                'master_status_id' => 2,
                'master_status_name' => 'Screening',
                'child_status_id' => 0,
                'child_status_name' => '',
                'feedback' => 0,
                'available_status' => 0,
                'average_star_rating' => 0,
                'mailstatus' => 0,
            ],
            [
                'id' => 102,
                'wiseID' => 502,
                'job_application_id' => 702,
                'jobId' => 81,
                'job_id' => 81,
                'job_company_id' => 99,
                'full_name' => 'Vibhanshu <script>alert(1)</script>',
                'current_designation' => 'PHP / Laravel Developer',
                'current_company' => 'Acme Labs',
                'city_name' => '',
                'country_name' => '',
                'email' => 'v@example.com',
                'job_title' => 'PHP / Laravel Developer',
                'employment_type' => 1,
                'self_company' => 0,
                'client_name' => 'STRIPE 128',
                'company_name' => 'Stripe Inc',
                'uc_company_name' => '4Bell Technology',
                'recommendation_id' => 12,
                'to_company_id' => 2,
                'thirdparty_company_id' => 0,
                'ClientComID' => 2,
                'schedule_date' => date('Y-m-d H:i:s', strtotime('+1 day')),
                'schedule_to_time' => '15:00',
                'timezone_abbreviation' => 'IST',
                'interview_mode' => 3,
                'mode_name' => 'F2F',
                'mode_lavel_name' => 'In-Person',
                'interview_type' => 2,
                'interview_mention_link' => '123 Enterprise Park, Bengaluru',
                'interviewers' => 'Alice Smith , Bob Jones , Carol Lee , Dave Kim',
                'posted_by' => 'vibhanshu jaiswal',
                'submitted_at' => now()->subDays(3)->toIso8601String(),
                'created_at' => now()->subDays(3)->toIso8601String(),
                'master_status_id' => 8,
                'master_status_name' => 'In Review',
                'child_status_id' => 0,
                'child_status_name' => '',
                'feedback' => 1,
                'available_status' => 1,
                'average_star_rating' => 4.5,
                'mailstatus' => 1,
            ],
        ];

        $followupMap = collect([
            102 => (object) ['id' => 102, 'followup_status' => 'confirmed', 'followup_status_at' => '2026-11-12 10:00:00'],
        ]);
        $feedbackMap = collect([
            102 => (object) ['id' => 900, 'interview_id' => 102, 'available_status' => 1, 'interview_result' => 'selected', 'overall_recommendation' => 'strong_hire'],
        ]);

        $html = View::make('service_company.ajax_pages.interview-tracker-ajax', [
            'data' => $sampleData,
            'userPermissions' => ['view_schedule'],
            'feedback_status' => [1 => ['id' => 1, 'name' => 'Selected']],
            'available_status' => [],
            'company_id' => 2,
            'chkAdminRole' => 1,
            'followupMap' => $followupMap,
            'feedbackMap' => $feedbackMap,
            'spokpersonMap' => collect(),
            'clientCompanyMap' => [86 => 'TESLA', 81 => 'STRIPE 128'],
            'jobMetaMap' => [
                86 => ['client_name' => 'TESLA', 'self_company' => 0, 'employment_type' => 0],
                81 => ['client_name' => 'STRIPE 128', 'self_company' => 0, 'employment_type' => 1],
            ],
            'candidateMetaMap' => [
                'by_wise' => [
                    501 => ['current_company' => 'Infosys BPM', 'submitted_at' => now()->subHours(9)->toIso8601String()],
                    502 => ['current_company' => 'Acme Labs', 'submitted_at' => now()->subDays(3)->toIso8601String()],
                ],
                'by_app' => [],
            ],
            'userCaptainJobIds' => [86 => true],
        ])->render();

        // Rule 20 Card + 6 Discrete Sub-Cards + Bottom Strip
        $this->assertStringContainsString('class="it-card"', $html);
        $this->assertStringContainsString('it-card-grid', $html);
        $this->assertStringContainsString('it-subcard-candidate', $html);
        $this->assertStringContainsString('it-subcard-job', $html);
        $this->assertStringContainsString('it-subcard-schedule', $html);
        $this->assertStringContainsString('it-subcard-panel', $html);
        $this->assertStringContainsString('it-subcard-owner', $html);
        $this->assertStringContainsString('it-subcard-feedback', $html);
        $this->assertStringContainsString('it-card-bottom', $html);

        // Candidate Subcard: Current Company shown instead of location
        $this->assertStringContainsString('Infosys BPM', $html);
        $this->assertStringNotContainsString('Noida, India', $html);

        // Ownership & Recruiter Subcard content
        $this->assertStringContainsString('Ownership &amp; Recruiter', $html);
        $this->assertStringContainsString('Submitted:', $html);
        $this->assertStringContainsString('4Bell Technology', $html);

        // Standardized Org Classification Badges (Internal Client & Direct Client; never raw "Internal")
        $this->assertStringContainsString('Internal Client', $html);
        $this->assertStringContainsString('Direct Client', $html);
        $this->assertStringContainsString('it-scope-internal', $html);
        $this->assertStringContainsString('it-scope-direct', $html);

        // Requisition Subcard: Employment Type (Full-time / Contract) & NO text clipping
        $this->assertStringContainsString('Full-time', $html);
        $this->assertStringContainsString('Contract', $html);
        $firstReqStart = strpos($html, '<!-- Sub-Card 2: REQUISITION -->');
        $firstSchedStart = strpos($html, '<!-- Sub-Card 3: SCHEDULE & LINK -->');
        $this->assertNotFalse($firstReqStart);
        $this->assertNotFalse($firstSchedStart);
        $firstReqSection = substr($html, $firstReqStart, $firstSchedStart - $firstReqStart);
        $this->assertStringNotContainsString('Google Meet', $firstReqSection);

        // Schedule Subcard: Interview Mode badge preserved, NO "Google Meet" text anywhere on card
        $firstPanelStart = strpos($html, '<!-- Sub-Card 4: INTERVIEW PANEL -->');
        $this->assertNotFalse($firstPanelStart);
        $firstSchedSection = substr($html, $firstSchedStart, $firstPanelStart - $firstSchedStart);
        $this->assertStringNotContainsString('Google Meet', $firstSchedSection);
        $this->assertStringContainsString('it-mode-video', $firstSchedSection);
        $this->assertStringContainsString('Video', $firstSchedSection);
        $this->assertStringNotContainsString('>Completed<', $firstSchedSection);
        $this->assertStringNotContainsString('it-time-tag', $html);
        $this->assertStringNotContainsString('it-platform-pill', $html);

        // No raw table tags
        $this->assertStringNotContainsString('<tr', $html);
        $this->assertStringNotContainsString('<td', $html);

        // XSS payload properly escaped
        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
        $this->assertStringContainsString('&lt;script&gt;alert(1)&lt;/script&gt;', $html);

        // Interactive hooks preserved
        $this->assertStringContainsString('show-application-detail', $html);
        $this->assertStringContainsString('js-it-open-jd', $html);
        $this->assertStringContainsString('open-interview-link', $html);
        $this->assertStringContainsString('show-feedback', $html);
        $this->assertStringContainsString('js-it-reschedule-btn', $html);
        $this->assertStringContainsString('Strong Hire', $html);
        $this->assertStringContainsString('4 Panelists', $html);
        $this->assertStringContainsString('data-schedule-ts=', $html);
    }

    public function test_interview_tracker_ajax_renders_clean_empty_card(): void
    {
        $html = View::make('service_company.ajax_pages.interview-tracker-ajax', [
            'data' => [],
            'userPermissions' => ['view_schedule'],
            'feedback_status' => [],
            'available_status' => [],
            'company_id' => 2,
            'chkAdminRole' => 1,
            'followupMap' => collect(),
            'feedbackMap' => collect(),
            'spokpersonMap' => collect(),
            'clientCompanyMap' => [],
            'jobMetaMap' => [],
            'candidateMetaMap' => [],
            'userCaptainJobIds' => [],
        ])->render();

        $this->assertStringContainsString('it-empty-card', $html);
        $this->assertStringContainsString('No Interviews Found', $html);
        $this->assertStringNotContainsString('<tr', $html);
    }

    public function test_interview_tracker_supports_my_organization_badge_and_app_tracker_colors(): void
    {
        $myOrgHtml = View::make('service_company.ajax_pages.interview-tracker-ajax', [
            'data' => [[
                'id' => 205,
                'wiseID' => 605,
                'jobId' => 99,
                'job_id' => 99,
                'job_company_id' => 2,
                'full_name' => 'Aarav Mehta',
                'current_designation' => 'Backend Engineer',
                'current_company' => 'Paytm',
                'job_title' => 'Senior Laravel Engineer',
                'employment_type' => 0,
                'self_company' => 1,
                'company_name' => '4Bell Technology',
                'recommendation_id' => 0,
                'to_company_id' => 0,
                'ClientComID' => 2,
                'schedule_date' => '2026-11-15 11:00:00',
                'schedule_to_time' => '11:00',
                'timezone_abbreviation' => 'IST',
                'interview_mode' => 1,
                'mode_name' => 'Telecon',
                'interview_mention_link' => '',
                'interviewers' => 'John Doe',
                'posted_by' => 'Recruiter Lead',
                'master_status_id' => 2,
                'master_status_name' => 'Screening',
                'feedback' => 0,
                'available_status' => 0,
            ]],
            'userPermissions' => ['view_schedule'],
            'feedback_status' => [],
            'available_status' => [],
            'company_id' => 2,
            'chkAdminRole' => 1,
            'followupMap' => collect(),
            'feedbackMap' => collect(),
            'spokpersonMap' => collect(),
            'clientCompanyMap' => [],
            'jobMetaMap' => [
                99 => ['client_name' => '4Bell Technology', 'self_company' => 1, 'employment_type' => 0],
            ],
            'candidateMetaMap' => [
                'by_wise' => [605 => ['current_company' => 'Paytm', 'submitted_at' => now()->subDays(2)->toIso8601String()]],
                'by_app' => [],
            ],
            'userCaptainJobIds' => [],
        ])->render();

        $this->assertStringContainsString('My Organization', $myOrgHtml);
        $this->assertStringContainsString('it-scope-myorg', $myOrgHtml);
        $this->assertStringContainsString('Telecon', $myOrgHtml);

        $shellCss = file_get_contents(resource_path('views/service_company/interview-schedule/table.blade.php'));
        $this->assertNotFalse($shellCss);
        // AppTracker standardized hex tokens on My Organization, Internal Client, Direct Client
        $this->assertStringContainsString('#fef3c7', $shellCss);
        $this->assertStringContainsString('#92400e', $shellCss);
        $this->assertStringContainsString('#dbeafe', $shellCss);
        $this->assertStringContainsString('#1e40af', $shellCss);
        $this->assertStringContainsString('#d1fae5', $shellCss);
        $this->assertStringContainsString('#166534', $shellCss);
    }

    public function test_interview_tracker_shell_has_client_tracker_kpi_single_toolbar_card_and_icon_fix(): void
    {
        $bladePath = resource_path('views/service_company/interview-schedule/table.blade.php');
        $content = file_get_contents($bladePath);
        $this->assertNotFalse($content);

        // Icon-font safety: Font Awesome 6 Free included & never overridden by Poppins
        $this->assertStringContainsString('font-awesome/6.5.1/css/all.min.css', $content);
        $this->assertStringContainsString('"Font Awesome 6 Free"', $content);
        $this->assertStringContainsString('*:not(i):not(.fa):not(.fas)', $content);
        $this->assertStringNotContainsString('href="assets/plugins/global/plugins.bundle.css"', $content);

        // Client Tracker KPI design parity — 7 total interactive metric cards
        $this->assertStringContainsString('class="it-stat-grid"', $content);
        $this->assertStringContainsString('class="it-stat-card it-stat-filter"', $content);
        $this->assertStringContainsString('class="it-stat-icon blue"', $content);
        $this->assertStringContainsString('class="it-stat-num"', $content);
        $this->assertStringContainsString('class="it-stat-sub', $content);

        // All 7 KPI cards present with data-kpi-filter
        $this->assertStringContainsString('data-kpi-filter="this_month"', $content);
        $this->assertStringContainsString('data-kpi-filter="today"', $content);
        $this->assertStringContainsString('data-kpi-filter="upcoming"', $content);
        $this->assertStringContainsString('data-kpi-filter="pending_feedback"', $content);
        $this->assertStringContainsString('data-kpi-filter="direct_client"', $content);
        $this->assertStringContainsString('data-kpi-filter="internal_client"', $content);
        $this->assertStringContainsString('data-kpi-filter="my_organization"', $content);
        $this->assertStringContainsString('Direct Client Interviews', $content);
        $this->assertStringContainsString('Internal Client Interviews', $content);
        $this->assertStringContainsString('My Organization Interviews', $content);

        // Subtabs completely removed
        $this->assertStringNotContainsString('class="it-tabs-card"', $content);
        $this->assertStringNotContainsString('id="tab-count-all"', $content);
        $this->assertStringNotContainsString('filterByTab', $content);

        // Search clear X button removed
        $this->assertStringNotContainsString('id="itSearchClear"', $content);

        // Single dedicated white toolbar card holding Search, Mode, Sort & Reset together
        $this->assertStringContainsString('class="it-toolbar-card"', $content);
        $this->assertStringContainsString('class="it-filter-bar"', $content);
        $this->assertStringContainsString('id="itSearchBoxWrapper"', $content);
        $this->assertStringContainsString('id="itSearchMatchCount"', $content);
        $this->assertStringContainsString('id="itSearchKbdHint"', $content);
        $this->assertStringContainsString('id="itModeDropdownWrapper"', $content);
        $this->assertStringContainsString('id="itSortDropdownWrapper"', $content);
        $this->assertStringContainsString('id="itResetFiltersBtn"', $content);

        // Verify Search, Mode, Sort & Reset all live inside .it-toolbar-card (before .it-stream-wrapper)
        $toolbarPos = strpos($content, '<div class="it-toolbar-card">');
        $streamPos = strpos($content, '<div class="it-stream-wrapper">');
        $this->assertNotFalse($toolbarPos);
        $this->assertNotFalse($streamPos);
        $this->assertLessThan($streamPos, $toolbarPos);

        $toolbarSection = substr($content, $toolbarPos, $streamPos - $toolbarPos);
        $this->assertStringContainsString('id="kt_tagify_2"', $toolbarSection);
        $this->assertStringContainsString('id="itModeFilter"', $toolbarSection);
        $this->assertStringContainsString('id="sortingfilter"', $toolbarSection);
        $this->assertStringContainsString('id="itResetFiltersBtn"', $toolbarSection);
    }
}
