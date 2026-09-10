<?php

namespace App\Jobs;

use App\Services\ElasticService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Class SyncTeamRankToElasticJob
 * 
 * Intent: Asynchronously index team rank point log updates into Elasticsearch (ELASTIC_TEAM_RANK).
 * Decouples Elasticsearch HTTP indexing calls from web request execution to eliminate latency spikes
 * and prevent MySQL transaction rollbacks if Elasticsearch is temporarily unavailable.
 *
 * Tenancy Note: Each team rank entry is explicitly scoped by `company_id` and `user_id`.
 */
class SyncTeamRankToElasticJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     *
     * @var int
     */
    public $backoff = 5;

    /**
     * Payload data to index in Elasticsearch.
     *
     * @var array
     */
    protected array $payload;

    /**
     * MySQL log ID matching the point transaction.
     *
     * @var int
     */
    protected int $mysqlId;

    /**
     * Create a new job instance.
     *
     * @param int $mysqlId
     * @param array $payload
     */
    public function __construct(int $mysqlId, array $payload)
    {
        $this->mysqlId = $mysqlId;
        $this->payload = $payload;
    }

    /**
     * Execute the job.
     *
     * @param ElasticService $elasticService
     * @return void
     */
    public function handle(ElasticService $elasticService): void
    {
        if (empty($this->mysqlId) || empty($this->payload)) {
            return;
        }

        try {
            $elasticService->getClient()->index([
                'index' => ELASTIC_TEAM_RANK,
                'id' => $this->mysqlId,
                'body' => [
                    'doc' => $this->payload,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::error("SyncTeamRankToElasticJob failed for MySQL ID {$this->mysqlId}: " . $e->getMessage(), [
                'mysql_id' => $this->mysqlId,
                'payload' => $this->payload,
                'exception' => $e,
            ]);

            throw $e;
        }
    }
}
