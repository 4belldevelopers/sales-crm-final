<?php

namespace App\Support;

use App\Helper\GeneralMethod;
use App\Models\User;

/**
 * Single source of truth for all post-login and already-authenticated redirect URLs.
 *
 * All three company types (Staffing=1, Service=2, Product=3) share 'service_company/dashboard'.
 * Phase 1 will replace profileCompletionRow()'s raw DB query with a TenantLookup Redis cache.
 */
class TenantRouter
{
    /** Post-login dashboard URL. Super-admins route to superadmin.dashboard.index. */
    public static function dashboardUrl(User $user): string
    {
        if ($user->is_superadmin) {
            return route('superadmin.dashboard.index');
        }

        return 'service_company/dashboard';
    }

    /** Profile-edit URL used by the first-login remember_token flow. */
    public static function profileEditUrl(int $userId): string
    {
        return "service_company/profile/{$userId}/edit";
    }

    /** Account-edit URL for companies with incomplete profiles. */
    public static function accountEditUrl(int $userId): string
    {
        return 'service_company/account/' . GeneralMethod::value_encrypt($userId) . '/edit';
    }

    /**
     * Returns the company's profile-completion row via TenantLookup (Redis-cached, 1h TTL).
     * Returns null if the row is missing or any required field is empty — signals account-edit redirect.
     */
    public static function profileCompletionRow(int $companyId): ?\stdClass
    {
        return TenantLookup::profileCompletion($companyId);
    }
}
