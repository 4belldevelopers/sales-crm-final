<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBenchRmNoteOutboxEventJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ProcessBenchRmNoteOutboxCommand
 *
 * Intent: Scheduled worker and CLI command to sweep pending and retryable outbox events
 * for Relationship Manager Note Templates (aggregate_type = 'BenchRmNoteTemplate').
 *
 * Signature: php artisan bench-rm-notes:process-outbox {--limit=50} {--retry-failed} {--replay-dead-letter} {--recover-leases}
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessBenchRmNoteOutboxCommand extends Command
{
    protected $signature = 'bench-rm-notes:process-outbox {--limit=50} {--retry-failed : Requeue failed events} {--replay-dead-letter : Requeue dead-letter events} {--recover-leases : Force recover abandoned processing leases}';

    protected $description = 'Process pending and retryable outbox events for Relationship Manager Note Templates';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');
        $replayDeadLetter = (bool) $this->option('replay-dead-letter');
        $recoverLeases = (bool) $this->option('recover-leases');

        // Automatically recover abandoned PROCESSING leases older than 300 seconds
        $abandonedThreshold = now()->subSeconds(300);
        $recoveredLeases = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'BenchRmNoteTemplate')
            ->where('status', 'PROCESSING')
            ->where('locked_at', '<', $abandonedThreshold)
            ->update([
                'status' => 'PENDING',
                'available_at' => now(),
            ]);

        if ($recoveredLeases > 0) {
            $this->info("Recovered {$recoveredLeases} abandoned PROCESSING outbox lease(s).");
        }

        if ($retryFailed) {
            $resetCount = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'BenchRmNoteTemplate')
                ->where('status', 'FAILED')
                ->update([
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'last_error' => null,
                ]);

            if ($resetCount > 0) {
                $this->info("Re-queued {$resetCount} previously failed events.");
            }
        }

        if ($replayDeadLetter) {
            $deadLetterCount = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'BenchRmNoteTemplate')
                ->where('status', 'DEAD_LETTER')
                ->update([
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'last_error' => null,
                ]);

            if ($deadLetterCount > 0) {
                $this->info("Replayed {$deadLetterCount} DEAD_LETTER outbox event(s).");
            }
        }

        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'BenchRmNoteTemplate')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->pluck('id')
            ->toArray();

        $count = count($events);
        if ($count === 0) {
            $this->line('No pending Relationship Manager note outbox events found.');

            return Command::SUCCESS;
        }

        $this->info("Processing {$count} outbox events...");

        foreach ($events as $eventId) {
            try {
                $job = new ProcessBenchRmNoteOutboxEventJob((int) $eventId);
                $job->handle();
                $this->line("Processed outbox event #{$eventId}");
            } catch (\Throwable $e) {
                $this->error("Failed processing event #{$eventId}: {$e->getMessage()}");
            }
        }

        $this->info('Completed Relationship Manager note outbox processing.');

        return Command::SUCCESS;
    }
}
