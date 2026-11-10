<?php

namespace Tests\Feature;

use App\Jobs\ProcessRecruiterNoteOutboxEventJob;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\RecruiterNoteCircuitBreaker;
use App\Services\SuperAdmin\SuperAdminRecruiterNoteCacheManager;
use App\Services\SuperAdmin\SuperAdminRecruiterNoteMutationService;
use App\Services\SuperAdmin\SuperAdminRecruiterNoteReadService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class SuperAdminRecruiterNoteEnterpriseTest
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
class SuperAdminRecruiterNoteEnterpriseTest extends TestCase
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
        $testIds = DB::table('recruiter_note_master')
            ->whereNull('company_id')
            ->where(function ($q) {
                if (! empty($this->createdTemplateIds)) {
                    $q->whereIn('id', $this->createdTemplateIds);
                }
                $q->orWhere('title', 'like', 'Enterprise Version Test%')
                    ->orWhere('title', 'like', 'Stale Event Drop Test%')
                    ->orWhere('title', 'like', 'Reconciliation Drift Test%')
                    ->orWhere('title', 'like', 'Technical Screening Rubric%')
                    ->orWhere('title', 'like', 'Long Key Rubric%')
                    ->orWhere('title', 'like', 'Original Payload Title%')
                    ->orWhere('title', 'like', 'Tampered Title Attack%');
            })
            ->pluck('id')
            ->toArray();

        if (! empty($testIds)) {
            DB::table('recruiter_note_master')->whereIn('id', $testIds)->delete();
            DB::table('sys_outbox_events')
                ->where('aggregate_type', 'RecruiterNoteTemplate')
                ->whereIn('aggregate_id', $testIds)
                ->delete();

            $elasticService = app(ElasticService::class);
            $indexName = defined('ELASTIC_RECRUITER_NOTE_MASTER')
                ? ELASTIC_RECRUITER_NOTE_MASTER
                : config('constants.ELASTIC_RECRUITER_NOTE_MASTER', 'local_recruiter_note_master');

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
                    } catch (\Throwable) {
                    }
                }
            }
        }

        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        DB::table('sys_outbox_events')
            ->where('aggregate_type', 'RecruiterNoteTemplate')
            ->where(function ($q) {
                $q->where('idempotency_key', 'like', 'stale_test_%')
                    ->orWhere('idempotency_key', 'like', 'tombstone_test_%')
                    ->orWhere('idempotency_key', 'like', 'dead_letter_test_%');
            })
            ->delete();

        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
            foreach ($this->createdIdempotencyKeys as $k) {
                $prefix = substr($k, 0, 30);
                DB::table('sys_outbox_events')->where('idempotency_key', 'like', "{$prefix}%")->delete();
            }
        }

        DB::table('sys_idempotency_keys')
            ->where(function ($q) {
                $q->where('idempotency_key', 'like', 'tamper-test-%')
                    ->orWhere('idempotency_key', 'like', 'test-rnote-%');
            })
            ->delete();

        RecruiterNoteCircuitBreaker::reset();
        SuperAdminRecruiterNoteCacheManager::flushCaches();
    }

    /**
     * Test 1: Monotonic Aggregate Versioning increments sequentially on consecutive mutations.
     */
    public function test_monotonic_aggregate_versioning(): void
    {
        $mutationService = app(SuperAdminRecruiterNoteMutationService::class);

        $payload = [
            'title' => 'Enterprise Version Test '.uniqid(),
            'content' => '<p>Initial version 1 content</p>',
            'status' => 1,
        ];

        $res1 = $mutationService->createTemplate($payload);
        $templateId = (int) ($res1['template']->id ?? $res1['data']['template']->id ?? $res1['id']);
        $this->createdTemplateIds[] = $templateId;

        // Verify version 1 outbox event
        $event1 = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'RecruiterNoteTemplate')
            ->where('aggregate_id', $templateId)
            ->where('event_type', 'RECRUITER_NOTE_TEMPLATE_CREATED')
            ->first();

        $this->assertNotNull($event1);
        $this->assertEquals(1, $event1->aggregate_version);

        // Update 1 -> should create version 2
        $payloadUpdate1 = [
            'title' => 'Enterprise Version Test Updated '.uniqid(),
            'content' => '<p>Updated content version 2</p>',
            'status' => 1,
        ];
        $mutationService->updateTemplate($templateId, $payloadUpdate1);

        $event2 = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'RecruiterNoteTemplate')
            ->where('aggregate_id', $templateId)
            ->where('event_type', 'RECRUITER_NOTE_TEMPLATE_UPDATED')
            ->first();

        $this->assertNotNull($event2);
        $this->assertEquals(2, $event2->aggregate_version);

        // Status Toggle -> should create version 3
        $mutationService->toggleStatus($templateId);

        $event3 = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'RecruiterNoteTemplate')
            ->where('aggregate_id', $templateId)
            ->where('event_type', 'RECRUITER_NOTE_TEMPLATE_STATUS_TOGGLED')
            ->first();

        $this->assertNotNull($event3);
        $this->assertEquals(3, $event3->aggregate_version);
    }

    /**
     * Test 2: Outbox worker drops out-of-order stale events if Elasticsearch already contains newer version.
     */
    public function test_outbox_worker_drops_stale_out_of_order_event(): void
    {
        $elasticService = app(ElasticService::class);
        $indexName = defined('ELASTIC_RECRUITER_NOTE_MASTER')
            ? ELASTIC_RECRUITER_NOTE_MASTER
            : config('constants.ELASTIC_RECRUITER_NOTE_MASTER', 'local_recruiter_note_master');

        // Create base record in MySQL
        $templateId = DB::table('recruiter_note_master')->insertGetId([
            'company_id' => null,
            'title' => 'Stale Event Drop Test '.uniqid(),
            'content' => '<p>Stale event content</p>',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->createdTemplateIds[] = $templateId;

        // Manually prime Elasticsearch with version 3
        $elasticService->getClient()->index([
            'index' => $indexName,
            'id' => (string) $templateId,
            'refresh' => 'wait_for',
            'body' => [
                'id' => $templateId,
                'company_id' => null,
                'title' => 'Newer Version Title In ES',
                'content' => '<p>Version 3 content</p>',
                'status' => 1,
                'version' => 3,
                'created_at' => now()->toIso8601String(),
                'updated_at' => now()->toIso8601String(),
            ],
        ]);

        // Create a stale outbox event at version 1
        $staleEventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => 'stale_test_'.uniqid(),
            'aggregate_type' => 'RecruiterNoteTemplate',
            'aggregate_id' => $templateId,
            'aggregate_version' => 1, // Stale! (1 < 3)
            'event_type' => 'RECRUITER_NOTE_TEMPLATE_UPDATED',
            'payload' => json_encode(['id' => $templateId, 'title' => 'Old Stale Title', 'version' => 1]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);
        $this->createdOutboxIds[] = $staleEventId;

        // Process stale outbox event
        $job = new ProcessRecruiterNoteOutboxEventJob($staleEventId);
        $job->handle($elasticService);

        // Verify outbox event was completed and skipped overwrite
        $processedEvent = DB::table('sys_outbox_events')->where('id', $staleEventId)->first();
        $this->assertSame('COMPLETED', $processedEvent->status);
        $this->assertStringContainsString('Skipped: ES has newer document', (string) $processedEvent->last_error);

        // Verify ES document still has version 3 and was NOT overwritten
        $esDoc = $elasticService->getClient()->get([
            'index' => $indexName,
            'id' => (string) $templateId,
        ]);
        $this->assertEquals(3, (int) $esDoc['_source']['version']);
        $this->assertSame('Newer Version Title In ES', $esDoc['_source']['title']);
    }

    /**
     * Test 3: Version-aware deletion tombstones prevent resurrection of deleted templates.
     */
    public function test_version_aware_deletion_tombstone_prevents_resurrection(): void
    {
        $elasticService = app(ElasticService::class);
        $templateId = 999999 + rand(1000, 9999);

        // Set deletion tombstone at version 2
        SuperAdminRecruiterNoteCacheManager::setTombstone($templateId, 2);

        // Create an outbox event representing an older version 1 upsert
        $resurrectEventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => 'tombstone_test_'.uniqid(),
            'aggregate_type' => 'RecruiterNoteTemplate',
            'aggregate_id' => $templateId,
            'aggregate_version' => 1, // Older than tombstone v2!
            'event_type' => 'RECRUITER_NOTE_TEMPLATE_UPDATED',
            'payload' => json_encode(['id' => $templateId, 'title' => 'Ghost Resurrection', 'version' => 1]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);
        $this->createdOutboxIds[] = $resurrectEventId;

        // Process outbox event
        $job = new ProcessRecruiterNoteOutboxEventJob($resurrectEventId);
        $job->handle($elasticService);

        $processedEvent = DB::table('sys_outbox_events')->where('id', $resurrectEventId)->first();
        $this->assertSame('COMPLETED', $processedEvent->status);
        $this->assertStringContainsString('entity tombstoned at v2', (string) $processedEvent->last_error);
    }

    /**
     * Test 4: Idempotency tampering guard rejects key reuse with mismatched request payload (422).
     */
    public function test_idempotency_tampering_guard_rejects_mismatched_payload(): void
    {
        $this->actingAs($this->superAdmin);

        $idempotencyKey = 'tamper-test-'.uniqid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        // First legitimate request
        $res1 = $this->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->postJson(route('superadmin.recruiter-note.store'), [
                'title' => 'Original Payload Title '.uniqid(),
                'content' => '<p>Original payload content</p>',
                'status' => 1,
            ]);

        $res1->assertStatus(200);
        $templateId = (int) $res1->json('template.id');
        $this->createdTemplateIds[] = $templateId;

        // Second request with SAME idempotency key but MODIFIED payload
        $this->withExceptionHandling();
        $res2 = $this->withHeaders(['X-Idempotency-Key' => $idempotencyKey])
            ->postJson(route('superadmin.recruiter-note.store'), [
                'title' => 'Tampered Title Attack '.uniqid(),
                'content' => '<p>Different payload content attempting hijack</p>',
                'status' => 1,
            ]);

        // Must reject with 422 Unprocessable Entity
        $res2->assertStatus(422);
    }

    /**
     * Test 5: Circuit Breaker state machine transitions (CLOSED -> OPEN -> HALF_OPEN -> CLOSED).
     */
    public function test_circuit_breaker_complete_state_transitions(): void
    {
        RecruiterNoteCircuitBreaker::reset();
        $this->assertSame(RecruiterNoteCircuitBreaker::STATE_CLOSED, RecruiterNoteCircuitBreaker::getState());
        $this->assertTrue(RecruiterNoteCircuitBreaker::isAvailable());

        $fakeException = new \RuntimeException('Elasticsearch connection refused');

        // Record 1st and 2nd failure -> still CLOSED
        RecruiterNoteCircuitBreaker::recordFailure($fakeException);
        $this->assertSame(RecruiterNoteCircuitBreaker::STATE_CLOSED, RecruiterNoteCircuitBreaker::getState());
        $this->assertSame(1, RecruiterNoteCircuitBreaker::getFailures());

        RecruiterNoteCircuitBreaker::recordFailure($fakeException);
        $this->assertSame(RecruiterNoteCircuitBreaker::STATE_CLOSED, RecruiterNoteCircuitBreaker::getState());
        $this->assertSame(2, RecruiterNoteCircuitBreaker::getFailures());

        // Record 3rd failure -> trips to OPEN
        RecruiterNoteCircuitBreaker::recordFailure($fakeException);
        $this->assertSame(RecruiterNoteCircuitBreaker::STATE_OPEN, RecruiterNoteCircuitBreaker::getState());
        $this->assertFalse(RecruiterNoteCircuitBreaker::isAvailable());

        // Simulate recovery probe window expiration (31 seconds later)
        Cache::put('superadmin:circuit:rnote:last_failure', Carbon::now()->subSeconds(35)->toIso8601String(), 86400);

        // State should now evaluate to HALF_OPEN (probe permitted)
        $this->assertSame(RecruiterNoteCircuitBreaker::STATE_HALF_OPEN, RecruiterNoteCircuitBreaker::getState());
        $this->assertTrue(RecruiterNoteCircuitBreaker::isAvailable());

        // Probe succeeds -> transitions back to CLOSED
        RecruiterNoteCircuitBreaker::recordSuccess();
        $this->assertSame(RecruiterNoteCircuitBreaker::STATE_CLOSED, RecruiterNoteCircuitBreaker::getState());
        $this->assertTrue(RecruiterNoteCircuitBreaker::isAvailable());
        $this->assertSame(0, RecruiterNoteCircuitBreaker::getFailures());
    }

    /**
     * Test 6: Dead-letter outbox handling and safe replay CLI execution.
     */
    public function test_dead_letter_handling_and_safe_replay(): void
    {
        $deadLetterEventId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => 'dead_letter_test_'.uniqid(),
            'aggregate_type' => 'RecruiterNoteTemplate',
            'aggregate_id' => 88888,
            'aggregate_version' => 1,
            'event_type' => 'RECRUITER_NOTE_TEMPLATE_CREATED',
            'payload' => json_encode(['id' => 88888]),
            'status' => 'DEAD_LETTER',
            'attempts' => 3,
            'available_at' => now(),
            'created_at' => now(),
            'last_error' => 'Simulated unrecoverable failure',
        ]);
        $this->createdOutboxIds[] = $deadLetterEventId;

        // Replay dead letter events using Artisan command
        $exitCode = Artisan::call('recruiter-notes:process-outbox', [
            '--replay-dead-letter' => true,
        ]);
        $this->assertSame(0, $exitCode);

        // Verify status transitioned from DEAD_LETTER to either PENDING or COMPLETED
        $replayed = DB::table('sys_outbox_events')->where('id', $deadLetterEventId)->first();
        $this->assertContains($replayed->status, ['PENDING', 'COMPLETED']);
        $this->assertNull($replayed->last_error);
    }

    /**
     * Test 7: Rich-text XSS sanitization strips dangerous tags and javascript protocols.
     */
    public function test_rich_text_xss_sanitization(): void
    {
        $mutationService = app(SuperAdminRecruiterNoteMutationService::class);

        $dirtyHtml = '<p>Normal text</p><script>alert("XSS")</script><iframe src="evil.com"></iframe><a href="javascript:stealCookie()">Click here</a><img src="x" onerror="malicious()"/>';
        $cleaned = $mutationService->sanitizeHtml($dirtyHtml);

        $this->assertStringNotContainsString('<script', $cleaned);
        $this->assertStringNotContainsString('<iframe', $cleaned);
        $this->assertStringNotContainsString('javascript:', $cleaned);
        $this->assertStringNotContainsString('onerror=', $cleaned);
        $this->assertStringContainsString('<p>Normal text</p>', $cleaned);
        $this->assertStringContainsString('Click here', $cleaned);
    }

    /**
     * Test 8: Observability operational metrics output valid operational status.
     */
    public function test_observability_operational_metrics(): void
    {
        $readService = app(SuperAdminRecruiterNoteReadService::class);
        $metrics = $readService->getOperationalMetrics();

        $this->assertArrayHasKey('circuit_breaker', $metrics);
        $this->assertArrayHasKey('outbox', $metrics);
        $this->assertArrayHasKey('elasticsearch', $metrics);
        $this->assertArrayHasKey('cache', $metrics);

        $this->assertSame('CLOSED', $metrics['circuit_breaker']['state']);
        $this->assertTrue($metrics['circuit_breaker']['is_available']);
        $this->assertIsInt($metrics['outbox']['pending']);
        $this->assertIsInt($metrics['outbox']['completed']);
        $this->assertIsInt($metrics['elasticsearch']['sync_drift']);
    }

    /**
     * Test 9: Automated self-healing reconciliation command detects and heals drift.
     */
    public function test_automated_self_healing_reconcile_command(): void
    {
        $elasticService = app(ElasticService::class);
        $indexName = defined('ELASTIC_RECRUITER_NOTE_MASTER')
            ? ELASTIC_RECRUITER_NOTE_MASTER
            : config('constants.ELASTIC_RECRUITER_NOTE_MASTER', 'local_recruiter_note_master');

        // Create template in MySQL only (missing in ES)
        $driftId = DB::table('recruiter_note_master')->insertGetId([
            'company_id' => null,
            'title' => 'Reconciliation Drift Test '.uniqid(),
            'content' => '<p>Reconciliation test body</p>',
            'status' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $this->createdTemplateIds[] = $driftId;

        // Run reconcile with --fix
        $exitCode = Artisan::call('recruiter-notes:reconcile', ['--fix' => true]);
        $this->assertSame(0, $exitCode);

        // Verify ES document was automatically backfilled
        $existsInEs = $elasticService->getClient()->exists([
            'index' => $indexName,
            'id' => (string) $driftId,
        ]);
        $this->assertTrue((bool) $existsInEs);
    }
}
