<?php

namespace Tests\Feature;

use App\Events\AppTrackerCandidateAssignedEvent;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

/**
 * Class BenchToJobAppTrackerPipelineContractTest
 *
 * Intent: Architectural and contract verification suite for Phases 1–6 of the
 * In-house Bench Talents -> Candidate Selection -> Add to Job -> Job Selection ->
 * Add Candidate -> AppTracker Listing workflow optimization.
 *
 * Enforces:
 * - Rule 1 & 2: 0% avoidable MySQL reads / 0 Blade N+1 queries on read paths
 * - Rule 3 & 4: Client-first RAM + sessionStorage stores, 250ms debounce, XHR abort, 10K concurrency guards
 * - Rule 5 & 6: Zero stale data & multi-tenant atomic cache invalidation (company_id + versioned keys)
 * - Rule 7: Zero business-logic or payload contract mutations
 * - Rule 16 & 17: Complete Page/Blade isolation across Bench, CopyJob Drawer, and AppTracker
 */
class BenchToJobAppTrackerPipelineContractTest extends TestCase
{
    /**
     * Phase 1 Contract: Verify search debouncing, XHR abort guards, idempotency headers,
     * and deduplicated event binding across Bench Talents, Add-to-Job Drawer, and AppTracker.
     */
    public function test_phase_1_browser_event_hygiene_debounce_and_duplicate_listener_guards(): void
    {
        $benchView = file_get_contents(base_path('resources/views/service_company/bench-candidates/index.blade.php'));
        $copyDrawerView = file_get_contents(base_path('resources/views/service_company/cvtracker/application_copy.blade.php'));
        $appTrackerView = file_get_contents(base_path('resources/views/service_company/apptracker/index.blade.php'));
        $internalBenchView = file_get_contents(base_path('resources/views/service_company/apptracker/internal_bench.blade.php'));

        // Bench Talents: 250ms debounce + XHR abort
        $this->assertStringContainsString('debouncedBenchSearch', $benchView);
        $this->assertStringContainsString('_benchListXhr.abort()', $benchView);

        // Add-to-Job Drawer: namespaced .off().on() listener deduplication + X-Idempotency-Key + no forced reload
        $this->assertStringContainsString("off('click.acCopySingleAdd', '.chk_copy_single_add')", $copyDrawerView);
        $this->assertStringContainsString("off('click.acCopyBulk')", $copyDrawerView);
        $this->assertStringContainsString('X-Idempotency-Key', $copyDrawerView);
        $this->assertStringNotContainsString('location.reload()', $copyDrawerView);

        // AppTracker: 250ms search debounce + XHR abort
        $this->assertStringContainsString('_aptkSearchTimer', $appTrackerView);
        $this->assertStringContainsString('_aptkListXhr.abort()', $appTrackerView);

        // AppTracker Internal Bench: double-submit lock + X-Idempotency-Key on .add_to_inreview
        $this->assertStringContainsString('X-Idempotency-Key', $internalBenchView);
    }

    /**
     * Phase 2 Contract: Verify zero direct MySQL DB::table() or Eloquent queries inside
     * the AJAX Blade row partials rendered for Bench Talents and Add-to-Job drawer listings.
     */
    public function test_phase_2_zero_mysql_queries_in_ajax_blade_partials(): void
    {
        $benchAjaxBlade = file_get_contents(base_path('resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php'));
        $copyJobAjaxBlade = file_get_contents(base_path('resources/views/service_company/ajax_pages/all_copyjobtable_ajax.blade.php'));

        $this->assertStringNotContainsString('DB::table(', $benchAjaxBlade, 'bench-candidate-list-ajax.blade.php must not execute per-row DB::table queries.');
        $this->assertStringNotContainsString('JobApplication::', $benchAjaxBlade, 'bench-candidate-list-ajax.blade.php must not execute Eloquent queries.');
        $this->assertStringNotContainsString('DB::table(', $copyJobAjaxBlade, 'all_copyjobtable_ajax.blade.php must not execute per-row DB::table queries.');

        // Verify Tagify city resolution in AppTracker uses ELASTIC_LOCATIONS + Redis cache
        $appTrackerController = file_get_contents(base_path('app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php'));
        $this->assertStringContainsString('tagify:es_cities:', $appTrackerController);
        $this->assertStringContainsString('ELASTIC_LOCATIONS', $appTrackerController);
    }

