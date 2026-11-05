<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTopUpCoinPackageOutboxEventJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ProcessTopUpCoinPackageOutboxCommand
 *
 * Intent: Reliable outbox dispatcher CLI for Top-Up Coin Package events (mutations, toggles, deletions).
 * Processes pending records from sys_outbox_events for aggregate_type 'TopUpCoinPackage'.
 *
 * Signature: php artisan topup-coin-packages:process-outbox {--limit=50} {--retry-failed}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessTopUpCoinPackageOutboxCommand extends Command
{
    protected $signature = 'topup-coin-packages:process-outbox 
                            {--limit=50 : Maximum number of events to process in one pass}
                            {--retry-failed : Include failed events that have passed their backoff window}';

    protected $description = 'Process pending and retryable Top-Up Coin Package outbox events from sys_outbox_events';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');

        $statuses = $retryFailed ? ['PENDING', 'FAILED'] : ['PENDING'];

        $query = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'TopUpCoinPackage')
            ->whereIn('status', $statuses)
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit);

        $events = $query->pluck('id')->toArray();

        if (empty($events)) {
            $this->line('No eligible Top-Up Coin Package outbox events to process.');

            return self::SUCCESS;
        }

        $this->info('Found '.count($events).' Top-Up Coin Package outbox event(s). Processing...');

        $processed = 0;
        foreach ($events as $eventId) {
            try {
                dispatch_sync(new ProcessTopUpCoinPackageOutboxEventJob((int) $eventId));
                $processed++;
            } catch (\Throwable $e) {
                $this->error("Failed to process outbox event #{$eventId}: ".$e->getMessage());
            }
        }

        $this->info("Completed processing {$processed} outbox event(s).");

        return self::SUCCESS;
    }
}
