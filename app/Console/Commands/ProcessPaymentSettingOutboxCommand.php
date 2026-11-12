<?php

namespace App\Console\Commands;

use App\Jobs\ProcessPaymentSettingOutboxEventJob;
use App\Services\ElasticService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ProcessPaymentSettingOutboxCommand
 *
 * Intent: CLI and scheduled dispatcher for transactional outbox events of the
 * Super Admin Payment Settings subsystem.
 *
 * Signature: php artisan payment-settings:process-outbox {--limit=50} {--retry-failed}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessPaymentSettingOutboxCommand extends Command
{
    protected $signature = 'payment-settings:process-outbox {--limit=50} {--retry-failed}';

    protected $description = 'Process pending transactional outbox events for Super Admin Payment Settings';

    public function handle(ElasticService $elasticService): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');

        if ($retryFailed) {
            $resetCount = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'PaymentSetting')
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
            ->where('aggregate_type', 'PaymentSetting')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->get();

        if ($events->isEmpty()) {
            $this->info('No pending Payment Setting outbox events to process.');

            return Command::SUCCESS;
        }

        $this->info("Processing {$events->count()} Payment Setting outbox events...");

        foreach ($events as $event) {
            $this->line("Processing event ID: {$event->id} (Type: {$event->event_type})...");
            $singleJob = new ProcessPaymentSettingOutboxEventJob((int) $event->id);
            $singleJob->handle($elasticService);
        }

        $this->info('Payment Setting outbox processing complete.');

        return Command::SUCCESS;
    }
}
