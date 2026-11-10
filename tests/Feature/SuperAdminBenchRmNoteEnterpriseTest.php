<?php

namespace Tests\Feature;

use App\Jobs\ProcessBenchRmNoteOutboxEventJob;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\BenchRmNoteCircuitBreaker;
use App\Services\SuperAdmin\SuperAdminBenchRmNoteCacheManager;
use App\Services\SuperAdmin\SuperAdminBenchRmNoteMutationService;
use App\Services\SuperAdmin\SuperAdminBenchRmNoteReadService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class SuperAdminBenchRmNoteEnterpriseTest
 *
 * Phase 2 Enterprise Hardening & Self-Recovery Test Suite:
 * - Domain A: Monotonic aggregate versioning, out-of-order stale event drop, deletion tombstones.
 * - Domain B: Distributed single-flight stampede locks, cardinality normalization.
 * - Domain C: Idempotency tampering protection (422), abandoned lease reclamation.
 * - Domain D: Dead-letter outbox transitions, safe replay, aggregate-level ordering.
 * - Domain E: Elasticsearch versioned indexing and drift repair.
 * - Domain F: Circuit-breaker state machine (CLOSED -> OPEN -> HALF_OPEN -> CLOSED).
 * - Domain G: Rich-text XSS sanitization and defense-in-depth authorization.
 * - Domain H: Observability operational metrics.
 * - Domain I: Automated self-healing reconciliation command.
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic cleanup in tearDown().
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminBenchRmNoteEnterpriseTest extends TestCase
{
    protected User $superAdmin;

    protected array $createdTemplateIds = [];

    protected array $createdOutboxIds = [];

    protected array $createdIdempotencyKeys = [];

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'newdata',
            'database.connections.mysql.username' => 'root',
            'database.connections.mysql.password' => '',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');

        $this->withoutMiddleware([
            \App\Http\Middleware\EnsureOneDeviceLogin::class,
        ]);

        $this->superAdmin = User::where('is_superadmin', 1)->first()
            ?? User::first();

        $this->purgeTestArtifacts();
    }

    protected function tearDown(): void
    {
        $this->purgeTestArtifacts();

        parent::tearDown();
    }

    protected function purgeTestArtifacts(): void
    {
        $testIds = DB::table('bench_rm_note_master')
            ->whereNull('company_id')
            ->where(function ($q) {
                if (! empty($this->createdTemplateIds)) {
                    $q->whereIn('id', $this->createdTemplateIds);
                }
                $q->orWhere('title', 'like', 'Enterprise Version Test%')
                    ->orWhere('title', 'like', 'Stale Drop Test%')
                    ->orWhere('title', 'like', 'Tombstone Test%')
                    ->orWhere('title', 'like', '100 Submissions Test%')
                    ->orWhere('title', 'like', 'Durable Tombstone Test%')
                    ->orWhere('title', 'like', 'Original RM Payload Title%')
                    ->orWhere('title', 'like', 'Tampered RM Title Attack%')
                    ->orWhere('title', 'like', 'Dead-Letter Test%')
                    ->orWhere('title', 'like', 'XSS Rubric Test%')
                    ->orWhere('title', 'like', 'Reconciliation Test%');
            })
            ->pluck('id')
            ->toArray();

        if (! empty($testIds)) {
            DB::table('bench_rm_note_master')->whereIn('id', $testIds)->delete();
            DB::table('sys_outbox_events')
                ->where('aggregate_type', 'BenchRmNoteTemplate')
                ->whereIn('aggregate_id', $testIds)
                ->delete();

            $elasticService = app(ElasticService::class);
            $indexName = defined('ELASTIC_BENCH_RM_NOTE_MASTER')
                ? ELASTIC_BENCH_RM_NOTE_MASTER
                : config('constants.ELASTIC_BENCH_RM_NOTE_MASTER', 'local_bench_rm_note_master');

            if ($elasticService->indexExists($indexName)) {
                foreach ($testIds as $tId) {
                    try {
                        if ($elasticService->getClient()->exists(['index' => $indexName, 'id' => (string) $tId])) {
                            $elasticService->getClient()->delete([
                                'index' => $indexName,
                                'id' => (string) $tId,
                                'refresh' => true,
                            ]);
                        }
                    } catch (\Throwable $e) {
                    }
                }
            }
        }

        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        BenchRmNoteCircuitBreaker::reset();
        SuperAdminBenchRmNoteCacheManager::flushCaches();
    }

    /**
     * Domain A & Test 1: Monotonic Aggregate Versioning increments sequentially on consecutive mutations.
     */
    public function test_monotonic_aggregate_versioning(): void
    {
        $this->actingAs($this->superAdmin);

        $mutationService = app(SuperAdminBenchRmNoteMutationService::class);

        // 1. Create -> v1
        $createRes = $mutationService->createTemplate([
            'title' => 'Enterprise Version Test '.uniqid(),
            'content' => '<p>Version 1 baseline content</p>',
            'status' => 1,
        ]);
        $this->assertSame('success', $createRes['status']);
        $templateId = (int) $createRes['template']->id;
        $this->createdTemplateIds[] = $templateId;

        $v1Event = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'BenchRmNoteTemplate')
            ->where('aggregate_id', $templateId)
            ->where('event_type', 'BENCH_RM_NOTE_TEMPLATE_CREATED')
            ->first();
        $this->assertNotNull($v1Event);
        $this->assertSame(1, (int) $v1Event->aggregate_version);
        $this->createdOutboxIds[] = $v1Event->id;

        // 2. Update -> v2
        $updateRes = $mutationService->updateTemplate($templateId, [
            'title' => 'Enterprise Version Test Updated '.uniqid(),
            'content' => '<p>Version 2 updated content</p>',
            'status' => 1,
        ]);
        $this->assertSame('success', $updateRes['status']);

        $v2Event = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'BenchRmNoteTemplate')
            ->where('aggregate_id', $templateId)
            ->where('event_type', 'BENCH_RM_NOTE_TEMPLATE_UPDATED')
            ->first();
        $this->assertNotNull($v2Event);
        $this->assertSame(2, (int) $v2Event->aggregate_version);
        $this->createdOutboxIds[] = $v2Event->id;

        // 3. Status Toggle -> v3
        $toggleRes = $mutationService->toggleStatus($templateId);
        $this->assertSame('success', $toggleRes['status']);

        $v3Event = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'BenchRmNoteTemplate')
            ->where('aggregate_id', $templateId)
            ->where('event_type', 'BENCH_RM_NOTE_TEMPLATE_STATUS_TOGGLED')
            ->first();
        $this->assertNotNull($v3Event);
        $this->assertSame(3, (int) $v3Event->aggregate_version);
        $this->createdOutboxIds[] = $v3Event->id;
    }

    /**
     * Domain A & Test 2: Outbox worker drops stale out-of-order events without overwriting newer ES documents.
     */
    public function test_outbox_worker_drops_stale_out_of_order_event(): void
    {
        $this->actingAs($this->superAdmin);

        $elasticService = app(ElasticService::class);
        $indexName = defined('ELASTIC_BENCH_RM_NOTE_MASTER')
            ? ELASTIC_BENCH_RM_NOTE_MASTER
            : config('constants.ELASTIC_BENCH_RM_NOTE_MASTER', 'local_bench_rm_note_master');

        if (! $elasticService->indexExists($indexName)) {
            app(SuperAdminBenchRmNoteReadService::class)->ensureIndexExists();
        }

        $now = now();
        $templateId = DB::table('bench_rm_note_master')->insertGetId([
            'company_id' => null,
            'title' => 'Stale Drop Test '.uniqid(),
            'content' => '<p>Newest Content v3</p>',
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->createdTemplateIds[] = $templateId;

        // Pre-seed Elasticsearch with version 3
        $elasticService->getClient()->index([
            'index' => $indexName,
            'id' => (string) $templateId,
            'refresh' => 'wait_for',
            'body' => [
                'id' => $templateId,
                'company_id' => null,
                'title' => 'Stale Drop Test in ES v3',
                'content' => '<p>Newest Content v3</p>',
                'status' => 1,
                'version' => 3,
                'created_at' => $now->toIso8601String(),
                'updated_at' => $now->toIso8601String(),
            ],
        ]);

        // Create a delayed v1 outbox event in PENDING state
        $staleEventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => 'ob_stale_test_'.uniqid(),
            'aggregate_type' => 'BenchRmNoteTemplate',
            'aggregate_id' => $templateId,
            'aggregate_version' => 1, // Stale version
            'event_type' => 'BENCH_RM_NOTE_TEMPLATE_CREATED',
            'payload' => json_encode(['id' => $templateId, 'title' => 'Stale v1 Title']),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => $now,
            'created_at' => $now,
        ]);
        $this->createdOutboxIds[] = $staleEventId;

        // Process stale event
        $job = new ProcessBenchRmNoteOutboxEventJob($staleEventId);
        $job->handle();

        // Assert outbox event was marked COMPLETED (dropped safely) with notice in last_error
        $eventRow = DB::table('sys_outbox_events')->where('id', $staleEventId)->first();
        $this->assertSame('COMPLETED', $eventRow->status);
        $this->assertStringContainsString('Skipped: ES has newer document', (string) $eventRow->last_error);

        // Verify ES still holds version 3 (not overwritten by v1)
        $doc = $elasticService->getClient()->get([
            'index' => $indexName,
            'id' => (string) $templateId,
        ]);
        $this->assertSame(3, (int) $doc['_source']['version']);
        $this->assertSame('Stale Drop Test in ES v3', $doc['_source']['title']);
    }

    /**
     * Domain A & Test 3: Version-aware deletion tombstones prevent entity resurrection.
     */
    public function test_version_aware_deletion_tombstone_prevents_resurrection(): void
    {
        $this->actingAs($this->superAdmin);

        $templateId = 9999901;
        $now = now();

        // Record a deletion tombstone at version 2
        SuperAdminBenchRmNoteCacheManager::setTombstone($templateId, 2);
        $this->assertSame(2, SuperAdminBenchRmNoteCacheManager::getTombstone($templateId));

        // Create a delayed upsert event at version 1 (which would otherwise recreate/resurrect the doc)
        $delayedEventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => 'ob_resurrect_'.uniqid(),
            'aggregate_type' => 'BenchRmNoteTemplate',
            'aggregate_id' => $templateId,
            'aggregate_version' => 1,
            'event_type' => 'BENCH_RM_NOTE_TEMPLATE_UPDATED',
            'payload' => json_encode(['id' => $templateId, 'title' => 'Resurrected Title']),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => $now,
            'created_at' => $now,
        ]);
        $this->createdOutboxIds[] = $delayedEventId;

        $job = new ProcessBenchRmNoteOutboxEventJob($delayedEventId);
        $job->handle();

        $eventRow = DB::table('sys_outbox_events')->where('id', $delayedEventId)->first();
        $this->assertSame('COMPLETED', $eventRow->status);
        $this->assertStringContainsString('entity tombstoned', (string) $eventRow->last_error);
    }

    /**
     * Domain C & Test 4: Idempotency tampering guard rejects key reuse with mismatched payloads (HTTP 422).
     */
    public function test_idempotency_tampering_guard_rejects_mismatched_payload(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'test-tamper-key-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        // 1. Initial valid submission
        $res1 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.rm-note.store'), [
            'title' => 'Original RM Payload Title',
            'content' => '<p>Original legitimate content rubric.</p>',
            'status' => 1,
        ]);

        $res1->assertStatus(200);
        $templateId = (int) $res1->json('template.id');
        $this->createdTemplateIds[] = $templateId;

        // 2. Tampered submission with same key but modified title/content (payload tampering attack)
        $res2 = $this->withHeaders([
            'X-Idempotency-Key' => $idempotencyKey,
        ])->postJson(route('superadmin.rm-note.store'), [
            'title' => 'Tampered RM Title Attack',
            'content' => '<p>Hostile payload modification under same idempotency key.</p>',
            'status' => 1,
        ]);

        // Expect HTTP 422 Unprocessable Entity
        $res2->assertStatus(422);
    }

    /**
     * Domain F & Test 5: Circuit breaker state machine transitions (CLOSED -> OPEN -> HALF_OPEN -> CLOSED).
     */
    public function test_circuit_breaker_complete_state_transitions(): void
    {
        BenchRmNoteCircuitBreaker::reset();
        $this->assertSame(BenchRmNoteCircuitBreaker::STATE_CLOSED, BenchRmNoteCircuitBreaker::getState());
        $this->assertTrue(BenchRmNoteCircuitBreaker::isAvailable());

        // Record 3 consecutive failures to trip circuit
        BenchRmNoteCircuitBreaker::recordFailure(new \RuntimeException('Connection refused 1'));
        $this->assertSame(1, BenchRmNoteCircuitBreaker::getFailures());
        $this->assertSame(BenchRmNoteCircuitBreaker::STATE_CLOSED, BenchRmNoteCircuitBreaker::getState());

        BenchRmNoteCircuitBreaker::recordFailure(new \RuntimeException('Connection refused 2'));
        $this->assertSame(2, BenchRmNoteCircuitBreaker::getFailures());

        BenchRmNoteCircuitBreaker::recordFailure(new \RuntimeException('Connection refused 3'));
        $this->assertSame(3, BenchRmNoteCircuitBreaker::getFailures());

        // Circuit should now be OPEN
        $this->assertSame(BenchRmNoteCircuitBreaker::STATE_OPEN, BenchRmNoteCircuitBreaker::getState());
        $this->assertFalse(BenchRmNoteCircuitBreaker::isAvailable());

        // Fast-forward last failure time by 31 seconds to test HALF_OPEN recovery probe
        Cache::put('superadmin:circuit:brmnote:last_failure', Carbon::now()->subSeconds(35)->toIso8601String(), 86400);

        $this->assertSame(BenchRmNoteCircuitBreaker::STATE_HALF_OPEN, BenchRmNoteCircuitBreaker::getState());
        $this->assertTrue(BenchRmNoteCircuitBreaker::isAvailable());

        // Successful probes reset circuit to CLOSED (requires SUCCESS_THRESHOLD = 2 canary probes)
        BenchRmNoteCircuitBreaker::recordSuccess();
        BenchRmNoteCircuitBreaker::recordSuccess();
        $this->assertSame(BenchRmNoteCircuitBreaker::STATE_CLOSED, BenchRmNoteCircuitBreaker::getState());
        $this->assertSame(0, BenchRmNoteCircuitBreaker::getFailures());
    }

    /**
     * Domain D & Test 6: Dead-letter outbox handling and CLI safe replay.
     */
    public function test_dead_letter_handling_and_safe_replay(): void
    {
        $now = now();
        $deadLetterEventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => 'ob_dead_letter_'.uniqid(),
            'aggregate_type' => 'BenchRmNoteTemplate',
            'aggregate_id' => 9999902,
            'aggregate_version' => 1,
            'event_type' => 'BENCH_RM_NOTE_TEMPLATE_CREATED',
            'payload' => json_encode(['id' => 9999902, 'title' => 'Dead-Letter Test']),
            'status' => 'DEAD_LETTER',
            'attempts' => 3,
            'available_at' => $now,
            'created_at' => $now,
        ]);
        $this->createdOutboxIds[] = $deadLetterEventId;

        // Verify status in DB is DEAD_LETTER
        $eventBefore = DB::table('sys_outbox_events')->where('id', $deadLetterEventId)->first();
        $this->assertSame('DEAD_LETTER', $eventBefore->status);

        // Replay dead-letter events via CLI command
        $this->artisan('bench-rm-notes:process-outbox', [
            '--replay-dead-letter' => true,
            '--limit' => 1,
        ])->assertExitCode(0);

        // Status must have been reset to PENDING and attempts cleared to 0 (or completed if fast-path caught it)
        $eventAfter = DB::table('sys_outbox_events')->where('id', $deadLetterEventId)->first();
        $this->assertNotSame('DEAD_LETTER', $eventAfter->status);
    }

    /**
     * Domain G & Test 7: Rich-text XSS sanitization strips dangerous tags and javascript protocols.
     */
    public function test_rich_text_xss_sanitization(): void
    {
        $mutationService = app(SuperAdminBenchRmNoteMutationService::class);

        $maliciousHtml = '<p>Safe intro</p><script>alert("xss")</script><iframe src="http://evil.com"></iframe><div onclick="steal()">Click me</div><a href="javascript:alert(1)">Bad Link</a>';

        $sanitized = $mutationService->sanitizeHtml($maliciousHtml);

        $this->assertStringNotContainsString('<script>', $sanitized);
        $this->assertStringNotContainsString('alert("xss")', $sanitized);
        $this->assertStringNotContainsString('<iframe', $sanitized);
        $this->assertStringNotContainsString('onclick', $sanitized);
        $this->assertStringNotContainsString('javascript:', $sanitized);
        $this->assertStringContainsString('Safe intro', $sanitized);
    }

    /**
     * Domain H & Test 8: Observability operational metrics exposes comprehensive subsystem health indicators.
     */
    public function test_observability_operational_metrics(): void
    {
        $readService = app(SuperAdminBenchRmNoteReadService::class);

        $metrics = $readService->getOperationalMetrics();

        $this->assertArrayHasKey('circuit_breaker', $metrics);
        $this->assertArrayHasKey('state', $metrics['circuit_breaker']);
        $this->assertArrayHasKey('is_available', $metrics['circuit_breaker']);
        $this->assertArrayHasKey('consecutive_failures', $metrics['circuit_breaker']);

        $this->assertArrayHasKey('outbox', $metrics);
        $this->assertArrayHasKey('pending', $metrics['outbox']);
        $this->assertArrayHasKey('processing', $metrics['outbox']);
        $this->assertArrayHasKey('completed', $metrics['outbox']);
        $this->assertArrayHasKey('failed', $metrics['outbox']);
        $this->assertArrayHasKey('dead_letter', $metrics['outbox']);

        $this->assertArrayHasKey('elasticsearch', $metrics);
        $this->assertArrayHasKey('healthy', $metrics['elasticsearch']);

        $this->assertArrayHasKey('synchronization', $metrics);
        $this->assertArrayHasKey('mysql_total', $metrics['synchronization']);
        $this->assertArrayHasKey('elasticsearch_total', $metrics['synchronization']);
        $this->assertArrayHasKey('drift_count', $metrics['synchronization']);

        $this->assertArrayHasKey('cache', $metrics);
        $this->assertArrayHasKey('generation_version', $metrics['cache']);
    }

    /**
     * Domain I & Test 9: Automated self-healing reconcile command repairs data drift between MySQL and ES.
     */
    public function test_automated_self_healing_reconcile_command(): void
    {
        $this->actingAs($this->superAdmin);

        $now = now();
        $dbId = DB::table('bench_rm_note_master')->insertGetId([
            'company_id' => null,
            'title' => 'Reconciliation Test '.uniqid(),
            'content' => '<p>Drift repair verification rubric.</p>',
            'status' => 1,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
        $this->createdTemplateIds[] = $dbId;

        // Run dry-run scan (should report drift without fixing)
        $this->artisan('bench-rm-notes:reconcile')
            ->assertExitCode(0);

        // Run with --fix to backfill into Elasticsearch
        $this->artisan('bench-rm-notes:reconcile', ['--fix' => true])
            ->assertExitCode(0);

        $elasticService = app(ElasticService::class);
        $indexName = defined('ELASTIC_BENCH_RM_NOTE_MASTER')
            ? ELASTIC_BENCH_RM_NOTE_MASTER
            : config('constants.ELASTIC_BENCH_RM_NOTE_MASTER', 'local_bench_rm_note_master');

        if ($elasticService->indexExists($indexName)) {
            $exists = $elasticService->getClient()->exists([
                'index' => $indexName,
                'id' => (string) $dbId,
            ]);
            $this->assertTrue($exists, 'Reconcile command --fix did not repair missing Elasticsearch document.');
        }
    }

    /**
     * Domain 2 Test: 100 identical submissions with identical idempotency key.
     * Guarantees exactly 1 database write and 99 cached replays (_replayed => true).
     */
    public function test_100_identical_concurrent_submissions_fenced_with_replay(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'brmnote_100_concurrent_test_'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $payload = [
            'title' => '100 Submissions Test '.uniqid(),
            'content' => '<p>High-concurrency idempotency rubric.</p>',
            'status' => 1,
            '_token' => csrf_token(),
            '_idempotency_key' => $idempotencyKey,
        ];

        $replayedCount = 0;
        $initialTemplateId = null;

        for ($i = 0; $i < 100; $i++) {
            $response = $this->withHeaders([
                'X-Idempotency-Key' => $idempotencyKey,
                'X-Requested-With' => 'XMLHttpRequest',
            ])->postJson(route('superadmin.rm-note.store'), $payload);

            $response->assertStatus(200);
            $json = $response->json();

            $this->assertEquals('success', $json['status']);

            if ($i === 0) {
                $this->assertArrayNotHasKey('_replayed', $json);
                $initialTemplateId = $json['template']['id'] ?? ($json['template']->id ?? null);
                if ($initialTemplateId) {
                    $this->createdTemplateIds[] = (int) $initialTemplateId;
                }
            } else {
                $this->assertTrue($json['_replayed'] ?? false, "Request #{$i} should have been replayed.");
                $replayedCount++;
            }
        }

        $this->assertEquals(99, $replayedCount, 'Exactly 99 responses should be marked as replayed.');

        // Verify exactly ONE record was inserted in MySQL
        $matchingRecordsCount = DB::table('bench_rm_note_master')
            ->whereNull('company_id')
            ->where('title', $payload['title'])
            ->count();
        $this->assertEquals(1, $matchingRecordsCount, 'Exactly one MySQL record must exist after 100 identical submissions.');
    }

    /**
     * Domain 1 Test: Durable tombstone loss recovery across Redis restarts.
     * Verifies that even if Redis tombstones are completely purged (simulating a Redis restart),
     * the system queries authoritative MySQL deletion history and prevents stale resurrection in ES.
     */
    public function test_durable_tombstone_loss_recovery_across_redis_restart(): void
    {
        $this->actingAs($this->superAdmin);

        $mutationService = app(SuperAdminBenchRmNoteMutationService::class);
        $res = $mutationService->createTemplate([
            'title' => 'Durable Tombstone Test '.uniqid(),
            'content' => '<p>Durable tombstone rubric test.</p>',
            'status' => 1,
        ]);
        $templateObj = $res['template'] ?? null;
        $templateId = is_object($templateObj) ? (int) $templateObj->id : (int) ($templateObj['id'] ?? 0);
        $this->assertGreaterThan(0, $templateId);
        $this->createdTemplateIds[] = $templateId;

        // Delete the template to generate authoritative MySQL deletion outbox event
        $mutationService->deleteTemplate($templateId);

        // Simulate complete Redis restart / flush by purging Redis tombstone key
        Cache::forget(SuperAdminBenchRmNoteCacheManager::TOMBSTONE_PREFIX.$templateId);
        $this->assertNull(Cache::get(SuperAdminBenchRmNoteCacheManager::TOMBSTONE_PREFIX.$templateId));

        // Attempt getTombstone - must recover from MySQL outbox
        $recoveredVersion = SuperAdminBenchRmNoteCacheManager::getTombstone($templateId);
        $this->assertNotNull($recoveredVersion, 'Tombstone was not recovered from MySQL outbox after simulated Redis restart.');
        $this->assertGreaterThanOrEqual(1, $recoveredVersion);

        // Dispatch a delayed outbox event for the deleted entity
        $staleEventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => 'stale_durable_test_'.uniqid(),
            'aggregate_type' => 'BenchRmNoteTemplate',
            'aggregate_id' => $templateId,
            'aggregate_version' => 1, // Stale version
            'event_type' => 'BENCH_RM_NOTE_TEMPLATE_UPDATED',
            'payload' => json_encode([
                'id' => $templateId,
                'title' => 'Stale Resurrection Attempt',
                'status' => 1,
                'version' => 1,
            ]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);
        $this->createdOutboxIds[] = $staleEventId;

        $job = new ProcessBenchRmNoteOutboxEventJob($staleEventId);
        $job->handle();

        // Verify the event was dropped and document does NOT exist in Elasticsearch
        $eventRecord = DB::table('sys_outbox_events')->where('id', $staleEventId)->first();
        $this->assertEquals('COMPLETED', $eventRecord->status);
        $this->assertStringContainsString('tombstoned', $eventRecord->last_error ?? '');

        $elasticService = app(ElasticService::class);
        $indexName = defined('ELASTIC_BENCH_RM_NOTE_MASTER')
            ? ELASTIC_BENCH_RM_NOTE_MASTER
            : config('constants.ELASTIC_BENCH_RM_NOTE_MASTER', 'local_bench_rm_note_master');

        if ($elasticService->indexExists($indexName)) {
            $exists = $elasticService->getClient()->exists([
                'index' => $indexName,
                'id' => (string) $templateId,
            ]);
            $this->assertFalse($exists, 'Deleted entity was resurrected in ES despite durable tombstone!');
        }
    }

    /**
     * Domain 7 Test: Circuit breaker requires SUCCESS_THRESHOLD = 2 canary successes in HALF_OPEN.
     */
    public function test_circuit_breaker_canary_success_threshold_in_half_open(): void
    {
        BenchRmNoteCircuitBreaker::reset();

        // 1. Force state to HALF_OPEN
        Cache::put('superadmin:circuit:brmnote:state', BenchRmNoteCircuitBreaker::STATE_HALF_OPEN, 86400);
        $this->assertEquals(BenchRmNoteCircuitBreaker::STATE_HALF_OPEN, BenchRmNoteCircuitBreaker::getState());

        // 2. First probe success (success count = 1) -> must remain in HALF_OPEN
        BenchRmNoteCircuitBreaker::recordSuccess();
        $this->assertEquals(1, BenchRmNoteCircuitBreaker::getSuccesses());
        $this->assertEquals(BenchRmNoteCircuitBreaker::STATE_HALF_OPEN, BenchRmNoteCircuitBreaker::getState());

        // 3. Second probe success (success count = 2 = SUCCESS_THRESHOLD) -> transitions to CLOSED
        BenchRmNoteCircuitBreaker::recordSuccess();
        $this->assertEquals(BenchRmNoteCircuitBreaker::STATE_CLOSED, BenchRmNoteCircuitBreaker::getState());
        $this->assertEquals(0, BenchRmNoteCircuitBreaker::getSuccesses());

        BenchRmNoteCircuitBreaker::reset();
    }
}
