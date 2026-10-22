<?php

namespace App\DTO;

/**
 * Class ClientRequestDTO
 *
 * Intent: Canonical, presentation-clean Data Transfer Object representing an incoming
 * Direct Client Partnership Request or Requisition Invitation.
 * Serves as an insulation layer between raw Elasticsearch hit sources and Blade view presenters.
 *
 * Tenancy: Scoped strictly to the authenticated company (to_company_id).
 */
class ClientRequestDTO
{
    /**
     * @param  int  $id  Staffing company invitation ID or primary request relation ID
     * @param  int  $clientId  Client entity ID
     * @param  string  $companyName  Direct Client Company Name
     * @param  string  $logoUrl  Client logo logical URL
     * @param  string  $initials  Fallback avatar initials
     * @param  string  $industry  Primary industry sector
     * @param  string  $employeeCount  Company employee size range
     * @param  string  $city  Primary location city
     * @param  string  $country  Primary location country
     * @param  string  $requestedAt  Raw ISO-8601 creation/received timestamp
     * @param  string|null  $expiresAt  Raw ISO-8601 SLA expiration timestamp
     * @param  bool  $isPendingRequest  Whether this request is currently awaiting recruiter action (status 0)
     * @param  string  $placementFee  Headline commercial fee percentage or terms
     * @param  int  $requisitionCount  Count of shared active requisitions attached to this request
     * @param  array  $permissions  Action permissions (can_accept, can_decline, can_view_terms)
     * @param  array  $uiMetadata  UI visual metadata (priority, status_label, status_color, action_state)
     * @param  int  $entityVersion  Authoritative transactional entity version from MySQL
     * @param  int  $projectionVersion  Current Elasticsearch projection version
     */
    public function __construct(
        public int $id,
        public int $clientId,
        public string $companyName,
        public string $logoUrl,
        public string $initials,
        public string $industry,
        public string $employeeCount,
        public string $city,
        public string $country,
        public string $requestedAt,
        public ?string $expiresAt,
        public bool $isPendingRequest,
        public string $placementFee,
        public int $requisitionCount,
        public array $permissions,
        public array $uiMetadata,
        public int $entityVersion,
        public int $projectionVersion
    ) {}

    /**
     * Factory method: Hydrates a canonical DTO from an Elasticsearch search hit.
     *
     * @param  array  $hit  Elasticsearch raw hit array
     */
    public static function fromElasticHit(array $hit): self
    {
        $source = $hit['_source'] ?? $hit;

        $createdAt = (string) ($source['from_created_at'] ?? ($source['created_at'] ?? now()->toIso8601String()));
        $expiresAt = ! empty($source['expires_at'])
            ? (string) $source['expires_at']
            : date('Y-m-d H:i:s', strtotime($createdAt.' +7 days'));

        $status = (int) ($source['to_invitation_status'] ?? ($source['final_status'] ?? 0));
        $isPending = ($status === 0);

        return new self(
            id: (int) ($source['staffing_company_invitation_id'] ?? ($source['id'] ?? ($hit['_id'] ?? 0))),
            clientId: (int) ($source['client_id'] ?? 0),
            companyName: ucwords((string) ($source['company_name'] ?? 'Client')),
            logoUrl: (string) ($source['logo'] ?? ''),
            initials: (string) ($source['initials'] ?? 'CL'),
            industry: (string) ($source['ind_name'] ?? ($source['industry'] ?? '')),
            employeeCount: (string) ($source['company_size'] ?? ''),
            city: (string) ($source['city_name'] ?? ($source['city'] ?? '')),
            country: (string) ($source['country_name'] ?? ($source['country'] ?? '')),
            requestedAt: $createdAt,
            expiresAt: $expiresAt,
            isPendingRequest: $isPending,
            placementFee: (string) ($source['commercial_terms']['placement_fee'] ?? '8.33%'),
            requisitionCount: (int) ($source['requisition_count'] ?? ($source['job_context']['requisition_count'] ?? 0)),
            permissions: $source['permissions'] ?? [
                'can_accept' => true,
                'can_decline' => true,
                'can_view_terms' => true,
            ],
            uiMetadata: $source['ui'] ?? [
                'priority' => 'high',
                'status_label' => $isPending ? 'Pending Invitation' : 'Active',
                'status_color' => $isPending ? 'warning' : 'success',
                'action_state' => $isPending ? 'action_required' : 'resolved',
            ],
            entityVersion: (int) ($source['versioning']['entity_version'] ?? ($source['data_version'] ?? 1)),
            projectionVersion: (int) ($source['versioning']['projection_version'] ?? 1)
        );
    }

    /**
     * Serializes the DTO into a standard associative array for JSON or View passing.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'client_id' => $this->clientId,
            'company_name' => $this->companyName,
            'logo_url' => $this->logoUrl,
            'initials' => $this->initials,
            'industry' => $this->industry,
            'employee_count' => $this->employeeCount,
            'city' => $this->city,
            'country' => $this->country,
            'requested_at' => $this->requestedAt,
            'expires_at' => $this->expiresAt,
            'is_pending_request' => $this->isPendingRequest,
            'placement_fee' => $this->placementFee,
            'requisition_count' => $this->requisitionCount,
            'permissions' => $this->permissions,
            'ui' => $this->uiMetadata,
            'entity_version' => $this->entityVersion,
            'projection_version' => $this->projectionVersion,
        ];
    }
}
