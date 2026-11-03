<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCoinFeatureOutboxEventJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ProcessCoinFeatureOutboxCommand
 *
 * Intent: Reliable outbox dispatcher CLI for Coin Feature events (status updates, new features).
 * Processes pending records from sys_outbox_events for aggregate_type 'CoinFeature'.
 *
 * Signature: php artisan coin-features:process-outbox {--limit=50} {--retry-failed}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ProcessCoinFeatureOutboxCommand extends Command
{
    protected $signature = 'coin-features:process-outbox 
                            {--limit=50 : Maximum number of events to process in one pass}
                            {--retry-failed : Include failed events that have passed their backoff window}';

    protected $description = 'Process pending and retryable Coin Feature outbox events from sys_outbox_events';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');

        $statuses = $retryFailed ? ['PENDING', 'FAILED'] : ['PENDING'];

        $query = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CoinFeature')
            ->whereIn('status', $statuses)
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit);

        $events = $query->pluck('id')->toArray();

        if (empty($events)) {
            $this->line('No eligible Coin Feature outbox events to process.');

            return self::SUCCESS;
        }

        $this->info('Found '.count($events).' Coin Feature outbox event(s). Processing...');

        $processed = 0;
        foreach ($events as $eventId) {
            try {
                dispatch_sync(new ProcessCoinFeatureOutboxEventJob((int) $eventId));
                $processed++;
            } catch (\Throwable $e) {
                $this->error("Failed to process outbox event #{$eventId}: ".$e->getMessage());
            }
        }

        $this->info("Successfully processed {$processed} outbox event(s).");

        return self::SUCCESS;
    }
}
