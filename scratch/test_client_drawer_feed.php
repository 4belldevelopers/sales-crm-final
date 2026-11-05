<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ServiceCompany\ClientActivityReadService;
use App\DTO\ServiceCompany\ClientActivityQuery;

$service = app(ClientActivityReadService::class);

// Flush redis cache for Company 2 viewing Client 3
\Illuminate\Support\Facades\Redis::del("ats:client_job_ids:2:3:direct:3:client");

$q = new ClientActivityQuery(
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

$vm = $service->getActivityStream($q);
echo "Total Items: " . count($vm->items) . "\n";
foreach ($vm->items as $i => $item) {
    echo "Item $i | Type: {$item['event_type']} | Title: " . ($item['candidate']['name'] ?? $item['job']['title'] ?? 'N/A') . "\n";
    echo "  Attribution: {$item['attribution']['label']} {$item['attribution']['name']} ({$item['attribution']['company_name']})\n";
    echo "  Occurred At: {$item['occurred_at']}\n";
    if (!empty($item['transition']['to_stage_name'])) {
        echo "  Transition: {$item['transition']['to_stage_name']}\n";
    }
}
