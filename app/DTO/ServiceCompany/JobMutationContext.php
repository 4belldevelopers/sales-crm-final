<?php

namespace App\DTO\ServiceCompany;

/**
 * JobMutationContext
 *
 * Immutable DTO enforcing strict separation between tenantId and serviceCompanyId
 * on write mutation paths (Rule: Tenant != Service Company).
 */
class JobMutationContext
{
    public function __construct(
        public readonly int $tenantId,                  // Top-level account/billing boundary
        public readonly int $serviceCompanyId,          // Operating recruitment agency
        public readonly int $userId,                    // Acting authenticated user
        public readonly int $clientId,                  // Target client ID
        public readonly string $clientType = 'internal', // 'internal' | 'direct'
        public readonly ?int $partnerCompanyId = null    // Validated partner company for direct client
    ) {}
}
