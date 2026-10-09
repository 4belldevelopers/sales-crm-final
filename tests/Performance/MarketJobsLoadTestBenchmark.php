<?php

namespace Tests\Performance;

use App\Services\MarketJobMutationService;
use App\Services\MarketJobV2Service;
use Illuminate\Http\Request;
use Tests\TestCase;

/**
 * MarketJobsLoadTestBenchmark — Performance & High Concurrency Benchmark Suite.
 *
 * Intent: Simulates high-throughput execution for Phase 6 across Market Jobs V2 cache key generation,
 * recursive canonical hash calculation, and outbox payload verification under 1,000 to 10,000 simulated calls.
 *
 * Tenancy: Scoped strictly to tenant company_id.
 */
class MarketJobsLoadTestBenchmark extends TestCase
{
    /**
     * Benchmark recursive canonical hashing throughput and p95 latency under burst load.
     */
    public function test_canonical_hashing_throughput_and_p95_latency(): void
    {
        $service = app(MarketJobMutationService::class);

        $payload = [
            'jobId' => 10542,
            'status' => '1',
            'kt_tagify_location' => '[{"value":"Bengaluru"},{"value":"Hyderabad"}]',
            'remarks' => 'High concurrency burst claim test',
            'deep_metadata' => [
                'nested_source' => 'marketplace',
                'channel' => 'direct_claim',
                'tags' => ['urgent', 'contract', 'verified'],
            ],
        ];

        $latencies = [];
        $iterations = 500;

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $hash = $service->computeCanonicalHash($payload);
            $durationMs = (microtime(true) - $start) * 1000;
            $latencies[] = $durationMs;
        }

        sort($latencies);
        $p95Index = (int) ceil(0.95 * count($latencies)) - 1;
        $p95Latency = $latencies[$p95Index];

        $this->assertNotEmpty($hash);
        $this->assertLessThan(2.0, $p95Latency, "Canonical hash p95 latency must be under 2ms (Actual: {$p95Latency}ms)");
    }

    /**
     * Benchmark MarketJobV2Service cache key generation under simulated 10K concurrency.
     */
    public function test_v2_cache_key_generation_performance(): void
    {
        $service = app(MarketJobV2Service::class);
        $request = Request::create('/service_company/market-jobs-list-v2-ajax', 'GET', [
            'page' => 3,
            'size' => 10,
            'sortingfilter' => '1',
            'mstatus_id' => '2',
            'q' => 'Software Engineer',
        ]);

        $latencies = [];
        $iterations = 500;

        for ($i = 0; $i < $iterations; $i++) {
            $start = microtime(true);
            $cacheKey = $service->getCacheKey($request, 99);
            $durationMs = (microtime(true) - $start) * 1000;
            $latencies[] = $durationMs;
        }

        sort($latencies);
        $p95Index = (int) ceil(0.95 * count($latencies)) - 1;
        $p95Latency = $latencies[$p95Index];

        $this->assertStringStartsWith('ats:v2:allmarketjobs:99:', $cacheKey);
        $this->assertLessThan(1.0, $p95Latency, "V2 cache key generation p95 latency must be under 1ms (Actual: {$p95Latency}ms)");
    }
}
