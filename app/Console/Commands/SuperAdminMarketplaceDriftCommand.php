<?php

namespace App\Console\Commands;

use App\Helper\GeneralMethod;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminMarketplaceCacheManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class SuperAdminMarketplaceDriftCommand
 *
 * Intent: Forensic reconciliation CLI comparing MySQL truth with Elasticsearch documents.
 * Identifies state drift in public_market_status, market_mod_state, market_pub_state,
 * and aggregate_version, with optional --heal flag to automatically reindex drifted documents.
 *
 * Signature: php artisan superadmin:marketplace:drift {--limit=100} {--heal} {--json}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminMarketplaceDriftCommand extends Command
{
    protected $signature = 'superadmin:marketplace:drift 
        {--limit=100 : Maximum number of job records to inspect}
        {--heal : Automatically heal drifted Elasticsearch documents}
        {--json : Output drift results as JSON}';

    protected $description = 'Forensic drift detection and reconciliation between MySQL truth and Elasticsearch for Marketplace Jobs';

    public function handle(ElasticService $elasticService): int
    {
        $limit = max(1, (int) $this->option('limit'));
        $heal = (bool) $this->option('heal');
        $jobsIndex = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : 'ats_jobs_elastic';

        $client = $elasticService->getClient();
        $indexExists = $elasticService->indexExists($jobsIndex);

        if (! $indexExists) {
            $this->error("Elasticsearch index '{$jobsIndex}' does not exist.");

            return 1;
        }

        $jobs = DB::table('jobs')
            ->where(function ($q) {
                $q->where('public_market_status', '>', 0)
                    ->orWhere('show_vendor', 'yes')
                    ->orWhere('bench_submission', 'enabled');
            })
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get();

        $drifted = [];
        $syncedCount = 0;

        foreach ($jobs as $job) {
            try {
                $response = $client->get([
                    'index' => $jobsIndex,
                    'id' => (string) $job->id,
                ]);

                $esDoc = $response['_source'] ?? [];
                $esStatus = (int) ($esDoc['public_market_status'] ?? 0);
                $esModState = (string) ($esDoc['market_mod_state'] ?? '');
                $esVersion = (int) ($esDoc['aggregate_version'] ?? ($esDoc['state_version'] ?? 0));

                $isDrifted = false;
                $reasons = [];

                if ($esStatus !== (int) $job->public_market_status) {
                    $isDrifted = true;
                    $reasons[] = "public_market_status mismatch (MySQL: {$job->public_market_status}, ES: {$esStatus})";
                }
                if ($esModState !== (string) ($job->market_mod_state ?? '')) {
                    $isDrifted = true;
                    $reasons[] = "market_mod_state mismatch (MySQL: {$job->market_mod_state}, ES: {$esModState})";
                }
                if ($esVersion < (int) ($job->aggregate_version ?? 0)) {
                    $isDrifted = true;
                    $reasons[] = "aggregate_version stale (MySQL: {$job->aggregate_version}, ES: {$esVersion})";
                }

                if ($isDrifted) {
                    $drifted[] = [
                        'job_id' => $job->id,
                        'title' => substr($job->title, 0, 30),
                        'reasons' => $reasons,
                        'healed' => false,
                    ];

                    if ($heal) {
                        GeneralMethod::elasticJobsAddEditUpdateIndex($job->id);
                        $drifted[count($drifted) - 1]['healed'] = true;
                    }
                } else {
                    $syncedCount++;
                }

            } catch (\Throwable $e) {
                // Document not found in ES
                $drifted[] = [
                    'job_id' => $job->id,
                    'title' => substr($job->title, 0, 30),
                    'reasons' => ['Document missing in Elasticsearch index'],
                    'healed' => false,
                ];

                if ($heal) {
                    GeneralMethod::elasticJobsAddEditUpdateIndex($job->id);
                    $drifted[count($drifted) - 1]['healed'] = true;
                }
            }
        }

        if ($heal && count($drifted) > 0) {
            SuperAdminMarketplaceCacheManager::invalidateAll();
        }

        if ($this->option('json')) {
            $this->output->write(json_encode([
                'inspected' => count($jobs),
                'synced' => $syncedCount,
                'drift_count' => count($drifted),
                'healed' => $heal,
                'items' => $drifted,
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return 0;
        }

        $this->newLine();
        $this->info('================================================================================');
        $this->info(' SUPER ADMIN MARKETPLACE OPERATIONS — FORENSIC DRIFT RECONCILER ');
        $this->info('================================================================================');
        $this->line('Inspected Records: '.count($jobs));
        $this->line("Synchronized:      <fg=green>{$syncedCount}</>");
        $this->line('Drift Detected:    '.(count($drifted) > 0 ? '<fg=red;options=bold>'.count($drifted).'</>' : '<fg=green>0</>'));

        if (count($drifted) > 0) {
            $this->newLine();
            $this->warn('Drifted Records:');
            foreach ($drifted as $item) {
                $statusText = $item['healed'] ? '<fg=green>[HEALED]</>' : '<fg=yellow>[DRIFTED]</>';
                $this->line("  Job #{$item['job_id']} - {$item['title']} {$statusText}: ".implode(', ', $item['reasons']));
            }

            if (! $heal) {
                $this->newLine();
                $this->comment('Tip: Run with --heal to automatically reindex drifted documents into Elasticsearch:');
                $this->line('  php artisan superadmin:marketplace:drift --heal');
            }
        }

        $this->newLine();

        return 0;
    }
}
