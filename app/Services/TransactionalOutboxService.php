<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Class TransactionalOutboxService
 *
 * Intent: High-throughput dual-lease transactional outbox engine decoupling Elasticsearch indexing
 * from Reverb WebSocket broadcasting for 10K concurrent ATS recruitment pipelines.
 * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
 */
class TransactionalOutboxService
{
    /**
     * Check if the optimized pipeline is enabled for the specified tenant.
     * Complies with docs/CODE_DOCUMENTATION_STANDARD.md.
     */
    public function isOptimizedPipelineEnabled(int $tenantId): bool
    {
        if (! config('apptracker.enable_optimized_pipeline', true)) {
            return false;
        }

        $canaryTenants = config('apptracker.canary_tenant_ids', []);
        if (in_array($tenantId, $canaryTenants, true)) {
            return true;
        }

        $percentage = (int) config('apptracker.canary_percentage', 100);
        if ($percentage <= 0) {
            return false;
        }
        if ($percentage >= 100) {
            return true;
        }

        return ($tenantId % 100) < $percentage;
    }

    /**
     * Prune completed outbox records older than the retention threshold.
     */
    public function pruneCompletedEvents(?int $days = null): int
    {
        $retentionDays = $days ?? (int) config('apptracker.outbox_retention_days', 7);
        $cutoff = now()->subDays($retentionDays);

        return DB::table('transactional_outboxes')
            ->where('es_status', 'completed')
            ->where('reverb_status', 'completed')
            ->where('updated_at', '<', $cutoff)
            ->delete();
    }

    /**
     * Record an event in the transactional outbox table.

     * Must be called inside an active DB::transaction.
     */
    public function recordEvent(int $tenantId, int $jobId, int $version, string $eventType, array $payload): int
    {
        return (int) DB::table('transactional_outboxes')->insertGetId([
            'tenant_id' => $tenantId,
            'job_id' => $jobId,
            'version' => $version,
            'event_type' => $eventType,
            'payload' => json_encode($payload),
            'es_status' => 'pending',
            'es_attempts' => 0,
            'es_available_at' => now(),
            'reverb_status' => 'pending',
            'reverb_attempts' => 0,
            'reverb_available_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Atomically claim a batch of outbox records for Elasticsearch processing.
     * Reclaims expired 'processing' leases from crashed workers automatically.
     */
    public function claimEsBatch(string $claimToken, int $limit = 50, int $leaseSeconds = 30): Collection
    {
        // 1. Claim batch with single update
        $now = now();
        $lockedUntil = now()->addSeconds($leaseSeconds);

        DB::update(
            "UPDATE transactional_outboxes
             SET es_claim_token = ?,
                 es_status = 'processing',
                 es_locked_until = ?,
                 es_attempts = es_attempts + 1
             WHERE (
                 es_status IN ('pending', 'failed')
                 OR (es_status = 'processing' AND es_locked_until < ?)
             )
             AND (es_locked_until IS NULL OR es_locked_until < ?)
             AND es_available_at <= ?
             AND es_attempts < 5
             ORDER BY id ASC
             LIMIT ?",
            [$claimToken, $lockedUntil, $now, $now, $now, $limit]
        );

        // 2. Fetch claimed rows
        return DB::table('transactional_outboxes')
            ->where('es_claim_token', $claimToken)
            ->where('es_status', 'processing')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Mark an outbox record as successfully processed by Elasticsearch.
     */
    public function completeEsEvent(int $id): bool
    {
        return DB::table('transactional_outboxes')
            ->where('id', $id)
            ->update([
                'es_status' => 'completed',
                'es_processed_at' => now(),
                'es_claim_token' => null,
                'es_locked_until' => null,
                'updated_at' => now(),
            ]) > 0;
    }

    /**
     * Mark an outbox record as failed for Elasticsearch, scheduling exponential backoff retry or DLQ.
     */
    public function failEsEvent(int $id, string $error, int $maxAttempts = 5): bool
    {
        $record = DB::table('transactional_outboxes')->where('id', $id)->first();
        if (! $record) {
            return false;
        }

        $attempts = (int) $record->es_attempts;
        $isDeadLetter = $attempts >= $maxAttempts;
        $backoffSeconds = min(300, (int) (pow(2, $attempts) * 2)); // 4s, 8s, 16s, 32s... max 300s

        return DB::table('transactional_outboxes')
            ->where('id', $id)
            ->update([
                'es_status' => $isDeadLetter ? 'dead_letter' : 'failed',
                'es_available_at' => now()->addSeconds($backoffSeconds),
                'es_claim_token' => null,
                'es_locked_until' => null,
                'es_last_error' => substr($error, 0, 2000),
                'updated_at' => now(),
            ]) > 0;
    }

    /**
     * Atomically claim a batch of outbox records for Reverb broadcasting.
     * Reclaims expired 'processing' leases from crashed workers automatically.
     */
    public function claimReverbBatch(string $claimToken, int $limit = 50, int $leaseSeconds = 30): Collection
    {
        $now = now();
        $lockedUntil = now()->addSeconds($leaseSeconds);

        DB::update(
            "UPDATE transactional_outboxes
             SET reverb_claim_token = ?,
                 reverb_status = 'processing',
                 reverb_locked_until = ?,
                 reverb_attempts = reverb_attempts + 1
             WHERE (
                 reverb_status IN ('pending', 'failed')
                 OR (reverb_status = 'processing' AND reverb_locked_until < ?)
             )
             AND (reverb_locked_until IS NULL OR reverb_locked_until < ?)
             AND reverb_available_at <= ?
             AND reverb_attempts < 5
             ORDER BY id ASC
             LIMIT ?",
            [$claimToken, $lockedUntil, $now, $now, $now, $limit]
        );

        return DB::table('transactional_outboxes')
            ->where('reverb_claim_token', $claimToken)
            ->where('reverb_status', 'processing')
            ->orderBy('id', 'asc')
            ->get();
    }

    /**
     * Mark an outbox record as successfully delivered to Reverb.
     */
    public function completeReverbEvent(int $id): bool
    {
        return DB::table('transactional_outboxes')
            ->where('id', $id)
            ->update([
                'reverb_status' => 'completed',
                'reverb_processed_at' => now(),
                'reverb_claim_token' => null,
                'reverb_locked_until' => null,
                'updated_at' => now(),
            ]) > 0;
    }

    /**
     * Mark an outbox record as failed for Reverb, scheduling exponential backoff retry or DLQ.
     */
    public function failReverbEvent(int $id, string $error, int $maxAttempts = 5): bool
    {
        $record = DB::table('transactional_outboxes')->where('id', $id)->first();
        if (! $record) {
            return false;
        }

        $attempts = (int) $record->reverb_attempts;
        $isDeadLetter = $attempts >= $maxAttempts;
        $backoffSeconds = min(60, (int) (pow(2, $attempts)));

        return DB::table('transactional_outboxes')
            ->where('id', $id)
            ->update([
                'reverb_status' => $isDeadLetter ? 'dead_letter' : 'failed',
                'reverb_available_at' => now()->addSeconds($backoffSeconds),
                'reverb_claim_token' => null,
                'reverb_locked_until' => null,
                'reverb_last_error' => substr($error, 0, 2000),
                'updated_at' => now(),
            ]) > 0;
    }
}
