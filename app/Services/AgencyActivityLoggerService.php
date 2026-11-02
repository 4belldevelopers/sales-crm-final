<?php

namespace App\Services;

use App\Events\AgencyActivityLoggedEvent;
use App\Jobs\ProcessAgencyActivityOutboxJob;
use App\Models\AgencyActivityLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Str;

/**
 * Service: AgencyActivityLoggerService
 *
 * Dedicated Write-Gateway for the Agency 360° Relationship & Recruitment Audit Trail.
 * Enforces dual-store CQRS persistence with Transactional Outbox Pattern,
 * idempotency deduplication, multi-tenant atomic cache invalidation, and Reverb broadcasting.
 */
class AgencyActivityLoggerService
{
    public function __construct(
        protected ElasticService $elasticService
    ) {}

    /**
     * Core log emitter: writes to MySQL & Transactional Outbox, indexes to Elasticsearch, busts cache, and broadcasts.
     */
    public function logEvent(array $data): AgencyActivityLog
    {
        $agencyId = (int) ($data['agency_company_id'] ?? 0);
        $serviceCompanyId = (int) ($data['service_company_id'] ?? 0);
        $createdAt = ! empty($data['created_at']) ? Carbon::parse($data['created_at']) : now();
        $timestamp = $createdAt->timestamp;
        $sequence = (int) ($timestamp * 1000 + (mt_rand(1, 999)));

        // 1. Deterministic Idempotency Key Resolution
        $idempotencyKey = ! empty($data['idempotency_key'])
            ? (string) $data['idempotency_key']
            : hash('sha256', implode(':', [
                $agencyId,
                $serviceCompanyId,
                $data['event_type'],
                $data['entity_type'] ?? 'Agency',
                $data['entity_id'] ?? 0,
                (int) ($timestamp / 3), // 3-second deduplication window for identical mutations
            ]));

        // Idempotent Deduplication Check
        $existing = AgencyActivityLog::where('idempotency_key', $idempotencyKey)->first();
        if ($existing) {
            return $existing;
        }

        $uuid = (string) Str::uuid();
        $actorId = (int) ($data['actor_id'] ?? (auth()->id() ?? 0));
        $actorName = ! empty($data['actor_name']) ? trim((string) $data['actor_name']) : (auth()->check() ? auth()->user()->name : 'System');
        $actorRole = ! empty($data['actor_role']) ? trim((string) $data['actor_role']) : 'system';
        $source = ! empty($data['source']) ? trim((string) $data['source']) : 'INTERNAL_ATS';
        $aggregateVersion = (int) ($data['aggregate_version'] ?? 1);

        // 2. Elasticsearch Document Assembly
        $indexName = defined('ELASTIC_AGENCY_ACTIVITY_STREAM') ? ELASTIC_AGENCY_ACTIVITY_STREAM : 'local_agency_activity_stream';

        $esDoc = [
            'event_uuid' => $uuid,
            'agency_company_id' => $agencyId,
            'service_company_id' => $serviceCompanyId,
            'category' => $data['category'],
            'event_type' => $data['event_type'],
            'event_title' => $data['event_title'],
            'event_description' => $data['event_description'] ?? null,
            'actor_id' => $actorId,
            'actor_name' => $actorName,
            'actor_role' => $actorRole,
            'source' => $source,
            'entity_type' => $data['entity_type'] ?? 'Agency',
            'entity_id' => (int) ($data['entity_id'] ?? 0),
            'related_job_id' => ! empty($data['related_job_id']) ? (int) $data['related_job_id'] : null,
            'related_job_title' => $data['related_job_title'] ?? null,
            'related_candidate_id' => ! empty($data['related_candidate_id']) ? (int) $data['related_candidate_id'] : null,
            'related_candidate_name' => $data['related_candidate_name'] ?? null,
            'related_client_id' => ! empty($data['related_client_id']) ? (int) $data['related_client_id'] : null,
            'related_client_name' => $data['related_client_name'] ?? null,
            'previous_value' => $data['previous_value'] ?? null,
            'new_value' => $data['new_value'] ?? null,
            'metadata' => $data['metadata'] ?? null,
            'visibility_scope' => $data['visibility_scope'] ?? 'PUBLIC',
            'agency_activity_sequence' => $sequence,
            'created_at' => $createdAt->toIso8601String(),
        ];

        // 3. MySQL Transactional Outbox Pattern Write
        $outboxId = null;
        $logRecord = DB::transaction(function () use ($data, $uuid, $idempotencyKey, $aggregateVersion, $agencyId, $serviceCompanyId, $createdAt, $actorId, $actorName, $actorRole, $source, $esDoc, &$outboxId) {
            $record = AgencyActivityLog::create([
                'event_uuid' => $uuid,
                'idempotency_key' => $idempotencyKey,
                'agency_company_id' => $agencyId,
                'service_company_id' => $serviceCompanyId,
                'category' => $data['category'],
                'event_type' => $data['event_type'],
                'event_title' => $data['event_title'],
                'event_description' => $data['event_description'] ?? null,
                'actor_id' => $actorId,
                'actor_name' => $actorName,
                'actor_role' => $actorRole,
                'source' => $source,
                'entity_type' => $data['entity_type'] ?? 'Agency',
                'entity_id' => (int) ($data['entity_id'] ?? 0),
                'aggregate_version' => $aggregateVersion,
                'related_job_id' => ! empty($data['related_job_id']) ? (int) $data['related_job_id'] : null,
                'related_job_title' => $data['related_job_title'] ?? null,
                'related_candidate_id' => ! empty($data['related_candidate_id']) ? (int) $data['related_candidate_id'] : null,
                'related_candidate_name' => $data['related_candidate_name'] ?? null,
                'related_client_id' => ! empty($data['related_client_id']) ? (int) $data['related_client_id'] : null,
                'related_client_name' => $data['related_client_name'] ?? null,
                'previous_value' => $data['previous_value'] ?? null,
                'new_value' => $data['new_value'] ?? null,
                'metadata' => $data['metadata'] ?? null,
                'visibility_scope' => $data['visibility_scope'] ?? 'PUBLIC',
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ]);

            // Save to sys_outbox_events within same atomic transaction
            $outboxId = DB::table('sys_outbox_events')->insertGetId([
                'tenant_id' => $serviceCompanyId,
                'idempotency_key' => $idempotencyKey,
                'aggregate_type' => 'AgencyActivity',
                'aggregate_id' => $agencyId,
                'aggregate_version' => $aggregateVersion,
                'event_type' => $data['event_type'],
                'payload' => json_encode($esDoc),
                'status' => 'PENDING',
                'attempts' => 0,
                'available_at' => now(),
                'created_at' => now(),
            ]);

            return $record;
        });

        // 4. Elastic Read Store Synchronization (Direct with Outbox Fallback)
        $indexedToEs = false;
        try {
            if ($this->elasticService->indexExists($indexName)) {
                $this->elasticService->getClient()->index([
                    'index' => $indexName,
                    'id' => $uuid,
                    'body' => $esDoc,
                    'refresh' => true,
                ]);
                $indexedToEs = true;

                // Mark outbox completed
                if ($outboxId) {
                    DB::table('sys_outbox_events')
                        ->where('id', $outboxId)
                        ->update([
                            'status' => 'COMPLETED',
                            'processed_at' => now(),
                        ]);
                }
            }
        } catch (\Throwable $e) {
            Log::warning('AgencyActivityLoggerService: Immediate ES indexing failed; queuing outbox worker for retry', [
                'uuid' => $uuid,
                'outbox_id' => $outboxId,
                'error' => $e->getMessage(),
            ]);
            // Fallback: Dispatch background outbox worker with retry
            if ($outboxId) {
                ProcessAgencyActivityOutboxJob::dispatch($outboxId)->delay(now()->addSeconds(5));
            }
        }

        // 5. Multi-Tenant Atomic Cache Invalidation (Rule 8)
        $newVersion = 1;
        try {
            $vKey1 = "ats:agency_act_ver:{$serviceCompanyId}:{$agencyId}";
            $vKey2 = "ats:agency_act_ver:{$agencyId}:{$agencyId}";
            $newVersion = (int) Redis::incr($vKey1);
            Redis::incr($vKey2);
        } catch (\Throwable) {
        }

        // 6. Real-time Reverb WebSocket Broadcast
        try {
            if ($serviceCompanyId > 0) {
                broadcast(new AgencyActivityLoggedEvent($serviceCompanyId, $agencyId, $esDoc, $newVersion))->toOthers();
            }
            if ($agencyId > 0 && $agencyId !== $serviceCompanyId) {
                broadcast(new AgencyActivityLoggedEvent($agencyId, $agencyId, $esDoc, $newVersion))->toOthers();
            }
        } catch (\Throwable) {
        }

        return $logRecord;
    }

