<?php

namespace App\Console\Commands;

use App\Jobs\ProcessCompanyOutboxEventJob;
use App\Services\ElasticService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ProcessCompanyOutboxCommand
 *
 * Intent: CLI and scheduled dispatcher for transactional outbox events of the
 * Super Admin Company subsystem.
 *
 * Signature: php artisan company:process-outbox {--limit=50} {--retry-failed}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessCompanyOutboxCommand extends Command
{
    protected $signature = 'company:process-outbox 
                            {--limit=50 : Maximum number of events to process} 
                            {--retry-failed : Reset FAILED outbox events to PENDING}
                            {--retry-dead-letter : Reset DEAD_LETTER outbox events to PENDING}';

    protected $description = 'Process pending transactional outbox events for Super Admin Companies';

    public function handle(ElasticService $elasticService): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');
        $retryDeadLetter = (bool) $this->option('retry-dead-letter');

        if ($retryFailed) {
            $resetCount = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'Company')
                ->where('status', 'FAILED')
                ->update([
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'last_error' => null,
                ]);

            $this->info("Reset {$resetCount} FAILED outbox events to PENDING status.");
        }

        if ($retryDeadLetter) {
            $resetDeadCount = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'Company')
                ->where('status', 'DEAD_LETTER')
                ->update([
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'last_error' => null,
                ]);

            $this->warn("Reset {$resetDeadCount} DEAD_LETTER outbox events to PENDING status.");
        }

        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'Company')
            ->where(function ($q) {
                $q->where('status', 'PENDING')
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'PROCESSING')
                            ->where('locked_at', '<', now()->subMinutes(5));
                    });
            })
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        if ($events->isEmpty()) {
            $this->info('No pending Company outbox events to process.');

            return Command::SUCCESS;
        }

        $this->info("Processing {$events->count()} Company outbox events...");

        foreach ($events as $event) {
            $this->line("Processing event ID: {$event->id} (Type: {$event->event_type})...");
            $singleJob = new ProcessCompanyOutboxEventJob((int) $event->id);
            $singleJob->handle($elasticService);
        }

        $this->info('Company outbox processing complete.');

        return Command::SUCCESS;
    }
}
