<?php

namespace App\Services\OwnershipEngine\Rules;

/**
 * ER8 — docs/OWNERSHIP_ENGINE_DESIGN.md §2: "Among N competing contenders, exactly one is
 * ever eligible to be owner (the earliest per ER2/ER3); every other contender that meets
 * ER5/ER6's own visibility condition is Duplicate against that one owner — this
 * generalizes the two-contender rules to arbitrary N without a new rule." Master Tracker:
 * S13, S20, S26, S30 (three-way and more).
 *
 * Deliberately NOT a separate decision branch: OwnershipEngine's single "does an owner
 * already exist?" check (ER2/ER3) and single "am I the owner?" check (ER5/ER6) already
 * apply uniformly no matter how many prior contenders a cluster has accumulated — there is
 * nothing here to implement beyond what those rules already do. This class exists purely so
 * the traceability matrix (§3) has a named location for scenarios that specifically exercise
 * three-or-more contenders, and to provide a human-readable explanation fragment for
 * OwnershipEngine::explain() when a cluster has more than two members.
 */
class ER8_MultiContenderGeneralization
{
    public function describe(int $contenderCount): string
    {
        if ($contenderCount <= 2) {
            return '';
        }

        return "This cluster has {$contenderCount} contenders. No special-casing exists for ".
            'N > 2 — ER2/ER3 (who may become owner) and ER5/ER6 (who is flagged as duplicate) '.
            'apply identically regardless of contender count.';
    }
}
