<?php

namespace App\Jobs;

use App\Events\AgencyRequestDeltaUpdated;
use App\Helper\GeneralMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessAgencyOutboxEventJob
 *
 * Intent:
 * Phase 5 Decoupled Transactional Outbox Worker for Partner Agency mutations.
 * Claims pending sys_outbox_events for aggregate_type 'AgencyRequest',
 * delivers real-time WebSocket broadcast notifications, ensures Elasticsearch
 * synchronization, handles asynchronous notification delivery, and marks outbox
 * records as COMPLETED with bounded retries and poison-pill isolation.
 *
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ProcessAgencyOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public int $timeout = 60;

    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('agency-outbox');
    }

    public function handle(): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId);

            return;
        }

        // Self-healing: reclaim stranded events locked > 5 minutes ago due to worker crash
        DB::table('sys_outbox_events')
            ->where('aggregate_type', 'AgencyRequest')
            ->where('status', 'PROCESSING')
            ->where('locked_at', '<', now()->subMinutes(5))
            ->update([
                'status' => 'PENDING',
                'available_at' => now(),
            ]);

        // Batch processing when triggered periodically by worker or scheduler
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'AgencyRequest')
            ->whereIn('status', ['PENDING', 'FAILED'])
            ->where('available_at', '<=', now())
            ->where('attempts', '<', $this->tries)
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
            ->where(function ($q) {
                $q->whereIn('status', ['PENDING', 'FAILED'])
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'PROCESSING')
                            ->where('locked_at', '<', now()->subMinutes(5));
                    });
            })
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
            $invitationId = (int) ($event->aggregate_id ?? $payload['invitation_id'] ?? 0);
            $relationId = (int) ($payload['relation_id'] ?? 0);
            $fromCompanyId = (int) ($event->tenant_id ?? $payload['from_company_id'] ?? 0);
            $toCompanyId = (int) ($payload['to_company_id'] ?? 0);
            $eventType = $event->event_type;

            // 1. Synchronize to Elasticsearch index
            if ($relationId > 0) {
                try {
                    GeneralMethod::jobRequestIndexElastic($relationId);
                } catch (\Throwable $e) {
                    Log::warning("[ProcessAgencyOutboxEventJob] ES sync warning for relation #{$relationId}: ".$e->getMessage());
                }
            }

            if ($invitationId > 0 && ($eventType === 'AGENCY_REQUEST_SENT' || $eventType === 'BULK_AGENCY_REQUIREMENT_SENT' || $eventType === 'AGENCY_INVITATION_SENT')) {
                try {
                    GeneralMethod::clientRequestIndexElastic($invitationId);
                } catch (\Throwable $e) {
                    Log::warning("[ProcessAgencyOutboxEventJob] ES sync warning for invitation #{$invitationId}: ".$e->getMessage());
                }
            }

            // 2. Real-Time WebSocket broadcast to both tenant and agency sessions
            if ($fromCompanyId > 0 && $toCompanyId > 0) {
                try {
                    $broadcastAction = ($eventType === 'AGENCY_REQUEST_CANCELLED') ? 'cancelled' : 'sent';

                    // Broadcast to employer company sessions
                    event(new AgencyRequestDeltaUpdated(
                        companyId: $fromCompanyId,
                        agencyId: $toCompanyId,
                        eventType: $broadcastAction,
                        entityVersion: (int) ($event->aggregate_version ?? 1),
                        invitationId: $invitationId,
                        extra: ['from_company_id' => $fromCompanyId]
                    ));

                    // Broadcast to partner agency company sessions
                    event(new AgencyRequestDeltaUpdated(
                        companyId: $toCompanyId,
                        agencyId: $fromCompanyId,
                        eventType: $broadcastAction,
                        entityVersion: (int) ($event->aggregate_version ?? 1),
                        invitationId: $invitationId,
                        extra: ['from_company_id' => $fromCompanyId]
                    ));
                } catch (\Throwable $e) {
                    Log::warning("[ProcessAgencyOutboxEventJob] WebSocket broadcast warning for invitation #{$invitationId}: ".$e->getMessage());
                }
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
            Log::error("[ProcessAgencyOutboxEventJob] Event processing failed for #{$eventId}: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $finalStatus = ($event->attempts >= $this->tries) ? 'DEAD_LETTER' : 'FAILED';

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $finalStatus,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => now()->addSeconds($this->backoff * max(1, $event->attempts)),
                ]);
        }
    }
}
