<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

class EnsureOneDeviceLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {

        

        if (Auth::check()) {

            $user = Auth::user();
            // $clientIp = $request->ip();
            // dd($request->session()->regenerate(),session()->getId(),$clientIp);
            

            if (Session::has('token_check')) {
                // Throttle: write super_admin_login_code clear at most once per minute per user
                $throttleKey = 'token_check_save_' . $user->id;
                if (!Cache::has($throttleKey)) {
                    $user->super_admin_login_code = '';
                    $user->save();
                    Cache::put($throttleKey, true, 60);
                }
            } else {

                if ($user->status !== 'active') {
                    Auth::logout();
                    return redirect('/login')->withErrors([
                        'device_message' => 'Your account has been suspended. To resolve this issue, please contact the administrator or send an email to support@jero.works for further assistance.'
                    ]);
                }

                $currentSessionId = session()->getId();
                if ($user->session_id && $user->session_id !== $currentSessionId) {
                    session()->getHandler()->destroy($user->session_id);
                }

                // Only write to DB when the session ID actually changes (BUG-01 fix)
                $cachedSessionId = Cache::get('user_session_' . $user->id);
                if ($cachedSessionId !== $currentSessionId) {
                    $user->session_id = $currentSessionId;
                    $user->save();
                    Cache::put('user_session_' . $user->id, $currentSessionId, 3600);
                }

            }
            


            

        }

        return $next($request);
    }
}
