<?php

namespace App\DTO\ServiceCompany;

use Illuminate\Http\Request;

/**
 * ClientJobsQuery
 *
 * Dedicated Read Query Contract for the Client Drawer Jobs Tab.
 * Strictly constructed from an already-authorized ClientAccessContext,
 * guaranteeing untrusted external parameters cannot reach Elasticsearch.
 */
class ClientJobsQuery
{
    public function __construct(
        public readonly int $tenantId,          // Top-level account boundary
        public readonly int $serviceCompanyId,  // Operating recruitment company
        public readonly int $clientId,          // Authoritatively validated client
        public readonly string $clientType,     // 'internal' | 'direct'
        public readonly ?int $partnerCompanyId, // Marketplace partner boundary
        public readonly ?string $search,
        public readonly ?string $status,        // 'all' | 'hiring' | 'hold' | 'closed' | 'inactive'
        public readonly int $page = 1,
        public readonly int $perPage = 10,
        public readonly string $sort = 'created_at',
        public readonly string $direction = 'desc'
    ) {}

    /**
     * Strictly construct from a policy-approved context.
     * Raw external parameters CANNOT instantiate this query without authorization.
     */
    public static function fromAuthorizedContext(ClientAccessContext $context, Request $request): self
    {
        if (!$context->isAuthorized) {
            abort(403, 'Unauthorized access: Client is outside authenticated tenant boundary.');
        }

        $rawSearch = $request->input('search') ?: $request->input('custom_search') ?: $request->input('q');
        $rawStatus = $request->input('status') ?: $request->input('custom_status') ?: 'all';

        return new self(
            tenantId: $context->tenantId,
            serviceCompanyId: $context->serviceCompanyId,
            clientId: $context->clientId,
            clientType: $context->clientType,
            partnerCompanyId: $context->partnerCompanyId,
            search: !empty($rawSearch) ? trim((string) $rawSearch) : null,
            status: strtolower((string) $rawStatus),
            page: max(1, (int) $request->input('page', 1)),
            perPage: min(50, max(1, (int) $request->input('per_page', $request->input('size', 10)))),
            sort: $request->input('sort', 'created_at'),
            direction: strtolower($request->input('direction', 'desc')) === 'asc' ? 'asc' : 'desc'
        );
    }

    /**
     * Cache Architecture Terminology & Specifications:
     * 1. 5 Static Identity Dimensions:
     *    {tenant}:{serviceCompany}:{clientType}:{partnerCompany}:{client}
     * 2. Redis Monotonic Invalidation Key (Stores scalar integer version):
     *    ats:client_jobs_version:{tenant}:{serviceCompany}:{clientType}:{partnerCompany}:{client}
     * 3. Query Result Cache Key (Incorporates current projectionVersion + normalized query hash):
     *    ats:client_jobs:v1:{tenant}:{serviceCompany}:{clientType}:{partnerCompany}:{client}:{projectionVersion}:{hash}
     */
    public function toCacheKey(int $projectionVersion): string
    {
        $partnerPart = $this->partnerCompanyId ?? 0;
        $normalizedPayload = [
            'tenant'             => $this->tenantId,
            'service_company'    => $this->serviceCompanyId,
            'client'             => $this->clientId,
            'client_type'        => $this->clientType,
            'partner_company'    => $partnerPart,
            'projection_version' => $projectionVersion,
            'search'             => $this->search,
            'status'             => $this->status,
            'page'               => $this->page,
            'per_page'           => $this->perPage,
            'sort'               => $this->sort,
            'direction'          => $this->direction,
        ];

        return "ats:client_jobs:v1:{$this->tenantId}:{$this->serviceCompanyId}:{$this->clientType}:{$partnerPart}:{$this->clientId}:{$projectionVersion}:" . md5(json_encode($normalizedPayload));
    }
}
