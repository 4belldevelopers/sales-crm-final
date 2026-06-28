<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                
                $user->super_admin_login_code = '';
                $user->save();
                 
             }else{

                if ($user->status !== 'active') {
                    Auth::logout();
                    return redirect('/login')->withErrors([
                        'device_message' => 'Your account has been suspended. To resolve this issue, please contact the administrator or send an email to support@jero.works for further assistance.'
                    ]);
                }

                $currentSessionId = session()->getId();
                // Check if the session ID stored for the user matches the current session ID
                if ($user->session_id && $user->session_id !== session()->getId()) {
                        // Logout the user if the session IDs do not match
                        // Auth::logout();
                        session()->getHandler()->destroy($user->session_id);

                        // Optionally, redirect them to the login page with a message
                        // return redirect('/login')->withErrors([
                        //     'device_message' => 'You have been logged out because your account was accessed from another device.'
                        // ]);
                    }

                    // Save the current session ID in the database
                    $user->session_id = $currentSessionId;
                    $user->save();

             }
            


            

        }

        return $next($request);
    }
}
