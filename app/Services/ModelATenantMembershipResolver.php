<?php

namespace App\Services;

use App\Models\User;
use App\Services\Contracts\TenantMembershipResolverInterface;
use Illuminate\Support\Facades\DB;

/**
 * ModelATenantMembershipResolver
 *
 * Implements Model A deliberate phase constraint: single tenant per user row (`users.company_id`).
 * Protects 217 tables from premature multi-tenant schema mutations while providing a clean
 * forward abstraction for Phase 4+ federation.
 */
class ModelATenantMembershipResolver implements TenantMembershipResolverInterface
{
    public function attachUserToTenant(User $user, int $tenantId, int $roleId): void
    {
        $user->company_id = $tenantId;
        $user->save();

        if ($roleId > 0) {
            DB::table('role_user')->updateOrInsert(
                ['user_id' => $user->id, 'role_id' => $roleId],
                ['role_id' => $roleId]
            );
        }
    }
}
