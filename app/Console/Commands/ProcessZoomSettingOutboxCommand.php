<?php

namespace App\Console\Commands;

use App\Jobs\ProcessZoomSettingOutboxEventJob;
use App\Services\ElasticService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ProcessZoomSettingOutboxCommand
 *
 * Intent: Command-line processor and cron worker for the Zoom Video Meeting Transactional Outbox.
 * Scans `sys_outbox_events` for pending or retryable ZoomSetting events and synchronizes them to Elasticsearch.
 *
 * Signature: php artisan zoom-setting:process-outbox {--limit=50} {--retry-failed}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessZoomSettingOutboxCommand extends Command
{
    protected $signature = 'zoom-setting:process-outbox {--limit=50} {--retry-failed}';

    protected $description = 'Process pending Zoom Setting transactional outbox events for dual-store synchronization';

    public function handle(ElasticService $elasticService): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');

        if ($retryFailed) {
            $resetCount = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'ZoomSetting')
                ->where('status', 'FAILED')
                ->update([
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => now(),
                ]);
            $this->info("Reset {$resetCount} failed ZoomSetting outbox events to PENDING.");
        }

        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'ZoomSetting')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->pluck('id')
            ->toArray();

        if (empty($events)) {
            $this->info('No pending ZoomSetting outbox events found.');

            return self::SUCCESS;
        }

        $this->info('Processing '.count($events).' pending Zoom outbox events...');

        $processed = 0;
        foreach ($events as $eventId) {
            try {
                $job = new ProcessZoomSettingOutboxEventJob((int) $eventId);
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
