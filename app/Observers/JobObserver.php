<?php

namespace App\Observers;

use App\Helper\GeneralMethod;
use App\Jobs\UpdateJobCountersJob;
use App\Models\Job;
use Illuminate\Support\Facades\Cache;

class JobObserver
{
    public function saved(Job $job): void
    {
        Cache::forget("custome_count_all:{$job->id}");
        Cache::forget('company:' . $job->company_id);

        // Bump company-level version so all team members' job listing caches are invalidated.
        GeneralMethod::cacheClean("alljobslist_version_company_{$job->company_id}");

        UpdateJobCountersJob::dispatch($job->id)->onQueue('counters');
    }

    public function deleted(Job $job): void
    {
        Cache::forget("custome_count_all:{$job->id}");
        GeneralMethod::cacheClean("alljobslist_version_company_{$job->company_id}");
        UpdateJobCountersJob::dispatch($job->id, isDelete: true)->onQueue('counters');
    }
}
