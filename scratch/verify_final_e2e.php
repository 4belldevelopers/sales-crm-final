<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ServiceCompany\ClientActivityReadService;
use App\DTO\ServiceCompany\ClientActivityQuery;
use Illuminate\Support\Facades\Redis;

$service = app(ClientActivityReadService::class);

// Flush Redis keys for both contexts
Redis::flushall();

echo "========================================================================\n";
echo "TEST 1: 4Bell (Company 2) viewing TechNova (Company 3) in AGENCIES TRACKER\n";
echo "========================================================================\n";
$q1 = new ClientActivityQuery(
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
    limit: 15,
    viewerType: 'agency'
);

$vm1 = $service->getActivityStream($q1);
echo "KPIs: " . json_encode($vm1->kpis) . "\n";
echo "Total Activity Items: " . count($vm1->items) . "\n";
foreach ($vm1->items as $idx => $it) {
    echo "  Item $idx | Type: {$it['event_type']} | Title: " . ($it['candidate']['name'] ?? $it['job']['title'] ?? 'N/A') . "\n";
    echo "    Attribution: {$it['attribution']['label']} {$it['attribution']['name']} ({$it['attribution']['company_name']})\n";
    echo "    Occurred At: {$it['occurred_at']}\n";
    echo "    Transition: " . ($it['transition']['to_stage_name'] ?? 'none') . "\n";
}

echo "\n========================================================================\n";
echo "TEST 2: 4Bell (Company 2) viewing TechNova (Company 3) in CLIENT TRACKER\n";
echo "========================================================================\n";
$q2 = new ClientActivityQuery(
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
    limit: 15,
    viewerType: 'client'
);

$vm2 = $service->getActivityStream($q2);
echo "KPIs: " . json_encode($vm2->kpis) . "\n";
echo "Total Activity Items: " . count($vm2->items) . "\n";
foreach ($vm2->items as $idx => $it) {
    echo "  Item $idx | Type: {$it['event_type']} | Title: " . ($it['candidate']['name'] ?? $it['job']['title'] ?? 'N/A') . "\n";
    echo "    Attribution: {$it['attribution']['label']} {$it['attribution']['name']} ({$it['attribution']['company_name']})\n";
    echo "    Occurred At: {$it['occurred_at']}\n";
    echo "    Transition: " . ($it['transition']['to_stage_name'] ?? 'none') . "\n";
}
