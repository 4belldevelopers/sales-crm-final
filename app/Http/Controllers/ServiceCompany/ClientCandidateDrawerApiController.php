<?php

namespace App\Http\Controllers\ServiceCompany;

use App\Http\Controllers\Controller;
use App\Services\Candidate\CandidateDrawerQueryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Class ClientCandidateDrawerApiController
 *
 * Dedicated zero-MySQL-read API controller for Candidates Tab in Client Drawer.
 * Bypasses legacy BaseController constructor queries (GlobalSetting, SmsSetting, ThemeSetting)
 * and route middleware DB queries, ensuring 100% CQRS read isolation and 0% MySQL reads.
 */
class ClientCandidateDrawerApiController extends Controller
{
    public function __construct(
        protected CandidateDrawerQueryService $queryService
    ) {
        // Intentionally do NOT call parent::__construct() to maintain strictly 0 MySQL reads.
    }

    /**
     * Internal Client Candidates List.
     */
    public function clientCandidateList(Request $request): JsonResponse
    {
        $companyId = (int) auth()->user()->company_id;
        $clientId = (int) ($request->input('clientid') ?: $request->input('client_id'));

        $result = $this->queryService->getCandidates(
            $companyId,
            $clientId,
            'internal',
            [
                'search' => $request->input('search', $request->input('q', '')),
                'status_id' => $request->input('status_id'),
                'job_id' => $request->input('job_id'),
                'page' => $request->input('page', 1),
                'size' => $request->input('size', 25),
                'cursor' => $request->input('cursor'),
            ]
        );

        return response()->json($result);
    }

    /**
     * Direct Client Candidates List.
     */
    public function directclientCandidateList(Request $request): JsonResponse
    {
        $companyId = (int) (auth()->user()->company_id ?? 0);
        $clientId = (int) ($request->input('clientid') ?: $request->input('client_id'));

        if ($companyId <= 1) {
            // Admin/SuperAdmin view: resolve target agency tenancy from request or fallback
            $passedCompanyId = (int) ($request->input('company_id') ?: $request->input('service_company_id') ?: session('company_id') ?: 0);
            if ($passedCompanyId <= 1) {
                // Super Admin receives empty envelope to prevent cross-tenant data harvesting (TC-10)
                return response()->json([
                    'data' => [],
                    'meta' => [
                        'client_id' => $clientId,
                        'client_type' => 'direct',
                        'query_fingerprint' => '',
                        'client_candidate_version' => 1,
                        'projection_schema_version' => 2,
                        'stage_counts' => [
                            'sourcing' => 0,
                            'screening' => 0,
                            'evaluation' => 0,
                            'negotiations' => 0,
                            'offer' => 0,
                            'joined' => 0,
                            'rejected' => 0,
                            'in_review' => 0,
                        ],
                        'server_time' => now()->toIso8601String(),
                    ],
                    'pagination' => [
                        'mode' => 'page',
                        'total' => 0,
                        'total_relation' => 'eq',
                        'per_page' => 25,
                        'has_more' => false,
                        'current_page' => 1,
                        'last_page' => 1,
                    ],
                    'filters' => [
                        'search' => '',
                        'status_id' => null,
                        'job_id' => null,
                    ],
                ]);
            }
            $companyId = $passedCompanyId;
        }

        $clientType = (string) ($request->input('client_type') ?: ($request->input('clienttype') ?: 'direct'));

        $result = $this->queryService->getCandidates(
            $companyId,
            $clientId,
            $clientType,
            [
                'search' => $request->input('search', $request->input('q', '')),
                'status_id' => $request->input('status_id'),
                'job_id' => $request->input('job_id'),
                'page' => $request->input('page', 1),
                'size' => $request->input('size', 25),
                'cursor' => $request->input('cursor'),
            ]
        );

        return response()->json($result);
    }
}
