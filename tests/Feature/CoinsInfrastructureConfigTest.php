<?php

namespace Tests\Feature;

use Tests\TestCase;

/**
 * CoinsInfrastructureConfigTest — V2 Coins Engine Phase 8 Feature Test Suite.
 *
 * Intent: Verifies production infrastructure configuration, Horizon queue supervisor definitions,
 * and tuning documentation existence.
 *
 * Tenancy Note: Test suite evaluates config/horizon.php and infrastructure guides.
 * Does NOT use RefreshDatabase (per Rule #2 and project guidelines).
 *
 * Author: Antigravity AI
 * Date: August 26, 2026
 */
class CoinsInfrastructureConfigTest extends TestCase
{
    /**
     * Test Horizon configuration defines dedicated supervisor-coins worker pool.
     *
     * Intent: Confirms supervisor-coins configuration exists in config/horizon.php.
     */
    public function test_horizon_configuration_defines_coins_supervisor(): void
    {
        $coinsSupervisor = config('horizon.defaults.supervisor-coins');

        $this->assertIsArray($coinsSupervisor, 'supervisor-coins must be configured in horizon.php');
        $this->assertContains('coins', $coinsSupervisor['queue'], 'supervisor-coins must process the coins queue');
        $this->assertEquals(10, $coinsSupervisor['maxProcesses']);
    }

    /**
     * Test Horizon indexing supervisor includes elasticsearch queue.
     *
     * Intent: Confirms supervisor-indexing handles the elasticsearch queue.
     */
    public function test_horizon_indexing_supervisor_includes_elasticsearch_queue(): void
    {
        $indexingSupervisor = config('horizon.defaults.supervisor-indexing');

        $this->assertIsArray($indexingSupervisor);
        $this->assertContains('elasticsearch', $indexingSupervisor['queue'], 'supervisor-indexing must process the elasticsearch queue');
    }

    /**
     * Test V2 Infrastructure Tuning Guide file exists.
     *
     * Intent: Confirms production MySQL, Redis, Octane, and OS tuning guide artifact is present.
     */
    public function test_infrastructure_tuning_guide_exists(): void
    {
        $guidePath = base_path('docs/V2_COINS/V2_INFRASTRUCTURE_TUNING_GUIDE.md');

        $this->assertFileExists($guidePath, 'V2 Infrastructure Tuning Guide must exist in docs/V2_COINS/');
    }
}
