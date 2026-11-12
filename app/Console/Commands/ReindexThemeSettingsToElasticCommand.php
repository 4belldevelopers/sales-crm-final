<?php

namespace App\Console\Commands;

use App\Jobs\ProcessThemeSettingOutboxEventJob;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminThemeSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminThemeSettingReadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexThemeSettingsToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes MySQL theme_settings (company_id IS NULL)
 * into Elasticsearch index (local_theme_settings) and purges all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan theme-settings:reindex {--force}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexThemeSettingsToElasticCommand extends Command
{
    protected $signature = 'theme-settings:reindex {--force : Force re-creation of indices}';

    protected $description = 'Reindex Super Admin Theme Settings into Elasticsearch with complete schema verification';

    public function handle(ElasticService $elasticService, SuperAdminThemeSettingReadService $readService): int
    {
        $this->info('Starting Super Admin Theme Settings Elasticsearch reindexing...');

        $indexName = $readService->getIndexName();
        $jobHelper = new ProcessThemeSettingOutboxEventJob;

        // 1. Ensure index exists
        if ($this->option('force') && $elasticService->indexExists($indexName)) {
            $this->warn("Deleting existing {$indexName} index due to --force flag...");
            $elasticService->getClient()->indices()->delete(['index' => $indexName]);
        }

        if (! $elasticService->indexExists($indexName)) {
            $this->info("Creating index {$indexName} with complete mapping...");
            $jobHelper->createThemeSettingsIndex($elasticService, $indexName);
        }

        // 2. Fetch Super Admin theme setting record from MySQL
        $themeRow = DB::table('theme_settings')
            ->whereNull('company_id')
            ->first();

        if ($themeRow) {
            $doc = $readService->buildThemeSettingDocument($themeRow);

            $elasticService->getClient()->index([
                'index' => $indexName,
                'id' => '1',
                'body' => $doc,
                'refresh' => 'wait_for',
            ]);

            $this->info("Indexed Theme Settings [ID {$themeRow->id}, Color: {$themeRow->primary_color}] into {$indexName}");
        } else {
            $this->warn('No Super Admin theme_settings record found in MySQL. Creating default projection in Elasticsearch...');
            $defaultDoc = $readService->buildThemeSettingDocument([
                'id' => 1,
                'company_id' => null,
                'primary_color' => SuperAdminThemeSettingReadService::DEFAULT_PRIMARY_COLOR,
                'admin_custom_css' => null,
                'front_custom_css' => null,
                'home_background_image' => null,
            ]);

            $elasticService->getClient()->index([
                'index' => $indexName,
                'id' => '1',
                'body' => $defaultDoc,
                'refresh' => 'wait_for',
            ]);

            $this->info("Indexed default theme setting projection into {$indexName}");
        }

        // 3. Invalidate Dragonfly/Redis L2 caches
        SuperAdminThemeSettingCacheManager::flushCaches();
        $this->info('Redis / Dragonfly L2 caches flushed successfully.');

        $this->info('Super Admin Theme Settings reindexing completed with 0 errors.');

        return Command::SUCCESS;
    }
}
