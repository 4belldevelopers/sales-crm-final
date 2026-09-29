<?php

return [
    /*
    |--------------------------------------------------------------------------
    | AppTracker Optimized Pipeline Feature Flags & Canary Settings
    |--------------------------------------------------------------------------
    |
    | Controls the phased rollout of the god-level ATS candidate assignment
    | optimization (dual-lease outbox, idempotency, Reverb delta sync).
    |
    */

    // Master kill-switch: if false, falls back cleanly to legacy execution path
    'enable_optimized_pipeline' => env('FEATURE_OPTIMIZED_SIMILAR_ADD_V2', true),

    // Canary rollout percentage (0 = off, 5 = 5%, 25 = 25%, 100 = full GA)
    'canary_percentage' => (int) env('FEATURE_SIMILAR_ADD_CANARY_PERCENTAGE', 100),

    // Specific tenant IDs explicitly whitelisted for early canary evaluation
    'canary_tenant_ids' => array_map('intval', array_filter(explode(',', (string) env('FEATURE_SIMILAR_ADD_CANARY_TENANTS', '')))),

    // Retention period for completed outbox events in days before pruning
    'outbox_retention_days' => (int) env('APPTRACKER_OUTBOX_RETENTION_DAYS', 7),
];
