<?php

// Intent: master feature-flag registry for JERO incremental rollouts.
// All flags default to false — safe to deploy before the feature is live.
// Usage: config('feature.<flag_name>')
// Toggle per environment: set the matching env var in .env or server config.

return [

    /*
    |--------------------------------------------------------------------------
    | Jobs Module — Three Drawers V3
    | (Job Status Drawer, Team Members Drawer, Add Agency Drawer)
    |
    | When true: drawer endpoints serve cached JSON instead of raw Blade HTML,
    | and background Horizon jobs handle write-path side effects.
    |
    | Redis cache keys used across all three drawers (1h TTL):
    |   status_drawer:{company_id}:{job_id}
    |   team_drawer:{company_id}:{job_id}
    |   agency_drawer:{company_id}:{job_id}:{page}
    |--------------------------------------------------------------------------
    */
    /*
    |--------------------------------------------------------------------------
    | Account Module V2 Optimization
    |
    | When true: uses lazy-loaded tab partials, Redis reference data caching,
    | and consolidated DB queries for Account Settings V2.
    |--------------------------------------------------------------------------
    */
    'account_v2' => (bool) env('FEATURE_ACCOUNT_V2', true),

    /*
    |--------------------------------------------------------------------------
    | Jobs Module V2 Engine
    |
    | When true: uses JobService, Redis JobCacheKeys, EnsureIdempotentSubmission,
    | JobConcurrencyLock, and asynchronous Horizon queue offloading for Jobs module.
    |--------------------------------------------------------------------------
    */
    'jobs_v2_enabled' => (bool) env('FEATURE_JOBS_V2_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Talent (Bench Candidates) Module V2 Engine
    |
    | Intent: Controls feature activation for Talent V2 optimizations, including
    | async Elasticsearch sync, bulk skill inserts, Redis cache tags, and backend
    | idempotency. Defaults to false for zero-impact deployment.
    |--------------------------------------------------------------------------
    */
    'talents_v2_enabled' => (bool) env('FEATURE_TALENTS_V2_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Team View More Module V2 Engine
    |
    | Intent: Controls feature activation for Team View More V2 optimizations, including
    | True Tab-Level Lazy Loading, lightweight initial shell, server-side tenant scoping,
    | Redis Cache::lock stampede protection, and request debouncing.
    | Defaults to true.
    |--------------------------------------------------------------------------
    */
    'team_viewmore_v2_enabled' => (bool) env('FEATURE_TEAM_VIEWMORE_V2_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Forward Resume V4.1 Engine
    |
    | Intent: Master feature flag for V4.1 Client-First, Zero-Synchronous Read
    | Forward Resume engine. Controls micro-JSON hydration, CKEditor singleton,
    | decoupled static assets, and direct Cloudflare R2 uploads.
    | Defaults to true.
    |--------------------------------------------------------------------------
    */
    'fwd_resume_v4_enabled' => (bool) env('FEATURE_FWD_RESUME_V4_ENABLED', true),

];
