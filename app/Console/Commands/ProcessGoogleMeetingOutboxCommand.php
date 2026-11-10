<?php

namespace App\Console\Commands;

use App\Jobs\ProcessGoogleMeetingOutboxEventJob;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ProcessGoogleMeetingOutboxCommand
 *
 * Intent: Scheduled and on-demand CLI command to dispatch pending and failed
 * Transactional Outbox events for the Super Admin Google Meet subsystem.
 *
 * Signature: php artisan google-meeting:process-outbox {--limit=50} {--retry-failed}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessGoogleMeetingOutboxCommand extends Command
{
    protected $signature = 'google-meeting:process-outbox {--limit=50 : Maximum number of events to process} {--retry-failed : Reclaim failed events}';

    protected $description = 'Process pending and failed Transactional Outbox events for Google Meet Link Pool';

    public function handle(ElasticService $elasticService): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');

        if ($retryFailed) {
            $reset = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'GoogleMeetingLink')
                ->whereIn('status', ['FAILED', 'PROCESSING'])
                ->update([
                    'status' => 'PENDING',
                    'available_at' => Carbon::now(),
                    'locked_at' => null,
                ]);

            if ($reset > 0) {
                $this->info("Reset {$reset} failed/stuck outbox events to PENDING.");
            }
        }

        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'GoogleMeetingLink')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->pluck('id')
            ->toArray();

        if (empty($events)) {
            $this->info('No pending Google Meet outbox events to process.');

            return self::SUCCESS;
        }

        $this->info('Processing '.count($events).' outbox event(s)...');
        $job = new ProcessGoogleMeetingOutboxEventJob;

        $processed = 0;
        foreach ($events as $eventId) {
            try {
                $instance = new ProcessGoogleMeetingOutboxEventJob((int) $eventId);
                $instance->handle($elasticService);
                $processed++;
            } catch (\Throwable $e) {
                $this->error("Failed to process event #{$eventId}: ".$e->getMessage());
            }
        }

        $this->info("Successfully processed {$processed}/".count($events).' outbox event(s).');

        return self::SUCCESS;
    }
}
