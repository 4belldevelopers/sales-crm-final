<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

config(['database.default' => 'mysql', 'database.connections.mysql.database' => 'newdata']);
\Illuminate\Support\Facades\Cache::put('jero_super_admin_company_ids', [1], 3600);
$service = app(App\Services\FindAgencyService::class);
$userA = new \App\Models\User();
$userA->id = 10;
$userA->company_id = 100;

$userB = new \App\Models\User();
$userB->id = 20;
$userB->company_id = 200;

\Illuminate\Support\Facades\Auth::setUser($userA);
$reqA = \Illuminate\Http\Request::create('/service_company/find-agency-data', 'GET', ['page' => 1]);
$resA = $service->getAgencyData($reqA, $userA);
echo 'ResA status: ' . $resA->getStatusCode() . PHP_EOL;
echo 'ResA ETag: ' . $resA->headers->get('ETag') . PHP_EOL;

\Illuminate\Support\Facades\Auth::setUser($userB);
$reqB = \Illuminate\Http\Request::create('/service_company/find-agency-data', 'GET', ['page' => 1], [], [], [
    'HTTP_IF_NONE_MATCH' => $resA->headers->get('ETag'),
    'HTTP_X_HAS_REPRESENTATION' => '1',
]);
$resB = $service->getAgencyData($reqB, $userB);
echo 'ResB status: ' . $resB->getStatusCode() . PHP_EOL;
echo 'ResB ETag: ' . $resB->headers->get('ETag') . PHP_EOL;
