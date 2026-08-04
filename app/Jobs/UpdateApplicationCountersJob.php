<?php

namespace App\Jobs;

use App\Models\JobApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

class UpdateApplicationCountersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 90];

    public function __construct(public int $jobApplicationId) {}

    public function handle(): void
    {
        $app = JobApplication::withTrashed()->find($this->jobApplicationId);
        if (!$app || $app->trashed()) {
            return;
        }

        $jobId = $app->job_id;

        try {
            Redis::hincrby("app_counters:{$jobId}", 'total_apps', 1);
        } catch (\Throwable) {
            // Redis unavailable — DB summary table still updated below
        }

        DB::table('application_counters')->updateOrInsert(
            ['job_id' => $jobId],
            [
                'total_apps'    => DB::raw('total_apps + 1'),
                'last_event_at' => now(),
            ]
        );
    }
}
