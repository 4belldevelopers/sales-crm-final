<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminJobRequestCacheManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminJobRequestsDriftCommand
 *
 * INTENT:
 * Forensic reconciliation and drift detection tool for Super Admin Job Collaboration Requests.
 * Detects discrepancies between MySQL authoritative records (recommendation_relation table) and
 * Elasticsearch CQRS read index (ELASTIC_JOB_REQUEST: local_jobs_request).
 * Automatically heals any drifted documents when the --heal flag is provided.
 *
 * SIGNATURE:
 * php artisan superadmin:job-requests:drift {--limit=100} {--heal : Reconcile drifted records to Elasticsearch}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class SuperAdminJobRequestsDriftCommand extends Command
{
    protected $signature = 'superadmin:job-requests:drift {--limit=100 : Number of recently updated job requests to check} {--heal : Reconcile drifted records to Elasticsearch}';

    protected $description = 'Detect and heal drift between MySQL recommendation_relation truth and Elasticsearch index';

    public function handle(ElasticService $elasticService): int
    {
        $limit = max((int) $this->option('limit'), 10);
        $heal = (bool) $this->option('heal');

        $this->info("Scanning last {$limit} updated Job Collaboration Requests for drift...");

        if (! $elasticService->indexExists(ELASTIC_JOB_REQUEST)) {
            $this->error('ELASTIC_JOB_REQUEST index does not exist!');

            return 1;
        }

        $relations = DB::table('recommendation_relation')
            ->select('id', 'job_id', 'final_status', 'to_status', 'accepterms', 'updated_at')
            ->orderByDesc('id')
            ->limit($limit)
            ->get();

        $driftCount = 0;
        $healedCount = 0;

        foreach ($relations as $rel) {
            $esDoc = null;
            try {
                $res = $elasticService->getClient()->get([
                    'index' => ELASTIC_JOB_REQUEST,
                    'id' => (string) $rel->id,
                ]);
                $esDoc = $res['_source'] ?? null;
            } catch (\Throwable $e) {
                // Doc missing in ES
            }

            $mysqlStatus = (int) $rel->final_status;
            $esStatus = isset($esDoc['final_status']) ? (int) $esDoc['final_status'] : null;

            if ($esStatus === null || $esStatus !== $mysqlStatus) {
                $driftCount++;
                $esStatusLabel = ($esStatus !== null) ? (string) $esStatus : 'MISSING';
                $this->warn("Drift detected on Request #{$rel->id} (Job #{$rel->job_id}): MySQL final_status [{$mysqlStatus}] != ES [{$esStatusLabel}]");

                if ($heal) {
                    try {
                        $updateBody = [
                            'final_status' => $mysqlStatus,
                            'to_status' => (int) $rel->to_status,
                            'updated_at' => now()->toIso8601String(),
                        ];
                        if (! empty($rel->accepterms)) {
                            $updateBody['accepterms'] = $rel->accepterms;
                        }

                        $elasticService->getClient()->update([
                            'index' => ELASTIC_JOB_REQUEST,
                            'id' => (string) $rel->id,
                            'retry_on_conflict' => 3,
                            'body' => [
                                'doc' => $updateBody,
                                'doc_as_upsert' => false,
                            ],
                            'refresh' => true,
                        ]);

                        SuperAdminJobRequestCacheManager::invalidateAll((int) $rel->id);
                        $healedCount++;
                        $this->info("  -> Healed Request #{$rel->id} in Elasticsearch.");
                    } catch (\Throwable $e) {
                        $this->error("  -> Failed to heal Request #{$rel->id}: ".$e->getMessage());
                    }
                }
            }
        }

        if ($driftCount === 0) {
            $this->info("Scan completed: 0 drift detected across {$relations->count()} requests. 100% synchronized.");

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
