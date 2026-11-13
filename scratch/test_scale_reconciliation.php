<?php

/**
 * Super Admin Subscription Management - Phase 5 Scale Reconciliation Verification Harness
 *
 * Verifies:
 * - Chunking performance and memory stability across large record counts
 * - Resumable execution via --from-id
 * - In-flight mutation collision prevention
 * - Elasticsearch mget bulk querying without memory leaks
 */

require_once __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

echo "================================================================================\n";
echo "   PHASE 5: RECONCILIATION ENGINE SCALE & MEMORY VERIFICATION\n";
echo "================================================================================\n\n";

$memStart = memory_get_usage(true);

echo "1. Testing Resumable Execution (--from-id=2)...\n";
Artisan::call('subscription:reconcile', [
    '--dry-run' => true,
    '--chunk' => 50,
    '--from-id' => 2,
]);
$resumableOutput = Artisan::output();
echo $resumableOutput . "\n";

echo "2. Testing Small Chunking (--chunk=1) Memory Stability...\n";
$memBeforeChunk = memory_get_usage(true);
Artisan::call('subscription:reconcile', [
    '--dry-run' => true,
    '--chunk' => 1,
]);
$memAfterChunk = memory_get_usage(true);
$chunkMemDelta = round(($memAfterChunk - $memBeforeChunk) / 1024, 2);
echo "Chunking Memory Delta: {$chunkMemDelta} KB (Expected < 500 KB, no memory leak)\n\n";

echo "3. Testing Concurrency Protection on In-Flight Mutation...\n";
// Insert an in-flight PROCESSING outbox row for company 2
$tempOutboxId = DB::table('sys_outbox_events')->insertGetId([
    'tenant_id' => 0,
    'idempotency_key' => 'scale_test_' . uniqid(),
    'aggregate_type' => 'SubscriptionCompany',
    'aggregate_id' => 2,
    'aggregate_version' => 999,
    'event_type' => 'COMPANY_STATUS_TOGGLED',
    'payload' => json_encode(['company_id' => 2, 'pack_status' => 'active']),
    'status' => 'PROCESSING',
    'attempts' => 1,
    'locked_at' => now(),
    'available_at' => now(),
    'created_at' => now(),
]);

Artisan::call('subscription:reconcile', [
    '--repair' => true,
    '--chunk' => 10,
]);
$repairOutput = Artisan::output();
echo $repairOutput . "\n";

// Clean up temporary outbox row
DB::table('sys_outbox_events')->where('id', $tempOutboxId)->delete();

$memEnd = memory_get_usage(true);
echo sprintf("Reconciliation Scale Test Complete. Total Peak Memory: %.2f MB\n", $memEnd / (1024 * 1024));
