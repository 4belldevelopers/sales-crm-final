<?php

namespace App\Http\Middleware;

use App\Support\DeviceSessionManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * V2_login Phase 3 — per-request session enforcement (Redis-only, no MySQL writes).
 *
 * Guards applied in order:
 *   1. Super-admin token_check bypass (throttled super_admin_login_code clear).
 *   2. Account status check (active only).
 *   3. Epoch gate (Phase 3.4): kicks sessions created before the latest block/suspend action.
 *   4. Single-device check (Phase 3.1): 1 Redis GET, no MySQL write per request.
 *
 * Phase 0 removed the MySQL session_id write-per-request (BUG-01).
 * Phase 3.1 replaces the remaining MySQL write with a Redis key set only on login.
 */
class EnsureOneDeviceLogin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // 1. Super-admin impersonation: throttle-clear the super_admin_login_code.
        if (session()->has('token_check')) {
            $throttleKey = 'token_check_save_'.$user->id;
            if (! Cache::has($throttleKey)) {
                $user->super_admin_login_code = '';
                $user->save();
                Cache::put($throttleKey, true, 60);
            }

            return $next($request);
        }

        // 2. Account status.
        if ($user->status !== 'active') {
            Auth::logout();

            return redirect('/login')->withErrors([
                'device_message' => 'Your account has been suspended. To resolve this issue, please contact the administrator or send an email to support@jero.works for further assistance.',
            ]);
        }

        // 3. Epoch gate: invalidates all sessions born before the latest block/suspend action.
        //    login_at is written to session by AttemptLoginAction on every successful login.
        $loginAt = (int) session('login_at', 0);
        if ($loginAt > 0) {
            $userEpoch = (int) Cache::get('user:epoch:'.$user->id, 0);
            $companyEpoch = (int) Cache::get('company:epoch:'.$user->company_id, 0);
            if ($loginAt < $userEpoch || $loginAt < $companyEpoch) {
                Auth::logout();

                return redirect('/login')->withErrors([
                    'device_message' => 'Your session has been terminated by an administrator.',
                ]);
            }
        }

        // 4. Single-device enforcement (1 Redis GET, no MySQL write).
        $currentSessionId = session()->getId();
        if (! DeviceSessionManager::enforce($user->id, $currentSessionId, $user->email ?? null)) {
            Auth::logout();
            if ($request->expectsJson() || $request->ajax() || $request->isXmlHttpRequest()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'You have been logged in from another device. Please login again.',
                    'redirect' => url('/login'),
                ], 401);
            }

            return redirect('/login')->withErrors([
                'device_message' => 'You have been logged in from another device. Please login again.',
            ]);
        }

        return $next($request);
    }
}
