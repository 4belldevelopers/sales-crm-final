<?php

namespace App\Jobs;

use App\Helper\GeneralMethod;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessClientOutboxEventJob
 *
 * Intent: Decoupled Transactional Outbox Worker for Client mutations (Add / Edit).
 * Claims pending sys_outbox_events for aggregate_type 'Client',
 * synchronizes Elasticsearch index (ELASTIC_CLIENT_LIST) asynchronously off the web HTTP thread,
 * and marks outbox records as COMPLETED with bounded retry backoff.
 *
 * Author: Antigravity AI
 * Date: October 15, 2026
 */
class ProcessClientOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 15;

    public int $timeout = 60;

    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('client-outbox');
    }

    public function handle(): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId);

            return;
        }

        // Batch processing if dispatched without specific event ID
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Client')
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
        DB::beginTransaction();
        try {
            $event = DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->where('aggregate_type', 'Client')
                ->whereIn('status', ['PENDING', 'PROCESSING'])
                ->lockForUpdate()
                ->first();

            if (! $event) {
                DB::rollBack();

                return;
            }

            // Mark PROCESSING
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'PROCESSING',
                    'locked_at' => now(),
                    'attempts' => DB::raw('attempts + 1'),
                ]);

            DB::commit();

            $clientId = (int) $event->aggregate_id;

            // 1. Synchronize to Elasticsearch asynchronously
            if ($clientId > 0) {
                try {
                    GeneralMethod::clientStaffingPartner($clientId);
                } catch (\Throwable $e) {
                    Log::warning("ProcessClientOutboxEventJob: ES index sync warning for client {$clientId}: ".$e->getMessage());
                }
            }

            // 2. Mark COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("ProcessClientOutboxEventJob failed for event {$eventId}: ".$e->getMessage(), [
                'event_id' => $eventId,
                'aggregate_id' => $event->aggregate_id ?? null,
                'attempts' => $event->attempts ?? 1,
                'exception' => $e,
            ]);

            $attempts = (int) ($event->attempts ?? 1);
            $isDeadLetter = ($attempts >= $this->tries);

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $isDeadLetter ? 'DEAD_LETTER' : 'PENDING',
                    'last_error' => substr($e->getMessage(), 0, 500),
                    'available_at' => $isDeadLetter ? now()->addHours(24) : now()->addSeconds(30 * $attempts),
                    'updated_at' => now(),
                ]);
        }
    }
}
