<?php

namespace App\Services\Analytics;

/**
 * ClientPerformanceConstants
 *
 * Single Source of Truth for constants, index mappings, status IDs,
 * and canonical aliases used in the Client Performance Tab analytics engine.
 *
 * Architectural Invariant:
 * Preserves historical baked-in naming conventions (e.g. ELASTIC_INERTVIEW_TRACKER)
 * per Rule 13, while exposing clean application-level constants.
 */
class ClientPerformanceConstants
{
    // Schema version
    public const SCHEMA_VERSION = 'v1.0';

    // Index config keys (resolves via config('constants...'))
    public const CONFIG_INDEX_CANDIDATES = 'constants.ELASTIC_CANDIDATES';
    public const CONFIG_INDEX_JOBS = 'constants.ELASTIC_JOBS';
    public const CONFIG_INDEX_INTERVIEWS = 'constants.ELASTIC_INERTVIEW_TRACKER';
    public const CONFIG_INDEX_CLIENTS = 'constants.ELASTIC_CLIENT_LIST';
    public const CONFIG_INDEX_TIMELINE = 'constants.ELASTIC_CANDIDATE_TIMELINE';
    public const CONFIG_INDEX_COMPANIES = 'constants.ELASTIC_COMPANIES_INDEX';

    // Status Milestone Identifiers (job_master_status_id)
    public const STATUS_SOURCING = 1;
    public const STATUS_SCREENING = 2;
    public const STATUS_INTERVIEW = 3;
    public const STATUS_NEGOTIATION = 4;
    public const STATUS_OFFER = 5;
    public const STATUS_JOINED = 6;
    public const STATUS_REJECTED = 7;
    public const STATUS_IN_REVIEW = 8;
    public const STATUS_ARCHIVED = 9;

    // Milestone set definitions for current active pipeline distribution
    public const MILESTONE_SCREENED_STATUSES = [2, 3, 4, 5, 6];
    public const MILESTONE_INTERVIEW_STATUSES = [3, 4, 5, 6];
    public const MILESTONE_OFFER_STATUSES = [5, 6];
    public const MILESTONE_JOINED_STATUSES = [6];

    // Master Process Names in ELASTIC_CANDIDATE_TIMELINE for historical stage progression
    public const TIMELINE_PROCESS_SOURCING = 'Sourcing';
    public const TIMELINE_PROCESS_SCREENING = 'Screening';
    public const TIMELINE_PROCESS_INTERVIEW = 'Evaluation';
    public const TIMELINE_PROCESS_NEGOTIATION = 'Negotiation';
    public const TIMELINE_PROCESS_OFFER = 'Offer';
    public const TIMELINE_PROCESS_JOINED = 'Joined';
    public const TIMELINE_PROCESS_REJECTED = 'Rejected';

    // Employment types mapped from GeneralMethod::employmentType()
    public const EMPLOYMENT_FULL_TIME = 0;
    public const EMPLOYMENT_CONTRACT = 1;
    public const EMPLOYMENT_FREELANCE = 2;
    public const EMPLOYMENT_PART_TIME = 3;
    public const EMPLOYMENT_UNSPECIFIED = -1;

    // Cache TTLs (in seconds)
    public const CACHE_TTL_SECONDS = 900; // 15 minutes fallback TTL
}
