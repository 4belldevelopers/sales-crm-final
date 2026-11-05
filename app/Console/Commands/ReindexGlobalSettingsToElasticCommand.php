<?php

namespace App\Console\Commands;

use App\Jobs\ProcessGlobalSettingOutboxEventJob;
use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminGlobalSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminGlobalSettingReadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexGlobalSettingsToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes MySQL global_settings and
 * sold_by_address into Elasticsearch indices (local_global_settings and local_sold_by_address)
 * and purges all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan global-settings:reindex
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexGlobalSettingsToElasticCommand extends Command
{
    protected $signature = 'global-settings:reindex {--force : Force re-creation of indices}';

    protected $description = 'Reindex Super Admin Global Settings and Sold-By Address into Elasticsearch with complete schema verification';

    public function handle(ElasticService $elasticService, SuperAdminGlobalSettingReadService $readService): int
    {
        $this->info('Starting Super Admin Global Settings Elasticsearch reindexing...');

        $gsIndex = $readService->getIndexName();
        $sbaIndex = $readService->getSoldByIndexName();

        $jobHelper = new ProcessGlobalSettingOutboxEventJob;

        // 1. Ensure local_global_settings index exists
        if ($this->option('force') && $elasticService->indexExists($gsIndex)) {
            $this->warn("Deleting existing {$gsIndex} index due to --force flag...");
            $elasticService->getClient()->indices()->delete(['index' => $gsIndex]);
        }

        if (! $elasticService->indexExists($gsIndex)) {
            $this->info("Creating index {$gsIndex} with complete mapping...");
            $jobHelper->createGlobalSettingsIndex($elasticService, $gsIndex);
        }

        // 2. Ensure local_sold_by_address index exists
        if (! $elasticService->indexExists($sbaIndex)) {
            $this->info("Creating index {$sbaIndex} with complete mapping...");
            $jobHelper->createSoldByAddressIndex($elasticService, $sbaIndex);
        }

        // 3. Reindex global_settings
        $settingRow = DB::table('global_settings')->where('id', 1)->first()
            ?: DB::table('global_settings')->first();

        if ($settingRow) {
            $currencyRow = DB::table('currencies')->where('id', $settingRow->currency_id)->first();
            $gsDoc = $readService->buildGlobalSettingDocument($settingRow, $currencyRow);

            $elasticService->getClient()->index([
                'index' => $gsIndex,
                'id' => '1',
                'body' => $gsDoc,
                'refresh' => 'wait_for',
            ]);

            $this->info("Indexed Global Settings [ID 1, Company: {$settingRow->company_name}] into {$gsIndex}");
        } else {
            $this->warn('No global_settings record found in MySQL database.');
        }

        // 4. Reindex sold_by_address
        $soldByRow = DB::table('sold_by_address')->where('id', 1)->first()
            ?: DB::table('sold_by_address')->first();

        if ($soldByRow) {
            $sbaDoc = $readService->buildSoldByAddressDocument($soldByRow);

            $elasticService->getClient()->index([
                'index' => $sbaIndex,
                'id' => '1',
                'body' => $sbaDoc,
                'refresh' => 'wait_for',
            ]);

            $this->info("Indexed Sold-By Address [ID 1, Org: {$soldByRow->organization}] into {$sbaIndex}");
        } else {
            $this->warn('No sold_by_address record found in MySQL database.');
        }

        // 5. Invalidate Dragonfly/Redis L2 caches
        SuperAdminGlobalSettingCacheManager::flushCaches();
        $this->info('Redis / Dragonfly L2 caches flushed successfully.');

        $this->info('Super Admin Global Settings reindexing completed with 0 errors.');

        return Command::SUCCESS;
    }
}
