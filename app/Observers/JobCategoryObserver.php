<?php

namespace App\Observers;

use App\Models\JobCategory;
use Illuminate\Support\Facades\Cache;

class JobCategoryObserver
{
    public function saving(JobCategory $jobCategory): void
    {
        if (company()) {
            $jobCategory->company_id = company()->id;
        }
    }

    public function saved(JobCategory $jobCategory): void
    {
        Cache::forget('tenant:' . $jobCategory->company_id . ':categories');
    }

    public function deleted(JobCategory $jobCategory): void
    {
        Cache::forget('tenant:' . $jobCategory->company_id . ':categories');
    }
}
