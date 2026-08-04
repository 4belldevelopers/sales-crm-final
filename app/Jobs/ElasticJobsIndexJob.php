<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

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
    }

    public function handle(): void
    {
        if ($this->isDelete) {
            if (method_exists(\App\Helper\GeneralMethod::class, 'elasticDelete')) {
                \App\Helper\GeneralMethod::elasticDelete('ats_jobs_elastic', $this->jobId);
            }
            return;
        }

        $job = DB::table('jobs')->where('id', $this->jobId)->first();
        if (!$job) {
            return;
        }

        if (method_exists(\App\Helper\GeneralMethod::class, 'elasticIndex')) {
            \App\Helper\GeneralMethod::elasticIndex('ats_jobs_elastic', $this->jobId, (array) $job);
        }
    }
}
