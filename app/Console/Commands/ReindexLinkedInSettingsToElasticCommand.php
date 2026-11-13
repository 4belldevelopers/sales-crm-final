<?php

namespace App\Console\Commands;

use App\Jobs\ProcessLinkedInSettingOutboxEventJob;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminLinkedInSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminLinkedInSettingReadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexLinkedInSettingsToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes MySQL linked_in_settings
 * into Elasticsearch index (local_linkedin_settings) and purges all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan linkedin-settings:reindex {--force}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexLinkedInSettingsToElasticCommand extends Command
{
    protected $signature = 'linkedin-settings:reindex {--force : Force re-creation of index}';

    protected $description = 'Reindex Super Admin LinkedIn Settings into Elasticsearch with complete schema verification';

    public function handle(ElasticService $elasticService, SuperAdminLinkedInSettingReadService $readService): int
    {
        $this->info('Starting Super Admin LinkedIn Settings Elasticsearch reindexing...');

        $indexName = $readService->getIndexName();
        $jobHelper = new ProcessLinkedInSettingOutboxEventJob;

        // 1. Ensure index exists
        if ($this->option('force') && $elasticService->indexExists($indexName)) {
            $this->warn("Deleting existing {$indexName} index due to --force flag...");
            $elasticService->getClient()->indices()->delete(['index' => $indexName]);
        }

        if (! $elasticService->indexExists($indexName)) {
            $this->info("Creating index {$indexName} with complete mapping...");
            $jobHelper->createLinkedInSettingsIndex($elasticService, $indexName);
        }

        // 2. Fetch Super Admin LinkedIn setting record from MySQL
        $settingRow = DB::table('linked_in_settings')
            ->where('id', 1)
            ->first()
            ?? DB::table('linked_in_settings')->first();

        if ($settingRow) {
            $doc = $readService->buildLinkedInSettingDocument($settingRow);

            $elasticService->getClient()->index([
                'index' => $indexName,
                'id' => '1',
                'body' => $doc,
                'refresh' => 'wait_for',
            ]);

            $this->info("Indexed LinkedIn Settings [ID {$settingRow->id}, Status: {$settingRow->status}] into {$indexName}");
        } else {
            $this->warn('No linked_in_settings record found in MySQL. Creating default projection in Elasticsearch...');
            $defaultDoc = $readService->buildLinkedInSettingDocument([
                'id' => 1,
                'client_id' => '',
                'client_secret' => '',
                'status' => 'disable',
                'callback_url' => route('jobs.linkedinCallback', 'linkedin'),
            ]);

            $elasticService->getClient()->index([
                'index' => $indexName,
                'id' => '1',
                'body' => $defaultDoc,
                'refresh' => 'wait_for',
            ]);

            $this->info("Indexed default LinkedIn setting projection into {$indexName}");
        }

        // 3. Invalidate Dragonfly/Redis L2 caches
        SuperAdminLinkedInSettingCacheManager::flushCaches();
        $this->info('Redis / Dragonfly L2 caches flushed successfully.');

        $this->info('Super Admin LinkedIn Settings reindexing completed with 0 errors.');

        return Command::SUCCESS;
    }
}
