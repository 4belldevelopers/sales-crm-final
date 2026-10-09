<?php

namespace App\Observers;

use App\Events\StageMoved;
use App\Helper\Files;
use App\Jobs\DecrementApplicationCountersJob;
use App\Jobs\DecrementPipelineCountersJob;
use App\Jobs\UpdateApplicationCountersJob;
use App\Jobs\UpdatePipelineCountersJob;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Cache;

class JobApplicationObserver
{
    /**
     * Defer observer executions until active database transaction has committed.
     */
    public bool $afterCommit = true;

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
        // Bust the ES counter cache — key format must match GeneralMethod::custome_count_all().
        Cache::forget("custome_count_all:{$app->company_id}:{$app->job_id}");

        UpdatePipelineCountersJob::dispatch($app->id)->onQueue('counters')->afterCommit();
        UpdateApplicationCountersJob::dispatch($app->id)->onQueue('counters')->afterCommit();

        // Sync candidate state to Elasticsearch read model post-commit
        if (class_exists(\App\Jobs\SyncCandidateToElasticJob::class)) {
            \App\Jobs\SyncCandidateToElasticJob::dispatch($app->id)->afterCommit();
        }

        if ($app->company_id) {
            \App\Support\Cache\TenantCache::invalidateTenant($app->company_id);
        }

        // Broadcast stage move over Reverb so all open Apptracker tabs refresh
        // their counter badges in real time without a page reload.
        if ($app->wasChanged('master_status_id') && $app->job_id && $app->master_status_id) {
            // Also bust the Redis flexible cache so the next loadcounter() call
            // reads fresh data instead of serving the stale 30-120s window.
            Cache::forget("pipeline:{$app->job_id}");
            broadcast(new StageMoved(
                (int) $app->job_id,
                (int) $app->id,
                (int) $app->master_status_id,
                (int) $app->company_id,
            ))->toOthers();
        }
    }

    public function deleted(JobApplication $app): void
    {
        DecrementPipelineCountersJob::dispatch($app->id)->onQueue('counters');
        DecrementApplicationCountersJob::dispatch($app->id)->onQueue('counters');

        /* Intent: Re-open capacity counter when application is deleted/purged */
        if ($app->job_id) {
            try {
                app(\App\Services\JobApplicationCapacityService::class)->releaseSlot((int) $app->job_id);
            } catch (\Throwable) {
            }
        }
    }
}
