<?php

namespace App\Http\Controllers\ServiceCompany;

use App\DTO\ServiceCompany\AgencyAccessContext;
use App\DTO\ServiceCompany\AgencyActivityQuery;
use App\Services\AgencyActivityLoggerService;
use App\Services\ServiceCompany\AgencyActivityReadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ServiceCompanyAgencyActivityController extends ServiceCompanyBaseController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * CQRS Read Endpoint: Fetch Agency Activity stream, KPIs, and pre-rendered cards HTML
     * Guarantees 0% MySQL reads on the request path.
     */
    public function getActivityData(Request $request, AgencyActivityReadService $readService): JsonResponse
    {
        try {
            $context = AgencyAccessContext::fromRequest($request);
            if (! $context->isAuthorized) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized or invalid agency context.',
                ], 403);
            }

            $query = AgencyActivityQuery::fromAuthorizedContext($context, $request);
            $viewModel = $readService->getActivityStream($query, $context);

            $html = view('service_company.ajax_pages.agency_activity_cards_ajax', [
                'viewModel' => $viewModel,
                'items' => $viewModel->items,
                'groupedItems' => $viewModel->groupedItems,
                'cursorSequence' => $query->cursorSequence,
            ])->render();

            return response()->json([
                'success' => true,
                'data' => $viewModel->items,
                'grouped_data' => $viewModel->groupedItems,
                'kpi' => $viewModel->kpis,
                'hero' => $viewModel->heroMetrics,
                'health' => $viewModel->health,
                'pagination' => $viewModel->pagination,
                'meta' => $viewModel->meta,
                'filters' => $viewModel->activeFilters,
                'html' => $html,
            ]);
        } catch (\Throwable $e) {
            Log::error('AgencyActivityController::getActivityData error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load agency activity feed: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * CQRS Write Endpoint: Log Quick Interaction Note
     * Writes to MySQL write-store, syncs to Elasticsearch, invalidates cache, and broadcasts.
     */
    public function saveQuickNote(Request $request, AgencyActivityLoggerService $loggerService): JsonResponse
    {
        $validated = $request->validate([
            'agency_company_id' => 'required|integer|min:1',
            'note' => 'required|string|min:2|max:2000',
        ]);

        try {
            $context = AgencyAccessContext::fromRequest($request);
            if (! $context->isAuthorized) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized agency context.',
                ], 403);
            }

            $user = auth()->user();
            $actorId = (int) ($user->id ?? 0);
            $actorName = $user->name ?? 'User';
            $actorRole = method_exists($user, 'roles') && $user->roles->first()
                ? ($user->roles->first()->display_name ?? $user->roles->first()->name)
                : 'Recruiter';

            $log = $loggerService->logQuickNote(
                agencyCompanyId: $context->agencyCompanyId,
                serviceCompanyId: $context->serviceCompanyId,
                note: trim($validated['note']),
                actorId: $actorId,
                actorName: $actorName,
                actorRole: $actorRole
            );

            return response()->json([
                'success' => true,
                'message' => 'Interaction note logged successfully.',
                'event_uuid' => $log->event_uuid,
            ]);
        } catch (\Throwable $e) {
            Log::error('AgencyActivityController::saveQuickNote error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to log interaction note: ' . $e->getMessage(),
            ], 500);
        }
    }
}
