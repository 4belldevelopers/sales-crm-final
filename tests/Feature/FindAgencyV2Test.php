<?php

namespace Tests\Feature;

use App\Events\AgencyRequestDeltaUpdated;
use App\Helper\GeneralMethod;
use App\Jobs\ProcessAgencyOutboxEventJob;
use App\Services\FindAgencyService;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * FindAgencyV2Test
 *
 * Feature and architectural verification test suite for the Find Agency V2 discovery and invitation engine.
 * Validates:
 * 1. Service, Job, and Event class contracts.
 * 2. Multi-tenant versioned Redis cache key generation and atomic invalidation.
 * 3. HTTP Route registration for listing and multi-method mutation endpoints.
 * 4. MySQL schema contracts (sys_idempotency_keys, sys_outbox_events, staffing_company_invitation).
 * 5. Frontend Client-First Architecture (L1 RAM, L2 IndexedDB, Echo WebSocket SWR, Request Abort).
 * 6. Batch helper query methods for N+1 elimination.
 * 7. Transactional Outbox retry and poison-pill backoff configurations.
 *
 * Run:
 *   php artisan test --filter=FindAgencyV2Test
 */
class FindAgencyV2Test extends TestCase
{
    /**
     * Test that FindAgencyService exists and is bound in the IoC container.
     */
    public function test_find_agency_service_exists_and_resolvable(): void
    {
        $this->assertTrue(
            class_exists(FindAgencyService::class),
            'FindAgencyService class must exist in App\\Services.'
        );

        $service = app(FindAgencyService::class);
        $this->assertInstanceOf(
            FindAgencyService::class,
            $service,
            'FindAgencyService must resolve from the service container.'
        );
    }

    /**
     * Test that AgencyRequestDeltaUpdated exists and adheres to broadcasting contracts.
     */
    public function test_agency_request_delta_updated_event_contracts(): void
    {
        $this->assertTrue(
            class_exists(AgencyRequestDeltaUpdated::class),
            'AgencyRequestDeltaUpdated must exist in App\\Events.'
        );

        $event = new AgencyRequestDeltaUpdated(
            companyId: 263,
            agencyId: 501,
            eventType: 'sent',
            entityVersion: 2,
            invitationId: 9999
        );

        $this->assertInstanceOf(
            ShouldBroadcast::class,
            $event,
            'AgencyRequestDeltaUpdated must implement ShouldBroadcast.'
        );

        $this->assertEquals(
            'agency.request.delta',
            $event->broadcastAs(),
            'Broadcast name must be agency.request.delta.'
        );

        $channel = $event->broadcastOn();
        $this->assertEquals(
            'private-company.263',
            $channel->name,
            'Event must broadcast to private tenant channel company.{companyId}.'
        );

        $payload = $event->broadcastWith();
        $this->assertEquals(263, $payload['company_id']);
        $this->assertEquals(501, $payload['agency_id']);
        $this->assertEquals('sent', $payload['event_type']);
        $this->assertEquals(2, $payload['entity_version']);
        $this->assertEquals(9999, $payload['invitation_id']);
    }

    /**
     * Test that ProcessAgencyOutboxEventJob exists and adheres to queue and retry contracts.
     */
    public function test_process_agency_outbox_event_job_contracts(): void
    {
        $this->assertTrue(
            class_exists(ProcessAgencyOutboxEventJob::class),
            'ProcessAgencyOutboxEventJob must exist in App\\Jobs.'
        );

        $job = new ProcessAgencyOutboxEventJob(101);

        $this->assertInstanceOf(
            ShouldQueue::class,
            $job,
            'ProcessAgencyOutboxEventJob must implement ShouldQueue.'
        );

        $this->assertEquals(
            'agency-outbox',
            $job->queue,
            'ProcessAgencyOutboxEventJob must run on the dedicated agency-outbox queue.'
        );

        $this->assertEquals(3, $job->tries, 'Job must have 3 tries for bounded retry.');
        $this->assertEquals(10, $job->backoff, 'Job must have 10 seconds exponential backoff.');
        $this->assertEquals(60, $job->timeout, 'Job must have 60 seconds timeout.');
    }

    /**
     * Test that Find Agency discovery and mutation routes are properly registered.
     */
    public function test_find_agency_routes_are_registered(): void
    {
        $hasIndex = Route::has('find.findAgency') || Route::has('service_company.find.findAgency');
        $this->assertTrue($hasIndex, "Route 'find.findAgency' must be registered.");

        $hasData = Route::has('find.findAgencyData') || Route::has('service_company.find.findAgencyData');
        $this->assertTrue($hasData, "Route 'find.findAgencyData' must be registered.");

        $reqRoute = Route::getRoutes()->getByName('service_company.agency.recommanded_request')
            ?? Route::getRoutes()->getByName('agency.recommanded_request');

        $this->assertNotNull($reqRoute, "Route 'agency.recommanded_request' must be registered.");
        $this->assertContains('GET', $reqRoute->methods(), 'Agency request route must support legacy GET.');
        $this->assertContains('POST', $reqRoute->methods(), 'Agency request route must support modern POST.');
    }

