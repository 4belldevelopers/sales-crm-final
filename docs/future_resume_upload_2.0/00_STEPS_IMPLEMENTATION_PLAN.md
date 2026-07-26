# Implementation Plan — 17 Steps to v2.0 GA

> *Intent: the actionable build plan. Each step is independently shippable,*
> *has a feature flag defaulting to OFF, and requires passing staging smoke*
> *test before the next step begins.*

## Correction: the parser is client-side

The v1 resume parser runs **entirely in the browser** — pdf.js and mammoth
extract text from the `File` object in memory, then `re_test()` regex-parses
name/email/phone and fills the form fields **instantly, in parallel** with
the file upload. **No server-side parse, no ChatGPT, no queue.**

This means:
- **No `ParseResumeJob`** — not needed
- **No `ResumeParserService`** — not needed 
- **No Reverb events for parser progress** — parsing is in-browser, instant
- **No populate-if-empty JS** — `re_test()` already fills fields inline

The v2 plan removes the **upload** (PHP multipart POST → direct R2 PUT)
but leaves the **client-side parse** completely untouched.

## How to use this plan

1. Start at **Step 1**. Do not skip ahead.
2. **Feature flag OFF** → code shipped. Flip flag **ON** → smoke test
   in staging (the "Test on staging" checklist at each step).
3. If staging smoke fails → fix → re-test. Do NOT proceed to next step
   until current step passes.
4. After every 5 steps: `php artisan test --testsuite=Feature --filter=ResumeV2`
   full regression.
5. The `NEVER` rules from [`98_failure_matrix_never.md`](98_failure_matrix_never.md)
   are PR review gates: any PR that violates one is closed regardless
   of how good the rest is.

---

## Step 1 — Environment baseline + health endpoint

**Purpose:** Prove the infrastructure can serve requests before any
upload code exists.

**Feature flag:** N/A (always-on infra)

**What we build:**
- `config/resumes.php` v2 entries (metrics_require_redis, health_token)
- `app/Http/Controllers/Api/ResumeHealthController.php`
- `app/Http/Middleware/InternalTokenMiddleware.php`
- Register `internal_token` alias in `bootstrap/app.php`
- Route `GET …/api/internal/health/resume` in routes/service.php
- `ResumeMetrics` service (Redis-direct counters, fail-fast)
- `ResumeLogContext` helper
- Monolog processor in `AppServiceProvider::boot()` for pod_id/hostname

**Files created:** ~6 new files, ~2 modified
**Lines added:** ~350
**Risk level:** Very low — pure observability infra

**Test on staging:**
```bash
# 1. PHP lint
php -l config/resumes.php app/Http/Controllers/Api/ResumeHealthController.php

# 2. Unit tests
php artisan test --filter=ResumeV2HealthTest

# 3. ai-guards
bash scripts/ai-guards.sh

# 4. Staging curl
curl -H 'Authorization: Bearer <token>' \
  https://testing.jero.works/service_company/api/internal/health/resume
# Expect: 200, status=ok, all checks ok

# 5. Tail logs
tail -n 20 storage/logs/laravel.log | grep "jobs.resume"

# 6. Confirm existing pages still work
curl -sI https://testing.jero.works/login | head -1
# Expect: 200 OK
```

**Dependencies:** Redis running. Bootstrap/cache writable.
**Rollback:** Remove the route + health endpoint (no other code depends on it).

---

## Step 2 — Additive migration: v2 columns + indexes

**Purpose:** Add DB columns BEFORE any code reads them. Zero behavior
change — safe to run on any environment.

**Feature flag:** N/A (DB-only, no app code reads these yet)

**What we build:**
- Migration 1: `add_resume_v2_indexes` — UNIQUE on
  `(company_id, idempotency_key)`, UNIQUE on `(uuid, company_id)`,
  INDEX on `(status, expires_at)`
- Migration 2: `add_resume_v2_session_columns` — `idempotency_key` char(64),
  `file_hash` char(64), `parser_started_at`/`parser_completed_at` timestamps,
  `parser_attempts` unsignedTinyInt, `expires_at` timestamp, `last_error` text
