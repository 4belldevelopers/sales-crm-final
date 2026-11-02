<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Services\ElasticsearchCircuitBreaker;
use App\Services\Marketplace\SuperAdminMarketplaceSearchService;
use App\Services\SuperAdmin\SuperAdminMarketplaceCacheManager;
use App\Services\SuperAdmin\SuperAdminMarketplaceMutationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Class SuperAdminMarketplaceController
 *
 * Intent:
 * Dedicated high-performance thin controller for Super Admin Marketplace Operations & Job Public Market.
 * Implements 100% CQRS read delegation to SuperAdminMarketplaceSearchService,
 * distributed Redis L2 caching with stampede protection via SuperAdminMarketplaceCacheManager,
 * and ACID transaction + outbox delegation to SuperAdminMarketplaceMutationService.
 *
 * Tenancy:
 * Super Admin global administrative scope with pre-cache 403 authorization gates.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminMarketplaceController extends SuperAdminBaseController
{
    public function __construct(
        private readonly SuperAdminMarketplaceSearchService $searchService,
        private readonly SuperAdminMarketplaceMutationService $mutationService,
        private readonly ?ElasticsearchCircuitBreaker $circuitBreaker = null
    ) {
        parent::__construct();
        $this->pageTitle = 'Marketplace Operations Command Center';
    }

    /**
     * Pre-cache authorization check verifying Super Admin credentials.
     */
    protected function authorizeSuperAdmin(): void
    {
        $user = auth()->user();
        $isAuthorized = $user && (
            ! empty($user->is_superadmin) ||
            (int) ($user->role_id ?? 0) === 1 ||
            ! empty($user->root_user)
        );

        if (! $isAuthorized) {
            abort(403, 'Unauthorized. Super Admin clearance required.');
        }
    }

    /**
     * Renders the unified Marketplace Command Center view.
     * Zero DB queries on initial render; client fetches data asynchronously.
     */
    public function index(Request $request, ?string $status = null): View
    {
        $this->authorizeSuperAdmin();

        $this->pageTitle = 'Marketplace Operations Command Center';
        $user = auth()->user();
        $this->user = $user;
        $this->companyName = $this->global?->company_name ?? config('app.name');
        $rawTab = $request->query('tab') ?? $status ?? $request->segment(3) ?? 'all';
        $tabMap = [
            'pending' => 'moderation',
            'approve' => 'live',
            'approved' => 'live',
            'hold' => 'quarantine',
            'reject' => 'delisted',
        ];
        $tab = $tabMap[$rawTab] ?? $rawTab;
        if (! in_array($tab, ['all', 'moderation', 'live', 'quarantine', 'delisted', 'claims', 'bench'])) {
            $tab = 'all';
        }
        $this->initialTab = $tab;

        // Zero-DB read retrieval of global settings via cache manager
        $globalSettings = SuperAdminMarketplaceCacheManager::getGlobalSettings();
        $this->autoApproval = (bool) $globalSettings->public_market_job_status;

        return view('super-admin.marketplace.index', array_merge($this->data, [
            'pageTitle' => $this->pageTitle,
            'user' => $user,
            'companyName' => $this->companyName,
            'adminTheme' => $this->adminTheme,
            'initialTab' => $this->initialTab,
            'autoApproval' => $this->autoApproval,
        ]));
    }

    /**
     * Returns real-time KPI metrics via Elasticsearch aggregations and Dragonfly L2 cache.
     */
    public function getTelemetryAjax(): JsonResponse
    {
        $this->authorizeSuperAdmin();

        $telemetry = $this->searchService->getTelemetryMetrics();
        $cbState = $this->circuitBreaker ? $this->circuitBreaker->getState() : 'CLOSED';

        return response()->json($telemetry)
            ->header('X-Circuit-Breaker', $cbState)
            ->header('X-Read-Source', $telemetry['source'] ?? 'elasticsearch');
    }

    /**
     * Executes 100% CQRS faceted requisition query with stampede protection.
     */
    public function getJobsGridAjax(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin();

        $filters = $request->only([
            'q', 'tab', 'status', 'company_id', 'employment_type',
            'min_salary', 'max_salary', 'page', 'per_page',
        ]);

        $results = $this->searchService->searchJobs($filters);
        $cbState = $this->circuitBreaker ? $this->circuitBreaker->getState() : 'CLOSED';

        return response()->json($results)
            ->header('X-Circuit-Breaker', $cbState)
            ->header('X-Read-Source', $results['source'] ?? 'elasticsearch');
    }

    /**
     * Returns granular requisition details for the 9-tab Command Center Drawer.
     * Compatible with both URL path parameter /job-detail-ajax/{id} and query parameter ?id=...
     */
    public function getJobDetailAjax(Request $request, ?int $id = null): JsonResponse
    {
        $this->authorizeSuperAdmin();

        $jobId = $id ?? (int) ($request->query('id') ?? $request->input('job_id') ?? $request->input('id') ?? 0);
        if ($jobId <= 0) {
            return response()->json(['status' => 'error', 'message' => 'Job ID is required.'], 400);
        }

        $detail = $this->searchService->getJobDetail($jobId);

        return response()->json($detail);
    }

    /**
     * 1-Click Approve a marketplace requisition with Two-Tier Idempotency.
     */
    public function approveJob(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'job_id' => 'required|integer',
            'notes' => 'nullable|string|max:500',
        ]);

        $jobId = (int) $request->input('job_id');
        $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('idempotency_key');
        $adminId = auth()->id() ?? 1;

        $result = $this->mutationService->approveJob(
            jobId: $jobId,
            notes: $request->input('notes'),
            idempotencyKey: $idempotencyKey,
            adminId: $adminId
        );

        $httpStatus = $result['http_status'] ?? 200;
        unset($result['http_status']);

        return response()->json($result, $httpStatus);
    }

    /**
     * Structured Rejection of a marketplace requisition with remediation code & idempotency.
     */
    public function rejectJob(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'job_id' => 'required|integer',
            'reason_code' => 'required|string|max:64',
            'notes' => 'nullable|string|max:1000',
        ]);

        $jobId = (int) $request->input('job_id');
        $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('idempotency_key');
        $adminId = auth()->id() ?? 1;

        $result = $this->mutationService->rejectJob(
            jobId: $jobId,
            reasonCode: $request->input('reason_code'),
            notes: $request->input('notes'),
            idempotencyKey: $idempotencyKey,
            adminId: $adminId
        );

        $httpStatus = $result['http_status'] ?? 200;
        unset($result['http_status']);

        return response()->json($result, $httpStatus);
    }

    /**
     * Toggles global Auto-Approval setting atomically with idempotency.
     */
    public function updateSettings(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'auto_approval' => 'required|boolean',
        ]);

        $autoApproval = $request->boolean('auto_approval');
        $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('idempotency_key');
        $adminId = auth()->id() ?? 1;

        $result = $this->mutationService->updateSettings(
            autoApproval: $autoApproval,
            idempotencyKey: $idempotencyKey,
            adminId: $adminId
        );

        $httpStatus = $result['http_status'] ?? 200;
        unset($result['http_status']);

        return response()->json($result, $httpStatus);
    }

    /**
     * Replays a Dead-Letter Queue (DLQ) event from sys_outbox_events.
     */
    public function replayOutboxEvent(Request $request): JsonResponse
    {
        $this->authorizeSuperAdmin();

        $request->validate([
            'event_id' => 'required|integer',
        ]);

        $idempotencyKey = $request->header('X-Idempotency-Key') ?? $request->input('idempotency_key');
        $result = $this->mutationService->replayOutboxEvent(
            eventId: (int) $request->input('event_id'),
            idempotencyKey: $idempotencyKey
        );

        $httpStatus = $result['http_status'] ?? 200;
        unset($result['http_status']);

        return response()->json($result, $httpStatus);
    }
}
