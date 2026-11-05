<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Services\ServiceCompany\ClientActivityReadService;
use App\DTO\ServiceCompany\ClientActivityQuery;
use Illuminate\Support\Facades\Redis;

$service = app(ClientActivityReadService::class);

echo "=== TEST 1: Company 2 (4Bell) viewing TechNova as Agency (Agency Drawer) ===\n";
Redis::del("ats:client_job_ids:2:3:direct:3:agency");

$ref = new ReflectionClass($service);
$resolveJobs = $ref->getMethod('resolveClientJobIds');
$resolveJobs->setAccessible(true);
$jobs1 = $resolveJobs->invoke($service, 2, 3, 'direct', 3, 'agency');
echo "Resolved Jobs (with current code): " . json_encode($jobs1) . "\n";
