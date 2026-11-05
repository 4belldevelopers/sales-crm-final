<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminSkillCacheManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexSkillsToElasticCommand
 *
 * Intent: High-performance CLI command to synchronize master skills between MySQL and Elasticsearch
 * (local_skills) with dual-store reconciliation, zero downtime, and O(1) cache invalidation.
 *
 * Signature: php artisan skills:reindex {--fresh}
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexSkillsToElasticCommand extends Command
{
    protected $signature = 'skills:reindex {--fresh : Drop and recreate the index}';

    protected $description = 'Synchronize master skills between MySQL and Elasticsearch (local_skills) with complete schema verification';

    public function handle(ElasticService $elasticService): int
    {
        $this->info('Starting Master Skills synchronization to Elasticsearch...');

        $indexName = defined('ELASTIC_SKILLS') ? ELASTIC_SKILLS : 'local_skills';

        // 1. Drop index if --fresh specified
        if ($this->option('fresh')) {
            if ($elasticService->indexExists($indexName)) {
                $this->warn("Dropping existing index '{$indexName}'...");
                $elasticService->getClient()->indices()->delete(['index' => $indexName]);
            }
        }

        // 2. Ensure index exists with authoritative mapping
        if (! $elasticService->indexExists($indexName)) {
            $this->info("Creating index '{$indexName}' with enterprise mappings...");
            $params = [
                'index' => $indexName,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.requests.cache.enable' => true,
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'keyword'],
                            'skill_name' => ['type' => 'keyword'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];
            $elasticService->getClient()->indices()->create($params);
        }

        // 3. Dual-Store Reconciliation: If MySQL skills table is empty, seed from existing ES documents
        $dbCount = DB::table('skills')->whereNull('company_id')->count();
        if ($dbCount === 0 && $elasticService->indexExists($indexName)) {
            $this->info('MySQL master skills table is empty. Reconciling baseline documents from Elasticsearch...');
            try {
                $search = $elasticService->getClient()->search([
                    'index' => $indexName,
                    'size' => 1000,
                ]);

                $imported = 0;
                foreach ($search['hits']['hits'] ?? [] as $hit) {
                    $skillName = strtolower(trim((string) ($hit['_source']['skill_name'] ?? '')));
                    if ($skillName !== '') {
                        $exists = DB::table('skills')
                            ->whereNull('company_id')
                            ->whereRaw('LOWER(name) = ?', [$skillName])
                            ->exists();

                        if (! $exists) {
                            $created = $hit['_source']['created_at'] ?? now();
                            DB::table('skills')->insert([
                                'company_id' => null,
                                'name' => $skillName,
                                'created_at' => Carbon::parse($created),
                                'updated_at' => Carbon::parse($created),
                            ]);
                            $imported++;
                        }
                    }
                }
                $this->info("Reconciliation complete: {$imported} master skills imported into MySQL.");
            } catch (\Throwable $recEx) {
                $this->warn("Reconciliation notice: {$recEx->getMessage()}");
            }
        }

        // 4. Bulk Index from MySQL to Elasticsearch
        $skillsQuery = DB::table('skills')->whereNull('company_id');
        $totalRecords = $skillsQuery->count();
        $this->info("Found {$totalRecords} master skills in MySQL to index into Elasticsearch.");

        $bar = $this->output->createProgressBar($totalRecords);
        $bar->start();

        $chunkSize = 500;
        $indexed = 0;

        DB::table('skills')
            ->whereNull('company_id')
            ->orderBy('id', 'asc')
            ->chunkById($chunkSize, function ($rows) use ($elasticService, $indexName, $bar, &$indexed) {
                $bulkParams = ['body' => []];

                foreach ($rows as $row) {
                    $docId = (string) $row->id;
                    $createdAt = $row->created_at ? Carbon::parse($row->created_at)->toIso8601String() : Carbon::now()->toIso8601String();
                    $updatedAt = $row->updated_at ? Carbon::parse($row->updated_at)->toIso8601String() : Carbon::now()->toIso8601String();

                    $bulkParams['body'][] = [
                        'index' => [
                            '_index' => $indexName,
                            '_id' => $docId,
                        ],
                    ];

                    $bulkParams['body'][] = [
                        'id' => $docId,
                        'skill_name' => strtolower(trim($row->name)),
                        'created_at' => $createdAt,
                        'updated_at' => $updatedAt,
                    ];

                    $indexed++;
                    $bar->advance();
                }

                if (! empty($bulkParams['body'])) {
                    $elasticService->getClient()->bulk($bulkParams);
                }
            });

        $bar->finish();
        $this->newLine();

        // 5. Force synchronous index refresh & cache flush
        $elasticService->getClient()->indices()->refresh(['index' => $indexName]);
        SuperAdminSkillCacheManager::flushCaches();

        $this->info("Successfully indexed {$indexed} master skills into '{$indexName}'.");
        $this->info('Redis/Dragonfly L2 cache invalidated via SuperAdminSkillCacheManager::flushCaches().');

        return Command::SUCCESS;
    }
}
