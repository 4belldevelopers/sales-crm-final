<?php
// Intent: Step 18 chaos resilience tests verifying counter fallback chains
// under MySQL replica, Redis, Elasticsearch, and Reverb outage scenarios.
//
// Each test follows: GIVEN (fault injected) → WHEN (service called) → THEN (graceful response).
// Chaos scenarios map to 18_STEP_IMPLEMENTATION_PLAN.md Step 18.

namespace Tests\Chaos;

use App\Events\StageMoved;
use App\Services\Apptracker\CounterService;
use App\Services\ElasticService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\MockInterface;
use PDOException;
use Tests\TestCase;

/**
 * ApptrackerResilienceTest — Chaos / Fault-injection suite (Step 18 of 18).
 *
 * Covers four outage scenarios:
 *   Chaos-1: MySQL replica outage     → fallback chain reaches Elasticsearch.
 *   Chaos-2: Redis (full) outage      → Cache::flexible throws → exception or safe zeros.
 *   Chaos-3: Reverb WebSocket outage  → broadcast failure does not break cache bust.
 *   Chaos-4: Optimistic UI / network  → JS layer (documented as Dusk requirement).
 */
class ApptrackerResilienceTest extends TestCase
{
    /* ── Helpers ─────────────────────────────────────────────────────── */

    /** Build a CounterService with a controllable ElasticService mock. */
    private function makeService(MockInterface $elasticMock): CounterService
    {
        return new CounterService($elasticMock);
    }

    /**
     * Return an ElasticService mock whose getClient()->search() returns $payload.
     * Uses an anonymous client mock (no ES class import) since getClient() is untyped.
     */
    private function mockEs(array $payload): MockInterface
    {
        $clientMock = Mockery::mock();
        $clientMock->shouldReceive('search')->andReturn($payload);

        $elasticMock = Mockery::mock(ElasticService::class);
        $elasticMock->shouldReceive('getClient')->andReturn($clientMock);

        return $elasticMock;
    }

    /** Return an ElasticService mock whose getClient()->search() throws. */
    private function mockEsDown(): MockInterface
    {
        $clientMock = Mockery::mock();
        $clientMock->shouldReceive('search')
            ->andThrow(new \RuntimeException('Elasticsearch unreachable'));

        $elasticMock = Mockery::mock(ElasticService::class);
        $elasticMock->shouldReceive('getClient')->andReturn($clientMock);

        return $elasticMock;
    }

    /** Canonical fake ES aggregation payload with known counts. */
    private function fakeEsAggResponse(): array
    {
        return [
            'aggregations' => [
                'allCandidate' => ['value'     => 42],
                'Sourcing'     => ['doc_count' => 10],
                'Screening'    => ['doc_count' => 8],
                'Interview'    => ['doc_count' => 7],
                'Negotiation'  => ['doc_count' => 6],
                'Offered'      => ['doc_count' => 5],
                'Joined'       => ['doc_count' => 4],
                'Rejected'     => ['doc_count' => 2],
            ],
        ];
    }

    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    /* ── Chaos-1: MySQL Replica Outage ──────────────────────────────── */

    /**
     * @test
     * Chaos-1a: When mysql_replica throws inside the lock block, CounterService
     * catches it and falls back to Elasticsearch.
     * Pipeline: Redis cache MISS → lock acquired → DB replica THROWS → ES hit.
     */
    public function it_falls_back_to_elasticsearch_when_mysql_replica_throws(): void
    {
        $esPayload = $this->fakeEsAggResponse();

        // Simulate Cache miss: flexible cache calls callback immediately.
        Cache::shouldReceive('flexible')
            ->once()
            ->andReturnUsing(fn ($key, $ttl, $callback) => $callback());

        // Simulate lock acquired but replica DB query throws inside the callback.
        $lockMock = Mockery::mock();
        $lockMock->shouldReceive('block')
            ->andThrow(new QueryException(
                'mysql_replica',
                'select * from `pipeline_counters`',
                [],
                new PDOException('SQLSTATE[HY000]: General error: 2006 MySQL server has gone away')
            ));
        Cache::shouldReceive('lock')->andReturn($lockMock);

        $elasticMock = $this->mockEs($esPayload);
        $service     = $this->makeService($elasticMock);

        $result = $service->load(999, 1);

        $this->assertSame(42, $result['allCandidate'], 'allCandidate must come from ES fallback');
        $this->assertSame(10, $result['Sourcing']);
        $this->assertSame(8,  $result['Screening']);
        $this->assertSame(7,  $result['Interview']);
    }

