<?php

namespace App\Services\Analytics\DTO;

/**
 * Class PerformanceScopeContract
 *
 * Intent: Immutable canonical DTO encapsulating the authorized multi-tenant scope
 * for the Client Performance Analytics Engine.
 * Enforces the invariant: No downstream metric, aggregation query, or visualization
 * may construct its own tenancy scope. All metrics inherit this identical root scope.
 *
 * (c) 2026 JERO.WORKS. All rights reserved.
 */
class PerformanceScopeContract
{
    public function __construct(
        public readonly int $clientId,
        public readonly int $agencyCompanyId,
        public readonly string $clientType,             // 'direct' | 'internal'
        public readonly string $clientName,             // Resolved client name
        public readonly string $relationshipType,       // 'MARKETPLACE_SHARED' | 'TENANT_OWNED'
        public readonly array $sharedJobIds,            // Canonical list of job IDs shared with this agency
        public readonly array $activeSharedJobIds,      // Subset of sharedJobIds with status 'active'|'hiring'
        public readonly bool $activeRelationshipOnly,   // final_status = 1
        public readonly string $range,                  // '30D', '3M', '6M', '12M', 'all'
        public readonly string $compareWith,            // 'previous_period', 'previous_year', 'none'
        public readonly string $timezone,               // Tenant timezone
        public readonly array $dateBoundaries,          // [current_start, current_end, previous_start, previous_end, days]
        public readonly string $theme,                  // 'emerald' (direct) | 'blue' (internal)
        public readonly string $themeColor,             // '#10b981' | '#009ef7'
        public readonly array $indices,                 // [candidates, jobs, interviews, timeline, clients]
        public readonly string $correlationId,          // Correlation UUID
        public readonly int $scopeVersion = 1           // Cache scope generation version
    ) {}

    /**
     * Check if a given job ID is within the authorized shared job scope.
     */
    public function hasJob(int $jobId): bool
    {
        return in_array($jobId, $this->sharedJobIds, true);
    }

    /**
     * Convert DTO to array format for backward compatibility where required.
     */
    public function toArray(): array
    {
        return [
            'client_id'                => $this->clientId,
            'client_name'              => $this->clientName,
            'client_type'              => $this->clientType,
            'agency_company_id'        => $this->agencyCompanyId,
            'auth_company_id'          => $this->agencyCompanyId,
            'relationship_type'        => $this->relationshipType,
            'shared_job_ids'           => $this->sharedJobIds,
            'active_shared_job_ids'    => $this->activeSharedJobIds,
            'active_relationship_only' => $this->activeRelationshipOnly,
            'range'                    => $this->range,
            'compare'                  => $this->compareWith,
            'timezone'                 => $this->timezone,
            'date_boundaries'          => $this->dateBoundaries,
            'theme'                    => $this->theme,
            'theme_color'              => $this->themeColor,
            'indices'                  => $this->indices,
            'correlation_id'           => $this->correlationId,
            'scope_version'            => $this->scopeVersion,
        ];
    }
}
