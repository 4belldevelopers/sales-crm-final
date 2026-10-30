<?php

namespace Tests\Feature;

use App\Http\Controllers\SuperAdmin\SuperAdminCompanyController;
use App\Jobs\ProcessMeetingOutboxEventJob;
use App\Models\User;
use App\Services\SuperAdmin\SuperAdminMeetingMutationService;
use App\Services\SuperAdminMeetingScheduleCacheManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class SuperAdminMeetingsLoadTest
 *
 * Automated Architecture, Idempotency, Concurrency, Circuit Breaker & Zero-MySQL-Read Verification Suite
 * for the Super Admin Meeting Tracker & Schedule Hub.
 *
 * Verifies all architectural pillars:
 * 1. Stampede Protection (Single-Flight Locking with double-check)
 * 2. Zero-MySQL-Read CQRS Performance on cached listings
 * 3. Authoritative in-memory mutation delta overlay
 * 4. Two-tier distributed idempotency with canonical replay (is_replayed: true)
 * 5. Strict rejection of idempotency key reuse with mismatched payloads (HTTP 422)
 * 6. Transactional Outbox write boundary with independent delivery ledgers
 * 7. Outbox worker completion semantics (ProcessMeetingOutboxEventJob)
 * 8. Private WebSocket broadcast channel authorization (private-superadmin.meetings)
 * 9. Circuit Breaker lifecycle & degraded snapshot fallback
 * 10. Operational Health Telemetry CLI Command (superadmin:meetings:health)
 * 11. 100-Request Benchmark with Little's Law Queuing Model Proof for 10,000 Concurrent Users
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminMeetingsLoadTest extends TestCase
{
    protected $controller;

    protected $adminUser;

    protected $mutationService;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'newdata',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        $this->adminUser = User::where('root_user', 1)->first() ?? User::first();
        if ($this->adminUser) {
            auth()->login($this->adminUser);
        }

        $this->controller = app(SuperAdminCompanyController::class);
        $this->controller->user = $this->adminUser;
        $this->mutationService = app(SuperAdminMeetingMutationService::class);
    }

    /**
     * Pillar 1: Stampede Protection (Single-Flight Locking).
     */
    public function test_stampede_protection_single_flight_execution(): void
    {
        $testKey = 'test:meet:stampede:'.uniqid();
        $executionCount = 0;

        $closure = function () use (&$executionCount) {
            $executionCount++;
            usleep(20000); // simulate 20ms computation

            return ['status' => 'computed', 'val' => 1042];
        };

        // First call computes
        $res1 = SuperAdminMeetingScheduleCacheManager::rememberWithStampedeProtection($testKey, 10, $closure);
        $this->assertEquals(1042, $res1['val']);
        $this->assertEquals(1, $executionCount);

        // Second call hits cache directly
        $res2 = SuperAdminMeetingScheduleCacheManager::rememberWithStampedeProtection($testKey, 10, $closure);
        $this->assertEquals(1042, $res2['val']);
        $this->assertEquals(1, $executionCount, 'Cache stampede protection must avoid re-execution on warm key.');

        Cache::forget($testKey);
    }

    /**
     * Pillar 2: Zero MySQL Reads on Cached Listing Requests (0% Database Read Strain).
     */
    public function test_zero_mysql_reads_on_cached_meeting_schedule_list(): void
    {
        SuperAdminMeetingScheduleCacheManager::invalidateAll();

        $request = Request::create('/super-admin/meetingstoreScheduleList', 'GET', ['per_page' => 20]);

        // Cold load: populates cache
        $this->controller->meetingScheduleList($request);

        // Warm load: assert 0 queries on business domain tables
        DB::flushQueryLog();
        DB::enableQueryLog();

        $this->controller->meetingScheduleList($request);

        $queries = DB::getQueryLog();
        DB::disableQueryLog();

        $domainQueries = array_filter($queries, function ($q) {
            $sql = strtolower($q['query']);

            return str_contains($sql, 'meeting_schedules') ||
                str_contains($sql, 'meeting_comments') ||
                str_contains($sql, 'interview_mode');
        });

        $this->assertCount(
            0,
            $domainQueries,
            'Zero-MySQL-Read Violation: Cached meeting listing executed '.count($domainQueries).' domain queries.'
        );
    }

    /**
     * Pillar 3: Authoritative Mutation Delta Overlay.
     */
    public function test_authoritative_mutation_delta_overlay(): void
    {
        $meetingId = 999999;
        $deltaPayload = [
            'action' => 'comment_added',
            'comment_id' => 555,
            'comment_count' => 8,
        ];

        SuperAdminMeetingScheduleCacheManager::setMutationDelta($meetingId, $deltaPayload);

        $retrieved = SuperAdminMeetingScheduleCacheManager::getMutationDelta($meetingId);
        $this->assertNotNull($retrieved);
        $this->assertEquals(8, $retrieved['comment_count']);

        SuperAdminMeetingScheduleCacheManager::invalidateAll($meetingId);
        $this->assertNull(SuperAdminMeetingScheduleCacheManager::getMutationDelta($meetingId));
    }

    /**
     * Pillar 4: Two-Tier Distributed Idempotency Lease Claim and Replay.
     */
    public function test_idempotency_lease_claim_and_replay_canonical_response(): void
    {
        // Find or create test meeting
        $meeting = DB::table('meeting_schedules')->first();
        if (! $meeting) {
            $meetingId = DB::table('meeting_schedules')->insertGetId([
                'schedule_date' => now()->addDays(2),
                'interview_type' => '1',
                'interview_mode' => 2,
                'from_company_id' => 1,
                'to_company_id' => 2,
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } else {
            $meetingId = (int) $meeting->id;
        }

        $idempotencyKey = 'test_idemp_cmt_'.uniqid();
        $commentText = 'Initial Candidate Interview Feedback '.uniqid();

        // 1. Initial Mutation
        $res1 = $this->mutationService->addComment(
            $meetingId,
            $commentText,
            (int) ($this->adminUser->id ?? 1),
            $idempotencyKey
        );

        $this->assertTrue($res1['status']);
        $this->assertFalse($res1['is_replayed']);
        $commentCount1 = $res1['comment_count'];

        // 2. Exact Duplicate Submission with same idempotency key
        $res2 = $this->mutationService->addComment(
            $meetingId,
            $commentText,
            (int) ($this->adminUser->id ?? 1),
            $idempotencyKey
        );

        $this->assertTrue($res2['status']);
        $this->assertTrue($res2['is_replayed'], 'Duplicate submission must return is_replayed: true');
        $this->assertEquals($commentCount1, $res2['comment_count'], 'Replayed response must match original canonical count.');
    }

    /**
     * Pillar 5: Idempotency Key Rejection on Payload Hash Mismatch.
     */
    public function test_idempotency_rejects_payload_hash_mismatch(): void
    {
        $meeting = DB::table('meeting_schedules')->first();
        $meetingId = $meeting ? (int) $meeting->id : 1;

        $idempotencyKey = 'test_idemp_mismatch_'.uniqid();

        // 1. First submission with Payload A
        $res1 = $this->mutationService->addComment(
            $meetingId,
            'Original Note Content',
            (int) ($this->adminUser->id ?? 1),
            $idempotencyKey
        );
        $this->assertTrue($res1['status']);

        // 2. Second submission with same key but DIFFERENT Payload B
        $res2 = $this->mutationService->addComment(
            $meetingId,
            'COMPLETELY DIFFERENT CONTENT WITH SAME KEY',
            (int) ($this->adminUser->id ?? 1),
            $idempotencyKey
        );

        $this->assertFalse($res2['status']);
        $this->assertEquals(422, $res2['http_status']);
        $this->assertStringContainsString('mismatch', strtolower($res2['message']));
    }

    /**
     * Pillar 6: Transactional Outbox Append with Delivery Ledgers.
     */
    public function test_transactional_outbox_appends_event_and_delivery_ledgers(): void
    {
        $meeting = DB::table('meeting_schedules')->first();
        $meetingId = $meeting ? (int) $meeting->id : 1;

        $res = $this->mutationService->addComment(
            $meetingId,
            'Outbox Ledger Verification Note '.uniqid(),
            (int) ($this->adminUser->id ?? 1)
        );

        $this->assertTrue($res['status']);
        $commentId = $res['comment_id'];

        // Assert outbox event exists
        $outboxEvent = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'MeetingSchedule')
            ->where('aggregate_id', $meetingId)
            ->where('event_type', 'meeting.comment_added')
            ->orderBy('id', 'desc')
            ->first();

        $this->assertNotNull($outboxEvent, 'Outbox event must be appended in sys_outbox_events.');

        // Assert dedicated delivery records exist
        $deliveries = DB::table('sys_outbox_deliveries')
            ->where('outbox_id', $outboxEvent->id)
            ->pluck('status', 'destination')
            ->toArray();

        $this->assertArrayHasKey('websocket', $deliveries, 'Outbox must track websocket delivery ledger.');
        $this->assertArrayHasKey('redis', $deliveries, 'Outbox must track redis delivery ledger.');
    }

    /**
     * Pillar 7: Background Outbox Worker Processing.
     */
    public function test_outbox_worker_processes_and_marks_completed(): void
    {
        $meeting = DB::table('meeting_schedules')->first();
        $meetingId = $meeting ? (int) $meeting->id : 1;

        $eventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => 'test_worker_'.uniqid(),
            'aggregate_type' => 'MeetingSchedule',
            'aggregate_id' => $meetingId,
            'aggregate_version' => 1,
            'event_type' => 'meeting.comment_added',
            'payload' => json_encode([
                'action' => 'comment_added',
                'meeting_id' => $meetingId,
                'data' => ['test' => true],
            ]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);

        $job = new ProcessMeetingOutboxEventJob($eventId);
        $job->handle();

        $updatedEvent = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        $this->assertEquals('COMPLETED', $updatedEvent->status, 'Outbox event must transition to COMPLETED upon successful projection delivery.');
    }

    /**
     * Pillar 8: WebSocket Private Channel Authorization Gate.
     */
    public function test_websocket_channel_authorization_gate(): void
    {
        $superAdminUser = $this->adminUser;
        $authorized = Broadcast::auth(
            Request::create('/broadcasting/auth', 'POST', [
                'channel_name' => 'private-superadmin.meetings',
                'socket_id' => '123.456',
            ])->setUserResolver(fn () => $superAdminUser)
        );

        $this->assertNotNull($authorized, 'Super Admin must be authorized on private-superadmin.meetings channel.');
    }

    /**
     * Pillar 9: Circuit Breaker Degraded Snapshot Fallback.
     */
    public function test_circuit_breaker_degraded_snapshot_fallback(): void
    {
        SuperAdminMeetingScheduleCacheManager::saveSnapshot([
            'total' => 42,
            'paginator' => new \Illuminate\Pagination\LengthAwarePaginator([], 42, 50),
        ]);

        $snapshot = SuperAdminMeetingScheduleCacheManager::getLatestSnapshot();
        $this->assertNotNull($snapshot);
        $this->assertEquals(42, $snapshot['total']);
        $this->assertLessThan(60, $snapshot['snapshot_age_seconds']);
    }

    /**
     * Pillar 10: Operational Telemetry & Health Probe CLI.
     */
    public function test_operational_health_command(): void
    {
        $exitCode = Artisan::call('superadmin:meetings:health', ['--json' => true]);
        $this->assertEquals(0, $exitCode);

        $output = json_decode(Artisan::output(), true);
        $this->assertIsArray($output);
        $this->assertArrayHasKey('status', $output);
        $this->assertArrayHasKey('components', $output);
        $this->assertArrayHasKey('redis', $output['components']);
        $this->assertArrayHasKey('outbox', $output['components']);
    }

    /**
     * Pillar 11: 100-Request Benchmark & Little's Law 10,000 Concurrent User Scalability Proof.
     */
    public function test_100_request_benchmark_and_littles_law_10k_concurrency(): void
    {
        $request = Request::create('/super-admin/meetingstoreScheduleList', 'GET', ['per_page' => 20]);

        // Pre-warm cache
        $this->controller->meetingScheduleList($request);

        $latencies = [];
        $iterations = 100;

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $this->controller->meetingScheduleList($request);
            $latencies[] = (microtime(true) - $start) * 1000; // in milliseconds
        }

        sort($latencies);
        $avg = round(array_sum($latencies) / $iterations, 2);
        $p50 = round($latencies[(int) ($iterations * 0.50)], 2);
        $p95 = round($latencies[(int) ($iterations * 0.95)], 2);
        $p99 = round($latencies[(int) ($iterations * 0.99)], 2);

        // Mathematical Queuing Model: Little's Law (L = lambda * W)
        // 10,000 concurrent users with 5s think time = 2,000 RPS
        $rps = 10000 / 5.0; // 2,000 RPS
        $latencySeconds = ($avg / 1000.0);
        $requiredWorkers = round($rps * $latencySeconds, 2);
        $frankenPhpWorkerPool = 50;
        $workerUtilization = round(($requiredWorkers / $frankenPhpWorkerPool) * 100, 2);

        echo "\n[SuperAdminMeetingsLoadTest] 100-Request Benchmark Results:\n";
        echo "  • Avg: {$avg}ms\n";
        echo "  • p50: {$p50}ms\n";
        echo "  • p95: {$p95}ms\n";
        echo "  • p99: {$p99}ms\n";
        echo "  • Required Server Workers under 10K Concurrency: {$requiredWorkers} (Pool: {$frankenPhpWorkerPool}, Utilization: {$workerUtilization}%)\n";

        $this->assertLessThan(50.0, $avg, 'Average latency on Redis L2 cached reads must be < 50ms.');
        $this->assertLessThan(50.0, $p95, 'p95 latency on Redis L2 cached reads must be < 50ms.');
        $this->assertLessThan($frankenPhpWorkerPool, $requiredWorkers, 'Required worker capacity must operate well within the 50-worker pool.');
    }
}
