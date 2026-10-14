<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

/**
 * ResolveTaxonomyLineageMiddleware
 *
 * INTENT: Resolves incoming job and candidate designations to canonical taxonomy entities.
 * Preserves recruiter creative freedom by capturing raw strings in raw_designation while
 * resolving canonical_designation_id and taxonomy_version for deterministic talent matching.
 *
 * TENANCY NOTE: Canonical taxonomy is a global read-only catalog; candidate and job writes
 * remain strictly scoped to the tenant's company_id.
 */
class ResolveTaxonomyLineageMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only process mutations (POST, PUT, PATCH)
        if (! in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            return $next($request);
        }

        // Determine input title/designation
        $rawTitle = $request->input('raw_designation') 
            ?? $request->input('title') 
            ?? $request->input('current_designation');

        if (! $rawTitle || ! is_string($rawTitle)) {
            return $next($request);
        }

        $rawTitle = trim($rawTitle);
        $clientVersion = $request->input('taxonomy_version');
        $minVersion = config('taxonomy.minimum_supported_version', '2.0.0');
        $currentVersion = config('taxonomy.current_version', '2.0.0');

        // Check version compatibility if client provided a version
        if ($clientVersion && version_compare($clientVersion, $minVersion, '<')) {
            return response()->json([
                'status' => 'error',
                'error_code' => 'TAXONOMY_VERSION_OUTDATED',
                'message' => 'Your client taxonomy version is outdated. Please refresh your browser to load the latest catalog.',
            ], 409, ['X-Taxonomy-Refresh-Required' => 'true']);
        }

        $canonicalId = $request->input('canonical_designation_id');
        $resolvedId = null;

        // 1. Verify directly supplied canonical_designation_id
        if (! empty($canonicalId) && is_numeric($canonicalId)) {
            $canonicalId = (int) $canonicalId;
            $exists = Cache::remember('canonical_desig_exists_' . $canonicalId, 3600, function () use ($canonicalId) {
                return DB::table('taxonomy_designations')
                    ->where('id', $canonicalId)
                    ->where('status', 1)
                    ->exists();
            });

            if ($exists) {
                $resolvedId = $canonicalId;
            }
        }

        // 2. If canonicalId not supplied or invalid, resolve via deterministic lookup
        if (! $resolvedId) {
            $resolvedId = $this->resolveCanonicalByText($rawTitle);
        }

        // Inject resolved taxonomy lineage into request inputs
        $request->merge([
            'raw_designation' => $rawTitle,
            'canonical_designation_id' => $resolvedId,
            'taxonomy_version' => $clientVersion ?: $currentVersion,
        ]);

        return $next($request);
    }

    /**
     * Deterministic text-to-canonical resolution via normalized keys and token overlap
     */
    protected function resolveCanonicalByText(string $rawText): ?int
    {
        $normalizedKey = preg_replace('/[^a-z0-9]/', '', strtolower($rawText));
        if (strlen($normalizedKey) < 2) {
            return null;
        }

        // 1. Check exact search_normalized_key on active designations
        $match = DB::table('taxonomy_designations')
            ->where('search_normalized_key', $normalizedKey)
            ->where('status', 1)
            ->value('id');

        if ($match) {
            return (int) $match;
        }

        // 2. Check commercial aliases
        $aliasMatch = DB::table('taxonomy_aliases')
            ->where('search_normalized_key', $normalizedKey)
            ->where('status', 1)
            ->value('canonical_designation_id');

        if ($aliasMatch) {
            return (int) $aliasMatch;
        }

        // 3. Multi-token partial match for composite phrasing
        $tokens = preg_split('/[\s\-_,\.\/]+/', strtolower($rawText));
        $tokens = array_filter($tokens, fn ($t) => strlen($t) >= 3);

        if (count($tokens) >= 1) {
            $query = DB::table('taxonomy_designations')
                ->where('status', 1);

            foreach ($tokens as $tok) {
                $cleanTok = preg_replace('/[^a-z0-9]/', '', $tok);
                if (strlen($cleanTok) >= 3) {
                    $query->where('search_normalized_key', 'LIKE', '%' . $cleanTok . '%');
                }
            }

            $first = $query->first(['id', 'canonical_title']);
            if ($first) {
                return (int) $first->id;
            }
        }

        return null;
    }
}
