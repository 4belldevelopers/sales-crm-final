<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReplayMarketplaceOutboxCommand
 *
 * Intent:
 * Replays failed or dead-lettered outbox events from sys_outbox_events.
 * Resets status to PENDING with attempts=0 and clears last_error, allowing
 * Horizon workers to pick up and process them idempotently with monotonic version checking.
 *
 * Adheres to Section 18.1 of docs/SUPERADMIN_JOB_MARKETPLACE_REDESIGN_PLAN.md.
 */
class ReplayMarketplaceOutboxCommand extends Command
{
    protected $signature = 'outbox:replay 
                            {id? : Specific outbox event ID to replay}
                            {--type= : Filter by event_type or aggregate_type}
                            {--limit=50 : Maximum number of events to replay}';

    protected $description = 'Safely replays failed or dead-lettered events from sys_outbox_events';

    public function handle(): int
    {
        $id = $this->argument('id');
        $type = $this->option('type');
        $limit = (int) $this->option('limit');

        $query = DB::table('sys_outbox_events')
            ->whereIn('status', ['FAILED', 'FAILED_RETRYABLE', 'FAILED_DEAD_LETTER', 'DEAD_LETTER']);

        if ($id) {
            $query->where('id', $id);
        }

        if ($type) {
            $query->where(function ($q) use ($type) {
                $q->where('event_type', $type)
                    ->orWhere('aggregate_type', $type);
            });
        }

        $events = $query->limit($limit)->get();

        if ($events->isEmpty()) {
            $this->info('No dead-lettered or failed outbox events found matching criteria.');

            return 0;
        }

        $this->info("Found {$events->count()} dead-lettered event(s). Rescheduling for execution...");

        $affected = DB::table('sys_outbox_events')
            ->whereIn('id', $events->pluck('id'))
            ->update([
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => now(),
                'last_error' => null,
                'locked_at' => null,
            ]);

        $this->info("Successfully rescheduled {$affected} event(s) for immediate outbox processing.");

        return 0;
    }
}
