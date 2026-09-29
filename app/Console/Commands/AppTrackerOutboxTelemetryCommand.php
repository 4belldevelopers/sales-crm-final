<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class AppTrackerOutboxTelemetryCommand
 *
 * Intent: Command line telemetry audit for AppTracker dual-lease transactional outbox SLOs.
 * Reports backlog, latency lag, dead letter counts, and active worker leases.
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class AppTrackerOutboxTelemetryCommand extends Command
{
    protected $signature = 'apptracker:telemetry {--json : Output metrics in JSON format for automated monitoring}';

    protected $description = 'Report AppTracker dual-lease transactional outbox telemetry and health status';

    public function handle(): int
    {
        $now = now();

        // 1. Elasticsearch status counts
        $esStats = DB::table('transactional_outboxes')
            ->select('es_status', DB::raw('count(*) as count'))
            ->groupBy('es_status')
            ->pluck('count', 'es_status')
            ->toArray();

        // 2. Reverb status counts
        $reverbStats = DB::table('transactional_outboxes')
            ->select('reverb_status', DB::raw('count(*) as count'))
            ->groupBy('reverb_status')
            ->pluck('count', 'reverb_status')
            ->toArray();

        // 3. Oldest pending events age
        $oldestPending = DB::table('transactional_outboxes')
            ->where(function ($q) {
                $q->where('es_status', 'pending')
                    ->orWhere('reverb_status', 'pending');
            })
            ->orderBy('id', 'asc')
            ->first();

        $oldestPendingAgeSec = $oldestPending ? $now->diffInSeconds($oldestPending->created_at) : 0;

        // 4. Expired locks count (potential crashed workers)
        $expiredEsLocks = DB::table('transactional_outboxes')
            ->where('es_status', 'processing')
            ->where('es_locked_until', '<', $now)
            ->count();

        $expiredReverbLocks = DB::table('transactional_outboxes')
            ->where('reverb_status', 'processing')
            ->where('reverb_locked_until', '<', $now)
            ->count();

        $deadLetterCount = ($esStats['dead_letter'] ?? 0) + ($reverbStats['dead_letter'] ?? 0);

        $alerts = [];
        if ($deadLetterCount > 0) {
            $alerts[] = "P0 Alert: {$deadLetterCount} outbox events in dead_letter queue!";
        }
        if ($oldestPendingAgeSec > 60) {
            $alerts[] = "P1 Alert: Oldest pending outbox event is {$oldestPendingAgeSec}s old (target: < 10s)";
        }
        if ($expiredEsLocks > 0 || $expiredReverbLocks > 0) {
            $alerts[] = "P2 Notice: Expired locks detected (ES: {$expiredEsLocks}, Reverb: {$expiredReverbLocks}) - will be reclaimed on next worker batch";
        }

        $metrics = [
            'es_pending' => $esStats['pending'] ?? 0,
            'es_processing' => $esStats['processing'] ?? 0,
            'es_completed' => $esStats['completed'] ?? 0,
            'es_failed' => $esStats['failed'] ?? 0,
            'es_dead_letter' => $esStats['dead_letter'] ?? 0,
            'reverb_pending' => $reverbStats['pending'] ?? 0,
            'reverb_processing' => $reverbStats['processing'] ?? 0,
            'reverb_completed' => $reverbStats['completed'] ?? 0,
            'reverb_failed' => $reverbStats['failed'] ?? 0,
            'reverb_dead_letter' => $reverbStats['dead_letter'] ?? 0,
            'oldest_pending_age_sec' => $oldestPendingAgeSec,
            'expired_es_locks' => $expiredEsLocks,
            'expired_reverb_locks' => $expiredReverbLocks,
            'alerts' => $alerts,
            'healthy' => empty($alerts),
        ];

        if ($this->option('json')) {
            $this->output->writeln(json_encode($metrics, JSON_PRETTY_PRINT));

            return empty($alerts) ? Command::SUCCESS : Command::FAILURE;
        }

        $this->info('=== AppTracker Dual-Lease Outbox Telemetry & Production SLOs ===');
        $this->table(
            ['Subsystem', 'Pending', 'Processing', 'Failed', 'Dead Letter', 'Completed'],
            [
                ['Elasticsearch', $metrics['es_pending'], $metrics['es_processing'], $metrics['es_failed'], $metrics['es_dead_letter'], $metrics['es_completed']],
                ['Reverb WebSockets', $metrics['reverb_pending'], $metrics['reverb_processing'], $metrics['reverb_failed'], $metrics['reverb_dead_letter'], $metrics['reverb_completed']],
            ]
        );

        $this->line("Oldest Pending Event Age: <comment>{$oldestPendingAgeSec}s</comment> (Target: < 10s)");
        $this->line("Expired Worker Locks: <comment>ES={$expiredEsLocks}, Reverb={$expiredReverbLocks}</comment>");

        if (! empty($alerts)) {
            $this->newLine();
            $this->error('TELEMETRY ALERTS DETECTED:');
            foreach ($alerts as $alert) {
                $this->error(" - {$alert}");
            }

            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('All AppTracker transactional outbox SLOs are healthy and within operating boundaries.');

        return Command::SUCCESS;
    }
}
