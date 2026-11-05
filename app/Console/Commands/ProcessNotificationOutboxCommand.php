<?php

namespace App\Console\Commands;

use App\Jobs\ProcessNotificationOutboxEventJob;
use App\Services\ElasticService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ProcessNotificationOutboxCommand
 *
 * Intent: Command-line processor and cron worker for the Super Admin Email Notifications Transactional Outbox.
 * Scans `sys_outbox_events` for pending or retryable EmailNotification events and synchronizes them to Elasticsearch projections.
 *
 * Signature: php artisan notifications:process-outbox {--limit=50} {--retry-failed}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessNotificationOutboxCommand extends Command
{
    protected $signature = 'notifications:process-outbox {--limit=50} {--retry-failed}';

    protected $description = 'Process pending Email Notification transactional outbox events for dual-store synchronization';

    public function handle(ElasticService $elasticService): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');

        if ($retryFailed) {
            $resetCount = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'EmailNotification')
                ->where('status', 'FAILED')
                ->update([
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                ]);
            $this->info("Reset {$resetCount} failed EmailNotification outbox events to PENDING.");
        }

        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'EmailNotification')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->pluck('id')
            ->toArray();

        if (empty($events)) {
            $this->info('No pending EmailNotification outbox events found.');

            return self::SUCCESS;
        }

        $this->info('Processing '.count($events).' pending outbox events...');

        $processed = 0;
        foreach ($events as $eventId) {
            try {
                $job = new ProcessNotificationOutboxEventJob((int) $eventId);
                $job->handle($elasticService);
                $processed++;
                $this->line("Processed outbox event #{$eventId}");
            } catch (Throwable $e) {
                $this->error("Failed to process outbox event #{$eventId}: ".$e->getMessage());
            }
        }

        $this->info("Successfully processed {$processed} outbox events.");

        return self::SUCCESS;
    }
}
