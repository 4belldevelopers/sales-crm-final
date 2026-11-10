<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminZoomSettingCacheManager;
use App\Services\SuperAdmin\SuperAdminZoomSettingReadService;
use Illuminate\Console\Command;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class HardenZoomDatabaseCommand
 *
 * Intent: Audits the `zoomsettings` database table, establishes an enforceable singleton
 * record with `id = 1`, adds the missing PRIMARY KEY constraint on `id`, and safely migrates
 * plaintext client secrets to encrypted storage at rest (using Laravel Crypt).
 *
 * Signature: php artisan zoom:harden-database {--dry-run}
 *
 * Author: Antigravity AI — Enterprise Zoom Integration Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class HardenZoomDatabaseCommand extends Command
{
    protected $signature = 'zoom:harden-database {--dry-run : Simulate modifications without committing}';

    protected $description = 'Audit zoomsettings table, enforce singleton id=1, add primary key constraint, and encrypt client secret at rest';

    public function handle(ElasticService $elasticService, SuperAdminZoomSettingReadService $readService): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $this->info('Starting Zoom Database Hardening & Integrity Audit...');
        if ($dryRun) {
            $this->warn('DRY RUN MODE: No modifications will be persisted.');
        }

        // 1. Audit existing records in zoomsettings
        $rows = DB::table('zoomsettings')->get();
        $this->line("Found {$rows->count()} records in `zoomsettings` table.");

        $primaryRecord = null;
        if ($rows->count() > 1) {
            $this->warn("Multiple records found in singleton table `zoomsettings`! Consolidating to id = 1...");

            // Take record id = 1 if exists, otherwise take first record
            $primaryRecord = $rows->firstWhere('id', 1) ?? $rows->first();

            if (! $dryRun) {
                DB::table('zoomsettings')->where('id', '!=', $primaryRecord->id)->delete();
                if ($primaryRecord->id != 1) {
                    DB::table('zoomsettings')->where('id', $primaryRecord->id)->update(['id' => 1]);
                    $primaryRecord->id = 1;
                }
                $this->info("Consolidated records into singleton record with id = 1.");
            }
        } elseif ($rows->count() === 1) {
            $primaryRecord = $rows->first();
            if ($primaryRecord->id != 1 && ! $dryRun) {
                DB::table('zoomsettings')->where('id', $primaryRecord->id)->update(['id' => 1]);
                $primaryRecord->id = 1;
                $this->info("Updated singleton record id to 1.");
            }
        } else {
            $this->warn("No records found in `zoomsettings`. Creating default singleton skeleton...");
            if (! $dryRun) {
                DB::table('zoomsettings')->insert([
                    'id' => 1,
                    'title' => 'JERO Official Zoom Integration',
                    'redirect_uri' => '',
                    'client_id' => '',
                    'client_secret' => '',
                    'zoom_access_token' => '',
                    'status' => 1,
                ]);
                $primaryRecord = DB::table('zoomsettings')->where('id', 1)->first();
            }
        }

        // 2. Audit and apply PRIMARY KEY constraint
        $primaryKeys = DB::select("SHOW KEYS FROM zoomsettings WHERE Key_name = 'PRIMARY'");
        if (empty($primaryKeys)) {
            $this->warn("PRIMARY KEY constraint is MISSING on `zoomsettings.id`! Applying constraint...");
            if (! $dryRun) {
                try {
                    DB::statement('ALTER TABLE `zoomsettings` ADD PRIMARY KEY (`id`)');
                    $this->info("Successfully added PRIMARY KEY constraint on `zoomsettings.id`.");
                } catch (Throwable $e) {
                    $this->error("Failed to add PRIMARY KEY constraint: ".$e->getMessage());
                    return self::FAILURE;
                }
            }
        } else {
            $this->info("PRIMARY KEY constraint already exists on `zoomsettings.id`.");
        }

        // 3. Audit and apply encryption migration on client_secret
        if ($primaryRecord && ! empty($primaryRecord->client_secret)) {
            $currentSecret = (string) $primaryRecord->client_secret;
            $isAlreadyEncrypted = false;

            try {
                Crypt::decryptString($currentSecret);
                $isAlreadyEncrypted = true;
                $this->info("Client Secret is already encrypted at rest.");
            } catch (DecryptException $e) {
                $isAlreadyEncrypted = false;
            }

            if (! $isAlreadyEncrypted) {
                $this->warn("Client Secret is stored in PLAINTEXT. Migrating to encrypted at rest...");
                if (! $dryRun) {
                    $encryptedSecret = Crypt::encryptString($currentSecret);
                    DB::table('zoomsettings')->where('id', 1)->update([
                        'client_secret' => $encryptedSecret,
                    ]);
                    $this->info("Successfully migrated Client Secret to AES-256-CBC encrypted format.");
                }
            }
        }

        // 4. Dual-Store Synchronization to Elasticsearch & Cache Invalidation
        if (! $dryRun) {
            $freshRecord = DB::table('zoomsettings')->where('id', 1)->first();
            if ($freshRecord) {
                $indexName = $readService->getIndexName();
                $doc = $readService->buildZoomSettingDocument($freshRecord);

                if ($elasticService->indexExists($indexName)) {
                    $elasticService->getClient()->index([
                        'index' => $indexName,
                        'id' => '1',
                        'body' => $doc,
                        'refresh' => 'wait_for',
                    ]);
                    $this->info("Synchronized hardened configuration to Elasticsearch index `{$indexName}`.");
                }

                SuperAdminZoomSettingCacheManager::flushCaches();
                $this->info("Flushed all L2 Dragonfly/Redis caches.");
            }
        }

        $this->info('Database hardening & integrity audit completed successfully.');

        return self::SUCCESS;
    }
}