- Migration 3: `create_resume_parse_results` table
- Migration 4: `create_upload_session_audits` table
- `app/Enums/ResumeUploadStatus.php`
- Read [`database/schema/mysql-schema.sql`](../../database/schema/mysql-schema.sql)
  BEFORE writing migrations to confirm column names

**Files created:** ~6 files (4 migrations + 1 enum)
**Lines added:** ~200
**Risk level:** Very low — additive only

**Test on staging:**
```bash
# 1. Migration
php artisan migrate --pretend | grep resume_v2
# Expect: ALTER TABLE resume_uploads ADD ...

php artisan migrate

# 2. Confirm columns
php artisan tinker --execute="DB::table('resume_uploads')->first()" | grep idempotency_key
# Expect: column exists

# 3. ai-guards
bash scripts/ai-guards.sh

# 4. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expect: 200
```

**Dependencies:** Step 1 (health endpoint — to confirm after migration).
**Rollback:** `php artisan migrate:rollback --step=4`.
**Pre-condition:** Read [`database/schema/mysql-schema.sql`](../../database/schema/mysql-schema.sql)
first (CLAUDE.md rule 2 — schema truth is NOT in migrations).

---

## Step 3 — `ResumeUploadSession` service (core logic)

**Purpose:** Build the core service class with full unit test coverage
before any endpoint exposes it.

**Feature flag:** `resumes.v2_core_service` (OFF — no endpoints use it yet)

**What we build:**
- `app/Services/ResumeUploadSession.php` — `createOrPresign()`,
  `get()`, `finalize()`
- `app/Models/ResumeUpload.php` — scopes, helper methods, status
  constants
- `app/Models/UploadSessionAudit.php` — model
- Unit tests

**Files created:** ~4 files (2 models, 1 service, test file)
**Lines added:** ~350
**Risk level:** Medium — core logic, but no production path uses it yet

**Test on staging:**
```bash
# 1. Unit tests (in-memory SQLite)
php artisan test --filter=ResumeV2SessionServiceTest

# 2. ai-guards
bash scripts/ai-guards.sh

# 3. Login smoke
curl -sI https://testing.jero.works/login
# Expect: 200

# 4. php -l
php -l app/Services/ResumeUploadSession.php
```

**Dependencies:** Step 2 (columns exist).
**Rollback:** Remove service files (nothing references them yet).

---

## Step 4 — `POST /resume-sessions` endpoint (presign)

**Purpose:** API creates a session and returns presigned R2 PUT URL.
No file bytes flow through PHP — pure metadata.

**Feature flag:** `resumes.presign_endpoint` (OFF on production, ON in staging)

**What we build:**
- `app/Http/Controllers/Api/ResumeUploadSessionController.php` —
  `store()` method
- Route `POST /service_company/api/resume-sessions`
- Rate limit middleware: `throttle:30,1` per user,
  `throttle:120,1` per company
- `config/resumes.php` R2 presign config (bucket, region, TTL)
- Validation: `idempotency_key` is hex64, `file_size_bytes ≤ 6_291_456`,
  `mime_type` in allowed set

**Files created:** ~1 controller, ~2 config additions
**Lines added:** ~200
**Risk level:** Low-Medium — new endpoint, no existing code path changes

**Test on staging:**
```bash
# 1. Unit tests
php artisan test --filter=ResumeV2PresignTest

# 2. Staging curl
curl -X POST https://testing.jero.works/service_company/api/resume-sessions \
  -H 'Content-Type: application/json' \
  -d '{"idempotency_key":"aaaa...64hex...","original_filename":"test.pdf","file_size_bytes":1000,"mime_type":"application/pdf"}' \
  -b <session-cookie>
# Expect: 201 + session.uuid + upload.url

# 3. Same idem key → same UUID (idempotency test)
# Re-run same POST — expect same uuid

# 4. Rate limit test
for i in $(seq 1 31); do curl ...; done
# Expect: 31st → 429

# 5. Log tail
tail storage/logs/laravel.log | grep "jobs.resume.session_created"
# Expect: appears

# 6. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expect: 200
```

