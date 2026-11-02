<?php

namespace App\DTO\ServiceCompany;

use App\Enums\ActivitySource;

/**
 * ActivityInternalContext
 *
 * Internal Domain Event representation for the Unified Activity Engine.
 * Strictly internal to the service engine: NEVER exposed or serialized directly to API/Blade.
 * Retains authoritative raw identifiers, scorecards, and performer metadata for internal processing.
 */
class ActivityInternalContext
{
    public function __construct(
        public readonly string $canonicalActivityId,
        public readonly ActivitySource $source,
        public readonly int|string $sourceRecordId,
        public readonly string $eventType,
        public readonly string $eventSubtype,
        public readonly string $lifecycleState, // 'active' | 'superseded' | 'cancelled' | 'redacted' | 'merged'

        // Domain Relationship Keys
        public readonly int $jobId,
        public readonly string $jobTitle,
        public readonly ?int $candidateId,
        public readonly ?string $candidateName,
        public readonly int $companyRelationshipId,
        public readonly int $jobAgencyRelationshipId,
        public readonly int $serviceRelationshipId,
        public readonly int $clientCompanyId,
        public readonly int $agencyCompanyId,

        // Internal Actor Context (Sensitive PII - Never exposed to third-party agency)
        public readonly int $actorUserId,
        public readonly int $actorCompanyId,
        public readonly string $actorCompanyType, // 'client' | 'agency' | 'system'
        public readonly string $actorRole,        // 'agency_captain' | 'client_representative' | 'interviewer' | 'system'
        public readonly string $rawActorName,     // Raw internal name
        public readonly ?string $rawActorAvatar,
        public readonly string $rawActorCompany,

        // Workflow States
        public readonly ?string $fromStage,
        public readonly ?string $toStage,
        public readonly string $workflowOwnerType, // 'agency' | 'client'

        // Privacy Classification & Confidential Raw Details
        public readonly string $privacyClassification, // 'public_collaborative' | 'client_confidential' | 'internal_eval'
        public readonly array $rawPayload,             // Internal notes, scorecards, raw ratings
        public readonly string $occurredAtUtc,
        public readonly int $timestampEpoch
    ) {}
}
