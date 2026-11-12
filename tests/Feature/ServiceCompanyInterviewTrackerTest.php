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
                'jobId' => 86,
                'job_id' => 86,
                'full_name' => 'uujaiswal',
                'current_designation' => 'RPA Developer',
                'city_name' => 'pune',
                'country_name' => 'india',
                'email' => 'uujaiswal@example.com',
                'job_title' => 'RPA Developer',
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
                'mode_lavel_name' => 'L1 Technical',
                'interview_type' => 1,
                'interview_mention_link' => 'https://meet.google.com/abc-defg-hij',
                'interviewers' => '',
                'posted_by' => 'vibhanshu jaiswal',
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
                'jobId' => 81,
                'job_id' => 81,
                'full_name' => 'Vibhanshu <script>alert(1)</script>',
                'current_designation' => 'PHP / Laravel Developer',
                'city_name' => '',
                'country_name' => '',
                'email' => 'v@example.com',
                'job_title' => 'PHP / Laravel Developer',
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
                'mode_lavel_name' => 'Final Panel',
                'interview_type' => 2,
                'interview_mention_link' => '123 Enterprise Park, Bengaluru',
                'interviewers' => 'Alice Smith , Bob Jones , Carol Lee , Dave Kim',
                'posted_by' => 'vibhanshu jaiswal',
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
            'userCaptainJobIds' => [86 => true],
        ])->render();

        // Rule 20 Card + 5 Discrete Sub-Cards + Bottom Strip
        $this->assertStringContainsString('class="it-card"', $html);
        $this->assertStringContainsString('it-card-grid', $html);
        $this->assertStringContainsString('it-subcard-candidate', $html);
        $this->assertStringContainsString('it-subcard-job', $html);
        $this->assertStringContainsString('it-subcard-schedule', $html);
        $this->assertStringContainsString('it-subcard-panel', $html);
        $this->assertStringContainsString('it-subcard-feedback', $html);
        $this->assertStringContainsString('it-card-bottom', $html);

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
            'userCaptainJobIds' => [],
        ])->render();

        $this->assertStringContainsString('it-empty-card', $html);
        $this->assertStringContainsString('No Interviews Found', $html);
        $this->assertStringNotContainsString('<tr', $html);
    }
}
