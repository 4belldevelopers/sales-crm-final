<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * Recomputes bench_total in job_agency_counter from the live bench_applications table.
 * Runs on the 'counters' queue so it never blocks the default worker pool.
 */
class UpdateBenchCountersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 90];
    public int $timeout = 30;

    public function __construct(
        public int $jobId,
        public int $companyId,
    ) {
        $this->onQueue('counters');
    }

    public function handle(): void
    {
        $benchTotal = DB::table('bench_applications')
            ->where('job_id', $this->jobId)
            ->whereNull('deleted_at')
            ->count();

        DB::table('job_agency_counter')
            ->upsert(
                [
                    'job_id'        => $this->jobId,
                    'company_id'    => $this->companyId,
                    'bench_total'   => $benchTotal,
                    'last_event_at' => now(),
                ],
                ['job_id'],
                ['bench_total', 'last_event_at']
            );

        try {
            Redis::hset("job_agency:{$this->jobId}", 'bench_total', $benchTotal);
        } catch (\Throwable) {
            // Redis unavailable — DB row is the source of truth
        }
    }
}
