<?php

namespace Tests\Feature;

/**
 * Class CvTrackerV2PerformanceTest
 *
 * INTENT: Automated performance and feature test for CV Tracker V2 listing endpoint.
 * Asserts permission gate security, response structure, zero-DB-query read path, and latency benchmarks.
 */

use App\Models\User;
use App\Models\Company;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Tests\TestCase;

class CvTrackerV2PerformanceTest extends TestCase
{
    /**
     * Test V2 candidate listing endpoint returns 403 Forbidden when view_job_applications gate is denied.
     *
     * @return void
     */
    public function test_v2_endpoint_requires_permission_gate(): void
    {
        Gate::define('view_job_applications', fn () => false);

        $user = User::factory()->make([
            'id' => 1,
            'company_id' => 1,
        ]);

        $response = $this->actingAs($user)->getJson(route('cvtracker.v2.allcandidates'));

        $response->assertStatus(403);
    }

    /**
     * Test V2 candidate listing endpoint executes ZERO read database queries when data is cached/served from ES.
     *
     * @return void
     */
    public function test_v2_endpoint_executes_zero_mysql_queries(): void
    {
        Gate::define('view_job_applications', fn () => true);

        $user = User::factory()->make([
            'id' => 1,
            'company_id' => 1,
        ]);

        DB::enableQueryLog();

        $startTime = microtime(true);
        $response = $this->actingAs($user)->getJson(route('cvtracker.v2.allcandidates', [
            'page' => 1,
            'size' => 20,
            'sortingfilter' => 1,
        ]));
        $executionTimeMs = (microtime(true) - $startTime) * 1000;

        $queryLog = DB::getQueryLog();

        // Assert response status and structure
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'status',
            'meta' => ['total', 'current_page', 'last_page'],
            'data',
        ]);

        // Assert read-path query count is 0
        $this->assertCount(0, $queryLog, 'V2 read path must execute 0 MySQL database queries.');

        // Assert latency benchmark (< 100ms)
        $this->assertLessThan(100, $executionTimeMs, 'V2 candidate listing response time must be under 100ms.');
    }
}
