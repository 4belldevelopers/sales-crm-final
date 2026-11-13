<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminLanguageCacheManager;
use App\Services\SuperAdmin\SuperAdminLanguageReadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ReindexLanguagesToElasticCommand
 *
 * Intent: Automated CLI tool to provision mappings and bulk-reindex all system
 * language settings from MySQL into Elasticsearch (local_language_settings).
 *
 * Usage: php artisan superadmin-language:reindex {--force}
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexLanguagesToElasticCommand extends Command
{
    protected $signature = 'superadmin-language:reindex {--force : Force recreation of index schema}';

    protected $description = 'Provision schema and denormalize all system language settings from MySQL to Elasticsearch';

    public function handle(ElasticService $elasticService, SuperAdminLanguageReadService $readService): int
    {
        $this->info('Starting Super Admin Language Settings Elasticsearch reindexing...');

        $indexName = $readService->getIndexName();
        $force = (bool) $this->option('force');

        try {
            if ($elasticService->indexExists($indexName)) {
                if ($force) {
                    $this->warn("Recreating index '{$indexName}' due to --force flag...");
                    $elasticService->getClient()->indices()->delete(['index' => $indexName]);
                    $elasticService->createIndex($indexName, $readService->getIndexMapping());
                    $this->info("Index '{$indexName}' created successfully.");
                } else {
                    $this->info("Index '{$indexName}' already exists. Proceeding with upsert...");
                }
            } else {
                $this->info("Index '{$indexName}' does not exist. Creating schema mapping...");
                $elasticService->createIndex($indexName, $readService->getIndexMapping());
                $this->info("Index '{$indexName}' created successfully.");
            }

            $count = DB::table('language_settings')->count();
            if ($count === 0) {
                $this->warn('No languages found in MySQL database.');

                return self::SUCCESS;
            }

            $this->info("Processing {$count} languages from MySQL into Elasticsearch...");
            $progressBar = $this->output->createProgressBar($count);
            $progressBar->start();

            $indexedCount = 0;
            DB::table('language_settings')->orderBy('id', 'asc')->chunk(100, function ($rows) use ($elasticService, $readService, $indexName, $progressBar, &$indexedCount) {
                $bulkParams = ['body' => []];

                foreach ($rows as $row) {
                    $doc = $readService->buildLanguageDocument($row);
                    $bulkParams['body'][] = [
                        'index' => [
                            '_index' => $indexName,
                            '_id' => (string) $row->id,
                        ],
                    ];
                    $bulkParams['body'][] = $doc;
                    $indexedCount++;
                    $progressBar->advance();
                }

                $elasticService->bulkIndex($bulkParams);
            });

            $progressBar->finish();
            $this->newLine(2);

            // Invalidate L2 caches
            SuperAdminLanguageCacheManager::flushCaches();

            $this->info("Successfully indexed {$indexedCount} languages into Elasticsearch '{$indexName}'.");
            $this->info('Redis/Dragonfly L2 cache invalidated and primed.');

            return self::SUCCESS;
        } catch (Throwable $e) {
            $this->newLine();
            $this->error('Reindexing failed: '.$e->getMessage());

            return self::FAILURE;
        }
    }
}
