<?php

namespace App\Services;

use App\Events\CoinFeatureDeltaUpdated;
use App\Jobs\ProcessCoinFeatureOutboxEventJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class CoinFeatureMutationService
 *
 * Intent: Encapsulates state-mutating operations for Coin Features & Events under the
 * Transactional Outbox, Durable Idempotency, and CQRS dual-store synchronization architecture.
 * Guarantees zero duplicate executions under network retries, ACID row-locked state transitions,
 * instant Elasticsearch dual-store synchronization, and real-time Reverb WebSocket broadcast pulses.
 *
 * Author: Antigravity AI — Super Admin Backend Architecture
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md and docs/AI_RULES.md.
 */
class CoinFeatureMutationService
{
    public function __construct(
        protected ElasticCoinEventsIndex $elasticIndex
    ) {}

    /**
     * Compute a canonical SHA-256 hash for a given payload.
     */
    public function computeRequestHash(array $payload): string
    {
        ksort($payload);

        return hash('sha256', json_encode($payload));
    }

    /**
     * Calculate fresh KPI aggregations directly from the database during mutation commit.
     */
    public function calculateKpis(): array
    {
        $kpiRow = DB::table('coin_events')
            ->selectRaw("
                COUNT(id) as total,
                SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 0 THEN 1 ELSE 0 END) as inactive,
                SUM(CASE WHEN event_type = 'operational' THEN 1 ELSE 0 END) as operational,
                SUM(CASE WHEN event_type = 'sales' THEN 1 ELSE 0 END) as sales,
                SUM(CASE WHEN deduction_type = 'daily' THEN 1 ELSE 0 END) as daily,
                SUM(CASE WHEN deduction_type = 'onetime' THEN 1 ELSE 0 END) as onetime
            ")->first();

        return [
            'total' => (int) ($kpiRow->total ?? 0),
            'active' => (int) ($kpiRow->active ?? 0),
            'inactive' => (int) ($kpiRow->inactive ?? 0),
            'operational' => (int) ($kpiRow->operational ?? 0),
            'sales' => (int) ($kpiRow->sales ?? 0),
            'daily' => (int) ($kpiRow->daily ?? 0),
            'onetime' => (int) ($kpiRow->onetime ?? 0),
        ];
    }

