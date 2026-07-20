<?php

namespace App\Services\OwnershipEngine\Rules;

/**
 * ER3 — docs/OWNERSHIP_ENGINE_DESIGN.md §2: "Absent a valid Company Reservation, the
 * earliest agency row to reach Sourcing becomes owner." Master Tracker: S3, S4, S5, S15,
 * S23, S26.
 *
 * Checked only at the InReview(8)->Sourcing(1) transition
 * (OwnershipEngine::claimForAgencySourcing()). Mechanically identical check to ER2 (both
 * ask "does no owner exist yet?") but kept as a separate, named rule because it fires from
 * a genuinely different trigger event and a different row category — collapsing them into
 * one class would lose the Scenario->Rule traceability the Master Tracker requires (§3).
 */
class ER3_AgencyOwnershipByFirstSourced
{
    public function wouldClaimOwnership(?int $existingOwnerRowId): bool
    {
        return $existingOwnerRowId === null;
    }
}
