<?php

namespace App\DTO\Commercials;

/**
 * CommercialStateDTO — Canonical domain representation of tenant commercial configurations.
 * Prevents fragmented database interpretations across all ATS and Marketplace consumers.
 *
 * Tenancy: Always strictly scoped to company_id.
 */
class CommercialStateDTO
{
    public function __construct(
        public readonly int $companyId,
        public readonly int $version,
        public readonly string $updatedAt,
        public readonly ServiceTermDTO $fulltime,
        public readonly ServiceTermDTO $contract
    ) {}

    public static function fromProjections(int $companyId, int $version, ?array $fulltimeData, ?array $contractData): self
    {
        $ftArray = $fulltimeData ?? ['commtype' => 2, 'company_id' => $companyId];
        $ctArray = $contractData ?? ['commtype' => 1, 'company_id' => $companyId];

        return new self(
            companyId: $companyId,
            version: $version,
            updatedAt: now()->toIso8601String(),
            fulltime: ServiceTermDTO::fromArray($ftArray),
            contract: ServiceTermDTO::fromArray($ctArray)
        );
    }

    public function toArray(): array
    {
        return [
            'company_id' => $this->companyId,
            'version' => $this->version,
            'updated_at' => $this->updatedAt,
            'services' => [
                'fulltime' => $this->fulltime->toArray(),
                'contract' => $this->contract->toArray(),
            ],
        ];
    }
}
