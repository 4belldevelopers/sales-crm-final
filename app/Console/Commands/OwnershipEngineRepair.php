<?php

namespace App\Console\Commands;

use App\Services\OwnershipEngine\OwnershipEngine;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Ownership Engine Phase 8, Recovery Utilities — the write counterpart to
 * ownership-engine:verify. Runs rebuildCluster()/rebuildCompany() over a scope — replays
 * ALREADY-RECORDED, immutable ownership_events; never creates a new event (Finding 4/6).
 */
class OwnershipEngineRepair extends Command
{
    protected $signature = 'ownership-engine:repair
                           {--cluster= : Rebuild a single cluster id}
                           {--company= : Rebuild every cluster scoped to this company id}
                           {--dry-run : Report what would be rebuilt, without writing}';

    protected $description = 'Phase 8: regenerate cluster/cache state by replaying existing events — never creates new events';

    public function handle(OwnershipEngine $engine): int
    {
        $clusterId = $this->option('cluster') ? (int) $this->option('cluster') : null;
        $companyId = $this->option('company') ? (int) $this->option('company') : null;
        $dryRun = (bool) $this->option('dry-run');

        if (!$clusterId && !$companyId) {
            $this->error('Provide --cluster=<id> or --company=<id>.');

            return self::FAILURE;
        }

        if ($clusterId) {
            if ($dryRun) {
                $issues = $engine->verifyClusterConsistency($clusterId);
                $this->info(empty($issues)
                    ? "Cluster #{$clusterId} is already consistent — nothing to repair."
                    : "Cluster #{$clusterId} has ".count($issues).' issue(s) that repair would fix.');

                return self::SUCCESS;
            }

            $results = $engine->rebuildCluster($clusterId);
            $this->info("Cluster #{$clusterId} rebuilt — ".count($results).' member row(s) updated.');

            return self::SUCCESS;
        }

        $clusterCount = DB::table('ownership_clusters')->where('scoping_company_id', $companyId)->count();

        if ($dryRun) {
            $this->info("Dry run: {$clusterCount} cluster(s) scoped to company #{$companyId} would be rebuilt.");

            return self::SUCCESS;
        }

        $this->info("Rebuilding {$clusterCount} cluster(s) for company #{$companyId}...");
        $rebuilt = $engine->rebuildCompany($companyId);
        $this->info("Done — {$rebuilt} cluster(s) rebuilt.");

        return self::SUCCESS;
    }
}