    /**
     * Domain Helper: Log Commercial Terms Update (Targeted Delta-Only Diff)
     */
    public function logCommercialTermsUpdated(int $agencyCompanyId, int $serviceCompanyId, array $prevTerms, array $newTerms, int $actorId, string $actorName): AgencyActivityLog
    {
        $desc = "Commercial staffing terms updated by {$actorName}.";
        if (isset($newTerms['fulltime_success_fee_percentage'])) {
            $prevFee = $prevTerms['fulltime_success_fee_percentage'] ?? '8.33';
            $currFee = $newTerms['fulltime_success_fee_percentage'];
            $desc = "Permanent Staffing fee changed from {$prevFee}% CTC to {$currFee}% CTC.";
        } elseif (isset($newTerms['contract_rate_amount'])) {
            $prevRate = $prevTerms['contract_rate_amount'] ?? 'Standard';
            $currRate = $newTerms['contract_rate_amount'];
            $desc = "Contract Staffing commercial rate updated to ₹{$currRate}/month.";
        }

        // Strict Delta-Only Diff Extraction (PII Minimization)
        $deltaPrev = [];
        $deltaNew = [];
        foreach ($newTerms as $key => $val) {
            if (! isset($prevTerms[$key]) || $prevTerms[$key] != $val) {
                $deltaPrev[$key] = $prevTerms[$key] ?? null;
                $deltaNew[$key] = $val;
            }
        }
        if (empty($deltaNew)) {
            $deltaPrev = $prevTerms;
            $deltaNew = $newTerms;
        }

        return $this->logEvent([
            'agency_company_id' => $agencyCompanyId,
            'service_company_id' => $serviceCompanyId,
            'category' => 'SERVICES',
            'event_type' => 'services.commercials_updated',
            'event_title' => 'Commercial Terms Updated',
            'event_description' => $desc,
            'actor_id' => $actorId,
            'actor_name' => $actorName,
            'actor_role' => 'admin',
            'source' => 'INTERNAL_ATS',
            'entity_type' => 'AgencyCommercialTerms',
            'entity_id' => $agencyCompanyId,
            'previous_value' => $deltaPrev,
            'new_value' => $deltaNew,
            'visibility_scope' => 'PUBLIC',
        ]);
    }

