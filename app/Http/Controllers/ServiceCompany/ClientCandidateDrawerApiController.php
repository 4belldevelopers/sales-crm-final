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
        $companyId = (int) auth()->user()->company_id;
        $clientId = (int) ($request->input('clientid') ?: $request->input('client_id'));

        $result = $this->queryService->getCandidates(
            $companyId,
            $clientId,
            'direct',
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
