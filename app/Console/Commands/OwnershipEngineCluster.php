<?php

namespace App\Console\Commands;

use App\Console\Commands\Concerns\MasksPii;
use App\Services\OwnershipEngine\Models\OwnershipCluster;
use App\Services\OwnershipEngine\OwnershipEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ownership Engine Phase 8 — single-cluster inspector, read-only. The cluster-scoped
 * counterpart to ownership-engine:candidate / explain().
 */
class OwnershipEngineCluster extends Command
{
    use MasksPii;

    protected $signature = 'ownership-engine:cluster {clusterId}';

    protected $description = 'Phase 8: inspect one cluster — current state plus full event history';

    public function handle(OwnershipEngine $engine): int
    {
        $clusterId = (int) $this->argument('clusterId');

        $cluster = OwnershipCluster::find($clusterId);
        if (!$cluster) {
            $this->error("Cluster #{$clusterId} does not exist.");

            return self::FAILURE;
        }

        $this->info("Cluster #{$clusterId}");
        $this->table(['Field', 'Value'], [
            ['Scoping company', $cluster->scoping_company_id],
            ['Scope mode', $cluster->scope_mode === 2 ? 'Company Overall' : 'Job'],
            ['Scope key', $cluster->scope_key],
            ['Identity (email)', $this->maskEmail($cluster->normalized_email)],
            ['Identity (phone)', $this->maskPhone($cluster->normalized_phone)],
            ['Owner row', $cluster->owner_row_id ?? '(undetermined)'],
            ['Owner type', $cluster->owner_type ?? '(n/a)'],
            ['Decision rule', $cluster->decision_rule ?? '(n/a)'],
            ['Decided at', $cluster->decided_at?->toDateTimeString() ?? '(n/a)'],
        ]);

        $members = DB::table('add_job_wise_condidates')
            ->where('ownership_cluster_id', $clusterId)
            ->select('id', 'thirdparty_company_id', 'ownership_state', 'job_id')
            ->get();

        $this->line('');
        $this->info('Member rows ('.$members->count().'):');
        $this->table(
            ['Row ID', 'Job', 'Type', 'State'],
            $members->map(fn ($m) => [
                $m->id,
                $m->job_id,
                (int) $m->thirdparty_company_id === 0 ? 'company' : 'agency',
                match ($m->ownership_state) { 1 => 'OWNER', 2 => 'DUPLICATE', default => 'undetermined' },
            ])->all()
        );

        $history = $engine->history($clusterId);
        $this->line('');
        $this->info('Event history ('.$history->count().' event(s), chronological):');
        foreach ($history as $event) {
            $this->line(sprintf(
                '  [%s] %s row #%d | rule=%s | trigger=%s | %s',
                $event->event_time,
                $event->event_type,
                $event->subject_row_id,
                $event->decision_rule ?? '-',
                $event->trigger_source,
                $event->reason,
            ));
        }

        $issues = $engine->verifyClusterConsistency($clusterId);
        $this->line('');
        $this->line(empty($issues) ? '✅ Consistent.' : '⚠️  '.count($issues).' issue(s) — run ownership-engine:verify --cluster='.$clusterId.' for detail.');

        return self::SUCCESS;
    }
}
