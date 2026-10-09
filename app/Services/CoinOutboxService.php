<?php

namespace App\Services;

use App\Jobs\ProcessCoinOutboxEventJob;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Class CoinOutboxService
 *
 * Intent: Transactional Outbox helper for Coin ledger mutations.
 * Guarantees atomic insertion of outbox records inside DB::transaction(),
 * and dispatches decoupled background event relay via Reverb.
 *
 * Tenancy: Strictly scoped by tenant company_id.
 *
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class CoinOutboxService
{
    /**
     * Record a coin transaction event in sys_outbox_events and dispatch the worker after commit.
     *
     * Must be executed within an active DB::transaction().
     *
     * @param  int  $companyId  Tenant company identifier
     * @param  int  $logId  Generated coin_transection_logs id
     * @param  string  $eventType  'operational' | 'sales'
     * @param  string  $transectionType  'credit' | 'debit'
     * @param  float  $coins  Coins transacted
     * @param  float  $balanceCoins  Running balance remaining
     * @param  string  $eventDescription  Description of the event
     * @return int Created outbox record id
     */
    public static function recordAndDispatch(
        int $companyId,
        int $logId,
        string $eventType,
        string $transectionType,
        float $coins,
        float $balanceCoins,
        string $eventDescription
    ): int {
        $idempotencyKey = (string) Str::uuid();

        $outboxId = (int) DB::table('sys_outbox_events')->insertGetId([
            'tenant_id' => $companyId,
            'idempotency_key' => $idempotencyKey,
            'aggregate_type' => 'CoinTransaction',
            'aggregate_id' => $logId,
            'aggregate_version' => 1,
            'event_type' => 'COIN_TRANSACTION_UPDATED',
            'payload' => json_encode([
                'log_id' => $logId,
                'company_id' => $companyId,
                'event_type' => $eventType,
                'transection_type' => strtolower($transectionType),
                'coins' => (float) $coins,
                'balance_coins' => (float) $balanceCoins,
                'event' => $eventDescription,
                'created_at' => now()->toIso8601String(),
            ]),
            'status' => 'PENDING',
            'attempts' => 0,
            'available_at' => now(),
            'created_at' => now(),
        ]);

        // Dispatch outbox worker after transaction commits
        DB::afterCommit(function () use ($outboxId) {
            try {
                ProcessCoinOutboxEventJob::dispatch($outboxId);
            } catch (\Throwable $e) {
                Log::warning('[CoinOutboxService] Direct outbox job dispatch deferred: '.$e->getMessage());
            }
        });

        return $outboxId;
    }
}
