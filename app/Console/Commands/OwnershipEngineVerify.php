<?php

namespace App\Console\Commands;

use App\Services\OwnershipEngine\OwnershipEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ownership Engine Phase 8, Recovery Utilities — read-only. Runs
 * verifyClusterConsistency()/orphan detection over a scope. Reports findings; never writes.
 * Use ownership-engine:repair to actually fix anything found here.
 */
class OwnershipEngineVerify extends Command
{
    protected $signature = 'ownership-engine:verify
                           {--company= : Verify every cluster scoped to this company id}
                           {--job= : Verify every cluster scoped to this job id (Job mode only)}
                           {--cluster= : Verify a single cluster id}
                           {--orphans : Also check for orphan events/clusters and cache inconsistencies (unscoped, capped by --limit)}
                           {--limit=1000 : Cap for the --orphans checks}';

    protected $description = 'Phase 8: read-only consistency check — cluster/cache correctness and orphan detection';

    public function handle(OwnershipEngine $engine): int
    {
        $clusterId = $this->option('cluster') ? (int) $this->option('cluster') : null;
        $companyId = $this->option('company') ? (int) $this->option('company') : null;
        $jobId = $this->option('job') ? (int) $this->option('job') : null;

        $totalIssues = 0;

        if ($clusterId) {
            $issues = $engine->verifyClusterConsistency($clusterId);
            $this->reportClusterIssues($clusterId, $issues);
            $totalIssues += count($issues);
        } elseif ($companyId || $jobId) {
            $query = DB::table('ownership_clusters')->select('id');
            if ($companyId) {
                $query->where('scoping_company_id', $companyId);
            }
            if ($jobId) {
                $query->where('scope_mode', 1)->where('scope_key', $jobId);
            }

            $clusters = $query->get();
            $this->info("Verifying {$clusters->count()} cluster(s)...");

            foreach ($clusters as $row) {
                $issues = $engine->verifyClusterConsistency((int) $row->id);
                if (!empty($issues)) {
                    $this->reportClusterIssues((int) $row->id, $issues);
                    $totalIssues += count($issues);
                }
            }

            if ($totalIssues === 0) {
                $this->info('All checked clusters are consistent.');
            }
        } elseif (!$this->option('orphans')) {
            $this->error('Provide --cluster=, --company=, --job=, or --orphans.');

            return self::FAILURE;
        }

        if ($this->option('orphans')) {
            $limit = (int) $this->option('limit');
            $this->line('');
            $this->info('Orphan / cache-inconsistency checks (unscoped, capped at '.$limit.'):');

            $orphanEvents = $engine->detectOrphanEvents($limit);
            $orphanClusters = $engine->detectOrphanClusters($limit);
            $cacheInconsistencies = $engine->detectCacheInconsistencies($limit);

            $this->line("Orphan events (cluster_id doesn't resolve): {$orphanEvents->count()}");
            $this->line("Orphan clusters (zero events): {$orphanClusters->count()}");
            $this->line("Cache inconsistencies (cache disagrees with cluster): {$cacheInconsistencies->count()}");

            $totalIssues += $orphanEvents->count() + $orphanClusters->count() + $cacheInconsistencies->count();

            if ($orphanClusters->isNotEmpty()) {
                $this->warn('Orphan clusters found — see docs/OWNERSHIP_ENGINE_DESIGN.md Phase 8 "Risks discovered" for the known, currently-unreachable code path this can come from.');
                $this->table(['Cluster ID', 'Company', 'Scope Mode', 'Scope Key'], $orphanClusters->map(fn ($c) => [$c->id, $c->scoping_company_id, $c->scope_mode, $c->scope_key])->all());
            }
        }

        if ($totalIssues > 0) {
            $this->warn("Total issues found: {$totalIssues}. Run ownership-engine:repair to fix.");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }

    private function reportClusterIssues(int $clusterId, array $issues): void
    {
        if (empty($issues)) {
            $this->info("Cluster #{$clusterId}: consistent.");

            return;
        }

        $this->warn("Cluster #{$clusterId}: ".count($issues).' issue(s):');
        foreach ($issues as $issue) {
            $this->line('  - '.$issue);
        }
    }
}
