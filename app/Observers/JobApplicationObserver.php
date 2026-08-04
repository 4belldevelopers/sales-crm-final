<?php

namespace App\Observers;

use App\Helper\Files;
use App\Jobs\UpdateApplicationCountersJob;
use App\Jobs\UpdatePipelineCountersJob;
use App\Jobs\DecrementApplicationCountersJob;
use App\Jobs\DecrementPipelineCountersJob;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Cache;

class JobApplicationObserver
{
    public function saving(JobApplication $jobApplication): void
    {
        if (company()) {
            $jobApplication->company_id = company()->id;
        }
    }

    public function updating(JobApplication $jobApplication): void
    {
        $original = $jobApplication->getOriginal();
        if ($jobApplication->isDirty('photo')) {
            Files::deleteFile($original['photo'], 'candidate-photos');
        }
    }

    public function saved(JobApplication $app): void
    {
        // Bust the ES counter cache for this job
        Cache::forget("custome_count_all:{$app->job_id}");

        UpdatePipelineCountersJob::dispatch($app->id)->onQueue('counters');
        UpdateApplicationCountersJob::dispatch($app->id)->onQueue('counters');
    }

    public function deleted(JobApplication $app): void
    {
        DecrementPipelineCountersJob::dispatch($app->id)->onQueue('counters');
        DecrementApplicationCountersJob::dispatch($app->id)->onQueue('counters');
    }
}
