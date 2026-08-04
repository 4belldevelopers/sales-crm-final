<?php

namespace App\Jobs;

use App\Models\Job;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class UpdateJobCountersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;
    public int $backoff = 10;

    public function __construct(public int $jobId, public bool $isDelete = false) {}

    public function handle(): void
    {
        $job = Job::withTrashed()->find($this->jobId);
        if (!$job) {
            return;
        }

        $companyId = $job->company_id;
        $status    = $this->isDelete ? 'removed' : $job->status;
        $delta     = $this->isDelete ? -1 : 1;
        $redisKey  = "job_counters:{$companyId}";

        try {
            Redis::pipeline(function ($pipe) use ($redisKey, $status, $delta) {
                $pipe->hincrby($redisKey, 'total_jobs', $delta);
                $pipe->hincrby($redisKey, "{$status}_jobs", $delta);
                $pipe->hset($redisKey, 'last_event_at', now()->toIso8601String());
            });
        } catch (\Throwable) {
            // Redis unavailable — DB summary table still updated below
        }

        DB::table('job_counters')->updateOrInsert(
            ['company_id' => $companyId],
            [
                'total_jobs'       => DB::raw("total_jobs + ({$delta})"),
                "{$status}_jobs"   => DB::raw("{$status}_jobs + ({$delta})"),
                'last_job_id'      => $job->id,
                'last_event_at'    => now(),
            ]
        );
    }
}
