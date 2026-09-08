<?php

namespace App\Jobs;

/**
 * Class UpdateAgencyStatsJob
 *
 * INTENT: Asynchronously invalidates and recalculates Agency Listing Redis stat caches
 * when agency relations, invitations, or candidate applications are updated.
 * Prevents blocking HTTP request cycles while ensuring real-time dashboard accuracy.
 */

use App\Services\AgencyListingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateAgencyStatsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 5;

    /**
     * Create a new job instance.
     *
     * @param  int  $companyId  Target company ID whose stats need invalidation/refreshing
     * @param  int|null  $userId  Optional user ID to increment consultancy cache version
     */
    public function __construct(
        public int $companyId,
        public ?int $userId = null
    ) {}

    /**
     * Execute the job.
     */
    public function handle(AgencyListingService $agencyListingService): void
    {
        // 1. Invalidate cached summary cards for company
        $agencyListingService->clearCompanyStatsCache($this->companyId);

        // 2. Increment user consultancy cache version if user ID is specified
        if ($this->userId) {
            $agencyListingService->clearUserConsultancyCache($this->userId);
        }

        // 3. Pre-warm fresh summary counters in Redis
        $agencyListingService->getSummaryCounters($this->companyId);
    }
}
