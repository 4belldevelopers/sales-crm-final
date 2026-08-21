<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * ConsultancyETag Middleware
 *
 * INTENT: Generates ETag response headers based on tenant company ID and cache version keys.
 * Handles HTTP 304 Not Modified short-circuiting when If-None-Match matches, eliminating
 * payload transfer and controller re-execution for cached requests.
 * TENANCY: Scoped by logged-in user company ID and user ID.
 */
class ConsultancyETag
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // ETag middleware applies to GET requests
        if (!$request->isMethod('GET')) {
            return $next($request);
        }

        $user = $request->user();
        if (!$user) {
            return $next($request);
        }

        $companyId = $user->company_id ?? 0;
        $userId = $user->id ?? 0;

        // Fetch version keys for tenant & user
        $companyVersion = Cache::get("ats:spartner_summary_cnt:comp_{$companyId}_v", 1);
        $userVersion = Cache::get("allconsultancy_version_user_{$userId}", 1);

        $etag = '"' . md5("consultancy_{$companyId}_{$userId}_{$companyVersion}_{$userVersion}_" . $request->fullUrl()) . '"';

        // Check If-None-Match header
        $clientEtag = $request->header('If-None-Match');
        if ($clientEtag && trim($clientEtag) === $etag) {
            return response('', 304, [
                'ETag' => $etag,
                'Cache-Control' => 'private, must-revalidate',
            ]);
        }

        /** @var Response $response */
        $response = $next($request);

        if ($response->getStatusCode() === 200) {
            $response->headers->set('ETag', $etag);
            $response->headers->set('Cache-Control', 'private, must-revalidate');
        }

        return $response;
    }
}
