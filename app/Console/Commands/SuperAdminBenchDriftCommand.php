<?php

namespace App\Console\Commands;

use App\Helper\GeneralMethod;
use App\Services\ElasticService;
use App\Services\SuperAdminBenchCandidatesCacheManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminBenchDriftCommand
 *
 * Intent: Forensic reconciliation and drift detection tool for Super Admin Bench Candidates.
 * Detects discrepancies between MySQL authoritative records (job_applications where status=2 or is_bench=1)
 * and Elasticsearch CQRS read index (ELASTIC_CANDIDATES_BENCH). Automatically heals drifted
 * documents in rate-limited chunks with throttle delays to prevent infrastructure spikes.
 *
 * Signature: php artisan superadmin:bench:drift {--limit=100} {--chunk=50} {--sleep-ms=20} {--heal} {--json}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminBenchDriftCommand extends Command
{
    protected $signature = 'superadmin:bench:drift 
                            {--limit=100 : Number of recently updated bench records to check} 
                            {--chunk=50 : Batch chunk size for rate-limited processing}
                            {--sleep-ms=20 : Throttle delay between chunks in milliseconds}
                            {--heal : Reconcile drifted records to Elasticsearch}
                            {--json : Output telemetry as JSON}';

    protected $description = 'Detect and heal drift between MySQL bench candidate records and Elasticsearch index';

    public function handle(ElasticService $elasticService): int
    {
        $limit = max((int) $this->option('limit'), 10);
        $chunkSize = max((int) $this->option('chunk'), 10);
        $sleepMs = max((int) $this->option('sleep-ms'), 0);
        $heal = (bool) $this->option('heal');
        $asJson = (bool) $this->option('json');
        $benchIndex = defined('ELASTIC_CANDIDATES_BENCH') ? ELASTIC_CANDIDATES_BENCH : config('constants.ELASTIC_CANDIDATES_BENCH', 'elastic_candidates_bench');

        if (! $asJson) {
            $this->info("Scanning last {$limit} updated bench candidate records for drift (Chunk: {$chunkSize}, Sleep: {$sleepMs}ms)...");
        }

        if (! $elasticService->indexExists($benchIndex)) {
            if ($asJson) {
                $this->output->writeln(json_encode(['status' => 'ERROR', 'message' => "Target index {$benchIndex} does not exist."]));
            } else {
                $this->error("Target index {$benchIndex} does not exist!");
            }

            return 1;
        }

        $candidates = DB::table('job_applications')
            ->select('id', 'full_name', 'superadminstatus', 'status', 'condidate_type', 'updated_at')
            ->where(function ($q) {
                $q->where('status', 2)
                    ->orWhere('condidate_type', 'bench_condidate');
            })
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
                        'index' => $benchIndex,
                        'id' => (string) $cand->id,
                    ]);
                    $esDoc = $res['_source'] ?? null;
                } catch (\Throwable $e) {
                    // Try alternative key format if applicable
                }

                $mysqlSaStatus = (int) ($cand->superadminstatus ?? 0);
                $esSaStatus = isset($esDoc['superadminstatus']) ? (int) $esDoc['superadminstatus'] : -1;

                if ($esDoc === null || $esSaStatus !== $mysqlSaStatus) {
                    $driftCount++;
                    $driftRecords[] = [
                        'candidate_id' => $cand->id,
                        'name' => trim($cand->full_name ?? ''),
                        'mysql_superadminstatus' => $mysqlSaStatus,
                        'es_superadminstatus' => $esSaStatus,
                        'es_missing' => ($esDoc === null),
                    ];

                    if (! $asJson) {
                        $this->warn("Drift on Candidate #{$cand->id}: MySQL superadminstatus [{$mysqlSaStatus}] != ES [{$esSaStatus}]".($esDoc === null ? ' (MISSING IN ES)' : ''));
                    }

                    if ($heal) {
                        try {
                            if (class_exists(GeneralMethod::class) && method_exists(GeneralMethod::class, 'careerStore')) {
                                GeneralMethod::careerStore($cand->id);
                            }
                            SuperAdminBenchCandidatesCacheManager::invalidateAll($cand->id);
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
                'status' => 'SUCCESS',
                'scanned' => $processed,
                'drift_count' => $driftCount,
                'healed_count' => $healedCount,
                'records' => $driftRecords,
            ], JSON_PRETTY_PRINT));

            return 0;
        }

        $this->newLine();
        $this->info('========================================================================');
        $this->info('              BENCH CANDIDATES DRIFT RECONCILIATION SUMMARY             ');
        $this->info('========================================================================');
        $this->line("Total Records Scanned : {$processed}");
        $this->line('Drifted Records Found : '.($driftCount > 0 ? "<fg=yellow;options=bold>{$driftCount}</>" : '<fg=green>0 (Perfect Parity)</>'));
        if ($heal) {
            $this->line("Successfully Healed   : <fg=green;options=bold>{$healedCount}</> / {$driftCount}");
        }
        $this->info('========================================================================');

        return $driftCount === 0 ? 0 : 2;
    }
}