**Dependencies:** Step 3 (service class).
**Rollback:** Remove route + controller.

---

## Step 5 — Browser JS: presign module + direct R2 PUT

**Purpose:** Browser talks to R2 directly. PHP sees ZERO bytes for
the upload. Progress bar shows real-time upload %.

**This is the core v2 change.** The existing `uploadWithProgress()` that
multipart-POSTs to PHP is replaced by: presign request → direct PUT to R2.
The client-side pdf.js/mammoth parser continues running unchanged — it
parses from the `File` object in browser memory, NOT from the server.

**Feature flag:** `resumes.v2_browser_upload` (OFF — existing multipart
path still serves as fallback)

**What we build:**
- `public/js/resume-presign.js` — POST `/resume-sessions` → receive
  presigned URL → PUT file to R2 directly via XMLHttpRequest
- Upload progress: progress bar, ETA, speed, "Uploading..." badge
- File validation: extension (pdf/doc/docx), size (≤6 MB), MIME
- SHA256 via `SubtleCrypto.digest('SHA-256', …)` before presign request
- localStorage: save `resume_session_uuid` for refresh recovery

**Files created:** ~1 JS module (~300 lines)
**Lines added:** ~300
**Risk level:** Medium — client-side only, legacy path preserved

**Test on staging:**
```bash
# 1. Browser DevTools test
# Open service_company/job-applications/create
# Open Network tab → select a PDF
# Confirm:
#   - POST /resume-sessions → 201 (session created)
#   - PUT <r2-url> → 200 (direct upload, no PHP)
#   - Progress bar fills from 0% to 100%
#   - "Uploaded ✓" badge appears
#   - Client-side parser (pdf.js) still runs and fills fields

# 2. Console check
# Confirm no "Upload through PHP" — should be zero PHP bytes

# 3. Legacy multipart still works
# Turn flag off → select file → confirm old path works

# 4. JavaScript lint
npx eslint public/js/resume-presign.js
```

**Dependencies:** Step 4 (presign endpoint).
**Rollback:** Flip `resumes.v2_browser_upload` to OFF — legacy path takes over.

---

## Step 6 — `POST /resume-sessions/{uuid}/finalize` endpoint

**Purpose:** After browser confirms R2 PUT completes, finalize updates
session status → `Uploaded`. Candidate creation and session linking
happen later during actual Submit (Step 7).

**Feature flag:** `resumes.finalize_endpoint` (OFF)

**What we build:**
- `ResumeUploadSessionController::finalize()` method
- Route `POST /service_company/api/resume-sessions/{uuid}/finalize`
- Conditional UPDATE: `status IN ('Created','Uploading') → 'Uploaded'`
- Server-side HEAD check on R2 object (sanity — the PUT already
  happened, we verify the object exists)
- Audit row: `ActionUploadCompleted`

**Files created:** ~1 controller method (~60 lines)
**Lines added:** ~60
**Risk level:** Low — single state transition, rate-limited

**Test on staging:**
```bash
# 1. Unit tests
php artisan test --filter=ResumeV2FinalizeTest

# 2. Full flow on staging
curl -X POST .../resume-sessions (get uuid + presigned URL)
# Manually PUT a small file to the presigned URL
curl -X POST .../resume-sessions/<uuid>/finalize
# Expect: 200 + session.status = "Uploaded"

# 3. Double-finalize (idempotency)
curl -X POST .../resume-sessions/<uuid>/finalize
# Expect: same 200 (no-op — conditional UPDATE finds status != Created)

# 4. finalize on expired session
# Expect: 422

# 5. Log tail
tail storage/logs/laravel.log | grep "jobs.resume.upload_finalized"
```

**Dependencies:** Step 4 (presign endpoint), Step 5 (browser upload
flow works before finalize can be tested).
**Rollback:** Remove route.

---

## Step 7 — Submit with session_uuid (merge finalize INTO submit)

