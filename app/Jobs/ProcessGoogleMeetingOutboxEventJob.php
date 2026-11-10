<?php

namespace App\Jobs;

use App\Services\ElasticService;
use App\Services\SuperAdmin\SuperAdminGoogleMeetingCacheManager;
use App\Services\SuperAdmin\SuperAdminGoogleMeetingReadService;
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
 * Class ProcessGoogleMeetingOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Super Admin Google Meet Link Pool subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_google_meeting_lists),
 * version-aware stale event drops, tombstone resurrection protection, and O(1) Redis/Dragonfly cache invalidation.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessGoogleMeetingOutboxEventJob implements ShouldQueue
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
     * Resolve target Elasticsearch index name.
     */
    protected function getIndexName(): string
    {
        if (defined('ELASTIC_GOOGLE_MEETING_LISTS')) {
            return ELASTIC_GOOGLE_MEETING_LISTS;
        }

        return (string) config('constants.ELASTIC_GOOGLE_MEETING_LISTS', 'local_google_meeting_lists');
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
            ->where('aggregate_type', 'GoogleMeetingLink')
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
            $readService = app(SuperAdminGoogleMeetingReadService::class);

            // Ensure index exists
            $readService->ensureIndexExists();

            $payload = is_string($event->payload) ? json_decode($event->payload, true) : (array) $event->payload;
            $eventType = $event->event_type;
            $aggregateVersion = (int) $event->aggregate_version;

            switch ($eventType) {
                case 'GOOGLE_MEETING_LINKS_CREATED':
                    $createdIds = (array) ($payload['ids'] ?? [$event->aggregate_id]);
                    $rows = DB::table('google_meeting_lists')->whereIn('id', $createdIds)->get();

                    foreach ($rows as $row) {
                        if (SuperAdminGoogleMeetingCacheManager::hasTombstone($row->id, $aggregateVersion)) {
                            Log::info("[OUTBOX] Google Meet link #{$row->id} has tombstone, dropping resurrecting upsert.");

                            continue;
                        }

                        $doc = $readService->buildDocument($row, $aggregateVersion);
                        $elasticService->getClient()->index([
                            'index' => $indexName,
                            'id' => (string) $row->id,
                            'body' => $doc,
                            'refresh' => 'wait_for',
                        ]);
                    }
                    break;

                case 'GOOGLE_MEETING_LINK_UPDATED':
                case 'GOOGLE_MEETING_LINK_STATUS_UPDATED':
                    $linkId = (int) $event->aggregate_id;

                    if (SuperAdminGoogleMeetingCacheManager::hasTombstone($linkId, $aggregateVersion)) {
                        Log::info("[OUTBOX] Google Meet link #{$linkId} has tombstone, dropping upsert.");
                        break;
                    }

                    $row = DB::table('google_meeting_lists')->where('id', $linkId)->first();
                    if ($row) {
                        // Check if existing document version is newer
                        try {
                            $existing = $elasticService->getClient()->get([
                                'index' => $indexName,
                                'id' => (string) $linkId,
                            ]);
                            $existingVersion = (int) ($existing['_source']['version'] ?? 0);
                            if ($existingVersion > $aggregateVersion) {
                                Log::info("[OUTBOX] Google Meet link #{$linkId} stale event dropped. (Event v{$aggregateVersion} < ES v{$existingVersion})");
                                break;
                            }
                        } catch (Throwable) {
                            // Doc does not exist yet in ES
                        }

                        $doc = $readService->buildDocument($row, $aggregateVersion);
                        $elasticService->getClient()->index([
                            'index' => $indexName,
                            'id' => (string) $linkId,
                            'body' => $doc,
                            'refresh' => 'wait_for',
                        ]);
                    }
                    break;

                case 'GOOGLE_MEETING_LINKS_DELETED':
                    $deletedIds = (array) ($payload['ids'] ?? [$event->aggregate_id]);
                    foreach ($deletedIds as $delId) {
                        try {
                            $elasticService->getClient()->delete([
                                'index' => $indexName,
                                'id' => (string) $delId,
                                'refresh' => 'wait_for',
                            ]);
                        } catch (Throwable $e) {
                            // Suppress 404 not found if already deleted
                        }
                    }
                    break;
            }

            // Mark outbox event as COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => Carbon::now(),
                ]);

            // Flush L2 in-memory caches
            SuperAdminGoogleMeetingCacheManager::flushCaches();

        } catch (Throwable $e) {
            $status = ($event->attempts >= 3) ? 'DEAD_LETTER' : 'PENDING';

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $status,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => Carbon::now()->addSeconds(10 * $event->attempts),
                ]);

            Log::error("[OUTBOX] ProcessGoogleMeetingOutboxEventJob failed for event #{$eventId}: ".$e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
