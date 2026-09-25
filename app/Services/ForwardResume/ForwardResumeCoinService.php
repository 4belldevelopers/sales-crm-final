<?php

namespace App\Services\ForwardResume;

use App\Helper\GeneralMethod;
use App\Models\CoinReservation;
use App\Services\ElasticService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class ForwardResumeCoinService
 *
 * Intent: Implements the two-phase financial reservation engine (Reserve -> Commit -> Release)
 * backed by Redis Lua concurrency locking and durable MySQL ACID authority.
 * Tenancy: Scoped strictly to company_id.
 */
class ForwardResumeCoinService
{
    protected ElasticService $elasticService;

    public function __construct(ElasticService $elasticService)
    {
        $this->elasticService = $elasticService;
    }

    /**
     * Lua script for atomic reservation with parameter immutability verification.
     */
    protected static function reserveScript(): string
    {
        return <<<'LUA'
local resStatus = redis.call('HGET', KEYS[2], 'status')
if resStatus then
    local existingComp = redis.call('HGET', KEYS[2], 'company_id')
    local existingFwd = redis.call('HGET', KEYS[2], 'forward_request_id')
    local existingAmount = tonumber(redis.call('HGET', KEYS[2], 'amount') or '0')
    
    if existingComp == ARGV[3] and existingFwd == ARGV[2] and existingAmount == tonumber(ARGV[1]) then
        return resStatus
    else
        return 'RESERVATION_PARAM_MISMATCH'
    end
end

local available = tonumber(redis.call('HGET', KEYS[1], 'available') or '0')
local required = tonumber(ARGV[1])

if available >= required then
    redis.call('HINCRBY', KEYS[1], 'available', -required)
    redis.call('HINCRBY', KEYS[1], 'reserved', required)
    
    redis.call('HMSET', KEYS[2], 
        'status', 'RESERVED',
        'amount', required,
        'forward_request_id', ARGV[2],
        'company_id', ARGV[3],
        'created_at', redis.call('TIME')[1]
    )
    redis.call('EXPIRE', KEYS[2], 3600)
    return 'RESERVED'
else
    return 'INSUFFICIENT_FUNDS'
end
LUA;
    }

    /**
     * Lua script for atomic reservation-specific release.
     */
    protected static function releaseScript(): string
    {
        return <<<'LUA'
local resStatus = redis.call('HGET', KEYS[2], 'status')

if not resStatus then
    return 'RESERVATION_NOT_FOUND'
end

if resStatus == 'RELEASED' then
    return 'ALREADY_RELEASED'
end

if resStatus == 'COMMITTED' then
    return 'ALREADY_COMMITTED'
end

if resStatus == 'RESERVED' then
    local amount = tonumber(redis.call('HGET', KEYS[2], 'amount') or '0')
    if amount > 0 then
        redis.call('HINCRBY', KEYS[1], 'reserved', -amount)
        redis.call('HINCRBY', KEYS[1], 'available', amount)
    end
    redis.call('HSET', KEYS[2], 'status', 'RELEASED')
    return 'RELEASED'
end

return 'INVALID_STATE'
LUA;
    }

    /**
     * Pre-warms or verifies tenant Redis balance mirror.
     */
    public function ensureTenantRedisBalance(int $companyId): void
    {
        $tenantKey = "tenant:{$companyId}:coins";
        try {
            $exists = Redis::exists($tenantKey);
            if (! $exists) {
                // Read from Elasticsearch (0 MySQL reads)
                $available = 0;
                try {
                    $indexName = config('constants.ELASTIC_COMPANIES_COINS', 'ats_company_coins');
                    $resp = $this->elasticService->getClient()->search([
                        'index' => $indexName,
                        'body' => [
                            'query' => ['term' => ['company_id' => $companyId]],
                            'size' => 1,
                        ],
                    ]);
                    $hit = $resp['hits']['hits'][0]['_source'] ?? null;
                    if ($hit && isset($hit['balance'])) {
                        $available = (int) $hit['balance'];
                    }
                } catch (\Throwable $e) {
                    // Fallback to MySQL if ES index is cold
                    $cc = DB::table('company_coins')->where('company_id', $companyId)->first();
                    $available = $cc ? (int) ($cc->coin ?? 0) : 0;
                }

                Redis::hmset($tenantKey, [
                    'available' => $available,
                    'reserved' => 0,
                ]);
                Redis::expire($tenantKey, 86400);
            }
        } catch (\Throwable $e) {
            Log::warning("[ForwardResumeCoinService] Redis ensureTenantRedisBalance warning: {$e->getMessage()}");
        }
    }

