<?php

/**
 * IndexNewCompany — V2 Registration Sprint 5, task 5.3 (ADR-V2R-06).
 *
 * Listens for CompanyRegistered and enqueues IndexCompanyBulkJob on the 'indexing'
 * queue. Because the job is queued (ShouldQueue on IndexCompanyBulkJob), an ES outage
 * cannot block the registration HTTP response.
 *
 * Wire in EventServiceProvider (or app/Providers/EventServiceProvider.php):
 *   CompanyRegistered::class => [IndexNewCompany::class],
 */

namespace App\Listeners;

use App\Events\CompanyRegistered;
use App\Jobs\IndexCompanyBulkJob;

class IndexNewCompany
{
    public function handle(CompanyRegistered $event): void
    {
        IndexCompanyBulkJob::dispatch($event->company->id)
            ->onQueue('indexing');
    }
}
