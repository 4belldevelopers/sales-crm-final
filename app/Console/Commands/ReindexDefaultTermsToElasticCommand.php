<?php

namespace App\Console\Commands;

use App\Services\AgencyCommercialTermsService;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminDefaultTermsCacheManager;
use App\Services\SuperAdmin\SuperAdminDefaultTermsReadService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexDefaultTermsToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes all 13 default compliance terms,
 * offer letters, and agency commercial defaults into Elasticsearch index ELASTIC_DEFAULT_TERMS
 * (local_default_terms) and purges all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan default-terms:reindex
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexDefaultTermsToElasticCommand extends Command
{
    protected $signature = 'default-terms:reindex';

    protected $description = 'Reindex all 13 Default Terms into Elasticsearch with complete schema and token metadata';

    public function handle(ElasticService $elasticService, SuperAdminDefaultTermsReadService $readService): int
    {
        $this->info('Starting Default Terms Elasticsearch reindexing...');

        $indexName = $readService->getIndexName();

        if (! $elasticService->indexExists($indexName)) {
            $this->warn("Elasticsearch index {$indexName} does not exist. Creating with complete mappings...");

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
                            'seq_id' => ['type' => 'keyword'],
                            'encrypted_id' => ['type' => 'keyword'],
                            'title' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'audience' => ['type' => 'keyword'],
                            'audience_key' => ['type' => 'keyword'],
                            'category' => ['type' => 'keyword'],
                            'category_label' => ['type' => 'keyword'],
                            'icon' => ['type' => 'keyword'],
                            'db_field' => ['type' => 'keyword'],
                            'storage' => ['type' => 'keyword'],
                            'raw_content' => ['type' => 'text'],
                            'clean_text' => ['type' => 'text'],
                            'is_configured' => ['type' => 'boolean'],
                            'char_count' => ['type' => 'integer'],
                            'word_count' => ['type' => 'integer'],
                            'excerpt' => ['type' => 'text'],
                            'fallback_to' => ['type' => 'keyword'],
                            'tokens' => ['type' => 'keyword'],
                            'scope_note' => ['type' => 'text'],
                            'public_route' => ['type' => 'keyword'],
                            'updated_at' => ['type' => 'date'],
                        ],
                    ],
                ],
            ]);
            $this->info("Created Elasticsearch index: {$indexName}");
        }

        $settings = DB::table('global_settings')->first() ?: (object) [];
        $agencyDefaults = app(AgencyCommercialTermsService::class)->getGlobalDefault();

        $termsDefinitions = $readService->buildTermsDefinitions($settings, $agencyDefaults);

        $this->info('Found '.count($termsDefinitions).' default terms definitions to index.');

        $bulkParams = ['body' => []];

        foreach ($termsDefinitions as $id => $def) {
            $rawContent = (string) $def['raw_content'];
            $stripped = trim(strip_tags($rawContent));
            $isConfigured = strlen($stripped) > 0;

            if ($id === 13) {
                $isConfigured = ! empty($agencyDefaults) && ! empty((array) $agencyDefaults);
                $stripped = 'Full-Time Model: '.($agencyDefaults->fulltime_fee_model ?? 'percentage').' ('.($agencyDefaults->fulltime_success_fee_percentage ?? 8.33).'% CTC), Payment Days: '.($agencyDefaults->fulltime_payment_terms_days ?? 30).', Replacement: '.($agencyDefaults->fulltime_replacement_period_days ?? 90).' Days; Contract Model: '.($agencyDefaults->contract_billing_model ?? 'monthly').' ('.($agencyDefaults->contract_billing_cycle ?? 'Monthly in Advance').')';
            }

            $charCount = strlen($stripped);
            $wordCount = $charCount > 0 ? str_word_count($stripped) : 0;
            $excerpt = $isConfigured
                ? mb_strimwidth(preg_replace('/\s+/', ' ', $stripped), 0, 140, '...')
                : (! empty($def['fallback_to'])
                    ? 'No custom template configured. Automatically inherits '.$def['fallback_to'].' as starter copy.'
                    : 'No content configured yet. Click Edit to establish master policy.');

            $doc = [
                'id' => (int) $id,
                'encrypted_id' => encrypt($id),
                'seq_id' => sprintf('#TERM-%03d', $id),
                'title' => $def['title'],
                'audience' => $def['audience'],
                'audience_key' => $def['audience_key'],
                'category' => $def['category'],
                'category_label' => $def['category_label'],
                'icon' => $def['icon'],
                'db_field' => $def['db_field'],
                'storage' => $def['storage'],
                'raw_content' => $rawContent,
                'clean_text' => $stripped,
                'is_configured' => $isConfigured,
                'char_count' => $charCount,
                'word_count' => $wordCount,
                'excerpt' => $excerpt,
                'fallback_to' => $def['fallback_to'],
                'tokens' => $def['tokens'],
                'scope_note' => $def['scope_note'],
                'public_route' => $def['public_route'],
                'updated_at' => Carbon::now()->toIso8601String(),
            ];

            $bulkParams['body'][] = [
                'index' => [
                    '_index' => $indexName,
                    '_id' => (string) $id,
                ],
            ];
            $bulkParams['body'][] = $doc;

            $this->line(sprintf('Indexed [%s] %s (%s chars, %s words)', $doc['seq_id'], $doc['title'], number_format($charCount), number_format($wordCount)));
        }

        if (! empty($bulkParams['body'])) {
            $response = $elasticService->bulkIndex($bulkParams);
            if (! empty($response['errors'])) {
                $this->error('Some documents encountered bulk indexing errors.');
            } else {
                $this->info('All '.count($termsDefinitions).' terms bulk indexed successfully into Elasticsearch.');
            }
        }

        // Invalidate all L2 Redis/Dragonfly caches
        SuperAdminDefaultTermsCacheManager::flushCaches();
        $this->info('Flushed L2 Redis/Dragonfly Default Terms cache (generation version incremented).');

        return self::SUCCESS;
    }
}
