<?php

namespace App\Listeners;

use App\Events\TenantEntityDeactivatedDueToCoins;
use App\Services\ElasticService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

/**
 * SyncElasticStatusOnCoinDeactivationListener — V2 Coins Engine Asynchronous ES Sync Listener.
 *
 * Intent: Listens for TenantEntityDeactivatedDueToCoins domain events and asynchronously
 * updates Elasticsearch index documents (ELASTIC_USERS_LIST or ELASTIC_JOBS) on the 'elasticsearch' queue
 * after database transactions commit ($afterCommit = true). Prevents synchronous REST calls from stalling
 * worker threads or holding open MySQL transactions.
 *
 * Tenancy Note: Logs and filters by companyId tenant context.
 *
 * Author: Antigravity AI
 * Date: August 26, 2026
 */
class SyncElasticStatusOnCoinDeactivationListener implements ShouldQueue
{
    use InteractsWithQueue;

    public bool $afterCommit = true;

    public int $tries = 3;

    public int $timeout = 30;

    protected ElasticService $elasticService;

    public function __construct(?ElasticService $elasticService = null)
    {
        $this->queue = 'elasticsearch';
        $this->elasticService = $elasticService ?? new ElasticService;
    }

    /**
     * Handle the asynchronous ES status sync event.
     */
    public function handle(TenantEntityDeactivatedDueToCoins $event): void
    {
        try {
            if ($event->entityType === 'user') {
                $this->updateElasticUserStatus($event->entityId, (int) $event->newStatus);
            } elseif ($event->entityType === 'job') {
                $this->updateElasticJobStatus($event->entityId, (string) $event->newStatus);
            }
        } catch (\Throwable $e) {
            Log::warning('SyncElasticStatusOnCoinDeactivationListener: ES status sync failed', [
                'company_id' => $event->companyId,
                'entity_id' => $event->entityId,
                'entity_type' => $event->entityType,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    private function updateElasticUserStatus(int $userId, int $status): void
    {
        if (! defined('ELASTIC_USERS_LIST')) {
            return;
        }

        try {
            $client = $this->elasticService->getClient();
            if (! $client->exists(['index' => ELASTIC_USERS_LIST, 'id' => $userId])) {
                return;
            }
            $client->update([
                'index' => ELASTIC_USERS_LIST,
                'id' => $userId,
                'body' => [
                    'doc' => ['working_status' => $status],
                    'doc_as_upsert' => false,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('SyncElasticStatusOnCoinDeactivationListener: ES user status update failed', [
                'user_id' => $userId,
                'error' => $e->getMessage(),
            ]);
        }
    }

    private function updateElasticJobStatus(int $jobId, string $status): void
    {
        if (! defined('ELASTIC_JOBS')) {
            return;
        }

        try {
            $client = $this->elasticService->getClient();
            if (! $client->exists(['index' => ELASTIC_JOBS, 'id' => $jobId])) {
                return;
            }
            $client->update([
                'index' => ELASTIC_JOBS,
                'id' => $jobId,
                'body' => [
                    'doc' => ['status' => $status],
                    'doc_as_upsert' => false,
                ],
            ]);
        } catch (\Throwable $e) {
            Log::warning('SyncElasticStatusOnCoinDeactivationListener: ES job status update failed', [
                'job_id' => $jobId,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
