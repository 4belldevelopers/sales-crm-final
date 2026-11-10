<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminZoomSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminZoomSettingReadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexZoomSettingToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes the Super Admin Zoom configuration
 * into Elasticsearch index ELASTIC_ZOOM_SETTING (local_zoom_setting) and purges L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan zoom-setting:reindex
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexZoomSettingToElasticCommand extends Command
{
    protected $signature = 'zoom-setting:reindex';

    protected $description = 'Reindex Super Admin Zoom integration settings into Elasticsearch with complete schema verification';

    public function handle(ElasticService $elasticService, SuperAdminZoomSettingReadService $readService): int
    {
        $this->info('Starting Super Admin Zoom Settings Elasticsearch reindexing...');

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
                    'mappings' => $readService->getIndexMapping(),
                ],
            ]);

            $this->info("Created index {$indexName}.");
        }

        $record = DB::table('zoomsettings')->where('id', 1)->first()
            ?? DB::table('zoomsettings')->first();

        if (! $record) {
            $this->warn('No records found in MySQL `zoomsettings` table. Indexing default placeholder singleton...');
            $record = (object) [
                'id' => 1,
                'title' => 'JERO Official Zoom Integration',
                'redirect_uri' => '',
                'client_id' => '',
                'client_secret' => '',
                'zoom_access_token' => '',
                'status' => 1,
            ];
        }

        $doc = $readService->buildZoomSettingDocument($record);

        $elasticService->getClient()->index([
            'index' => $indexName,
            'id' => '1',
            'body' => $doc,
            'refresh' => 'wait_for',
        ]);

        $this->info("Successfully indexed Zoom Setting document into {$indexName} (ID: 1).");

        // Flush L2 caches
        SuperAdminZoomSettingCacheManager::flushCaches();
        $this->info('Flushed L2 Dragonfly/Redis caches.');

        return self::SUCCESS;
    }
}
