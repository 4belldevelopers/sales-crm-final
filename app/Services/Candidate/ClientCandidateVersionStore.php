<?php

namespace App\Services\Candidate;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

/**
 * Class ClientCandidateVersionStore
 *
 * Dedicated Coordination Store managing monotonically increasing client candidate collection versions.
 * Implements atomic single-round-trip lookups and concurrency-safe event deduplication via Redis Lua scripts.
 *
 * Architectural Invariants:
 * 1. event_ledger_TTL (7 days) > maximum_outbox_replay_horizon (72 hours).
 * 2. Fail-Closed: If Dragonfly/Redis is unavailable, aborts with HTTP 503 (zero MySQL reads/fallbacks).
 * 3. Replay Idempotency: Duplicate event replay within 7 days produces zero spurious collection increments.
 */
class ClientCandidateVersionStore
{
    /**
     * Retrieve the current candidate collection version for a client.
     * Atomically bootstraps to version 0 if absent in a single network round-trip.
     */
    public function getVersion(int $companyId, int $clientId): int
    {
        $key = "client_candidate_version:{$companyId}:{$clientId}";

        $luaBootstrap = <<<'LUA'
            local key = KEYS[1]
            if redis.call('EXISTS', key) == 1 then
                return tonumber(redis.call('GET', key) or 0)
            else
                redis.call('SET', key, 0, 'NX')
                return 0
            end
LUA;

        try {
            $redis = cache()->store('dragonfly')->getRedis();

            return (int) $redis->eval($luaBootstrap, 1, $key);
        } catch (\Throwable $e) {
            Log::emergency('Dragonfly coordination tier unavailable for candidate version lookup', [
                'company_id' => $companyId,
                'client_id' => $clientId,
                'error' => $e->getMessage(),
            ]);
            abort(503, 'Coordination store unavailable. Candidate synchronization cannot be guaranteed.');
        }
    }

    /**
     * Atomically advance the collection version for an event with a 7-day deduplication ledger.
     */
    public function incrementVersion(int $companyId, int $clientId, string $eventId): int
    {
        $versionKey = "client_candidate_version:{$companyId}:{$clientId}";
        $eventKey = "event_idempotency:{$companyId}:{$clientId}:{$eventId}";
        $ttlSeconds = 604800; // 7-day replay deduplication horizon (exceeds 72h outbox redelivery horizon)

        $luaScript = <<<'LUA'
            local eventKey   = KEYS[1]
            local versionKey = KEYS[2]
            local ttl        = tonumber(ARGV[1])

            -- 1. Check if event was already processed (replay idempotency)
            if redis.call('EXISTS', eventKey) == 1 then
                return tonumber(redis.call('GET', versionKey) or 0)
            end

            -- 2. Mark event as processed with 7-day TTL ledger
            redis.call('SET', eventKey, 1, 'EX', ttl)

            -- 3. Atomically increment collection version
            local newVersion = redis.call('INCR', versionKey)
            return newVersion
LUA;

        try {
            $redis = cache()->store('dragonfly')->getRedis();

            return (int) $redis->eval($luaScript, 2, $eventKey, $versionKey, $ttlSeconds);
        } catch (\Throwable $e) {
            Log::emergency('Dragonfly version advancement failed', [
                'company_id' => $companyId,
                'client_id' => $clientId,
                'event_id' => $eventId,
                'error' => $e->getMessage(),
            ]);
            abort(503, 'Coordination store unavailable. Version increment aborted.');
        }
    }
}