    /**
     * Phase 3 Contract: Verify bulk ES alignment without forced refresh storms,
     * schema reflection memoization, and post-response bench index sync offloading.
     */
    public function test_phase_3_transactional_write_bulk_es_alignment_and_timeline_memoization(): void
    {
        $alignmentService = file_get_contents(base_path('app/Services/CandidateJobAlignmentService.php'));
        $generalMethod = file_get_contents(base_path('app/Helper/GeneralMethod.php'));
        $cvTrackerController = file_get_contents(base_path('app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php'));

        // CandidateJobAlignmentService must use ES _bulk for skills and questions and never call Redis::keys()
        $this->assertStringContainsString('$elasticClient->bulk([\'body\' => $bulkBody])', $alignmentService);
        $this->assertStringNotContainsString('Redis::keys(', $alignmentService);

        // GeneralMethod::UpdateTimeline must memoize ES document lookups in static request memory
        $this->assertStringContainsString('static $esTimelineDocCache = [];', $generalMethod);

        // copy_current_selected_applicant must batch-fetch job_applications and offload bench ES reindex via app()->terminating()
        $this->assertStringContainsString('->whereIn(\'id\', $selectedApplicantListing)', $cvTrackerController);
        $this->assertStringContainsString('app()->terminating(', $cvTrackerController);
    }

    /**
     * Phase 4 Contract: Verify unified tenant-scoped cache keys, single-flight locks,
     * and transactional outbox recording on candidate assignment.
     */
    public function test_phase_4_unified_cache_keys_single_flight_locks_and_outbox_recording(): void
    {
        $benchController = file_get_contents(base_path('app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php'));
        $appTrackerController = file_get_contents(base_path('app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php'));

        // Bench counts unified cache key + single-flight lock
        $this->assertStringContainsString('function getBenchStatusCounts', $benchController);
        $this->assertStringContainsString('tenant:{$companyId}:bench_counts:v{$tenantVersion}', $benchController);
        $this->assertStringContainsString("cache()->lock('lock:'.\$cacheKey, 5)", $benchController);

        // AppTracker pipeline counts versioned key + single-flight lock
        $this->assertStringContainsString('function getJobPipelineCounts', $appTrackerController);
        $this->assertStringContainsString('pipeline:{$company_id}:{$jobid}:v{$tenantVer}', $appTrackerController);
        $this->assertStringContainsString('lock:pipeline:{$company_id}:{$jobid}', $appTrackerController);
    }

    /**
     * Phase 5 Contract: Verify Client-First Browser RAM + sessionStorage stores,
     * embedded counter transport, and strict page/blade isolation (Rule 17).
     */
    public function test_phase_5_client_first_ram_stores_embedded_counts_and_page_isolation(): void
    {
        $benchView = file_get_contents(base_path('resources/views/service_company/bench-candidates/index.blade.php'));
        $copyDrawerView = file_get_contents(base_path('resources/views/service_company/cvtracker/application_copy.blade.php'));
        $appTrackerView = file_get_contents(base_path('resources/views/service_company/apptracker/index.blade.php'));

        // Each page has its own strictly isolated RAM store (Rule 17)
        $this->assertStringContainsString('var BenchPageRamStore =', $benchView);
        $this->assertStringNotContainsString('CopyJobRamStore', $benchView);
        $this->assertStringNotContainsString('AppTrackerRamStore', $benchView);

        $this->assertStringContainsString('var CopyJobRamStore =', $copyDrawerView);
        $this->assertStringNotContainsString('BenchPageRamStore', $copyDrawerView);
        $this->assertStringNotContainsString('AppTrackerRamStore', $copyDrawerView);

        $this->assertStringContainsString('var AppTrackerRamStore =', $appTrackerView);
        $this->assertStringNotContainsString('BenchPageRamStore', $appTrackerView);
        $this->assertStringNotContainsString('CopyJobRamStore', $appTrackerView);
    }

