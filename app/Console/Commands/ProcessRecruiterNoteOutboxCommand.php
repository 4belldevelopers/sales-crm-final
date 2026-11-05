<?php

namespace App\Console\Commands;

use App\Jobs\ProcessRecruiterNoteOutboxEventJob;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ProcessRecruiterNoteOutboxCommand
 *
 * Intent: Scheduled worker and CLI command to sweep pending and retryable outbox events
 * for Recruiter Note Templates (aggregate_type = 'RecruiterNoteTemplate').
 *
 * Signature: php artisan recruiter-notes:process-outbox {--limit=50} {--retry-failed}
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessRecruiterNoteOutboxCommand extends Command
{
    protected $signature = 'recruiter-notes:process-outbox {--limit=50} {--retry-failed : Requeue failed events}';

    protected $description = 'Process pending and retryable outbox events for Recruiter Note Templates';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');

        if ($retryFailed) {
            $resetCount = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'RecruiterNoteTemplate')
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

        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'RecruiterNoteTemplate')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->pluck('id')
            ->toArray();

        $count = count($events);
        if ($count === 0) {
            $this->line('No pending recruiter note outbox events found.');

            return Command::SUCCESS;
        }

        $this->info("Processing {$count} outbox events...");

        foreach ($events as $eventId) {
            try {
                $job = new ProcessRecruiterNoteOutboxEventJob((int) $eventId);
                $job->handle();
                $this->line("Processed outbox event #{$eventId}");
            } catch (\Throwable $e) {
                $this->error("Failed processing event #{$eventId}: {$e->getMessage()}");
            }
        }

        $this->info('Completed recruiter note outbox processing.');

        return Command::SUCCESS;
    }
}
