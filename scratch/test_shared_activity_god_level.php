<?php

require __DIR__ . '/../vendor/autoload.php';

$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\DTO\ServiceCompany\ClientActivityQuery;
use App\Services\ServiceCompany\ClientActivityReadService;
use Illuminate\Support\Facades\DB;

echo "=== REAL REQUISITION #2: TECHNOVA SOLUTIONS (DIRECT CLIENT 3) <-> 4BELL (AGENCY 2) ===\n\n";

$readService = app(ClientActivityReadService::class);

// ---------------------------------------------------------
// TEST 1: Direct Client View (Client 3 Requisitions)
// ---------------------------------------------------------
echo "[TEST 1] Direct Client Drawer Activity Stream (Client View - Unmasked):\n";

DB::flushQueryLog();
DB::enableQueryLog();

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
    limit: 20,
    forceRefresh: true,
    viewerType: 'client',
    agencyCompanyId: 2
);

$clientStartTime = microtime(true);
$clientViewModel = $readService->getActivityStream($clientQuery);
$clientDuration = (microtime(true) - $clientStartTime) * 1000;
$clientQueries = DB::getQueryLog();

echo "  -> Status: SUCCESS\n";
echo "  -> Total Events Normalized: " . count($clientViewModel->items) . "\n";
echo "  -> Execution Time: " . round($clientDuration, 2) . " ms\n";
echo "  -> MySQL Queries Executed: " . count($clientQueries) . " (Target: 0)\n";

if (count($clientQueries) > 0) {
    echo "  [WARNING] SQL Queries detected:\n";
    foreach ($clientQueries as $q) {
        echo "    SQL: " . $q['query'] . "\n";
    }
} else {
    echo "  [PASS] Absolute 0% MySQL Load Confirmed!\n";
}

echo "  Sample Events in Client View:\n";
foreach (array_slice($clientViewModel->items, 0, 5) as $item) {
    $eType = $item['event_type'] ?? '';
    $actor = $item['actor'] ?? [];
    $attr = $item['attribution'] ?? [];
    $cand = $item['candidate'] ?? [];
    $trans = $item['transition'] ?? [];
    echo "    - [{$eType}] Candidate: " . ($cand['name'] ?? 'N/A') . " | {$trans['from_stage_name']} -> {$trans['to_stage_name']} | Actor: " . ($attr['name'] ?? '') . " ({$actor['role']})\n";
}

// ---------------------------------------------------------
// TEST 2: Agency Drawer View (Agency 2 Partner View - Masked Privacy)
// ---------------------------------------------------------
echo "\n[TEST 2] Agency Drawer Activity Stream (Agency Partner View - Masked):\n";

DB::flushQueryLog();

$agencyQuery = new ClientActivityQuery(
    tenantId: 2,
    serviceCompanyId: 2,
    clientId: 3,
    clientType: 'agency',
    partnerCompanyId: 3,
    category: 'all',
    timeframe: 'all',
    search: null,
    cursorSequence: null,
    cursorEventId: null,
    limit: 20,
    forceRefresh: true,
    viewerType: 'agency',
    agencyCompanyId: 2
);

$agencyStartTime = microtime(true);
$agencyViewModel = $readService->getActivityStream($agencyQuery);
$agencyDuration = (microtime(true) - $agencyStartTime) * 1000;
$agencyQueries = DB::getQueryLog();

echo "  -> Status: SUCCESS\n";
echo "  -> Total Events Normalized: " . count($agencyViewModel->items) . "\n";
echo "  -> Execution Time: " . round($agencyDuration, 2) . " ms\n";
echo "  -> MySQL Queries Executed: " . count($agencyQueries) . " (Target: 0)\n";

if (count($agencyQueries) > 0) {
    echo "  [WARNING] SQL Queries detected:\n";
    foreach ($agencyQueries as $q) {
        echo "    SQL: " . $q['query'] . "\n";
    }
} else {
    echo "  [PASS] Absolute 0% MySQL Load Confirmed!\n";
}

echo "  Sample Events in Agency View (Check Masking & Attribution):\n";
foreach (array_slice($agencyViewModel->items, 0, 5) as $item) {
    $eType = $item['event_type'] ?? '';
    $actor = $item['actor'] ?? [];
    $attr = $item['attribution'] ?? [];
    $cand = $item['candidate'] ?? [];
    $trans = $item['transition'] ?? [];
    echo "    - [{$eType}] Candidate: " . ($cand['name'] ?? 'N/A') . " | {$trans['from_stage_name']} -> {$trans['to_stage_name']} | Actor: " . ($attr['name'] ?? '') . " ({$actor['role']})\n";
}

// ---------------------------------------------------------
// TEST 3: Master Blade Rendering Check (Both Views)
// ---------------------------------------------------------
echo "\n[TEST 3] Master Blade Pre-Render HTML:\n";
$clientHtml = view('service_company.ajax_pages.client_activity_cards_ajax', [
    'viewModel' => $clientViewModel,
    'items' => $clientViewModel->items,
    'companyTimezone' => 'Asia/Kolkata',
])->render();
echo "  -> Client Cards HTML length: " . strlen($clientHtml) . " bytes\n";

$agencyHtml = view('service_company.ajax_pages.client_activity_cards_ajax', [
    'viewModel' => $agencyViewModel,
    'items' => $agencyViewModel->items,
    'companyTimezone' => 'Asia/Kolkata',
])->render();
echo "  -> Agency Cards HTML length: " . strlen($agencyHtml) . " bytes\n";
echo "  [PASS] Zero Blade Errors across Client & Agency Streams!\n";

echo "\n=== ALL VERIFICATION TESTS COMPLETED ===\n";
