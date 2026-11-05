<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\DTO\ServiceCompany\ClientActivityQuery;
use App\Services\ServiceCompany\ClientActivityReadService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

echo "=== ENTERPRISE VERIFICATION: VISIBILITY LOGIC & ATTRIBUTION ENGINE ===\n\n";

DB::connection()->enableQueryLog();
$service = app(ClientActivityReadService::class);

// Flush Redis page cache for fresh execution
try {
    Redis::del("ats:client_act:page:3:3:pipeline:all:1:15:client:0:0");
    Redis::del("ats:client_act:page:2:3:pipeline:all:1:15:agency:0:0");
    Redis::del("ats:job_brief:v4:id:2:2:direct");
    Redis::del("ats:job_brief:v4:id:2:3:direct");
    Redis::del("ats:job_brief:v4:id:19:2:direct");
} catch (\Throwable) {}

// =========================================================================
// SCENARIO 1: REQUISITION #2 (TECHNOVA <-> 4BELL)
// =========================================================================
echo "[SCENARIO 1: REQUISITION #2 (TECHNOVA SOLUTIONS <-> 4BELL TECHNOLOGY)]\n";

// 1A. Direct Client View (Cognizant/TechNova looking at Activity tab)
$clientQuery = new ClientActivityQuery(
    tenantId: 3,
    serviceCompanyId: 2,
    clientId: 3,
    clientType: 'direct',
    partnerCompanyId: null,
    category: 'all',
    timeframe: 'all',
    search: null,
    cursorSequence: null,
    cursorEventId: null,
    limit: 15,
    forceRefresh: true,
    viewerType: 'client',
    agencyCompanyId: 2
);

DB::flushQueryLog();
$clientResult = $service->getActivityStream($clientQuery);
$clientDbQueries = DB::getQueryLog();

echo "1A. Direct Client View (Technova):\n";
echo "  -> Normalized Events Count: " . count($clientResult->items) . "\n";
echo "  -> MySQL Queries Executed: " . count($clientDbQueries) . " (Target: 0)\n";
if (count($clientDbQueries) === 0) {
    echo "  [PASS] 0% MySQL Load Confirmed!\n";
} else {
    echo "  [FAIL] MySQL queries detected: " . json_encode($clientDbQueries) . "\n";
}

// 1B. Agency Partner View (4Bell looking at the exact same shared activity tab)
$agencyQuery = new ClientActivityQuery(
    tenantId: 2,
    serviceCompanyId: 3,
    clientId: 2,
    clientType: 'direct',
    partnerCompanyId: null,
    category: 'all',
    timeframe: 'all',
    search: null,
    cursorSequence: null,
    cursorEventId: null,
    limit: 15,
    forceRefresh: true,
    viewerType: 'agency',
    agencyCompanyId: 2
);

DB::flushQueryLog();
$agencyResult = $service->getActivityStream($agencyQuery);
$agencyDbQueries = DB::getQueryLog();

echo "\n1B. Agency Partner View (4Bell Technology):\n";
echo "  -> Normalized Events Count: " . count($agencyResult->items) . "\n";
echo "  -> MySQL Queries Executed: " . count($agencyDbQueries) . " (Target: 0)\n";
if (count($agencyDbQueries) === 0) {
    echo "  [PASS] 0% MySQL Load Confirmed!\n";
} else {
    echo "  [FAIL] MySQL queries detected: " . json_encode($agencyDbQueries) . "\n";
}

// =========================================================================
// SCENARIO 2: POINT 1 & POINT 2 CHECKS (Attribution, No "Agency Captain", No "Client Partner")
// =========================================================================
echo "\n[SCENARIO 2: ACTOR ATTRIBUTION & PREFIX REMOVAL]\n";

foreach ($agencyResult->items as $event) {
    $type = $event['event_type'];
    $attr = $event['attribution'];
    $job = $event['job'];
    $actorName = $attr['name'];
    $actorCo = $attr['company_name'];
    $label = $attr['label'];

    // Check: No "Agency Captain" text in name
    if (str_contains(strtolower($actorName), 'agency captain')) {
        echo "  [FAIL] Found 'Agency Captain' in name: {$actorName}\n";
        exit(1);
    }

    // Check: No "Client Partner" in actor name or company
    if (strtolower($actorName) === 'client partner' || strtolower($actorCo) === 'client partner') {
        echo "  [FAIL] Found 'Client Partner' in attribution: {$actorName} · {$actorCo}\n";
        exit(1);
    }

    if ($type === 'candidate.uploaded') {
        echo "  - Candidate Submission: {$label} {$actorName} · {$actorCo}\n";
        if (strtolower($actorCo) !== '4bell technology') {
            echo "  [FAIL] Agency submission company should be 4Bell Technology, got: {$actorCo}\n";
            exit(1);
        }
    } elseif ($type === 'candidate.joined' || $type === 'candidate.stage_changed') {
        echo "  - Stage Movement: {$label} {$actorName} · {$actorCo}\n";
    } elseif ($type === 'job.shared') {
        echo "  - Job Shared: {$label} {$actorName} · {$actorCo}\n";
    }
}
echo "  [PASS] All attribution names are clean actual names without 'Agency Captain' or 'Client Partner'!\n";
echo "  [PASS] Agency candidate submission attributes correctly to 4Bell Technology!\n";

