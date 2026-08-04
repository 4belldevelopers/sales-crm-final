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

class UpdatePipelineCountersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [10, 30, 90];
    public int $timeout = 30;

    public function __construct(public int $jobApplicationId) {}

    public function handle(): void
    {
        $app = JobApplication::withTrashed()->find($this->jobApplicationId);
        if (!$app || $app->trashed()) {
            return;
        }

        $stage = $this->stageFromMasterStatus((int) $app->master_status_id);
        $jobId = $app->job_id;

        try {
            Redis::pipeline(function ($pipe) use ($jobId, $stage) {
                $pipe->hincrby("pipeline:{$jobId}", $stage, 1);
                $pipe->hset("pipeline:{$jobId}", 'last_event_at', now()->toIso8601String());
            });
        } catch (\Throwable) {
            // Redis unavailable — DB summary table still updated below
        }

        DB::table('pipeline_counters')->updateOrInsert(
            ['job_id' => $jobId, 'company_id' => $app->company_id],
            [
                $stage         => DB::raw("{$stage} + 1"),
                'last_event_at' => now(),
                'fresh_data'   => 0,
            ]
        );
    }

    private function stageFromMasterStatus(int $masterStatusId): string
    {
        return match ($masterStatusId) {
            1       => 'sourcing',
            2       => 'screening',
            3       => 'evaluation',
            4       => 'negotiation',
            5       => 'offered',
            6       => 'joined',
            7, 9    => 'rejected',
            default => 'sourcing',
        };
    }
}
