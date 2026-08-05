<?php

namespace Tests\Feature;

use App\Events\CandidateStatusChanged;
use App\Events\InterviewScheduled;
use App\Events\OperationalCoinUpdated;
use App\Jobs\AutoCloseExpiredJobsJob;
use App\Jobs\ProcessOfferTimeoutJob;
use App\Jobs\ProcessOperationalCoinDeductionsJob;
use App\Services\ServiceCompanyDashboardService;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

/**
 * ServiceCompanyDashboardTest — Verifies the V2 Dashboard performance plan.
 *
 * What the plan promises:
 *   Phase 1 — Background jobs replace synchronous cron on every GET.
 *   Phase 2 — Compound MySQL indexes + range query on schedule_date.
 *   Phase 3 — ServiceCompanyDashboardService provides testable data layer.
 *   Phase 4 — JSON API routes registered; cache service available.
 *   Phase 5 — Broadcast events exist and implement ShouldBroadcast.
 *   Phase 6 — public/js/service_company_dashboard.js exists and is non-empty.
 *
 * Run:
 *   php artisan test --filter=ServiceCompanyDashboardTest
 */
class ServiceCompanyDashboardTest extends TestCase
{
    // =========================================================================
    // SECTION A — Job class existence & contracts
    // =========================================================================

    public function test_process_operational_coin_deductions_job_exists(): void
    {
        $this->assertTrue(
            class_exists(ProcessOperationalCoinDeductionsJob::class),
            'ProcessOperationalCoinDeductionsJob must exist in App\\Jobs'
        );
    }

    public function test_auto_close_expired_jobs_job_exists(): void
    {
        $this->assertTrue(
            class_exists(AutoCloseExpiredJobsJob::class),
            'AutoCloseExpiredJobsJob must exist in App\\Jobs'
        );
    }

    public function test_process_offer_timeout_job_exists(): void
    {
        $this->assertTrue(
            class_exists(ProcessOfferTimeoutJob::class),
            'ProcessOfferTimeoutJob must exist in App\\Jobs'
        );
    }

    public function test_all_background_jobs_implement_should_queue(): void
    {
        foreach ([
            ProcessOperationalCoinDeductionsJob::class,
            AutoCloseExpiredJobsJob::class,
            ProcessOfferTimeoutJob::class,
        ] as $jobClass) {
            $ref = new \ReflectionClass($jobClass);
            $this->assertTrue(
                $ref->implementsInterface(\Illuminate\Contracts\Queue\ShouldQueue::class),
                "{$jobClass} must implement ShouldQueue"
            );
        }
    }

    // =========================================================================
    // SECTION B — Retry policies
    // =========================================================================

    public function test_process_operational_coin_deductions_job_retry_policy(): void
    {
        $ref      = new \ReflectionClass(ProcessOperationalCoinDeductionsJob::class);
        $instance = $ref->newInstanceWithoutConstructor();
        $tries    = $ref->getProperty('tries');
        $tries->setAccessible(true);
        $this->assertGreaterThanOrEqual(2, $tries->getValue($instance), '$tries must be >= 2');
    }

    public function test_auto_close_expired_jobs_job_retry_policy(): void
    {
        $ref      = new \ReflectionClass(AutoCloseExpiredJobsJob::class);
        $instance = $ref->newInstanceWithoutConstructor();
        $tries    = $ref->getProperty('tries');
        $tries->setAccessible(true);
        $this->assertGreaterThanOrEqual(2, $tries->getValue($instance), '$tries must be >= 2');
    }

    // =========================================================================
    // SECTION C — Queue dispatch contracts (fake queue)
    // =========================================================================

    public function test_all_background_jobs_can_be_dispatched(): void
    {
        Queue::fake();

        ProcessOperationalCoinDeductionsJob::dispatch();
        AutoCloseExpiredJobsJob::dispatch();
        ProcessOfferTimeoutJob::dispatch();

        Queue::assertPushed(ProcessOperationalCoinDeductionsJob::class);
        Queue::assertPushed(AutoCloseExpiredJobsJob::class);
        Queue::assertPushed(ProcessOfferTimeoutJob::class);
    }

    // =========================================================================
    // SECTION D — Broadcast event contracts
    // =========================================================================

    public function test_candidate_status_changed_event_exists_and_is_broadcastable(): void
    {
        $this->assertTrue(class_exists(CandidateStatusChanged::class));
        $ref = new \ReflectionClass(CandidateStatusChanged::class);
        $this->assertTrue(
            $ref->implementsInterface(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class),
            'CandidateStatusChanged must implement ShouldBroadcast'
        );
    }

    public function test_interview_scheduled_event_exists_and_is_broadcastable(): void
    {
        $this->assertTrue(class_exists(InterviewScheduled::class));
        $ref = new \ReflectionClass(InterviewScheduled::class);
        $this->assertTrue(
            $ref->implementsInterface(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class),
            'InterviewScheduled must implement ShouldBroadcast'
        );
    }

    public function test_operational_coin_updated_event_exists_and_is_broadcastable(): void
    {
        $this->assertTrue(class_exists(OperationalCoinUpdated::class));
        $ref = new \ReflectionClass(OperationalCoinUpdated::class);
        $this->assertTrue(
            $ref->implementsInterface(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class),
            'OperationalCoinUpdated must implement ShouldBroadcast'
        );
    }

