<?php

namespace App\Http\Controllers\ServiceCompany;

use App\Http\Controllers\Controller;
use App\Services\Candidate\ClientCandidateVersionStore;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Class CandidateVersionApiController
 *
 * Lightweight, 0% MySQL reads coordination endpoint for WebSocket reconnects and tab focus reconciliation.
 */
class CandidateVersionApiController extends Controller
{
    protected ClientCandidateVersionStore $versionStore;

    public function __construct(ClientCandidateVersionStore $versionStore)
    {
        $this->versionStore = $versionStore;
    }

    /**
     * Return current candidate collection version for a client.
     */
    public function getVersion(Request $request, int $clientId): JsonResponse
    {
        $companyId = (int) auth()->user()->company_id;

        if ($clientId <= 0 || $companyId <= 0) {
            abort(403, 'Invalid client or tenant identification.');
        }

        // Rule 3: Fail-Closed Tenant Authorization check before returning version
        try {
            $redis = cache()->store('dragonfly')->getRedis();
            $authKey = "auth:tenant_clients:{$companyId}";
            if ($redis->exists($authKey) && ! $redis->sismember($authKey, $clientId)) {
                abort(403, 'Unauthorized tenant access.');
            }
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            throw $e;
        } catch (\Throwable $e) {
            Log::emergency('Dragonfly unavailable for candidate version endpoint authorization', [
                'company_id' => $companyId,
                'client_id' => $clientId,
                'error' => $e->getMessage(),
            ]);
            abort(503, 'Coordination store unavailable.');
        }

        $version = $this->versionStore->getVersion($companyId, $clientId);

        return response()->json([
            'client_id' => $clientId,
            'client_candidate_version' => $version,
            'projection_schema_version' => 2,
            'server_time' => now()->toIso8601String(),
        ]);
    }
}
