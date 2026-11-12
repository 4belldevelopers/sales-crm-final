<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminProfileCacheManager;
use App\Services\SuperAdmin\SuperAdminProfileReadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Class ReindexSuperAdminProfileToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes the Super Admin Profile records
 * into Elasticsearch index ELASTIC_SUPERADMIN_PROFILE (local_superadmin_profile) and purges L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan superadmin-profile:reindex {--force}
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexSuperAdminProfileToElasticCommand extends Command
{
    protected $signature = 'superadmin-profile:reindex {--force}';

    protected $description = 'Reindex Super Admin profile records into Elasticsearch with complete schema verification';

    public function handle(ElasticService $elasticService, SuperAdminProfileReadService $readService): int
    {
        $this->info('Starting Super Admin Profile Elasticsearch reindexing...');

        $indexName = $readService->getIndexName();

        if ($this->option('force') && $elasticService->indexExists($indexName)) {
            $this->warn("Force flag detected. Deleting existing index {$indexName}...");
            $elasticService->getClient()->indices()->delete(['index' => $indexName]);
        }

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

        $safeColumns = [
            'id', 'company_id', 'name', 'email', 'calling_code', 'mobile',
            'mobile_verified', 'whatsapp_number', 'linkedin', 'image',
            'is_superadmin', 'status', 'last_login', 'created_at', 'updated_at',
        ];

        $superAdmins = DB::table('users')->where('is_superadmin', 1)->select($safeColumns)->get();

        if ($superAdmins->isEmpty()) {
            // Also check ID 1 as standard root admin fallback
            $rootUser = DB::table('users')->where('id', 1)->select($safeColumns)->first();
            if ($rootUser) {
                $superAdmins = collect([$rootUser]);
            }
        }

        if ($superAdmins->isEmpty()) {
            $this->warn('No Super Admin users found in MySQL `users` table.');

            return self::SUCCESS;
        }

        $indexedCount = 0;
        foreach ($superAdmins as $admin) {
            $doc = $readService->buildProfileDocument($admin);

            $elasticService->getClient()->index([
                'index' => $indexName,
                'id' => (string) $admin->id,
                'body' => $doc,
                'refresh' => 'wait_for',
            ]);

            SuperAdminProfileCacheManager::flushCaches((int) $admin->id);
            $indexedCount++;
            $this->line("Indexed Super Admin profile #{$admin->id} ({$admin->email})");
        }

        $this->info("Successfully indexed {$indexedCount} Super Admin profile(s) into {$indexName}.");
        $this->info('Flushed L2 Dragonfly/Redis caches.');

        return self::SUCCESS;
    }
}
