<?php

/**
 * Feature flags and operational knobs for the resume upload subsystem.
 *
 * Lives in config/ (not env) because Bug-02 in BUG_REPORT says env() returns
 * null under config:cache + Octane. Use config('resumes.use_presigned_put')
 * everywhere; set the value via the deployment pipeline.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Phase 4 — Presigned R2 PUT opt-in
    |--------------------------------------------------------------------------
    |
    | When true, the JS shim in /js/resume-presign.js will request a presigned
    | PUT URL from POST /api/resume-sessions/presign and upload bytes directly
    | to R2, bypassing PHP. When false (or when the presign endpoint returns
    | 422 with code=presign_unavailable), the JS falls back to the existing
    | multipart POST to /uploadresume.
    |
    | Roll-out plan:
    |   - Keep FALSE in production until ops confirms R2 CORS allows PUT
    |     from the front origin with the Content-Type header. See the CORS
    |     config block below and the operational note at the top of
    |     public/js/resume-presign.js.
    |   - Flip to TRUE per-tenant when smoke-tested on a recruiter account.
    |   - Once stable across one payroll cycle, flip to TRUE globally.
    */

    'use_presigned_put' => env('RESUMES_USE_PRESIGNED_PUT', false),

    /*
    |--------------------------------------------------------------------------
    | R2 CORS — operational prerequisites (NOT enforced in code)
    |--------------------------------------------------------------------------
    |
    | The presigned-PUT path requires Cloudflare R2 to accept PUTs from the
    | browser origin with explicit Content-Type + Content-Length headers.
    | Configure the R2 bucket CORS rules via Cloudflare dashboard or via the
    | wrangler/r2 CLI:
    |
    |   [
    |     {
    |       "AllowedOrigins":   ["https://app.jero.works", "https://staging.jero.works"],
    |       "AllowedMethods":   ["PUT"],
    |       "AllowedHeaders":   ["Content-Type", "Content-Length"],
    |       "ExposeHeaders":    ["ETag"],
    |       "MaxAgeSeconds":    300
    |     }
    |   ]
    |
    | Without this, browsers block the PUT with a CORS error in DevTools and
    | the user sees a stalled upload. The presign endpoint still returns 200;
    | the failure shows only AFTER the JS calls fetch() on the signed URL.
    |
    | Until this is configured: leave use_presigned_put=false so the JS shim
    | falls back to /uploadresume (multipart).
    */
    'r2_cors_origin' => env('RESUMES_R2_CORS_ORIGIN', 'https://app.jero.works'),

    /*
    |--------------------------------------------------------------------------
    | Phase 5 — Reconciliation knobs
    |--------------------------------------------------------------------------
    |
    | All knobs are env-driven so the production rollout can override them
    | per tenant / per environment. Keep defaults at sane, non-aggressive
    | values so a misconfigured env never culls aggressively.
    */
    'orphan_grace_hours' => (int) env('RESUMES_ORPHAN_GRACE_HOURS', 24),
    'reconcile_batch_size' => (int) env('RESUMES_RECONCILE_BATCH_SIZE', 200),
    'reconcile_schedule' => env('RESUMES_RECONCILE_SCHEDULE', 'weekly'),

    'delete_attempt_max' => (int) env('RESUMES_DELETE_ATTEMPT_MAX', 3),

    /*
    |--------------------------------------------------------------------------
    | Phase 5 — Stuck-session repair knobs
    |--------------------------------------------------------------------------
    */
    'uploading_timeout_minutes' => (int) env('RESUMES_UPLOADING_TIMEOUT_MIN', 30),
    'parsing_timeout_minutes' => (int) env('RESUMES_PARSING_TIMEOUT_MIN', 60),
    'repair_dispatch_max' => (int) env('RESUMES_REPAIR_DISPATCH_MAX', 3),

    /*
    |--------------------------------------------------------------------------
    | Phase 5 — Metrics + Alerts
    |--------------------------------------------------------------------------
    |
    | Counter increments live in Laravel cache (Redis -> DB fallback). The
    | alert gauges are read live at log time, not polled — the alert path
    | is one `Log::warning('jobs.resume.alert')` invocation the operator
    | pipeline picks up.
    |
    | Order of work for a production rollout:
    |   1. Deploy with metrics_enabled=false and alerts_enabled=false.
    |   2. After 1 week: flip metrics_enabled=true, collect a baseline.
    |   3. After 2 weeks: flip alerts_enabled=true with thresholds tuned
    |      from observed baseline (see alert_thresholds.* below for shape).
    */
    'metrics_enabled' => (bool) env('RESUMES_METRICS_ENABLED', false),
    'alerts_enabled' => (bool) env('RESUMES_ALERTS_ENABLED', false),
    /*
    | Phase 6 — Redis-as-shared-cache contract.
    |
    | When true (default) ResumeMetrics MUST be served by the redis
    | cache backend. The constructor of ResumeMetrics asserts this and
    | throws RuntimeException otherwise. Production MIs rely on this
    | fail-fast so a stale config:cache or wrong .env gets caught at
    | boot, not at the first INCRBY.
    |
    | Tests override this flag because the array cache driver is the
    | hermetic test fixture. Override only in phpunit.xml or per-test
    | via config(['resumes.metrics_require_redis' => false]).
    */
    'metrics_require_redis' => (bool) env('RESUMES_METRICS_REQUIRE_REDIS', true),

    /*
    | Alert thresholds (Phase 5). Each key maps to one alertable signal;
    | the inner map keys are "window" strings ("last_hour", "last_day")
    | and the values are ints. A threshold of 0 disables that window.
    |
    | These are intentionally not hardcoded; ops tunes them from baseline.
    */
    'alert_thresholds' => [
        'orphan_uploads' => [
            'current' => (int) env('RESUMES_ALERT_ORPHAN_CURRENT', 50),
        ],
        'stuck_sessions' => [
            'current' => (int) env('RESUMES_ALERT_STUCK_CURRENT', 20),
        ],
        'delete_failures' => [
            'last_hour' => (int) env('RESUMES_ALERT_DELETE_FAILURES_HOUR', 10),
        ],
        'finalize_failures' => [
            'last_hour' => (int) env('RESUMES_ALERT_FINALIZE_FAILURES_HOUR', 25),
        ],
        'parser_fail_rate' => [
            // alert when fail_count / total_count over the last hour
            // exceeds this ratio. Separate from the queue-based parser
            // (Phase 8) so ops can spot a vendor-vs-smalot inline-parser
            // regression without merging counters.
            'last_hour_ratio' => (float) env('RESUMES_ALERT_PARSER_FAIL_RATIO', 0.25),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Phase 8 — Dedicated parse queue knobs
    |--------------------------------------------------------------------------
    |
    | The parser is on its own queue (see config/queue.php's
    | redis_parse_resumes connection and config/horizon.php's
    | parse-supervisor-1) so vendor RTT cannot starve the mail/elastic
    | stream. parser_vendor lets ops flip the integration via env
    | without code change; today `phase2_stub` is the safe default.
    */
    'parser_connection' => env('RESUMES_PARSER_CONNECTION', 'redis_parse_resumes'),
    'parser_queue' => env('RESUMES_PARSER_QUEUE', 'parse_resumes'),
    'parser_vendor' => env('RESUMES_PARSER_VENDOR', 'phase2_stub'),

    /*
    |--------------------------------------------------------------------------
    | Phase 6 — Internal health token
    |--------------------------------------------------------------------------
    |
    | Random bearer token gating /api/internal/health/resume. Generate
    | per env via `openssl rand -hex 32` and add to .env on each pod.
    | Empty token = endpoint refuses all probes (no unauthenticated
    | fallback by design).
    */
    'health_token' => env('RESUMES_HEALTH_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Phase 9 follow-up — inline parser knobs
    |--------------------------------------------------------------------------
    |
    | The /api/resume-parser/parse endpoint runs in the request thread —
    | a single 5 MB PDF parse pins a PHP-FPM worker for the duration of
    | the smalot extraction. Three knobs bound the blast radius:
    |
    |   parser_max_kb        — request size cap (KB).  Default 5120 = 5 MB.
    |                           Smaller than the upload pipeline because
    |                           PDF parsing builds a heavy in-memory DOM.
    |   parser_timeout_seconds — per-file extraction budget. Default 5s.
    |   parser_submit_wait_ms — how long the recruiter's submit handler
    |                           waits for the inline parse to resolve
    |                           before proceeding. Default 2500 ms.
    |
    | parser_rate_limit_per_min — per-recruiter per-minute cap. Default 10;
    |                              0 disables. Redis-backed; falls through
    |                              silently if Redis is unreachable so a
    |                              Redis outage does not lock recruiters
    |                              out of parsing.
    */
    'parser_max_kb' => (int) env('RESUMES_PARSER_MAX_KB', 5120),
    'parser_timeout_seconds' => (float) env('RESUMES_PARSER_TIMEOUT_SECONDS', 5.0),
    'parser_submit_wait_ms' => (int) env('RESUMES_PARSER_SUBMIT_WAIT_MS', 2500),
    'parser_rate_limit_per_min' => (int) env('RESUMES_PARSER_RATE_LIMIT_PER_MIN', 10),
];
