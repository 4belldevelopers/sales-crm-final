<?php

/**
 * JERO ATS Core Architecture Configuration
 *
 * Configures the transactional outbox engine, multi-tenant search relevance weights,
 * and distributed caching parameters in accordance with JERO Architecture Standard (Rule 11).
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Transactional Outbox Worker Configuration
    |--------------------------------------------------------------------------
    |
    | Governs the behavior of Horizon outbox projection workers processing
    | sys_outbox_events. All batch sizes, claim timeouts, and retry policies
    | are parameterized here to prevent hardcoding.
    |
    */

    'outbox' => [
        'batch_size'                 => (int) env('ATS_OUTBOX_BATCH_SIZE', 50),
        'lock_timeout_seconds'       => (int) env('ATS_OUTBOX_LOCK_TIMEOUT_SECONDS', 300),
        'max_attempts'               => (int) env('ATS_OUTBOX_MAX_ATTEMPTS', 5),
        'retry_backoff_seconds'      => (int) env('ATS_OUTBOX_RETRY_BACKOFF_SECONDS', 60),
        'dead_letter_after_attempts' => (int) env('ATS_OUTBOX_DEAD_LETTER_AFTER_ATTEMPTS', 5),
    ],

    /*
    |--------------------------------------------------------------------------
    | Elasticsearch Search Relevance Configuration
    |--------------------------------------------------------------------------
    |
    | Multi-match query field weights, boolean operator, and match thresholds
    | for the Jobs Tab Elasticsearch read queries.
    |
    */

    'search' => [
        'jobs' => [
            'fields' => [
                'title.keyword^5',
                'title^3',
                'skills.name^2',
                'job_code^4',
                'hiring_manager^1.5',
            ],
            'operator'             => env('ATS_SEARCH_JOBS_OPERATOR', 'AND'),
            'minimum_should_match' => env('ATS_SEARCH_JOBS_MINIMUM_MATCH', '75%'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Distributed Cache & Versioning Policy
    |--------------------------------------------------------------------------
    |
    | Redis / Dragonfly multi-tenant query cache TTL and version key parameters.
    |
    */

    'cache' => [
        'query_ttl_seconds'       => (int) env('ATS_QUERY_CACHE_TTL_SECONDS', 300), // 5 minutes
        'version_ttl_seconds'     => (int) env('ATS_VERSION_CACHE_TTL_SECONDS', 86400 * 30), // 30 days
        'auth_context_ttl_seconds'=> (int) env('ATS_AUTH_CONTEXT_TTL_SECONDS', 600), // 10 minutes
    ],

];
