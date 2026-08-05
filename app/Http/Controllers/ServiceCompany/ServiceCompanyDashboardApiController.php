<?php

/**
 * ServiceCompanyDashboardApiController — V2 JSON API endpoints for the dashboard.
 *
 * Phase 4 of the V2 Dashboard plan.
 * Returns compact JSON from Redis cache (~2.5 KB per response) instead of the
 * old ~500 KB rendered-HTML cache that the monolithic index() method produced.
 *
 * Auth: all endpoints are inside the existing 'auth' middleware group (see service.php).
 * Tenancy: company_id is always taken from auth()->user()->company_id (Rule #3).
 */

namespace App\Http\Controllers\ServiceCompany;

use App\Services\ServiceCompanyDashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceCompanyDashboardApiController extends ServiceCompanyBaseController
{
    public function __construct(
        private readonly ServiceCompanyDashboardService $dashboardService,
    ) {
        parent::__construct();
    }

    /**
     * GET /api/v2/service-company/dashboard/kpis
     *
     * Top-card metrics: coin balances, active job count, active user count,
     * today's interview count. Cached 60 seconds in Redis per company.
     */
    public function kpis(): JsonResponse
    {
        $data = $this->dashboardService->getCompanyKpiMetrics(
            (int) auth()->user()->company_id
        );

        return response()->json([
            'status' => 'ok',
            'data'   => $data,
        ]);
    }

    /**
     * GET /api/v2/service-company/dashboard/leaderboards
     *
     * Top-10 recruiters by submission count (last 30 days).
     * Cached 60 seconds in Redis per company.
     */
    public function leaderboards(): JsonResponse
    {
        $data = $this->dashboardService->getRecruiterLeaderboard(
            (int) auth()->user()->company_id
        );

        return response()->json([
            'status' => 'ok',
            'data'   => $data,
        ]);
    }

    /**
     * GET /api/v2/service-company/dashboard/interviews
     *
     * Today's interview schedule for the authenticated user.
     * Cached 60 seconds in Redis per company+user.
     */
    public function interviews(): JsonResponse
    {
        $data = $this->dashboardService->getTodayInterviews(
            (int) auth()->user()->company_id,
            (int) auth()->user()->id
        );

        return response()->json([
            'status' => 'ok',
            'data'   => $data,
        ]);
    }

    /**
     * POST /api/v2/service-company/dashboard/cache/invalidate
     *
     * Force-invalidates the company's dashboard Redis cache.
     * Called after bulk operations (mass status change, etc.).
     */
    public function invalidateCache(): JsonResponse
    {
        $this->dashboardService->invalidateCache(
            (int) auth()->user()->company_id
        );

        return response()->json(['status' => 'ok', 'message' => 'Dashboard cache cleared.']);
    }
}