**Purpose:** When the recruiter clicks Submit, the candidate is created
and the upload session is linked — atomically, in one transaction.
This merges what was formerly the finalize+submit sequence into a single
server hit.

**Feature flag:** `resumes.v2_submit_with_session` (OFF — legacy submit
path preserved)

**What we build:**
- Update `ServiceCompanyJobApplicationController::store()`:
  - Accept optional `session_uuid` in request body
  - If present: verify session status is `Uploaded`, HEAD-check R2
  - Single transaction: INSERT candidate + INSERT documents row +
    UPDATE session `status='Parsed'`, `linked_candidate_id`
  - If absent: fall back to legacy multipart flow
  - If session not `Uploaded` → 422 `upload_incomplete`
- Update submit JS: POST `/job-applications` includes `session_uuid`

**Files created:** ~1 controller update, 1 JS update
**Lines added:** ~80
**Risk level:** Medium — touches candidate creation. Rollback is
one flag flip.

**Test on staging:**
```bash
# 1. Unit tests
php artisan test --filter=ResumeV2SubmitWithSessionTest

# 2. Full browser flow:
# Step A: POST /resume-sessions → get presigned URL
# Step B: PUT file to R2 directly (Step 5 code)
# Step C: Click Submit → POST /job-applications with session_uuid
# Expect: candidate created, session linked

# 3. Idempotent re-submit
# Same session_uuid → POST again → expect 200 + existing candidate_id

# 4. Submit without upload
# session_uuid = fresh one (no PUT yet) → expect 422 upload_incomplete

# 5. Log tail
tail storage/logs/laravel.log | grep "jobs.resume.submit_completed"

# 6. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expect: 200
```

**Dependencies:** Steps 4-6 (presign + upload + session management).
**Rollback:** Flip `resumes.v2_submit_with_session` OFF — legacy
multipart submit path reinstates.

---

## Step 8 — Cancel endpoint + UI button

**Purpose:** Recruiter can cancel an in-progress upload. Clean state.

**Feature flag:** `resumes.v2_cancel` (OFF)

**What we build:**
- `ResumeUploadSessionController::cancel()` method
- Route `DELETE /service_company/api/resume-sessions/{uuid}`
- Conditional UPDATE: `status NOT IN ('Cancelled','Reconciled','Expired') → 'Cancelled'`
- Best-effort DELETE of R2 temp object
- Cancel button in Blade (re-enable "Choose File" after cancel)
- Metrics: `resume_cancelled_total`

**Files created:** ~1 endpoint + Blade/cancel UI
**Lines added:** ~60
**Risk level:** Low

**Test on staging:**
```bash
# 1. Unit tests
php artisan test --filter=ResumeV2CancelTest

# 2. Browser: start upload → click cancel
# Expect: progress bar resets, "Choose File" re-enabled,
#          DELETE endpoint hit

# 3. Re-upload after cancel
# Expect: fresh session UUID

# 4. Log tail
tail storage/logs/laravel.log | grep "jobs.resume.cancelled"
```

**Dependencies:** Steps 4-6 (session endpoints exist).
**Rollback:** Remove route + hide button.

---

## Step 9 — Refresh recovery (GET session)

**Purpose:** If browser refreshes mid-upload, re-read session state
from server and rehydrate UI. Zero data loss.

**Feature flag:** `resumes.v2_refresh_recovery` (OFF)

**What we build:**
- `ResumeUploadSessionController::show()` method
- Route `GET /service_company/api/resume-sessions/{uuid}`
- Server reads `resume_uploads` row + `linked_candidate_id`
- Response includes: `status`, `expires_at`, `linked_candidate_id`
- JS: on page load → check localStorage for session UUID →
  GET → rehydrate UI:
  - If 200 + `Uploaded` → show green badge, enable submit
  - If 200 + `Created`/`Uploading` → resume upload (Step 10)
  - If 200 + `Failed` → show retry modal
  - If 404 (expired/gone) → start fresh

**Files created:** ~1 GET endpoint + JS recovery logic
**Lines added:** ~100
**Risk level:** Low — read-only endpoint

