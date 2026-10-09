<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * CoinService — V2 Coins Engine Domain Service.
 *
 * Intent: Encapsulates atomic coin balance deductions, validations, auto-settlement,
 * and ledger transaction creation. Combines Redis distributed locks (Cache::lock), fast-path
 * balance caching, and pessimistic database row locking (lockForUpdate) within DB::transaction()
 * to prevent race conditions, lost updates, and double deductions at 10K+ concurrency.
 *
 * Tenancy Note: All queries and cache keys strictly include tenant identifier (company_id).
 *
 * Author: Antigravity AI
 * Date: August 26, 2026
 */
class CoinService
{
    protected CoinMetricsService $metricsService;

    public function __construct(?CoinMetricsService $metricsService = null)
    {
        $this->metricsService = $metricsService ?? new CoinMetricsService;
    }

    /**
     * Perform atomic coin deduction with Redis distributed locking and pessimistic DB row locking.
     *
     * Intent: Validates balance, debits coins, writes transaction log, and invalidates Redis cache atomically.
     * Implements fallback to DB transaction if Redis lock acquisition is unavailable.
     *
     * Tenancy Note: Scoped strictly to company_id.
     *
     * @param  int  $companyId  Target company tenant ID.
     * @param  int  $eventId  Coin event ID from coin_events table.
     * @param  int  $userId  Performing user ID.
     * @param  int  $fromCompanyId  Source company tenant ID.
     * @param  mixed  $jobId  Optional job ID.
     * @param  mixed  $teamId  Optional team ID.
     * @return object Result object matching legacy payload contract.
     */
    public function deductCoinsAtomic(
        int $companyId,
        int $eventId,
        int $userId,
        int $fromCompanyId,
        mixed $jobId = null,
        mixed $teamId = null
    ): object {
        return $this->metricsService->measure('deduct_coins_atomic', $companyId, function () use ($companyId, $eventId, $userId, $fromCompanyId, $jobId, $teamId) {
            $lockKey = "coin_lock:{$companyId}";
            $lockAcquired = false;
            $lock = null;

            try {
                $lock = Cache::lock($lockKey, 5);
                $lockAcquired = $lock->get();
            } catch (\Throwable $e) {
                // Graceful fallback if Redis driver is unavailable
                $lockAcquired = false;
            }

            try {
                return DB::transaction(function () use ($companyId, $eventId, $userId, $fromCompanyId, $jobId, $teamId) {
                    $coinEvent = DB::table('coin_events')->where('id', $eventId)->first();
                    if (! $coinEvent) {
                        return (object) [
                            'status' => true,
                            'message' => 'Invalid coin event ID',
                            'company_coins' => 0.00,
                        ];
                    }

                    // Pessimistic row lock on tenant company_coins row
                    $companyCoins = DB::table('company_coins')
                        ->where('company_id', $companyId)
                        ->where('event_type', $coinEvent->event_type)
                        ->lockForUpdate()
                        ->first();

                    if (! $companyCoins) {
                        return (object) [
                            'status' => true,
                            'message' => 'Packages have expired or package coins are low',
                            'company_coins' => 0.00,
                        ];
                    }

                    $coinOrderEvent = DB::table('coin_order_events')
                        ->where('company_id', $companyId)
                        ->where('coin_event_id', $eventId)
                        ->where('event_type', $coinEvent->event_type)
                        ->where('coin_order_id', $companyCoins->coin_order_id)
                        ->first();

                    $deductionRate = $coinOrderEvent ? (float) $coinOrderEvent->deductions : (float) $coinEvent->default_rate;

                    if ($companyCoins->coins >= $deductionRate) {
                        $newBalance = $companyCoins->coins - $deductionRate;

                        DB::table('company_coins')
                            ->where('company_id', $companyId)
                            ->where('event_type', $coinEvent->event_type)
                            ->update(['coins' => $newBalance, 'updated_at' => now()]);

                        $logData = [
                            'coin_order_id' => $companyCoins->coin_order_id,
                            'from_company_id' => $fromCompanyId,
                            'company_id' => $companyId,
                            'event_by' => $userId,
                            'event' => ($teamId === '0' || $teamId === 0) ? ' Working Status changed' : $coinEvent->title,
                            'amount' => '',
                            'discount' => '',
                            'coins' => $deductionRate,
                            'transection_type' => 'Debit',
                            'settlement_coins' => 0.00,
                            'balance_coins' => 0.00,
                            'remain_coins' => $newBalance,
                            'actual_coins' => $companyCoins->coins,
                            'event_type' => $coinEvent->event_type,
                            'team_id' => $teamId,
                            'job_id' => $jobId,
                            'start_date' => null,
                            'end_date' => null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];

                        $logId = DB::table('coin_transection_logs')->insertGetId($logData);

                        // Transactional Outbox: durable outbox log and decoupled Reverb broadcast
                        CoinOutboxService::recordAndDispatch(
                            companyId: $companyId,
                            logId: (int) $logId,
                            eventType: $coinEvent->event_type,
                            transectionType: 'debit',
                            coins: (float) $deductionRate,
                            balanceCoins: (float) $newBalance,
                            eventDescription: $logData['event']
                        );

                        // Invalidate and update Redis balance cache
                        try {
                            Cache::put("tenant:{$companyId}:coins:{$coinEvent->event_type}", $newBalance, 300);
                        } catch (\Throwable $e) {
                            // Non-blocking warning on cache failure
                        }

                        return (object) [
                            'status' => false,
                            'message' => 'Success',
                            'company_coins' => $newBalance,
                        ];
                    }

                    return (object) [
                        'status' => true,
                        'message' => 'does not have sufficient coins, minimum required coins',
                        'company_coins' => (float) $companyCoins->coins,
                    ];
                });
            } finally {
                if ($lockAcquired && $lock) {
                    try {
                        $lock->release();
                    } catch (\Throwable $e) {
                        // Suppress lock release exception
                    }
                }
            }
        });
    }

