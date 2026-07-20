<?php

namespace App\Services\OwnershipEngine\Rules;

/**
 * ER2 — docs/OWNERSHIP_ENGINE_DESIGN.md §2: "A Company Recruiter row reserves ownership at
 * the instant of creation IF no agency has established ownership before that instant."
 * Master Tracker: S8, S9-S14, S19-S22, S27-S30.
 *
 * Checked at candidate-creation time only (OwnershipEngine::attemptCompanyReservation()) —
 * a company row's later stage progress never re-triggers this rule (ER4: ownership never
 * flips once decided).
 */
class ER2_CompanyReservation
{
    public function wouldEstablishOwnership(?int $existingOwnerRowId): bool
    {
        return $existingOwnerRowId === null;
    }
}
