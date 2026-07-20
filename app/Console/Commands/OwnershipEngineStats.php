<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ownership Engine Phase 8, Metrics — all derived from existing data, no new instrumentation
 * (see docs/OWNERSHIP_ENGINE_DESIGN.md Phase 8 "Metrics" table for what is and isn't
 * measurable this way; queue latency and lock contention are explicitly NOT included here).
 */
class OwnershipEngineStats extends Command
{
    protected $signature = 'ownership-engine:stats
                           {--company= : Scope to this company id}
                           {--job= : Scope to this job id}';

    protected $description = 'Phase 8: ownership/cluster/event statistics, duplicate distribution, event throughput';

    public function handle(): int
    {
        $companyId = $this->option('company') ? (int) $this->option('company') : null;
        $jobId = $this->option('job') ? (int) $this->option('job') : null;

        $clusterQuery = DB::table('ownership_clusters');
        $rowQuery = DB::table('add_job_wise_condidates');
        $eventQuery = DB::table('ownership_events');

        if ($companyId) {
            $clusterQuery->where('scoping_company_id', $companyId);
            $rowQuery->where('company_id', $companyId);
            $eventQuery->where('scoping_company_id', $companyId);
        }
        if ($jobId) {
            $clusterQuery->where('scope_mode', 1)->where('scope_key', $jobId);
            $rowQuery->where('job_id', $jobId);
            $eventQuery->where('scope_key', $jobId)->where('scope_mode', 1);
        }

        $this->info('Ownership Engine statistics'.($companyId ? " (company #{$companyId})" : '').($jobId ? " (job #{$jobId})" : ''));
        $this->line('');

        // --- Cluster statistics ---
        $clusterCount = (clone $clusterQuery)->count();
        $determinedClusters = (clone $clusterQuery)->whereNotNull('owner_row_id')->count();
        $this->info('Cluster statistics:');
        $this->table(['Metric', 'Value'], [
            ['Total clusters', $clusterCount],
            ['Owner determined', $determinedClusters],
            ['Still undetermined (no owner yet)', $clusterCount - $determinedClusters],
        ]);

        // --- Ownership / duplicate distribution ---
        $stateCounts = (clone $rowQuery)
            ->select('ownership_state', DB::raw('COUNT(*) as cnt'))
            ->groupBy('ownership_state')
            ->pluck('cnt', 'ownership_state');
        $this->line('');
        $this->info('Duplicate ownership distribution (add_job_wise_condidates.ownership_state):');
        $this->table(['State', 'Count'], [
            ['Owner (1)', $stateCounts[1] ?? 0],
            ['Duplicate (2)', $stateCounts[2] ?? 0],
            ['Undetermined/private (null)', $stateCounts[null] ?? 0],
        ]);

        // --- Event statistics / throughput ---
        $eventCount = (clone $eventQuery)->count();
        $byTriggerSource = (clone $eventQuery)->select('trigger_source', DB::raw('COUNT(*) as cnt'))->groupBy('trigger_source')->pluck('cnt', 'trigger_source');
        $byRule = (clone $eventQuery)->select('decision_rule', DB::raw('COUNT(*) as cnt'))->groupBy('decision_rule')->pluck('cnt', 'decision_rule');
        $this->line('');
        $this->info('Event statistics:');
        $this->line("Total events: {$eventCount}");
        $this->table(['Trigger source', 'Count'], collect($byTriggerSource)->map(fn ($cnt, $src) => [$src, $cnt])->values()->all());
        $this->table(['Decision rule', 'Count'], collect($byRule)->map(fn ($cnt, $rule) => [$rule ?? '(none)', $cnt])->values()->all());

        $last24h = (clone $eventQuery)->where('created_at', '>=', now()->subDay())->count();
        $last7d = (clone $eventQuery)->where('created_at', '>=', now()->subWeek())->count();
        $this->line("Event throughput: {$last24h} in last 24h, {$last7d} in last 7d.");

        // --- Processing time proxy (created_at vs event_time — see Metrics table in design doc) ---
        $avgDeltaSeconds = (clone $eventQuery)
            ->whereNotNull('event_time')
            ->selectRaw('AVG(TIMESTAMPDIFF(MICROSECOND, event_time, created_at)) / 1000000 as avg_seconds')
            ->value('avg_seconds');
        $this->line('');
        $this->comment('Processing time proxy (created_at - event_time, not wall-clock request latency): '.
            ($avgDeltaSeconds !== null ? round((float) $avgDeltaSeconds, 4).'s average' : 'n/a (no events)'));

        return self::SUCCESS;
    }
}
