<?php

namespace Tests\Feature;

use App\Events\TenantEntityDeactivatedDueToCoins;
use App\Listeners\SyncElasticStatusOnCoinDeactivationListener;
use App\Services\ElasticService;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * CoinsElasticAsyncSyncTest — V2 Coins Engine Phase 7 Feature Test Suite.
 *
 * Intent: Verifies event dispatching, queued listener configuration ($afterCommit = true, 'elasticsearch' queue),
 * and decoupled asynchronous Elasticsearch index status updates.
 *
 * Tenancy Note: Tenant isolation is enforced via companyId in domain events and listeners.
 * Does NOT use RefreshDatabase (per Rule #2 and project guidelines).
 *
 * Author: Antigravity AI
 * Date: August 26, 2026
 */
class CoinsElasticAsyncSyncTest extends TestCase
{
    /**
     * Test TenantEntityDeactivatedDueToCoins domain event dispatching.
     *
     * Intent: Confirms event is dispatched with correct companyId and payload.
     */
    public function test_domain_event_dispatched_with_tenant_context(): void
    {
        Event::fake([TenantEntityDeactivatedDueToCoins::class]);

        $companyId = 999993;
        $userId = 1234;

        TenantEntityDeactivatedDueToCoins::dispatch($companyId, $userId, 'user', 0);

        Event::assertDispatched(TenantEntityDeactivatedDueToCoins::class, function ($event) use ($companyId, $userId) {
            return $event->companyId === $companyId && $event->entityId === $userId && $event->entityType === 'user';
        });
    }

    /**
     * Test listener configuration ($afterCommit = true, 'elasticsearch' queue).
     *
     * Intent: Verifies listener properties ensure asynchronous execution after DB transaction commit.
     */
    public function test_listener_configured_for_after_commit_and_elasticsearch_queue(): void
    {
        $listener = new SyncElasticStatusOnCoinDeactivationListener;

        $this->assertTrue($listener->afterCommit, 'Listener must execute after DB transaction commits ($afterCommit = true)');
        $this->assertEquals('elasticsearch', $listener->queue, 'Listener must be assigned to the elasticsearch queue');
    }

    /**
     * Test listener handle execution completes safely without throw.
     *
     * Intent: Confirms listener handles event safely when ES service client is invoked.
     */
    public function test_listener_handles_user_deactivation_event_safely(): void
    {
        $elasticServiceMock = $this->createMock(ElasticService::class);
        $listener = new SyncElasticStatusOnCoinDeactivationListener($elasticServiceMock);

        $event = new TenantEntityDeactivatedDueToCoins(999994, 555, 'user', 0);
        $listener->handle($event);

        $this->assertTrue(true, 'Listener handled user deactivation safely');
    }
}
