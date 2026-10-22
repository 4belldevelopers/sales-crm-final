<?php

namespace App\Services;

use App\DTO\ServiceCompany\JobMutationContext;
use Illuminate\Support\Facades\DB;

/**
 * JobMutationService
 *
 * Handles atomic write mutations for Job requisitions.
 * In accordance with JERO CQRS & Transactional Outbox Standard:
 * 1. Pessimistic row locking ensures strictly monotonic aggregate version increments.
 * 2. SoR update, durable audit logging (job_status_logs), and outbox event
 *    emission belong in the exact same transactional boundary.
 * 3. Physical MySQL storage engine uniqueness is enforced via idx_outbox_tenant_idempotency.
 */
class JobMutationService
{
    public function updateStatus(int $jobId, string $newStatus, JobMutationContext $context): int
    {
        return DB::transaction(function () use ($jobId, $newStatus, $context) {
            // 1. Explicit pessimistic row lock guarantees monotonic aggregate versioning
            $job = DB::table('jobs')->where('id', $jobId)->lockForUpdate()->first();
            if (!$job) {
                throw new \InvalidArgumentException("Job requisition #{$jobId} not found.");
            }

            $oldStatus = (string) ($job->status ?? 'active');
            $currentVer = (int) ($job->aggregate_version ?? $job->state_version ?? 0);
            $nextVersion = $currentVer + 1;

            // 2. Transactional Update of System of Record (Persisting incremented version)
            DB::table('jobs')->where('id', $jobId)->update([
                'status'            => $newStatus,
                'state_version'     => $nextVersion,
                'aggregate_version' => $nextVersion,
                'updated_at'        => now(),
            ]);

            // 3. Synchronous Transactional Audit Log Insert (Guaranteed Durable)
            DB::table('job_status_logs')->insert([
                'job_id'      => $jobId,
                'company_id'  => $context->serviceCompanyId,
                'from_status' => $oldStatus,
                'to_status'   => $newStatus,
                'changed_by'  => $context->userId,
                'created_at'  => now(),
            ]);

            // 4. Durable Transactional Outbox Insert (Exact schema: mysql-schema.sql:4227)
            // Physical Storage Engine Uniqueness: UNIQUE KEY idx_outbox_tenant_idempotency (tenant_id, idempotency_key)
            $idempotencyKey = "job:{$jobId}:v{$nextVersion}";
            DB::table('sys_outbox_events')->insert([
                'tenant_id'         => $context->tenantId,
                'idempotency_key'   => $idempotencyKey,
                'aggregate_type'    => 'job',
                'aggregate_id'      => $jobId,
                'aggregate_version' => $nextVersion,
                'event_type'        => 'job.status.changed',
                'payload'           => json_encode([
                    'tenant_id'          => $context->tenantId,
                    'company_id'         => $context->serviceCompanyId,
                    'service_company_id' => $context->serviceCompanyId,
                    'job_id'             => $jobId,
                    'client_id'          => $context->clientId,
                    'client_type'        => $context->clientType,
                    'partner_company_id' => $context->partnerCompanyId ?? 0,
                    'from_status'        => $oldStatus,
                    'to_status'          => $newStatus,
                    'version'            => $nextVersion,
                    'occurred_at'        => now()->timestamp,
                ]),
                'status'       => 'PENDING',
                'attempts'     => 0,
                'available_at' => now(),
                'created_at'   => now(),
            ]);

            return $nextVersion;
        });
    }
}
