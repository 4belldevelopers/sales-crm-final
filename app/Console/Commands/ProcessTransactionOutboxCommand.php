<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTransactionOutboxEventJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ProcessTransactionOutboxCommand
 *
 * Intent: Reliable outbox dispatcher CLI for Transaction History & Invoicing events.
 * Claims and processes pending records from sys_outbox_events for aggregate_type 'CartTransaction',
 * updates Elasticsearch local_cart_transaction_history, and flushes L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan transactions:process-outbox {--limit=50} {--retry-failed}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ProcessTransactionOutboxCommand extends Command
{
    protected $signature = 'transactions:process-outbox 
                            {--limit=50 : Maximum number of events to process in one pass}
                            {--retry-failed : Include failed events that have passed their backoff window}';

    protected $description = 'Process pending and retryable Cart Transaction outbox events from sys_outbox_events';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');

        $statuses = $retryFailed ? ['PENDING', 'FAILED'] : ['PENDING'];

        $query = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CartTransaction')
            ->whereIn('status', $statuses)
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit);

        $events = $query->pluck('id')->toArray();

        if (empty($events)) {
            $this->line('No eligible Cart Transaction outbox events to process.');

            return self::SUCCESS;
        }

        $this->info('Found '.count($events).' Cart Transaction outbox event(s). Processing...');

        $processed = 0;
        foreach ($events as $eventId) {
            try {
                dispatch_sync(new ProcessTransactionOutboxEventJob((int) $eventId));
                $processed++;
            } catch (\Throwable $e) {
                $this->error("Failed to process outbox event #{$eventId}: ".$e->getMessage());
            }
        }

        $this->info("Completed processing {$processed} outbox event(s).");

        return self::SUCCESS;
    }
}
