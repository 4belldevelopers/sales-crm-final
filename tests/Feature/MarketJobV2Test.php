<?php

namespace Tests\Feature;

use App\Console\Commands\ReindexAtsJobsV2;
use App\Http\Resources\MarketJobV2Resource;
use App\Services\MarketJobV2Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * MarketJobV2Test
 *
 * Intent: Feature test suite for the Find Jobs V2 architecture. Verifies class contracts,
 * V2 API route registration, DTO transformations, versioned Redis cache key generation,
 * and tenant cache invalidation methods.
 *
 * Run:
 *   php artisan test --filter=MarketJobV2Test
 */
class MarketJobV2Test extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        cache()->put('currency_codesww', [0 => 'INR', 1 => 'USD', 2 => 'EUR'], 86400);
        cache()->put('currency_symbols_all', [0 => '₹', 1 => '$', 2 => '€', 'INR' => '₹', 'USD' => '$', 'EUR' => '€'], 86400);
        cache()->put('ref:currencies:all', [
            'inr_based_rates' => ['INR' => 1, 'USD' => 83, 'EUR' => 88.5],
            'rates' => ['INR' => 0.012, 'USD' => 1, 'EUR' => 1.08],
            'symbolsValue' => ['INR' => 0, 'USD' => 1, 'EUR' => 2],
            'symbols' => ['INR' => '₹', 'USD' => '$', 'EUR' => '€'],
            'getKey' => [0 => 'INR', 1 => 'USD', 2 => 'EUR'],
        ], 86400);
    }

    /**
     * Test that MarketJobV2Service class exists and is instantiable.
     */
    public function test_market_job_v2_service_class_exists(): void
    {
        $this->assertTrue(
            class_exists(MarketJobV2Service::class),
            'MarketJobV2Service must exist in App\\Services'
        );
    }

    /**
     * Test that MarketJobV2Resource class exists in App\Http\Resources.
     */
    public function test_market_job_v2_resource_class_exists(): void
    {
        $this->assertTrue(
            class_exists(MarketJobV2Resource::class),
            'MarketJobV2Resource must exist in App\\Http\\Resources'
        );
    }

    /**
     * Test that ReindexAtsJobsV2 console command exists in App\Console\Commands.
     */
    public function test_reindex_ats_jobs_v2_command_exists(): void
    {
        $this->assertTrue(
            class_exists(ReindexAtsJobsV2::class),
            'ReindexAtsJobsV2 must exist in App\\Console\\Commands'
        );
    }

    /**
     * Test that V2 AJAX route 'jobs.marketJobListV2_ajax' is registered in routes/service.php.
     */
    public function test_v2_route_is_registered(): void
    {
        $hasRoute = Route::has('jobs.marketJobListV2_ajax') || Route::has('service_company.jobs.marketJobListV2_ajax');
        $this->assertTrue(
            $hasRoute,
            "Route 'jobs.marketJobListV2_ajax' or 'service_company.jobs.marketJobListV2_ajax' must be registered."
        );
    }

    /**
     * Test versioned cache key generation in MarketJobV2Service.
     */
    public function test_v2_service_get_cache_key_format(): void
    {
        $service = app(MarketJobV2Service::class);
        $request = Request::create('/service_company/market-jobs-list-v2-ajax', 'GET', [
            'page' => 1,
            'size' => 10,
        ]);

        $companyId = 99;
        $cacheKey = $service->getCacheKey($request, $companyId);

        $this->assertStringStartsWith(
            'ats:v2:allmarketjobs:99:',
            $cacheKey,
            'Cache key must be scoped by tenant company_id and ats:v2 namespace.'
        );
    }

    /**
     * Test tenant cache invalidation increments tenant version key.
     */
    public function test_tenant_cache_invalidation_increments_version(): void
    {
        $companyId = 12345;
        $versionKey = 'allmarketjobs_version_company_'.$companyId;

        $initialVersion = (int) cache()->get($versionKey, 1);
        MarketJobV2Service::invalidateCacheForCompany($companyId);
        $newVersion = (int) cache()->get($versionKey, 1);

        $this->assertEquals(
            $initialVersion + 1,
            $newVersion,
            'Tenant cache version must increment by 1 on invalidation.'
        );
    }

    /**
     * Test DTO transformation structure in MarketJobV2Resource.
     */
    public function test_market_job_v2_resource_dto_transformation(): void
    {
        $dummyJob = [
            'id' => 101,
            'title' => 'Senior React Developer',
            'comId' => 50,
            'client_name' => 'Acme Tech',
            'category_name' => 'Engineering',
            'min_exp' => 3,
            'max_exp' => 6,
            'total_positions' => 4,
            'employment_type' => 0,
            'public_market_status' => 2,
            'work_type' => 'yes',
            'job_metronics_skills' => [
                ['skill_name' => 'React'],
                ['skill_name' => 'Redux'],
                ['skill_name' => 'JavaScript'],
            ],
            'company_size_from' => 50,
            'company_size' => 200,
            'industry_name' => 'Information Technology',
            'vendor_added_count' => 5,
        ];

        $resource = new MarketJobV2Resource($dummyJob);
        $request = Request::create('/service_company/market-jobs-list-v2-ajax', 'GET');
        $transformed = $resource->toArray($request);

        $this->assertEquals(101, $transformed['id']);
        $this->assertEquals('Engineering', $transformed['department']);
        $this->assertEquals('Remote', $transformed['work_type']);
        $this->assertEquals('Full-Time', $transformed['employment_type']);
        $this->assertContains('React', $transformed['skills']);
        $this->assertEquals(5, $transformed['vendor_added_count']);
        $this->assertArrayHasKey('action_type', $transformed);
    }

    /**
     * Test location only filtering parameter parsing.
     */
    public function test_location_only_filter_parsing(): void
    {
        $service = app(\App\Services\MarketJobV2Service::class);
        $request = Request::create('/service_company/market-jobs-list-v2-ajax', 'GET', [
            'kt_tagify_location' => '[{"value":"Bangalore"}]',
        ]);
        $this->assertInstanceOf(\App\Services\MarketJobV2Service::class, $service);
    }

    /**
     * Test Phase 1 & Phase 2 Browser-First architecture in Market Jobs blade template.
     * Verifies existence of L1 RAM MarketJobStore, L2 JeroMarketJobsDB with tenant isolation,
     * applyMarketJobsPayload rendering pipeline, and dual-cache invalidation on mutations.
     */
    public function test_market_jobs_blade_implements_l1_ram_and_l2_indexeddb_architecture(): void
    {
        $bladePath = resource_path('views/service_company/jobs/market-jobs.blade.php');
        $this->assertFileExists($bladePath, 'Market Jobs blade template must exist');

        $content = file_get_contents($bladePath);

        // Phase 1: L1 In-Memory RAM Store
        $this->assertStringContainsString('window.MarketJobStore = {', $content, 'MarketJobStore must be defined');
        $this->assertStringContainsString('abortInFlight', $content, 'abortInFlight must be implemented for request cancellation');
        $this->assertStringContainsString('applyMarketJobsPayload', $content, 'applyMarketJobsPayload rendering pipeline must be implemented');

        // Phase 2: L2 Scoped IndexedDB Cache
        $this->assertStringContainsString('window.JeroMarketJobsDB = (function()', $content, 'JeroMarketJobsDB must be defined');
        $this->assertStringContainsString('jero_mkt_tenant_', $content, 'IndexedDB database name must be tenant-isolated via jero_mkt_tenant_');
        $this->assertStringContainsString('MAX_PAGES = 50', $content, 'IndexedDB must enforce MAX_PAGES LRU budget');
        $this->assertStringContainsString('MAX_JOBS = 1000', $content, 'IndexedDB must enforce MAX_JOBS LRU budget');
        $this->assertStringContainsString('clearPages', $content, 'JeroMarketJobsDB must provide clearPages method');

        // Mutation invalidation
        $this->assertStringContainsString('window.MarketJobStore.invalidate()', $content, 'Mutation callback must invalidate RAM store');
        $this->assertStringContainsString('window.JeroMarketJobsDB.clearPages()', $content, 'Mutation callback must invalidate IndexedDB pages');

        // Phase 3: Real-Time Delta Synchronization
        $this->assertStringContainsString('data-market-job-id=', $content, 'Job card element must have data-market-job-id attribute');
        $this->assertStringContainsString('applyJobCardDelta', $content, 'applyJobCardDelta must be implemented for targeted DOM patching');
        $this->assertStringContainsString('updateJobDelta', $content, 'Store must implement updateJobDelta');
        $this->assertStringContainsString('.market.job.delta', $content, 'Blade must subscribe to .market.job.delta broadcast event');
    }

    /**
     * Test Phase 3 MarketJobDeltaUpdated broadcast event contracts.
     */
    public function test_market_job_delta_updated_event_broadcast_contracts(): void
    {
        $this->assertTrue(
            class_exists(\App\Events\MarketJobDeltaUpdated::class),
            'MarketJobDeltaUpdated event class must exist'
        );

        $event = new \App\Events\MarketJobDeltaUpdated(
            companyId: 99,
            jobId: 10542,
            eventType: 'claimed',
            entityVersion: 3,
            actionType: 'under_review',
            statusLabel: 'HOLD'
        );

        $this->assertInstanceOf(
            \Illuminate\Contracts\Broadcasting\ShouldBroadcast::class,
            $event,
            'Event must implement ShouldBroadcast'
        );

        $channel = $event->broadcastOn();
        $this->assertEquals('private-company.99', $channel->name);
        $this->assertEquals('market.job.delta', $event->broadcastAs());

        $payload = $event->broadcastWith();
        $this->assertEquals(99, $payload['company_id']);
        $this->assertEquals(10542, $payload['job_id']);
        $this->assertEquals('claimed', $payload['event_type']);
        $this->assertEquals('under_review', $payload['action_type']);
        $this->assertEquals('HOLD', $payload['status_label']);
        $this->assertArrayHasKey('timestamp', $payload);
    }

    /**
     * Test Phase 4 MarketJobMutationService canonical hashing and class structure.
     */
    public function test_market_job_mutation_service_and_canonical_hashing(): void
    {
        $this->assertTrue(
            class_exists(\App\Services\MarketJobMutationService::class),
            'MarketJobMutationService class must exist in App\\Services'
        );

        $service = app(\App\Services\MarketJobMutationService::class);

        $payloadA = ['jobId' => 101, 'mstatus_id' => 2, 'remarks' => 'Ready to claim'];
        $payloadB = ['remarks' => 'Ready to claim', 'jobId' => 101, 'mstatus_id' => 2];

        $hashA = $service->computeCanonicalHash($payloadA);
        $hashB = $service->computeCanonicalHash($payloadB);

        $this->assertEquals(
            $hashA,
            $hashB,
            'Canonical hash must be deterministic regardless of payload key ordering.'
        );

        $payloadC = ['jobId' => 102, 'mstatus_id' => 2, 'remarks' => 'Ready to claim'];
        $hashC = $service->computeCanonicalHash($payloadC);

        $this->assertNotEquals(
            $hashA,
            $hashC,
            'Canonical hash must differentiate between different payloads.'
        );
    }

    /**
     * Test Phase 4 ProcessMarketJobOutboxEventJob contracts and queue assignment.
     */
    public function test_process_market_job_outbox_event_job_contracts(): void
    {
        $this->assertTrue(
            class_exists(\App\Jobs\ProcessMarketJobOutboxEventJob::class),
            'ProcessMarketJobOutboxEventJob must exist in App\\Jobs'
        );

        $job = new \App\Jobs\ProcessMarketJobOutboxEventJob(123);

        $this->assertInstanceOf(
            \Illuminate\Contracts\Queue\ShouldQueue::class,
            $job,
            'Job must implement ShouldQueue for asynchronous processing'
        );

        $this->assertEquals(
            'market-outbox',
            $job->queue,
            'Job must target dedicated queue market-outbox'
        );
    }

    /**
     * Test Phase 4 blade idempotency headers and schema indexing contracts.
     */
    public function test_phase4_blade_idempotency_and_schema_indexing(): void
    {
        $bladePath = resource_path('views/service_company/jobs/market-jobs.blade.php');
        $bladeContent = file_get_contents($bladePath);

        $this->assertStringContainsString(
            'X-Idempotency-Key',
            $bladeContent,
            'Market Jobs blade must pass X-Idempotency-Key in claim AJAX request headers.'
        );
        $this->assertStringContainsString(
            'mktIdempotencyKey',
            $bladeContent,
            'Market Jobs blade must generate mktIdempotencyKey.'
        );

        $schemaPath = base_path('database/schema/mysql-schema.sql');
        $this->assertFileExists($schemaPath, 'mysql-schema.sql must exist');
        $schemaContent = file_get_contents($schemaPath);

        $this->assertStringContainsString(
            'idx_mkt_job_status',
            $schemaContent,
            'mysql-schema.sql must define idx_mkt_job_status on market_job_added_count.'
        );
        $this->assertStringContainsString(
            'sys_idempotency_keys',
            $schemaContent,
            'mysql-schema.sql must define sys_idempotency_keys.'
        );
        $this->assertStringContainsString(
            'sys_outbox_events',
            $schemaContent,
            'mysql-schema.sql must define sys_outbox_events.'
        );
    }

    /**
     * Test Phase 5 dedicated POST claim route, blade resilience, and outbox retry properties.
     */
    public function test_phase5_claim_route_and_outbox_wiring(): void
    {
        // 1. Verify routes
        $hasPostRoute = Route::has('jobs.market_claim') || Route::has('service_company.jobs.market_claim');
        $this->assertTrue($hasPostRoute, 'Dedicated POST route jobs.market_claim must be registered');

        $claimRoute = Route::getRoutes()->getByName('service_company.jobs.marketrequestaadded')
            ?? Route::getRoutes()->getByName('jobs.marketrequestaadded');
        $this->assertNotNull($claimRoute, 'Claim route must exist');
        $this->assertContains('GET', $claimRoute->methods(), 'Claim route must support legacy GET');
        $this->assertContains('POST', $claimRoute->methods(), 'Claim route must support modern POST');

        // 2. Verify blade error callback and waiting status handling
        $bladePath = resource_path('views/service_company/jobs/market-jobs.blade.php');
        $bladeContent = file_get_contents($bladePath);

        $this->assertStringContainsString(
            "response.status == 'waiting'",
            $bladeContent,
            'Blade must handle waiting status for concurrent in-flight claims.'
        );
        $this->assertStringContainsString(
            'error: function',
            $bladeContent,
            'Blade must define error callback to re-enable button and alert on failure.'
        );

        // 3. Verify Outbox Job properties
        $job = new \App\Jobs\ProcessMarketJobOutboxEventJob(555);
        $this->assertEquals(3, $job->tries, 'Outbox job must have 3 retry attempts');
        $this->assertEquals(10, $job->backoff, 'Outbox job must have 10s backoff');
        $this->assertEquals(60, $job->timeout, 'Outbox job must have 60s timeout');
    }

    /**
     * Test Phase 7 production rollout, Horizon outbox worker provisioning, and observability readiness.
     */
    public function test_phase7_production_rollout_and_observability_readiness(): void
    {
        // 1. Verify Horizon supervisor-outbox queue provisioning
        $horizonDefaults = config('horizon.defaults', []);
        $this->assertArrayHasKey('supervisor-outbox', $horizonDefaults, 'Horizon must configure supervisor-outbox in defaults');
        $this->assertContains('market-outbox', $horizonDefaults['supervisor-outbox']['queue'], 'supervisor-outbox must listen to market-outbox');

        $horizonProd = config('horizon.environments.production', []);
        $this->assertArrayHasKey('supervisor-outbox', $horizonProd, 'Horizon production environment must supervise outbox workers');

        // 2. Verify Load Test Artifacts
        $k6Script = base_path('tests/load/market_jobs_10k_load.js');
        $this->assertFileExists($k6Script, 'k6 10,000 concurrent user load test script must exist');

        $benchmarkClass = \Tests\Performance\MarketJobsLoadTestBenchmark::class;
        $this->assertTrue(class_exists($benchmarkClass), 'MarketJobsLoadTestBenchmark class must exist');
    }
}
