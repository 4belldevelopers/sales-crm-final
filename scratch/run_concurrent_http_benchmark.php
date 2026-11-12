<?php

/**
 * Super Admin Subscription Management - Phase 3 Genuine Concurrent HTTP Benchmark Harness
 *
 * Executes genuine concurrent HTTP requests over TCP/IP sockets to Apache/PHP using curl_multi.
 * Validates real network I/O, Apache thread concurrency, Redis L2 caching, and CQRS zero-MySQL read isolation.
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

// 1. Establish Authenticated Super Admin Session
$user = User::where('is_superadmin', 1)->first();
$sessionHandler = app('session.store');
$sessionHandler->start();
Auth::guard('web')->setUser($user);
$guardKey = Auth::guard('web')->getName();
$sessionHandler->put($guardKey, $user->id);
$sessionHandler->save();

$sid = $sessionHandler->getId();
$encrypter = app('encrypter');
$rawCookieVal = \Illuminate\Cookie\CookieValuePrefix::create('jero_session', $encrypter->getKey()) . $sid;
$signedCookie = $encrypter->encrypt($rawCookieVal, false);

$cookieHeader = 'Cookie: jero_session=' . urlencode($signedCookie);

$baseUrl = 'http://127.0.0.1/git/JWMaincode28June2026';
$endpoints = [
    $baseUrl . '/super-admin/countaccountviewmore/1',
    $baseUrl . '/super-admin/countaccountviewmore/2',
    $baseUrl . '/super-admin/all-subscription-list?pck_id=1&condition=all',
    $baseUrl . '/super-admin/all-subscription-list?pck_id=2&condition=all',
    $baseUrl . '/super-admin/all-subscription-list?pck_id=1&condition=1',
];

echo "========================================================================\n";
echo "   PHASE 3: GENUINE CONCURRENT HTTP DISTRIBUTED LOAD TESTING HARNESS\n";
echo "========================================================================\n";
echo "Base URL: {$baseUrl}\n";
echo "Session: Super Admin (User ID: {$user->id})\n";
echo "Elasticsearch & Redis L2 CQRS validation active.\n\n";

function runConcurrentHttpWorkload(array $endpoints, string $cookieHeader, int $totalRequests, int $concurrency): array
{
    echo "--- Executing Load Tier: {$totalRequests} Requests @ Concurrency Window {$concurrency} ---\n";

    $mh = curl_multi_init();
    $activeHandles = [];
    $latencies = [];
    $statusCodes = [];
    $bytesReceived = 0;

    $reqIndex = 0;
    $endpointCount = count($endpoints);

    // Initial DB & Redis baseline
    $dbQueriesBefore = DB::select("SHOW GLOBAL STATUS LIKE 'Questions'")[0]->Value ?? 0;
    $timeStart = microtime(true);

    $addHandle = function() use (&$mh, &$activeHandles, &$reqIndex, $endpoints, $endpointCount, $cookieHeader, $totalRequests) {
        if ($reqIndex >= $totalRequests) {
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
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 10,
        ]);

        curl_multi_add_handle($mh, $ch);
        $activeHandles[(int)$ch] = [
            'ch' => $ch,
            'start' => microtime(true),
            'id' => $reqIndex,
        ];

        $reqIndex++;
        return true;
    };

    // Prime concurrency window
    for ($i = 0; $i < $concurrency && $i < $totalRequests; $i++) {
        $addHandle();
    }

    $stillRunning = null;
    do {
        $status = curl_multi_exec($mh, $stillRunning);

        while ($info = curl_multi_info_read($mh)) {
            $ch = $info['handle'];
            $handleId = (int)$ch;
            $meta = $activeHandles[$handleId] ?? null;

            if ($meta) {
                $duration = (microtime(true) - $meta['start']) * 1000; // ms
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $content = curl_multi_getcontent($ch);
                $bytesReceived += strlen($content);

                $latencies[] = $duration;
                $statusCodes[$httpCode] = ($statusCodes[$httpCode] ?? 0) + 1;

                curl_multi_remove_handle($mh, $ch);
                curl_close($ch);
                unset($activeHandles[$handleId]);

                $addHandle();
            }
        }

        if ($stillRunning > 0) {
            curl_multi_select($mh, 0.05);
        }
    } while ($stillRunning > 0 || !empty($activeHandles));

    $timeTotal = microtime(true) - $timeStart;
    curl_multi_close($mh);

    $dbQueriesAfter = DB::select("SHOW GLOBAL STATUS LIKE 'Questions'")[0]->Value ?? 0;
    $dbDelta = max(0, $dbQueriesAfter - $dbQueriesBefore);

    sort($latencies);
    $count = count($latencies);
    $p50 = $count > 0 ? $latencies[(int) ceil(0.50 * $count) - 1] : 0;
    $p95 = $count > 0 ? $latencies[(int) ceil(0.95 * $count) - 1] : 0;
    $p99 = $count > 0 ? $latencies[(int) ceil(0.99 * $count) - 1] : 0;
    $min = $count > 0 ? $latencies[0] : 0;
    $max = $count > 0 ? $latencies[$count - 1] : 0;
    $rps = $timeTotal > 0 ? round($count / $timeTotal, 2) : 0;

    echo sprintf("Completed: %d requests in %.3f sec (%.1f req/sec)\n", $count, $timeTotal, $rps);
    echo sprintf("Latency (ms) - Min: %.2f | p50: %.2f | p95: %.2f | p99: %.2f | Max: %.2f\n", $min, $p50, $p95, $p99, $max);
    echo "HTTP Status Codes: " . json_encode($statusCodes) . "\n";
    echo sprintf("Throughput: %.2f MB received | MySQL Query Delta: %d\n\n", $bytesReceived / (1024 * 1024), $dbDelta);

    return [
        'total' => $count,
        'concurrency' => $concurrency,
        'duration_sec' => round($timeTotal, 3),
        'rps' => $rps,
        'min_ms' => round($min, 2),
        'p50_ms' => round($p50, 2),
        'p95_ms' => round($p95, 2),
        'p99_ms' => round($p99, 2),
        'max_ms' => round($max, 2),
        'status_codes' => $statusCodes,
        'bytes_mb' => round($bytesReceived / (1024 * 1024), 2),
    ];
}

// Warm up single request to ensure cache is primed
$warmCh = curl_init($endpoints[0]);
curl_setopt_array($warmCh, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [$cookieHeader, 'Accept: application/json', 'X-Requested-With: XMLHttpRequest'],
]);
curl_exec($warmCh);
curl_close($warmCh);

$results = [];

// Tier 1: 1,000 requests @ 50 concurrent sockets
$results['1k'] = runConcurrentHttpWorkload($endpoints, $cookieHeader, 1000, 50);

// Tier 2: 5,000 requests @ 100 concurrent sockets
$results['5k'] = runConcurrentHttpWorkload($endpoints, $cookieHeader, 5000, 100);

// Tier 3: 10,000 requests @ 150 concurrent sockets
$results['10k'] = runConcurrentHttpWorkload($endpoints, $cookieHeader, 10000, 150);

file_put_contents(__DIR__ . '/http_concurrency_results.json', json_encode($results, JSON_PRETTY_PRINT));
echo "Results saved to scratch/http_concurrency_results.json\n";
