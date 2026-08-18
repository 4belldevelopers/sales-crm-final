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
        if (class_exists(GeneralMethod::class) && method_exists(GeneralMethod::class, 'JobAddEditUpload')) {
            try {
                GeneralMethod::JobAddEditUpload($job->id);
            } catch (\Throwable $e) {
                // Log observer ES sync exception silently to prevent blocking MySQL write transaction
                logger()->error('JobObserver ES sync failed for Job ID ' . $job->id . ': ' . $e->getMessage());
            }
        }
    }
}
