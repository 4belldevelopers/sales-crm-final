<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * JobVisibilityScopeResolver
 *
 * Resolves all client visibility scopes currently authorized to view a mutated job requisition.
 * In JERO's B2B marketplace, a single requisition may be shared with multiple service companies
 * and direct clients via active recommendation_relation records.
 *
 * NOTE: Executes strictly inside asynchronous Horizon Outbox workers;
 * NEVER on the web request path (0% MySQL reads on request path preserved).
 */
class JobVisibilityScopeResolver
{
    /**
     * Resolves all affected client scopes for a job mutation:
     * 1. The owning internal client scope.
     * 2. All active partner recommendation direct client scopes.
     *
     * @return array<int, array{tenant_id: int, service_company_id: int, client_type: string, partner_company_id: int, client_id: int}>
     */
    public function resolveAffectedScopes(int $jobId, array $payload): array
    {
        $scopes = [];

        $owningTenantId = (int) ($payload['tenant_id'] ?? 0);
        $owningCompanyId = (int) ($payload['company_id'] ?? $payload['service_company_id'] ?? 0);
        $owningClientId = (int) ($payload['client_id'] ?? 0);

        // 1. Owning Internal Client Scope
        if ($owningCompanyId > 0 && $owningClientId > 0) {
            $scopes[] = [
                'tenant_id'          => $owningTenantId ?: $owningCompanyId,
                'service_company_id' => $owningCompanyId,
                'client_type'        => 'internal',
                'partner_company_id' => 0,
                'client_id'          => $owningClientId,
            ];
        }

        // 2. Marketplace Recommendation Fan-Out Scopes
        // Query active shared recommendations from recommendation_relation table
        $recommendations = DB::table('recommendation_relation')
            ->select('to_company_id', 'from_company_id')
            ->where('job_id', $jobId)
            ->where('final_status', 1)
            ->get();

        foreach ($recommendations as $rec) {
            $toCompanyId = (int) ($rec->to_company_id ?? 0);
            $fromCompanyId = (int) ($rec->from_company_id ?? 0);

            if ($toCompanyId > 0 && $owningClientId > 0) {
                $scopes[] = [
                    'tenant_id'          => $toCompanyId,
                    'service_company_id' => $toCompanyId,
                    'client_type'        => 'direct',
                    'partner_company_id' => $fromCompanyId,
                    'client_id'          => $owningClientId,
                ];
            }
        }

        return $scopes;
    }
}
