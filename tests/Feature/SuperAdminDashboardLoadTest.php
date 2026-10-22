<?php

namespace Tests\Feature;

use App\Http\Controllers\SuperAdmin\SuperAdminDashboardController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * SuperAdminDashboardLoadTest
 *
 * Automated verification suite for Super Admin Analytics Dashboard:
 * 1. 0% MySQL reads on dashboard request path (CQRS strictly via Elasticsearch).
 * 2. 100% Metric parity across all 10 analytics cards.
 * 3. 0% NaN% in rendered Blade DOM output.
 * 4. All 10 Per-Chart Filter API endpoints return valid JSON responses.
 * 5. High-throughput Multi-Search latency benchmark.
 */
class SuperAdminDashboardLoadTest extends TestCase
{
    protected $controller;

    protected $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'mysql',
            'database.connections.mysql.database' => 'newdata',
        ]);
        DB::purge('mysql');
        DB::reconnect('mysql');
        DB::setDefaultConnection('mysql');

        $this->adminUser = User::where('is_superadmin', 1)->first();
        if ($this->adminUser) {
            Auth::login($this->adminUser);
        }

        $this->controller = app(SuperAdminDashboardController::class);
    }

    /**
     * Test 1: Verify Initial Dashboard Load executes 0 MySQL reads and maintains exact metric parity.
     */
    public function test_dashboard_index_executes_zero_mysql_reads_and_matches_metrics(): void
    {
        // Warm settings cache to simulate production state
        Cache::remember('superadmin_global_setting', 86400, fn () => \App\Models\GlobalSetting::first());
        Cache::remember('superadmin_sms_setting', 86400, fn () => \App\Models\SmsSetting::first());
        Cache::remember('superadmin_theme_setting', 86400, fn () => \App\Models\ThemeSetting::withoutGlobalScopes()->whereNull('company_id')->first() ?? (object) []);
        Cache::remember('superadmin_language_settings', 86400, fn () => \App\Models\LanguageSetting::where('status', 'enabled')->orderBy('language_name')->get());
        Cache::remember('superadmin_superadmin_company_ids', 86400, fn () => [1]);
        Cache::remember('jero_super_admin_company_ids', 86400, fn () => [1]);

        $mysqlQueries = [];
        DB::listen(function ($query) use (&$mysqlQueries) {
            $sql = strtolower(trim($query->sql));
            if (str_starts_with($sql, 'select')) {
                $mysqlQueries[] = $query->sql;
            }
        });

        $controller = app(SuperAdminDashboardController::class);
        $view = $controller->index();

        $this->assertCount(
            0,
            $mysqlQueries,
            'Super Admin Dashboard load must execute 0 MySQL SELECT queries. Actual: '.json_encode($mysqlQueries)
        );

        $data = $view->getData();

        // 100% Metric Parity Assertions (Excluding Super Admin Company ID 1)
        $this->assertEquals(3, $data['total_company_count'], 'Total Companies must be 3 (excluding Super Admin)');
        $this->assertEquals(2, $data['staffing_company_count'], 'Staffing Companies must be 2');
        $this->assertEquals(1, $data['service_company_count'], 'Service Companies must be 1 (excluding Super Admin)');
        $this->assertEquals(3, $data['activecompanies'], 'Active Companies must be 3 (excluding Super Admin)');
        $this->assertEquals(6, $data['bench_total'], 'Bench Candidates must be 6');
        $this->assertEquals(6, $data['jobPostTotal'], 'Job Posts Total must be 6');
        $this->assertEquals(6, $data['jobsStatusTotal'], 'Job Status Total must be 6');
        $this->assertEquals(3, $data['oprationalPlanTotal'], 'Operational Plan Total must be 3 (excluding Super Admin)');
        $this->assertEquals(3, $data['salesPlanTotal'], 'Sales Plan Total must be 3 (excluding Super Admin)');
        $this->assertEquals(2, $data['recruiterStatsTotal'], 'Recruiters Total must be 2');
        $this->assertEquals(56, $data['allcandidates_total'], 'Candidates Total must be 56');
        $this->assertEquals(1, $data['interviewData_total'], 'Interview Total must be 1');
        $this->assertEquals(1119, $data['emailSummaryData_total'], 'Email Activity Total must be 1119 (excluding Super Admin)');
        $this->assertEquals(0, $data['whatsappSummary_total'], 'WhatsApp Activity Total must be 0');
    }

    /**
     * Test 2: Verify rendered Blade HTML has 0 occurrences of NaN% and contains all 10 charts.
     */
    public function test_rendered_blade_contains_zero_nan_and_all_ten_cards(): void
    {
        $view = $this->controller->index();
        $html = $view->render();

        $this->assertStringNotContainsString('NaN%', $html, 'Rendered HTML must not contain NaN%');
        $this->assertStringContainsString('Dashboard', $html, 'Rendered HTML must contain Dashboard page title');
        $this->assertStringContainsString('subscription_overview_chart', $html);
        $this->assertStringContainsString('companyCountData', $html);
        $this->assertStringContainsString('active_companies_chart', $html);
        $this->assertStringContainsString('bench_candidate_chart', $html);
        $this->assertStringContainsString('jobs_post_chart', $html);
        $this->assertStringContainsString('jobs_status_chart', $html);
        $this->assertStringContainsString('total_candidate_uploads_chart', $html);
        $this->assertStringContainsString('total_interview_uploads_chart', $html);
        $this->assertStringContainsString('total_email_chart', $html);
        $this->assertStringContainsString('total_whatsapp_chart', $html);
    }

    /**
     * Test 3: Verify all 10 Chart API endpoints return successful JSON payloads.
     */
    public function test_chart_api_endpoints_return_valid_json_for_all_keys(): void
    {
        $chartKeys = [
            'subscription_overview',
            'company_types',
            'company_activity',
            'bench_candidate',
            'job_posts_trend',
            'job_status',
            'candidate_pipeline',
            'interview_status',
            'email_activity',
            'whatsapp_activity',
        ];

        $periods = ['week', 'month', 'custom'];

        foreach ($chartKeys as $key) {
            foreach ($periods as $period) {
                $params = ['period' => $period];
                if ($period === 'custom') {
                    $params['start_date'] = '2026-09-01';
                    $params['end_date'] = '2026-10-22';
                }

                $request = Request::create("/super-admin/dashboard/api/chart/{$key}", 'GET', $params);
                $response = $this->controller->chartApi($request, $key);
                $this->assertEquals(200, $response->getStatusCode(), "Chart {$key} for period {$period} must return HTTP 200");

                $json = json_decode($response->getContent(), true);
                $this->assertTrue($json['success'], "Chart {$key} for period {$period} must return success: true");
                $this->assertIsArray($json['labels'], "Chart {$key} labels must be an array");
                $this->assertIsArray($json['data'], "Chart {$key} data must be an array");
                $this->assertArrayHasKey('total', $json, "Chart {$key} must have total key");
            }
        }

        // Test week period for trend has exactly 7 daily ticks
        $reqWeek = Request::create('/super-admin/dashboard/api/chart/job_posts_trend', 'GET', ['period' => 'week']);
        $respWeek = $this->controller->chartApi($reqWeek, 'job_posts_trend');
        $jsonWeek = json_decode($respWeek->getContent(), true);
        $this->assertCount(7, $jsonWeek['labels'], 'Weekly trend must contain 7 daily ticks');
    }

    /**
     * Test 4: Verify Multi-Search throughput and latency SLA.
     */
    public function test_msearch_performance_and_latency(): void
    {
        $latencies = [];

        for ($i = 0; $i < 5; $i++) {
            $t0 = microtime(true);
            $this->controller->index();
            $latencies[] = (microtime(true) - $t0) * 1000;
        }

        sort($latencies);
        $medianLatency = $latencies[2];

        // Ensure median execution time is well within high-performance threshold (< 500 ms in local test env)
        $this->assertLessThan(
            500,
            $medianLatency,
            "Dashboard controller execution latency must be sub-500ms. Measured median: {$medianLatency} ms"
        );
    }

    /**
     * Test 5: High-concurrency simulated workload testing memory stability and bounded p95 latency.
     */
    public function test_high_concurrency_simulated_load_benchmark(): void
    {
        $iterations = 25;
        $latencies = [];
        $memStart = memory_get_usage(true);

        for ($i = 0; $i < $iterations; $i++) {
            $t0 = microtime(true);
            $this->controller->index();
            $latencies[] = (microtime(true) - $t0) * 1000;
        }

        $memEnd = memory_get_usage(true);
        $memDeltaMb = ($memEnd - $memStart) / (1024 * 1024);

        sort($latencies);
        $p50 = $latencies[(int) ($iterations * 0.50)];
        $p95 = $latencies[(int) ($iterations * 0.95) - 1];

        // Assert memory growth remains tightly bounded across repeated executions
        $this->assertLessThan(
            15.0,
            $memDeltaMb,
            "Memory growth must remain under 15 MB across {$iterations} cycles. Delta: {$memDeltaMb} MB"
        );

        // Assert p95 latency remains stable under repeated query execution
        $this->assertLessThan(
            500.0,
            $p95,
            "p95 latency must be sub-500ms under repeated load. Measured p95: {$p95} ms, p50: {$p50} ms"
        );
    }

    /**
     * Test 6: Verify Circuit Breaker degradation when an invalid chart key or search fault occurs.
     */
    public function test_circuit_breaker_graceful_degradation_on_fault(): void
    {
        $request = Request::create('/super-admin/dashboard/api/chart/invalid_key_xyz', 'GET');
        $response = $this->controller->chartApi($request, 'invalid_key_xyz');

        $this->assertEquals(500, $response->getStatusCode());
        $json = json_decode($response->getContent(), true);

        $this->assertFalse($json['success']);
        $this->assertStringContainsString('Circuit breaker activated', $json['message']);
        $this->assertArrayHasKey('correlation_id', $json);
    }
}
