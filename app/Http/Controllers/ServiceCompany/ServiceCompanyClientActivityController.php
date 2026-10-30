<?php

namespace App\Http\Controllers\ServiceCompany;

use App\DTO\ServiceCompany\ClientAccessContext;
use App\DTO\ServiceCompany\ClientActivityQuery;
use App\Http\Controllers\Controller;
use App\Services\ServiceCompany\ClientActivityReadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

/**
 * ServiceCompanyClientActivityController
 *
 * Dedicated CQRS Controller for Client Drawer Dynamic Activity Tab.
 * Guarantees:
 * 1. 0% MySQL reads on the request path.
 * 2. Strict tenancy boundary verification via ClientAccessContext.
 * 3. JSON-first contract with presentation-compatible HTML payload for Phase 1.
 */
class ServiceCompanyClientActivityController extends Controller
{
    public function __construct() {}

    /**
     * Resolves paginated and filtered activity stream for the client drawer.
     */
    public function getActivityData(Request $request, ClientActivityReadService $readService): JsonResponse
    {
        // 1. Pre-ES Gate: Tenant-scoped authorization check
        $authContext = ClientAccessContext::fromRequest($request);

        if (! $authContext->isAuthorized) {
            return response()->json([
                'success' => false,
                'error' => [
                    'code' => 'FORBIDDEN',
                    'message' => 'Unauthorized access: Client is outside authenticated tenant boundary or restricted for Super Admin.',
                    'retryable' => false,
                    'request_id' => 'req_'.bin2hex(random_bytes(6)),
                ],
                'data' => [],
                'kpi' => [
                    'total_activities' => 0,
                    'pipeline_events' => 0,
                    'interview_events' => 0,
                    'offer_events' => 0,
                    'job_events' => 0,
                    'note_events' => 0,
                    'all' => 0,
                    'jobs' => 0,
                    'candidates' => 0,
                    'interviews' => 0,
                    'pipeline' => 0,
                    'offers' => 0,
                    'crm' => 0,
                ],
                'sync' => [
                    'baseline_sequence' => 0,
                    'from_sequence' => 0,
                    'to_sequence' => 0,
                    'last_contiguous_sequence' => 0,
                    'highest_sequence' => 0,
                    'highest_authoritative_sequence' => 0,
                    'highest_projected_sequence' => 0,
                    'missing_sequences' => [],
                    'projection_lagging' => false,
                    'projection_lag_seconds' => 0.0,
                    'projection_version' => 1,
                ],
                'pagination' => [
                    'next_cursor' => null,
                    'has_more' => false,
                ],
                'health' => [
                    'score' => 0,
                    'label' => 'Inactive',
                    'last_activity_at' => null,
                ],
                'items' => [],
                'html' => '',
            ], 403);
        }

        // 2. Construct Dedicated Read Query Contract
        $query = ClientActivityQuery::fromAuthorizedContext($authContext, $request);

        // 3. Execute Read Service (Bounded _msearch + Redis caching)
        $viewModel = $readService->getActivityStream($query);

        // 4. Render Phase 1 presentation compatibility cards
        $targetCompanyId = $authContext->serviceCompanyId > 0 ? $authContext->serviceCompanyId : $authContext->tenantId;
        $companyTimezone = \App\Helper\GeneralMethod::defaultTimeZone($targetCompanyId);
        $html = view('service_company.ajax_pages.client_activity_cards_ajax', [
            'viewModel' => $viewModel,
            'items' => $viewModel->items,
            'companyTimezone' => $companyTimezone,
        ])->render();

        // 5. Authoritative JSON-First Response Envelope
        return response()->json([
            'success' => true,
            'data' => $viewModel->items,
            'kpi' => $viewModel->kpis,
            'sync' => $viewModel->sync,
            'pagination' => $viewModel->pagination,
            'health' => $viewModel->health,
            'meta' => $viewModel->meta,
            'html' => $html,
        ]);
    }
}
