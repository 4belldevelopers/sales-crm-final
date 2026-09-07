<?php

namespace Tests\Feature;

use App\Services\CoinMetricsService;
use App\Services\CoinTelemetryDashboardService;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

/**
 * CoinsTelemetryObservabilityTest — V2 Coins Engine Phase 9 Feature Test Suite.
 *
 * Intent: Verifies APM telemetry collection, Redis metrics accumulation, cache hit ratio
 * calculation, and structured JSON dashboard output.
 *
 * Tenancy Note: Supports company_id tenant scoping. Does NOT use RefreshDatabase
 * (per Rule #2 and project guidelines).
 *
 * Author: Antigravity AI
 * Date: August 26, 2026
 */
class CoinsTelemetryObservabilityTest extends TestCase
{
    /**
     * Test CoinMetricsService increments telemetry counters during measured operations.
     *
     * Intent: Confirms metrics service increments coins_telemetry:total_operations in Redis.
     */
    public function test_redis_telemetry_metrics_accumulation(): void
    {
        $metricsService = new CoinMetricsService;
        $initialCount = (int) Cache::get('coins_telemetry:total_operations', 0);

        $metricsService->measure('test_telemetry_op', 999995, function () {
            return true;
        });

        $newCount = (int) Cache::get('coins_telemetry:total_operations', 0);
        $this->assertEquals($initialCount + 1, $newCount, 'Total operations counter in Redis must increment');
    }

    /**
     * Test CoinTelemetryDashboardService returns HEALTHY structured APM payload.
     *
     * Intent: Verifies getTelemetrySummary() structure and field presence.
     */
    public function test_telemetry_dashboard_service_returns_structured_payload(): void
    {
        $dashboardService = new CoinTelemetryDashboardService;

        $summary = $dashboardService->getTelemetrySummary();

        $this->assertEquals('HEALTHY', $summary['status']);
        $this->assertArrayHasKey('metrics', $summary);
        $this->assertArrayHasKey('performance_targets', $summary);
        $this->assertGreaterThanOrEqual(0, $summary['metrics']['total_operations']);
        $this->assertGreaterThanOrEqual(0, $summary['metrics']['cache_hit_ratio_percent']);
    }

    /**
     * Test per-tenant scoped APM telemetry summary.
     *
     * Intent: Confirms company_id filtering applies to telemetry summary.
     */
    public function test_per_tenant_scoped_telemetry_summary(): void
    {
        $dashboardService = new CoinTelemetryDashboardService;
        $companyId = 999996;

        $summary = $dashboardService->getTelemetrySummary($companyId);

        $this->assertStringContainsString("Company ID: {$companyId}", $summary['tenant_scope']);
    }
}
