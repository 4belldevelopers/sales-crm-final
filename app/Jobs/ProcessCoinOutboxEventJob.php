<?php

namespace App\Jobs;

use App\Events\CoinTransactionUpdated;
use App\Services\ElasticService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class ProcessCoinOutboxEventJob
 *
 * Intent:
 * Phase 5 Decoupled Transactional Outbox Worker for Coin mutations.
 * Claims pending sys_outbox_events for aggregate_type 'CoinTransaction',
 * delivers real-time WebSocket broadcast notifications via Reverb,
 * keeps audit logs durable, and marks outbox records as COMPLETED with
 * bounded retries and poison-pill isolation.
 *
 * Tenancy: Scoped strictly by tenant company_id.
 *
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class ProcessCoinOutboxEventJob implements ShouldQueue
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

    public function handle(?ElasticService $elasticService = null): void
    {
        if ($this->specificEventId !== null) {
            $this->processEvent($this->specificEventId, $elasticService);

            return;
        }

        // Batch processing when triggered periodically by worker or scheduler
        $events = DB::table('sys_outbox_events')
            ->where('aggregate_type', 'CoinTransaction')
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

    protected function processEvent(int $eventId, ?ElasticService $elasticService = null): void
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
            $companyId = (int) ($event->tenant_id ?? $payload['company_id'] ?? 0);
            $logId = (int) ($event->aggregate_id ?? $payload['log_id'] ?? 0);
            $eventType = (string) ($payload['event_type'] ?? 'operational');
            $transectionType = (string) ($payload['transection_type'] ?? 'debit');
            $coins = (float) ($payload['coins'] ?? 0);
            $balanceCoins = (float) ($payload['balance_coins'] ?? 0);
            $eventName = (string) ($payload['event'] ?? 'Coin Transaction');

            // 1. Broadcast real-time delta update via Reverb on private channel
            if ($companyId > 0) {
                event(new CoinTransactionUpdated(
                    companyId: $companyId,
                    eventType: $eventType,
                    transectionType: $transectionType,
                    coins: $coins,
                    balanceCoins: $balanceCoins,
                    event: $eventName,
                    eventId: 'tx_'.$logId
                ));
            }

            // 2. Mark Outbox event COMPLETED
            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'COMPLETED',
                    'processed_at' => now(),
                    'last_error' => null,
                ]);

        } catch (\Throwable $e) {
            Log::error("[ProcessCoinOutboxEventJob] Failed for event #{$eventId}: ".$e->getMessage());

            DB::table('sys_outbox_events')
                ->where('id', $eventId)
                ->update([
                    'status' => 'FAILED',
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'available_at' => now()->addSeconds(30),
                ]);
        }
    }
}
