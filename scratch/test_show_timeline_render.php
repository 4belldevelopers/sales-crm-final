<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

$user = User::where('company_id', 2)->first();
if ($user) {
    Auth::login($user);
}

$company_id = 2; // 4Bell Technology
$jobId = 3;
$conid = 6;

$job = DB::table('jobs')->where('id', $jobId)->first();
$job->comId = $job->company_id;

$updateTimeLine = DB::table('updatetimeline')
    ->leftJoin('users', 'users.id', '=', 'updatetimeline.event_by_user_id')
    ->leftJoin('companies', 'companies.id', '=', 'users.company_id')
    ->where('updatetimeline.job_id', $jobId)
    ->where('updatetimeline.application_id', $conid)
    ->select('updatetimeline.*', 'users.name', 'companies.company_name as cur_companyname')
    ->get()
    ->toArray();

$html = view('service_company.jobs.show', [
    'job' => $job,
    'updateTimeLine' => $updateTimeLine,
    'userPermissions' => ['view_jobs'],
    'salaryMode' => [],
    'currency' => [],
    'employmentType' => [],
    'skillArr' => (object)[],
    'jobLocationCount' => 0,
    'clientContactPersonListing' => [],
    'assignTeamCount' => 0,
    'jobAssignList' => [],
    'clientjobAssignListCaptain' => '',
    'agencyView' => false,
    'captainAccessCheck' => false,
    'alertmessage' => '',
    'jobCitySelected' => '',
    'recommendedDataList' => [],
    'terms' => '',
    'interviewShedule' => collect(),
    'bench_assign_count' => 0,
    'bench_assign_row' => [],
    'CaptainCompanyid' => $company_id,
    'tabshow' => false,
    'benchshowtab' => false,
    'benchshowtabComman' => true,
    'benchshowtabclose' => false,
    'jobfindhide' => true,
    'viewmore' => true,
    'hideMarketSections' => false,
    'category_name' => '',
    'jobLocation' => '',
    'recruiterlist' => [],
    'total_candidates' => 0,
    'sourcing_candidates' => 0,
    'evaluation_candidates' => 0,
    'offer_candidates' => 0,
    'joined_candidates' => 0,
    'candidateValuesTotal' => 0,
    'interviewValuesTotal' => 0,
    'candidateValuesTotaloffer' => 0,
    'joinedValuesTotal' => 0,
    'candidateValuesTotaljoin' => 0,
    'candidateValuesTotalinterview' => 0,
    'startOfMonthcomman' => date('Y-m-d'),
    'endOfMonthcomman' => date('Y-m-d'),
    'startOfMonth' => date('Y-m-d'),
    'endOfMonth' => date('Y-m-d'),
    'upcomingData' => [],
    'inreview_count' => 0,
    'sourcing_count' => 0,
    'screening_count' => 0,
    'evaluation_count' => 0,
    'negotiation_count' => 0,
    'offer_count' => 0,
    'joined_count' => 0,
    'Rejected_count' => 0,
    'labels' => [],
    'candidateValues' => [],
    'interviewValues' => [],
    'candidateValuesoffer' => [],
    'candidateValuesjoin' => [],
    'labelsCommoffer' => [],
    'candidateValuesCommoffer' => [],
    'labelsCommjoin' => [],
    'candidateValuesCommjoin' => [],
    'interviewlabelsMonth' => [],
    'interviewValuesMonth' => [],
    'candidatelabelsMonth' => [],
    'candidateValuesMonth' => [],
    'candidatelabels' => [],
    'interviewlabels' => [],
    'candidatelabelsoffer' => [],
    'candidatelabelsjoin' => [],
])->render();

preg_match_all('/<span><b>By:<\/b>\s*([^<]+)<\/span>/i', $html, $matches);

foreach ($matches[1] as $idx => $byLine) {
    echo "Timeline Item " . ($idx + 1) . " Author: " . trim($byLine) . "\n";
}
