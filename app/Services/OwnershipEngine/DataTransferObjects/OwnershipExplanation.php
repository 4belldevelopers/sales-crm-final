<?php

namespace App\Services\OwnershipEngine\DataTransferObjects;

/**
 * The structured answer behind OwnershipEngine::explain() — docs/OWNERSHIP_ENGINE_DESIGN.md
 * §11/§26. Built entirely from stored fields (the cluster row + the one decisive event),
 * never free text authored at decision time, so the wording of `reason` can be improved
 * later without the underlying facts changing.
 */
final class OwnershipExplanation
{
    public function __construct(
        public readonly int $rowId,
        public readonly ?int $state,
        public readonly ?int $ownerRowId,
        public readonly ?string $ownerType,
        public readonly ?string $decisionRule,
        public readonly ?string $scenarioRef,
        public readonly ?string $decidedAt,
        public readonly ?string $comparedAgainstTimestamp,
        public readonly string $reason,
    ) {
    }

    public function toArray(): array
    {
        return [
            'row_id' => $this->rowId,
            'state' => $this->state,
            'owner_row_id' => $this->ownerRowId,
            'owner_type' => $this->ownerType,
            'decision_rule' => $this->decisionRule,
            'scenario_ref' => $this->scenarioRef,
            'decided_at' => $this->decidedAt,
            'compared_against_timestamp' => $this->comparedAgainstTimestamp,
            'reason' => $this->reason,
        ];
    }
}
