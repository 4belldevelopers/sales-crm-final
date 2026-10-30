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
 * Signature:
 *   php artisan superadmin:jobs:drift                           (Check last 100 jobs)
 *   php artisan superadmin:jobs:drift --heal                    (Detect & heal drift)
 *   php artisan superadmin:jobs:drift --dry-run                 (Explicit dry-run scan)
 *   php artisan superadmin:jobs:drift --job-id=123 --heal       (Heal specific job)
 *   php artisan superadmin:jobs:drift --limit=1000 --batch-size=200
 *
 * Author: Antigravity AI
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminJobsDriftCommand extends Command
{
    protected $signature = 'superadmin:jobs:drift 
                            {--limit=100 : Number of recently updated jobs to check} 
                            {--batch-size=100 : Chunk size for processing}
                            {--job-id= : Check and optionally heal a specific job ID}
                            {--dry-run : Explicit check without applying healing}
                            {--heal : Reconcile and synchronize drifted records to Elasticsearch}
                            {--json : Output drift results as JSON}';

    protected $description = 'Detect and heal drift between MySQL jobs truth and Elasticsearch index';

    public function handle(ElasticService $elasticService): int
    {
        $limit = max((int) $this->option('limit'), 1);
        $batchSize = max((int) $this->option('batch-size'), 10);
        $jobId = $this->option('job-id');
        $heal = (bool) $this->option('heal') && ! (bool) $this->option('dry-run');
        $isJson = (bool) $this->option('json');

        if (! $elasticService->indexExists(ELASTIC_JOBS)) {
            $msg = 'ELASTIC_JOBS index does not exist!';
            if ($isJson) {
                $this->line(json_encode(['status' => 'ERROR', 'message' => $msg]));
            } else {
                $this->error($msg);
            }

            return 1;
        }

        $query = DB::table('jobs')
            ->select('id', 'status', 'company_id', 'title', 'updated_at');

        if ($jobId) {
            $query->where('id', (int) $jobId);
        } else {
            $query->orderByDesc('updated_at')->limit($limit);
        }

        $jobs = $query->get();

        if ($jobs->isEmpty()) {
            if ($isJson) {
                $this->line(json_encode(['status' => 'EMPTY', 'count' => 0, 'drifted' => []]));
            } else {
                $this->info('No jobs found matching criteria.');
            }

            return 0;
        }

        if (! $isJson) {
            $this->info("Scanning {$jobs->count()} job(s) for MySQL <-> Elasticsearch drift...");
        }

        $driftedRecords = [];
        $healedCount = 0;

        foreach ($jobs->chunk($batchSize) as $chunk) {
            foreach ($chunk as $job) {
                $esDoc = null;
                try {
                    $res = $elasticService->getClient()->get([
                        'index' => ELASTIC_JOBS,
                        'id' => $job->id,
                    ]);
                    $esDoc = $res['_source'] ?? null;
                } catch (\Throwable $e) {
                    // Document missing in Elasticsearch
                }

                $mysqlStatus = strtolower(trim((string) $job->status));
                $esStatus = strtolower(trim((string) ($esDoc['status'] ?? 'MISSING')));

                if ($esStatus !== $mysqlStatus) {
                    $driftedRecords[] = [
                        'job_id' => $job->id,
                        'title' => $job->title,
                        'mysql_status' => $mysqlStatus,
                        'es_status' => $esStatus,
                    ];

                    if (! $isJson) {
                        $this->warn("Drift detected on Job #{$job->id} ({$job->title}): MySQL [{$mysqlStatus}] != ES [{$esStatus}]");
                    }

                    if ($heal) {
                        try {
                            GeneralMethod::elasticJobsAddEditUpdateIndex($job->id);
                            SuperAdminJobsCacheManager::invalidateAll($job->id);
                            $healedCount++;
                            if (! $isJson) {
                                $this->info("  -> Healed Job #{$job->id} in Elasticsearch & invalidated cache.");
                            }
                        } catch (\Throwable $e) {
                            if (! $isJson) {
                                $this->error("  -> Failed to heal Job #{$job->id}: ".$e->getMessage());
                            }
                        }
                    }
                }
            }
        }

        $driftCount = count($driftedRecords);

        if ($isJson) {
            $this->line(json_encode([
                'status' => ($driftCount === 0) ? 'IN_SYNC' : ($heal ? 'HEALED' : 'DRIFT_DETECTED'),
                'scanned_count' => $jobs->count(),
                'drift_count' => $driftCount,
                'healed_count' => $healedCount,
                'drifted' => $driftedRecords,
            ], JSON_PRETTY_PRINT));

            return ($driftCount === 0 || $heal) ? 0 : 1;
        }

        if ($driftCount === 0) {
            $this->info("Scan completed: 0 drift detected across {$jobs->count()} jobs. 100% synchronized.");

            return 0;
        }

        if ($heal) {
            $this->info("Drift Reconciliation Complete: {$healedCount}/{$driftCount} record(s) healed successfully.");

            return 0;
        }

        $this->warn("Total drift detected: {$driftCount} record(s). Run with --heal to reconcile.");

        return 1;
    }
}
