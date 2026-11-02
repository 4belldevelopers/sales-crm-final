<?php

namespace App\ViewModels\ServiceCompany;

/**
 * AgencyActivityTabViewModel
 *
 * Immutable View Model for the Agency Drawer Activity Tab.
 * Unifies chronological event DTOs, 15-category KPI totals, hero summary metrics,
 * cursor pagination, and health/freshness telemetry with strictly 0% MySQL reads.
 */
class AgencyActivityTabViewModel
{
    public function __construct(
        public readonly array $items,
        public readonly array $groupedItems,
        public readonly array $kpis,
        public readonly array $heroMetrics,
        public readonly array $health,
        public readonly array $pagination,
        public readonly array $meta,
        public readonly array $activeFilters
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
            'grouped_data' => $this->groupedItems,
            'kpi' => $this->kpis,
            'hero' => $this->heroMetrics,
            'health' => $this->health,
            'pagination' => $this->pagination,
            'meta' => $this->meta,
            'filters' => $this->activeFilters,
        ];
    }
}
