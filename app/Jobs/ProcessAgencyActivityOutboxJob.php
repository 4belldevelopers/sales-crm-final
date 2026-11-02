<?php

namespace App\Jobs;

use App\Events\AgencyActivityLoggedEvent;
use App\Services\ElasticService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class ProcessAgencyActivityOutboxJob
 *
 * Dedicated Transactional Outbox Worker for Agency 360° Activity Events.
 * Guarantees at-least-once delivery, recoverable Elasticsearch indexing,
 * multi-tenant cache invalidation, and real-time Reverb WebSocket broadcasts.
 */
class ProcessAgencyActivityOutboxJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 5;

    public int $backoff = 10;

    public int $timeout = 60;

    public function __construct(
        public readonly ?int $specificEventId = null
    ) {
        $this->onQueue('agency-activity-outbox');
    }

    public function handle(ElasticService $elasticService): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId, $elasticService);

            return;
        }

        // Self-healing: reclaim stranded events locked > 5 minutes ago due to crash
        DB::table('sys_outbox_events')
            ->where('aggregate_type', 'AgencyActivity')
            ->where('status', 'PROCESSING')
            ->where('locked_at', '<', now()->subMinutes(5))
            ->update([
                'status' => 'PENDING',
                'available_at' => now(),
            ]);

        // Batch processing
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'AgencyActivity')
            ->whereIn('status', ['PENDING', 'FAILED'])
            ->where('available_at', '<=', now())
            ->where('attempts', '<', $this->tries)
            ->orderBy('id', 'asc')
            ->limit(50)
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
            ->where(function ($q) {
                $q->whereIn('status', ['PENDING', 'FAILED'])
                    ->orWhere(function ($sub) {
                        $sub->where('status', 'PROCESSING')
                            ->where('locked_at', '<', now()->subMinutes(5));
                    });
            })
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
            $payload = json_decode($event->payload, true) ?? [];
            $uuid = (string) ($payload['event_uuid'] ?? '');
            $agencyId = (int) ($event->aggregate_id ?? $payload['agency_company_id'] ?? 0);
            $serviceCompanyId = (int) ($event->tenant_id ?? $payload['service_company_id'] ?? 0);
            $version = (int) ($event->aggregate_version ?? 1);

            $indexName = defined('ELASTIC_AGENCY_ACTIVITY_STREAM') ? ELASTIC_AGENCY_ACTIVITY_STREAM : 'local_agency_activity_stream';

            // 1. Elasticsearch Read Store Indexing (Recoverable)
            if ($elasticService->indexExists($indexName)) {
                $elasticService->getClient()->index([
                    'index' => $indexName,
                    'id' => $uuid,
                    'body' => $payload,
                    'refresh' => true,
                ]);
            }

            // 2. Multi-Tenant Atomic Cache Invalidation (Redis Dragonfly)
            $newVersion = $version;
            try {
                if ($serviceCompanyId > 0 && $agencyId > 0) {
                    $newVersion = (int) Redis::incr("ats:agency_act_ver:{$serviceCompanyId}:{$agencyId}");
                }
                if ($agencyId > 0) {
                    Redis::incr("ats:agency_act_ver:{$agencyId}:{$agencyId}");
                }
            } catch (\Throwable) {
            }

            // 3. Real-time Reverb WebSocket Broadcast
            try {
                if ($serviceCompanyId > 0) {
                    broadcast(new AgencyActivityLoggedEvent($serviceCompanyId, $agencyId, $payload, $newVersion))->toOthers();
                }
                if ($agencyId > 0 && $agencyId !== $serviceCompanyId) {
                    broadcast(new AgencyActivityLoggedEvent($agencyId, $agencyId, $payload, $newVersion))->toOthers();
                }
            } catch (\Throwable) {
            }

            // 4. Mark Outbox Record COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);

        } catch (\Throwable $e) {
            Log::error("[ProcessAgencyActivityOutboxJob] Processing failed for event #{$eventId}: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
            ]);

            $finalStatus = ($event->attempts >= $this->tries) ? 'DEAD_LETTER' : 'FAILED';

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $finalStatus,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => now()->addSeconds($this->backoff * max(1, $event->attempts)),
                ]);
        }
    }
}
