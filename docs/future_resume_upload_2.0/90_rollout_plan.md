# 90 — Rollout plan

> *Intent: identify the **14 small steps** to ship v2 live, each one*
> *independently shippable, testable under real load.*

> **Each step starts with a feature flag OFF, ships the code, and flips**
> **the flag ON with a 5-min smoke test. If the smoke test fails, the flag**
> **stays OFF and the step is rolled back without impacting production.**

| Flag | Default |
|---|---|
| `resumes.presign_endpoint` | OFF in v2.0.0a, ON in v2.0.0c |
| `resumes.v2_session_table` | OFF until v2.0.0b; then ON |
| `resumes.v2_queue` | OFF until v2.0.0c; then ON |
| `resumes.v2_reverb_events` | OFF until v2.0.0d; then ON |
| `resumes.v2_health_endpoint` | ON in v2.0.0a (always-on observability) |
| `resumes.submit_preflight` | ON in v2.0.0c |
| `resumes.legacy_uploadresume_compat` | ON (compat shim) in v2.0.0a; OFF in v2.1.0 |

## Step-by-step

### v2.0.0a — Additive schema + health endpoint (background infra)

**Changes:**
- Migration 1: `add_resume_v2_indexes` — UNIQUE(uuid, company_id), INDEX(company_id, idempotency_key) — **NO UNIQUE on idempotency_key** (ADR-002, ADR-009)
- Migration 2: `add_resume_v2_session_columns`
- Migration 3: `create_resume_parse_results`
- Migration 4: `create_upload_session_audits`
- Add `ResumeUploadStatus` enum (does NOT replace existing status string)
- Add v2 `ResumeMetrics` (Redis-direct counter, fail-fast)
- Add health endpoint `/api/internal/health/resume` with bearer-token auth

**Validation:**
1. `php artisan tinker --execute "DB::table('resume_uploads')->get()"` — schema unchanged.
2. `php artisan test --filter=ResumeV2HealthTest`
3. `bash scripts/ai-guards.sh` clean
4. `bash tests/Load/10k_concurrent.php 60` (1-min saturate): zero server-side 5xx
5. `curl -H 'Authorization: Bearer X' https://testing.jero.works/service_company/api/internal/health/resume` returns 200 with status=ok

**Risk:** minimal — purely additive.

**Sign-off required because** layout migration column additions.

### v2.0.0b — Idempotency key + presign endpoint

**Changes:**
- Add `idempotency_key`, `file_hash` columns populated.
- New `POST /service_company/api/resume-sessions` endpoint.
- `ResumeUploadSession` service: `createOrPresign(idemKey, ...)`.
- New `ResumePreflightController` scaffolding (returns 200 with server time, no logic).
- Rate limit: 30/min/user, 120/min/company on POST.
- JS: `resume-presign.js` introduced.

**Flag:** `resumes.presign_endpoint` initially OFF; one recruiter in
staging.

**Validation:**
1. Filter: `ResumeV2PresignTest` — same key returns same UUID.
2. Filter: `ResumeV2RateLimitTest` — 31st presign/min returns 429.
3. Browser smoke: select resume → "Choose File" → see presign issued in DevTools Network; no PHP byte path.
4. Log tail 15 min: `jobs.resume.session_created` lines appear with sane uuid, company_id, idem key.

**Sign-off required** — affects existing flow selectively.

### v2.0.0c — Direct PUT to R2 + finalize + cache state

**Changes:**
- Existing `career-apply.blade.php` MULTIPART path remains
  (fallback). Recruiter-side `create.blade.php` swaps to presigned
  PUT.
- New `POST /service_company/api/resume-sessions/{uuid}/finalize`.
- R2 bucket lifecycle rule (deployment-time).
- Multipart upload from browser ≥5 MB.
- localStorage 4 KB writes.
- JS: `resume-upload-progress.js` invoked.

**Flag:** `resumes.presign_endpoint` ON for service_company users;
OFF for public careers.

**Validation:**
1. Filter: `ResumeV2FinalizeTest`.
2. Browser smoke: ttl = 5 min for testing; complete upload → finalize → check Reverb `ResumeUploaded` event in DevTools.
3. Force R2 5xx (using `chaos-tool`: kill local minio), confirm UI retry modal.
4. Tail logs for canonical events.

**Sign-off** — codepath replacement.

### v2.0.0d — Parser queue + Reverb

**Changes:**
- Replace `ChatGptService` raw cURL consumer with `ResumeParserService` wrapper.
- New `parse_resumes` queue in Horizon.
- `ParseResumeJob` introduced.
- Reverb channel auth (private-resume.{company_id}).
- `ResumeParseProgress` and `ResumeParsed` events broadcast.

**Flag:** `resumes.v2_reverb_events` for 1 tenant in staging.