**Test on staging:**
```bash
# 1. Unit tests
php artisan test --filter=ResumeV2RefreshRecoveryTest

# 2. Browser test
# Start upload → refresh page
# Expect: progress reappears without re-upload

# 3. After upload complete, refresh
# Expect: "Uploaded ✓" badge, submit enabled

# 4. 404 test
# Start upload, wait 30+ min for expiry, refresh
# Expect: clean "Choose File" state

# 5. Log tail
tail storage/logs/laravel.log | grep "jobs.resume.session_status_ok"
```

**Dependencies:** Step 8 (cancel state), Step 7 (submit state).
**Rollback:** Remove endpoint; localStorage refs survive harmlessly.

---

## Step 10 — Multipart upload from R2 (for files ≥5 MB)

**Purpose:** Large-file uploads are resumable from the last successful
byte. No need to re-send entire file on network blip.

**Feature flag:** `resumes.v2_multipart_upload` (OFF — single-PUT for
all file sizes by default)

**What we build:**
- JS: detect `file.size >= 5_242_880` (5 MB) → use R2 multipart upload
  API
- `createMultipartUpload()` → get UploadId
- `uploadPart()` sequentially (each part up to 5 MB)
- `completeMultipartUpload()` or `abortMultipartUpload()` on cancel
- On network failure mid-multipart: list parts → find offset →
  resume from next byte
- Progress calculated as sum-of-uploaded-bytes / total-bytes
- Fallback: if R2 doesn't support multipart, use single-PUT with
  exponential backoff

**Files created:** ~1 JS module (~200 lines)
**Lines added:** ~200
**Risk level:** Low-Medium — multipart is standard R2 API.

**Test on staging:**
```bash
# 1. Create a 6 MB test file
dd if=/dev/urandom of=/tmp/test-6mb.pdf bs=1M count=6

# 2. Browser test
# Upload 6 MB file → confirm multipart created
# Check Network tab for multiple PUT parts

# 3. Interrupt mid-upload
# Kill network at 50% → wait → re-connect
# Expect: upload resumes from roughly 50%, not from 0

# 4. Cancel mid-multipart
# Expect: abortMultipartUpload called
```

**Dependencies:** Step 4 (presign — presigned URLs for multipart
differ from single-PUT).
**Rollback:** Flip flag OFF — all files use single-PUT.

---

## Step 11 — Network resilience: offline/online + preflight

**Purpose:** Browser detects actual network state (not
`navigator.onLine`) and handles transitions gracefully.

**Feature flag:** `resumes.v2_network_resilience` (OFF — offline
handling is best-effort)

**What we build:**
- `resume-retry-manager.js` — online/offline listener + preflight ping
  (`GET /api/resume-preflight`) + localStorage state preservation
- On offline:
  - Abort upload XHR (store offset for resume)
  - Pause progress UI
  - Show "Network Lost" badge
  - Save session UUID + idem key to localStorage
- On online:
  - Ping preflight endpoint 3 times with 1 s spacing
  - If any returns 200: declare "online"
  - Re-run GET `/resume-sessions/{uuid}` to get server truth
  - Resume upload from offset (multipart) or restart (single-PUT)
- Metrics: `resume_recovery_from_offline_total`

**Files created:** ~1 JS module (~200 lines)
**Lines added:** ~200
**Risk level:** Medium — new state machine in JS.

**Test on staging:**
```bash
# 1. Offline mid-upload
# Start upload → DevTools: Network → Offline
# Wait 5 s → switch back to "Online"
# Expect: upload resumes, progress continues

# 2. Offline at submit
# Complete upload → click submit → network lost before response
# Restore network → submit auto-retries — idempotency key ensures
# only one candidate is created

# 3. localStorage preservation test
# Start upload → kill tab → open new tab → navigate to same page
# Expect: recovery prompt (from Step 9)

# 4. preflight endpoint fails
# Kill Octane → expect graceful "Server unavailable" badge
# Restore Octane → expect retry without page reload
```