    /**
     * Domain Helper: Log Job Shared
     */
    public function logJobShared(int $agencyCompanyId, int $serviceCompanyId, int $jobId, string $jobTitle, int $actorId, string $actorName, ?int $clientId = null, ?string $clientName = null): AgencyActivityLog
    {
        return $this->logEvent([
            'agency_company_id' => $agencyCompanyId,
            'service_company_id' => $serviceCompanyId,
            'category' => 'JOBS',
            'event_type' => 'job.shared',
            'event_title' => "Requirement Shared: {$jobTitle}",
            'event_description' => "{$actorName} shared requisition '{$jobTitle}' with agency.",
            'actor_id' => $actorId,
            'actor_name' => $actorName,
            'actor_role' => 'account_lead',
            'source' => 'INTERNAL_ATS',
            'entity_type' => 'Job',
            'entity_id' => $jobId,
            'related_job_id' => $jobId,
            'related_job_title' => $jobTitle,
            'related_client_id' => $clientId,
            'related_client_name' => $clientName,
            'visibility_scope' => 'PUBLIC',
        ]);
    }

    /**
     * Domain Helper: Log Candidate Submitted
     */
    public function logCandidateSubmitted(int $agencyCompanyId, int $serviceCompanyId, int $candId, string $candName, int $jobId, string $jobTitle, int $actorId, string $actorName, ?int $clientId = null, ?string $clientName = null): AgencyActivityLog
    {
        return $this->logEvent([
            'agency_company_id' => $agencyCompanyId,
            'service_company_id' => $serviceCompanyId,
            'category' => 'CANDIDATES',
            'event_type' => 'candidate.submitted',
            'event_title' => "Candidate Submitted: {$candName}",
            'event_description' => "{$actorName} submitted candidate {$candName} for {$jobTitle}.",
            'actor_id' => $actorId,
            'actor_name' => $actorName,
            'actor_role' => 'agency_recruiter',
            'source' => 'AGENCY_PORTAL',
            'entity_type' => 'Candidate',
            'entity_id' => $candId,
            'related_job_id' => $jobId,
            'related_job_title' => $jobTitle,
            'related_candidate_id' => $candId,
            'related_candidate_name' => $candName,
            'related_client_id' => $clientId,
            'related_client_name' => $clientName,
            'visibility_scope' => 'PUBLIC',
        ]);
    }

