<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBenchCandidateOutboxJob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminBenchDlqCommand
 *
 * Intent: Operational Dead-Letter Queue (DLQ) recovery and management tool for Bench Candidate outbox events.
 * Identifies events that exceeded maximum retry attempts in sys_outbox_events, inspects error
 * payloads, and enables retrying them (by dispatching ProcessBenchCandidateOutboxJob) or purging them.
 *
 * Signature: php artisan superadmin:bench:dlq {--limit=50} {--event=} {--retry} {--purge} {--force} {--json}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminBenchDlqCommand extends Command
{
    protected $signature = 'superadmin:bench:dlq 
                            {--limit=50 : Maximum number of dead-letter events to process} 
                            {--event= : Specific outbox event ID to inspect/retry} 
                            {--retry : Reset and retry the dead-lettered events}
                            {--purge : Purge and permanently delete dead-lettered events}
                            {--force : Force operation without interactive prompt}
                            {--json : Output results as JSON}';

    protected $description = 'Inspect, recover, retry, or purge dead-lettered Bench Candidate outbox events';

    public function handle(): int
    {
        $specificId = $this->option('event');
        $limit = max((int) $this->option('limit'), 1);
        $shouldRetry = (bool) $this->option('retry');
        $shouldPurge = (bool) $this->option('purge');
        $force = (bool) $this->option('force');
        $asJson = (bool) $this->option('json');

        $query = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'BenchCandidate')
            ->where('status', 'DEAD_LETTER');

        if ($specificId) {
            $query->where('id', (int) $specificId);
        } else {
            $query->limit($limit);
        }

        $events = $query->get();

        if ($events->isEmpty()) {
            if ($asJson) {
                $this->output->writeln(json_encode(['status' => 'EMPTY', 'count' => 0, 'events' => []]));
            } else {
                $this->info('No dead-lettered Bench Candidate outbox events found in sys_outbox_events.');
            }

            return 0;
        }

        if ($asJson && ! $shouldRetry && ! $shouldPurge) {
            $this->output->writeln(json_encode([
                'status' => 'DEAD_LETTER_FOUND',
                'count' => $events->count(),
                'events' => $events->toArray(),
            ], JSON_PRETTY_PRINT));

            return 0;
        }

        if (! $asJson) {
            $this->warn("Found {$events->count()} dead-lettered Bench Candidate event(s) in sys_outbox_events.");

            $headers = ['ID', 'Event Type', 'Aggregate ID', 'Attempts', 'Last Error', 'Created At'];
            $rows = $events->map(function ($ev) {
                return [
                    $ev->id,
                    $ev->event_type,
                    $ev->aggregate_id,
                    $ev->attempts,
                    substr($ev->last_error ?? 'N/A', 0, 40).'...',
                    $ev->created_at,
                ];
            })->toArray();

            $this->table($headers, $rows);
        }

        // Action: Purge
        if ($shouldPurge) {
            if (! $force && ! $this->confirm("Are you sure you want to permanently DELETE these {$events->count()} dead-letter events?")) {
                $this->info('Purge aborted by operator.');

                return 0;
            }

            $deleted = DB::table('sys_outbox_events')
                ->whereIn('id', $events->pluck('id'))
                ->delete();

            if ($asJson) {
                $this->output->writeln(json_encode(['status' => 'PURGED', 'count' => $deleted]));
            } else {
                $this->info("Successfully purged {$deleted} dead-lettered Bench Candidate outbox event(s).");
            }

            return 0;
        }

        // Action: Retry
        if ($shouldRetry) {
            if (! $force && ! $this->confirm("Do you wish to reset and retry these {$events->count()} dead-letter events?")) {
                $this->info('Retry aborted by operator.');

                return 0;
            }

            $requeued = 0;
            foreach ($events as $ev) {
                DB::table('sys_outbox_events')
                    ->where('id', $ev->id)
                    ->update([
                        'status' => 'PENDING',
                        'attempts' => 0,
                        'available_at' => Carbon::now(),
                        'locked_at' => null,
                        'last_error' => '[DLQ RETRIED at '.Carbon::now()->toIso8601String().'] '.$ev->last_error,
                    ]);

                try {
                    ProcessBenchCandidateOutboxJob::dispatch($ev->id);
                    $requeued++;
                } catch (\Throwable $dispatchErr) {
                    $this->error("Failed to dispatch job for event #{$ev->id}: ".$dispatchErr->getMessage());
                }
            }

            if ($asJson) {
                $this->output->writeln(json_encode(['status' => 'REQUEUED', 'count' => $requeued]));
            } else {
                $this->info("Successfully recovered and re-dispatched {$requeued} dead-lettered Bench Candidate outbox event(s).");
            }

            return 0;
        }

        $this->comment('Use --retry to requeue these events, or --purge to remove them.');

        return 0;
    }
}
