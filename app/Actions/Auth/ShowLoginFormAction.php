<?php

namespace App\Actions\Auth;

use App\Support\TenantRouter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

/**
 * Resolves the already-authenticated redirect on the login-form GET route.
 * Returns null when the visitor is a guest (controller shows the form).
 * Returns a RedirectResponse when the user is already logged in.
 */
class ShowLoginFormAction
{
    public function redirectIfAuthenticated(): ?RedirectResponse
    {
        if (! auth()->check()) {
            return null;
        }

        $user   = auth()->user();
        $userId = $user->id;

        if ($user->is_superadmin) {
            return redirect(route('superadmin.dashboard.index'));
        }

        // First-login via team invite: remember_token is set to the invited user's ID.
        if ($user->remember_token > 0) {
            DB::table('users')->where('id', $userId)->limit(1)->update(['remember_token' => '']);
            return redirect(TenantRouter::profileEditUrl($userId));
        }

        // Post-verification onboarding: if company has not yet set their organization name, route to clean Onboarding Hub
        $company = DB::table('companies')->where('id', $user->company_id)->select('company_name')->first();
        if ($company && (empty($company->company_name) || $company->company_name === 'Pending Onboarding')) {
            return redirect(url('/service_company/account/onboarding'));
        }

        return redirect(TenantRouter::dashboardUrl($user));
    }
}