**Dependencies:** Step 9 (refresh recovery), Step 10 (multipart resume).
**Rollback:** Flip flag OFF.

---

## Step 12 — Cron jobs: reconciler + repair

**Purpose:** Background maintenance. Clean up expired sessions,
repair stuck states. Zero user-facing change.

**Feature flag:** None (cron-only, always safe to run)

**What we build:**
- `app/Console/Kernel.php` schedule entries:
  - `uploads:cleanup-orphans` — daily 03:30 IST
  - `uploads:reconcile` — weekly Sunday 04:30
  - `uploads:repair` — every 15 min
  - All with `->onOneServer()->withoutOverlapping(<TTL>)`
- `app/Console/Commands/ReconcileOrphanUploads.php` — mark expired
  sessions, DELETE from R2 best-effort, increment metrics
- `app/Console/Commands/RepairStuckSessions.php` — mark stuck-as-Uploading
  sessions as `Failed`
- Tests: `test_reconcile_is_idempotent`

**Files created:** ~2 commands, ~1 kernel update, ~2 test files
**Lines added:** ~200
**Risk level:** Low — cron commands are isolated

**Test on staging:**
```bash
# 1. Run commands manually
php artisan uploads:reconcile --dispatch
# Expect: log line "jobs.resume.reconcile_completed"

php artisan uploads:repair --dispatch
# Expect: log line "jobs.resume.repair_completed"

# 2. Verify scheduler registered
php artisan schedule:list | grep uploads
# Expect: 3 entries

# 3. Test idempotency
php artisan uploads:reconcile --dispatch
php artisan uploads:reconcile --dispatch
# Second run: "reconcile_completed (idempotent)" log

# 4. Log tail
tail storage/logs/laravel.log | grep "jobs.resume.reconcile"
```

**Dependencies:** Steps 2-3 (DB columns + models exist so cron
can query them).
**Rollback:** Remove schedule entries from Kernel.

---

## Step 13 — Rate limiting hardening (Redis + per-company)

**Purpose:** Protect endpoints from a single aggressive tenant.

**Feature flag:** N/A (hardening, always ON)

**What we build:**
- Redis-backed per-user throttle on presign: `30/min`
- Redis-backed per-company throttle on presign: `120/min`
- Redis-backed per-user throttle on submit: `20/min`
- Custom `CompanyThrottleMiddleware`: reads company_id from
  `Auth::user()->company_id`, rate-limit key = `{c:{cid}}:{route}`
- Fallback: if Redis is down, rate-limiter fails open with log
  (ADR-014)
- Health endpoint reports: `global_throttle_hit_total`
- Metrics: `resume_throttled_total{reason=per_user|per_company}`

**Files created:** ~1 middleware, ~1 config addition
**Lines added:** ~100
**Risk level:** Low — additive, no behavior change until rate limit
exceeded

**Test on staging:**
```bash
# 1. Per-user rate limit
for i in $(seq 1 31); do curl -X POST .../resume-sessions ... ; done
# 30th = 200, 31st = 429

# 2. Per-company rate limit
# User A hits 30 → 429. User B from same company hits → 429

# 3. Double-submit protection
# Same session submitted twice → second is idempotent (Step 7 logic)

# 4. Redis fallback test
# Stop Redis → all rate limits pass through (logged)
# curl ... → 200 (no 429)
# tail log -> "resume.throttle_fails_open"

# 5. Metrics verify
curl -H 'Authorization: Bearer <token>' .../health/resume
# Expect: global_throttle_hit_total > 0
```

**Dependencies:** Step 1 (health endpoint), Step 4 (presign endpoint).
**Rollback:** Remove middleware from route groups.

---

## Step 14 — BroadcastChannel for same-origin tab sync (v2.5)

**Purpose:** Same-browser tab sync costs ZERO server bytes. No
WebSocket needed for same-origin tabs.

**Feature flag:** `resumes.v2_broadcast_channel` (OFF — tabs
are independent as today)

