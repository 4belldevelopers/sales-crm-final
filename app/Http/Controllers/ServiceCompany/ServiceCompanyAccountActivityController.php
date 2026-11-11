<?php

namespace App\Http\Controllers\ServiceCompany;

use App\Http\Controllers\Controller;
use App\Services\ServiceCompany\CompanyActivityReadService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Controller: ServiceCompanyAccountActivityController
 *
 * Dedicated CQRS controller for the Company Activity & Audit Timeline on the Company Profile.
 * Completely independent of client-based drawers. Scoped strictly to company users' activity.
 *
 * Tenancy: auth()->user()->company_id
 */
class ServiceCompanyAccountActivityController extends Controller
{
    protected CompanyActivityReadService $activityReadService;

    public function __construct(CompanyActivityReadService $activityReadService)
    {
        $this->activityReadService = $activityReadService;
    }

    /**
     * Resolves paginated and filtered activity stream for the company profile activity tab.
     */
    public function getActivityData(Request $request): JsonResponse
    {
        $user = auth()->user();
        if (! $user || ! $user->company_id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized or missing company context.',
                'data' => [],
                'kpi' => ['all' => 0, 'pipeline' => 0, 'interviews' => 0, 'offers' => 0, 'jobs' => 0, 'team' => 0],
                'html' => '',
            ], 403);
        }

        $companyId = (int) $user->company_id;

        $filters = [
            'category' => $request->input('category', 'all'),
            'timeframe' => $request->input('timeframe', 'all'),
            'search' => $request->input('search', ''),
            'page' => (int) $request->input('page', 1),
            'per_page' => (int) $request->input('per_page', 15),
        ];

        $stream = $this->activityReadService->getActivityStream($companyId, $filters);

        $html = view('service_company.account.components.company-activity-cards-ajax', [
            'items' => $stream['items'],
            'page' => $stream['pagination']['page'],
        ])->render();

        return response()->json([
            'success' => true,
            'data' => $stream['items'],
            'kpi' => $stream['kpis'],
            'pagination' => $stream['pagination'],
            'health' => $stream['health'],
            'html' => $html,
        ]);
    }
}