    /**
     * Domain Helper: Log Interview Scheduled
     */
    public function logInterviewScheduled(int $agencyCompanyId, int $serviceCompanyId, int $interviewId, int $candId, string $candName, int $jobId, string $jobTitle, string $date, string $time, int $actorId, string $actorName, ?int $clientId = null, ?string $clientName = null): AgencyActivityLog
    {
        return $this->logEvent([
            'agency_company_id' => $agencyCompanyId,
            'service_company_id' => $serviceCompanyId,
            'category' => 'INTERVIEWS',
            'event_type' => 'interview.scheduled',
            'event_title' => "Interview Scheduled: {$candName}",
            'event_description' => "Evaluation round scheduled with {$candName} ({$jobTitle}) on {$date} at {$time} by {$actorName}.",
            'actor_id' => $actorId,
            'actor_name' => $actorName,
            'actor_role' => 'recruiter',
            'source' => 'INTERNAL_ATS',
            'entity_type' => 'Interview',
            'entity_id' => $interviewId,
            'related_job_id' => $jobId,
            'related_job_title' => $jobTitle,
            'related_candidate_id' => $candId,
            'related_candidate_name' => $candName,
            'related_client_id' => $clientId,
            'related_client_name' => $clientName,
            'visibility_scope' => 'PUBLIC',
        ]);
    }

    /**
     * Domain Helper: Log Interaction Note
     */
    public function logQuickNote(int $agencyCompanyId, int $serviceCompanyId, string $note, int $actorId, string $actorName, string $actorRole = 'recruiter'): AgencyActivityLog
    {
        return $this->logEvent([
            'agency_company_id' => $agencyCompanyId,
            'service_company_id' => $serviceCompanyId,
            'category' => 'COMMUNICATION',
            'event_type' => 'note.logged',
            'event_title' => 'Interaction Note Logged',
            'event_description' => $note,
            'actor_id' => $actorId,
            'actor_name' => $actorName,
            'actor_role' => $actorRole,
            'source' => 'INTERNAL_ATS',
            'entity_type' => 'AgencyNote',
            'entity_id' => $agencyCompanyId,
            'visibility_scope' => 'PUBLIC',
        ]);
    }

    /**
     * Domain Helper: Log Representative Assignment
     */
    public function logRepresentativeAssigned(int $agencyCompanyId, int $serviceCompanyId, int $jobId, string $jobTitle, int $oldUserId, string $oldUserName, int $newUserId, string $newUserName, int $actorId, string $actorName): AgencyActivityLog
    {
        return $this->logEvent([
            'agency_company_id' => $agencyCompanyId,
            'service_company_id' => $serviceCompanyId,
            'category' => 'TEAM',
            'event_type' => 'team.representative_changed',
            'event_title' => 'Agency Representative Reassigned',
            'event_description' => "{$actorName} changed representative for {$jobTitle} from {$oldUserName} to {$newUserName}.",
            'actor_id' => $actorId,
            'actor_name' => $actorName,
            'actor_role' => 'admin',
            'source' => 'INTERNAL_ATS',
            'entity_type' => 'JobAssign',
            'entity_id' => $jobId,
            'related_job_id' => $jobId,
            'related_job_title' => $jobTitle,
            'previous_value' => ['user_id' => $oldUserId, 'user_name' => $oldUserName],
            'new_value' => ['user_id' => $newUserId, 'user_name' => $newUserName],
            'visibility_scope' => 'PUBLIC',
        ]);
    }
}
