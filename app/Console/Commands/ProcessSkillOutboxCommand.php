<?php

namespace App\Console\Commands;

use App\Jobs\ProcessSkillOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ProcessSkillOutboxCommand
 *
 * Intent: Scheduled and CLI dispatcher for pending and retryable Transactional Outbox
 * events belonging to the Super Admin Skills subsystem.
 *
 * Signature: php artisan skills:process-outbox {--limit=50} {--retry-failed}
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessSkillOutboxCommand extends Command
{
    protected $signature = 'skills:process-outbox 
                            {--limit=50 : Maximum number of events to process in this run}
                            {--retry-failed : Re-dispatch failed retryable events}';

    protected $description = 'Process pending or retryable outbox events for Super Admin skills';

    public function handle(): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $retryFailed = (bool) $this->option('retry-failed');

        $query = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Skill');

        if ($retryFailed) {
            $query->whereIn('status', ['PENDING', 'FAILED'])
                ->where('attempts', '<', 5);
        } else {
            $query->where('status', 'PENDING')
                ->where('available_at', '<=', Carbon::now());
        }

        $events = $query->orderBy('id', 'asc')
            ->limit($limit)
            ->pluck('id')
            ->toArray();

        $count = count($events);
        if ($count === 0) {
            $this->info('No pending Skill outbox events to process.');

            return Command::SUCCESS;
        }

        $this->info("Dispatching {$count} Skill outbox events...");

        foreach ($events as $eventId) {
            ProcessSkillOutboxEventJob::dispatchSync((int) $eventId);
            $this->line("Processed Outbox Event #{$eventId}");
        }

        $this->info('Skill outbox processing complete.');

        return Command::SUCCESS;
    }
}
