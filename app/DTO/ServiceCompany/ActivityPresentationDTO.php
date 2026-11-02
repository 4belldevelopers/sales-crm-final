<?php

namespace App\DTO\ServiceCompany;

/**
 * ActivityPresentationDTO
 *
 * Boundary-Safe Presentation Contract for Client & Agency Drawers.
 * Generated strictly AFTER relationship authorization and role-aware privacy transformations.
 * Raw internal client PII, confidential scorecards, and private notes are physically excluded.
 */
class ActivityPresentationDTO
{
    public function __construct(
        public readonly string $activityId,
        public readonly string $eventType,
        public readonly string $eventCategory,     // 'jobs' | 'candidates' | 'interviews' | 'offers' | 'pipeline' | 'crm'
        public readonly string $lifecycleState,    // 'active' | 'cancelled' | 'superseded'

        // Sanitized Target Entity Information
        public readonly int $jobId,
        public readonly string $jobTitle,
        public readonly ?int $candidateId,
        public readonly ?string $candidateName,
        public readonly ?string $fromStage,
        public readonly ?string $toStage,

        // Sanitized Actor Presentation (Governed by Viewer Policy)
        public readonly string $displayActorName,   // e.g., "Rahul Sharma" OR "Client Partner"
        public readonly string $displayActorRole,   // e.g., "Agency Captain" OR "Client Representative"
        public readonly ?string $displayActorAvatar,// Masked/Generic icon for Agency viewing Client
        public readonly string $displayCompanyName, // Masked "Client Partner" for Agency
        public readonly bool $isClientActor,

        // Public Collaborative Metadata (Internal notes & scorecards stripped)
        public readonly array $publicMetadata,      // e.g., ['interview_mode' => 'Google Meet', 'slot' => '11:00 AM']

        // Timezone Localization
        public readonly string $occurredAtUtc,
        public readonly string $relativeTime,       // "2 hours ago"
        public readonly string $localizedTimeDisplay,// "02 Nov 2026, 11:42 AM IST"
        public readonly string $sourceIndex = 'candidate_timeline' // Diagnostic origin
    ) {}

    /**
     * Convert to array for Blade / JSON rendering compatibility with existing templates.
     */
    public function toArray(): array
    {
        return [
            'event_id' => $this->activityId,
            'event_type' => $this->eventType,
            'category' => $this->eventCategory,
            'lifecycle_state' => $this->lifecycleState,
            'occurred_at' => $this->occurredAtUtc,
            'time_ago' => $this->relativeTime,
            'display_time' => $this->localizedTimeDisplay,
            'source' => $this->sourceIndex,
            'job' => [
                'id' => $this->jobId,
                'title' => $this->jobTitle,
                'shared_by_name' => $this->displayActorName,
                'shared_by_company' => $this->displayCompanyName,
            ],
            'candidate' => [
                'id' => $this->candidateId,
                'name' => $this->candidateName,
            ],
            'actor' => [
                'name' => $this->displayActorName,
                'role' => $this->displayActorRole,
                'company_name' => $this->displayCompanyName,
                'avatar' => $this->displayActorAvatar,
                'is_client' => $this->isClientActor,
            ],
            'attribution' => [
                'name' => $this->displayActorName,
                'role' => $this->displayActorRole,
                'company_name' => $this->displayCompanyName,
                'avatar' => $this->displayActorAvatar,
            ],
            'transition' => [
                'from_stage' => $this->fromStage,
                'to_stage' => $this->toStage,
                'master_process_name' => $this->toStage,
                'prev_process_name' => $this->fromStage,
            ],
            'metadata' => $this->publicMetadata,
        ];
    }
}
