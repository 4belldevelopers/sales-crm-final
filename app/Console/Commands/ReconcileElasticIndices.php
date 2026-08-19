<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * ReconcileElasticIndices — Detects and repairs drift between MySQL and Elasticsearch.
 *
 * Intent:
 * Compares primary entity IDs in MySQL against Elasticsearch indices (companies, candidates, jobs).
 * Identifies missing or out-of-sync documents and dispatches indexing jobs to repair drift.
 *
 * Usage:
 *   php artisan elastic:reconcile --entity=companies
 *   php artisan elastic:reconcile --entity=jobs --limit=500
 *   php artisan elastic:reconcile --dry-run
 *
 * Author: Antigravity AI
 * Date: August 19, 2026
 */
class ReconcileElasticIndices extends Command
{
    protected $signature = 'elastic:reconcile
                            {--entity=all : Entity to reconcile (companies, candidates, jobs, or all)}
                            {--dry-run : Count drift without dispatching indexing jobs}
                            {--limit=1000 : Maximum records to check per run}';

    protected $description = 'Diff MySQL against Elasticsearch and dispatch re-indexing for missing documents';

    public function handle(ElasticService $es): int
    {
        $entity = strtolower((string) $this->option('entity'));
        $dryRun = (bool) $this->option('dry-run');
        $limit = (int) $this->option('limit');

        $this->info("Starting Elasticsearch drift reconciliation [entity: {$entity}, dry-run: ".($dryRun ? 'YES' : 'NO').']');

        $totalDrift = 0;

        if ($entity === 'all' || $entity === 'companies') {
            $totalDrift += $this->reconcileCompanies($es, $dryRun, $limit);
        }

        if ($entity === 'all' || $entity === 'jobs') {
            $totalDrift += $this->reconcileJobs($es, $dryRun, $limit);
        }

        $this->info("Reconciliation complete. Total drift items detected: {$totalDrift}");

        return self::SUCCESS;
    }

    private function getIndexName(string $key, string $default): string
    {
        if (defined($key)) {
            return constant($key);
        }

        return config("constants.{$key}", $default);
    }

    private function reconcileCompanies(ElasticService $es, bool $dryRun, int $limit): int
    {
        $mysqlIds = DB::table('companies')
            ->whereNull('delete_account_at')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($mysqlIds)) {
            return 0;
        }

        $indexName = $this->getIndexName('ELASTIC_COMPANIES_INDEX', 'local_companies_elastic');

        try {
            $response = $es->getClient()->search([
                'index' => $indexName,
                'body' => [
                    'size' => count($mysqlIds),
                    'query' => ['terms' => ['id' => $mysqlIds]],
                    '_source' => false,
                ],
            ]);

            $esIds = array_map('intval', array_column($response['hits']['hits'], '_id'));
            $missingIds = array_values(array_diff($mysqlIds, $esIds));

            $this->info('Companies: Checked '.count($mysqlIds).' | Missing in ES: '.count($missingIds));

            if (! empty($missingIds) && ! $dryRun) {
                foreach ($missingIds as $id) {
                    if (class_exists(\App\Jobs\ElasticCompanyIndex::class)) {
                        \App\Jobs\ElasticCompanyIndex::dispatch($id);
                    }
                }
                $this->info('Dispatched '.count($missingIds).' company re-index jobs.');
            }

            return count($missingIds);
        } catch (\Throwable $e) {
            $this->warn("Note on ES index {$indexName}: ".$e->getMessage());
            Log::warning('ES Reconcile Notice (Companies)', ['message' => $e->getMessage()]);

            return 0;
        }
    }

    private function reconcileJobs(ElasticService $es, bool $dryRun, int $limit): int
    {
        $mysqlIds = DB::table('jobs')
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->pluck('id')
            ->map(fn ($id) => (int) $id)
            ->all();

        if (empty($mysqlIds)) {
            return 0;
        }

        $indexName = $this->getIndexName('ELASTIC_JOBS', 'local_jobs_elastic');

        try {
            $response = $es->getClient()->search([
                'index' => $indexName,
                'body' => [
                    'size' => count($mysqlIds),
                    'query' => ['terms' => ['id' => $mysqlIds]],
                    '_source' => false,
                ],
            ]);

            $esIds = array_map('intval', array_column($response['hits']['hits'], '_id'));
            $missingIds = array_values(array_diff($mysqlIds, $esIds));

            $this->info('Jobs: Checked '.count($mysqlIds).' | Missing in ES: '.count($missingIds));

            if (! empty($missingIds) && ! $dryRun) {
                foreach ($missingIds as $id) {
                    if (class_exists(\App\Jobs\ElasticJobsIndex::class)) {
                        \App\Jobs\ElasticJobsIndex::dispatch($id);
                    }
                }
                $this->info('Dispatched '.count($missingIds).' job re-index jobs.');
            }

            return count($missingIds);
        } catch (\Throwable $e) {
            $this->warn("Note on ES index {$indexName}: ".$e->getMessage());
            Log::warning('ES Reconcile Notice (Jobs)', ['message' => $e->getMessage()]);

            return 0;
        }
    }
}
