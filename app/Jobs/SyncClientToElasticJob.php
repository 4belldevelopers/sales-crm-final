<?php

namespace App\Jobs;

use App\Helper\GeneralMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Job: Sync Client Document to Elasticsearch
 *
 * Intent: Asynchronously process client indexing to Elasticsearch off the web HTTP request thread.
 * Tenancy: Scoped by client_id.
 *
 * Author: Antigravity AI
 * Date: August 13, 2026
 */
class SyncClientToElasticJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [10, 30, 60, 120, 300];

    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $clientId
    ) {
        $this->onQueue('es-index');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if (method_exists(GeneralMethod::class, 'clientStaffingPartner')) {
            GeneralMethod::clientStaffingPartner($this->clientId);
        }
    }
}
