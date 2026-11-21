<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\View;
use Tests\TestCase;

/**
 * ServiceCompanyBenchInterviewScheduleTest
 *
 * Intent: Verifies that the Service Company Bench Candidate Interview Tracker views render cleanly
 * under the Rule 20 Modern Card + Sub-Card Enterprise Design System, enforce
 * permission gating, escape XSS payloads, and preserve interactive DOM hooks.
 * Tenancy: Scoped strictly to company_id.
 */
class ServiceCompanyBenchInterviewScheduleTest extends TestCase
{
    public function test_bench_interview_tracker_views_exist(): void
    {
        $this->assertTrue(View::exists('service_company.bench-candidates.interview-schedule'));
        $this->assertTrue(View::exists('service_company.ajax_pages.bench-candidate-interview-schedule-ajax'));
    }

    public function test_bench_interview_ajax_renders_rule_20_card_and_subcards(): void
    {
        $sampleData = [
            [
                'id' => 201,
                'wiseID' => 601,
                'job_application_id' => 801,
                'jobId' => 90,
                'job_id' => 90,
                'job_company_id' => 2,
                'full_name' => 'Bench Candidate Alpha',
                'current_designation' => 'Senior Java Engineer',
                'current_company' => 'Acme Corp',
                'city_name' => 'Bengaluru',
                'country_name' => 'India',
                'email' => 'alpha@example.com',
                'job_title' => 'Senior Java Engineer',
                'employment_type' => 0,
                'self_company' => 0,
                'client_name' => 'ORACLE',
                'company_name' => '4Bell Technology',
                'uc_company_name' => '4Bell Technology',
                'recommendation_id' => 0,
                'to_company_id' => 0,
                'thirdparty_company_id' => 0,
                'ClientComID' => 2,
                'schedule_date' => '2026-11-25 11:00:00',
                'schedule_to_time' => '11:00',
                'timezone_abbreviation' => 'IST',
                'interview_mode' => 2,
                'mode_name' => 'Video',
                'mode_lavel_name' => 'Google Meet',
                'interview_type' => 1,
                'interview_mention_link' => 'https://meet.google.com/xyz-uvwx-rst',
                'interviewers' => 'John Doe',
                'posted_by' => 'Admin Recruiter',
                'submitted_at' => now()->subHours(5)->toIso8601String(),
                'created_at' => now()->subHours(5)->toIso8601String(),
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
                'id' => 202,
                'wiseID' => 602,
                'job_application_id' => 802,
                'jobId' => 95,
                'job_id' => 95,
                'job_company_id' => 88,
                'full_name' => 'Bench Candidate Beta <script>alert("xss")</script>',
                'current_designation' => 'DevOps Specialist',
                'current_company' => 'Cloud Inc',
                'city_name' => '',
                'country_name' => '',
                'email' => 'beta@example.com',
                'job_title' => 'Cloud Architect',
                'employment_type' => 1,
                'self_company' => 0,
                'client_name' => 'MICROSOFT',
                'company_name' => 'Direct Partner',
                'uc_company_name' => 'Direct Partner',
                'recommendation_id' => 15,
                'to_company_id' => 2,
                'thirdparty_company_id' => 0,
                'ClientComID' => 2,
                'schedule_date' => date('Y-m-d H:i:s', strtotime('+2 days')),
                'schedule_to_time' => '14:30',
                'timezone_abbreviation' => 'IST',
                'interview_mode' => 3,
                'mode_name' => 'In-Person',
                'mode_lavel_name' => 'Face to Face',
                'interview_type' => 2,
                'interview_mention_link' => 'Outer Ring Road, Bengaluru',
                'interviewers' => 'Alice Walker , Bob Vance , Charlie Green',
                'posted_by' => 'Lead Recruiter',
                'submitted_at' => now()->subDays(2)->toIso8601String(),
                'created_at' => now()->subDays(2)->toIso8601String(),
                'master_status_id' => 8,
                'master_status_name' => 'In Review',
                'child_status_id' => 0,
                'child_status_name' => '',
                'feedback' => 1,
                'available_status' => 1,
                'average_star_rating' => 5.0,
                'mailstatus' => 1,
            ],
        ];

        $followupMap = collect([
            202 => (object) ['id' => 202, 'followup_status' => 'confirmed', 'followup_status_at' => '2026-11-21 12:00:00'],
        ]);
        $feedbackMap = collect([
            202 => (object) ['id' => 950, 'interview_id' => 202, 'available_status' => 1, 'interview_result' => 'selected', 'overall_recommendation' => 'strong_hire'],
        ]);

        $html = View::make('service_company.ajax_pages.bench-candidate-interview-schedule-ajax', [
            'data' => $sampleData,
            'userPermissions' => ['view_bench', 'view_schedule'],
            'feedback_status' => [1 => ['id' => 1, 'name' => 'Selected']],
            'available_status' => [],
            'company_id' => 2,
            'chkAdminRole' => 1,
            'followupMap' => $followupMap,
            'feedbackMap' => $feedbackMap,
            'spokpersonMap' => collect(),
            'clientCompanyMap' => [90 => 'ORACLE', 95 => 'MICROSOFT'],
            'jobMetaMap' => [
                90 => ['client_name' => 'ORACLE', 'self_company' => 0, 'employment_type' => 0],
                95 => ['client_name' => 'MICROSOFT', 'self_company' => 0, 'employment_type' => 1],
            ],
            'candidateMetaMap' => [
                'by_wise' => [
                    601 => ['current_company' => 'Acme Corp', 'submitted_at' => now()->subHours(5)->toIso8601String()],
                    602 => ['current_company' => 'Cloud Inc', 'submitted_at' => now()->subDays(2)->toIso8601String()],
                ],
                'by_app' => [],
            ],
            'userCaptainJobIds' => [90 => true],
        ])->render();

        // 1. Rule 20 Card + 6 Discrete Sub-Cards + Bottom Strip
        $this->assertStringContainsString('class="it-card"', $html);
        $this->assertStringContainsString('it-card-grid', $html);
        $this->assertStringContainsString('it-subcard', $html);
        $this->assertStringContainsString('it-card-bottom', $html);

        // 2. Candidate Sub-card & XSS escaping
        $this->assertStringContainsString('Bench Candidate Alpha', $html);
        $this->assertStringNotContainsString('<script>alert("xss")</script>', $html);
        $this->assertStringContainsString('Bench Candidate Beta', $html);

        // 3. Requisition Sub-card
        $this->assertStringContainsString('Senior Java Engineer', $html);
        $this->assertStringContainsString('Cloud Architect', $html);

        // 4. Schedule Sub-card
        $this->assertStringContainsString('open-interview-link', $html);

        // 5. Panelists Sub-card
        $this->assertStringContainsString('John Doe', $html);
        $this->assertStringContainsString('Alice Walker', $html);

        // 6. Ownership & Evaluation Sub-cards
        $this->assertStringContainsString('it-feedback-cell', $html);

        // 7. Bottom Strip & Follow-up Dropdown
        $this->assertStringContainsString('it-followup-dropdown', $html);
        $this->assertStringContainsString('show-application-detail', $html);

        // 8. No raw table tags
        $this->assertStringNotContainsString('<tr', $html);
        $this->assertStringNotContainsString('<td', $html);
    }

