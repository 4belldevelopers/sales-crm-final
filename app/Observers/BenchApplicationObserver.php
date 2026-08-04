<?php

namespace App\Observers;

use App\Jobs\UpdateBenchCountersJob;
use App\Models\BenchApplication;

/**
 * Keeps job_agency_counter.bench_total accurate after any bench application change.
 */
class BenchApplicationObserver
{
    public function saved(BenchApplication $bench): void
    {
        UpdateBenchCountersJob::dispatch($bench->job_id, $bench->company_id);
    }

    public function deleted(BenchApplication $bench): void
    {
        UpdateBenchCountersJob::dispatch($bench->job_id, $bench->company_id);
    }
}
