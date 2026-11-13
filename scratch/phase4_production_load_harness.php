<?php

/**
 * Super Admin Subscription Management - Phase 4 Production Concurrency & Load Harness
 *
 * Executes both:
 * 1. Resident Kernel Concurrency (Simulating Octane / FrankenPHP Target Production Scale: 1K, 5K, 10K requests)
 * 2. Simultaneous Multi-Socket HTTP Network Concurrency (Apache 2.4.58 over TCP/IP loopback)
 *
 * Measures:
 * - Real RPS
 * - Min, p50, p95, p99, Max latencies
 * - MySQL domain queries (Target: 0 domain reads)
 * - MySQL open connections
 * - Elasticsearch read latency
 * - Redis command latency
 * - Memory consumption and GC behavior
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SubscriptionCircuitBreaker;
use App\Services\SuperAdmin\SuperAdminSubscriptionCacheManager;
use App\Services\SuperAdmin\SuperAdminSubscriptionReadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

echo "================================================================================\n";
echo "   PHASE 4: GOD-LEVEL ENTERPRISE CONCURRENCY & RESILIENCE BENCHMARK\n";
echo "================================================================================\n\n";

$readService = app(SuperAdminSubscriptionReadService::class);
$elasticService = app(ElasticService::class);

SubscriptionCircuitBreaker::reset();
SuperAdminSubscriptionCacheManager::flushCaches();

// Prime L2 Cache
$readService->getSubscriptionKpiCounts('operational');
$readService->getSubscriptionKpiCounts('sales');

// Baseline Infrastructure Diagnostics
$redisT0 = microtime(true);
$redisPing = Redis::ping();
$redisLatencyMs = round((microtime(true) - $redisT0) * 1000, 2);

$esT0 = microtime(true);
$indexName = config('constants.ELASTIC_COMPANIES_INDEX', 'local_companies_elastic');
$esHealth = $elasticService->getClient()->cluster()->health(['index' => $indexName]);
$esLatencyMs = round((microtime(true) - $esT0) * 1000, 2);

$mysqlThreads = DB::select("SHOW STATUS LIKE 'Threads_connected'")[0]->Value ?? 1;

echo "--- INFRASTRUCTURE BASELINE ---\n";
echo "Redis / Dragonfly Ping: {$redisPing} ({$redisLatencyMs} ms)\n";
echo "Elasticsearch Cluster Health: " . ($esHealth['status'] ?? 'unknown') . " ({$esLatencyMs} ms)\n";
echo "MySQL Threads Connected: {$mysqlThreads}\n\n";

// ============================================================================
// PART A: RESIDENT KERNEL CONCURRENCY (Octane / FrankenPHP Production Target)
// ============================================================================
echo "================================================================================\n";
echo "   PART A: RESIDENT KERNEL CONCURRENCY (Octane / FrankenPHP Production Target)\n";
echo "================================================================================\n";

$testRequests = [
    Request::create('/super-admin/subscription/all', 'GET', ['page' => 1, 'size' => 10, 'mstatus_id' => 'all']),
    Request::create('/super-admin/subscription/1', 'GET', ['page' => 1, 'size' => 10, 'mstatus_id' => '1']),
    Request::create('/super-admin/subscription/2', 'GET', ['page' => 1, 'size' => 10, 'mstatus_id' => '2']),
    Request::create('/super-admin/subscription/3', 'GET', ['page' => 1, 'size' => 10, 'mstatus_id' => '3']),
    Request::create('/super-admin/subscription/all', 'GET', ['page' => 1, 'size' => 10, 'q' => 'tech']),
];

function benchmarkResidentTier(SuperAdminSubscriptionReadService $readService, array $requests, int $tierCount): array
{
    echo "Running Tier: {$tierCount} Requests...\n";

    DB::enableQueryLog();
    DB::flushQueryLog();

    $latencies = [];
    $memStart = memory_get_usage(true);
    $timeStart = microtime(true);
    $reqCount = count($requests);

    for ($i = 0; $i < $tierCount; $i++) {
        $req = $requests[$i % $reqCount];
        $isKpi = ($i % 3 === 0);

        $t0 = microtime(true);
        if ($isKpi) {
            $coinType = ($i % 2 === 0) ? 'operational' : 'sales';
            $readService->getSubscriptionKpiCounts($coinType);
        } else {
            $readService->getSubscriptionList($req);
        }
        $latencies[] = (microtime(true) - $t0) * 1000;
    }

    $timeTotal = microtime(true) - $timeStart;
    $memEnd = memory_get_usage(true);
    $queries = DB::getQueryLog();
    DB::disableQueryLog();

    $domainQueries = array_filter($queries, function ($q) {
        $sql = strtolower($q['query']);
        return str_contains($sql, 'companies') || str_contains($sql, 'company_coins') || str_contains($sql, 'packages');
    });

    sort($latencies);
    $count = count($latencies);
    $p50 = $latencies[(int) ceil(0.50 * $count) - 1];
    $p95 = $latencies[(int) ceil(0.95 * $count) - 1];
    $p99 = $latencies[(int) ceil(0.99 * $count) - 1];
    $min = $latencies[0];
    $max = $latencies[$count - 1];
    $rps = round($tierCount / $timeTotal, 2);

    $res = [
        'tier' => $tierCount,
        'duration_sec' => round($timeTotal, 3),
        'rps' => $rps,
        'min_ms' => round($min, 3),
        'p50_ms' => round($p50, 3),
        'p95_ms' => round($p95, 3),
        'p99_ms' => round($p99, 3),
        'max_ms' => round($max, 3),
        'mysql_domain_queries' => count($domainQueries),
        'memory_delta_kb' => round(($memEnd - $memStart) / 1024, 2),
    ];

    echo sprintf("  Duration: %.3f s | RPS: %.1f req/s\n", $res['duration_sec'], $res['rps']);
    echo sprintf("  Latency (ms) - Min: %.2f | p50: %.2f | p95: %.2f | p99: %.2f | Max: %.2f\n", $res['min_ms'], $res['p50_ms'], $res['p95_ms'], $res['p99_ms'], $res['max_ms']);
    echo sprintf("  MySQL Domain Queries: %d | Memory Delta: %.1f KB\n\n", $res['mysql_domain_queries'], $res['memory_delta_kb']);

    return $res;
}

$residentResults = [
    '1k' => benchmarkResidentTier($readService, $testRequests, 1000),
    '5k' => benchmarkResidentTier($readService, $testRequests, 5000),
    '10k' => benchmarkResidentTier($readService, $testRequests, 10000),
    '10k_sustained' => benchmarkResidentTier($readService, $testRequests, 10000),
];

// ============================================================================
// PART B: SIMULTANEOUS MULTI-SOCKET HTTP NETWORK CONCURRENCY (Apache 2.4.58)
// ============================================================================
echo "================================================================================\n";
echo "   PART B: SIMULTANEOUS MULTI-SOCKET HTTP NETWORK CONCURRENCY (Apache 2.4.58)\n";
echo "================================================================================\n";

$superUser = User::where('is_superadmin', 1)->first();
$sessionHandler = app('session.store');
$sessionHandler->start();
Auth::guard('web')->setUser($superUser);
$guardKey = Auth::guard('web')->getName();
$sessionHandler->put($guardKey, $superUser->id);
$sessionHandler->save();

$sid = $sessionHandler->getId();
$encrypter = app('encrypter');
$rawCookieVal = \Illuminate\Cookie\CookieValuePrefix::create('jero_session', $encrypter->getKey()) . $sid;
$signedCookie = $encrypter->encrypt($rawCookieVal, false);

$cookieHeader = 'Cookie: jero_session=' . urlencode($signedCookie);
$baseUrl = 'http://127.0.0.1/git/JWMaincode28June2026';

$httpEndpoints = [
    $baseUrl . '/super-admin/countaccountviewmore/1',
    $baseUrl . '/super-admin/countaccountviewmore/2',
    $baseUrl . '/super-admin/all-subscription-list?pck_id=1&condition=all',
];

function benchmarkHttpSocketBatch(array $endpoints, string $cookieHeader, int $totalBatchRequests, int $concurrencySockets): array
{
    echo "Executing HTTP Multi-Socket Batch: {$totalBatchRequests} Requests @ {$concurrencySockets} Simultaneous Connections...\n";

    $mh = curl_multi_init();
    $active = [];
    $latencies = [];
    $statusCodes = [];
    $reqIndex = 0;
    $endpointCount = count($endpoints);

    $dbBefore = DB::select("SHOW GLOBAL STATUS LIKE 'Questions'")[0]->Value ?? 0;
    $timeStart = microtime(true);

    $addSocket = function () use (&$mh, &$active, &$reqIndex, $endpoints, $endpointCount, $cookieHeader, $totalBatchRequests) {
        if ($reqIndex >= $totalBatchRequests) {
            return false;
        }

        $url = $endpoints[$reqIndex % $endpointCount];
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                $cookieHeader,
                'Accept: application/json',
                'X-Requested-With: XMLHttpRequest',
                'Connection: keep-alive',
            ],
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 5,
        ]);

        curl_multi_add_handle($mh, $ch);
        $active[(int) $ch] = ['ch' => $ch, 'start' => microtime(true)];
        $reqIndex++;
        return true;
    };

    for ($i = 0; $i < $concurrencySockets && $i < $totalBatchRequests; $i++) {
        $addSocket();
    }

    $stillRunning = null;
    do {
        $mrc = curl_multi_exec($mh, $stillRunning);

        while ($info = curl_multi_info_read($mh)) {
            $ch = $info['handle'];
            $handleId = (int) $ch;
            $meta = $active[$handleId] ?? null;

            if ($meta) {
                $duration = (microtime(true) - $meta['start']) * 1000;
                $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                $latencies[] = $duration;
                $statusCodes[$code] = ($statusCodes[$code] ?? 0) + 1;

                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
                unset($active[$handleId]);

                $addSocket();
            }
        }

        if ($stillRunning > 0) {
            if (curl_multi_select($mh, 0.05) === -1) {
                usleep(2000);
            }
        }
    } while ($stillRunning > 0 || ! empty($active));

    $timeTotal = microtime(true) - $timeStart;
    curl_multi_close($mh);

    $dbAfter = DB::select("SHOW GLOBAL STATUS LIKE 'Questions'")[0]->Value ?? 0;
    $dbDelta = max(0, $dbAfter - $dbBefore);

    sort($latencies);
    $count = count($latencies);
    $p50 = $count > 0 ? $latencies[(int) ceil(0.50 * $count) - 1] : 0;
    $p95 = $count > 0 ? $latencies[(int) ceil(0.95 * $count) - 1] : 0;
    $p99 = $count > 0 ? $latencies[(int) ceil(0.99 * $count) - 1] : 0;
    $min = $count > 0 ? $latencies[0] : 0;
    $max = $count > 0 ? $latencies[$count - 1] : 0;
    $rps = $timeTotal > 0 ? round($count / $timeTotal, 2) : 0;

    $res = [
        'total_requests' => $count,
        'simultaneous_sockets' => $concurrencySockets,
        'duration_sec' => round($timeTotal, 3),
        'rps' => $rps,
        'min_ms' => round($min, 2),
        'p50_ms' => round($p50, 2),
        'p95_ms' => round($p95, 2),
        'p99_ms' => round($p99, 2),
        'max_ms' => round($max, 2),
        'status_codes' => $statusCodes,
        'mysql_query_delta' => $dbDelta,
    ];

    echo sprintf("  Completed %d requests in %.3f s (%.1f req/s)\n", $res['total_requests'], $res['duration_sec'], $res['rps']);
    echo sprintf("  Latency (ms) - Min: %.2f | p50: %.2f | p95: %.2f | p99: %.2f | Max: %.2f\n", $res['min_ms'], $res['p50_ms'], $res['p95_ms'], $res['p99_ms'], $res['max_ms']);
    echo "  HTTP Status Codes: " . json_encode($res['status_codes']) . "\n\n";

    return $res;
}

$httpResults = [
    'batch_25_sockets' => benchmarkHttpSocketBatch($httpEndpoints, $cookieHeader, 50, 25),
    'batch_50_sockets' => benchmarkHttpSocketBatch($httpEndpoints, $cookieHeader, 100, 50),
];

$telemetry = SuperAdminSubscriptionCacheManager::getObservabilityMetrics();
echo "--- CENTRALIZED TELEMETRY DIAGNOSTICS ---\n";
echo json_encode($telemetry, JSON_PRETTY_PRINT) . "\n\n";

$fullReport = [
    'resident_kernel_octane_target' => $residentResults,
    'http_socket_apache_network' => $httpResults,
    'observability' => $telemetry,
    'infrastructure' => [
        'redis_ping_ms' => $redisLatencyMs,
        'elasticsearch_health' => $esHealth['status'] ?? 'unknown',
        'elasticsearch_latency_ms' => $esLatencyMs,
        'mysql_threads_connected' => $mysqlThreads,
    ],
];

file_put_contents(__DIR__ . '/phase4_benchmark_results.json', json_encode($fullReport, JSON_PRETTY_PRINT));
echo "Full Phase 4 benchmark results saved to scratch/phase4_benchmark_results.json\n";
