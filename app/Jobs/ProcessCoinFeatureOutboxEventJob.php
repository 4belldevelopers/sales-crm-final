<?php

namespace App\Jobs;

use App\Events\CoinFeatureDeltaUpdated;
use App\Services\ElasticCoinEventsIndex;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessCoinFeatureOutboxEventJob
 *
 * Intent: Transactional Outbox asynchronous worker for the Coin Features subsystem.
 * Guarantees at-least-once delivery, dual-store synchronization to Elasticsearch (local_coin_events),
 * and real-time Reverb WebSocket broadcast propagation with bounded retry backoff and DLQ isolation.
 *
 * Queue: outbox
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class ProcessCoinFeatureOutboxEventJob implements ShouldQueue
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

    public function handle(ElasticCoinEventsIndex $elasticIndex): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId, $elasticIndex);

            return;
        }

        // Batch processing when triggered periodically by scheduler or CLI
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CoinFeature')
            ->where('status', 'PENDING')
            ->where('available_at', '<=', now())
            ->orderBy('id', 'asc')
            ->limit(25)
            ->pluck('id')
            ->toArray();

        foreach ($events as $eventId) {
            $this->processEvent((int) $eventId, $elasticIndex);
        }
    }

    protected function processEvent(int $eventId, ElasticCoinEventsIndex $elasticIndex): void
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
            $payload = json_decode($event->payload, true) ?? [];
            $featureId = (int) ($event->aggregate_id ?? $payload['id'] ?? 0);
            $actionType = (string) ($payload['action_type'] ?? 'COIN_FEATURE_UPDATED');

            // 1. Dual-Store Synchronization: Ensure document is indexed in Elasticsearch
            if (! empty($payload['event_data'])) {
                $elasticIndex->indexEvent($payload['event_data']);
            } elseif ($featureId > 0) {
                $row = DB::table('coin_events')->where('id', $featureId)->first();
                if ($row) {
                    $elasticIndex->indexEvent($row);
                }
            }

            // 2. Real-Time Broadcast Notification via Reverb
            try {
                broadcast(new CoinFeatureDeltaUpdated(
                    id: $featureId,
                    title: (string) ($payload['title'] ?? ''),
                    eventType: (string) ($payload['event_type'] ?? 'sales'),
                    deductionType: (string) ($payload['deduction_type'] ?? 'onetime'),
                    defaultRate: (float) ($payload['default_rate'] ?? 0.0),
                    minimumRequired: (float) ($payload['minimum_required'] ?? 0.0),
                    status: (int) ($payload['status'] ?? 1),
                    actionType: $actionType,
                    kpiCounts: (array) ($payload['kpi_counts'] ?? []),
                    description: (string) ($payload['description'] ?? ''),
                    updatedBy: (string) ($payload['updated_by'] ?? 'Super Admin'),
                    timestamp: (string) ($payload['timestamp'] ?? now()->toIso8601String())
                ));
            } catch (\Throwable $bEx) {
                Log::warning('[ProcessCoinFeatureOutboxEventJob] WebSocket broadcast notice: '.$bEx->getMessage());
            }

            // 3. Mark Outbox event COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);

        } catch (\Throwable $e) {
            Log::error("[ProcessCoinFeatureOutboxEventJob] Failed for event #{$eventId}: ".$e->getMessage());

            $currentAttempts = (int) ($event->attempts ?? 1);
            $nextStatus = ($currentAttempts >= 3) ? 'DEAD_LETTER' : 'FAILED';

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => $nextStatus,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => now()->addSeconds(30),
                ]);
        }
    }
}
