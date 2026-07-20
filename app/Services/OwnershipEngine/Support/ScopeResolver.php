<?php

namespace App\Services\OwnershipEngine\Support;

use Illuminate\Support\Facades\DB;

/**
 * Resolves the identity+scope cluster key. docs/OWNERSHIP_ENGINE_DESIGN.md §1 hierarchy
 * rule 3 / §7 / ER7: scope reuses the scoping company's OWN `app_duplicate` setting rather
 * than a separate mode — 1 = Job (scope_key = job_id), 2 = Company Overall
 * (scope_key = scoping_company_id itself).
 *
 * §24: Company Overall mode's live path needs no `jobs` join at all — the scope key is the
 * company id directly. The `jobs.company_id` join some earlier revisions of this design
 * discussed only ever applies to the queued rebuild path (Phase 4), never here.
 */
class ScopeResolver
{
    public const MODE_JOB = 1;
    public const MODE_COMPANY_OVERALL = 2;

    /**
     * Finding 3 (docs/OWNERSHIP_ENGINE_DESIGN.md): app_duplicate = 0 means this company has
     * not opted into duplicate detection at all — the engine must not evaluate any of its
     * rows. Checked once here (the single source of truth, principle #3), not replicated as
     * a guard at every call site.
     */
    public function isEnabled(int $scopingCompanyId): bool
    {
        $mode = (int) (DB::table('companies')->where('id', $scopingCompanyId)->value('app_duplicate') ?? 0);

        return in_array($mode, [self::MODE_JOB, self::MODE_COMPANY_OVERALL], true);
    }

    /**
     * @return array{0: int, 1: int} [$scopeMode, $scopeKey]
     */
    public function resolve(int $scopingCompanyId, int $jobId): array
    {
        $mode = (int) (DB::table('companies')->where('id', $scopingCompanyId)->value('app_duplicate') ?? 0);

        // Callers must check isEnabled() first — by the time resolve() runs, mode should
        // always be 1 or 2. This fallback exists only so resolve() never crashes if called
        // out of order; it is not an expected live path.
        $scopeMode = in_array($mode, [self::MODE_JOB, self::MODE_COMPANY_OVERALL], true)
            ? $mode
            : self::MODE_JOB;

        $scopeKey = $scopeMode === self::MODE_COMPANY_OVERALL ? $scopingCompanyId : $jobId;

        return [$scopeMode, $scopeKey];
    }
}