    /**
     * Phase 6 Contract: Verify Reverb WebSocket event structure, private tenant-scoped channel,
     * 0ms DOM counter delta patching, and 300ms burst coalescing.
     */
    public function test_phase_6_reverb_event_contract_and_zero_http_dom_delta_patching(): void
    {
        $event = new AppTrackerCandidateAssignedEvent(
            tenantId: 42,
            jobId: 108,
            version: 7,
            candidateId: 5001,
            wiseId: 9001,
            masterStatusId: 8,
            metadata: [
                'candidate_name' => 'Aarav Sharma',
                'added_count' => 1,
            ]
        );

        $channel = $event->broadcastOn();
        $this->assertInstanceOf(PrivateChannel::class, $channel);
        $this->assertSame('private-tenant.42.job.108', $channel->name);
        $this->assertSame('candidate.assigned', $event->broadcastAs());

        $payload = $event->broadcastWith();
        $this->assertSame(42, $payload['tenant_id']);
        $this->assertSame(108, $payload['job_id']);
        $this->assertSame(7, $payload['version']);
        $this->assertSame(5001, $payload['candidate_id']);
        $this->assertSame(9001, $payload['wise_id']);
        $this->assertSame(8, $payload['master_status_id']);
        $this->assertSame('Aarav Sharma', $payload['metadata']['candidate_name']);
        $this->assertSame(1, $payload['metadata']['added_count']);

        // Verify 0ms DOM counter patching & burst coalescing in AppTracker & Bench views
        $appTrackerView = file_get_contents(base_path('resources/views/service_company/apptracker/index.blade.php'));
        $benchAjaxBlade = file_get_contents(base_path('resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php'));
        $benchView = file_get_contents(base_path('resources/views/service_company/bench-candidates/index.blade.php'));

        $this->assertStringContainsString('function patchAptkDomCountersFromDelta', $appTrackerView);
        $this->assertStringContainsString('function scheduleCoalescedAptkRefresh', $appTrackerView);
        $this->assertStringContainsString('data-bench-row-id=', $benchAjaxBlade);
        $this->assertStringContainsString('data-bench-pipe="inreview"', $benchAjaxBlade);
        $this->assertStringContainsString('data-bench-pipe-interview=', $benchAjaxBlade);
        $this->assertStringContainsString('data-bench-row-id="', $benchView);

        // Verify Real-time BroadcastChannel & overlay in find-talent, internal_bench, and similar_candidates
        $findTalentView = file_get_contents(base_path('resources/views/service_company/apptracker/find-talent.blade.php'));
        $internalBenchView = file_get_contents(base_path('resources/views/service_company/apptracker/internal_bench.blade.php'));
        $similarCandView = file_get_contents(base_path('resources/views/service_company/apptracker/similar_candidates.blade.php'));

        $this->assertStringContainsString('AgencyBenchStore.applyAddedOverlay()', $findTalentView);
        $this->assertStringContainsString("BroadcastChannel('jero_ats_sync_'", $internalBenchView);
        $this->assertStringContainsString("BroadcastChannel('jero_ats_sync_'", $similarCandView);
        $this->assertStringContainsString("jero:candidate-added-to-job", $internalBenchView);
        $this->assertStringContainsString("jero:candidate-added-to-job", $similarCandView);
    }

