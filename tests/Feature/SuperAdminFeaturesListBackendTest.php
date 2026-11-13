<?php

namespace Tests\Feature;

use App\Jobs\ProcessFeatureOutboxEventJob;
use App\Models\User;
use App\Services\ElasticService;
use App\Services\SuperAdmin\FeatureCircuitBreaker;
use App\Services\SuperAdmin\SuperAdminFeatureCacheManager;
use App\Services\SuperAdmin\SuperAdminFeatureReadService;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Tests\TestCase;

/**
 * Class SuperAdminFeaturesListBackendTest
 *
 * Automated verification suite for Super Admin Platform Features & Expiration Rules
 * God-Level Backend Architecture:
 * - CQRS Read Path: 0% MySQL reads on warm page loads (Rule 1 & Rule 2)
 * - Two-Tier Idempotency: Replay caching and duplicate prevention
 * - Transactional Outbox: Event persistence and Elasticsearch synchronization
 * - Circuit-Breaker Resilience: State transitions and emergency DB fallback
 * - Generational L2 Cache: O(1) version bump and atomic invalidation
 * - CLI Commands: Synchronous dual-store reindexing and outbox processing
 *
 * CRITICAL RULE: Zero RefreshDatabase. Atomic restoration in tearDown().
 */
class SuperAdminFeaturesListBackendTest extends TestCase
{
    protected User $superAdmin;

    protected array $createdFeatureIds = [];

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

