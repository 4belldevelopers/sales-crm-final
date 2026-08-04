<?php

namespace App\Observers;

use App\Jobs\UpdateJobCountersJob;
use App\Models\Job;
use Illuminate\Support\Facades\Cache;

class JobObserver
{
    public function saved(Job $job): void
    {
        // Bust per-job ES counter cache for this tenant
        Cache::forget("custome_count_all:{$job->id}");
        Cache::forget("filter_jobs:{$job->company_id}");
        Cache::forget('company:' . $job->company_id);

        UpdateJobCountersJob::dispatch($job->id)->onQueue('counters');
    }

    public function deleted(Job $job): void
    {
        Cache::forget("custome_count_all:{$job->id}");
        UpdateJobCountersJob::dispatch($job->id, isDelete: true)->onQueue('counters');
    }
}
