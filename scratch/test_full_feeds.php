<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ServiceCompany\ClientActivityReadService;
use App\DTO\ServiceCompany\ClientActivityQuery;

$service = app(ClientActivityReadService::class);

echo "1. Testing Agency Drawer Feed (Company 2 viewing Agency 3):\n";
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
echo "  KPIs: " . json_encode($vm1->kpis) . "\n";
echo "  Total Items: " . count($vm1->items) . "\n";

echo "\n2. Testing Client Drawer Feed (Company 3 viewing Direct Client 2):\n";
$q2 = new ClientActivityQuery(
    tenantId: 3,
    serviceCompanyId: 3,
    clientId: 2,
    clientType: 'direct',
    partnerCompanyId: 2,
    category: 'all',
    timeframe: 'all',
    search: null,
    cursorSequence: null,
    cursorEventId: null,
    limit: 15,
    viewerType: 'client'
);
$vm2 = $service->getActivityStream($q2);
echo "  KPIs: " . json_encode($vm2->kpis) . "\n";
echo "  Total Items: " . count($vm2->items) . "\n";

echo "\n3. Testing Agency Drawer Feed (Company 3 viewing Agency 2):\n";
$q3 = new ClientActivityQuery(
    tenantId: 3,
    serviceCompanyId: 3,
    clientId: 2,
    clientType: 'direct',
    partnerCompanyId: 2,
    category: 'all',
    timeframe: 'all',
    search: null,
    cursorSequence: null,
    cursorEventId: null,
    limit: 15,
    viewerType: 'agency'
);
$vm3 = $service->getActivityStream($q3);
echo "  KPIs: " . json_encode($vm3->kpis) . "\n";
echo "  Total Items: " . count($vm3->items) . "\n";

echo "\n4. Testing Client Drawer Feed (Company 2 viewing Direct Client 3):\n";
$q4 = new ClientActivityQuery(
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
$vm4 = $service->getActivityStream($q4);
echo "  KPIs: " . json_encode($vm4->kpis) . "\n";
echo "  Total Items: " . count($vm4->items) . "\n";

echo "\nALL 4 RELATIONSHIP CONTEXT FEEDS EXECUTED WITH 0 ERRORS!\n";
