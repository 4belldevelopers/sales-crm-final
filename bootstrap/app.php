<?php

use App\Http\Middleware\CustomPackage;
use App\Http\Middleware\EnsureOneDeviceLogin;
use App\Http\Middleware\Package;
use App\Http\Middleware\RedirectIfAuthenticated;
use App\Http\Middleware\SecurityHeaders;
use App\Http\Middleware\SuperAdmin;
use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Auth\Middleware\AuthenticateWithBasicAuth;
use Illuminate\Auth\Middleware\Authorize;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\SetCacheHeaders;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Illuminate\Routing\Middleware\ValidateSignature;
use Illuminate\Session\TokenMismatchException;
use Symfony\Component\HttpKernel\Exception\HttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Phase 5.5: exempt Stripe + Razorpay webhook receivers from CSRF.
        // These endpoints are authenticated via provider signatures, not session tokens.
        $middleware->validateCsrfTokens(except: [
            '/save-invoices',
            '/save-razorpay-invoices',
            '*/auth-custom-package/validatePackage',
            '*/auth-custom-package/validateClientPackage',
        ]);

        // Phase 5.7: security headers on every response.
        $middleware->append(SecurityHeaders::class);

        $middleware->alias([
            'auth' => Authenticate::class,
            'auth.basic' => AuthenticateWithBasicAuth::class,
            'bindings' => SubstituteBindings::class,
            'cache.headers' => SetCacheHeaders::class,
            'can' => Authorize::class,
            'guest' => RedirectIfAuthenticated::class,
            'signed' => ValidateSignature::class,
            'throttle' => ThrottleRequests::class,
            'super-admin' => SuperAdmin::class,
            'EnsureOneDeviceLogin' => EnsureOneDeviceLogin::class,
            'active-package' => Package::class,
            'custom-package' => CustomPackage::class,
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
            'recruiter.throttle' => \App\Http\Middleware\RecruiterThrottle::class,
            'recruiter.sliding' => \App\Http\Middleware\RecruiterSlidingWindowThrottle::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->report(function (\Throwable $e) {
            if (app()->bound('sentry')) {
                app('sentry')->captureException($e);
            }
        });

        /**
         * Intent: Catch CSRF token mismatch / session expiration errors (HTTP 419)
         * and gracefully handle them: AJAX requests get a 419 JSON response so
         * JavaScript can show a user-friendly dialog; browser requests redirect
         * to login with an error flash.
         *
         * Note: Laravel's prepareException() converts TokenMismatchException →
         * HttpException(419) before render callbacks run, so we must catch
         * HttpException here, not TokenMismatchException.
         */
        $exceptions->render(function (HttpException $e, $request) {
            if ($e->getStatusCode() !== 419) {
                return null; // let other exceptions fall through
            }
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json(['message' => 'Your session has expired. Please reload the page.'], 419);
            }

            return redirect()->route('login')->with('error', 'Your session has expired. Please log in again.');
        });
    })->create();
