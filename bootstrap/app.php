<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Auth\Middleware\Authorize;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Routing\Middleware\ThrottleRequests;
use App\Http\Middleware\SuperAdmin;
use App\Http\Middleware\Package;
use App\Http\Middleware\EnsureOneDeviceLogin;
use App\Http\Middleware\CustomPackage;

use Illuminate\Session\TokenMismatchException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
            $middleware->alias([
                'auth'             => Authenticate::class,
                'auth.basic'       => AuthenticateWithBasicAuth::class,
                'bindings'         => SubstituteBindings::class,
                'cache.headers'    => SetCacheHeaders::class,
                'can'              => Authorize::class,
                'guest'            => RedirectIfAuthenticated::class,
                'signed'           => ValidateSignature::class,
                'throttle'         => ThrottleRequests::class,
                'super-admin'      => SuperAdmin::class,
                'EnsureOneDeviceLogin'      => EnsureOneDeviceLogin::class,
                'active-package'   => Package::class,
                'custom-package'   => CustomPackage::class,
                'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
                'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
                'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
                'recruiter.throttle' => \App\Http\Middleware\RecruiterThrottle::class,
                'recruiter.sliding'  => \App\Http\Middleware\RecruiterSlidingWindowThrottle::class,
            ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        /**
         * Intent: Catch CSRF token mismatch / session expiration errors (HTTP 419)
         * and gracefully redirect the user to the login screen with a notification
         * instead of displaying a raw 419 error page.
         */
        $exceptions->render(function (TokenMismatchException $e, $request) {
            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
        });
    })->create();
