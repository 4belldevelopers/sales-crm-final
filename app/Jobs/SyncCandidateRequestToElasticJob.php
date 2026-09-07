<?php

namespace App\Jobs;

use App\Helper\GeneralMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SyncCandidateRequestToElasticJob — Asynchronous Elasticsearch candidate request document index worker.
 *
 * Intent:
 * Offloads GeneralMethod::candidateRequestIndexElastic($recId) Elasticsearch document update out of the
 * synchronous HTTP request lifecycle into Horizon queue.
 *
 * Tenancy Note:
 * Scoped by rec_candidate_assign_id ($recId).
 *
 * Author: Antigravity AI
 * Date: August 25, 2026
 */
class SyncCandidateRequestToElasticJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [10, 30, 60, 120, 300];

    public int $timeout = 60;

    public function __construct(
        public int $recId
    ) {
        $this->onQueue('es-index');
        $this->afterCommit = true;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            GeneralMethod::candidateRequestIndexElastic($this->recId);
        } catch (\Throwable $e) {
            Log::error('SyncCandidateRequestToElasticJob failed: '.$e->getMessage(), [
                'rec_id' => $this->recId,
                'exception' => $e,
            ]);
            throw $e;
        }
    }
}
