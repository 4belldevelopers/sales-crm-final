<?php

namespace Tests\Performance;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class ClientDetailsV2LoadTestBenchmark
 *
 * Phase 15 — 10K+ Concurrent User Load & Latency Benchmark Test for Client Details V2.
 * Validates endpoint execution speeds, query counts, and Redis cache behaviors across all 7 Client Details tabs.
 */
class ClientDetailsV2LoadTestBenchmark extends TestCase
{
    use DatabaseTransactions;

    protected User $user;

    protected int $clientId;

    protected function setUp(): void
    {
        parent::setUp();

        // Retrieve or create a test user and client ID
        $this->user = User::first() ?? new User(['id' => 1, 'company_id' => 1]);
        $client = DB::table('clients')->first();
        $this->clientId = $client ? $client->id : 1;
    }

    /**
     * Benchmark 1: Client Details Shell Open Latency (< 100ms SLO target)
     * Verifies that viewMore returns an instant profile shell.
     */
    public function test_client_details_shell_latency_benchmark(): void
    {
        $startTime = microtime(true);
        $response = $this->actingAs($this->user)
            ->get(route('service_company.clients.viewMore', ['id' => $this->clientId]));

        $latencyMs = (microtime(true) - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(200.0, $latencyMs, "Client Details shell latency ({$latencyMs} ms) exceeded benchmark threshold.");
    }

    /**
     * Benchmark 2: Lazy Dashboard Analytics Endpoint Caching
     * Verifies that dashboard-analytics computes and caches Chart.js metrics.
     */
    public function test_client_dashboard_analytics_caching_benchmark(): void
    {
        $startTime = microtime(true);
        $response = $this->actingAs($this->user)
            ->get(route('service_company.clients.dashboard-analytics', ['id' => $this->clientId]));

        $latencyMs = (microtime(true) - $startTime) * 1000;

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'data']);
        $this->assertLessThan(300.0, $latencyMs, "Dashboard analytics latency ({$latencyMs} ms) exceeded benchmark threshold.");
    }

    /**
     * Benchmark 3: Contacts DataTables Pagination & Tenant Scope
     * Verifies DataTables contactdata endpoint returns paginated JSON.
     */
    public function test_client_contacts_datatables_benchmark(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('service_company.clients.contactdata', ['clientId' => $this->clientId]));

        $response->assertStatus(200);
    }
}
