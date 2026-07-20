<?php

namespace App\Services\OwnershipEngine\DataTransferObjects;

/**
 * The Ownership Engine's read/write result shape. docs/OWNERSHIP_ENGINE_DESIGN.md §9.
 *
 * `state` is null when the row is undetermined/private (§7, ER1/ER6 — an agency row that
 * hasn't reached Sourcing yet has no cached value at all, which IS the "private" state,
 * not a separate value to track).
 */
final class OwnershipResult
{
    public const STATE_OWNER = 1;
    public const STATE_DUPLICATE = 2;

    public function __construct(
        public readonly int $rowId,
        public readonly ?int $state,
        public readonly ?int $clusterId,
        public readonly ?int $ownerRowId = null,
        public readonly ?string $ownerType = null,
        public readonly ?string $decisionRule = null,
        public readonly ?string $decidedAt = null,
    ) {
    }

    public function isOwner(): bool
    {
        return $this->state === self::STATE_OWNER;
    }

    public function isDuplicate(): bool
    {
        return $this->state === self::STATE_DUPLICATE;
    }

    public function isUndetermined(): bool
    {
        return $this->state === null;
    }
}