    /**
     * @test
     * Chaos-1b: When mysql_replica returns null (pipeline_counters not yet seeded
     * by counters:reconcile on cold start), falls through to Elasticsearch.
     * Pipeline: Redis cache MISS → lock acquired → DB row NULL → ES hit.
     */
    public function it_falls_back_to_elasticsearch_when_pipeline_counters_row_is_absent(): void
    {
        $esPayload = $this->fakeEsAggResponse();

        Cache::shouldReceive('flexible')
            ->once()
            ->andReturnUsing(fn ($key, $ttl, $callback) => $callback());

        $lockMock = Mockery::mock();
        $lockMock->shouldReceive('block')
            ->andReturnUsing(function ($timeout, $callback) {
                // DB replica query returns null → falls through to ES inside callback.
                DB::shouldReceive('connection')->with('mysql_replica')->andReturnSelf();
                DB::shouldReceive('table')->andReturnSelf();
                DB::shouldReceive('where')->andReturnSelf();
                DB::shouldReceive('whereNotNull')->andReturnSelf();
                DB::shouldReceive('first')->andReturn(null);

                return $callback();
            });
        Cache::shouldReceive('lock')->andReturn($lockMock);

        $elasticMock = $this->mockEs($esPayload);
        $service     = $this->makeService($elasticMock);

        $result = $service->load(999, 1);

        $this->assertSame(42, $result['allCandidate']);
    }

    /**
     * @test
     * Chaos-1c: loadcounter with inputSearch bypasses cache and DB entirely;
     * always hits ES live — confirming the search-filter fast path is unaffected
     * by any replica outage.
     */
    public function it_bypasses_cache_and_db_when_input_search_is_set(): void
    {
        $esPayload   = $this->fakeEsAggResponse();
        $elasticMock = $this->mockEs($esPayload);
        $service     = $this->makeService($elasticMock);

        // Cache::flexible must NOT be called on the search path.
        Cache::shouldReceive('flexible')->never();

        $result = $service->load(999, 1, 'john doe');

        $this->assertSame(42, $result['allCandidate'], 'ES data surfaced on search path');
    }

    /* ── Chaos-2: Redis Outage ───────────────────────────────────────── */

    /**
     * @test
     * Chaos-2a: When Cache::flexible itself throws (Redis fully down),
     * the exception propagates to the caller (controller).
     *
     * Mitigation: wrap loadcounter() in the controller with try-catch → safe zeros.
     * See docs/CHAOS_RUNBOOK.md §Chaos-2 for the hardening action item.
     */
    public function it_propagates_exception_when_redis_is_completely_unreachable(): void
    {
        Cache::shouldReceive('flexible')
            ->once()
            ->andThrow(new \RuntimeException('Connection refused (redis)'));

        $elasticMock = Mockery::mock(ElasticService::class);
        $elasticMock->shouldReceive('getClient')->never();

        $service = $this->makeService($elasticMock);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessageMatches('/Connection refused/');

        $service->load(999, 1);
    }

    /**
     * @test
     * Chaos-2b: When Cache::lock()->block() times out (Redis overloaded, not fully down),
     * the catch(\Throwable) in CounterService falls back to ES.
     * Pipeline: flexible cache MISS → lock timeout → ES hit.
     */
    public function it_falls_back_to_elasticsearch_when_redis_lock_times_out(): void
    {
        $esPayload = $this->fakeEsAggResponse();

        Cache::shouldReceive('flexible')
            ->once()
            ->andReturnUsing(fn ($key, $ttl, $callback) => $callback());

        $lockMock = Mockery::mock();
        $lockMock->shouldReceive('block')
            ->andThrow(new \Illuminate\Contracts\Cache\LockTimeoutException('Lock not available'));
        Cache::shouldReceive('lock')->andReturn($lockMock);

        Log::shouldReceive('warning')->once()->withArgs(
            fn ($msg) => str_contains($msg, 'lock failed') && str_contains($msg, '999')
        );

        $elasticMock = $this->mockEs($esPayload);
        $service     = $this->makeService($elasticMock);

        $result = $service->load(999, 1);

        $this->assertSame(42, $result['allCandidate'], 'ES fallback data on lock timeout');
    }

    /**
     * @test
     * Chaos-2c: Even when ES is also down (triple failure: Redis lock + ES),
     * CounterService returns safe zero-filled counters rather than throwing.
     */
    public function it_returns_empty_counters_when_both_redis_lock_and_es_are_down(): void
    {
        Cache::shouldReceive('flexible')
            ->once()
            ->andReturnUsing(fn ($key, $ttl, $callback) => $callback());

        $lockMock = Mockery::mock();
        $lockMock->shouldReceive('block')
            ->andThrow(new \Illuminate\Contracts\Cache\LockTimeoutException('Lock not available'));
        Cache::shouldReceive('lock')->andReturn($lockMock);
        Log::shouldReceive('warning')->once();

        $service = $this->makeService($this->mockEsDown());

        $result = $service->load(999, 1);

        $this->assertSame(0, $result['allCandidate'], 'Must return safe zeros — not throw');
        $this->assertArrayHasKey('Sourcing',  $result);
        $this->assertArrayHasKey('Screening', $result);
        $this->assertArrayHasKey('Rejected',  $result);
    }

    /* ── Chaos-3: Reverb WebSocket Outage ──────────────────────────── */

