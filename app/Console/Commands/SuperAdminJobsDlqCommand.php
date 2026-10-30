<?php

namespace App\Console\Commands;

use App\Jobs\ProcessJobOutboxEventJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminJobsDlqCommand
 *
 * Intent: Dead-Letter Queue (DLQ) inspection and recovery CLI for the Super Admin Jobs Outbox.
 * Allows site administrators and automated operational monitors to inspect poisoned or repeatedly
 * failed outbox events, retry them safely with a reset attempt counter, or purge obsolete records.
 *
 * Usage:
 *   php artisan superadmin:jobs:dlq              (Inspect dead-letter events)
 *   php artisan superadmin:jobs:dlq --retry      (Requeue dead-letter events for execution)
 *   php artisan superadmin:jobs:dlq --purge      (Purge dead-letter events)
 *   php artisan superadmin:jobs:dlq --id=123     (Inspect or retry single event)
 *
 * Tenancy: Operates across sys_outbox_events scoped to aggregate_type = 'job'.
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminJobsDlqCommand extends Command
{
    protected $signature = 'superadmin:jobs:dlq 
                            {--retry : Requeue dead-lettered events to PENDING and trigger processing}
                            {--purge : Permanently delete dead-lettered events}
                            {--id= : Target a specific outbox event ID}
                            {--limit=50 : Maximum number of events to inspect or process}
                            {--json : Output results as JSON}';

    protected $description = 'Inspect, retry, or purge Dead-Letter Queue (DLQ) events for Super Admin Jobs';

    public function handle(): int
    {
        $id = $this->option('id');
        $limit = max((int) $this->option('limit'), 1);
        $retry = (bool) $this->option('retry');
        $purge = (bool) $this->option('purge');
        $isJson = (bool) $this->option('json');

        $query = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'job')
            ->where('status', 'DEAD_LETTER');

        if ($id) {
            $query->where('id', (int) $id);
        }

        $deadLetterEvents = $query->orderBy('id', 'desc')->limit($limit)->get();

        if ($deadLetterEvents->isEmpty()) {
            if ($isJson) {
                $this->line(json_encode(['status' => 'EMPTY', 'count' => 0, 'events' => []]));
            } else {
                $this->info('Dead-Letter Queue is empty. No dead-lettered Super Admin Job events found.');
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
                $this->warn("Purged {$deleted} dead-lettered event(s) from sys_outbox_events.");
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
                ProcessJobOutboxEventJob::dispatch((int) $event->id);
                $retriedCount++;
            }

            if ($isJson) {
                $this->line(json_encode(['status' => 'RETRIED', 'count' => $retriedCount]));
            } else {
                $this->info("Successfully reset and dispatched {$retriedCount} dead-lettered event(s) for retry.");
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
        $this->warn(" Found {$deadLetterEvents->count()} Dead-Lettered Super Admin Job Event(s)");
        $this->warn('================================================================================');

        $tableData = $deadLetterEvents->map(fn ($e) => [
            $e->id,
            $e->aggregate_id,
            $e->aggregate_version,
            $e->attempts,
            mb_substr((string) $e->last_error, 0, 45).'...',
            $e->created_at,
        ]);

        $this->table(['ID', 'Job ID', 'Version', 'Attempts', 'Last Error', 'Created At'], $tableData);
        $this->line('Run with <info>--retry</info> to reset and re-process, or <comment>--purge</comment> to delete permanently.');

        return 0;
    }
}
