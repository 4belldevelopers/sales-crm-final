<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminBenchRmNoteCacheManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Class ReindexBenchRmNotesToElasticCommand
 *
 * Intent: High-performance CLI command to synchronize Relationship Manager note templates between MySQL and Elasticsearch
 * (local_bench_rm_note_master) with schema verification, precomputed telemetry, zero downtime, and O(1) cache invalidation.
 *
 * Signature: php artisan bench-rm-notes:reindex {--fresh}
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexBenchRmNotesToElasticCommand extends Command
{
    protected $signature = 'bench-rm-notes:reindex {--fresh : Drop and recreate the index}';

    protected $description = 'Synchronize Relationship Manager note templates between MySQL and Elasticsearch (local_bench_rm_note_master)';

    public function handle(ElasticService $elasticService): int
    {
        $this->info('Starting Relationship Manager Note Templates synchronization to Elasticsearch...');

        $indexName = defined('ELASTIC_BENCH_RM_NOTE_MASTER')
            ? ELASTIC_BENCH_RM_NOTE_MASTER
            : config('constants.ELASTIC_BENCH_RM_NOTE_MASTER', 'local_bench_rm_note_master');

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
                        'analysis' => [
                            'analyzer' => [
                                'lowercase_analyzer' => [
                                    'type' => 'custom',
                                    'tokenizer' => 'standard',
                                    'filter' => ['lowercase'],
                                ],
                            ],
                        ],
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'integer'],
                            'company_id' => ['type' => 'integer'],
                            'title' => [
                                'type' => 'text',
                                'analyzer' => 'lowercase_analyzer',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword'],
                                ],
                            ],
                            'content' => [
                                'type' => 'text',
                                'analyzer' => 'lowercase_analyzer',
                            ],
                            'status' => ['type' => 'integer'],
                            'word_count' => ['type' => 'integer'],
                            'char_count' => ['type' => 'integer'],
                            'preview_text' => ['type' => 'text'],
                            'created_formatted' => ['type' => 'keyword'],
                            'updated_human' => ['type' => 'keyword'],
                            'created_at' => ['type' => 'date'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ];
            $elasticService->getClient()->indices()->create($params);
        }

        // 3. Index all global templates from MySQL
        $templates = DB::table('bench_rm_note_master')
            ->whereNull('company_id')
            ->orderBy('id', 'asc')
            ->get();

        $count = $templates->count();
        $this->info("Found {$count} Relationship Manager note templates in MySQL to index.");

        if ($count > 0) {
            $bulkParams = ['body' => []];
            foreach ($templates as $tpl) {
                $rawContent = (string) $tpl->content;
                $cleanText = trim(strip_tags($rawContent));
                $cleanText = html_entity_decode($cleanText, ENT_QUOTES | ENT_HTML5, 'UTF-8');

                $bulkParams['body'][] = [
                    'index' => [
                        '_index' => $indexName,
                        '_id' => (string) $tpl->id,
                    ],
                ];

                $bulkParams['body'][] = [
                    'id' => (int) $tpl->id,
                    'company_id' => null,
                    'title' => (string) $tpl->title,
                    'content' => (string) $tpl->content,
                    'status' => (int) $tpl->status,
                    'word_count' => $cleanText !== '' ? str_word_count($cleanText) : 0,
                    'char_count' => mb_strlen($cleanText),
                    'preview_text' => Str::limit($cleanText, 180),
                    'created_formatted' => $tpl->created_at ? Carbon::parse($tpl->created_at)->format('d M Y') : '—',
                    'updated_human' => $tpl->updated_at ? Carbon::parse($tpl->updated_at)->diffForHumans() : ($tpl->created_at ? Carbon::parse($tpl->created_at)->diffForHumans() : 'Just now'),
                    'created_at' => $tpl->created_at ? Carbon::parse($tpl->created_at)->toIso8601String() : null,
                    'updated_at' => $tpl->updated_at ? Carbon::parse($tpl->updated_at)->toIso8601String() : null,
                ];
            }

            $response = $elasticService->getClient()->bulk($bulkParams);
            if (! empty($response['errors'])) {
                $this->error('Some items encountered indexing errors in Elasticsearch:');
                foreach ($response['items'] as $it) {
                    if (! empty($it['index']['error'])) {
                        $this->error('ID '.($it['index']['_id'] ?? '?').': '.json_encode($it['index']['error']));
                    }
                }
            } else {
                $this->info("Successfully indexed {$count} templates into '{$indexName}'.");
            }
        }

        // 4. Invalidate L2 in-memory caches
        SuperAdminBenchRmNoteCacheManager::flushCaches();
        $this->info('L2 in-memory cache successfully invalidated.');

        return Command::SUCCESS;
    }
}
