<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCandidateOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminCandidatesDlqRetryCommand
 *
 * Intent: Operational Dead-Letter Queue (DLQ) recovery tool for Candidate outbox events.
 * Identifies events that exceeded maximum retry attempts in sys_outbox_events, inspects error
 * payloads, and resets their status to PENDING with exponential backoff zeroed out so that
 * ProcessCandidateOutboxEventJob can re-attempt independent projections.
 *
 * Signature: php artisan superadmin:candidates:dlq-retry {--limit=50} {--event=} {--force}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminCandidatesDlqRetryCommand extends Command
{
    protected $signature = 'superadmin:candidates:dlq-retry 
                            {--limit=50 : Maximum number of dead-letter events to process} 
                            {--event= : Specific outbox event ID to retry} 
                            {--force : Force retry without confirmation}';

    protected $description = 'Recover and retry dead-lettered Candidate outbox events from sys_outbox_events';

    public function handle(): int
    {
        $specificId = $this->option('event');
        $limit = max((int) $this->option('limit'), 1);

        $query = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Candidate')
            ->where('status', 'DEAD_LETTER');

        if ($specificId) {
            $query->where('id', (int) $specificId);
        } else {
            $query->limit($limit);
        }

        $events = $query->get();

        if ($events->isEmpty()) {
            $this->info('No dead-lettered Candidate outbox events found in sys_outbox_events.');

            return 0;
        }

        $this->warn("Found {$events->count()} dead-lettered event(s) in sys_outbox_events.");

        $headers = ['ID', 'Aggregate ID', 'Attempts', 'Last Error', 'Created At'];
        $rows = $events->map(function ($ev) {
            return [
                $ev->id,
                $ev->aggregate_id,
                $ev->attempts,
                substr($ev->last_error ?? 'N/A', 0, 40).'...',
                $ev->created_at,
            ];
        })->toArray();

        $this->table($headers, $rows);

        if (! $this->option('force') && ! $this->confirm('Do you wish to reset and retry these dead-letter events?')) {
            $this->info('Aborted by operator.');

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
                ProcessCandidateOutboxEventJob::dispatch($ev->id);
                $requeued++;
            } catch (\Throwable $dispatchErr) {
                $this->error("Failed to dispatch job for event #{$ev->id}: ".$dispatchErr->getMessage());
            }
        }

        $this->info("Successfully recovered and re-dispatched {$requeued} dead-lettered Candidate outbox event(s).");

        return 0;
    }
}
