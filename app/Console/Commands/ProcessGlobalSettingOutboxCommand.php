<?php

namespace App\Console\Commands;

use App\Jobs\ProcessGlobalSettingOutboxEventJob;
use App\Services\ElasticService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ProcessGlobalSettingOutboxCommand
 *
 * Intent: CLI and scheduled dispatcher for transactional outbox events of the
 * Super Admin Global Settings subsystem.
 *
 * Signature: php artisan global-settings:process-outbox {--limit=50} {--retry-failed}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessGlobalSettingOutboxCommand extends Command
{
    protected $signature = 'global-settings:process-outbox {--limit=50} {--retry-failed}';

    protected $description = 'Process pending transactional outbox events for Super Admin Global Settings';

    public function handle(ElasticService $elasticService): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');

        if ($retryFailed) {
            $resetCount = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'GlobalSetting')
                ->where('status', 'FAILED')
                ->update([
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                    'last_error' => null,
                ]);

            $this->info("Reset {$resetCount} FAILED outbox events to PENDING status.");
        }

        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'GlobalSetting')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        if ($events->isEmpty()) {
            $this->info('No pending Global Setting outbox events to process.');

            return Command::SUCCESS;
        }

        $this->info("Processing {$events->count()} Global Setting outbox events...");

        $job = new ProcessGlobalSettingOutboxEventJob;
        foreach ($events as $event) {
            $this->line("Processing event ID: {$event->id} (Type: {$event->event_type})...");
            $singleJob = new ProcessGlobalSettingOutboxEventJob((int) $event->id);
            $singleJob->handle($elasticService);
        }

        $this->info('Global Setting outbox processing complete.');

        return Command::SUCCESS;
    }
}
