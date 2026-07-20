<?php

namespace App\Services\OwnershipEngine\Rules;

use App\Services\OwnershipEngine\Support\ScopeResolver;

/**
 * ER7 — docs/OWNERSHIP_ENGINE_DESIGN.md §2: "Scope determines the comparison universe: Job
 * mode compares within one job; Company Overall mode compares across every eligible job the
 * scoping company owns, as ONE cluster, not per-job islands." Master Tracker: S19-S30 vs.
 * S1-S18.
 *
 * Named rule entry point for traceability (§3); the actual lookup lives in ScopeResolver
 * (§7/§24) so the query logic isn't duplicated between the two classes.
 */
class ER7_ScopeDeterminesComparisonUniverse
{
    public function __construct(private readonly ScopeResolver $scopeResolver)
    {
    }

    /**
     * @return array{0: int, 1: int} [$scopeMode, $scopeKey]
     */
    public function resolve(int $scopingCompanyId, int $jobId): array
    {
        return $this->scopeResolver->resolve($scopingCompanyId, $jobId);
    }

    /**
     * Finding 3 (docs/OWNERSHIP_ENGINE_DESIGN.md): app_duplicate = 0 means the scoping
     * company has not opted into duplicate detection — the engine must not evaluate at all.
     */
    public function isEnabled(int $scopingCompanyId): bool
    {
        return $this->scopeResolver->isEnabled($scopingCompanyId);
    }
}
