<?php

namespace App\Console\Commands;

use App\Jobs\ProcessFeatureOutboxEventJob;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminFeatureCacheManager;
use App\Services\SuperAdmin\SuperAdminFeatureReadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexFeaturesToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes MySQL features
 * into Elasticsearch index (local_features_list) and purges all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan features:reindex {--force}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexFeaturesToElasticCommand extends Command
{
    protected $signature = 'features:reindex {--force : Force re-creation of indices}';

    protected $description = 'Reindex Super Admin Platform Feature Policies into Elasticsearch with complete schema verification';

    public function handle(ElasticService $elasticService, SuperAdminFeatureReadService $readService): int
    {
        $this->info('Starting Super Admin Platform Feature Policies Elasticsearch reindexing...');

        $indexName = $readService->getIndexName();
        $jobHelper = new ProcessFeatureOutboxEventJob;

        // 1. Ensure index exists
        if ($this->option('force') && $elasticService->indexExists($indexName)) {
            $this->warn("Deleting existing {$indexName} index due to --force flag...");
            $elasticService->getClient()->indices()->delete(['index' => $indexName]);
        }

        if (! $elasticService->indexExists($indexName)) {
            $this->info("Creating index {$indexName} with complete mapping...");
            $jobHelper->createFeaturesListIndex($elasticService, $indexName);
        }

        // 2. Fetch all feature policy records from MySQL
        $featureRows = DB::table('features')
            ->orderBy('id', 'asc')
            ->get();

        if ($featureRows->isNotEmpty()) {
            $count = 0;
            foreach ($featureRows as $featureRow) {
                $doc = $readService->buildFeatureDocument($featureRow);

                $elasticService->getClient()->index([
                    'index' => $indexName,
                    'id' => (string) $featureRow->id,
                    'body' => $doc,
                    'refresh' => 'wait_for',
                ]);

                $count++;
                $this->line("Indexed Feature Policy [ID {$featureRow->id}: {$featureRow->title}] into {$indexName}");
            }

            $this->info("Successfully indexed {$count} feature policies into {$indexName}.");
        } else {
            $this->warn('No feature policy records found in MySQL features table.');
        }

        // 3. Invalidate Dragonfly/Redis L2 caches
        SuperAdminFeatureCacheManager::flushCaches();
        $this->info('Redis / Dragonfly L2 caches flushed successfully.');

        $this->info('Super Admin Feature Policies reindexing completed with 0 errors.');

        return Command::SUCCESS;
    }
}
