<?php

namespace App\Jobs;

use App\Helper\GeneralMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job: Sync User Document to Elasticsearch
 *
 * Intent: Asynchronously process user indexing to Elasticsearch off the web HTTP request thread.
 * Tenancy: Scoped by userId.
 *
 * Author: Antigravity AI
 * Date: August 14, 2026
 */
class SyncUserToElasticJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [10, 30, 60, 120, 300];

    public int $timeout = 60;

    public function __construct(
        public int $userId
    ) {
        $this->onQueue('es-index');
        $this->afterCommit = true;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (method_exists(GeneralMethod::class, 'userIndexingupload')) {
            GeneralMethod::userIndexingupload($this->userId);
        }
    }
}
