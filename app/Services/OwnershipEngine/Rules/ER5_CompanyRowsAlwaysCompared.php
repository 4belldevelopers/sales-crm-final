<?php

namespace App\Services\OwnershipEngine\Rules;

/**
 * ER5 — docs/OWNERSHIP_ENGINE_DESIGN.md §2: "A Company Recruiter row is compared against
 * the current owner immediately upon creation, regardless of its own stage — never
 * privacy-shielded." Master Tracker: S15-S18, S23-S26 (the company row is shown as
 * Duplicate while still described only as "Added ... InReview").
 */
class ER5_CompanyRowsAlwaysCompared
{
    public function isDuplicateAgainstOwner(int $ownerRowId, int $thisRowId): bool
    {
        return $ownerRowId !== $thisRowId;
    }
}
