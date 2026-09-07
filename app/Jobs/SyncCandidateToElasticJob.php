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
 * SyncCandidateToElasticJob — Asynchronous Elasticsearch candidate document index worker.
 *
 * Intent:
 * Offloads GeneralMethod::careerStore($candidateId) Elasticsearch document update out of the
 * synchronous MySQL database transaction thread into Horizon queue.
 *
 * Tenancy:
 * Scoped by job_application_id ($candidateId).
 *
 * Author: Antigravity AI
 * Date: August 14, 2026
 */
class SyncCandidateToElasticJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [10, 30, 60, 120, 300];

    public int $timeout = 60;

    public function __construct(
        public int $candidateId
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
            GeneralMethod::careerStore($this->candidateId);
        } catch (\Throwable $e) {
            Log::error('SyncCandidateToElasticJob failed: '.$e->getMessage(), [
                'candidate_id' => $this->candidateId,
                'exception' => $e,
            ]);
            throw $e;
        }
    }
}
