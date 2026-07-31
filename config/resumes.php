<?php
// Intent: central configuration for the resume upload v2 subsystem.
// Every magic number lives here — not in controllers, jobs, or JS.
// All feature flags default to OFF for safe roll-out.

return [

    /*
    |--------------------------------------------------------------------------
    | Feature flags (all OFF by default for safe incremental rollout)
    |--------------------------------------------------------------------------
    */
    'features' => [
        'presign_endpoint'     => (bool) env('RESUMES_FEATURE_PRESIGN', false),
        'browser_upload'       => (bool) env('RESUMES_FEATURE_BROWSER_UPLOAD', false),
        'finalize_endpoint'    => (bool) env('RESUMES_FEATURE_FINALIZE', false),
        'submit_with_session'  => (bool) env('RESUMES_FEATURE_SUBMIT_SESSION', false),
        'cancel'               => (bool) env('RESUMES_FEATURE_CANCEL', false),
        'refresh_recovery'     => (bool) env('RESUMES_FEATURE_REFRESH_RECOVERY', false),
        'multipart_upload'     => (bool) env('RESUMES_FEATURE_MULTIPART', false),
        'network_resilience'   => (bool) env('RESUMES_FEATURE_NETWORK_RESILIENCE', false),
        'broadcast_channel'    => (bool) env('RESUMES_FEATURE_BROADCAST_CHANNEL', false),
        'career_controller'    => (bool) env('RESUMES_FEATURE_CAREER_CONTROLLER', false),
        'reconcile_orphans'    => (bool) env('RESUMES_FEATURE_RECONCILE_ORPHANS', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cloudflare R2 — temp bucket (browser uploads land here)
    |--------------------------------------------------------------------------
    */
    'r2' => [
        'temp_bucket'     => env('CLOUDFLARE_R2_BUCKET', 'jero-resume-uploads-temp'),
        'temp_prefix'     => sprintf('resumes/%s/', date('Y/m/d')),
        'presign_ttl'     => (int) env('RESUMES_R2_PRESIGN_TTL', 900),        // 15 min
        'presign_ttl_min' => 60,   // minimum acceptable TTL before rotation
        'endpoint'        => env('CLOUDFLARE_R2_ENDPOINT'),
        'account_id'      => env('CLOUDFLARE_R2_ACCOUNT_ID'),
        'region'          => env('CLOUDFLARE_R2_REGION', 'auto'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Session lifecycle
    |--------------------------------------------------------------------------
    */
    'session' => [
        'ttl'             => 1800,  // 30 min — after this, session is Expired
        'expires_at_from' => 'created_at',
    ],

    /*
    |--------------------------------------------------------------------------
    | File validation
    |--------------------------------------------------------------------------
    */
    'validation' => [
        'max_file_size'       => 6_291_456,  // 6 MB
        'allowed_mime_types'  => [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'allowed_extensions'  => ['pdf', 'doc', 'docx'],
        'idempotency_key_len' => 64,  // UUID or sha256 hex
    ],

    /*
    |--------------------------------------------------------------------------
    | Rate limits
    |--------------------------------------------------------------------------
    */
    'rate_limits' => [
        'presign_per_user'  => 3,   // 3/min per recruiter
        'finalize_per_user' => 10,  // one per upload attempt; generous for retries
        'status_per_user'   => 60,  // polling endpoint; high limit
        'submit_per_user'   => 20,  // POST /job-applications per minute
    ],

    /*
    |--------------------------------------------------------------------------
    | Network resilience (Phase 6) — browser-side retry config
    | Injected as window.RESUMES_CONFIG in blade; JS module reads these values.
    |--------------------------------------------------------------------------
    */
    'network_resilience' => [
        // Maximum number of R2 PUT attempts (first try + retries).
        'max_put_attempts' => (int) env('RESUMES_MAX_PUT_ATTEMPTS', 3),
        // Base back-off delay in milliseconds; doubled each retry (1s → 2s → 4s).
        'put_backoff_ms'   => (int) env('RESUMES_PUT_BACKOFF_MS', 1000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Orphan reconciliation (cron sweep)
    |--------------------------------------------------------------------------
    */
    'reconcile' => [
        // Rows processed per DB query; keep ≤200 to avoid lock contention.
        'chunk_size'        => (int) env('RESUMES_RECONCILE_CHUNK', 100),
        // Expired sessions older than this many days are promoted to Reconciled.
        'expire_after_days' => (int) env('RESUMES_RECONCILE_EXPIRE_DAYS', 7),
    ],

    /*
    |--------------------------------------------------------------------------
    | Health endpoint
    |--------------------------------------------------------------------------
    */
    'health_token'           => env('RESUMES_HEALTH_TOKEN'),
    'metrics_require_redis'  => (bool) env('RESUMES_METRICS_REQUIRE_REDIS', true),
];
