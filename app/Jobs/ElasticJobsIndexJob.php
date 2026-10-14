<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Re-indexes a single job document in ats_jobs_elastic.
 * Runs on the 'es-index' queue to isolate ES I/O from business queues.
 */
class ElasticJobsIndexJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [10, 30, 60, 120, 300];

    public int $timeout = 60;

    public function __construct(
        public int $jobId,
        public bool $isDelete = false,
    ) {
        $this->onQueue('es-index');
        $this->afterCommit = true;
    }

    public function handle(): void
    {
        if ($this->isDelete) {
            if (method_exists(\App\Helper\GeneralMethod::class, 'elasticDelete')) {
                \App\Helper\GeneralMethod::elasticDelete('ats_jobs_elastic', $this->jobId);
            }

            return;
        }

        \App\Helper\GeneralMethod::elasticJobsAddEditUpdateIndex($this->jobId);

        // Intent: Signal instant readiness for recruiter 10-second verification gate (0% MySQL reads)
        try {
            $companyId = \Illuminate\Support\Facades\DB::table('jobs')->where('id', $this->jobId)->value('company_id');
            if ($companyId) {
                \Illuminate\Support\Facades\Cache::put("job:ready:{$companyId}:{$this->jobId}", true, 120);
            }
        } catch (\Throwable $e) {
            // Non-blocking telemetry
        }
    }
}
