<?php
require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use App\DTO\ServiceCompany\ClientActivityQuery;
use App\Services\ServiceCompany\ClientActivityReadService;
use App\Services\Candidate\CandidateDrawerQueryService;
use App\Services\ServiceCompany\ClientJobsReadService;
use App\DTO\ServiceCompany\ClientJobsQuery;

Redis::flushall();

echo "========================================================\n";
echo "   GOD-LEVEL MULTI-MODULE BIDIRECTIONAL VERIFICATION   \n";
echo "========================================================\n\n";

$user4Bell = DB::table('users')->where('company_id', 2)->first();
auth()->loginUsingId($user4Bell->id);

echo "Logged in as User: {$user4Bell->name} (Company: 2 - 4Bell)\n\n";

// ---------------------------------------------------------
// TEST 1: 4Bell viewing TechNova as Agency (Agency Drawer)
// ---------------------------------------------------------
echo "---------------------------------------------------------\n";
echo "TEST 1: 4Bell viewing TechNova as Agency (Agency Drawer /consultancy/dviewMore/3)\n";
echo "Expected: 0 active shared jobs, 0 candidates, 0 activities\n";
echo "---------------------------------------------------------\n";

$agencyActivityService = app(ClientActivityReadService::class);
$agencyQuery = new ClientActivityQuery(
    tenantId: 2,
    serviceCompanyId: 2,
    clientId: 3,
    clientType: 'direct',
    partnerCompanyId: 3,
    category: 'all',
    timeframe: 'all',
    search: null,
    cursorSequence: null,
    cursorEventId: null,
    limit: 25,
    forceRefresh: true,
    viewerType: 'agency',
    agencyCompanyId: 3
);
$agencyFeed = $agencyActivityService->getActivityStream($agencyQuery);
echo "Agency Activity Events: " . count($agencyFeed->items) . " (Expected: 0)\n";
echo "Agency Total Events Counter: " . $agencyFeed->kpis['total_activities'] . " (Expected: 0)\n";

// Shared Jobs check in controller query
$sharedJobIdsAgency = DB::table('recommendation_relation')
    ->where('from_company_id', 2)
    ->where('to_company_id', 3)
    ->where('final_status', 1)
    ->pluck('job_id')
    ->toArray();
echo "Active Shared Job IDs with Agency TechNova: " . json_encode($sharedJobIdsAgency) . " (Expected: [])\n";

if (count($agencyFeed->items) === 0 && empty($sharedJobIdsAgency)) {
    echo ">>> TEST 1 PASSED: Strict Agency role isolation verified!\n\n";
} else {
    echo ">>> TEST 1 FAILED!\n\n";
}

// ---------------------------------------------------------
// TEST 2: 4Bell viewing TechNova as Direct Client (Client Drawer)
// ---------------------------------------------------------
echo "---------------------------------------------------------\n";
echo "TEST 2: 4Bell viewing TechNova as Direct Client (Client Drawer /client/dviewMore/3)\n";
echo "Expected: 1 shared job (Job #2), active candidates, activities\n";
echo "---------------------------------------------------------\n";

$clientQuery = new ClientActivityQuery(
    tenantId: 2,
    serviceCompanyId: 2,
    clientId: 3,
    clientType: 'direct',
    partnerCompanyId: 3,
    category: 'all',
    timeframe: 'all',
    search: null,
    cursorSequence: null,
    cursorEventId: null,
    limit: 25,
    forceRefresh: true,
    viewerType: 'client',
    agencyCompanyId: 2
);
$clientFeed = $agencyActivityService->getActivityStream($clientQuery);
echo "Client Activity Events: " . count($clientFeed->items) . " (Expected: > 0)\n";
foreach ($clientFeed->items as $idx => $it) {
    echo "  - Event #$idx: [{$it['event_type']}] Title=" . ($it['candidate']['name'] ?? $it['job']['title'] ?? 'N/A') . " | Date: {$it['occurred_at']} | Attribution: {$it['attribution']['label']} {$it['attribution']['name']} ({$it['attribution']['company_name']})\n";
    if (!empty($it['badge'])) {
        echo "    Badge: [{$it['badge']['text']}]\n";
    }
}

// Check Jobs Read Service with internal client masking
echo "\n--- Testing ClientJobsReadService Masking ---\n";
$jobsService = app(ClientJobsReadService::class);
$jobsQuery = new ClientJobsQuery(
    tenantId: 2,
    serviceCompanyId: 2,
    clientId: 3,
    clientType: 'direct',
    partnerCompanyId: 3,
    search: '',
    status: 'all',
    page: 1,
    perPage: 20
);
$jobsViewModel = $jobsService->getJobs($jobsQuery);
echo "Jobs Found: " . count($jobsViewModel->jobs) . "\n";
foreach ($jobsViewModel->jobs as $j) {
    echo "  Job #{$j->id}: Title={$j->title}, Company={$j->client_name}\n";
    if (stripos($j->client_name, 'mahindra') !== false) {
        echo "  [ERROR] Leaked internal client!\n";
    } else {
        echo "  [OK] Internal client strictly masked to: {$j->client_name}\n";
    }
}

// Check CandidateDrawerQueryService Masking
echo "\n--- Testing CandidateDrawerQueryService Masking ---\n";
$candService = app(CandidateDrawerQueryService::class);
$candResult = $candService->getCandidates(2, 3, 'direct', ['size' => 10]);
echo "Candidates Found: " . count($candResult['data']) . "\n";
foreach ($candResult['data'] as $cand) {
    $submittedBy = $cand['submitted_by_display'] ?? 'N/A';
    $compName = $cand['company_name'] ?? ($cand['client_name'] ?? 'N/A');
    echo "  Candidate #{$cand['id']}: Name={$cand['full_name']}, Target Company={$compName}, Submitted By={$submittedBy}\n";
    if (stripos($compName, 'mahindra') !== false) {
        echo "  [ERROR] Candidate leaked internal client in company_name!\n";
    }
}

echo "\n========================================================\n";
echo "              ALL CHECKS EXECUTED                      \n";
echo "========================================================\n";