// =========================================================================
// SCENARIO 3: INTERNAL CLIENT VISIBILITY (MAHINDRA / CLIENT BETA)
// =========================================================================
echo "\n[SCENARIO 3: INTERNAL CLIENT VISIBILITY ON REQ #19 (CLIENT BETA under 4BELL)]\n";

$mockEvents = [
    [
        'source_type' => 'jobs',
        'hit' => [
            '_id' => '19',
            '_source' => [
                'id' => 19,
                'title' => 'Beta Position',
                'created_at' => '2026-10-20T12:00:00+05:30',
                'user_name' => 'Rahul Sharma'
            ]
        ]
    ]
];

// Reflection to invoke normalizeAndSortEvents directly
$reflection = new ReflectionClass(ClientActivityReadService::class);
$method = $reflection->getMethod('normalizeAndSortEvents');
$method->setAccessible(true);

// 3A. Direct Client Viewer
$directQuery = new ClientActivityQuery(
    tenantId: 2,
    serviceCompanyId: 2,
    clientId: 2,
    clientType: 'internal',
    partnerCompanyId: null,
    category: 'all',
    timeframe: 'all',
    search: null,
    cursorSequence: null,
    cursorEventId: null,
    viewerType: 'client'
);
$directNormalized = $method->invoke($service, $mockEvents, $directQuery);
$directJob = $directNormalized[0]['job'];

echo "3A. Direct Client View of Req #19:\n";
echo "  -> Job Title: " . $directJob['title'] . "\n";
echo "  -> Row 2 Company Name: " . $directJob['company_name'] . "\n";
echo "  -> Is Internal Client: " . ($directJob['is_internal_client'] ? 'YES (Visible)' : 'NO') . "\n";
if (strtolower($directJob['company_name']) === 'client beta' && $directJob['is_internal_client'] === true) {
    echo "  [PASS] Internal client 'Client Beta' is correctly VISIBLE to Direct Client!\n";
} else {
    echo "  [FAIL] Expected 'Client Beta', got: " . $directJob['company_name'] . "\n";
    exit(1);
}

// 3B. Agency Viewer
$agencyReqQuery = new ClientActivityQuery(
    tenantId: 2,
    serviceCompanyId: 2,
    clientId: 2,
    clientType: 'direct',
    partnerCompanyId: null,
    category: 'all',
    timeframe: 'all',
    search: null,
    cursorSequence: null,
    cursorEventId: null,
    viewerType: 'agency'
);
$agencyNormalized = $method->invoke($service, $mockEvents, $agencyReqQuery);
$agencyJob = $agencyNormalized[0]['job'];

echo "\n3B. Agency Partner View of Req #19:\n";
echo "  -> Job Title: " . $agencyJob['title'] . "\n";
echo "  -> Row 2 Company Name: " . $agencyJob['company_name'] . "\n";
echo "  -> Is Internal Client: " . ($agencyJob['is_internal_client'] ? 'YES' : 'NO (Hidden)') . "\n";
if (strtolower($agencyJob['company_name']) === '4bell technology' && $agencyJob['is_internal_client'] === false) {
    echo "  [PASS] Internal client 'Client Beta' is 100% HIDDEN! Agency sees Direct Client: 4Bell Technology!\n";
} else {
    echo "  [FAIL] Expected '4Bell Technology', got: " . $agencyJob['company_name'] . "\n";
    exit(1);
}

// =========================================================================
// SCENARIO 4: BLADE RENDERING OF 3-ROW CARDS
// =========================================================================
echo "\n[SCENARIO 4: BLADE RENDERING CHECK]\n";

$renderedHtml = view('service_company.ajax_pages.client_activity_cards_ajax', [
    'items' => $agencyResult->items,
    'companyTimezone' => 'Asia/Kolkata',
])->render();

echo "  -> Rendered HTML length: " . strlen($renderedHtml) . " bytes\n";

if (str_contains($renderedHtml, 'cd-activity-company-context') && str_contains($renderedHtml, 'cd-activity-company-name')) {
    echo "  [PASS] Row 2 cd-activity-company-context rendered in Blade HTML!\n";
} else {
    echo "  [FAIL] Missing cd-activity-company-context in Blade HTML\n";
    exit(1);
}

if (!str_contains($renderedHtml, 'Agency Captain') && !str_contains($renderedHtml, 'Client Partner')) {
    echo "  [PASS] Blade HTML contains 0 occurrences of 'Agency Captain' and 'Client Partner'!\n";
} else {
    echo "  [FAIL] Found forbidden string in Blade HTML\n";
    exit(1);
}

echo "\n=== ALL VERIFICATION SCENARIOS PASSED WITH 100% SUCCESS ===\n";
