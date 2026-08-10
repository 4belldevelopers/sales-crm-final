<?php

namespace App\Http\Middleware;

use App\Support\TenantRouter;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

/**
 * Redirects already-authenticated visitors away from guest-only routes.
 * Uses TenantRouter as the single source of truth for redirect URLs (Phase 2.3).
 */
class RedirectIfAuthenticated
{
    public function handle($request, Closure $next, $guard = null): Response
    {
        if (! Auth::guard($guard)->check()) {
            return $next($request);
        }

        $user   = auth()->user();
        $userId = $user->id;

        // First-login via team invite: remember_token equals the user's own ID.
        if ($userId == $user->remember_token) {
            DB::table('users')->where('id', $userId)->limit(1)->update(['remember_token' => '']);
            return redirect(TenantRouter::profileEditUrl($userId));
        }

        if ($user->is_superadmin) {
            return redirect(route('superadmin.dashboard.index'));
        }

        if ($user->hasRole('employee')) {
            return redirect(route('member.dashboard'));
        }

        return redirect(TenantRouter::dashboardUrl($user));
    }
}
