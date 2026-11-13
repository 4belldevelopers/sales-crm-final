<?php

/**
 * Super Admin Company Management - Phase 5.1
 * Real HTTP Multi-Socket Concurrency Benchmark (With Cached Config & MySQL Fallback)
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use Illuminate\Support\Facades\Auth;

echo "================================================================================\n";
echo "   PHASE 5.1: REAL HTTP MULTI-SOCKET CONCURRENCY (AUTHENTICATED SESSION)\n";
echo "================================================================================\n\n";

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
    $baseUrl . '/super-admin/company-list?page=1&size=10&status=all',
    $baseUrl . '/super-admin/company-list?page=1&size=10&status=active',
    $baseUrl . '/super-admin/company-list?page=1&size=10&account_verify=1',
];

function runHttpSocketBatch(array $endpoints, string $cookieHeader, int $totalBatchRequests, int $concurrencySockets): array
{
    echo "  Executing Batch: {$totalBatchRequests} Requests @ {$concurrencySockets} Simultaneous Connections...\n";

    $mh = curl_multi_init();
    $active = [];
    $latencies = [];
    $statusCodes = [];
    $reqIndex = 0;
    $endpointCount = count($endpoints);

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
            CURLOPT_TIMEOUT => 45,
            CURLOPT_CONNECTTIMEOUT => 15,
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
    ];

    echo sprintf("    Completed %d requests in %.3f s (%.1f req/s) | p50: %.2f ms | p95: %.2f ms | Status: %s\n",
        $res['total_requests'], $res['duration_sec'], $res['rps'], $res['p50_ms'], $res['p95_ms'], json_encode($res['status_codes']));

    return $res;
}

$httpBenchmarkResults = [
    'batch_10_sockets' => runHttpSocketBatch($httpEndpoints, $cookieHeader, 30, 10),
    'batch_25_sockets' => runHttpSocketBatch($httpEndpoints, $cookieHeader, 50, 25),
    'batch_50_sockets' => runHttpSocketBatch($httpEndpoints, $cookieHeader, 100, 50),
];

$jsonPath = dirname(__DIR__) . '/docs/audit documents/superadmin_company/phase5/http_concurrency_evidence.json';
file_put_contents($jsonPath, json_encode([
    'timestamp' => now()->toIso8601String(),
    'batches' => $httpBenchmarkResults,
], JSON_PRETTY_PRINT));

echo "\nHTTP Concurrency Evidence Persisted to: {$jsonPath}\n";
