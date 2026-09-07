<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * V2_login Phase 5.7 — global security response headers.
 *
 * Applied to every response. CSP is intentionally omitted here because Metronic
 * uses pervasive inline styles/scripts — a restrictive policy would break the UI.
 * Add per-route CSP headers in a dedicated middleware once the Metronic upgrade
 * removes inline-script dependencies.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // The authenticated salary editor is deliberately rendered in the account page's same-origin drawer.
        // Keep all other responses unframeable; this exception does not allow cross-origin embedding.
        $isEmbeddedSalaryEditor = $request->boolean('embed')
            && $request->routeIs('service_company.account.offerAmountChange');

        $response->headers->set('X-Frame-Options', $isEmbeddedSalaryEditor ? 'SAMEORIGIN' : 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

        return $response;
    }
}