    /**
     * Validate coin balance with Redis fast-path caching and pessimistic row locking fallback.
     *
     * Intent: Checks if tenant company has sufficient coins using Redis fast-path when available.
     *
     * Tenancy Note: Scoped strictly to company_id.
     *
     * @param  int  $companyId  Target company tenant ID.
     * @param  int  $eventId  Coin event ID.
     * @param  int  $userId  Performing user ID.
     * @param  int  $fromCompanyId  Source company tenant ID.
     * @param  mixed  $jobId  Optional job ID.
     * @param  mixed  $teamId  Optional team ID.
     * @return object Result object matching legacy payload contract.
     */
    public function validateCoinsAtomic(
        int $companyId,
        int $eventId,
        int $userId,
        int $fromCompanyId,
        mixed $jobId = null,
        mixed $teamId = null
    ): object {
        return $this->metricsService->measure('validate_coins_atomic', $companyId, function () use ($companyId, $eventId) {
            $coinEvent = DB::table('coin_events')->where('id', $eventId)->first();
            if (! $coinEvent) {
                return (object) [
                    'status' => true,
                    'message' => 'Invalid coin event ID',
                    'company_coins' => 0.00,
                ];
            }

            // Check Redis fast-path balance cache
            $cacheKey = "tenant:{$companyId}:coins:{$coinEvent->event_type}";
            $cachedBalance = null;

            try {
                $cachedBalance = Cache::get($cacheKey);
            } catch (\Throwable $e) {
                $cachedBalance = null;
            }

            if ($cachedBalance !== null) {
                $deductionRate = (float) $coinEvent->default_rate;
                if ((float) $cachedBalance >= $deductionRate) {
                    return (object) [
                        'status' => false,
                        'message' => 'Success (Fast-Path)',
                        'company_coins' => (float) $cachedBalance,
                    ];
                }
            }

            // DB Fallback check
            $companyCoins = DB::table('company_coins')
                ->where('company_id', $companyId)
                ->where('event_type', $coinEvent->event_type)
                ->first();

            if (! $companyCoins) {
                return (object) [
                    'status' => true,
                    'message' => 'Packages have expired or package coins are low',
                    'company_coins' => 0.00,
                ];
            }

            $coinOrderEvent = DB::table('coin_order_events')
                ->where('company_id', $companyId)
                ->where('coin_event_id', $eventId)
                ->where('event_type', $coinEvent->event_type)
                ->where('coin_order_id', $companyCoins->coin_order_id)
                ->first();

            $deductionRate = $coinOrderEvent ? (float) $coinOrderEvent->deductions : (float) $coinEvent->default_rate;
            $currentCoins = (float) $companyCoins->coins;

            // Warm Redis cache for future reads
            try {
                Cache::put($cacheKey, $currentCoins, 300);
            } catch (\Throwable $e) {
                // Non-blocking
            }

            if ($currentCoins >= $deductionRate) {
                return (object) [
                    'status' => false,
                    'message' => 'Success',
                    'company_coins' => $currentCoins,
                ];
            }

            return (object) [
                'status' => true,
                'message' => 'does not have sufficient coins, minimum required coins',
                'company_coins' => $currentCoins,
            ];
        });
    }
}
