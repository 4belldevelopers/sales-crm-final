<?php

namespace App\Services\OwnershipEngine\Rules;

/**
 * ER1 — docs/OWNERSHIP_ENGINE_DESIGN.md §2: "An agency row that has never reached Sourcing
 * contributes nothing to ownership and is never itself flagged." Master Tracker: S1, S2.
 *
 * This rule is primarily enforced by WHEN the engine is called at all (Phase 3's trigger
 * integration must never call claimForAgencySourcing() before a row actually reaches
 * Sourcing, and never calls it at all for a row that stays at InReview). This class is the
 * defense-in-depth guard against a caller wiring bug, not the sole enforcement mechanism.
 */
class ER1_PrivateUntilSourced
{
    /**
     * @throws \LogicException if an agency row is evaluated without having reached Sourcing.
     */
    public function assertMayEvaluate(bool $isCompanyRow, bool $hasReachedSourcing, int $rowId): void
    {
        if (!$isCompanyRow && !$hasReachedSourcing) {
            throw new \LogicException(
                "ER1 violation: agency row #{$rowId} was evaluated before reaching Sourcing. ".
                'Agency rows are private until they themselves reach Sourcing — see '.
                'docs/OWNERSHIP_ENGINE_DESIGN.md §2 (ER1) and Master Tracker S1/S2.'
            );
        }
    }
}