    /**
     * @test
     * Chaos-3a: When Reverb is unreachable and broadcast() throws, the cache
     * bust (Cache::forget) still fires, preserving counter accuracy for
     * recruiters who poll via HTTP while WebSockets are down.
     */
    public function it_busts_cache_even_when_reverb_broadcast_throws(): void
    {
        $cacheForgetCalled = false;

        Cache::shouldReceive('forget')
            ->once()
            ->with('pipeline:42')
            ->andReturnUsing(function () use (&$cacheForgetCalled) {
                $cacheForgetCalled = true;
            });

        // Simulate Reverb down: broadcast() throws after Cache::forget already ran.
        Broadcast::shouldReceive('event')
            ->andThrow(new \RuntimeException('Reverb connection refused'));

        $elasticMock = Mockery::mock(ElasticService::class);
        $service     = $this->makeService($elasticMock);

        try {
            $service->onStageMoved(42, 7, 3, 1);
        } catch (\RuntimeException) {
            // The broadcast exception propagates; but Cache::forget must have fired first.
        }

        $this->assertTrue($cacheForgetCalled, 'Cache::forget must fire before broadcast attempt');
    }

    /**
     * @test
     * Chaos-3b: Happy path — onStageMoved busts cache and fires a StageMoved
     * event carrying the correct jobId, applicationId, newStatus, companyId.
     */
    public function it_dispatches_stage_moved_event_with_correct_payload(): void
    {
        Event::fake([StageMoved::class]);

        Cache::shouldReceive('forget')->once()->with('pipeline:10');

        $elasticMock = Mockery::mock(ElasticService::class);
        $service     = $this->makeService($elasticMock);

        $service->onStageMoved(jobId: 10, applicationId: 5, newStatus: 2, companyId: 1);

        Event::assertDispatched(StageMoved::class, function (StageMoved $e) {
            return $e->jobId === 10
                && $e->applicationId === 5
                && $e->newStatus === 2
                && $e->companyId === 1;
        });
    }

    /**
     * @test
     * Chaos-3c: emptyCounters() returns all 8 expected keys with zero values.
     * Callers rely on this contract when all data sources are unreachable.
     */
    public function empty_counters_returns_all_eight_keys_at_zero(): void
    {
        $elasticMock = Mockery::mock(ElasticService::class);
        $service     = $this->makeService($elasticMock);

        $empty = $service->emptyCounters();

        $expected = ['allCandidate', 'Sourcing', 'Screening', 'Interview',
                     'Negotiation', 'Offered', 'Joined', 'Rejected'];

        foreach ($expected as $key) {
            $this->assertArrayHasKey($key, $empty, "emptyCounters() must have key '{$key}'");
            $this->assertSame(0, $empty[$key], "emptyCounters()['{$key}'] must be 0");
        }
        $this->assertCount(8, $empty);
    }

    /* ── Chaos-4: Flaky Network / Optimistic UI (Dusk) ─────────────── */

    /**
     * @test
     * Chaos-4 DOCUMENTED (Dusk): Optimistic UI queues stage-move actions locally
     * when the network is degraded (500ms latency or offline).
     *
     * Verification requires browser-level execution of:
     *   - public/js/apptracker/_optimistic.js
     *   - public/js/apptracker/_fetch.js
     * under DevTools network throttling.
     *
     * PHPUnit cannot verify JS behavior. This test records the requirement so
     * CI can track it. Manual + Dusk checklist: see docs/CHAOS_RUNBOOK.md §Chaos-4.
     *
     * @group dusk-pending
     */
    public function chaos_4_optimistic_ui_under_flaky_network_is_verified_by_dusk(): void
    {
        // Dusk scenarios (see docs/CHAOS_RUNBOOK.md §Chaos-4):
        //   1. Open Apptracker → DevTools → Network → Slow 3G.
        //   2. Move a candidate to a new stage.
        //   3. Verify: badge updates in <16ms (optimistic), no blank screen.
        //   4. Verify: action queued in localStorage['apptracker_offline_queue'].
        //   5. Restore network → queue flushes → server confirms.
        //   6. Verify: no duplicate stage-move request sent.

        $this->markTestIncomplete(
            'Chaos-4 requires Laravel Dusk or Playwright. ' .
            'See docs/CHAOS_RUNBOOK.md §Chaos-4 for the manual verification checklist.'
        );
    }

    /* ── Redis cache hit (regression guard) ─────────────────────────── */

    /**
     * @test
     * Happy path: Cache::flexible returns a cached value without hitting DB or ES.
     * This is the P95 fast path (<1.5ms) — guard against regressions that
     * accidentally bypass the cache.
     */
    public function it_returns_cached_counters_without_db_or_es_on_cache_hit(): void
    {
        $cachedData = [
            'allCandidate' => 100, 'Sourcing' => 20, 'Screening' => 15,
            'Interview'    => 30,  'Negotiation' => 10, 'Offered' => 12,
            'Joined'       => 8,   'Rejected' => 5,
        ];

        Cache::shouldReceive('flexible')
            ->once()
            ->andReturn($cachedData);

        // DB and ES must not be called when cache is warm.
        DB::shouldReceive('connection')->never();

        $elasticMock = Mockery::mock(ElasticService::class);
        $elasticMock->shouldReceive('getClient')->never();

        $service = $this->makeService($elasticMock);
        $result  = $service->load(55, 3);

        $this->assertSame($cachedData, $result);
    }
}
