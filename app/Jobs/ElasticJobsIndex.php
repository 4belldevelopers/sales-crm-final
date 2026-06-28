<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use Illuminate\Queue\SerializesModels;
use App\Services\ElasticJobListIndex;

class ElasticJobsIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $comid;
    protected $elasticJobListIndex;

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
    public function handle(ElasticJobListIndex $elasticJobListIndex)
    {
        $elasticJobListIndex->jobsIndexElastic($this->comid);
    }
}
