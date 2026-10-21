<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCustomPlanRequestOutboxEventJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessCustomPlanRequestOutboxCommand
 *
 * Intent: Reliable outbox dispatcher for Custom Plan Request events (status updates, new submissions).
 * Scoped to sys_outbox_events table for aggregate_type 'CustomPlanRequest'.
 *
 * Author: Antigravity AI
 * Date: October 21, 2026
 */
class ProcessCustomPlanRequestOutboxCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'custom-plans:process-outbox 
                            {--limit=50 : Maximum number of events to process in one pass}
                            {--retry-failed : Include failed events that have passed their backoff window}';

    /**
     * The console command description.
     */
    protected $description = 'Process pending and retryable Custom Plan Request outbox events from sys_outbox_events';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');

        $statuses = $retryFailed ? ['PENDING', 'FAILED'] : ['PENDING'];

        $query = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CustomPlanRequest')
            ->whereIn('status', $statuses)
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit);

        $events = $query->pluck('id')->toArray();

        if (empty($events)) {
            $this->line('No eligible Custom Plan Request outbox events to process.');

            return self::SUCCESS;
        }

        $this->info('Found '.count($events).' Custom Plan Request outbox event(s). Processing...');

        $processed = 0;
        foreach ($events as $eventId) {
            try {
                $job = new ProcessCustomPlanRequestOutboxEventJob((int) $eventId);
                $job->handle();
                $processed++;
            } catch (\Throwable $e) {
                Log::error("[CPR_OUTBOX_CMD] Failed for event #{$eventId}: ".$e->getMessage());
                $this->error("Failed event #{$eventId}: ".$e->getMessage());
            }
        }

        $this->info("Successfully processed {$processed} outbox event(s).");

        return self::SUCCESS;
    }
}
