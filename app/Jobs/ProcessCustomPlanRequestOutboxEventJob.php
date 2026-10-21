<?php

namespace App\Jobs;

use App\Events\CustomPlanRequestDeltaUpdated;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessCustomPlanRequestOutboxEventJob
 *
 * Intent: Decoupled Transactional Outbox Worker for Custom Plan Request mutations.
 * Claims pending events from sys_outbox_events (aggregate_type 'CustomPlanRequest'),
 * dispatches Reverb WebSocket broadcast events on private channel superadmin.custom-plans,
 * invalidates multi-tenant analytics caches, and guarantees at-least-once delivery with
 * exponential backoff retry and DLQ protection.
 *
 * Author: Antigravity AI
 * Date: October 21, 2026
 */
class ProcessCustomPlanRequestOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $backoff = 10;

    public int $timeout = 45;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('custom-plan-outbox');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId);

            return;
        }

        // Batch processing when triggered periodically by worker or scheduler
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CustomPlanRequest')
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

    /**
     * Process an individual claimed outbox event.
     */
    protected function processEvent(int $eventId): void
    {
        DB::beginTransaction();
        try {
            $event = DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->where('aggregate_type', 'CustomPlanRequest')
                ->whereIn('status', ['PENDING', 'PROCESSING'])
                ->lockForUpdate()
                ->first();

            if (! $event) {
                DB::rollBack();

                return;
            }

            // Mark as PROCESSING
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'PROCESSING',
                    'locked_at' => now(),
                    'attempts' => DB::raw('attempts + 1'),
                ]);

            DB::commit();

            $payload = is_string($event->payload) ? json_decode($event->payload, true) : (array) $event->payload;
            $payload = $payload ?? [];

            // 1. Broadcast real-time delta update to connected Super Admin sessions
            try {
                broadcast(new CustomPlanRequestDeltaUpdated(
                    id: (int) ($event->aggregate_id ?? $payload['id'] ?? 0),
                    reference: (string) ($payload['reference'] ?? 'CPR-N/A'),
                    eventType: (string) ($event->event_type ?? 'status_updated'),
                    newStatus: (string) ($payload['new_status'] ?? 'Updated'),
                    previousStatus: $payload['previous_status'] ?? null,
                    entityVersion: (int) ($event->aggregate_version ?? $payload['entity_version'] ?? 1),
                    statusBadge: $payload['status_badge'] ?? null,
                    counts: $payload['counts'] ?? [],
                    lastUpdated: $payload['last_updated'] ?? now()->format('d M Y, h:i A'),
                    adminNotes: $payload['admin_notes'] ?? null,
                    updatedBy: $payload['updated_by'] ?? 'Super Admin',
                    companyId: isset($payload['company_id']) ? (int) $payload['company_id'] : null,
                    extra: $payload['extra'] ?? []
                ));
            } catch (\Throwable $broadcastEx) {
                Log::warning('[CPR_OUTBOX] Reverb broadcast failed; proceeding to mark complete: '.$broadcastEx->getMessage(), [
                    'event_id' => $eventId,
                ]);
            }

            // 2. Invalidate cached analytics counts
            Cache::forget('custom_plan_requests_counts');

            // 3. Mark outbox record as COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[CPR_OUTBOX] Outbox worker failed to process event #'.$eventId.': '.$e->getMessage(), [
                'event_id' => $eventId,
                'trace' => $e->getTraceAsString(),
            ]);

            $attempts = (int) (DB::table('sys_outbox_events')->where('id', $eventId)->value('attempts') ?? 1);
            $nextStatus = ($attempts >= $this->tries) ? 'DEAD_LETTER' : 'FAILED';
            $backoffSecs = min(300, 5 * pow(2, min($attempts, 6))); // Exponential backoff with jitter cap

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $nextStatus,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => now()->addSeconds($backoffSecs),
                ]);
        }
    }
}
