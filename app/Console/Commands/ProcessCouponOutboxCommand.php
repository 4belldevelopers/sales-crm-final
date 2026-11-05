<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCouponOutboxEventJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ProcessCouponOutboxCommand
 *
 * Intent: CLI and scheduled dispatcher to process pending or retry failed
 * outbox events for the Super Admin Coupons subsystem.
 *
 * Signature: php artisan coupons:process-outbox {--limit=50} {--retry-failed}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessCouponOutboxCommand extends Command
{
    protected $signature = 'coupons:process-outbox {--limit=50} {--retry-failed}';

    protected $description = 'Process pending or retry failed outbox events for the Coupons subsystem';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');

        $query = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Coupon');

        if ($retryFailed) {
            $query->whereIn('status', ['FAILED', 'DEAD_LETTER']);
            $this->info('Resetting failed/dead-letter coupon outbox events to PENDING...');
            $affected = $query->update([
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => now(),
                'locked_at' => null,
            ]);
            $this->info("Reset {$affected} event(s) to PENDING.");
        }

        $pendingEvents = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Coupon')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->pluck('id');

        $count = $pendingEvents->count();
        $this->info("Found {$count} pending coupon outbox event(s) to dispatch.");

        foreach ($pendingEvents as $eventId) {
            ProcessCouponOutboxEventJob::dispatchSync((int) $eventId);
            $this->line("Processed outbox event #{$eventId}");
        }

        $this->info('Coupons outbox processing finished successfully.');

        return self::SUCCESS;
    }
}
