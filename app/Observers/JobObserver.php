<?php

namespace App\Observers;

use App\Models\Job;
use App\Services\GeneralMethod;

/**
 * JobObserver
 *
 * Keeps MySQL mutations synchronized with Elasticsearch index.
 * Dispatches async reindex helper on job created, updated, or deleted events.
 *
 * NOTE: Intent & ES-sync note per docs/CODE_DOCUMENTATION_STANDARD.md
 */
class JobObserver
{
    /**
     * Defer observer executions until active database transaction has committed.
     */
    public bool $afterCommit = true;

    /**
     * Handle the Job "created" event.
     */
    public function created(Job $job): void
    {
        $this->syncElasticsearch($job);
    }

    /**
     * Handle the Job "updated" event.
     */
    public function updated(Job $job): void
    {
        $this->syncElasticsearch($job);
    }

    /**
     * Handle the Job "deleted" event.
     */
    public function deleted(Job $job): void
    {
        $this->syncElasticsearch($job);
    }

    /**
     * Sync job entity to Elasticsearch read store.
     */
    protected function syncElasticsearch(Job $job): void
    {
        if ($job->company_id) {
            \App\Support\Cache\TenantCache::invalidateTenant($job->company_id);
            \Illuminate\Support\Facades\Cache::forget("ats:spartner_summary_cnt:comp_{$job->company_id}");
        }

        if (class_exists(GeneralMethod::class) && method_exists(GeneralMethod::class, 'JobAddEditUpload')) {
            try {
                \Illuminate\Support\Facades\DB::afterCommit(function () use ($job) {
                    GeneralMethod::JobAddEditUpload($job->id);
                });
            } catch (\Throwable $e) {
                // Log observer ES sync exception silently to prevent blocking MySQL write transaction
                logger()->error('JobObserver ES sync failed for Job ID '.$job->id.': '.$e->getMessage());
            }
        }
    }
}
