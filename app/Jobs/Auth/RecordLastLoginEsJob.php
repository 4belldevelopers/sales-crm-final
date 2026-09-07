<?php

namespace App\Jobs\Auth;

use App\Services\ElasticService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Records last_login for both the user and their company in a single ES bulk call.
 *
 * Replaces the 4 synchronous ES round-trips (2× exists() + 2× update()) in the old
 * LoginController::login() with 1 async HTTP call using doc_as_upsert — no pre-checks needed.
 * V2_login Phase 1.2 (docs/V2_login/IMPLEMENTATION_PLAN.md §1.2).
 */
class RecordLastLoginEsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int   $tries  = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(
        private int    $userId,
        private int    $companyId,
        private string $timestamp,
    ) {}

    public function handle(ElasticService $elasticService): void
    {
        $elasticService->getClient()->bulk([
            'body' => [
                ['update' => ['_index' => ELASTIC_USERS_LIST, '_id' => $this->userId, 'retry_on_conflict' => 5]],
                ['doc' => ['last_login' => $this->timestamp], 'doc_as_upsert' => true],
                ['update' => ['_index' => ELASTIC_COMPANIES_INDEX, '_id' => $this->companyId, 'retry_on_conflict' => 5]],
                ['doc' => ['last_login' => $this->timestamp], 'doc_as_upsert' => true],
            ],
        ]);
    }

    public function failed(\Throwable $e): void
    {
        Log::error('jobs.auth.record_last_login_es.failed', [
            'user_id'    => $this->userId,
            'company_id' => $this->companyId,
            'exception'  => $e->getMessage(),
        ]);
    }
}
