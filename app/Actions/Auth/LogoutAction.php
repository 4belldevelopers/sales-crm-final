<?php

namespace App\Actions\Auth;

use App\Support\DeviceSessionManager;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Handles user logout: clears session_id, invalidates the session, redirects to login.
 */
class LogoutAction
{
    public function execute(Request $request): RedirectResponse
    {
        $user = auth()->user();
        auth()->logout();

        if ($user) {
            DeviceSessionManager::invalidate($user->id);
            $user->session_id = null;
            $user->save();
        }

        $request->session()->invalidate();

        if (module_enabled('Subdomain')) {
            if ($user && $user->is_superadmin) {
                return redirect(route('front.super-admin-login'));
            }

            return redirect(route('login'));
        }

        return redirect(route('login'));
    }
}
