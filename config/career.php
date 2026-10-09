<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Maximum Accepted Applications Per Job
    |--------------------------------------------------------------------------
    |
    | Defines the maximum number of accepted applications allowed for any
    | individual job posting on the JERO Career Apply portal. Once this threshold
    | is reached (default 1,000), intake is immediately sealed and further
    | submissions, resume uploads, and processing are rejected at the edge.
    |
    */
    'max_applications_per_job' => (int) env('CAREER_MAX_APPLICATIONS_PER_JOB', 1000),

    /*
    |--------------------------------------------------------------------------
    | Capacity Cache Settings
    |--------------------------------------------------------------------------
    |
    | TTL for capacity counters and closed status flags stored in Redis.
    | Default is 30 days (2,592,000 seconds).
    |
    */
    'capacity_cache_ttl' => (int) env('CAREER_CAPACITY_CACHE_TTL', 2592000),

    /*
    |--------------------------------------------------------------------------
    | Double-Checked Initialization Lock Timeout
    |--------------------------------------------------------------------------
    |
    | Lock duration (in seconds) used to prevent MySQL thundering herd during
    | cold-start initialization of capacity counters under 10k concurrent users.
    |
    */
    'init_lock_timeout' => (int) env('CAREER_CAPACITY_INIT_LOCK_TIMEOUT', 10),

    /*
    |--------------------------------------------------------------------------
    | Honeypot Bot Field
    |--------------------------------------------------------------------------
    |
    | Hidden input field name used to trap automated spam bots with zero overhead.
    |
    */
    'honeypot_field' => env('CAREER_HONEYPOT_FIELD', 'career_applicant_website_url'),
];
