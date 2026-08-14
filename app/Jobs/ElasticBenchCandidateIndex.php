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

    public $tries = 3;

    public $backoff = [5, 15, 60];

    protected $elasticCandidateIndex;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($comid)
    {
        $this->comid = $comid;
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
