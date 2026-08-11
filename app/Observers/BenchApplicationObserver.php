<?php

namespace App\Observers;

use App\Jobs\ElasticBenchCandidateIndex;
use App\Jobs\UpdateBenchCountersJob;
use App\Models\BenchApplication;

/**
 * Keeps job_agency_counter.bench_total accurate and syncs Elasticsearch bench index asynchronously.
 */
class BenchApplicationObserver
{
    public function saved(BenchApplication $bench): void
    {
        UpdateBenchCountersJob::dispatch($bench->job_id, $bench->company_id);
        
        // INTENT: Asynchronously sync updated candidate to Elasticsearch bench index via background queue worker
        if ($bench->company_id) {
            ElasticBenchCandidateIndex::dispatch($bench->company_id);
        }
    }

    public function deleted(BenchApplication $bench): void
    {
        UpdateBenchCountersJob::dispatch($bench->job_id, $bench->company_id);
        
        if ($bench->company_id) {
            ElasticBenchCandidateIndex::dispatch($bench->company_id);
        }
    }
}
