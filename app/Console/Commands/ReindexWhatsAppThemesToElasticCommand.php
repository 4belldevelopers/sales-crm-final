<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\WhatsAppThemeCacheManager;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexWhatsAppThemesToElasticCommand
 *
 * Intent: Authoritatively synchronizes all MySQL WhatsApp Templates (temptype=9)
 * into Elasticsearch index ELASTIC_EMAIL_TEMPLATE / ELASTIC_WHATSAPP_THEMES (local_email_template),
 * prunes stale/orphaned documents, and purges all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan whatsapp-themes:reindex
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexWhatsAppThemesToElasticCommand extends Command
{
    protected $signature = 'whatsapp-themes:reindex {--force : Force complete reindex and purge orphaned documents}';

    protected $description = 'Reindex all WhatsApp Templates (temptype=9) into Elasticsearch and prune orphaned documents';

    public function handle(ElasticService $elasticService): int
    {
        $this->info('Starting WhatsApp Templates Elasticsearch reindexing...');

        $indexName = defined('ELASTIC_WHATSAPP_THEMES')
            ? ELASTIC_WHATSAPP_THEMES
            : (defined('ELASTIC_EMAIL_TEMPLATE') ? ELASTIC_EMAIL_TEMPLATE : 'local_email_template');

        if (! $elasticService->indexExists($indexName)) {
            $this->warn("Index {$indexName} does not exist. Creating with complete mappings...");

            $elasticService->getClient()->indices()->create([
                'index' => $indexName,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.requests.cache.enable' => true,
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'e_temp_id' => ['type' => 'long'],
                            'template_name' => ['type' => 'text'],
                            'display_name' => ['type' => 'text'],
                            'template_subject' => ['type' => 'text'],
                            'template_description' => ['type' => 'text'],
                            'template_message' => ['type' => 'text'],
                            'status' => ['type' => 'long'],
                            'curr_date' => ['type' => 'date'],
                            'temptype' => ['type' => 'long'],
                            'tempForInterview' => ['type' => 'long'],
                            'client_required' => ['type' => 'long'],
                            'company_id' => ['type' => 'long'],
                            'orderByShow' => ['type' => 'integer'],
                        ],
                    ],
                ],
            ]);
        }

        $templates = DB::table('th_email_template')
            ->where('temptype', 9)
            ->get();

        $this->info("Found {$templates->count()} WhatsApp templates in MySQL.");

        $validIds = [];
        $indexedCount = 0;

        foreach ($templates as $t) {
            $tempId = (int) $t->e_temp_id;
            $validIds[] = $tempId;

            $doc = [
                'id' => $tempId,
                'e_temp_id' => $tempId,
                'template_name' => (string) $t->template_name,
                'display_name' => (string) $t->display_name,
                'template_subject' => (string) ($t->template_subject ?? ''),
                'template_description' => (string) $t->template_description,
                'status' => (int) $t->status,
                'orderByShow' => ! is_null($t->orderByShow) && $t->orderByShow !== '' ? (int) $t->orderByShow : null,
                'tempForInterview' => (int) ($t->tempForInterview ?? 1),
                'client_required' => (int) ($t->client_required ?? 0),
                'temptype' => 9,
                'curr_date' => Carbon::parse($t->curr_date)->toIso8601String(),
            ];

            try {
                $elasticService->getClient()->update([
                    'index' => $indexName,
                    'id' => (string) $tempId,
                    'retry_on_conflict' => 5,
                    'refresh' => 'wait_for',
                    'body' => [
                        'doc' => $doc,
                        'doc_as_upsert' => true,
                        'detect_noop' => false,
                    ],
                ]);
                $indexedCount++;
            } catch (\Throwable $e) {
                $this->error("Failed to index WhatsApp theme #{$tempId}: ".$e->getMessage());
            }
        }

        // Detect and prune orphaned documents in Elasticsearch that no longer exist in MySQL
        $prunedCount = 0;
        try {
            $esSearch = $elasticService->getClient()->search([
                'index' => $indexName,
                'body' => [
                    'size' => 1000,
                    'query' => [
                        'bool' => [
                            'filter' => [
                                ['term' => ['temptype' => 9]],
                            ],
                        ],
                    ],
                ],
            ]);

            foreach ($esSearch['hits']['hits'] ?? [] as $hit) {
                $esTempId = (int) ($hit['_source']['e_temp_id'] ?? $hit['_id']);
                if (! in_array($esTempId, $validIds, true)) {
                    $this->warn("Pruning orphaned ES document #{$esTempId} not in MySQL...");
                    try {
                        $elasticService->getClient()->delete([
                            'index' => $indexName,
                            'id' => (string) $hit['_id'],
                            'refresh' => 'wait_for',
                        ]);
                        $prunedCount++;
                    } catch (\Throwable $delEx) {
                        $this->error("Could not delete orphan document #{$esTempId}: ".$delEx->getMessage());
                    }
                }
            }
        } catch (\Throwable $e) {
            $this->warn('Could not complete orphan pruning: '.$e->getMessage());
        }

        // Flush L2 Dragonfly / Redis caches
        WhatsAppThemeCacheManager::flushCaches();

        $this->info("Successfully indexed {$indexedCount} templates and pruned {$prunedCount} orphaned documents.");
        $this->info('L2 Cache flushed successfully.');

        return self::SUCCESS;
    }
}
