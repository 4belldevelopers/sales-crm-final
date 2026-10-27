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
                    'message' => 'Unauthorized access: Client is outside authenticated tenant boundary.',
                    'retryable' => false,
                    'request_id' => 'req_'.bin2hex(random_bytes(6)),
                ],
            ], 403);
        }

        // 2. Construct Dedicated Read Query Contract
        $query = ClientActivityQuery::fromAuthorizedContext($authContext, $request);

        // 3. Execute Read Service (Bounded _msearch + Redis caching)
        $viewModel = $readService->getActivityStream($query);

        // 4. Render Phase 1 presentation compatibility cards
        $html = view('service_company.ajax_pages.client_activity_cards_ajax', [
            'viewModel' => $viewModel,
            'items' => $viewModel->items,
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
