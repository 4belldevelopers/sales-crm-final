<?php

namespace App\Console\Commands;

use App\Jobs\ProcessEmailThemeOutboxEventJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ProcessEmailThemeOutboxCommand
 *
 * Intent: Reliable outbox dispatcher CLI for Email Theme events (created, updated, toggled, bulk, deleted).
 * Processes pending records from sys_outbox_events for aggregate_type 'EmailTheme'.
 *
 * Signature: php artisan email-themes:process-outbox {--limit=50} {--retry-failed}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ProcessEmailThemeOutboxCommand extends Command
{
    protected $signature = 'email-themes:process-outbox 
                            {--limit=50 : Maximum number of events to process in one pass}
                            {--retry-failed : Include failed events that have passed their backoff window}';

    protected $description = 'Process pending and retryable Email Theme outbox events from sys_outbox_events';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');

        $statuses = $retryFailed ? ['PENDING', 'FAILED'] : ['PENDING'];

        $query = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'EmailTheme')
            ->whereIn('status', $statuses)
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit);

        $events = $query->pluck('id')->toArray();

        if (empty($events)) {
            $this->line('No eligible Email Theme outbox events to process.');

            return self::SUCCESS;
        }

        $this->info('Found '.count($events).' Email Theme outbox event(s). Processing...');

        $processed = 0;
        foreach ($events as $eventId) {
            try {
                dispatch_sync(new ProcessEmailThemeOutboxEventJob((int) $eventId));
                $processed++;
            } catch (\Throwable $e) {
                $this->error("Failed to process outbox event #{$eventId}: ".$e->getMessage());
            }
        }

        $this->info("Completed processing {$processed} outbox event(s).");

        return self::SUCCESS;
    }
}
