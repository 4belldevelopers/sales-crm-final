<?php

namespace App\Services\OwnershipEngine\Rules;

/**
 * ER4 — docs/OWNERSHIP_ENGINE_DESIGN.md §2: "Once an owner is set for an identity+scope
 * cluster, it never changes, regardless of what any other row does afterward." Master
 * Tracker: S6, S7, S10-S12, S16-S18, S21, S22, S24, S25, S30.
 *
 * The real enforcement of this rule is structural: OwnershipEngine's write path only ever
 * sets `ownership_clusters.owner_row_id` inside the branch where it was previously null
 * (see OwnershipEngine::evaluate()) — there is no code path that overwrites a non-null
 * value. This class is a self-check invariant guard against a future change accidentally
 * introducing one, not the primary mechanism.
 */
class ER4_OwnershipNeverFlips
{
    /**
     * @throws \LogicException if called where an owner is already set — the caller should
     * never reach this assertion in that state; if it does, something upstream tried to
     * overwrite a locked-in owner.
     */
    public function assertNotOverwritingOwner(?int $existingOwnerRowId, int $clusterId): void
    {
        if ($existingOwnerRowId !== null) {
            throw new \LogicException(
                "ER4 violation: attempted to overwrite an already-decided owner (row ".
                "#{$existingOwnerRowId}) on cluster #{$clusterId}. Ownership never flips — ".
                'see docs/OWNERSHIP_ENGINE_DESIGN.md §2 (ER4).'
            );
        }
    }
}
