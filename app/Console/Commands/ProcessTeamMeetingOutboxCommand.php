<?php

namespace App\Console\Commands;

use App\Jobs\ProcessTeamMeetingOutboxEventJob;
use App\Services\ElasticService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ProcessTeamMeetingOutboxCommand
 *
 * Intent: Scheduled and on-demand CLI command to dispatch pending and failed
 * Transactional Outbox events for the Super Admin Microsoft Teams Meet subsystem.
 *
 * Signature: php artisan team-meeting:process-outbox {--limit=50} {--retry-failed}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessTeamMeetingOutboxCommand extends Command
{
    protected $signature = 'team-meeting:process-outbox {--limit=50 : Maximum number of events to process} {--retry-failed : Reclaim failed events}';

    protected $description = 'Process pending and failed Transactional Outbox events for Teams Meet Link Pool';

    public function handle(ElasticService $elasticService): int
    {
        $limit = (int) $this->option('limit');
        $retryFailed = (bool) $this->option('retry-failed');

        if ($retryFailed) {
            $reset = DB::table('sys_outbox_events')
                ->where('aggregate_type', 'TeamMeetingLink')
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
            ->where('aggregate_type', 'TeamMeetingLink')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit($limit)
            ->pluck('id')
            ->toArray();

        if (empty($events)) {
            $this->info('No pending Teams Meet outbox events to process.');

            return self::SUCCESS;
        }

        $this->info('Processing '.count($events).' outbox event(s)...');

        $processed = 0;
        foreach ($events as $eventId) {
            try {
                $instance = new ProcessTeamMeetingOutboxEventJob((int) $eventId);
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
