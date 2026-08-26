<?php

/**
 * ServiceCompanyProfileV2ApiController — Asynchronous V2 JSON API for recruiter profile dashboard.
 *
 * INTENT:
 * Serves lightweight, Redis-cached JSON payloads for recruiter profile cards,
 * leaderboards, monthly ranks, performance graphs, and interview trackers.
 * Replaces monolithic rendered-HTML responses with fast < 50ms JSON responses.
 *
 * AUTH & TENANCY:
 * Auth: Protected by 'auth' middleware group.
 * Tenancy: company_id & user_id are strictly derived from auth()->user().
 */

namespace App\Http\Controllers\ServiceCompany;

use App\Services\ServiceCompanyProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceCompanyProfileV2ApiController extends ServiceCompanyBaseController
{
    public function __construct(
        private readonly ServiceCompanyProfileService $profileService
    ) {
        parent::__construct();
    }

    /**
     * GET /service_company/api/v2/profile/kpis
     * Returns KPI header counters and candidate status pipeline counts.
     */
    public function kpis(): JsonResponse
    {
        $userId = (int) auth()->user()->id;
        $companyId = (int) auth()->user()->company_id;

        $data = $this->profileService->getKpiMetrics($userId, $companyId);

        return response()->json([
            'status' => 'ok',
            'data' => $data,
        ]);
    }

    /**
     * GET /service_company/api/v2/profile/leaderboard
     * Returns top 10 company leaderboard and current user's rank.
     */
    public function leaderboard(): JsonResponse
    {
        $userId = (int) auth()->user()->id;
        $companyId = (int) auth()->user()->company_id;

        $data = $this->profileService->getLeaderboard($companyId, $userId);

        return response()->json([
            'status' => 'ok',
            'data' => $data,
        ]);
    }

    /**
     * GET /service_company/api/v2/profile/ranks
     * Returns historical monthly rank performance.
     */
    public function ranks(): JsonResponse
    {
        $userId = (int) auth()->user()->id;
        $companyId = (int) auth()->user()->company_id;

        $data = $this->profileService->getMonthlyRanks($userId, $companyId);

        return response()->json([
            'status' => 'ok',
            'data' => $data,
        ]);
    }

    /**
     * GET /service_company/api/v2/profile/graphs
     * Returns 7-day candidate upload and 6-month offer histograms.
     */
    public function graphs(): JsonResponse
    {
        $userId = (int) auth()->user()->id;
        $companyId = (int) auth()->user()->company_id;

        $data = $this->profileService->getGraphData($userId, $companyId);

        return response()->json([
            'status' => 'ok',
            'data' => $data,
        ]);
    }

    /**
     * GET /service_company/api/v2/profile/list-items
     * Returns joined candidate list and upcoming interview schedules.
     */
    public function listItems(): JsonResponse
    {
        $userId = (int) auth()->user()->id;
        $companyId = (int) auth()->user()->company_id;

        $data = $this->profileService->getListItemsData($userId, $companyId);

        return response()->json([
            'status' => 'ok',
            'data' => $data,
        ]);
    }

    /**
     * POST /service_company/api/v2/profile/cache/invalidate
     * Force invalidates recruiter profile Redis cache.
     */
    public function invalidateCache(): JsonResponse
    {
        $userId = (int) auth()->user()->id;
        $companyId = (int) auth()->user()->company_id;

        $this->profileService->invalidateProfileCache($userId, $companyId);

        return response()->json([
            'status' => 'ok',
            'message' => 'Profile cache invalidated successfully.',
        ]);
    }
}
