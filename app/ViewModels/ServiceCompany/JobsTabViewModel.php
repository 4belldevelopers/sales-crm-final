<?php

namespace App\ViewModels\ServiceCompany;

/**
 * JobsTabViewModel
 *
 * Immutable View Model for the Client Drawer Jobs Tab.
 * Unifies canonical metrics, filtered listing, pipeline stage maps,
 * pagination with page-clamping, and distributed observability metadata.
 *
 * Rule: Zero database or Elasticsearch queries allowed inside views;
 * all data must be fully assembled and exposed through this ViewModel.
 */
class JobsTabViewModel
{
    public function __construct(
        public readonly int $totalCount,         // Canonical count of ALL jobs for this client (unfiltered)
        public readonly int $filteredCount,      // Count of jobs matching current filter & search (for pagination)
        public readonly int $hiringCount,        // Active/Hiring status count (for tab badge & hero KPI)
        public readonly int $holdCount,          // Hold status count
        public readonly int $closedCount,        // Closed status count
        public readonly int $inactiveCount,      // Inactive/Expired status count
        public readonly array $jobs,             // Array of job objects/arrays
        public readonly array $pipelineStageMap, // [jobId => [stageId => count]]
        public readonly array $logoMap,          // [jobId => resolvedLogoPathOrInitial]
        public readonly array $pagination,        // Pagination metadata
        public readonly array $activeFilters,    // Active filter state
        public readonly int $projectionVersion,  // Observable version identity
        public readonly string $queryHash,       // Observable query identity
        public readonly string $generatedAt      // Generation timestamp for telemetry
    ) {}

    public function hasJobs(): bool
    {
        return !empty($this->jobs);
    }

    public function getActiveFilter(): string
    {
        return $this->activeFilters['status'] ?? 'all';
    }

    public function getSearchQuery(): ?string
    {
        return $this->activeFilters['search'] ?? null;
    }
}
