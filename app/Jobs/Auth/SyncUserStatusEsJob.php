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
 * V2_login Phase 3.3 — async ES status sync for company-wide block/unblock operations.
 *
 * Used only by BlockCompanyAction (updateByQuery across all company users + company doc).
 * For single-user status changes, GeneralMethod::userIndexingupload() is used instead
 * (it performs a full document re-index with profile data).
 *
 * No 'refresh' => true — ES eventual consistency is acceptable for status toggles.
 */
class SyncUserStatusEsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public array $backoff = [30, 60, 120];

    public function __construct(
        public readonly int    $companyId,
        public readonly string $newStatus,
    ) {}

    public function handle(ElasticService $es): void
    {
        $updateTime = now()->toIso8601String();

        // Update all users in the company.
        $usersParams = [
            'index' => ELASTIC_USERS_LIST,
            'body'  => [
                'script' => [
                    'source' => 'ctx._source.status = params.status; ctx._source.updated_at = params.updated_at;',
                    'lang'   => 'painless',
                    'params' => ['status' => $this->newStatus, 'updated_at' => $updateTime],
                ],
                'query' => [
                    'term' => ['company_id' => $this->companyId],
                ],
            ],
        ];
        if ($es->indexExists(ELASTIC_USERS_LIST)) {
            $es->getClient()->updateByQuery($usersParams);
        }

        // Update the company document.
        $companyParams = [
            'index' => ELASTIC_COMPANIES_INDEX,
            'body'  => [
                'script' => [
                    'source' => 'ctx._source.status = params.status; ctx._source.updated_at = params.updated_at;',
                    'lang'   => 'painless',
                    'params' => ['status' => $this->newStatus, 'updated_at' => $updateTime],
                ],
                'query' => [
                    'term' => ['id' => $this->companyId],
                ],
            ],
        ];
        if ($es->indexExists(ELASTIC_COMPANIES_INDEX)) {
            $es->getClient()->updateByQuery($companyParams);
        }
    }

    public function failed(\Throwable $e): void
    {
        Log::error('jobs.auth.sync_user_status_es.failed', [
            'companyId' => $this->companyId,
            'newStatus' => $this->newStatus,
            'error'     => $e->getMessage(),
        ]);
    }
}
