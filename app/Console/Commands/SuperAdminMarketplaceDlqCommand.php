<?php

namespace App\Console\Commands;

use App\Jobs\ProcessMarketJobOutboxEventJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminMarketplaceDlqCommand
 *
 * Intent: Dead-Letter Queue (DLQ) inspection, recovery, and purge CLI for the Super Admin
 * Marketplace Transactional Outbox (sys_outbox_events). Allows system administrators
 * and automated SRE probes to inspect poisoned or repeatedly failed marketplace outbox events,
 * retry them safely with a reset attempt counter, or permanently purge obsolete records.
 *
 * Usage:
 *   php artisan superadmin:marketplace:dlq              (Inspect dead-letter events)
 *   php artisan superadmin:marketplace:dlq --retry      (Requeue dead-letter events for execution)
 *   php artisan superadmin:marketplace:dlq --purge      (Purge dead-letter events)
 *   php artisan superadmin:marketplace:dlq --id=123     (Inspect or retry single event)
 *
 * Tenancy: Operates across sys_outbox_events scoped to aggregate_type = 'MarketJob'.
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminMarketplaceDlqCommand extends Command
{
    protected $signature = 'superadmin:marketplace:dlq 
                            {--retry : Requeue dead-lettered events to PENDING and trigger processing}
                            {--purge : Permanently delete dead-lettered events}
                            {--id= : Target a specific outbox event ID}
                            {--limit=50 : Maximum number of events to inspect or process}
                            {--json : Output results as JSON}';

    protected $description = 'Inspect, retry, or purge Dead-Letter Queue (DLQ) events for Super Admin Marketplace';

    public function handle(): int
    {
        $id = $this->option('id');
        $limit = max((int) $this->option('limit'), 1);
        $retry = (bool) $this->option('retry');
        $purge = (bool) $this->option('purge');
        $isJson = (bool) $this->option('json');

        $query = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'MarketJob')
            ->where('status', 'DEAD_LETTER');

        if ($id) {
            $query->where('id', (int) $id);
        }

        $deadLetterEvents = $query->orderBy('id', 'desc')->limit($limit)->get();

        if ($deadLetterEvents->isEmpty()) {
            if ($isJson) {
                $this->line(json_encode(['status' => 'EMPTY', 'count' => 0, 'events' => []]));
            } else {
                $this->info('Dead-Letter Queue is empty. No dead-lettered Super Admin Marketplace events found.');
            }

            return 0;
        }

        // Action: PURGE
        if ($purge) {
            $ids = $deadLetterEvents->pluck('id')->toArray();
            $deleted = DB::table('sys_outbox_events')->whereIn('id', $ids)->delete();

            if ($isJson) {
                $this->line(json_encode(['status' => 'PURGED', 'deleted_count' => $deleted, 'ids' => $ids]));
            } else {
                $this->warn("Purged {$deleted} dead-lettered marketplace event(s) from sys_outbox_events.");
            }

            return 0;
        }

        // Action: RETRY
        if ($retry) {
            $retriedCount = 0;
            foreach ($deadLetterEvents as $event) {
                DB::table('sys_outbox_events')
                    ->where('id', $event->id)
                    ->update([
                        'status' => 'PENDING',
                        'attempts' => 0,
                        'available_at' => now(),
                        'locked_at' => null,
                        'last_error' => null,
                    ]);

                // Dispatch worker immediately for retry
                ProcessMarketJobOutboxEventJob::dispatch((int) $event->id);
                $retriedCount++;
            }

            if ($isJson) {
                $this->line(json_encode(['status' => 'RETRIED', 'count' => $retriedCount]));
            } else {
                $this->info("Successfully reset and dispatched {$retriedCount} dead-lettered marketplace event(s) for retry.");
            }

            return 0;
        }

        // Default: INSPECT / LIST
        if ($isJson) {
            $this->line(json_encode([
                'status' => 'DLQ_ACTIVE',
                'count' => $deadLetterEvents->count(),
                'events' => $deadLetterEvents->map(fn ($e) => [
                    'id' => $e->id,
                    'tenant_id' => $e->tenant_id,
                    'aggregate_id' => $e->aggregate_id,
                    'aggregate_version' => $e->aggregate_version,
                    'event_type' => $e->event_type,
                    'attempts' => $e->attempts,
                    'last_error' => $e->last_error,
                    'created_at' => $e->created_at,
                ]),
            ], JSON_PRETTY_PRINT));

            return 0;
        }

        $this->warn('================================================================================');
        $this->warn(" Found {$deadLetterEvents->count()} Dead-Lettered Super Admin Marketplace Event(s)");
        $this->warn('================================================================================');

        $tableData = $deadLetterEvents->map(fn ($e) => [
            $e->id,
            $e->aggregate_id,
            $e->aggregate_version,
            $e->event_type,
            $e->attempts,
            mb_substr((string) $e->last_error, 0, 40).'...',
            $e->created_at,
        ]);

        $this->table(['ID', 'Job ID', 'Version', 'Event Type', 'Attempts', 'Last Error', 'Created At'], $tableData);
        $this->line('Run with <info>--retry</info> to reset and re-process, or <comment>--purge</comment> to delete permanently.');

        return 0;
    }
}
