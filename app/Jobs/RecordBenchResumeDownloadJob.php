<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Intent: Record Bench Resume Center download analytics away from the HTTP request
 * so a click spike does not turn into synchronous MySQL write pressure.
 */
class RecordBenchResumeDownloadJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 30;

    public function __construct(
        private readonly int $candidateId,
        private readonly int $userId,
        private readonly int $companyId
    ) {
        $this->onQueue('counters');
    }

    public function handle(): void
    {
        try {
            DB::table('bench_count_view_download')->insert([
                'user_id' => $this->userId,
                'candidate_id' => $this->candidateId,
                'type' => 2,
                'status' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('Bench resume download counter insert failed', [
                'candidate_id' => $this->candidateId,
                'user_id' => $this->userId,
                'company_id' => $this->companyId,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
