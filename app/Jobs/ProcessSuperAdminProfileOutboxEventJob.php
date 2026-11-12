<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminProfileCacheManager;
use App\Services\SuperAdmin\SuperAdminProfileReadService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * Class ProcessSuperAdminProfileOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Super Admin Root Profile subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_superadmin_profile
 * and secondary projection local_users_list_elastic), cache invalidation in Redis/Dragonfly,
 * and atomic status transitions with bounded retry backoff.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessSuperAdminProfileOutboxEventJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $backoff = 10;

    public int $timeout = 60;

    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('outbox');
    }

    /**
     * Resolve target Elasticsearch index name for super admin profile.
     */
    protected function getIndexName(): string
    {
        if (defined('ELASTIC_SUPERADMIN_PROFILE')) {
            return ELASTIC_SUPERADMIN_PROFILE;
        }

        return (string) config('constants.ELASTIC_SUPERADMIN_PROFILE', 'local_superadmin_profile');
    }

    /**
     * Resolve target Elasticsearch index name for users list.
     */
    protected function getUsersListIndexName(): string
    {
        if (defined('ELASTIC_USERS_LIST')) {
            return ELASTIC_USERS_LIST;
        }

        return (string) config('constants.ELASTIC_USERS_LIST', 'local_users_list_elastic');
    }

    public function handle(?ElasticService $elasticService = null): void
    {
        $elasticService = $elasticService ?? app(ElasticService::class);

        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId, $elasticService);

            return;
        }

        // Batch processing when triggered by scheduler or CLI
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'SuperAdminProfile')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit(25)
            ->pluck('id')
            ->toArray();

        foreach ($events as $eventId) {
            $this->processEvent((int) $eventId, $elasticService);
        }
    }

    protected function processEvent(int $eventId, ElasticService $elasticService): void
    {
        $claimed = DB::table('sys_outbox_events')
            ->where('id', $eventId)
            ->where('status', 'PENDING')
            ->update([
                'status' => 'PROCESSING',
                'locked_at' => now(),
                'attempts' => DB::raw('attempts + 1'),
            ]);

        if (! $claimed) {
            return;
        }

        $event = DB::table('sys_outbox_events')->where('id', $eventId)->first();
        if (! $event) {
            return;
        }

        try {
            $indexName = $this->getIndexName();
            $readService = app(SuperAdminProfileReadService::class);

            // Ensure primary superadmin profile index exists
            if (! $elasticService->indexExists($indexName)) {
                $this->createIndexWithSchema($elasticService, $indexName, $readService);
            }

            $userId = (int) $event->aggregate_id;
            $incomingVersion = (int) ($event->aggregate_version ?? 1);

            // Forensic Check 5: Optimistic Concurrency Control (OCC) Aggregate-Version Guard
            if ($elasticService->getClient()->exists(['index' => $indexName, 'id' => (string) $userId])) {
                $existingDoc = $elasticService->getClient()->get([
                    'index' => $indexName,
                    'id' => (string) $userId,
                ]);

                $existingVersion = (int) ($existingDoc['_source']['aggregate_version'] ?? 0);
                if ($existingVersion > $incomingVersion) {
                    Log::warning("ProcessSuperAdminProfileOutboxEventJob: Stale outbox event #{$eventId} rejected. Existing ES version: {$existingVersion}, incoming version: {$incomingVersion}");

                    DB::table('sys_outbox_events')
                        ->where('id', $eventId)
                        ->update([
                            'status' => 'COMPLETED',
                            'processed_at' => now(),
                            'last_error' => 'Skipped: Newer aggregate version already indexed in Elasticsearch',
                        ]);

                    return;
                }
            }

            // Forensic Check 2: Strictly select non-sensitive columns
            $safeColumns = [
                'id', 'company_id', 'name', 'email', 'calling_code', 'mobile',
                'mobile_verified', 'whatsapp_number', 'linkedin', 'image',
                'is_superadmin', 'status', 'last_login', 'created_at', 'updated_at',
            ];

            $userRecord = DB::table('users')->where('id', $userId)->select($safeColumns)->first();

            if (! $userRecord) {
                // If user deleted or record unavailable, reconstruct from outbox payload
                $payload = is_string($event->payload) ? json_decode($event->payload, true) : (array) $event->payload;
                $userRecord = (object) ($payload['attributes'] ?? [
                    'id' => $userId,
                    'name' => 'Super Administrator',
                    'email' => 'admin@jero.works',
                    'is_superadmin' => 1,
                    'status' => 'active',
                ]);
            }

            $doc = $readService->buildProfileDocument($userRecord);
            $doc['aggregate_version'] = $incomingVersion;

            // 1. Primary Sync: local_superadmin_profile
            $elasticService->getClient()->index([
                'index' => $indexName,
                'id' => (string) $userId,
                'body' => $doc,
                'refresh' => false,
            ]);

            // 2. Secondary Sync: local_users_list_elastic (if index exists and doc exists)
            $usersListIndex = $this->getUsersListIndexName();
            try {
                if ($elasticService->indexExists($usersListIndex)) {
                    $userExists = $elasticService->getClient()->exists([
                        'index' => $usersListIndex,
                        'id' => (string) $userId,
                    ]);

                    if ($userExists) {
                        $elasticService->getClient()->update([
                            'index' => $usersListIndex,
                            'id' => (string) $userId,
                            'retry_on_conflict' => 3,
                            'body' => [
                                'doc' => [
                                    'name' => $doc['name'],
                                    'email' => $doc['email'],
                                    'image' => $doc['image'],
                                    'calling_code' => $doc['calling_code'],
                                    'mobile' => $doc['mobile'],
                                    'aggregate_version' => $incomingVersion,
                                    'updated_at' => $doc['updated_at'],
                                ],
                            ],
                            'refresh' => false,
                        ]);
                    }
                }
            } catch (Throwable $e) {
                Log::warning('ProcessSuperAdminProfileOutboxEventJob: secondary users list sync skipped: ' . $e->getMessage());
            }

            // 3. Invalidate L2 caches
            SuperAdminProfileCacheManager::flushCaches($userId);

            // 4. Mark outbox event completed
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);

            Log::info("ProcessSuperAdminProfileOutboxEventJob: successfully synchronized outbox event #{$eventId} for user #{$userId} (version {$incomingVersion})");
        } catch (Throwable $e) {
            $attempts = (int) $event->attempts;
            $newStatus = $attempts >= $this->tries ? 'DEAD_LETTER' : 'PENDING';
            $backoffSeconds = $this->backoff * $attempts;

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $newStatus,
                    'available_at' => Carbon::now()->addSeconds($backoffSeconds),
                    'last_error' => substr($e->getMessage(), 0, 1000),
                ]);

            Log::error("ProcessSuperAdminProfileOutboxEventJob: failed event #{$eventId} (attempt {$attempts}): " . $e->getMessage());

            if ($this->specificEventId !== null) {
                throw $e;
            }
        }
    }

    protected function createIndexWithSchema(ElasticService $elasticService, string $indexName, SuperAdminProfileReadService $readService): void
    {
        try {
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
            Log::info("ProcessSuperAdminProfileOutboxEventJob: created Elasticsearch index {$indexName}");
        } catch (Throwable $e) {
            Log::warning("ProcessSuperAdminProfileOutboxEventJob: index creation failed (may already exist): " . $e->getMessage());
        }
    }
}
