<?php

namespace App\Console\Commands;

use App\Services\CandidateOutboxTelemetryService;
use Illuminate\Console\Command;

/**
 * Class CandidateOutboxTelemetryCommand
 *
 * Intent: Command line telemetry audit for candidate outbox SLOs per docs/drawer_candidate.md.
 */
class CandidateOutboxTelemetryCommand extends Command
{
    protected $signature = 'candidates:outbox-telemetry {--json}';

    protected $description = 'Report Candidate Outbox telemetry SLO metrics and critical alerts';

    public function handle(CandidateOutboxTelemetryService $telemetryService): int
    {
        $metrics = $telemetryService->getTelemetryMetrics();

        if ($this->option('json')) {
            $this->output->writeln(json_encode($metrics, JSON_PRETTY_PRINT));

            return empty($metrics['alerts']) ? 0 : 1;
        }

        $this->info('=== ATS Candidate Outbox Telemetry & Production SLOs ===');
        $this->table(
            ['Metric', 'Current Value', 'Target / Normal', 'Critical Alert (P0)'],
            [
                ['max_projection_lag_ver', $metrics['max_projection_lag_ver'].' versions', '< 5 versions', '> 100 versions'],
                ['oldest_pending_event_age', $metrics['oldest_pending_event_age_sec'].' s', '< 2 seconds', '> 30 seconds'],
                ['outbox_dead_letter_count', $metrics['outbox_dead_letter_count'], '0', '> 0'],
                ['mutation_resilience_rate', $metrics['mutation_resilience_rate'].' %', '100%', '< 99.99%'],
            ]
        );

        if (! empty($metrics['alerts'])) {
            $this->error('CRITICAL ALERTS DETECTED:');
            foreach ($metrics['alerts'] as $alert) {
                $this->error(" - {$alert}");
            }

            return 1;
        }

        $this->info('All candidate outbox SLOs are healthy and within operating boundaries.');

        return 0;
    }
}
