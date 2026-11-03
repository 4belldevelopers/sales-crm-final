<?php

namespace App\Services\Contracts;

use App\Models\User;

/**
 * TenantMembershipResolverInterface
 *
 * Provides the forward-compatible architectural bridge between Model A
 * (Phase 1-3 Single Tenant constraint) and Model B (Phase 4+ Federated Multi-Tenant Memberships).
 */
interface TenantMembershipResolverInterface
{
    /**
     * Attaches an activated user to a tenant organization with the designated role.
     */
    public function attachUserToTenant(User $user, int $tenantId, int $roleId): void;
}
