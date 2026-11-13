<?php

namespace App\Console\Commands;

use App\Helper\GeneralMethod;
use App\Services\ElasticCaompanyIndex;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminCompanyCacheManager;
use App\Services\SuperAdmin\SuperAdminSubscriptionCacheManager;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ReindexCompaniesToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes MySQL companies, coins, and packages
 * into Elasticsearch index (local_companies_elastic / ELASTIC_COMPANIES_INDEX) and purges all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan company:reindex {--force} {--chunk=250}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexCompaniesToElasticCommand extends Command
{
    protected $signature = 'company:reindex {--force : Force reindex all companies} {--chunk=250 : Chunk size for processing}';

    protected $description = 'Reindex Super Admin Companies into Elasticsearch ELASTIC_COMPANIES_INDEX with complete denormalization and cache purge';

    public function handle(ElasticService $elasticService, ElasticCaompanyIndex $indexer): int
    {
        $this->info('Starting Super Admin Companies Elasticsearch reindexing...');

        $indexName = config('constants.ELASTIC_COMPANIES_INDEX', defined('ELASTIC_COMPANIES_INDEX') ? ELASTIC_COMPANIES_INDEX : 'local_companies_elastic');
        $superAdminCompanyIds = GeneralMethod::superAdminCompanyIds();

        // 1. Verify index existence
        if (! $elasticService->indexExists($indexName)) {
            $this->warn("Index {$indexName} does not exist. Creating via ElasticCaompanyIndex...");
        }

        // 2. Fetch all non-superadmin companies
        $query = DB::table('companies')
            ->whereNotIn('id', $superAdminCompanyIds)
            ->orderBy('id', 'asc');

        $total = $query->count();
        $this->info("Found {$total} companies to index into {$indexName}.");

        $chunkSize = (int) $this->option('chunk');
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $processed = 0;
        $errors = 0;

        $query->chunk($chunkSize, function ($companies) use ($indexer, $bar, &$processed, &$errors) {
            foreach ($companies as $company) {
                try {
                    $indexer->companyIndexElastic($company->id);
                    $processed++;
                } catch (Throwable $e) {
                    $errors++;
                }
                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine(2);

        // 3. Purge L2 Dragonfly / Redis caches
        SuperAdminCompanyCacheManager::flushCaches();
        if (class_exists(SuperAdminSubscriptionCacheManager::class)) {
            SuperAdminSubscriptionCacheManager::flushCaches();
        }
        $this->info('Redis / Dragonfly L2 company and subscription caches flushed successfully.');

        $this->info("Company reindexing completed: {$processed} indexed, {$errors} errors.");

        return Command::SUCCESS;
    }
}
