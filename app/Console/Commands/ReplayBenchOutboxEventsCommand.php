<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReplayBenchOutboxEventsCommand
 *
 * Intent: Replays dead-lettered outbox events back into pending state with audit trail.
 * Scope: Service Company bench candidate outbox queue.
 */
class ReplayBenchOutboxEventsCommand extends Command
{
    protected $signature = 'bench:outbox-replay 
                            {--event_id= : Specific event ID to replay}
                            {--company_id= : Replay all DLQ events for tenant}
                            {--reason= : Operational reason for replay}';

    protected $description = 'Replays dead-lettered outbox events back into pending state with audit trail';

    public function handle(): int
    {
        $eventId = $this->option('event_id');
        $companyId = $this->option('company_id');
        $reason = $this->option('reason') ?: 'Manual administrative replay';

        $query = DB::table('candidate_outbox_events')
            ->where('aggregate_type', 'bench_candidate')
            ->where('status', 'failed');

        if ($eventId) {
            $query->where('id', $eventId);
        } elseif ($companyId) {
            $query->where('company_id', $companyId);
        }

        $events = $query->get();
        if ($events->isEmpty()) {
            $this->info('No failed/dead-lettered events matched query.');
            return 0;
        }

        $this->info('Replaying ' . $events->count() . ' dead-lettered events...');

        DB::table('candidate_outbox_events')
            ->whereIn('id', $events->pluck('id'))
            ->update([
                'status' => 'pending',
                'retry_count' => 0,
                'available_at' => now(),
                'replayed_by' => 'admin_cli',
                'replayed_at' => now(),
                'replay_reason' => $reason,
                'locked_at' => null,
                'locked_by' => null,
                'lock_token' => null,
                'updated_at' => now(),
            ]);

        $this->info('Events returned to pending. Horizon worker will process immediately.');
        return 0;
    }
}