    /**
     * Toggle the active status of a Coin Feature with ACID locking, durable idempotency,
     * outbox persistence, and real-time dual-store synchronization.
     *
     * @param  int  $id  Target event ID
     * @param  string|null  $idempotencyKey  Durable idempotency key
     * @param  int|null  $adminUserId  Authenticated administrator ID
     * @return array Standardized mutation response
     */
    public function toggleStatus(int $id, ?string $idempotencyKey = null, ?int $adminUserId = null): array
    {
        $idempotencyKey = $idempotencyKey ? trim($idempotencyKey) : null;
        $requestPayload = ['action' => 'toggle_status', 'id' => $id];
        $requestHash = $this->computeRequestHash($requestPayload);

        // 1. Durable Idempotency Check
        if ($idempotencyKey) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                if ($existing->request_hash !== $requestHash) {
                    abort(409, 'Idempotency key reuse with modified payload detected.');
                }

                if ($existing->status === 'COMPLETED' && $existing->response_body) {
                    $cached = json_decode($existing->response_body, true);
                    if (is_array($cached)) {
                        $cached['_replayed'] = true;

                        return $cached;
                    }
                }

                if ($existing->status === 'PROCESSING') {
                    abort(409, 'A concurrent operation is already in progress for this request. Please wait.');
                }
            }
        }

        $outboxId = null;
        $eventDoc = null;
        $kpis = [];

        try {
            $result = DB::transaction(function () use (
                $id,
                $idempotencyKey,
                $requestHash,
                &$outboxId,
                &$eventDoc,
                &$kpis
            ) {
                // 2. Claim Idempotency Key Lease
                if ($idempotencyKey) {
                    DB::table('sys_idempotency_keys')->insertOrIgnore([
                        'tenant_id' => 0,
                        'idempotency_key' => $idempotencyKey,
                        'request_hash' => $requestHash,
                        'status' => 'PROCESSING',
                        'expires_at' => Carbon::now()->addMinutes(30),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }

                // 3. Row-level Lock
                $row = DB::table('coin_events')->where('id', $id)->lockForUpdate()->first();
                if (! $row) {
                    abort(404, 'Coin feature event not found.');
                }

                $newStatus = ((int) $row->status === 1) ? 0 : 1;

                // 4. Update Database
                DB::table('coin_events')->where('id', $id)->update(['status' => $newStatus]);

                $eventDoc = [
                    'id' => (int) $row->id,
                    'event_type' => (string) $row->event_type,
                    'title' => (string) $row->title,
                    'default_rate' => (float) $row->default_rate,
                    'description' => (string) ($row->description ?? ''),
                    'deduction_type' => (string) ($row->deduction_type ?? 'onetime'),
                    'minimum_required' => (float) ($row->minimum_required ?? 0.0),
                    'status' => $newStatus,
                ];

                // 5. Atomic Cache Invalidation & KPI Refresh
                CoinFeatureCacheManager::flushCaches();
                $kpis = $this->calculateKpis();

                $deltaPayload = [
                    'id' => (int) $row->id,
                    'title' => (string) $row->title,
                    'event_type' => (string) $row->event_type,
                    'deduction_type' => (string) ($row->deduction_type ?? 'onetime'),
                    'default_rate' => (float) $row->default_rate,
                    'minimum_required' => (float) ($row->minimum_required ?? 0.0),
                    'status' => $newStatus,
                    'action_type' => 'STATUS_TOGGLED',
                    'kpi_counts' => $kpis,
                    'description' => (string) ($row->description ?? ''),
                    'updated_by' => auth()->user()->name ?? 'Super Admin',
                    'timestamp' => Carbon::now()->toIso8601String(),
                    'event_data' => $eventDoc,
                ];

                // 6. Transactional Outbox Event
                $outboxId = DB::table('sys_outbox_events')->insertGetId([
                    'tenant_id' => 0,
                    'idempotency_key' => $idempotencyKey,
                    'aggregate_type' => 'CoinFeature',
                    'aggregate_id' => $row->id,
                    'aggregate_version' => 1,
                    'event_type' => 'CoinFeatureStatusToggled',
                    'payload' => json_encode($deltaPayload),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ]);

                $responseResult = [
                    'status' => 'success',
                    'message' => 'Changed setting Successfully.',
                    'id' => (int) $row->id,
                    'new_status' => $newStatus,
                    'kpi_counts' => $kpis,
                    'outbox_id' => $outboxId,
                ];

                // 7. Mark Idempotency Key COMPLETED
                if ($idempotencyKey) {
                    DB::table('sys_idempotency_keys')
                        ->where('tenant_id', 0)
                        ->where('idempotency_key', $idempotencyKey)
                        ->update([
                            'status' => 'COMPLETED',
                            'response_status' => 200,
                            'response_body' => json_encode($responseResult),
                            'completed_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                }

                return $responseResult;
            });

            // 8. Fast-path Dual-Store Synchronization to Elasticsearch
            if ($eventDoc) {
                try {
                    $this->elasticIndex->indexEvent($eventDoc);
                } catch (\Throwable $esEx) {
                    Log::warning('CoinFeatureMutationService: Fast-path ES sync notice: '.$esEx->getMessage());
                }
            }

            // 9. Fast-path WebSockets broadcast
            try {
                broadcast(new CoinFeatureDeltaUpdated(
                    id: (int) $result['id'],
                    title: (string) ($eventDoc['title'] ?? ''),
                    eventType: (string) ($eventDoc['event_type'] ?? 'sales'),
                    deductionType: (string) ($eventDoc['deduction_type'] ?? 'onetime'),
                    defaultRate: (float) ($eventDoc['default_rate'] ?? 0.0),
                    minimumRequired: (float) ($eventDoc['minimum_required'] ?? 0.0),
                    status: (int) $result['new_status'],
                    actionType: 'STATUS_TOGGLED',
                    kpiCounts: $kpis,
                    description: (string) ($eventDoc['description'] ?? ''),
                    updatedBy: auth()->user()->name ?? 'Super Admin'
                ));
            } catch (\Throwable $bEx) {
                Log::warning('CoinFeatureMutationService: Fast-path broadcast notice: '.$bEx->getMessage());
            }

            // 10. Dispatch Outbox worker job
            if ($outboxId) {
                try {
                    ProcessCoinFeatureOutboxEventJob::dispatch((int) $outboxId)->afterCommit();
                } catch (\Throwable $dEx) {
                    Log::warning('CoinFeatureMutationService: Outbox job dispatch notice: '.$dEx->getMessage());
                }
            }

            return $result;
        } catch (HttpException $e) {
            if ($idempotencyKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $idempotencyKey)
                    ->delete();
            }

            throw $e;
        }
    }

    /**
     * Store or update a Coin Feature event with ACID validation, durable idempotency,
     * outbox logging, and dual-store synchronization.
     *
     * @param  array  $payload  Validated input attributes
     * @param  string|null  $idempotencyKey  Durable idempotency key
     * @param  int|null  $adminUserId  Authenticated administrator ID
     * @return array Standardized mutation response
     */
    public function storeEvent(array $payload, ?string $idempotencyKey = null, ?int $adminUserId = null): array
    {
        $idempotencyKey = $idempotencyKey ? trim($idempotencyKey) : null;
        $requestHash = $this->computeRequestHash($payload);

        // 1. Durable Idempotency Check
        if ($idempotencyKey) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                if ($existing->request_hash !== $requestHash) {
                    abort(409, 'Idempotency key reuse with modified payload detected.');
                }

                if ($existing->status === 'COMPLETED' && $existing->response_body) {
                    $cached = json_decode($existing->response_body, true);
                    if (is_array($cached)) {
                        $cached['_replayed'] = true;

                        return $cached;
                    }
                }

                if ($existing->status === 'PROCESSING') {
                    abort(409, 'A concurrent operation is already in progress for this request. Please wait.');
                }
            }
        }

        $outboxId = null;
        $eventDoc = null;
        $kpis = [];
        $isUpdate = false;
        $featureId = 0;

        try {
            $result = DB::transaction(function () use (
                $payload,
                $idempotencyKey,
                $requestHash,
                &$outboxId,
                &$eventDoc,
                &$kpis,
                &$isUpdate,
                &$featureId
            ) {
                // 2. Claim Idempotency Key Lease
                if ($idempotencyKey) {
                    DB::table('sys_idempotency_keys')->insertOrIgnore([
                        'tenant_id' => 0,
                        'idempotency_key' => $idempotencyKey,
                        'request_hash' => $requestHash,
                        'status' => 'PROCESSING',
                        'expires_at' => Carbon::now()->addMinutes(30),
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now(),
                    ]);
                }

                $tokenId = isset($payload['token_id']) ? (int) $payload['token_id'] : 0;
                $isUpdate = ($tokenId > 0);

                $statusVal = (! empty($payload['status']) && ($payload['status'] == 1 || $payload['status'] === 'on' || $payload['status'] === true)) ? 1 : 0;

                $data = [
                    'title' => trim((string) ($payload['title'] ?? '')),
                    'default_rate' => (float) ($payload['default_rate'] ?? 0.0),
                    'minimum_required' => (float) ($payload['minimum_required'] ?? 0.0),
                    'description' => trim((string) ($payload['description'] ?? '')),
                    'deduction_type' => in_array($payload['deduction_type'] ?? '', ['daily', 'onetime'], true) ? $payload['deduction_type'] : 'onetime',
                    'event_type' => in_array($payload['event_type'] ?? '', ['operational', 'sales'], true) ? $payload['event_type'] : 'operational',
                    'status' => $statusVal,
                ];

                if ($isUpdate) {
                    $row = DB::table('coin_events')->where('id', $tokenId)->lockForUpdate()->first();
                    if (! $row) {
                        abort(404, 'Coin feature event to update was not found.');
                    }
                    DB::table('coin_events')->where('id', $tokenId)->update($data);
                    $featureId = $tokenId;
                    $message = 'Event updated Successfully';
                    $actionType = 'FEATURE_UPDATED';
                } else {
                    $featureId = DB::table('coin_events')->insertGetId($data);
                    $message = 'Event Created Successfully';
                    $actionType = 'FEATURE_CREATED';
                }

                $eventDoc = array_merge(['id' => $featureId], $data);

                // 3. Atomic Cache Invalidation & KPI Refresh
                CoinFeatureCacheManager::flushCaches();
                $kpis = $this->calculateKpis();

                $deltaPayload = array_merge($eventDoc, [
                    'action_type' => $actionType,
                    'kpi_counts' => $kpis,
                    'updated_by' => auth()->user()->name ?? 'Super Admin',
                    'timestamp' => Carbon::now()->toIso8601String(),
                    'event_data' => $eventDoc,
                ]);

                // 4. Transactional Outbox Event
                $outboxId = DB::table('sys_outbox_events')->insertGetId([
                    'tenant_id' => 0,
                    'idempotency_key' => $idempotencyKey,
                    'aggregate_type' => 'CoinFeature',
                    'aggregate_id' => $featureId,
                    'aggregate_version' => 1,
                    'event_type' => ($isUpdate ? 'CoinFeatureUpdated' : 'CoinFeatureCreated'),
                    'payload' => json_encode($deltaPayload),
                    'status' => 'PENDING',
                    'attempts' => 0,
                    'available_at' => Carbon::now(),
                    'created_at' => Carbon::now(),
                ]);

                $responseResult = [
                    'status' => 'success',
                    'message' => $message,
                    'url' => route('superadmin.company.coinFeatures'),
                    'id' => $featureId,
                    'action_type' => $actionType,
                    'kpi_counts' => $kpis,
                    'data' => $eventDoc,
                    'outbox_id' => $outboxId,
                ];

                // 5. Mark Idempotency Key COMPLETED
                if ($idempotencyKey) {
                    DB::table('sys_idempotency_keys')
                        ->where('tenant_id', 0)
                        ->where('idempotency_key', $idempotencyKey)
                        ->update([
                            'status' => 'COMPLETED',
                            'response_status' => 200,
                            'response_body' => json_encode($responseResult),
                            'completed_at' => Carbon::now(),
                            'updated_at' => Carbon::now(),
                        ]);
                }

                return $responseResult;
            });

            // 6. Fast-path Dual-Store Synchronization to Elasticsearch
            if ($eventDoc) {
                try {
                    $this->elasticIndex->indexEvent($eventDoc);
                } catch (\Throwable $esEx) {
                    Log::warning('CoinFeatureMutationService: Fast-path ES sync notice: '.$esEx->getMessage());
                }
            }

            // 7. Fast-path WebSockets broadcast
            try {
                broadcast(new CoinFeatureDeltaUpdated(
                    id: (int) $result['id'],
                    title: (string) ($eventDoc['title'] ?? ''),
                    eventType: (string) ($eventDoc['event_type'] ?? 'sales'),
                    deductionType: (string) ($eventDoc['deduction_type'] ?? 'onetime'),
                    defaultRate: (float) ($eventDoc['default_rate'] ?? 0.0),
                    minimumRequired: (float) ($eventDoc['minimum_required'] ?? 0.0),
                    status: (int) ($eventDoc['status'] ?? 1),
                    actionType: $result['action_type'],
                    kpiCounts: $kpis,
                    description: (string) ($eventDoc['description'] ?? ''),
                    updatedBy: auth()->user()->name ?? 'Super Admin'
                ));
            } catch (\Throwable $bEx) {
                Log::warning('CoinFeatureMutationService: Fast-path broadcast notice: '.$bEx->getMessage());
            }

            // 8. Dispatch Outbox worker job
            if ($outboxId) {
                try {
                    ProcessCoinFeatureOutboxEventJob::dispatch((int) $outboxId)->afterCommit();
                } catch (\Throwable $dEx) {
                    Log::warning('CoinFeatureMutationService: Outbox job dispatch notice: '.$dEx->getMessage());
                }
            }

            return $result;
        } catch (HttpException $e) {
            if ($idempotencyKey) {
                DB::table('sys_idempotency_keys')
                    ->where('tenant_id', 0)
                    ->where('idempotency_key', $idempotencyKey)
                    ->delete();
            }

            throw $e;
        }
    }
}
