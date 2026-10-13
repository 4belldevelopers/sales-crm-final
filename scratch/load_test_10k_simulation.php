<?php
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\ServiceCompany\ServiceCompanyJobsController;

$user = DB::table('users')->where('company_id', '>', 0)->first();
$userModel = \App\Models\User::find($user->id);
Auth::login($userModel);

$job = DB::table('jobs')->where('company_id', $user->company_id)->first();
if (!$job) {
    $job = DB::table('jobs')->first();
}

$controller = app(ServiceCompanyJobsController::class);
$controller->user = $userModel;

echo "===============================================================\n";
echo "   10K CONCURRENCY BURST SIMULATION: ADD AGENCY DRAWER\n";
echo "===============================================================\n";
echo "User: ID {$user->id} | Company: ID {$user->company_id} | Job: ID {$job->id}\n\n";

// Pre-warm the cache first
$warmReq = Request::create("/service_company/jobs/recommendedData", 'GET', [
    'page' => 1,
    'jobid' => $job->id,
    'size' => 20
]);
app()->instance('request', $warmReq);
$warmRes = $controller->recommendedData($warmReq);
$etag = $warmRes->headers->get('ETag');

// Run 200 consecutive / burst requests representing peak concurrent burst throughput
$iterations = 200;
$durations = [];
$statusCodes = [];
$queriesTotal = 0;

echo "Simulating 200 high-frequency peak drawer requests (80% warm/304, 20% fresh/cold)...\n";
$startTotal = microtime(true);

for ($i = 1; $i <= $iterations; $i++) {
    DB::flushQueryLog();
    DB::enableQueryLog();
    $startReq = microtime(true);

    $is304 = ($i % 5 !== 0); // 80% warm (ETag / conditional 304), 20% fresh
    $page = ($i % 3) + 1;

    $req = Request::create("/service_company/jobs/recommendedData", 'GET', [
        'page' => $page,
        'jobid' => $job->id,
        'size' => 20
    ]);

    if ($is304) {
        $req->headers->set('If-None-Match', $etag);
    }

    app()->instance('request', $req);
    $res = $controller->recommendedData($req);

    $dur = (microtime(true) - $startReq) * 1000;
    $durations[] = $dur;
    $statusCodes[$res->getStatusCode()] = ($statusCodes[$res->getStatusCode()] ?? 0) + 1;

    $qLog = DB::getQueryLog();
    DB::disableQueryLog();
    $queriesTotal += count($qLog);
}

$totalWallTime = microtime(true) - $startTotal;
sort($durations);

$count = count($durations);
$avg = array_sum($durations) / $count;
$min = $durations[0];
$max = $durations[$count - 1];
$p50 = $durations[(int)($count * 0.50)];
$p95 = $durations[(int)($count * 0.95)];
$p99 = $durations[(int)($count * 0.99)];
$rps = $iterations / $totalWallTime;

echo "\n--- SIMULATION RESULTS ---\n";
echo "Total Requests:          {$iterations}\n";
echo "Total Wall Time:         " . round($totalWallTime * 1000, 2) . " ms (" . round($totalWallTime, 3) . " s)\n";
echo "Throughput:              " . round($rps, 2) . " Requests / Sec (single-process CPU execution)\n";
echo "Average Latency:         " . round($avg, 2) . " ms\n";
echo "Min Latency:             " . round($min, 2) . " ms\n";
echo "Median (p50):            " . round($p50, 2) . " ms\n";
echo "95th Percentile (p95):   " . round($p95, 2) . " ms\n";
echo "99th Percentile (p99):   " . round($p99, 2) . " ms\n";
echo "Max Latency:             " . round($max, 2) . " ms\n";
echo "Total MySQL Queries:     {$queriesTotal} across {$iterations} requests (Avg " . round($queriesTotal / $iterations, 2) . " queries/req)\n";
echo "Status Code Distribution:\n";
foreach ($statusCodes as $code => $cnt) {
    echo "  HTTP {$code}: {$cnt} requests (" . round(($cnt / $iterations) * 100, 1) . "%)\n";
}

echo "\n--- 10K CONCURRENCY PROJECTION ---\n";
$peakBurstRps = 412.5; // Peak burst for 10K users
$workersRequired = ($peakBurstRps * ($avg / 1000));
$dbQpsAtPeak = $peakBurstRps * ($queriesTotal / $iterations);

echo "Peak Concurrency Target: 10,000 active users (Peak Burst = 412.5 RPS)\n";
echo "Required App Workers:    " . round($workersRequired, 1) . " workers (standard Octane pool is 64–128)\n";
echo "Projected Peak MySQL QPS: " . round($dbQpsAtPeak, 1) . " QPS (dedicated 32GB MySQL capacity is 10,000+ QPS)\n";
echo "Health Margin:           " . round((128 / max($workersRequired, 1)), 1) . "x worker headroom, " . round((10000 / max($dbQpsAtPeak, 1)), 1) . "x database headroom\n";

// Secondary test: 100% Uncached Cold Requests (Worst-Case Burst)
echo "\n--- WORST-CASE SIMULATION: 50 UNCACHED COLD REQUESTS ---\n";
$coldDurations = [];
$coldQueriesTotal = 0;
for ($c = 1; $c <= 50; $c++) {
    cache()->forget('ats:job_summary_meta:' . $job->id);
    // Unique cache key to force full Elasticsearch scan & Blade render
    $reqCold = Request::create("/service_company/jobs/recommendedData", 'GET', [
        'page' => 1,
        'jobid' => $job->id,
        'size' => 20,
        'unique_burst' => $c
    ]);
    DB::flushQueryLog();
    DB::enableQueryLog();
    $startCold = microtime(true);
    app()->instance('request', $reqCold);
    $resCold = $controller->recommendedData($reqCold);
    $durCold = (microtime(true) - $startCold) * 1000;
    $coldDurations[] = $durCold;
    $qCold = DB::getQueryLog();
    DB::disableQueryLog();
    $coldQueriesTotal += count($qCold);
}
sort($coldDurations);
$coldAvg = array_sum($coldDurations) / count($coldDurations);
$coldP95 = $coldDurations[(int)(count($coldDurations) * 0.95)];
$coldWorkers = ($peakBurstRps * ($coldAvg / 1000));
$coldQps = $peakBurstRps * ($coldQueriesTotal / 50);

echo "Average Cold Latency:    " . round($coldAvg, 2) . " ms\n";
echo "Median Cold Latency:     " . round($coldDurations[25], 2) . " ms\n";
echo "p95 Cold Latency:        " . round($coldP95, 2) . " ms\n";
echo "Queries per Cold Req:    " . round($coldQueriesTotal / 50, 1) . " queries\n";
echo "Workers at 100% Cold Burst: " . round($coldWorkers, 1) . " workers (out of 128 available)\n";
echo "MySQL QPS at 100% Cold:  " . round($coldQps, 1) . " QPS (out of 10,000+ available)\n";
echo "Worst-Case Safety:       " . round((128 / max($coldWorkers, 1)), 1) . "x worker headroom, " . round((10000 / max($coldQps, 1)), 1) . "x DB headroom\n";
echo "===============================================================\n";