**Validation:**
1. Filter: `ResumeV2ParserQueueTest`.
2. Browser smoke: tab A submits, tab B sees parser progress.
3. Cross-tenant smoke: tab A in tenant X, tab B in tenant Y → no events visible.
4. Force OpenAI 429 (mock), confirm `Retry-After` honored.

**Sign-off** — parser behavior change.

### v2.0.0e — Submit preflight + dedup + idempotent submit recovery

**Changes:**
- New `GET /service_company/api/resume-preflight` returns server time.
- `StoreJobApplication` reads session; verifies state; de-dupes on
  `(email, phone, company_id)` composite.
- New `resume_submit_recovered_total` metric.

**Validation:**
1. Filter: `ResumeV2SubmitRecoveryTest`.
2. Browser smoke: kill network at submit POST → user retries → submit succeeds, candidate is one record (not two).

### v2.0.0f — Extracted public career-apply page

**Changes:**
- `careerStore` controller extracted from `HomeController` god.
- New vertical slice `CareerApplyController` thin delegating wrapper.
- The legacy multipart path persists.

**Sign-off** — controller boundary change.

### v2.0.0g — Recruiter add-candidate extracted

**Changes:**
- `ServiceCompanyJobApplicationController::store` extracted to `JobApplicationCandidateStoreAction`
- `store` controller method delegates; not yet fully vertical sliced.

### v2.0.0h — Cross-tab state via Reverb

**Changes:**
- Reverb events for cross-tab progress.
- Browser `StorageEvent` cross-tab sync as fallback.

**Validation:**
- Filter: cross-tab Reverb subscriber tests

### v2.0.0i — Cancel + Refresh recovery

**Changes:**
- New `DELETE /service_company/api/resume-sessions/{uuid}` endpoint.
- Browser cancel button wired.
- Refresh recovery: GET finalized sessions, restore state.

**Validation:**
- Filter: `ResumeV2CancelTest`, `ResumeV2RefreshRecoveryTest`

### v2.0.0j — Retry manager: parser + upload dual retry

**Changes:**
- `resume-retry-manager.js` introduced.
- Honors Retry-After on parser 429.
- Resumes PUT from offset on multipart failure.

**Validation:**
- Filter: `ResumeV2RetryTest`
- Chaos harness CAOS-7 (parse 429 honor)

### v2.0.0k — Bulk upload path (separate, but composable)

**Changes:**
- New `Bulkupload` controller uses v2 primitives.
- Reuses presign + finalize + queue.

**Sign-off** — public bulk path concerns

### v2.0.0l — Performance tuning + DTrace

**Changes:**
- Horizon metrics for parser queue; alert thresholds tuned.
- Redis cluster tested under synthetic 20K burst.

### v2.0.0m — Public apply (career-apply) extracted from HomeController

**Changes:**
- Vertically sliced into `CareerApplyController`.
- Reverb not used (kept on HTTP polling at 1 Hz for public).
- Same data model.

### v2.0.0n — GA + deprecate v1 paths

**Changes:**
- v1 `POST /uploadresume` rate-limited to ghost traffic.
- `careerApply` v1 path retained via feature flag for 6 months.

## Post-launch monitoring

| Signal | Check |
|---|---|
| 5xx rate uploads | < 0.01% |
| 5xx rate finalize | < 0.001% |
| Parser success rate | ≥ 95% |
| Parser 429 rate | < 2% (target) |
| Submit duplicate merge rate | n/a (no merge — by design, Step 20) |
| Submit candidate-creation-per-submit ratio | **1.0** (one submit = one candidate, always) |
| Duplicate-badge display rate | tracked (recruiter UX signal, not a correctness KPI) |
| Cross-tenant event leak | zero |
| Health endpoint latency | < 250 ms p95 |

## Rollback procedure

Each step v2.0.0a..m has a documented rollback to the prior step's
flag state. Specifically:

| Step | Rollback action |
|---|---|
| v2.0.0a | drop new tables (none exist yet) |
| v2.0.0b | `resumes.presign_endpoint=OFF` |
| v2.0.0c | `resumes.presign_endpoint=OFF`, fall back to multipart |
| v2.0.0d | `resumes.v2_reverb_events=OFF` |
| ... | ... |

We preserve **the same feature flags for 6 months**, until v2.1.0.

## Step-independent verifications

These run after every step:
- `bash scripts/ai-guards.sh`
- `php artisan test --testsuite=Feature && php artisan test --testsuite=ResumeV2`
- `bash tests/Load/2k_idle.php 90` — 90-second 2K concurrent baseline
- `curl -H 'Authorization: Bearer X' https://…/api/internal/health/resume`
- `tail storage/logs/laravel.log | grep jobs.resume`

## Acceptance for GA (v2.0.0n)

1. Each step's tests are green.
2. Health endpoint is `ok` for 24 hours continuously in staging.
3. Live read-write test in production with FAKE `company_id=999999`
   runs for 1 hour at 100 RPS load.
