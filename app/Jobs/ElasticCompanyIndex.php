<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Str;
use Illuminate\Queue\SerializesModels;
use App\Services\ElasticCaompanyIndex;

class ElasticCompanyIndex implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /** Job controls */
    public $tries = 10;                // retry attempts
    public $backoff = 60;              // wait 60s between retries
    public $timeout = 600;             // job can run 10 mins max
    public $failOnTimeout = false;     // timeout won't hard fail job
    public function __construct($comid)
    {
        $this->comid = $comid;
        $this->onQueue('es-index');
        $this->afterCommit = true;
        $this->uuid = (string) Str::uuid(); // force fresh job UUID
    }

    public function handle(ElasticCaompanyIndex $elasticCaompanyIndex)
    {
        try {

            Log::info("[ElasticCompanyIndex] Starting index job", [
                'company_id' => $this->comid,
                'uuid' => $this->uuid
            ]);

            $elasticCaompanyIndex->companyIndexElastic($this->comid);

            Log::info("[ElasticCompanyIndex] Index completed successfully", [
                'company_id' => $this->comid
            ]);

        } catch (\Throwable $e) {

            Log::error("[ElasticCompanyIndex] FAILED attempt", [
                'company_id' => $this->comid,
                'msg' => $e->getMessage(),
                'line' => $e->getLine(),
                'file' => $e->getFile()
            ]);

            /**
             * VERY IMPORTANT:
             * This releases job back into queue
             * instead of marking a failed attempt
             */
            return $this->release($this->backoff);
        }
    }

    /**
     * When job completely dies after all $tries
     */
    public function failed(\Throwable $e)
    {
        Log::error("[ElasticCompanyIndex] Permanently failed after all attempts", [
            'company_id' => $this->comid,
            'final_error' => $e->getMessage(),
        ]);
    }
}
