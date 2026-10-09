<?php

namespace App\Jobs;

use App\Events\MarketJobDeltaUpdated;
use App\Helper\GeneralMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessMarketJobOutboxEventJob
 *
 * Intent:
 * Phase 4 Decoupled Transactional Outbox Worker for Market Job mutations.
 * Claims pending sys_outbox_events for aggregate_type 'MarketJob',
 * delivers real-time WebSocket broadcast notifications, ensures Elasticsearch
 * synchronization, and marks outbox records as COMPLETED.
 */
class ProcessMarketJobOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public int $timeout = 60;

    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('market-outbox');
    }

    public function handle(): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId);

            return;
        }

        // Batch processing when run periodically by worker/cron
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'MarketJob')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit(25)
            ->pluck('id')
            ->toArray();

        foreach ($events as $eventId) {
            $this->processEvent((int) $eventId);
        }
    }

    protected function processEvent(int $eventId): void
    {
        $claimed = DB::table('sys_outbox_events')
            ->where('id', $eventId)
            ->where('status', 'PENDING')
            ->update([
                'status' => 'PROCESSING',
                'locked_at' => now(),
                'attempts' => DB::raw('attempts + 1'),
            ]);

        if (! $claimed) {
            return;
        }

        $event = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        if (! $event) {
            return;
        }

        try {
            $payload = json_decode($event->payload, true) ?? [];
            $jobId = (int) ($event->aggregate_id ?? $payload['job_id'] ?? 0);
            $tenantId = (int) ($event->tenant_id ?? 0);

            // 1. Broadcast real-time delta update to connected recruiter sessions
            if ($tenantId > 0 && $jobId > 0) {
                event(new MarketJobDeltaUpdated(
                    companyId: $tenantId,
                    jobId: $jobId,
                    eventType: 'claimed',
                    entityVersion: (int) ($event->aggregate_version ?? 1),
                    actionType: 'under_review',
                    statusLabel: 'HOLD'
                ));

                $fromCompanyId = (int) ($payload['from_company_id'] ?? 0);
                if ($fromCompanyId > 0 && $fromCompanyId !== $tenantId) {
                    event(new MarketJobDeltaUpdated(
                        companyId: $fromCompanyId,
                        jobId: $jobId,
                        eventType: 'vendor_claimed',
                        entityVersion: (int) ($event->aggregate_version ?? 1),
                        actionType: 'vendor_fulfilled',
                        statusLabel: 'HOLD'
                    ));
                    \App\Services\MarketJobV2Service::invalidateCacheForCompany($fromCompanyId);
                }

                \App\Services\MarketJobV2Service::invalidateCacheForCompany($tenantId);
            }

            // 2. Keep Elasticsearch index in sync
            if ($jobId > 0) {
                GeneralMethod::elasticJobsAddEditUpdateIndex($jobId);
            }

            // 3. Mark outbox event as COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);

        } catch (\Throwable $e) {
            Log::error('[ProcessMarketJobOutboxEventJob] Event processing failed for #'.$eventId.': '.$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $finalStatus = ($event->attempts >= $this->tries) ? 'DEAD_LETTER' : 'FAILED';

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $finalStatus,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                ]);

            throw $e;
        }
    }
}
