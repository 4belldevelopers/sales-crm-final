<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyCsrfToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
     protected $except = [
        '/save-invoices',
       //'/verify-webhook',
        '/save-razorpay-invoices'
    ];
    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);
    // }
}