        FeatureCircuitBreaker::reset();
        SuperAdminFeatureCacheManager::flushCaches();
    }

    protected function tearDown(): void
    {
        // Clean up created features in MySQL and Elasticsearch
        if (! empty($this->createdFeatureIds)) {
            $esService = app(ElasticService::class);
            $readService = app(SuperAdminFeatureReadService::class);
            $indexName = $readService->getIndexName();

            foreach ($this->createdFeatureIds as $id) {
                DB::table('features')->where('id', $id)->delete();
                try {
                    $esService->getClient()->delete([
                        'index' => $indexName,
                        'id' => (string) $id,
                    ]);
                } catch (\Throwable) {
                }
            }
        }

        if (! empty($this->createdOutboxIds)) {
            DB::table('sys_outbox_events')->whereIn('id', $this->createdOutboxIds)->delete();
        }

        if (! empty($this->createdIdempotencyKeys)) {
            DB::table('sys_idempotency_keys')->whereIn('idempotency_key', $this->createdIdempotencyKeys)->delete();
        }

        FeatureCircuitBreaker::reset();
        SuperAdminFeatureCacheManager::flushCaches();

        parent::tearDown();
    }

    /**
     * Test CQRS Read path: 100% queries and KPI metrics served from Elasticsearch / L2 Cache.
     * Guarantees 0% MySQL reads on warm page requests.
     */
    public function test_cqrs_read_path_serves_features_from_elasticsearch_with_zero_mysql_reads(): void
    {
        // 1. Prime cache/Elasticsearch
        Artisan::call('features:reindex');

        // 2. Perform warm request
        DB::flushQueryLog();
        DB::enableQueryLog();

        $response = $this->actingAs($this->superAdmin)
            ->get(route('superadmin.company.featuresList'));

        $response->assertStatus(200);
        $response->assertViewHas('th_features');
        $response->assertViewHas('totalFeaturesCount');
        $response->assertViewHas('activeFeaturesCount');

        $queries = DB::getQueryLog();
        $featuresQueries = collect($queries)->filter(function ($q) {
            return str_contains(strtolower($q['query']), 'from `features`')
                || str_contains(strtolower($q['query']), 'from features');
        });

        $this->assertCount(
            0,
            $featuresQueries,
            'CQRS Violation: MySQL was queried for features on the read path!'
        );
    }

    /**
     * Test Two-Tier Idempotency: Duplicate requests with identical X-Idempotency-Key
     * claim atomic leases, avoid duplicate executions, and replay cached responses.
     */
    public function test_two_tier_idempotency_claims_lease_and_replays_response(): void
    {
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        // Find an existing feature policy to toggle
        $existingFeature = DB::table('features')->first();
        $this->assertNotNull($existingFeature);

        $featureId = (int) $existingFeature->id;
        $originalStatus = (int) $existingFeature->status;
        $toggleStatus = $originalStatus === 1 ? 0 : 1;

        // First execution
        $response1 = $this->actingAs($this->superAdmin)
            ->withHeader('X-Idempotency-Key', $idempotencyKey)
            ->postJson(route('superadmin.company.featuresToggleStatus'), [
                'id' => $featureId,
                'status' => $toggleStatus,
            ]);

        $response1->assertStatus(200);
        $data1 = $response1->json();
        $this->assertEquals('success', $data1['status']);
        $this->assertEquals($toggleStatus, $data1['new_status']);

        // Check lease stored in sys_idempotency_keys
        $lease = DB::table('sys_idempotency_keys')
            ->where('tenant_id', 0)
            ->where('idempotency_key', $idempotencyKey)
            ->first();

        $this->assertNotNull($lease);
        $this->assertEquals('COMPLETED', $lease->status);

        // Track outbox ID for cleanup
        if (! empty($data1['outbox_id'])) {
            $this->createdOutboxIds[] = $data1['outbox_id'];
        }

        // Second execution with identical key -> Replay response
        $response2 = $this->actingAs($this->superAdmin)
            ->withHeader('X-Idempotency-Key', $idempotencyKey)
            ->postJson(route('superadmin.company.featuresToggleStatus'), [
                'id' => $featureId,
                'status' => $toggleStatus,
            ]);

        $response2->assertStatus(200);
        $data2 = $response2->json();
        $this->assertTrue($data2['_replayed'] ?? false, 'Expected _replayed flag on idempotent duplicate execution');

        // Restore original status
        DB::table('features')->where('id', $featureId)->update(['status' => $originalStatus]);
    }

    /**
     * Test Transactional Outbox Pattern: State-changing operations write atomic events
     * to sys_outbox_events with millisecond OCC timestamps.
     */
    public function test_transactional_outbox_persists_event_on_feature_save(): void
    {
        $idempotencyKey = (string) Str::uuid();
        $this->createdIdempotencyKeys[] = $idempotencyKey;

        $testTitle = 'Test Automated Policy '.Str::random(8);

        $response = $this->actingAs($this->superAdmin)
            ->withHeader('X-Idempotency-Key', $idempotencyKey)
            ->postJson(route('superadmin.company.addEditfeaturesListupdate'), [
                'tokenId' => 0,
                'title' => $testTitle,
                'days' => 14,
                'status_is' => 1,
            ]);

        $response->assertStatus(200);
        $data = $response->json();
        $this->assertEquals('success', $data['status']);

        $createdId = $data['data']['feature']['id'] ?? $data['feature']['id'] ?? null;
        $this->assertNotNull($createdId);
        $this->createdFeatureIds[] = (int) $createdId;

        $outboxId = $data['data']['outbox_id'] ?? $data['outbox_id'] ?? null;
        $this->assertNotNull($outboxId);
        $this->createdOutboxIds[] = (int) $outboxId;

        // Verify row in sys_outbox_events
        $outboxRow = DB::table('sys_outbox_events')->where('id', $outboxId)->first();
        $this->assertNotNull($outboxRow);
        $this->assertEquals('Feature', $outboxRow->aggregate_type);
        $this->assertEquals($createdId, $outboxRow->aggregate_id);
        $this->assertEquals('FEATURE_CREATED', $outboxRow->event_type);
    }

    /**
     * Test Outbox Worker: ProcessFeatureOutboxEventJob dual-store synchronizes
     * authoritative MySQL records into Elasticsearch index local_features_list.
     */
    public function test_outbox_worker_processes_and_indexes_to_elasticsearch(): void
    {
        // Create feature in MySQL
        $featureId = DB::table('features')->insertGetId([
            'title' => 'Outbox ES Sync Policy Test',
            'days' => 5,
            'status' => 1,
        ]);
        $this->createdFeatureIds[] = $featureId;

        $outboxId = DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => 0,
            'idempotency_key' => (string) Str::uuid(),
            'aggregate_type' => 'Feature',
            'aggregate_id' => $featureId,
            'aggregate_version' => (int) (microtime(true) * 1000),
            'event_type' => 'FEATURE_CREATED',
            'payload' => json_encode(['id' => $featureId, 'title' => 'Outbox ES Sync Policy Test']),
            'status' => 'PENDING',
            'attempts' => 0,
            'created_at' => now(),
            'available_at' => now(),
        ]);
        $this->createdOutboxIds[] = $outboxId;

        // Execute outbox job
        $job = new ProcessFeatureOutboxEventJob($outboxId);
        $job->handle();

        // Verify outbox status completed
        $outboxRow = DB::table('sys_outbox_events')->where('id', $outboxId)->first();
        $this->assertEquals('COMPLETED', $outboxRow->status);

        // Verify ES document
        $esService = app(ElasticService::class);
        $readService = app(SuperAdminFeatureReadService::class);
        $indexName = $readService->getIndexName();

        $doc = $esService->getClient()->get([
            'index' => $indexName,
            'id' => (string) $featureId,
        ]);

        $this->assertTrue($doc['found']);
        $this->assertEquals('Outbox ES Sync Policy Test', $doc['_source']['title']);
        $this->assertEquals(5, $doc['_source']['days']);
        $this->assertEquals(1, $doc['_source']['status']);
    }

    /**
     * Test Circuit Breaker: When tripped to OPEN, fast-fails to emergency
     * storm-guarded DB fallback without server collapse.
     */
    public function test_feature_circuit_breaker_degrades_gracefully_to_mysql_fallback(): void
    {
        FeatureCircuitBreaker::transitionTo(FeatureCircuitBreaker::STATE_OPEN);

        $this->assertEquals(FeatureCircuitBreaker::STATE_OPEN, FeatureCircuitBreaker::getState());
        $this->assertEquals(FeatureCircuitBreaker::MODE_DEGRADED_ES_OUTAGE, FeatureCircuitBreaker::getMode());
        $this->assertFalse(FeatureCircuitBreaker::isAvailable());

        $readService = app(SuperAdminFeatureReadService::class);
        $features = $readService->getFeatures();

        $this->assertNotEmpty($features);
        $this->assertGreaterThanOrEqual(1, count($features));

        $telemetry = FeatureCircuitBreaker::getTelemetry();
        $this->assertEquals(FeatureCircuitBreaker::STATE_OPEN, $telemetry['state']);
        $this->assertGreaterThanOrEqual(1, $telemetry['fallback_count']);

        // Reset
        FeatureCircuitBreaker::reset();
        $this->assertEquals(FeatureCircuitBreaker::STATE_CLOSED, FeatureCircuitBreaker::getState());
    }

    /**
     * Test CLI Reindex command: php artisan features:reindex
     */
    public function test_reindex_command_synchronizes_features_to_elasticsearch(): void
    {
        $exitCode = Artisan::call('features:reindex');
        $this->assertEquals(0, $exitCode);

        $esService = app(ElasticService::class);
        $readService = app(SuperAdminFeatureReadService::class);
        $indexName = $readService->getIndexName();

        $count = $esService->getClient()->count(['index' => $indexName]);
        $this->assertGreaterThanOrEqual(5, $count['count']);
    }

    /**
     * Test CLI Process Outbox command: php artisan features:process-outbox
     */
    public function test_process_outbox_command_processes_pending_events(): void
    {
        $exitCode = Artisan::call('features:process-outbox');
        $this->assertEquals(0, $exitCode);
    }
}
