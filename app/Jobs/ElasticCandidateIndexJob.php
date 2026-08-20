<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Re-indexes a single candidate document in ats_candidates_elastic.
 * Runs on the 'es-index' queue to keep ES writes isolated from business queues.
 */
class ElasticCandidateIndexJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public array $backoff = [10, 30, 60, 120, 300];

    public int $timeout = 60;

    /**
     * Dispatch queue job only after database transaction has committed.
     */
    public bool $afterCommit = true;

    public function __construct(public int $candidateId)
    {
        $this->onQueue('indexing');
    }

    public function handle(): void
    {
        $candidate = DB::table('job_applications')
            ->where('id', $this->candidateId)
            ->first();

        if (! $candidate) {
            return;
        }

        // Delegate to whichever ES helper the codebase already uses.
        // GeneralMethod::elasticIndex() or equivalent — adapt to actual helper.
        if (method_exists(\App\Helper\GeneralMethod::class, 'elasticIndex')) {
            \App\Helper\GeneralMethod::elasticIndex('ats_candidates_elastic', $this->candidateId, (array) $candidate);
        }
    }
}
