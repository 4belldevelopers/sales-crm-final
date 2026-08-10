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

        // Incomplete company profile → account-edit before dashboard.
        if (TenantRouter::profileCompletionRow($user->company_id) === null) {
            return redirect(TenantRouter::accountEditUrl($userId));
        }

        return redirect(TenantRouter::dashboardUrl($user));
    }
}
