<?php

namespace App\ViewModels\ServiceCompany;

/**
 * ClientActivityTabViewModel
 *
 * Immutable View Model for Client Drawer Activity Tab.
 * Unifies chronological event DTOs, aggregate KPI totals, sync/sequence states,
 * encrypted cursor pagination, and health/freshness telemetry.
 *
 * Invariant: 0% database/Elasticsearch reads inside views;
 * all data is pre-assembled and exposed via this ViewModel.
 */
class ClientActivityTabViewModel
{
    public function __construct(
        public readonly array $items,
        public readonly array $kpis,
        public readonly array $sync,
        public readonly array $pagination,
        public readonly array $health,
        public readonly array $meta,
        public readonly array $activeFilters,
        public readonly string $clientType = 'internal'
    ) {}

    public function hasItems(): bool
    {
        return ! empty($this->items);
    }

    public function toArray(): array
    {
        return [
            'success' => true,
            'data' => $this->items,
            'kpi' => $this->kpis,
            'sync' => $this->sync,
            'pagination' => $this->pagination,
            'health' => $this->health,
            'meta' => $this->meta,
            'filters' => $this->activeFilters,
        ];
    }
}
