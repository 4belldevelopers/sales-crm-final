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
 * Recomputes rec_sent/accepted/rejected in job_agency_counter from recommendation_relation.
 * Runs on the 'counters' queue.
 */
class UpdateJobAgencyCounterJob implements ShouldQueue
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
        $rows = DB::table('recommendation_relation')
            ->where('job_id', $this->jobId)
            ->selectRaw('final_status, COUNT(*) as cnt')
            ->groupBy('final_status')
            ->pluck('cnt', 'final_status');

        $recSent     = (int) ($rows[0] ?? 0);
        $recAccepted = (int) ($rows[1] ?? 0);
        $recRejected = (int) ($rows[2] ?? 0);

        DB::table('job_agency_counter')
            ->upsert(
                [
                    'job_id'        => $this->jobId,
                    'company_id'    => $this->companyId,
                    'rec_sent'      => $recSent,
                    'rec_accepted'  => $recAccepted,
                    'rec_rejected'  => $recRejected,
                    'last_event_at' => now(),
                ],
                ['job_id'],
                ['rec_sent', 'rec_accepted', 'rec_rejected', 'last_event_at']
            );

        try {
            Redis::hmset("job_agency:{$this->jobId}", [
                'rec_sent'     => $recSent,
                'rec_accepted' => $recAccepted,
                'rec_rejected' => $recRejected,
            ]);
        } catch (\Throwable) {
            // Redis unavailable — DB row is the source of truth
        }
    }
}
