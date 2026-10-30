<?php

namespace App\Console\Commands;

use App\Helper\GeneralMethod;
use App\Services\ElasticService;
use App\Services\SuperAdminCandidateListsCacheManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminCandidatesDriftCommand
 *
 * Intent: Forensic reconciliation and drift detection tool for Super Admin Candidates.
 * Detects discrepancies between MySQL authoritative records (add_job_wise_condidates / job_applications)
 * and Elasticsearch CQRS read index (ELASTIC_CANDIDATES). Automatically heals drifted
 * documents in rate-limited chunks with throttle delays to prevent infrastructure spikes.
 *
 * Signature: php artisan superadmin:candidates:drift {--limit=100} {--chunk=50} {--sleep-ms=20} {--heal} {--json}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminCandidatesDriftCommand extends Command
{
    protected $signature = 'superadmin:candidates:drift 
                            {--limit=100 : Number of recently updated candidate applications to check} 
                            {--chunk=50 : Batch chunk size for rate-limited processing}
                            {--sleep-ms=20 : Throttle delay between chunks in milliseconds}
                            {--heal : Reconcile drifted records to Elasticsearch}
                            {--json : Output telemetry as JSON}';

    protected $description = 'Detect and heal drift between MySQL candidate records and Elasticsearch index';

    public function handle(ElasticService $elasticService): int
    {
        $limit = max((int) $this->option('limit'), 10);
        $chunkSize = max((int) $this->option('chunk'), 10);
        $sleepMs = max((int) $this->option('sleep-ms'), 0);
        $heal = (bool) $this->option('heal');
        $asJson = (bool) $this->option('json');
        $candidatesIndex = defined('ELASTIC_CANDIDATES') ? ELASTIC_CANDIDATES : config('constants.ELASTIC_CANDIDATES', 'elastic_candidates');

        if (! $asJson) {
            $this->info("Scanning last {$limit} updated candidate records for drift (Chunk: {$chunkSize}, Sleep: {$sleepMs}ms)...");
        }

        if (! $elasticService->indexExists($candidatesIndex)) {
            if ($asJson) {
                $this->output->writeln(json_encode(['status' => 'ERROR', 'message' => "Target index {$candidatesIndex} does not exist."]));
            } else {
                $this->error("Target index {$candidatesIndex} does not exist!");
            }

            return 1;
        }

        $candidates = DB::table('add_job_wise_condidates')
            ->select('id', 'job_application_id', 'job_id', 'master_status_id', 'child_status_id', 'updated_at')
            ->orderByDesc('updated_at')
            ->limit($limit)
            ->get();

        $driftCount = 0;
        $healedCount = 0;
        $driftRecords = [];
        $processed = 0;

        foreach ($candidates->chunk($chunkSize) as $chunk) {
            foreach ($chunk as $cand) {
                $processed++;
                $esDoc = null;
                try {
                    $res = $elasticService->getClient()->get([
                        'index' => $candidatesIndex,
                        'id' => (string) $cand->id,
                    ]);
                    $esDoc = $res['_source'] ?? null;
                } catch (\Throwable $e) {
                    if ($cand->job_application_id) {
                        try {
                            $res = $elasticService->getClient()->get([
                                'index' => $candidatesIndex,
                                'id' => (string) $cand->job_application_id,
                            ]);
                            $esDoc = $res['_source'] ?? null;
                        } catch (\Throwable $e2) {
                        }
                    }
                }

                $mysqlStatus = (int) ($cand->master_status_id ?? 0);
                $esStatus = isset($esDoc['job_master_status_id']) ? (int) $esDoc['job_master_status_id'] : (isset($esDoc['master_status_id']) ? (int) $esDoc['master_status_id'] : -1);

                if ($esDoc === null || $esStatus !== $mysqlStatus) {
                    $driftCount++;
                    $driftRecords[] = [
                        'wise_id' => $cand->id,
                        'app_id' => $cand->job_application_id,
                        'mysql_status' => $mysqlStatus,
                        'es_status' => $esStatus,
                    ];

                    if (! $asJson) {
                        $this->warn("Drift on Candidate #{$cand->id} (App #{$cand->job_application_id}): MySQL [{$mysqlStatus}] != ES [{$esStatus}]");
                    }

                    if ($heal) {
                        try {
                            if ($cand->job_application_id) {
                                GeneralMethod::careerStore($cand->job_application_id);
                            }
                            SuperAdminCandidateListsCacheManager::invalidateAll($cand->id);
                            $healedCount++;
                            if (! $asJson) {
                                $this->info("  -> Healed Candidate #{$cand->id} in Elasticsearch.");
                            }
                        } catch (\Throwable $e) {
                            if (! $asJson) {
                                $this->error("  -> Failed healing #{$cand->id}: ".$e->getMessage());
                            }
                        }
                    }
                }
            }

            if ($sleepMs > 0) {
                usleep($sleepMs * 1000);
            }
        }

        if ($asJson) {
            $this->output->writeln(json_encode([
                'status' => ($driftCount === 0 || $healedCount === $driftCount) ? 'SUCCESS' : 'DRIFT_DETECTED',
                'scanned_count' => $candidates->count(),
                'drift_count' => $driftCount,
                'healed_count' => $healedCount,
                'drift_records' => $driftRecords,
            ], JSON_PRETTY_PRINT));

            return ($driftCount === 0 || $heal) ? 0 : 1;
        }

        if ($driftCount === 0) {
            $this->info("Scan completed: 0 drift detected across {$candidates->count()} candidate records. 100% synchronized.");

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
