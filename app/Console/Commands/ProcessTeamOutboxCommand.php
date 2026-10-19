<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTeamOutboxEventJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessTeamOutboxCommand
 *
 * Intent: Reliable outbox dispatcher for Team Member events (invitations, role updates, status updates).
 * Bounded batch processing, worker crash recovery (>5 min lock expiry), and safe concurrent claim.
 *
 * Scoping: Scoped to sys_outbox_events table for aggregate_type 'TeamMember'.
 *
 * Author: Antigravity AI
 * Date: October 19, 2026
 */
class ProcessTeamOutboxCommand extends Command
{
    protected $signature = 'team:process-outbox 
                            {--limit=25 : Maximum number of events to claim per batch} 
                            {--daemon : Run continuously as a background daemon} 
                            {--sleep=3 : Sleep seconds when no pending events exist}';

    protected $description = 'Process pending and retryable Team Member outbox events from sys_outbox_events';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $daemon = (bool) $this->option('daemon');
        $sleep = (int) $this->option('sleep');

        $this->info('Starting Team Member Outbox Dispatcher (PID: '.getmypid().')...');

        do {
            $events = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'TeamMember')
                ->where(function ($q) {
                    $q->whereIn('status', ['PENDING', 'FAILED'])
                        ->orWhere(function ($sub) {
                            $sub->where('status', 'PROCESSING')
                                ->where('locked_at', '<', now()->subMinutes(5));
                        });
                })
                ->where('available_at', '<=', now())
                ->where('attempts', '<', 3)
                ->orderBy('id', 'asc')
                ->limit($limit)
                ->pluck('id')
                ->toArray();

            if (empty($events)) {
                if ($daemon) {
                    sleep($sleep);

                    continue;
                }
                $this->info('No eligible Team Member outbox events to process.');
                break;
            }

            $this->info('Found '.count($events).' Team Member outbox events. Processing...');

            foreach ($events as $eventId) {
                try {
                    $job = new ProcessTeamOutboxEventJob((int) $eventId);
                    $job->handle();
                    $this->line(" [OK] Event #{$eventId} processed successfully.");
                } catch (\Throwable $e) {
                    $this->error(" [FAIL] Event #{$eventId} failed: ".$e->getMessage());
                    Log::error("ProcessTeamOutboxCommand failed for event #{$eventId}: ".$e->getMessage());
                }
            }

        } while ($daemon);

        return Command::SUCCESS;
    }
}