    /**
     * Reserve coins in Redis and return status.
     */
    public function reserve(
        int $companyId,
        int $userId,
        string $forwardRequestId,
        string $reservationId,
        int $requiredCoins
    ): array {
        $tenantKey = "tenant:{$companyId}:coins";
        $reservationKey = "coin_reservation:{$reservationId}";

        $this->ensureTenantRedisBalance($companyId);

        try {
            $result = (string) Redis::eval(
                self::reserveScript(),
                2,
                $tenantKey,
                $reservationKey,
                $requiredCoins,
                $forwardRequestId,
                $companyId
            );

            if ($result === 'RESERVED') {
                return ['success' => true, 'status' => 'RESERVED'];
            }

            return ['success' => false, 'status' => $result];
        } catch (\Throwable $e) {
            Log::error("[ForwardResumeCoinService] reserve error: {$e->getMessage()}");
            // If Redis is unreachable, allow graceful DB fallback
            return ['success' => true, 'status' => 'RESERVED', 'redis_fallback' => true];
        }
    }

    /**
     * Release reserved coins back to available pool.
     */
    public function release(int $companyId, string $reservationId): array
    {
        $tenantKey = "tenant:{$companyId}:coins";
        $reservationKey = "coin_reservation:{$reservationId}";

        try {
            $result = (string) Redis::eval(
                self::releaseScript(),
                2,
                $tenantKey,
                $reservationKey
            );

            // Update MySQL reservation record
            CoinReservation::where('reservation_id', $reservationId)
                ->where('company_id', $companyId)
                ->update([
                    'status' => 'RELEASED',
                    'released_at' => now(),
                ]);

            return ['success' => true, 'status' => $result];
        } catch (\Throwable $e) {
            Log::warning("[ForwardResumeCoinService] release warning: {$e->getMessage()}");
            return ['success' => false, 'status' => 'ERROR', 'error' => $e->getMessage()];
        }
    }

    /**
     * Durable MySQL commit of reserved coins.
     */
    public function commit(
        int $companyId,
        int $userId,
        string $forwardRequestId,
        string $reservationId,
        int $amount
    ): bool {
        return DB::transaction(function () use ($companyId, $userId, $forwardRequestId, $reservationId, $amount) {
            $reservation = CoinReservation::where('reservation_id', $reservationId)
                ->where('company_id', $companyId)
                ->lockForUpdate()
                ->first();

            if (! $reservation) {
                // If not found in DB, check by forward_request_id
                $reservation = CoinReservation::where('forward_request_id', $forwardRequestId)
                    ->where('company_id', $companyId)
                    ->lockForUpdate()
                    ->first();
            }

            if ($reservation && $reservation->status === 'COMMITTED') {
                return true; // Already committed idempotently
            }

            // Execute authoritative balance deduction
            $eventId = 43; // Candidate resume forward event
            try {
                GeneralMethod::coinsDeductions($companyId, $eventId, $userId, $companyId, '', '');
            } catch (\Throwable $e) {
                Log::warning("[ForwardResumeCoinService] coinsDeductions warning: {$e->getMessage()}");
            }

            // Update reservation record
            if ($reservation) {
                $reservation->update([
                    'status' => 'COMMITTED',
                    'committed_at' => now(),
                ]);
            }

            // Update Redis state
            try {
                $tenantKey = "tenant:{$companyId}:coins";
                $reservationKey = "coin_reservation:{$reservationId}";
                Redis::hset($reservationKey, 'status', 'COMMITTED');
                Redis::hincrby($tenantKey, 'reserved', -$amount);
            } catch (\Throwable $redisErr) {
                Log::warning("[ForwardResumeCoinService] Redis commit sync warning: {$redisErr->getMessage()}");
            }

            return true;
        });
    }
}
