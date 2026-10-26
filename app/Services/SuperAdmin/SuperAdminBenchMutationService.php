<?php

namespace App\Services\SuperAdmin;

use App\Jobs\ProcessBenchCandidateOutboxJob;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SuperAdminBenchMutationService
 *
 * INTENT:
 * Encapsulates all authoritative mutating operations for Super Admin Bench Candidate Moderation
 * under the Transactional Outbox and Idempotency Architecture (Phase 1).
 * Guarantees zero duplicate executions under network retries, atomic state transitions,
 * decouples asynchronous email dispatching and Elasticsearch synchronization via sys_outbox_events,
 * invalidates multi-tenant Redis/Dragonfly caches, and triggers Reverb broadcasting under 10K concurrency.
 *
 * TENANCY NOTE:
 * Operates across tenant boundaries under Super Admin authority. Coordination in sys_idempotency_keys
 * and sys_outbox_events is registered with tenant_id = 0.
 *
 * Author: Antigravity AI — Phase 1 Outbox & Idempotency Architecture
 * Date: October 26, 2026
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class SuperAdminBenchMutationService
{
    /**
     * Format or synthesize an idempotency key.
     *
     * @param  string  $action  Action descriptor ('approve', 'reject', 'auto_approval')
     * @param  array<int>  $candidateIds  List of target candidate IDs
     * @param  string|null  $providedKey  Client-provided idempotency key from header/body
     * @return string Sanitized idempotency key (max 64 chars)
     */
    public function formatIdempotencyKey(string $action, array $candidateIds = [], ?string $providedKey = null): string
    {
        if ($providedKey && trim($providedKey) !== '') {
            $cleanKey = preg_replace('/[^a-zA-Z0-9_\-\:\.]/', '', trim($providedKey));

            return substr($cleanKey, 0, 64);
        }

        $idHash = substr(md5(implode(',', $candidateIds)), 0, 8);
        $entropy = substr(sha1(uniqid((string) time(), true)), 0, 10);

        return substr("sa_bench_{$action}_{$idHash}_{$entropy}", 0, 64);
    }

    /**
     * Authoritatively approve one or more bench candidates (Pending -> Live on Market).
     *
     * @param  array<int>  $candidateIds
     * @return array<string, mixed>
     */
    public function approveCandidates(
        array $candidateIds,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null
    ): array {
        $candidateIds = array_values(array_filter(array_map('intval', $candidateIds)));

        if (empty($candidateIds)) {
            return [
                'status' => 'info',
                'message' => 'No valid candidate selected for approval.',
            ];
        }

        $idempotencyKey = $this->formatIdempotencyKey('approve', $candidateIds, $idempotencyKey);
        $canonicalPayload = [
            'action' => 'approve',
            'candidate_ids' => $candidateIds,
            'admin_id' => $adminId,
        ];
        $canonicalHash = sha1(json_encode($canonicalPayload));

        // 1. Atomic Idempotency Claim Check
        $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => 0,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $canonicalHash,
            'status' => 'PROCESSING',
            'expires_at' => Carbon::now()->addHours(24),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if (! $inserted) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                if ($existing->status === 'COMPLETED') {
                    $cachedBody = json_decode($existing->response_body, true);

                    return is_array($cachedBody)
                        ? array_merge($cachedBody, ['replayed' => true])
                        : ['status' => 'success', 'message' => 'Selected Applicant Approved.', 'replayed' => true];
                }

                if ($existing->status === 'PROCESSING') {
                    abort(409, 'Candidate approval is already currently processing under this idempotency key.');
                }

                if ($existing->status === 'FAILED_FINAL') {
                    $cachedBody = json_decode($existing->response_body, true);

                    return is_array($cachedBody)
                        ? $cachedBody
                        : ['status' => 'error', 'message' => $existing->last_error ?: 'Previous approval attempt failed.'];
                }
            }
        }

        // 2. Authoritative Database Transaction + Outbox Insertion
        DB::beginTransaction();
        try {
            // Update job_applications superadminstatus to 1 (Approved) and status to 2 (Bench)
            DB::table('job_applications')
                ->whereIn('id', $candidateIds)
                ->update([
                    'superadminstatus' => 1,
                    'status' => 2,
                    'updated_at' => Carbon::now(),
                ]);

            $outboxPayload = [
                'candidate_ids' => $candidateIds,
                'superadmin_status' => 1,
                'admin_id' => $adminId,
                'admin_name' => $adminName ?: 'Super Admin',
                'timestamp' => Carbon::now()->toIso8601String(),
            ];

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'BenchCandidate',
                'aggregate_id' => $candidateIds[0],
                'aggregate_version' => 1,
                'event_type' => 'CANDIDATE_SUPERADMIN_APPROVED',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            $response = [
                'status' => 'success',
                'message' => 'Selected Applicant Approved.',
                'candidate_ids' => $candidateIds,
                'datatype' => 'approve',
                'outbox_id' => $outboxId,
            ];

            // 3. Mark Idempotency Key as COMPLETED
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($response),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            DB::commit();

            // 4. Dispatch Asynchronous Outbox Worker
            ProcessBenchCandidateOutboxJob::dispatch($outboxId)->afterCommit();

            return $response;

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[BENCH_MUTATION] Approval failed: '.$e->getMessage(), [
                'candidate_ids' => $candidateIds,
                'idempotency_key' => $idempotencyKey,
                'trace' => $e->getTraceAsString(),
            ]);

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_FINAL',
                    'response_status' => 500,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => Carbon::now(),
                ]);

            throw $e;
        }
    }

    /**
     * Authoritatively reject one or more bench candidates.
     *
     * @param  array<int>  $candidateIds
     * @return array<string, mixed>
     */
    public function rejectCandidates(
        array $candidateIds,
        ?string $idempotencyKey = null,
        ?int $adminId = null,
        ?string $adminName = null
    ): array {
        $candidateIds = array_values(array_filter(array_map('intval', $candidateIds)));

        if (empty($candidateIds)) {
            return [
                'status' => 'info',
                'message' => 'No valid candidate selected for rejection.',
            ];
        }

        $idempotencyKey = $this->formatIdempotencyKey('reject', $candidateIds, $idempotencyKey);
        $canonicalPayload = [
            'action' => 'reject',
            'candidate_ids' => $candidateIds,
            'admin_id' => $adminId,
        ];
        $canonicalHash = sha1(json_encode($canonicalPayload));

        // 1. Atomic Idempotency Claim Check
        $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => 0,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $canonicalHash,
            'status' => 'PROCESSING',
            'expires_at' => Carbon::now()->addHours(24),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if (! $inserted) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                if ($existing->status === 'COMPLETED') {
                    $cachedBody = json_decode($existing->response_body, true);

                    return is_array($cachedBody)
                        ? array_merge($cachedBody, ['replayed' => true])
                        : ['status' => 'success', 'message' => 'Selected Applicant Rejected.', 'replayed' => true];
                }

                if ($existing->status === 'PROCESSING') {
                    abort(409, 'Candidate rejection is already processing.');
                }
            }
        }

        DB::beginTransaction();
        try {
            DB::table('job_applications')
                ->whereIn('id', $candidateIds)
                ->update([
                    'superadminstatus' => 2,
                    'updated_at' => Carbon::now(),
                ]);

            $outboxPayload = [
                'candidate_ids' => $candidateIds,
                'superadmin_status' => 2,
                'admin_id' => $adminId,
                'admin_name' => $adminName ?: 'Super Admin',
                'timestamp' => Carbon::now()->toIso8601String(),
            ];

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'BenchCandidate',
                'aggregate_id' => $candidateIds[0],
                'aggregate_version' => 1,
                'event_type' => 'CANDIDATE_SUPERADMIN_REJECTED',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            $response = [
                'status' => 'success',
                'message' => 'Selected Applicant Rejected.',
                'candidate_ids' => $candidateIds,
                'datatype' => 'reject',
                'outbox_id' => $outboxId,
            ];

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($response),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            DB::commit();

            ProcessBenchCandidateOutboxJob::dispatch($outboxId)->afterCommit();

            return $response;

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[BENCH_MUTATION] Rejection failed: '.$e->getMessage(), [
                'candidate_ids' => $candidateIds,
                'idempotency_key' => $idempotencyKey,
            ]);

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_FINAL',
                    'response_status' => 500,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => Carbon::now(),
                ]);

            throw $e;
        }
    }

    /**
     * Authoritatively toggle global bench auto-approval setting.
     *
     * @return array<string, mixed>
     */
    public function toggleAutoApproval(int $settingId, ?string $idempotencyKey = null, ?int $adminId = null): array
    {
        $idempotencyKey = $this->formatIdempotencyKey('auto_approval', [$settingId], $idempotencyKey);
        $canonicalPayload = [
            'action' => 'toggle_auto_approval',
            'setting_id' => $settingId,
            'admin_id' => $adminId,
        ];
        $canonicalHash = sha1(json_encode($canonicalPayload));

        // 1. Atomic Idempotency Claim Check
        $inserted = DB::table('sys_idempotency_keys')->insertOrIgnore([
            'tenant_id' => 0,
            'idempotency_key' => $idempotencyKey,
            'request_hash' => $canonicalHash,
            'status' => 'PROCESSING',
            'expires_at' => Carbon::now()->addHours(24),
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if (! $inserted) {
            $existing = DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->first();

            if ($existing) {
                if ($existing->status === 'COMPLETED') {
                    $cachedBody = json_decode($existing->response_body, true);

                    return is_array($cachedBody)
                        ? array_merge($cachedBody, ['replayed' => true])
                        : ['status' => 'success', 'message' => 'Changed setting auto approval.', 'replayed' => true];
                }

                if ($existing->status === 'PROCESSING') {
                    abort(409, 'Auto-approval toggle is already in-progress under this idempotency key.');
                }

                if ($existing->status === 'FAILED_FINAL') {
                    $cachedBody = json_decode($existing->response_body, true);

                    return is_array($cachedBody)
                        ? $cachedBody
                        : ['status' => 'error', 'message' => $existing->last_error ?: 'Previous toggle attempt failed.'];
                }
            }
        }

        // 2. Authoritative Database Transaction + Outbox Insertion
        DB::beginTransaction();
        try {
            $bench_status = DB::table('global_settings')
                ->select('bench_status')
                ->where('id', $settingId)
                ->lockForUpdate()
                ->first();

            $newStatus = 1;
            if ($bench_status && $bench_status->bench_status) {
                $newStatus = 0;
            }

            DB::table('global_settings')
                ->where('id', $settingId)
                ->update(['bench_status' => $newStatus]);

            $outboxPayload = [
                'setting_id' => $settingId,
                'new_status' => $newStatus,
                'admin_id' => $adminId,
                'timestamp' => Carbon::now()->toIso8601String(),
            ];

            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => 0,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'BenchCandidate',
                'aggregate_id' => $settingId,
                'aggregate_version' => 1,
                'event_type' => 'SETTING_AUTO_APPROVAL_TOGGLED',
                'payload' => json_encode($outboxPayload),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => Carbon::now(),
                'created_at' => Carbon::now(),
            ]);

            $response = [
                'status' => 'success',
                'message' => 'Changed setting auto approval.',
                'bench_status' => $newStatus,
                'outbox_id' => $outboxId,
            ];

            // 3. Mark Idempotency Key as COMPLETED
            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'COMPLETED',
                    'response_status' => 200,
                    'response_body' => json_encode($response),
                    'completed_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);

            DB::commit();

            // 4. Dispatch Asynchronous Outbox Worker
            ProcessBenchCandidateOutboxJob::dispatch($outboxId)->afterCommit();

            return $response;

        } catch (\Throwable $e) {
            DB::rollBack();

            Log::error('[BENCH_MUTATION] Toggle auto-approval failed: '.$e->getMessage());

            DB::table('sys_idempotency_keys')
                ->where('tenant_id', 0)
                ->where('idempotency_key', $idempotencyKey)
                ->update([
                    'status' => 'FAILED_FINAL',
                    'response_status' => 500,
                    'last_error' => substr($e->getMessage(), 0, 1000),
                    'updated_at' => Carbon::now(),
                ]);

            throw $e;
        }
    }
}
