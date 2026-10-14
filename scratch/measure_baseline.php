<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController;
use Illuminate\Http\Request;

$user = User::find(2);
Auth::login($user);

// Clear drawer cache to simulate cold opening
cache()->flush();

DB::enableQueryLog();
$t0 = microtime(true);
$m0 = memory_get_usage();

$controller = app(ServiceCompanyJobsController::class);
$controller->user = $user;
$controller->userPermissions = [];

// Force clear cache
request()->merge(['_force' => 1]);

// 1. Measure spartnerlisting
$responseSpartner = $controller->spartnerlisting(8);
$t1 = microtime(true);
$m1 = memory_get_usage();
$queriesSpartner = DB::getQueryLog();
DB::flushQueryLog();

$spartnerDuration = round(($t1 - $t0) * 1000, 2);
$spartnerMemory = round(($m1 - $m0) / 1024, 2);
$spartnerQueryCount = count($queriesSpartner);

// 2. Measure recommendedData
$req = Request::create(route('service_company.jobs.recommendedData'), 'GET', [
    'jobid' => 8,
    '_force' => 1
]);
$t2 = microtime(true);
$m2 = memory_get_usage();
$responseRecommended = $controller->recommendedData($req);
$t3 = microtime(true);
$m3 = memory_get_usage();
$queriesRecommended = DB::getQueryLog();

$recommendedDuration = round(($t3 - $t2) * 1000, 2);
$recommendedMemory = round(($m3 - $m2) / 1024, 2);
$recommendedQueryCount = count($queriesRecommended);

echo json_encode([
    'spartnerlisting' => [
        'duration_ms' => $spartnerDuration,
        'memory_kb' => $spartnerMemory,
        'mysql_queries_count' => $spartnerQueryCount,
        'queries' => array_map(fn($q) => [
            'sql' => $q['query'],
            'time' => $q['time']
        ], $queriesSpartner)
    ],
    'recommendedData' => [
        'duration_ms' => $recommendedDuration,
        'memory_kb' => $recommendedMemory,
        'mysql_queries_count' => $recommendedQueryCount,
        'queries' => array_map(fn($q) => [
            'sql' => $q['query'],
            'time' => $q['time']
        ], $queriesRecommended)
    ]
], JSON_PRETTY_PRINT);
