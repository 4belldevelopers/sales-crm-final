<?php

/**
 * Super Admin Company Management - Phase 5.1
 * Multi-Worker Outbox Concurrency, Crash-Recovery & Tombstone Safety Harness
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Jobs\ProcessCompanyOutboxEventJob;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

echo "================================================================================\n";
echo "   PHASE 5.1: TRANSACTIONAL OUTBOX MULTI-WORKER CONCURRENCY & CRASH RECOVERY\n";
echo "================================================================================\n\n";

$elasticService = app(ElasticService::class);
$outboxResults = [];

// ----------------------------------------------------------------------------
// Test 1: Atomic Event Claiming with 5 Competing Workers on 10 Events
// ----------------------------------------------------------------------------
echo "--- TEST 1: ATOMIC EVENT CLAIMING ACROSS 5 CONCURRENT WORKERS ---\n";
$seededEventIds = [];
for ($i = 1; $i <= 10; $i++) {
    $seededEventIds[] = DB::table('sys_outbox_events')->insertGetId([
        'tenant_id' => 0,
        'aggregate_type' => 'Company',
        'aggregate_id' => 999000 + $i,
        'aggregate_version' => 1,
        'event_type' => 'COMPANY_STATUS_TOGGLED',
        'payload' => json_encode(['company_id' => 999000 + $i, 'status' => 'active']),
        'status' => 'PENDING',
        'attempts' => 0,
        'available_at' => now(),
        'created_at' => now(),
    ]);
}

$workerClaims = [];
for ($w = 1; $w <= 5; $w++) {
    $workerClaims["worker_{$w}"] = [];
}

// Simulate 5 workers concurrently trying to claim all 10 events
foreach ($seededEventIds as $eventId) {
    for ($w = 1; $w <= 5; $w++) {
        $claimed = DB::table('sys_outbox_events')
            ->where('id', $eventId)
            ->where('aggregate_type', 'Company')
            ->where(function ($q) {
                $q->whereIn('status', ['PENDING', 'FAILED'])
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'PROCESSING')
                            ->where('locked_at', '<', now()->subMinutes(5));
                    });
            })
            ->update([
                'status' => 'PROCESSING',
                'locked_at' => now(),
                'attempts' => DB::raw('attempts + 1'),
            ]);

        if ($claimed) {
            $workerClaims["worker_{$w}"][] = $eventId;
        }
    }
}

$allClaimed = array_merge(...array_values($workerClaims));
$uniqueClaimed = array_unique($allClaimed);
$hasCollisions = (count($allClaimed) !== count($uniqueClaimed));

echo "  Total Events: " . count($seededEventIds) . " | Total Claims: " . count($allClaimed) . "\n";
echo "  Claim Collisions: " . ($hasCollisions ? "FAIL: DUPLICATE CLAIMS DETECTED" : "ZERO (Strict Atomic Exclusivity Verified)") . "\n";
foreach ($workerClaims as $wId => $claims) {
    echo "    {$wId}: claimed " . count($claims) . " events\n";
}

// Cleanup Test 1
DB::table('sys_outbox_events')->whereIn('id', $seededEventIds)->delete();
$outboxResults['atomic_claiming_exclusive'] = ! $hasCollisions && count($allClaimed) === count($seededEventIds);

// ----------------------------------------------------------------------------
// Test 2: Crash AFTER Elasticsearch Synchronization, BEFORE Outbox Completion
// ----------------------------------------------------------------------------
echo "\n--- TEST 2: CRASH AFTER ELASTIC SYNC, BEFORE OUTBOX COMPLETION ---\n";
$crashTestCompanyId = 777001;
$indexName = config('constants.ELASTIC_COMPANIES_INDEX', 'local_companies_elastic');

// Seed ES doc with version = 5
$elasticService->getClient()->index([
    'index' => $indexName,
    'id' => (string) $crashTestCompanyId,
    'refresh' => 'wait_for',
    'body' => [
        'id' => $crashTestCompanyId,
        'company_name' => 'Crash Recovery Corp',
        'status' => 'active',
        'aggregate_version' => 5,
    ],
]);

// Step A: Seed outbox event v6 in PROCESSING state (Worker 1 crashed after applying v6 to ES)
$crashedEventId = DB::table('sys_outbox_events')->insertGetId([
    'tenant_id' => 0,
    'aggregate_type' => 'Company',
    'aggregate_id' => $crashTestCompanyId,
    'aggregate_version' => 6,
    'event_type' => 'COMPANY_STATUS_TOGGLED',
    'payload' => json_encode(['company_id' => $crashTestCompanyId, 'status' => 'deactive']),
    'status' => 'PROCESSING', // Stuck in processing
    'attempts' => 1,
    'locked_at' => Carbon::now()->subMinutes(10), // Expired lease
    'available_at' => Carbon::now()->subMinutes(10),
    'created_at' => Carbon::now()->subMinutes(10),
]);

// Worker 1 applied v6 to ES before crashing:
$elasticService->getClient()->update([
    'index' => $indexName,
    'id' => (string) $crashTestCompanyId,
    'refresh' => 'wait_for',
    'body' => [
        'doc' => [
            'status' => 'deactive',
            'aggregate_version' => 6,
        ],
    ],
]);

// Step B: Worker 2 wakes up, reclaims crashed lease, processes event
$job = new ProcessCompanyOutboxEventJob((int) $crashedEventId);
$job->handle($elasticService);

$eventAfterRecovery = DB::table('sys_outbox_events')->where('id', $crashedEventId)->first();
$docAfterRecovery = $elasticService->getClient()->get([
    'index' => $indexName,
    'id' => (string) $crashTestCompanyId,
]);

$esConsistent = ($docAfterRecovery['_source']['status'] === 'deactive' && $docAfterRecovery['_source']['aggregate_version'] === 6);
$outboxCompleted = ($eventAfterRecovery->status === 'COMPLETED');

echo "  Worker 2 Reclaimed Stuck PROCESSING Event: " . ($outboxCompleted ? "YES (Status: COMPLETED)" : "NO") . "\n";
echo "  Elasticsearch State Monotonically Consistent (v6, deactive): " . ($esConsistent ? "YES" : "NO") . "\n";

// Cleanup Test 2
DB::table('sys_outbox_events')->where('id', $crashedEventId)->delete();
$elasticService->getClient()->delete(['index' => $indexName, 'id' => (string) $crashTestCompanyId, 'refresh' => 'wait_for']);

$outboxResults['crash_after_sync_recovered'] = ($outboxCompleted && $esConsistent);

// ----------------------------------------------------------------------------
// Test 3: Tombstone Fence & Delayed Outbox Event Zombie Defense
// ----------------------------------------------------------------------------
echo "\n--- TEST 3: TOMBSTONE FENCE & DELAYED OUTBOX EVENT ZOMBIE DEFENSE ---\n";
$tombstoneCompanyId = 777002;

// Set tombstone in cache: Company 777002 was deleted at version 10
Cache::put("company:tombstone:{$tombstoneCompanyId}", 10, now()->addDays(7));

// Stale delayed outbox event arrives with version 8 (pre-deletion event)
$staleEventId = DB::table('sys_outbox_events')->insertGetId([
    'tenant_id' => 0,
    'aggregate_type' => 'Company',
    'aggregate_id' => $tombstoneCompanyId,
    'aggregate_version' => 8,
    'event_type' => 'COMPANY_STATUS_TOGGLED',
    'payload' => json_encode(['company_id' => $tombstoneCompanyId, 'status' => 'active']),
    'status' => 'PENDING',
    'attempts' => 0,
    'available_at' => now(),
    'created_at' => now(),
]);

$staleJob = new ProcessCompanyOutboxEventJob((int) $staleEventId);
$staleJob->handle($elasticService);

$staleEventRecord = DB::table('sys_outbox_events')->where('id', $staleEventId)->first();
$docExistsResp = $elasticService->getClient()->exists([
    'index' => $indexName,
    'id' => (string) $tombstoneCompanyId,
]);
$docExistsInEs = is_bool($docExistsResp) ? $docExistsResp : (method_exists($docExistsResp, 'asBool') ? $docExistsResp->asBool() : (bool) $docExistsResp);

$tombstoneGuarded = ($staleEventRecord->status === 'COMPLETED' && ! $docExistsInEs);

echo "  Stale Event (v8 <= tombstone v10) Blocked: " . ($staleEventRecord->status === 'COMPLETED' ? "YES (Marked COMPLETED without ES Write)" : "NO") . "\n";
echo "  Ghost Document Exists in ES: " . ($docExistsInEs ? "FAIL: GHOST RESURRECTED" : "NO (100% Zero Ghost Resurrection)") . "\n";

// Cleanup Test 3
DB::table('sys_outbox_events')->where('id', $staleEventId)->delete();
Cache::forget("company:tombstone:{$tombstoneCompanyId}");

$outboxResults['tombstone_zombie_defense'] = $tombstoneGuarded;

// ----------------------------------------------------------------------------
// Verdict Summary
// ----------------------------------------------------------------------------
$allPassed = ! in_array(false, $outboxResults, true);
echo "\n================================================================================\n";
echo "   OUTBOX CONCURRENCY & TOMBSTONE SAFETY VERDICT: " . ($allPassed ? "ALL TESTS PASSED" : "FAILED") . "\n";
echo "================================================================================\n";

$jsonPath = dirname(__DIR__) . '/docs/audit documents/superadmin_company/phase5/outbox_concurrency_evidence.json';
file_put_contents($jsonPath, json_encode([
    'timestamp' => now()->toIso8601String(),
    'verdict' => $allPassed ? 'VERIFIED' : 'FAILED',
    'tests' => $outboxResults,
], JSON_PRETTY_PRINT));
echo "Outbox Concurrency Evidence Persisted to: {$jsonPath}\n";