    public function test_bench_interview_shell_has_rule_20_5_tier_architecture(): void
    {
        $bladePath = resource_path('views/service_company/bench-candidates/interview-schedule.blade.php');
        $content = file_get_contents($bladePath);
        $this->assertNotFalse($content);

        // Tier 1: Top Header Card
        $this->assertStringContainsString('class="it-header-card"', $content);
        $this->assertStringContainsString('Talent Bench Interviews', $content);
        $this->assertStringContainsString('id="itHeaderTotalBadge"', $content);
        $this->assertStringContainsString('id="itRefreshBtn"', $content);

        // Tier 2: 5-KPI Metric Strip
        $this->assertStringContainsString('class="it-stat-grid"', $content);
        $this->assertStringContainsString('id="stat-all-interviews"', $content);
        $this->assertStringContainsString('id="stat-interview-month"', $content);
        $this->assertStringContainsString('id="stat-today"', $content);
        $this->assertStringContainsString('id="stat-upcoming"', $content);
        $this->assertStringContainsString('id="stat-feedback-pending"', $content);

        // Tier 3: Unified Toolbar Card
        $this->assertStringContainsString('class="it-toolbar-card"', $content);
        $this->assertStringContainsString('id="kt_tagify_2"', $content);
        $this->assertStringContainsString('id="itDateRangeDropdownWrapper"', $content);
        $this->assertStringContainsString('id="itCompanyTypeDropdownWrapper"', $content);
        $this->assertStringContainsString('id="itModeDropdownWrapper"', $content);
        $this->assertStringContainsString('id="itSortDropdownWrapper"', $content);
        $this->assertStringContainsString('id="itResetFiltersBtn"', $content);

        // Tier 4: Card Stream Container
        $this->assertStringContainsString('id="Interview-trackers"', $content);
        $this->assertStringContainsString('id="tableLoading"', $content);

        // Tier 5: Footer Card
        $this->assertStringContainsString('class="it-footer-card"', $content);
        $this->assertStringContainsString('id="showingText"', $content);
        $this->assertStringContainsString('id="pagination"', $content);

        // Modals & Overlays
        $this->assertStringContainsString('id="interview_feedback"', $content);
        $this->assertStringContainsString('id="ilOverlay"', $content);
        $this->assertStringContainsString('id="jdPanelOverlay"', $content);

        // Client Engine: 3-tier caching & AJAX endpoints
        $this->assertStringContainsString('itRamCache', $content);
        $this->assertStringContainsString('JeroBenchInterviewLocalStore', $content);
        $this->assertStringContainsString('benchinterviewdata_ajax', $content);
        $this->assertStringContainsString('patchInterviewRowDelta', $content);
    }

    public function test_bench_interview_empty_state(): void
    {
        $html = View::make('service_company.ajax_pages.bench-candidate-interview-schedule-ajax', [
            'data' => [],
            'userPermissions' => ['view_bench'],
            'feedback_status' => [],
            'available_status' => [],
        ])->render();

        $this->assertStringContainsString('class="it-empty-card"', $html);
        $this->assertStringContainsString('No Bench Interviews Found', $html);
        $this->assertStringContainsString('Reset All Filters', $html);
    }
}
