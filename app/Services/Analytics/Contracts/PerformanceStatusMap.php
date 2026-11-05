<?php

namespace App\Services\Analytics\Contracts;

/**
 * Class PerformanceStatusMap
 *
 * Intent: Canonical Single Source of Truth for recruitment lifecycle stage definitions,
 * database master status IDs, and the Sourcing Gating Invariant.
 *
 * Sourcing Gating Rule (P0 Non-Negotiable Contract):
 * Candidate Uploaded ≠ Candidate Shared
 * Candidate moved to SOURCING = Candidate Shared
 *
 * Candidates in 'In Review' (Status 8) are internal to the Agency's working space
 * and are 100% invisible to the Direct Client until moved to Sourcing (Status 1).
 * The positive allow-list SHARED_PIPELINE_STATUSES = [1, 2, 3, 4, 5, 6] governs all client metrics.
 *
 * (c) 2026 JERO.WORKS. All rights reserved.
 */
class PerformanceStatusMap
{
    // Canonical Domain Stages
    public const STAGE_IN_REVIEW = 'IN_REVIEW';   // ID = 8 (Agency Internal Only)

    public const STAGE_SOURCING = 'SOURCING';    // ID = 1 (Official Submission Trigger)

    public const STAGE_SCREENING = 'SCREENING';   // ID = 2

    public const STAGE_INTERVIEW = 'INTERVIEW';   // ID = 3

    public const STAGE_EVALUATION = 'EVALUATION'; // ID = 3

    public const STAGE_NEGOTIATION = 'NEGOTIATION'; // ID = 4

    public const STAGE_OFFER = 'OFFER';       // ID = 5

    public const STAGE_JOINED = 'JOINED';      // ID = 6

    public const STAGE_REJECTED = 'REJECTED';    // ID = 7 (Count only ones marked 7; do not include 9)

    public const STAGE_ARCHIVED = 'ARCHIVED';    // ID = 9

    // Integer Status Identifiers
    public const STATUS_IN_REVIEW_INT = 8;

    public const STATUS_REJECTED_INT = 7;

    public const STATUS_REJECTED_DIRECT_INT = 9;

    public const STATUS_ARCHIVED_INT = 9;

    // Concrete Database / Elasticsearch Status ID Bindings
    public const STATUS_ID_MAP = [
        self::STAGE_IN_REVIEW => [8],
        self::STAGE_SOURCING => [1],
        self::STAGE_SCREENING => [2],
        self::STAGE_INTERVIEW => [3],
        self::STAGE_EVALUATION => [3],
        self::STAGE_NEGOTIATION => [4],
        self::STAGE_OFFER => [5],
        self::STAGE_JOINED => [6],
        self::STAGE_REJECTED => [7],
        self::STAGE_ARCHIVED => [9],
    ];

    public const REJECTED_STATUSES = [7];

    /**
     * Authoritative positive allow-list for candidates officially shared with the Direct Client.
     * Sourcing (1) through Joined (6).
     * Excludes In Review (8), Archived (9), and pre-sourcing rejections.
     */
    public const SHARED_PIPELINE_STATUSES = [1, 2, 3, 4, 5, 6];

    /**
     * Pipeline statuses strictly for the Performance Tab's Hiring Pipeline funnel (strictly statuses 1..6, 0% In Review).
     */
    public const PIPELINE_WITH_IN_REVIEW_STATUSES = [1, 2, 3, 4, 5, 6];

    // Canonical Pipeline Stage Status Sets (Discrete Recruitment Lifecycle Stages)
    public const MILESTONE_SOURCING_STATUSES = [1];

    public const MILESTONE_SCREENED_STATUSES = [2];

    public const MILESTONE_INTERVIEW_STATUSES = [3, 4];

    public const MILESTONE_OFFER_STATUSES = [5];

    public const MILESTONE_JOINED_STATUSES = [6];

    /**
     * Determine if a status ID belongs to the client-visible shared pipeline.
     */
    public static function isSharedPipelineStatus(int $statusId): bool
    {
        return in_array($statusId, self::SHARED_PIPELINE_STATUSES, true);
    }
}
