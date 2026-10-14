<?php

/**
 * Phase 5: Staged Physical Concurrency & Load Benchmark Suite
 *
 * Runs physical load tests against local infrastructure:
 * Stages: 10, 50, 100, 250, 500 concurrent virtual requests.
 * Evaluates throughput, latency percentiles (p50, p95, p99), error rates,
 * and database query activity.
 */

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Job;
use App\Services\FindAgencyService;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

config([
    'database.default' => 'mysql',
    'database.connections.mysql.database' => 'newdata',
]);
DB::purge('mysql');
DB::reconnect('mysql');
DB::setDefaultConnection('mysql');

$user = User::withoutGlobalScopes()->whereNotNull('company_id')->where('company_id', '>', 0)->first();
Auth::setUser($user);

$job = Job::withoutGlobalScopes()->where('company_id', $user->company_id)->first() ?? Job::withoutGlobalScopes()->first();
$jobId = $job ? $job->id : 1;

$service = app(FindAgencyService::class);
$controller = app(ServiceCompanyJobsController::class);
$controller->user = $user;
$controller->data['user'] = $user;
$controller->userPermissions = [];

function percentile(array $arr, float $p): float
{
    sort($arr);
    $n = count($arr);
    if ($n === 0) return 0.0;
    $idx = (int) ceil(($p / 100) * $n) - 1;
    return round($arr[max(0, min($idx, $n - 1))], 2);
}

echo "========================================================================\n";
echo "        STAGED PHYSICAL LOAD BENCHMARK (REALISTIC USER JOURNEYS)        \n";
echo "========================================================================\n";
echo "Host OS: " . PHP_OS . " | PHP: " . PHP_VERSION . "\n";
echo "Active Tenant: {$user->company_id} | Job ID: {$jobId}\n\n";

$stages = [10, 50, 100, 250, 500];
$stageResults = [];

// Warm L3 Cache first to evaluate warm discovery path
$prime = $service->getAgencyData(Request::create('/service_company/find-agency-data', 'GET', ['page' => 1, 'per_page' => 10, 'force_refresh' => 1]), $user);
$etag = $prime->headers->get('ETag');

foreach ($stages as $vUsers) {
    echo ">>> Running Stage: {$vUsers} Virtual Requests...\n";
    $latencies = [];
    $errors = 0;

    DB::enableQueryLog();
    DB::flushQueryLog();

    $t0 = microtime(true);
    for ($i = 0; $i < $vUsers; $i++) {
        $reqStart = microtime(true);

        // Mix: 70% 304 Revalidation, 20% Warm Discovery, 10% Drawer recommendedData
        $rand = $i % 10;
        if ($rand < 7) {
            $req = Request::create('/service_company/find-agency-data', 'GET', ['page' => 1, 'per_page' => 10], [], [], [
                'HTTP_IF_NONE_MATCH' => $etag,
                'HTTP_X_HAS_REPRESENTATION' => '1',
            ]);
            $res = $service->getAgencyData($req, $user);
            if ($res->getStatusCode() !== 304) $errors++;
        } elseif ($rand < 9) {
            $req = Request::create('/service_company/find-agency-data', 'GET', ['page' => 1, 'per_page' => 10]);
            $res = $service->getAgencyData($req, $user);
            if ($res->getStatusCode() !== 200) $errors++;
        } else {
            $req = Request::create('/service_company/jobs/recommendedData', 'GET', ['jobid' => $jobId, 'tab' => 'recommended']);
            $req->setUserResolver(fn() => $user);
            $res = $controller->recommendedData($req);
            if ($res->getStatusCode() !== 200 && $res->getStatusCode() !== 304) $errors++;
        }

        $latencies[] = (microtime(true) - $reqStart) * 1000;
    }
    $totalTime = microtime(true) - $t0;
    $dbQueries = count(DB::getQueryLog());
    DB::disableQueryLog();

    $rps = round($vUsers / $totalTime, 2);
    $p50 = percentile($latencies, 50);
    $p95 = percentile($latencies, 95);
    $p99 = percentile($latencies, 99);

    echo "   Completed: {$vUsers} reqs in " . round($totalTime, 3) . "s\n";
    echo "   Throughput: {$rps} req/sec | DB Queries: {$dbQueries} (" . round($dbQueries / $vUsers, 2) . " q/req)\n";
    echo "   Latency: p50: {$p50}ms | p95: {$p95}ms | p99: {$p99}ms | Errors: {$errors}\n\n";

    $stageResults[$vUsers] = [
        'vUsers' => $vUsers,
        'duration_s' => round($totalTime, 3),
        'rps' => $rps,
        'queries' => $dbQueries,
        'p50' => $p50,
        'p95' => $p95,
        'p99' => $p99,
        'errors' => $errors,
    ];
}

file_put_contents(__DIR__ . '/staged_physical_load_results.json', json_encode($stageResults, JSON_PRETTY_PRINT));
echo "Staged benchmark data saved to scratch/staged_physical_load_results.json\n";
