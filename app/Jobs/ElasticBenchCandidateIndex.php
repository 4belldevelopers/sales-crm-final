<?php

namespace App\Jobs;

use App\Services\ElasticBenchCandidatesIndex;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

class ElasticBenchCandidateIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $comid;

    public int $tries = 5;

    public array $backoff = [10, 30, 60, 120, 300];

    public int $timeout = 60;

    public function __construct($comid)
    {
        $this->comid = $comid;
        $this->onQueue('es-index');
        $this->afterCommit = true;
        if (empty($this->uuid)) {
            $this->uuid = (string) Str::uuid();
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ElasticBenchCandidatesIndex $elasticCandidateIndex)
    {
        $elasticCandidateIndex->benchCandidateIndexElastic($this->comid);
    }
}