    public function test_broadcast_events_use_private_company_channel(): void
    {
        $event = new CandidateStatusChanged(42, 1, 1, 2, 'PHP Dev');
        $channel = $event->broadcastOn();
        $this->assertInstanceOf(\Illuminate\Broadcasting\PrivateChannel::class, $channel);
        $this->assertStringContainsString('company.42.dashboard', $channel->name);
    }

    public function test_broadcast_events_dispatch_correctly(): void
    {
        Event::fake([CandidateStatusChanged::class, InterviewScheduled::class, OperationalCoinUpdated::class]);

        event(new CandidateStatusChanged(1, 10, 1, 2, 'Test'));
        event(new InterviewScheduled(1, 5, '2026-08-05T10:00:00', 'Technical'));
        event(new OperationalCoinUpdated(1, 95.0, 5.0, 'team'));

        Event::assertDispatched(CandidateStatusChanged::class);
        Event::assertDispatched(InterviewScheduled::class);
        Event::assertDispatched(OperationalCoinUpdated::class);
    }

    // =========================================================================
    // SECTION E — Service class existence & structure
    // =========================================================================

    public function test_service_company_dashboard_service_exists(): void
    {
        $this->assertTrue(
            class_exists(ServiceCompanyDashboardService::class),
            'ServiceCompanyDashboardService must exist in App\\Services'
        );
    }

    public function test_dashboard_service_has_required_methods(): void
    {
        foreach (['getCompanyKpiMetrics', 'getRecruiterLeaderboard', 'getTodayInterviews', 'invalidateCache'] as $method) {
            $this->assertTrue(
                method_exists(ServiceCompanyDashboardService::class, $method),
                "ServiceCompanyDashboardService::{$method}() must exist"
            );
        }
    }

    // =========================================================================
    // SECTION F — Phase 6: JS module exists and is non-empty
    // =========================================================================

    public function test_dashboard_js_module_exists_and_is_non_empty(): void
    {
        $path = public_path('js/service_company_dashboard.js');
        $this->assertFileExists($path, 'public/js/service_company_dashboard.js must exist');
        $this->assertGreaterThan(500, filesize($path), 'JS module must be non-trivial (> 500 bytes)');
    }

    public function test_dashboard_js_module_contains_abort_controller(): void
    {
        $content = file_get_contents(public_path('js/service_company_dashboard.js'));
        $this->assertStringContainsString('AbortController', $content,
            'JS module must use AbortController for request cancellation');
    }

    public function test_dashboard_js_module_contains_exponential_backoff(): void
    {
        $content = file_get_contents(public_path('js/service_company_dashboard.js'));
        $this->assertStringContainsString('backoff', strtolower($content),
            'JS module must implement exponential backoff');
    }

    // =========================================================================
    // SECTION G — Architecture invariants
    // =========================================================================

    /**
     * The controller's index() must NOT contain the synchronous cron calls.
     *
     * This is the most important architectural invariant of Phase 1:
     * no write-heavy loops should run on every dashboard GET.
     *
     * Manual verification:
     *   1. Load /service_company/dashboard.
     *   2. Confirm no coin deduction queries fire (check query log).
     *   3. Confirm ProcessOperationalCoinDeductionsJob appears in Horizon within 5 min.
     */
    public function test_dashboard_controller_does_not_call_close_expired_jobs_synchronously(): void
    {
        $controllerPath = app_path(
            'Http/Controllers/ServiceCompany/ServiceCompanyDashboardController.php'
        );
        $source = file_get_contents($controllerPath);

        $this->assertStringNotContainsString(
            'closeExpiredJobs($this->company_id)',
            $source,
            'index() must not call closeExpiredJobs() synchronously — use AutoCloseExpiredJobsJob'
        );
    }

    public function test_dashboard_controller_does_not_run_coin_deductions_on_get(): void
    {
        $controllerPath = app_path(
            'Http/Controllers/ServiceCompany/ServiceCompanyDashboardController.php'
        );
        $source = file_get_contents($controllerPath);

        $this->assertStringNotContainsString(
            "event'=> \"Team Manage Auto Deductions\"",
            $source,
            'index() must not contain synchronous coin deduction strings'
        );

        $this->assertStringNotContainsString(
            '$usersData as $userVal',
            $source,
            'index() must not contain the synchronous user coin deduction foreach loop'
        );
    }

    public function test_dashboard_controller_date_query_uses_range_not_date_function(): void
    {
        $controllerPath = app_path(
            'Http/Controllers/ServiceCompany/ServiceCompanyDashboardController.php'
        );
        $source = file_get_contents($controllerPath);

        $this->assertStringNotContainsString(
            "DATE(`schedule_date`)",
            $source,
            "DATE() column wrap must be replaced with range query (whereBetween) so the index can be used"
        );

        $this->assertStringContainsString(
            'whereBetween',
            $source,
            'schedule_date query must use whereBetween range for index use'
        );
    }

    public function test_migration_for_compound_indexes_exists(): void
    {
        $migrations = glob(database_path('migrations/*v2_dashboard_compound_indexes*'));
        $this->assertNotEmpty($migrations, 'Migration for V2 dashboard compound indexes must exist');
    }
}
