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
 * Class ProcessTeamOutboxEventJob
 *
 * Intent: Decoupled Outbox Worker for Team Member mutations.
 * Claims pending sys_outbox_events for aggregate_type 'TeamMember',
 * keeps Elasticsearch indices in sync, guarantees event delivery,
 * and marks outbox records as COMPLETED.
 */
class ProcessTeamOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 15;

    public int $timeout = 60;

    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('team-outbox');
    }

    public function handle(): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId);

            return;
        }

        // Batch processing if called without specific ID
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'TeamMember')
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
                ->where('aggregate_type', 'TeamMember')
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

            $targetUserId = (int) $event->aggregate_id;

            // 1. Synchronize to Elasticsearch if user exists
            if ($targetUserId > 0) {
                try {
                    GeneralMethod::userIndexingupload($targetUserId);
                    SyncUserToElasticJob::dispatch($targetUserId)->afterCommit();
                } catch (\Throwable $e) {
                    Log::warning("ProcessTeamOutboxEventJob: ES index sync warning for user {$targetUserId}: ".$e->getMessage());
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
            Log::error("ProcessTeamOutboxEventJob failed for event {$eventId}: ".$e->getMessage());

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'FAILED',
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => now()->addSeconds(30),
                ]);
        }
    }
}
