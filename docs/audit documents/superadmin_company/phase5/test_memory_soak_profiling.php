<?php

/**
 * Super Admin Company Management - Phase 5.1
 * Forensic Memory Soak Profiling & Heap Stability Analysis
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Services\SuperAdmin\SuperAdminCompanyCacheManager;
use App\Services\SuperAdmin\SuperAdminCompanyReadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

echo "================================================================================\n";
echo "   PHASE 5.1: FORENSIC MEMORY & HEAP STABILITY SOAK TEST (HIGH-ITERATION)\n";
echo "================================================================================\n\n";

$readService = app(SuperAdminCompanyReadService::class);
gc_collect_cycles();
gc_enable();

$iterations = 6000;
$checkInterval = 1000;

$snapshots = [];
$mem0Heap = memory_get_usage(false);
$mem0Alloc = memory_get_usage(true);
$peak0 = memory_get_peak_usage(false);

$redisInfo0 = Redis::info('memory');
$redisMem0 = $redisInfo0['used_memory'] ?? 0;

$t0 = microtime(true);

echo "Initial State:\n";
echo "  PHP Heap Memory: " . number_format($mem0Heap) . " bytes (" . round($mem0Heap / 1024 / 1024, 2) . " MB)\n";
echo "  OS Allocated Memory: " . number_format($mem0Alloc) . " bytes (" . round($mem0Alloc / 1024 / 1024, 2) . " MB)\n";
echo "  Redis Used Memory: " . number_format($redisMem0) . " bytes (" . round($redisMem0 / 1024 / 1024, 2) . " MB)\n\n";

$snapshots[] = [
    'iteration' => 0,
    'elapsed_sec' => 0,
    'heap_bytes' => $mem0Heap,
    'os_allocated_bytes' => $mem0Alloc,
    'peak_heap_bytes' => $peak0,
    'redis_used_bytes' => (int) $redisMem0,
    'gc_runs' => gc_status()['runs'] ?? 0,
    'gc_collected' => gc_status()['collected'] ?? 0,
];

$testRequests = [
    Request::create('/super-admin/company-list', 'GET', ['page' => 1, 'size' => 10, 'status' => 'all']),
    Request::create('/super-admin/company-list', 'GET', ['page' => 1, 'size' => 10, 'status' => 'active', 'account_verify' => '1']),
    Request::create('/super-admin/company-list', 'GET', ['page' => 2, 'size' => 10, 'search' => ['value' => 'corp']]),
];

echo "Executing {$iterations} Soak Iterations across mixed read/search/KPI operations...\n";

for ($i = 1; $i <= $iterations; $i++) {
    // Mixed workload: 33% KPI counts, 67% Ajax company listings
    if ($i % 3 === 0) {
        $readService->getCompanyKpiCounts();
    } else {
        $req = $testRequests[$i % count($testRequests)];
        $readService->getCompanyListAjax($req);
    }

    if ($i % $checkInterval === 0) {
        $nowHeap = memory_get_usage(false);
        $nowAlloc = memory_get_usage(true);
        $nowPeak = memory_get_peak_usage(false);
        $nowSec = round(microtime(true) - $t0, 2);
        $nowRedis = (int) (Redis::info('memory')['used_memory'] ?? 0);
        $gc = gc_status();

        $snapshots[] = [
            'iteration' => $i,
            'elapsed_sec' => $nowSec,
            'heap_bytes' => $nowHeap,
            'os_allocated_bytes' => $nowAlloc,
            'peak_heap_bytes' => $nowPeak,
            'redis_used_bytes' => $nowRedis,
            'gc_runs' => $gc['runs'] ?? 0,
            'gc_collected' => $gc['collected'] ?? 0,
        ];

        $deltaHeapFromStart = $nowHeap - $mem0Heap;
        echo sprintf("  [%5d / %5d] Elapsed: %5.1fs | Heap: %10s bytes (Delta: %+6d bytes) | OS Alloc: %10s bytes | Peak: %10s bytes\n",
            $i, $iterations, $nowSec, number_format($nowHeap), $deltaHeapFromStart, number_format($nowAlloc), number_format($nowPeak));
    }
}

$totalDuration = microtime(true) - $t0;
$throughputQps = round($iterations / max($totalDuration, 0.001), 1);

$finalHeap = memory_get_usage(false);
$finalAlloc = memory_get_usage(true);
$finalPeak = memory_get_peak_usage(false);
$netHeapDelta = $finalHeap - $mem0Heap;
$leakSlopeBytesPerIter = round($netHeapDelta / $iterations, 4);

$postWarmupHeap = $snapshots[1]['heap_bytes'] ?? $mem0Heap;
$postWarmupIter = $snapshots[1]['iteration'] ?? 1;
$steadyStateIters = $iterations - $postWarmupIter;
$steadyStateDelta = $finalHeap - $postWarmupHeap;
$steadyStateSlope = ($steadyStateIters > 0) ? round($steadyStateDelta / $steadyStateIters, 4) : 0;

echo "\n================================================================================\n";
echo "   SOAK TEST RESULTS SUMMARY\n";
echo "================================================================================\n";
echo "Total Iterations: " . number_format($iterations) . "\n";
echo "Total Duration: " . round($totalDuration, 2) . " seconds (" . $throughputQps . " QPS)\n";
echo "Initial Cold Heap: " . number_format($mem0Heap) . " bytes\n";
echo "Post-Warmup Heap (Iter {$postWarmupIter}): " . number_format($postWarmupHeap) . " bytes\n";
echo "Final Heap:   " . number_format($finalHeap) . " bytes\n";
echo "Steady-State Heap Delta: " . sprintf("%+d", $steadyStateDelta) . " bytes (" . round($steadyStateDelta / 1024, 2) . " KB across {$steadyStateIters} requests)\n";
echo "Peak Heap Memory: " . number_format($finalPeak) . " bytes\n";
echo "Steady-State Growth Slope: " . $steadyStateSlope . " bytes/iteration (Includes telemetry snapshots)\n";

// A steady-state growth slope < 1.0 byte per iteration confirms bounded O(1) memory stability
$leakVerdict = ($steadyStateSlope < 1.0) ? "ZERO_LEAK_CONFIRMED" : "LEAK_SUSPECTED";
echo "Memory Stability Verdict: " . $leakVerdict . "\n";

$jsonPath = dirname(__DIR__) . '/docs/audit documents/superadmin_company/phase5/memory_soak_profiling_evidence.json';
file_put_contents($jsonPath, json_encode([
    'timestamp' => now()->toIso8601String(),
    'total_iterations' => $iterations,
    'duration_sec' => round($totalDuration, 3),
    'throughput_qps' => $throughputQps,
    'initial_cold_heap_bytes' => $mem0Heap,
    'post_warmup_heap_bytes' => $postWarmupHeap,
    'final_heap_bytes' => $finalHeap,
    'steady_state_heap_delta_bytes' => $steadyStateDelta,
    'steady_state_growth_slope_bytes_per_iteration' => $steadyStateSlope,
    'peak_heap_bytes' => $finalPeak,
    'verdict' => $leakVerdict,
    'snapshots' => $snapshots,
], JSON_PRETTY_PRINT));

echo "Soak Telemetry Persisted to: {$jsonPath}\n";
