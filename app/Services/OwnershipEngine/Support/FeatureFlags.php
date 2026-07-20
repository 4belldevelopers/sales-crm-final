<?php

namespace App\Services\OwnershipEngine\Support;

use Illuminate\Support\Facades\DB;

/**
 * Ownership Engine Phase 8 (Activation Readiness) — docs/OWNERSHIP_ENGINE_DESIGN.md,
 * Finding 7 (supersedes Finding 6). isAuthoritative() does NOT gate live event processing —
 * attemptCompanyReservation()/claimForAgencySourcing() run unconditionally (subject only to
 * ScopeResolver::isEnabled()'s unrelated app_duplicate gate, Finding 3) so the engine keeps
 * building real ownership_events/ownership_clusters/cache history in production throughout
 * the observation period. This method is reserved for the not-yet-built cutover mechanism
 * (Phase 7's original scope): "is the engine authoritative for this scope" — currently
 * checked by nothing, genuinely inert until that mechanism exists.
 *
 * Precedence, most specific wins: kill switch (unconditional) > job-level override >
 * company-level override > global default. The global default is false — legacy stays
 * authoritative until cutover is explicitly approved.
 */
class FeatureFlags
{
    public function isAuthoritative(int $companyId, ?int $jobId = null): bool
    {
        $settings = DB::table('ownership_engine_settings')->where('id', 1)->first();

        if (!$settings || $settings->kill_switch_active) {
            return false;
        }

        if ($jobId !== null) {
            $jobOverride = DB::table('jobs')->where('id', $jobId)->value('ownership_engine_enabled');
            if ($jobOverride !== null) {
                return (bool) $jobOverride;
            }
        }

        $companyOverride = DB::table('companies')->where('id', $companyId)->value('ownership_engine_enabled');
        if ($companyOverride !== null) {
            return (bool) $companyOverride;
        }

        return (bool) $settings->globally_enabled;
    }

    /**
     * Finding 7: the kill switch now stops live processing directly — checked at the top of
     * attemptCompanyReservation()/claimForAgencySourcing(), unconditionally — as well as
     * backfillCandidate(). The single, immediate "stop everything the engine does" control.
     */
    public function isKillSwitchActive(): bool
    {
        return (bool) DB::table('ownership_engine_settings')->where('id', 1)->value('kill_switch_active');
    }
}
