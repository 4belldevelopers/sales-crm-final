<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminMeetingsOutboxPruneCommand
 *
 * Intent: Safely prunes completed outbox events and expired idempotency keys
 * for the Super Admin Meeting Tracker subsystem.
 *
 * Signature: php artisan superadmin:meetings:outbox-prune {--days=14} {--dry-run}
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminMeetingsOutboxPruneCommand extends Command
{
    protected $signature = 'superadmin:meetings:outbox-prune {--days=14 : Retention period in days for completed outbox records} {--dry-run : Only count records to prune without deleting}';

    protected $description = 'Prune old completed outbox events and expired idempotency keys for meetings';

    public function handle(): int
    {
        $days = (int) $this->option('days');
        $dryRun = (bool) $this->option('dry-run');
        $cutoff = now()->subDays($days);

        $this->info("Scanning completed meeting outbox records older than {$days} days (cutoff: {$cutoff})...");

        // Completed events
        $completedEventsQuery = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'MeetingSchedule')
            ->where('status', 'COMPLETED')
            ->where('created_at', '<', $cutoff);

        $eventsCount = $completedEventsQuery->count();

        // Expired idempotency keys
        $expiredKeysQuery = DB::table('sys_idempotency_keys')
            ->where('tenant_id', 0)
            ->where('idempotency_key', 'LIKE', 'sa_meet_%')
            ->where('expires_at', '<', now());

        $keysCount = $expiredKeysQuery->count();

        $this->line("Completed outbox events eligible: {$eventsCount}");
        $this->line("Expired idempotency keys eligible: {$keysCount}");

        if ($dryRun) {
            $this->comment('Dry-run complete. No rows deleted.');

            return Command::SUCCESS;
        }

        if ($eventsCount > 0) {
            $eventIds = $completedEventsQuery->pluck('id')->toArray();
            DB::table('sys_outbox_deliveries')->whereIn('outbox_id', $eventIds)->delete();
            $completedEventsQuery->delete();
            $this->info("Successfully pruned {$eventsCount} completed meeting outbox records.");
        }

        if ($keysCount > 0) {
            $expiredKeysQuery->delete();
            $this->info("Successfully pruned {$keysCount} expired idempotency keys.");
        }

        return Command::SUCCESS;
    }
}
