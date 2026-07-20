<?php

namespace App\Services\OwnershipEngine\Rules;

/**
 * ER6 — docs/OWNERSHIP_ENGINE_DESIGN.md §2: "An agency row is only compared/flagged once
 * IT ITSELF reaches Sourcing — never before, no matter who else already owns the
 * identity." Master Tracker: S9-S13, S19-S20, S26, S28-S30 (Agency A/B only listed under
 * "Duplicate Shown To" once their own stage is Sourcing+).
 *
 * In practice `$hasReachedSourcing` is always true here, since
 * OwnershipEngine::claimForAgencySourcing() is only ever invoked at the moment a row
 * reaches Sourcing (ER1 already guards this upstream) — accepted explicitly anyway so this
 * rule is honestly testable in isolation rather than hard-coding the assumption.
 */
class ER6_AgencyRowsPrivacyShieldedUntilSourced
{
    public function isDuplicateAgainstOwner(int $ownerRowId, int $thisRowId, bool $hasReachedSourcing): bool
    {
        return $hasReachedSourcing && $ownerRowId !== $thisRowId;
    }
}
