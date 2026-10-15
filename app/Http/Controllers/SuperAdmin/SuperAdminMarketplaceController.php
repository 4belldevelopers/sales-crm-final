<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Job;
use App\Models\SysOutboxEvent;
use App\Services\Marketplace\SuperAdminMarketplaceSearchService;
use App\Services\MarketplaceModerationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Class SuperAdminMarketplaceController
 *
 * Intent:
 * Dedicated thin controller for Super Admin Marketplace Operations Command Center.
 * Implements 100% CQRS read delegation to SuperAdminMarketplaceSearchService
 * and ACID transaction delegation to MarketplaceModerationService.
 *
 * Tenancy:
 * Super Admin global administrative scope.
 */
class SuperAdminMarketplaceController extends Controller
{
    public function __construct(
        private readonly SuperAdminMarketplaceSearchService $searchService,
        private readonly MarketplaceModerationService $moderationService
    ) {}

    /**
     * Renders the unified Marketplace Command Center view.
     * Zero DB queries on initial render; client fetches data asynchronously.
     */
    public function index(Request $request): View
    {
        return view('super-admin.marketplace.index', [
            'initialTab' => $request->query('tab', 'moderation'),
            'autoApproval' => (bool) DB::table('global_settings')->value('public_market_job_status'),
        ]);
    }

    /**
     * Returns real-time KPI metrics via Elasticsearch aggregations.
     */
    public function getTelemetryAjax(): JsonResponse
    {
        return response()->json($this->searchService->getTelemetryMetrics());
    }

    /**
     * Executes 100% CQRS faceted requisition query.
     */
    public function getJobsGridAjax(Request $request): JsonResponse
    {
        $filters = $request->only([
            'q', 'tab', 'status', 'company_id', 'employment_type',
            'min_salary', 'max_salary', 'page', 'per_page',
        ]);

        return response()->json($this->searchService->searchJobs($filters));
    }

    /**
     * Returns granular requisition details for the 9-tab Command Center Drawer.
     */
    public function getJobDetailAjax(int $id): JsonResponse
    {
        return response()->json($this->searchService->getJobDetail($id));
    }

    /**
     * 1-Click Approve a marketplace requisition.
     */
    public function approveJob(Request $request): JsonResponse
    {
        $request->validate([
            'job_id' => 'required|integer',
            'notes' => 'nullable|string|max:500',
        ]);

        $job = Job::findOrFail($request->input('job_id'));
        $success = $this->moderationService->approve(
            job: $job,
            actorId: auth()->id() ?? 1,
            actorRole: 'superadmin',
            notes: $request->input('notes')
        );

        return response()->json([
            'status' => $success ? 'success' : 'error',
            'message' => $success ? "Requisition #{$job->id} approved and published to marketplace." : 'Failed to approve job.',
        ]);
    }

    /**
     * Structured Rejection of a marketplace requisition with remediation code.
     */
    public function rejectJob(Request $request): JsonResponse
    {
        $request->validate([
            'job_id' => 'required|integer',
            'reason_code' => 'required|string|max:64',
            'notes' => 'nullable|string|max:1000',
        ]);

        $job = Job::findOrFail($request->input('job_id'));
        $success = $this->moderationService->reject(
            job: $job,
            actorId: auth()->id() ?? 1,
            reasonCode: $request->input('reason_code'),
            notes: $request->input('notes'),
            actorRole: 'superadmin'
        );

        return response()->json([
            'status' => $success ? 'success' : 'error',
            'message' => $success ? "Requisition #{$job->id} rejected with remediation instructions sent." : 'Failed to reject job.',
        ]);
    }

    /**
     * Toggles global Auto-Approval setting.
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $request->validate([
            'auto_approval' => 'required|boolean',
        ]);

        $newVal = $request->boolean('auto_approval') ? 1 : 0;
        DB::table('global_settings')->update(['public_market_job_status' => $newVal]);

        return response()->json([
            'status' => 'success',
            'auto_approval' => (bool) $newVal,
            'message' => $newVal ? 'Auto-Approval enabled (Trust Score >= 85).' : 'Auto-Approval paused (Manual review required).',
        ]);
    }

    /**
     * Replays a Dead-Letter Queue (DLQ) event from sys_outbox_events.
     */
    public function replayOutboxEvent(Request $request): JsonResponse
    {
        $request->validate([
            'event_id' => 'required|integer',
        ]);

        $event = SysOutboxEvent::findOrFail($request->input('event_id'));
        $event->update([
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'last_error' => null,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => "Event #{$event->id} rescheduled for outbox processing.",
        ]);
    }
}