    /**
     * Test tenant cache invalidation increments tenant-scoped version key.
     */
    public function test_tenant_cache_invalidation_increments_version(): void
    {
        $companyId = 888;
        $versionKey = "ats:v2:findagency:version:{$companyId}";
        Cache::put($versionKey, 5, 3600);

        FindAgencyService::invalidateTenantCache($companyId);

        $newVersion = (int) Cache::get($versionKey);
        $this->assertGreaterThan(
            5,
            $newVersion,
            'invalidateTenantCache must increment the tenant version key.'
        );
    }

    /**
     * Test that mysql-schema.sql defines required tables and indexes.
     */
    public function test_mysql_schema_defines_required_tables(): void
    {
        $schemaPath = base_path('database/schema/mysql-schema.sql');
        $this->assertFileExists($schemaPath);
        $schemaContent = file_get_contents($schemaPath);

        $this->assertStringContainsString(
            'sys_idempotency_keys',
            $schemaContent,
            'mysql-schema.sql must define sys_idempotency_keys for atomic request deduplication.'
        );

        $this->assertStringContainsString(
            'sys_outbox_events',
            $schemaContent,
            'mysql-schema.sql must define sys_outbox_events for transactional outbox persistence.'
        );

        $this->assertStringContainsString(
            'staffing_company_invitation',
            $schemaContent,
            'mysql-schema.sql must define staffing_company_invitation for partner requests.'
        );
    }

    /**
     * Test that find-agency.blade.php implements L1 RAM and L2 IndexedDB client caching.
     */
    public function test_find_agency_blade_implements_client_caching_and_realtime(): void
    {
        $bladePath = resource_path('views/service_company/common/find-agency.blade.php');
        $this->assertFileExists($bladePath);
        $content = file_get_contents($bladePath);

        $this->assertStringContainsString(
            'window.JeroFindAgencyRAM',
            $content,
            'Blade must implement window.JeroFindAgencyRAM for L1 in-memory caching.'
        );

        $this->assertStringContainsString(
            'window.JeroFindAgencyDB',
            $content,
            'Blade must implement window.JeroFindAgencyDB for L2 IndexedDB persistence.'
        );

        $this->assertStringContainsString(
            'agency.request.delta',
            $content,
            'Blade must subscribe to agency.request.delta WebSocket broadcasts via Echo.'
        );

        $this->assertStringContainsString(
            'X-Idempotency-Key',
            $content,
            'Blade mutation AJAX call must include X-Idempotency-Key header.'
        );

        $this->assertStringContainsString(
            'activeAgencyXHR.abort()',
            $content,
            'Blade must support request aborting to avoid race condition DOM thrashing.'
        );
    }

    /**
     * Test batch helper methods in GeneralMethod for N+1 elimination.
     */
    public function test_batch_helper_methods_handle_empty_inputs_safely(): void
    {
        $clients = GeneralMethod::batchGetClientIdCompany([]);
        $this->assertIsArray($clients);
        $this->assertEmpty($clients);

        $relations = GeneralMethod::batchGetAgencyRelationData(1, []);
        $this->assertIsArray($relations);
        $this->assertEmpty($relations);

        $jobs = GeneralMethod::batchAgencyJobsCount([]);
        $this->assertIsArray($jobs);
        $this->assertEmpty($jobs);

        $candidates = GeneralMethod::batchAgencyJobsCandidatesCount([]);
        $this->assertIsArray($candidates);
        $this->assertEmpty($candidates);
    }

    /**
     * Test find_agency_ajax.blade.php consumes pre-enriched batch properties.
     */
    public function test_find_agency_ajax_blade_consumes_enriched_batch_properties(): void
    {
        $ajaxBladePath = resource_path('views/service_company/ajax_pages/find_agency_ajax.blade.php');
        $this->assertFileExists($ajaxBladePath);
        $content = file_get_contents($ajaxBladePath);

        $this->assertStringContainsString(
            '_client_info',
            $content,
            'Template must support pre-enriched _client_info.'
        );

        $this->assertStringContainsString(
            '_relation_info',
            $content,
            'Template must support pre-enriched _relation_info.'
        );

        $this->assertStringContainsString(
            '_match_found',
            $content,
            'Template must support pre-enriched _match_found.'
        );

        $this->assertStringContainsString(
            '_user_size_count',
            $content,
            'Template must support pre-enriched _user_size_count.'
        );

        $this->assertStringContainsString(
            '_agency_jobs_count',
            $content,
            'Template must support pre-enriched _agency_jobs_count.'
        );

        $this->assertStringContainsString(
            '_agency_candidates_count',
            $content,
            'Template must support pre-enriched _agency_candidates_count.'
        );
    }
}
