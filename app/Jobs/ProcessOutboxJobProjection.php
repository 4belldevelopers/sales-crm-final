<?php

namespace App\Jobs;

use App\Events\JobStatusChangedEvent;
use App\Services\ElasticService;
use App\Services\JobProjectionService;
use App\Services\JobVisibilityScopeResolver;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

/**
 * ProcessOutboxJobProjection
 *
 * Horizon Outbox Worker Job processing sys_outbox_events.
 *
 * Implements:
 * 1. Primary-Shard Atomic Conditional ES Update (Painless script prevents version regression).
 * 2. Full Schema Preservation Guard (Rebuilds full canonical document if missing).
 * 3. Multi-Scope Job Visibility Scope Invalidation (Invalidates Redis for all partner shared scopes).
 * 4. Real-time acceleration broadcast via Reverb.
 * 5. 100% Idempotent retries (Safe against worker crashes prior to COMPLETED marking).
 */
class ProcessOutboxJobProjection implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public readonly int $outboxEventId
    ) {}

    public function handle(
        ElasticService $elasticService,
        JobProjectionService $projectionService,
        JobVisibilityScopeResolver $scopeResolver
    ): void {
        $event = DB::table('sys_outbox_events')->where('id', $this->outboxEventId)->first();
        if (!$event || $event->status === 'COMPLETED') {
            return;
        }

        $payload = json_decode((string) $event->payload, true) ?: [];
        $jobId = (int) $event->aggregate_id;
        $version = (int) $event->aggregate_version;
        $index = defined('ELASTIC_JOBS') ? ELASTIC_JOBS : 'ats_jobs_elastic';

        try {
            // 1. Primary-Shard Atomic Conditional Update (Strict Monotonic Projection Invariant)
            // If an older or equal version arrives late, ctx.op is set to 'noop', preventing version regression!
            $elasticService->getClient()->update([
                'index' => $index,
                'id'    => (string) $jobId,
                'body'  => [
                    'script' => [
                        'source' => "if (ctx._source.projection_version == null || params.version > ctx._source.projection_version) { " .
                                    "ctx._source.status = params.status; " .
                                    "ctx._source.projection_version = params.version; " .
                                    "ctx._source.updated_at = params.updated_at; " .
                                    "} else { ctx.op = 'noop'; }",
                        'lang'   => 'painless',
                        'params' => [
                            'status'     => (string) ($payload['to_status'] ?? 'active'),
                            'version'    => $version,
                            'updated_at' => now()->toIso8601String(),
                        ],
                    ],
                ],
            ]);
        } catch (\Throwable $e) {
            // 2. Full Schema Preservation Guard: Missing document rebuild
            // Document missing or evicted from Elasticsearch! Rebuild authoritative canonical document from SoR.
            $projectionService->rebuildAndIndexCanonicalJob($jobId);
        }

        // 3. Multi-Scope Monotonic Redis Invalidation & Reverb Fan-Out
        $scopes = $scopeResolver->resolveAffectedScopes($jobId, $payload);

        foreach ($scopes as $scope) {
            $partnerPart = (int) ($scope['partner_company_id'] ?? 0);
            $redisKey = "ats:client_jobs_version:{$scope['tenant_id']}:{$scope['service_company_id']}:{$scope['client_type']}:{$partnerPart}:{$scope['client_id']}";

            Redis::eval(
                "local cur = tonumber(redis.call('get', KEYS[1]) or 0)
                 local newVer = tonumber(ARGV[1])
                 if newVer > cur then
                     redis.call('set', KEYS[1], newVer)
                     return newVer
                 else
                     return cur
                 end",
                1,
                $redisKey,
                $version
            );

            // Broadcast real-time patch to each specific tenant/company channel
            try {
                broadcast(new JobStatusChangedEvent(array_merge($payload, [
                    'tenant_id'          => $scope['tenant_id'],
                    'company_id'         => $scope['service_company_id'],
                    'client_type'        => $scope['client_type'],
                    'partner_company_id' => $partnerPart,
                ])))->toOthers();
            } catch (\Throwable $broadcastErr) {
                // Reverb accelerates convergence; transmission failure does not invalidate projection
            }
        }

        // 4. Mark Outbox Record COMPLETED (Safe from crashes; retries are 100% idempotent)
        DB::table('sys_outbox_events')->where('id', $event->id)->update([
            'status'       => 'COMPLETED',
            'processed_at' => now(),
        ]);
    }
}