**What we build:**
- `public/js/resume-broadcast-sync.js` (~80 lines):
  - `BroadcastChannel` named `'resume-sync-' + companyId`
  - On message: parse JSON, if uuid matches local session → sync status
  - Emit on every state change (upload start, complete, cancelled)
  - Fallback for Safari < 15.4: `localStorage` `storage` events

**Files created:** ~1 JS module (~80 lines)
**Lines added:** ~80
**Risk level:** Low — zero server impact, falls back gracefully

**Test on staging:**
```bash
# 1. Same-browser two-tab test
# Tab A: upload file → Tab B sees "Uploading..." in < 5 ms
# Tab A: upload complete → Tab B sees "Uploaded ✓"

# 2. Cross-browser test (Chrome + Firefox, same machine)
# Tab A uploads → Tab B should NOT see event
# Expect: nothing (no BroadcastChannel across browsers — correct)

# 3. Console check
# No BroadcastChannel errors
# No server logs related to tab sync

# 4. Flag OFF → tabs behave independently (current behavior)
```

**Dependencies:** Step 5 (the upload events that get broadcast).
**Rollback:** Flip flag OFF.

---

## Step 15 — Public career-apply extraction

**Purpose:** Extract `HomeController::careerStore` + `careerApply`
into a thin `CareerApplyController`. Same resume-upload v2 code
reused for public career pages.

**Feature flag:** `resumes.v2_career_controller` (OFF — HomeController
still serves)

**What we build:**
- `app/Http/Controllers/Api/CareerApplyController.php` —
  `apply()` wraps `careerApply`, `store()` wraps `careerStore`
- Legacy `HomeController` methods become thin delegates to new
  controller (if flag ON) or stay as-is (if flag OFF)
- Same data model: uses `ResumeUpload` and presign endpoint under
  the hood
- Public career preserves HTTP polling for progress (no Reverb for
  public — ADR-011)

**Files created:** ~1 controller, ~1 test file
**Lines added:** ~100
**Risk level:** Low-Medium — extracts from 16 KLOC god controller.

**Test on staging:**
```bash
# 1. Full career flow
# Open career-apply URL → fill form → upload PDF → submit
# Expect: candidate created, resume linked

# 2. Same as before extraction (flag OFF)
# Flip flag OFF → do same flow → same result
# Expect: identical output

# 3. Verify HomeController still works
curl -sI https://testing.jero.works | head -1
# Expect: 200

# 4. Log tail
tail storage/logs/laravel.log | grep "jobs.resume.career_submit"
```

**Dependencies:** Steps 1-14 (entire v2 upload pipeline exists).
**Rollback:** Flip flag OFF — HomeController serves.

---

## Step 16 — Performance tuning + load test

**Purpose:** Prove throughput holds. Tune bottlenecks.

**Feature flag:** All v2 flags ON in staging (not in production yet)

**What we build:**
- `tests/Load/10k_concurrent.php` — automated load test:
  - 10K concurrent virtual recruiters
  - Each does: presign → PUT 2 KB file → submit
  - Duration: 5 minutes
  - Measure: p50/p95/p99 latency, 5xx rate, throughput
- Health endpoint monitored during load test

**Files created:** ~1 load test script
**Lines added:** ~200
**Risk level:** Low — staging only, read-only

**Test on staging:**
```bash
# 1. Run load test
php tests/Load/10k_concurrent.php --duration=300 --rate=33

# 2. Verify metrics during test
curl -H 'Authorization: Bearer <token>' .../health/resume
# Expect: status=ok, all latency green

# 3. Verify after test
tail -n 50 storage/logs/laravel.log | grep "jobs.resume"
# Expect: all events present, no ERROR

# 4. Compare old vs new
php tests/Load/10k_concurrent.php --flow=v2-direct-r2
php tests/Load/10k_concurrent.php --flow=v1-multipart-php
# Expect: v2 has ~80% fewer PHP requests (no file bytes through PHP)
```

**Dependencies:** Steps 1-15.
**Rollback:** Only staging; nothing in production changes.

---

## Step 17 — Production GA: flip flags, deprecate legacy path

