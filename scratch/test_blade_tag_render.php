<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$company_id = 2; // 4Bell Technology

$data = [
    [
        'id' => 101,
        'job_id' => 1,
        'wiseID' => 1,
        'job_title' => 'Lead Java Developer',
        'full_name' => 'bhupendra char',
        'current_designation' => 'Java Developer',
        'company_name' => '4Bell Technology',
        'client_name' => '4Bell Technology',
        'client_title_name_enriched' => '4Bell Technology',
        'is_self_company_enriched' => true,
        'job_company_id' => 2,
        'thirdparty_company_id' => 2,
        'schedule_date' => '2026-09-26',
        'schedule_to_time' => '12:20',
        'interview_mode' => 2,
        'mode_name' => 'Video',
        'posted_by' => 'Vibhanshu Jaiswal',
        'timezone_abbreviation' => 'IST',
        'interviewers' => '',
        'master_status_id' => 1,
        'child_status_id' => 0,
        'master_status_name' => 'InReview',
        'feedback' => 5,
    ],
    [
        'id' => 102,
        'job_id' => 2,
        'wiseID' => 2,
        'job_title' => 'Junior Java Developer',
        'full_name' => 'bhupendra char',
        'current_designation' => 'Java Developer',
        'company_name' => '4Bell Technology',
        'client_name' => 'Lepton Software',
        'client_title_name_enriched' => 'Lepton Software',
        'is_self_company_enriched' => false,
        'job_company_id' => 2,
        'thirdparty_company_id' => 2,
        'schedule_date' => '2026-09-25',
        'schedule_to_time' => '16:15',
        'interview_mode' => 1,
        'mode_name' => 'Face to Face',
        'posted_by' => 'Vibhanshu Jaiswal',
        'timezone_abbreviation' => 'IST',
        'interviewers' => '',
        'master_status_id' => 1,
        'child_status_id' => 0,
        'master_status_name' => 'InReview',
        'feedback' => 5,
    ],
    [
        'id' => 103,
        'job_id' => 3,
        'wiseID' => 3,
        'job_title' => 'Junior Java Developer',
        'full_name' => 'bhupendra char',
        'current_designation' => 'Java Developer',
        'company_name' => 'InnovateSoft Technologies',
        'client_name' => 'InnovateSoft Technologies',
        'client_title_name_enriched' => 'InnovateSoft Technologies',
        'is_self_company_enriched' => true,
        'job_company_id' => 4,
        'thirdparty_company_id' => 4,
        'schedule_date' => '2026-09-24',
        'schedule_to_time' => '13:51',
        'interview_mode' => 1,
        'mode_name' => 'Face to Face',
        'posted_by' => 'Varsha Sharma',
        'timezone_abbreviation' => 'IST',
        'interviewers' => '',
        'master_status_id' => 2,
        'child_status_id' => 0,
        'master_status_name' => 'Sourcing',
        'feedback' => 5,
    ],
];

$html = view('service_company.ajax_pages.bench-candidate-interview-schedule-ajax', [
    'data' => $data,
    'company_id' => $company_id,
    'userPermissions' => ['view_bench', 'view_schedule'],
    'spokpersonMap' => collect(),
])->render();

preg_match_all('/class="it-job-title"[^>]*>\s*([^<]+)<\/div>[\s\S]*?class="it-job-company"[^>]*>\s*([^<]+)<\/div>[\s\S]*?class="it-job-type ([^"]+)">([^<]+)<\/span>[\s\S]*?class="it-job-id">([^<]+)<\/a>/', $html, $matches, PREG_SET_ORDER);

foreach ($matches as $idx => $m) {
    echo "Row " . ($idx + 1) . ": Title='" . trim($m[1]) . "' | Company='" . trim($m[2]) . "' | BadgeClass='" . trim($m[3]) . "' | BadgeLabel='" . trim($m[4]) . "' | JobID='" . trim($m[5]) . "'\n";
}
