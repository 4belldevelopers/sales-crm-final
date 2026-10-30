<?php

namespace App\Console\Commands;

use App\Helper\GeneralMethod;
use App\Services\ElasticService;
use App\Services\SuperAdminJobsCacheManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminJobsDriftCommand
 *
 * Intent: Forensic reconciliation and drift detection tool for Super Admin Jobs.
 * Detects discrepancies between MySQL authoritative records (jobs table) and
 * Elasticsearch CQRS read index (ELASTIC_JOBS). Automatically heals any drifted
 * documents when the --heal flag is provided.
 *
 * Signature: php artisan superadmin:jobs:drift {--limit=100} {--heal : Automatically reconcile drifted records}
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminJobsDriftCommand extends Command
{
    protected $signature = 'superadmin:jobs:drift {--limit=100 : Number of recently updated jobs to check} {--heal : Reconcile drifted records to Elasticsearch}';

    protected $description = 'Detect and heal drift between MySQL jobs truth and Elasticsearch index';

    public function handle(ElasticService $elasticService): int
    {
        $limit = max((int) $this->option('limit'), 10);
        $heal = (bool) $this->option('heal');

        $this->info("Scanning last {$limit} updated jobs for drift...");

        if (! $elasticService->indexExists(ELASTIC_JOBS)) {
            $this->error('ELASTIC_JOBS index does not exist!');

            return 1;
        }

        $jobs = DB::table('jobs')
            ->select('id', 'status', 'company_id', 'title', 'updated_at')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();

        $driftCount = 0;
        $healedCount = 0;

        foreach ($jobs as $job) {
            $esDoc = null;
            try {
                $res = $elasticService->getClient()->get([
                    'index' => ELASTIC_JOBS,
                    'id' => $job->id,
                ]);
                $esDoc = $res['_source'] ?? null;
            } catch (\Throwable $e) {
                // Doc missing in ES
            }

            $mysqlStatus = strtolower(trim((string) $job->status));
            $esStatus = strtolower(trim((string) ($esDoc['status'] ?? 'MISSING')));

            if ($esStatus !== $mysqlStatus) {
                $driftCount++;
                $this->warn("Drift detected on Job #{$job->id} ({$job->title}): MySQL [{$mysqlStatus}] != ES [{$esStatus}]");

                if ($heal) {
                    try {
                        GeneralMethod::elasticJobsAddEditUpdateIndex($job->id);
                        SuperAdminJobsCacheManager::invalidateAll($job->id);
                        $healedCount++;
                        $this->info("  -> Healed Job #{$job->id} in Elasticsearch.");
                    } catch (\Throwable $e) {
                        $this->error("  -> Failed to heal Job #{$job->id}: ".$e->getMessage());
                    }
                }
            }
        }

        if ($driftCount === 0) {
            $this->info("Scan completed: 0 drift detected across {$jobs->count()} jobs. 100% synchronized.");

            return 0;
        }

        if ($heal) {
            $this->info("Drift Reconciliation Complete: {$healedCount}/{$driftCount} records healed.");

            return 0;
        }

        $this->warn("Total drift detected: {$driftCount} record(s). Run with --heal to reconcile.");

        return 1;
    }
}