**Purpose:** v2 is the primary upload path. v1 multipart is
deprecated, rate-limited, slated for removal.

**Feature flag:** All v2 flags ON in production.
`resumes.legacy_uploadresume_compat` set to `deprecated`.

**What we build:**
- Flip all flags in production config
- v1 `POST /uploadresume`: rate-limited to 10/min, logs deprecation
  warning, returns `X-Deprecated: true` header
- v1 `POST /candidates` without `session_uuid`: logs deprecation
  warning
- Documentation: update `docs/CHANGELOG.md`
- Runbook: post-launch monitoring checklist for Day +1 through Day +30
- Rollback plan for each flag documented in runbook

**Files created:** ~1 changelog entry, ~1 runbook update
**Lines added:** ~100
**Risk level:** Medium — highest visibility change. Each flag has
a documented rollback step.

**Test on staging:**
```bash
# 1. All flags ON
curl -X POST .../resume-sessions ... | grep "upload.url" | head -1
# Expect: presigned URL returned

# 2. Legacy path deprecated
curl -X POST .../uploadresume -F "file=@/tmp/test.pdf"
# Expect: 200, but X-Deprecated: true header

curl -X POST .../candidates -d '{"session_uuid": "...", ...}'
# Expect: 201, candidate created via v2 flow

# 3. Full regression
php artisan test --testsuite=Feature --filter=ResumeV2

# 4. Login still works
curl -sI https://testing.jero.works/login | head -1
# Expect: 200

# 5. Production smoke (first 5 min after deploy)
# Monitor: 200 rate, 5xx rate — both must be green
```

**Dependencies:** All Steps 1-16.

---

## Summary view

| Step | Name | Files | Lines | Risk | Key check |
|---|---|---|---|---|---|
| 1 | Health endpoint + infra | ~8 new | ~350 | Very low | curl → 200 |
| 2 | Additive migrations | 4 migrations | ~200 | Very low | `php artisan migrate` |
| 3 | ResumeUploadSession service | ~4 files | ~350 | Medium | Unit tests pass |
| 4 | POST presign endpoint | ~1 controller | ~200 | Low-Med | 201 + presigned URL |
| 5 | **Browser JS: R2 direct PUT** | 1 JS module | ~300 | Medium | Zero PHP bytes in Network tab |
| 6 | Finalize endpoint | 1 method | ~60 | Low | Double-finalize idempotent |
| 7 | **Submit with session_uuid** | 1 controller | ~80 | Medium | 2 server hits vs 4 |
| 8 | Cancel endpoint + UI | 1 endpoint | ~60 | Low | Cancel → re-upload clean |
| 9 | Refresh recovery GET | 1 endpoint | ~100 | Low | Refresh preserves state |
| 10 | Multipart (≥5 MB) | 1 JS module | ~200 | Low-Med | Resume from 50% |
| 11 | Network resilience | 1 JS module | ~200 | Medium | Offline → Online resume |
| 12 | Cron jobs | 2 commands | ~200 | Low | Schedule list shows 3 |
| 13 | Rate limiting | 1 middleware | ~100 | Low | 31st request = 429 |
| 14 | BroadcastChannel | 1 JS module | ~80 | Low | Two tabs sync instantly |
| 15 | Career-apply extract | 1 controller | ~100 | Low-Med | Flag OFF = same behavior |
| 16 | Load test | 1 script | ~200 | Low | v2 = 80% fewer PHP bytes |
| 17 | **Production GA** | config + docs | ~100 | Medium | All flags ON, monitor 5 min |

## Key cross-references

- Failure modes: [`45_failure_modes.md`](45_failure_modes.md)
- Data model: [`40_data_model.md`](40_data_model.md)
- API contracts: [`50_api_contracts.md`](50_api_contracts.md)
- Storage: [`60_storage_and_queue.md`](60_storage_and_queue.md)
- Invariants: [`96_invariants.md`](96_invariants.md)
- ADRs: [`99_decision_log.md`](99_decision_log.md)
- Never matrix: [`98_failure_matrix_never.md`](98_failure_matrix_never.md)