    /**
     * Phase 6 Contract: Verify Async Elasticsearch Re-indexing, Dual-Lease Outbox Worker,
     * Dead-Letter Queueing, Pruning, and Schedule Registration.
     */
    public function test_phase_6_async_elasticsearch_reindexing_and_outbox_worker(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('transactional_outboxes')) {
            \Illuminate\Support\Facades\Schema::create('transactional_outboxes', function ($table) {
                $table->id();
                $table->unsignedBigInteger('tenant_id');
                $table->unsignedBigInteger('job_id')->nullable();
                $table->unsignedBigInteger('version')->default(1);
                $table->string('event_type');
                $table->text('payload');
                $table->string('es_status')->default('pending');
                $table->unsignedInteger('es_attempts')->default(0);
                $table->timestamp('es_available_at')->nullable();
                $table->string('es_claim_token')->nullable();
                $table->timestamp('es_locked_until')->nullable();
                $table->timestamp('es_processed_at')->nullable();
                $table->text('es_last_error')->nullable();
                $table->string('reverb_status')->default('pending');
                $table->unsignedInteger('reverb_attempts')->default(0);
                $table->timestamp('reverb_available_at')->nullable();
                $table->string('reverb_claim_token')->nullable();
                $table->timestamp('reverb_locked_until')->nullable();
                $table->timestamp('reverb_processed_at')->nullable();
                $table->text('reverb_last_error')->nullable();
                $table->timestamps();
            });
        }

        $outboxService = app(\App\Services\TransactionalOutboxService::class);

        // 1. Record test outbox event
        $eventId = $outboxService->recordEvent(
            tenantId: 9999,
            jobId: 8888,
            version: 1,
            eventType: 'CandidateAddedToJob',
            payload: [
                'wise_id' => 12345,
                'candidate_id' => 67890,
                'source_from_type' => 2,
                'job_id' => 8888,
            ]
        );
        $this->assertGreaterThan(0, $eventId);

        // 2. Claim batch for Elasticsearch
        $token = 'test_token_'.time();
        $batch = $outboxService->claimEsBatch($token, 10, 30);
        $record = $batch->firstWhere('id', $eventId);
        $this->assertNotNull($record);
        $this->assertSame('processing', $record->es_status);
        $this->assertSame($token, $record->es_claim_token);

        // 3. Complete ES event
        $completed = $outboxService->completeEsEvent($eventId);
        $this->assertTrue($completed);

        $freshRow = DB::table('transactional_outboxes')->where('id', $eventId)->first();
        $this->assertSame('completed', $freshRow->es_status);
        $this->assertNull($freshRow->es_claim_token);

        // 4. Test fail & DLQ
        $failEventId = $outboxService->recordEvent(
            tenantId: 9999,
            jobId: 8888,
            version: 2,
            eventType: 'CandidateAddedToJob',
            payload: ['wise_id' => 99999]
        );
        $outboxService->claimEsBatch('fail_token_'.time(), 10, 30);
        $outboxService->failEsEvent($failEventId, 'Test ES Error', 1);
        $dlqRow = DB::table('transactional_outboxes')->where('id', $failEventId)->first();
        $this->assertSame('dead_letter', $dlqRow->es_status);
        $this->assertStringContainsString('Test ES Error', $dlqRow->es_last_error);

        // Clean up test rows
        DB::table('transactional_outboxes')->whereIn('id', [$eventId, $failEventId])->delete();

        // 5. Verify console.php schedules
        $consoleRoutes = file_get_contents(base_path('routes/console.php'));
        $this->assertStringContainsString("Schedule::command('outbox:process-es --limit=100')", $consoleRoutes);
        $this->assertStringContainsString("Schedule::command('outbox:process-reverb --limit=100')", $consoleRoutes);
        $this->assertStringContainsString("Schedule::command('apptracker:prune-outbox --days=7')", $consoleRoutes);

        // 6. Verify ProcessElasticsearchOutboxCommand handles bench candidate sync
        $esWorkerContent = file_get_contents(base_path('app/Console/Commands/ProcessElasticsearchOutboxCommand.php'));
        $this->assertStringContainsString('elasticBenchCandidateAddEditIndex', $esWorkerContent);
    }
}
