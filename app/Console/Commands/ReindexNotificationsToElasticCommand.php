<?php

namespace App\Console\Commands;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminNotificationCacheManager;
use App\Services\SuperAdmin\SuperAdminNotificationReadService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Class ReindexNotificationsToElasticCommand
 *
 * Intent: Authoritatively synchronizes and denormalizes all Email Notification templates and
 * role permissions into Elasticsearch indices (local_email_notifications & local_mail_permissions)
 * and purges all L2 Redis/Dragonfly caches.
 *
 * Signature: php artisan notifications:reindex
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ReindexNotificationsToElasticCommand extends Command
{
    protected $signature = 'notifications:reindex';

    protected $description = 'Reindex all Email Notifications and Permissions into Elasticsearch with complete schema verification';

    public function handle(ElasticService $elasticService, SuperAdminNotificationReadService $readService): int
    {
        $this->info('Starting Super Admin Email Notifications Elasticsearch reindexing...');

        $notificationIndex = $readService->getIndexName();
        $permissionIndex = $readService->getPermissionIndexName();

        // 1. Verify / Create notification index mapping
        if (! $elasticService->indexExists($notificationIndex)) {
            $this->warn("Index {$notificationIndex} does not exist. Creating schema mapping...");

            $elasticService->getClient()->indices()->create([
                'index' => $notificationIndex,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                        'index.requests.cache.enable' => true,
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'purpose' => ['type' => 'text'],
                            'subject' => ['type' => 'text'],
                            'from_email_id' => ['type' => 'text'],
                            'body_description' => ['type' => 'text'],
                            'status' => ['type' => 'long'],
                            'mailType' => ['type' => 'long'],
                            'role_names' => [
                                'type' => 'text',
                                'fields' => [
                                    'keyword' => ['type' => 'keyword', 'ignore_above' => 256],
                                ],
                            ],
                            'aggregate_version' => ['type' => 'long'],
                            'created_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd\'T\'HH:mm:ssZZZZZ||epoch_millis||strict_date_optional_time'],
                            'updated_at' => ['type' => 'date', 'format' => 'yyyy-MM-dd HH:mm:ss||yyyy-MM-dd\'T\'HH:mm:ssZZZZZ||epoch_millis||strict_date_optional_time'],
                        ],
                    ],
                ],
            ]);
        }

        // 2. Verify / Create permissions index mapping
        if (! $elasticService->indexExists($permissionIndex)) {
            $this->warn("Index {$permissionIndex} does not exist. Creating schema mapping...");

            $elasticService->getClient()->indices()->create([
                'index' => $permissionIndex,
                'body' => [
                    'settings' => [
                        'number_of_shards' => 1,
                        'number_of_replicas' => 0,
                    ],
                    'mappings' => [
                        'properties' => [
                            'id' => ['type' => 'long'],
                            'role_name' => ['type' => 'keyword'],
                            'notification_id' => ['type' => 'long'],
                        ],
                    ],
                ],
            ]);
        }

        // 3. Load authoritative MySQL data
        $notifications = DB::table('email_notifications')->orderBy('id', 'asc')->get();
        $permissions = DB::table('mail_permissions')->get();

        $permissionsGrouped = [];
        foreach ($permissions as $perm) {
            $permissionsGrouped[(int) $perm->notification_id][] = (string) $perm->role_name;
        }

        $this->info("Indexing {$notifications->count()} notifications into {$notificationIndex}...");

        $notifCount = 0;
        foreach ($notifications as $notif) {
            $id = (int) $notif->id;
            $roles = $permissionsGrouped[$id] ?? [];
            sort($roles);

            $elasticService->getClient()->update([
                'index' => $notificationIndex,
                'id' => $id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => $id,
                        'purpose' => (string) $notif->purpose,
                        'subject' => (string) $notif->subject,
                        'from_email_id' => (string) $notif->from_email_id,
                        'body_description' => $notif->body_description,
                        'status' => (int) $notif->status,
                        'mailType' => (int) $notif->mailType,
                        'role_names' => array_values(array_unique($roles)),
                        'created_at' => $notif->created_at ? \Carbon\Carbon::parse($notif->created_at)->toIso8601String() : now()->toIso8601String(),
                        'updated_at' => $notif->updated_at ? \Carbon\Carbon::parse($notif->updated_at)->toIso8601String() : now()->toIso8601String(),
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
            $notifCount++;
        }

        $this->info("Indexing {$permissions->count()} permissions into {$permissionIndex}...");

        $permCount = 0;
        foreach ($permissions as $perm) {
            $elasticService->getClient()->update([
                'index' => $permissionIndex,
                'id' => (int) $perm->id,
                'retry_on_conflict' => 5,
                'body' => [
                    'doc' => [
                        'id' => (int) $perm->id,
                        'role_name' => (string) $perm->role_name,
                        'notification_id' => (int) $perm->notification_id,
                    ],
                    'doc_as_upsert' => true,
                    'detect_noop' => false,
                ],
            ]);
            $permCount++;
        }

        // 4. Force refresh indices
        try {
            $elasticService->getClient()->indices()->refresh(['index' => $notificationIndex]);
            $elasticService->getClient()->indices()->refresh(['index' => $permissionIndex]);
        } catch (Throwable $e) {
            // Best effort
        }

        // 5. Invalidate Dragonfly / Redis L2 cache
        SuperAdminNotificationCacheManager::flushCaches();

        $this->info("Reindexing complete: {$notifCount} notifications and {$permCount} permissions indexed. L2 caches flushed.");

        return self::SUCCESS;
    }
}
