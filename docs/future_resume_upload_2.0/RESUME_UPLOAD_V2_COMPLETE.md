# Resume Upload v2 — Single Source of Truth

> **Intent:** one document that captures everything an engineer or reviewer needs
> to understand, build, review, and operate Resume Upload v2. The full 23-doc
> tree under [`docs/future_resume_upload_2.0/`](.) remains authoritative for
> detail; this is the **consolidated executive view** that links the canonical
> contracts, invariants, ADRs, data model, API, rollout, and the no-merge
> contract into one narrative.

> **Read this first.** All other docs in this tree are deep dives on a section
> below.

---

## 0. The one-paragraph pitch

JERO's Resume Upload v2 lifts the upload subsystem from "1,000 users
collapse" to "10,000 users without failure" by **moving bytes off PHP** (the
browser PUTs directly to Cloudflare R2 via a presigned URL) and **adding a
transport-retry `Idempotency-Key` HTTP header** that protects against
network blips without ever becoming a content-addressed deduplication. The
upload subsystem creates a brand-new session per file pick (INV-1), never
deletes candidate rows, and never merges duplicates — the **Duplicate
Ownership Engine** observes and reports (badges), it never mutates.

---

## 1. What v2 is (and is not)

### Is
- A 2-RTT browser→R2→submit flow.
- A `status` state machine on `resume_uploads` with conditional UPDATEs as the
  correctness mechanism.
- An HTTP `Idempotency-Key` header that handles transport retries only.
- A read-only contract for the **Duplicate Ownership Engine** — it surfaces
  badges, never merges.
- Multi-tenant isolation via `UNIQUE(uuid, company_id)` + `forCompany()`.

### Is NOT
- NOT a content-addressed deduplication system. The upload subsystem
  performs **no business duplicate detection** (INV-1).
- NOT a merging system. Candidates persist forever, even across duplicate
  uploads (Step 20).
- NOT a Cloudflare-Worker presign (that's v3; ADR-017).
- NOT a state machine in a separate transitions table (ADR-004).
- NOT a UNIQUE-constrained idempotency key (ADR-002, ADR-009).

---

## 2. The 8 product criteria (and where each is satisfied)

| # | Criterion | Where it lives |
|---|---|---|
| 1 | Super fast / responsive | Direct browser→R2 PUT (Step 5); `BroadcastChannel` zero-server fan-out for same-browser tabs (Step 14, ADR-018) |
| 2 | Min server load for 10K concurrent | 0 bytes through PHP; ~3 RPS/recruiter on Octane; ≥4 parser pods with bounded concurrency (75_capacity_plan) |
| 3 | Covers network failure | Idempotency-Key header for transport retry (Step 7, ADR-002, INV-4); offline/online listeners + localStorage cache (Step 9) |
| 4 | Perfect UX | Manual-values-always-win (INV-2); "Nothing you've entered has been lost" copy (97_faq_enterprise); no silent data loss |
| 5 | Enterprise level | Health endpoint with bearer-token auth (Step 15, ADR-008); structured logs with `pod_id`/`hostname`/`request_id` (Phase 6); `Schedule::onOneServer()` leader election |
| 6 | No duplicate check while upload | INV-1: every file pick = new session; engine handles dupes at submit time only |
| 7 | No multiple rows with same session | INV-1: one file pick = one `upload_session_uuid`; cross-request race resolved by conditional UPDATE (INV-3) |
| 8 | Don't change business logic / coins | Upload subsystem does NOT touch `coins`, `matching_score`, `application_status` pipelines; Step 20 forbids candidate mutation |

---

## 3. The 5 invariants (the rules that MUST hold)

> Full text + tests in [`96_invariants.md`](96_invariants.md).

### INV-1 — Every file pick creates a brand-new session
- `Idempotency-Key` HTTP header is a **per-request random token** from
  `crypto.randomUUID()` — NOT a content hash.
- Two different file picks always produce two different sessions, even if
  the bytes are identical.
- Duplicate detection happens only at submit time, in the **Duplicate
  Ownership Engine**, which DETECTS and REPORTS only — never merges
  (Step 20).

### INV-2 — Parser may populate empty fields only
- `<input data-parser-populated="false">` attribute prevents overwrite.
- `resume-populate-if-empty.js` checks before assigning.
- Test: `test_manual_values_always_win_over_parser`.

### INV-3 — Submit may retry; server verifies state before insert
- Conditional UPDATE inside a transaction: `WHERE status='Uploaded'`.
- Cross-request race → first pod wins; second returns
  `200 session_replay_winner_found`.
- Disambiguation from Step 20: INV-3 is "same session, two concurrent
  submits" → one candidate row; Step 20 is "different sessions, any
  tenant, any time" → both candidate rows persist.

### INV-4 — HTTP retry safety only — NOT business dedupe
- Same header on retried POST → server replays.
- Different header on new POST → new session.
- **No UNIQUE constraint** on `(company_id, idempotency_key)`.

### INV-5 — One parser result per session
- `resume_parse_results.resume_upload_id` UNIQUE.
- `ParseResumeJob::uniqueId()` returns the `resumeUploadId`; `uniqueFor = 60s`.
- `parser_attempts` capped at 1 server-side.

---

## 4. The 18 ADRs (decision log)

> Full text in [`99_decision_log.md`](99_decision_log.md).

| ADR | Decision | Why |
|---|---|---|
| 001 | Direct browser→R2 presigned PUT | 60 GB/s through PHP at 10K is impossible |
| **002** | **`Idempotency-Key` header is per-request random UUID, NOT content hash** | **Content-addressed dedupe violates INV-1; orphans the v1.5 carousel** |
| 003 | Reverb auth from session, not URL/localStorage | Cross-tenant leak prevention |
| 004 | State machine on `status` column, not separate transitions table | Conditional UPDATE is race-safe; audit table covers who/when |
| 005 | ONE Retry-After in parser; no retry loop | Fairness across tenants; 429 must not amplify |
| 006 | v1 `POST /uploadresume` kept as fallback | Defense in depth on JS module load failure |
| 007 | No `chmod 0777` on `bootstrap/cache` | Octane `octane:reload` shouldn't need world-writable |
| 008 | Health endpoint ON in v2.0.0a, even alone | Ops cannot decide partial vs total without it |
| **009** | **INDEX (not UNIQUE) on `(company_id, idempotency_key)`** | **Replay-lookup aid; UNIQUE would re-introduce ADR-002 anti-pattern** |
| 010 | File cap = 6 MB hard limit | No negotiation; matches v1 `max:6048` |
| 011 | Public career-apply uses HTTP polling at 1 Hz | Reverb has 1 channel/user; public blast unbounded |
| 012 | Multipart R2 PUT for ≥5 MB | Single 6 MB PUT has too low success rate on small-loss networks |
| 013 | Explicit "Manual values always win" test | Without this test, JS will eventually overwrite |
| 014 | Vertical slices from `HomeController` (813 KB) + `ServiceCompanyJobApplicationController` (16,633 LOC) | God controllers caused v1 500-chain bugs |
| 015 | Per-pod parser concurrency via Redis semaphore, key=`tenant_id` | One tenant spiking shouldn't throttle all |
| 016 | `documents()` UNIQUE on `(name='Resume', company_id, candidate_id)` | v1 had cross-candidate collision on this column |
| 017 | Move presign to Cloudflare Worker at v3.0, NOT v2 | Octane keeps low-risk proven path; v2.5 already halves load |
| 018 | `BroadcastChannel` for same-origin tab sync, NOT Reverb, in v2.5 | 95% of multi-tab is same browser process; zero server for that |

---

## 5. The data model (40_data_model.md)

```sql
CREATE TABLE resume_uploads (
    id                      BIGINT UNSIGNED PRIMARY KEY,
    uuid                    CHAR(36)        NOT NULL,
    company_id              BIGINT UNSIGNED NOT NULL,
    original_filename       VARCHAR(128),
    file_size_bytes         INT UNSIGNED,
    mime_type               VARCHAR(64),
    sha256_hex              CHAR(64),                -- sanity check, NOT idem key
    idempotency_key         CHAR(64),                -- per-request random UUID from HTTP header
    status                  VARCHAR(32)    NOT NULL, -- Created|Uploading|Uploaded|QueuedForParsing|Parsing|Parsed|Failed|Cancelled|Reconciled|Expired
    r2_temp_key             VARCHAR(255),
    r2_final_key            VARCHAR(255),
    expires_at              TIMESTAMP,
    upload_completed_at     TIMESTAMP     NULL,
    parser_started_at       TIMESTAMP     NULL,
    parser_completed_at     TIMESTAMP     NULL,
    parser_attempts         TINYINT       DEFAULT 0,
    parser_last_error       VARCHAR(255)  NULL,
    last_error              VARCHAR(255)  NULL,
    cleanup_reason          VARCHAR(64)   NULL,     -- Phase 5 Reconciled
    linked_candidate_id     BIGINT UNSIGNED NULL,
    linked_application_id   BIGINT UNSIGNED NULL,
    created_at              TIMESTAMP,
    updated_at              TIMESTAMP,

    UNIQUE KEY uq_uuid_company (uuid, company_id),     -- tenant-isolation structural guarantee
    INDEX idx_company_idem (company_id, idempotency_key), -- replay-lookup aid, NOT uniqueness gate
    INDEX idx_status (status),
    INDEX idx_expires (expires_at)
);

CREATE TABLE resume_parse_results (
    id                  BIGINT UNSIGNED PRIMARY KEY,
    resume_upload_id    BIGINT UNSIGNED NOT NULL UNIQUE, -- INV-5
    payload             JSON,
    parser_pod_id       VARCHAR(64),
    parsed_at           TIMESTAMP,
    -- ...
);

CREATE TABLE upload_session_audits (
    id                  BIGINT UNSIGNED PRIMARY KEY,
    session_uuid        CHAR(36),
    company_id          BIGINT UNSIGNED,
    action              VARCHAR(64),     -- ActionPresignIssued | ActionSubmitReplayWinnerFound | ActionReconcileSwept | ...
    actor_user_id       BIGINT UNSIGNED NULL,
    actor_ip_hash       CHAR(64),
    payload             JSON,
    request_id          CHAR(36),
    trace_id            CHAR(36),
    pod_id              VARCHAR(64),
    action_at           TIMESTAMP
);
```

**Indexes:**
- `UNIQUE(uuid, company_id)` — structural cross-tenant probe prevention.
- `INDEX(company_id, idempotency_key)` — replay-lookup performance. **NOT
  UNIQUE.** See ADR-009.
- `INDEX(status)`, `INDEX(expires_at)` — sweeper jobs.

---

## 6. The API (50_api_contracts.md)

### EP-1 — `POST /service_company/api/resume-sessions`

**Request headers (required):**
- `Idempotency-Key: <uuid>` — per-request random token, NOT a content hash.

**Request body:**
```json
{
  "original_filename": "jane_doe_resume.pdf",
  "file_size_bytes": 142137,
  "mime_type": "application/pdf",
  "sha256_hex": "sha256hex64..."
}
```

**Response 201 (created):**
```json
{
  "session": {
    "uuid": "uuid-v4",
    "company_id": 7,
    "expires_at": "2026-07-26T12:30:00Z",
    "r2_temp_key": "temp/2026/07/7/abc.../def.pdf"
  },
  "upload": {
    "url": "https://r2.cloudflarestorage.com/...?X-Amz-Expires=1800",
    "headers": {"x-amz-acl": "private"},
    "method": "PUT",
    "expires_at": "2026-07-26T12:30:00Z"
  }
}
```

**Response 200 (replayed — same `Idempotency-Key` on retry):**
- Same shape + `replayed: true` body field.
- Server log: `jobs.resume.session_replayed`.

**Error matrix:**
| Code | Cause |
|---|---|
| 400 | Missing/invalid `Idempotency-Key` header |
| 422 | file_size_bytes > 6 MB |
| 422 | mime_type ∉ allowed |
| 409 | session exists for this key but status=Failed — caller MUST issue a new key |
| 429 | rate-limit exceeded |

### EP-2 — `GET /service_company/api/resume-sessions/{uuid}`

- Read-only session state. 404 = "never existed / wrong tenant / expired".

### EP-3 — `POST /service_company/api/resume-sessions/{uuid}/finalize`

- Verify R2 object via HEAD (422 `upload_incomplete` if missing).
- Conditional UPDATE `Created`/`Uploading` → `Uploaded`.
- Emit Reverb `ResumeUploaded`.
- Dispatch `ParseResumeJob`.

### EP-4 — `DELETE /service_company/api/resume-sessions/{uuid}`

- Cancel + cleanup. Idempotent.

### EP-5 — `GET /service_company/api/resume-preflight`

- Returns `{"ok": true, "server_time": "..."}`.

### EP-6 — `/service_company/api/internal/health/resume`

- Bearer-token auth. Status `ok`/`degraded`/`down`.

---

## 7. The state machine (00_STEPS_IMPLEMENTATION_PLAN.md, Step 7)

```
         ┌────────────────────────────────────────────┐
         │                                            │
         ▼                                            │
[Created] → [Uploading] → [Uploaded] → [QueuedForParsing] → [Parsing] → [Parsed]
   │            │             │              │                    │          │
   ▼            ▼             ▼              ▼                    ▼          ▼
[Cancelled]  [Cancelled]  [Cancelled]   [Cancelled]            [Failed]  [Cancelled]
                                                                 │
                                                                 ▼
                                                          [Failed (terminal)]
```

**Transitions are guarded by conditional UPDATE:** `UPDATE … WHERE status=?`.

**Cross-request race (INV-3):** Two pods, same `session_uuid`, concurrent
submits. First pod's UPDATE claims the row; second pod's UPDATE matches
zero rows. Second pod reads `linked_candidate_id` and returns
`200 session_replay_winner_found` with the first pod's candidate_id.

**Cross-pod race on cold `Idempotency-Key` (ADR-009):** Two pods, same
header, concurrent presign. INSERT-then-SELECT pattern resolves to one
winner. The second pod sees the first pod's row via the lookup. Two INSERT
rows MAY exist transiently; the second is reconciled within 24 h by
`ReconcileOrphanUploadsJob`.

---

## 8. The 20-step implementation (00_STEPS_IMPLEMENTATION_PLAN.md)

| Phase | Steps | What ships |
|---|---|---|
| **Phase A — Additive infra** | 1-3 | Schema migrations, models, audit table, cleanup job |
| **Phase B — Idempotency + presign** | 4-6 | `idempotency_key` column, `POST /resume-sessions` endpoint, rate limits |
| **Phase C — Browser flow** | 7-9 | `resume-presign.js`, R2 PUT, `localStorage` cache, refresh recovery |
| **Phase D — Cross-tab + parser** | 10-14 | `BroadcastChannel`, `ParseResumeJob`, Reverb auth |
| **Phase E — Submit + extraction** | 15-16 | `finalize` + submit merge, god-controller extraction |
| **Phase F — Edit page** | 19 | Edit flow vertical slice with `Branch A`/`B`/`C` for replace/keep/remove |
| **Phase G — Verify + load** | 17-18 | Load test (10K concurrent), curl examples, rollback runbook |
| **Phase H — Duplicates** | **20** | **Duplicate Ownership Engine contract — DETECTS and REPORTS, never MERGES** |

---

## 9. The duplicate-ownership contract (Step 20 — the most important rule)

> **Cardinal rule:** *Candidate count in the ATS = submit count. Every
> successful submit creates exactly one new candidate that exists forever,
> and is never modified by the upload subsystem or the engine.*

### What the engine DOES
- Read `documents` rows + `candidates` rows.
- Compute a fingerprint per candidate.
- Surface a "possible duplicate" badge in the UI when two candidates share
  a fingerprint.

### What the engine DOES NOT (NEVER, at any cost)
- Merge two candidate rows.
- Delete a candidate row.
- UPDATE any candidate row.
- Touch `resume_uploads` or `documents` rows.
- Read or write across `company_id` boundaries.

### Acceptance scenarios

| # | Scenario | Outcome |
|---|---|---|
| 1 | Same recruiter picks same file twice, submits both | 2 sessions, 2 candidates, both flagged "possible duplicate" |
| 2 | Recruiter A and B (same tenant) upload same file, both submit | 2 sessions, 2 candidates, both flagged |
| 3 | Recruiter A and B (different tenants) upload same file, both submit | 2 sessions, 2 candidates, **one per tenant** — cross-tenant detection is opt-in per-tenant policy |
| 4 | Two browser tabs (same recruiter) submit concurrently, same file | INV-3 path → 1 candidate row + `200 session_replay_winner_found` |
| 5 | Network failure mid-submit, browser retries | INV-4 path → server replays same candidate_id |
| 6 | Recruiter re-uploads last week's file | 1 new session, 1 new candidate; old candidate NOT modified |
| 7 | Recruiter uploads same file 3 times in a minute, submits all 3 | 3 sessions, 3 candidates, all flagged |

### 4 guard tests (must pass; PR closed otherwise)

1. `test_engine_does_not_modify_uploaded_rows`
2. `test_engine_does_not_delete_candidates`
3. `test_engine_does_not_touch_resume_uploads`
4. `test_engine_read_only_under_load`

### Ops runbook
- Two candidates flagged "possible duplicate" → recruiter decides manually
  (typically: archive one).
- Engine shows a badge, never an action button. The "merge" concept does
  not exist in the engine.

---

## 10. The 14-step rollout (90_rollout_plan.md)

| Step | What ships | Feature flag | Default |
|---|---|---|---|
| v2.0.0a | Additive schema + health endpoint | `resumes.v2_health_endpoint` | ON |
| v2.0.0b | Idempotency key + presign endpoint | `resumes.presign_endpoint` | OFF→ON in 0c |
| v2.0.0c | Direct PUT to R2 + finalize | (same) | ON |
| v2.0.0d | Parser queue + Reverb | `resumes.v2_reverb_events` | OFF→ON |
| v2.0.0e | Submit preflight + dedup recovery | `resumes.submit_preflight` | ON |
| v2.0.0f | Public career-apply extracted | (controller boundary) | shipped |
| v2.0.0g | Recruiter add-candidate extracted | (controller boundary) | shipped |
| v2.0.0h | Cross-tab state via Reverb | `resumes.v2_reverb_events` | ON |
| v2.0.0i | Cancel + refresh recovery | (DELETE endpoint) | shipped |
| v2.0.0j | Retry manager: parser + upload dual | (JS module) | shipped |
| v2.0.0k | Bulk upload path | (separate slice) | shipped |
| v2.0.0l | Performance tuning + DTrace | (Horizon) | shipped |
| v2.0.0m | Public apply extracted from `HomeController` | (controller boundary) | shipped |
| v2.0.0n | GA + deprecate v1 paths | `resumes.legacy_uploadresume_compat` | OFF in v2.1.0 |

**Each step ships with the flag OFF, flips to ON with a 5-min smoke test,
rolls back without impact if smoke fails.**

---

## 11. Cross-cutting concerns

### Tenancy (CLAUDE.md rule 3)
- Every `ResumeUpload` Eloquent query goes through `withGlobalScope` or
  explicit `->where('company_id', $cid)`. **No `Resume::all()`.**
- Every Redis key prefixes with `c{company_id}:`.
- Every Reverb channel includes `company_id`; auth callback enforces
  `(int) Auth::user()->company_id === (int) $channelCompanyId`.
- ES index alias: `candidates-{company_id}-v2`.
- R2 key: `temp/{year}/{month}/{company_id}/...`.

### File validation (on `POST /resume-sessions`)
- `Idempotency-Key` is a UUID → 400 if missing/malformed.
- `mime_type ∈ {PDF, DOC, DOCX}` → 422.
- `file_size_bytes ≤ 6 MB` → 422.
- `sha256_hex` — sanity-check field, NOT used as idem key. Mismatch
  logged, not 422.
- `filename length 1..128` → 422.

### Rate limits
- `POST /resume-sessions`: 30/min/user, 120/min/company.
- `GET /resume-sessions/{uuid}`: 60/min/user, 240/min/company.
- `POST /finalize`: 30/min/user, 120/min/company.
- `DELETE /resume-sessions/{uuid}`: 30/min/user, 120/min/company.
- `GET /resume-preflight`: 60/min/user, 600/min/company.

### Secrets (CLAUDE.md rule 5)
- All `config(...)`, never raw `env()` outside `config/*.php`.
- `php artisan config:cache` works in production.
- `RESUMES_HEALTH_TOKEN` bearer token for the health endpoint.

### Logging (Phase 6)
- `ResumeLogContext::initJob($queue, $worker)` at job start.
- `RequestContextMiddleware` stamps `X-Request-Id` + `X-Trace-Id` on every
  request.
- Monolog processor pushes `pod_id` + `hostname` into every record's
  `extra`.
- Structured events: `jobs.resume.session_created`, `jobs.resume.session_replayed`,
  `jobs.resume.uploaded`, `jobs.resume.parsed`, `jobs.resume.parse_failed`.

### Health endpoint
- `GET /api/internal/health/resume` with bearer-token auth.
- Returns MySQL/Redis/R2/queue depth/orphan count/stuck count/parser backlog.
- Status `ok` if all checks `ok=true`, `degraded` if any `ok=false` BUT
  MySQL is `ok=true`, `down` if MySQL is `ok=false`.

---

## 12. Capacity targets (75_capacity_plan.md)

| Target | Number |
|---|---|
| Concurrent users | 10,000 |
| Files per second at peak | ~1,200 |
| Bytes through PHP | 0 (R2 direct) |
| HTTP req/s on Octane | ~6,000 (v2.0); ~2,000 (v2.5 with `BroadcastChannel`) |
| Reverb connections | ~10,000 (v2.0); ~500 (v2.5) |
| Parser pods | ≥4 with bounded concurrency |
| Parser timeout | 30 s |
| Parser retry | 1 with `Retry-After` |
| Session TTL | 30 min after creation |
| R2 temp lifecycle | 30 days |
| Worst-case single recruiter | 700 ms (v2.0); 350 ms (v2.5) |

---

## 13. Failure modes (45_failure_modes.md, summary)

| # | Failure | Behavior |
|---|---|---|
| 1 | Browser offline mid-upload | Badge "Network Lost"; session stays Created/Uploading; resume on online |
| 2 | Browser offline mid-finalize | Re-POST finalize on online; server is idempotent |
| 3 | Refresh after online event | GET `/resume-sessions/{uuid}` returns truth |
| 4 | Two pods both move Created→Uploaded | Conditional UPDATE → one wins |
| 5 | R2 PUT 4xx | Rotate presigned URL; same UUID holds |
| 6 | R2 PUT 5xx | Retry modal ×3 → Failed |
| 7-9 | Parser fails | 1 attempt; cap on retry; Failed status; manual fields survive |
| 10 | Submit POST times out | `200 session_replay_winner_found` on retry (INV-3) |
| 11 | Submit 5xx, browser retries | Engine flags "possible duplicate" badge; **never merges** (Step 20) |
| 12 | Session expires (>30 min) | PUT 403; "Resume expired, please pick again" |
| 13 | Reverb pod crash | State advances server-side; refresh re-reads |
| 14 | Pod OOM | HPA at 70% mem; rate-limit 120/min/company |
| 15 | Cross-tenant Reverb attempt | 403; metric `resume_reverb_auth_failed_total` |
| 16 | File < 1 KB | 422 too_small |
| 17 | Filename injection | `Illuminate\Support\Str::ascii(strip_control($orig))` + length 1..128 |
| 18 | Stale finalize (modal open during Parsed) | Disables Update Candidate (read-only inspection) |
| 19 | ES index write fails | `bulkelasticindex` queue; retries 5×; audit `ActionElasticIndexFailed` |
| 20 | Different `Idempotency-Key` on retry | New session, always (INV-1) |

---

## 14. Build from this doc tree

**Tier 1 — Build from these (canonical, all consistent):**
1. [`RESUME_UPLOAD_V2_COMPLETE.md`](RESUME_UPLOAD_V2_COMPLETE.md) ← this file
2. [`96_invariants.md`](96_invariants.md) — invariants + tests
3. [`00_STEPS_IMPLEMENTATION_PLAN.md`](00_STEPS_IMPLEMENTATION_PLAN.md) — 20-step plan
4. [`40_data_model.md`](40_data_model.md) — schema
5. [`50_api_contracts.md`](50_api_contracts.md) — endpoints + JSON
6. [`99_decision_log.md`](99_decision_log.md) — ADRs

**Tier 2 — Read for context (deep dives):**
- [`30_architecture.md`](30_architecture.md) — 6-tier architecture
- [`45_failure_modes.md`](45_failure_modes.md) — every way 10K breaks
- [`55_tenancy_and_security.md`](55_tenancy_and_security.md) — tenant scope + file validation
- [`60_storage_and_queue.md`](60_storage_and_queue.md) — R2 + queue
- [`70_jobs_and_events.md`](70_jobs_and_events.md) — `ParseResumeJob` + Reverb
- [`75_capacity_plan.md`](75_capacity_plan.md) — actual pod/queue/Redis numbers
- [`80_observability.md`](80_observability.md) — metrics + health + logs
- [`90_rollout_plan.md`](90_rollout_plan.md) — 14-step rollout

**Tier 3 — Aspirational / documentary:**
- [`95_god_mode_spec.md`](95_god_mode_spec.md) — v2.5 / v3 deferred work
- [`97_faq_enterprise.md`](97_faq_enterprise.md) — reviewer Q&A in production tone
- [`98_failure_matrix_never.md`](98_failure_matrix_never.md) — wall-poster recovery

---

## 15. Definition of done (per step)

A v2 step is **done** when ALL of these hold:

1. ✅ Code matches the canonical contract (this doc + the 5 invariants).
2. ✅ Schema is additive — no destructive migrations.
3. ✅ Tests written for the change (≥1 invariant test if touching INV-1..5).
4. ✅ `bash scripts/ai-guards.sh` clean (no `dd()`/`env()` in `app/`).
5. ✅ Reverb auth callback still does `(int)` cast on `company_id`.
6. ✅ `Idempotency-Key` is required and is per-request random UUID.
7. ✅ Engine read-only contract preserved (Step 20).
8. ✅ `audit_logs` row written for state transitions.
9. ✅ Feature flag defaults to OFF in production until smoke-tested.
10. ✅ Rollback runbook entry exists (per-flag + nuclear).

---

## 16. The non-negotiable recap

| Rule | Source | Why |
|---|---|---|
| Every file pick = new session | INV-1 | The upload subsystem performs no business duplicate detection |
| Manual values always win | INV-2 | The v1 product brief's most important rule |
| Server verifies state before insert | INV-3 | Retry safety at submit time |
| `Idempotency-Key` is per-request random | INV-4 + ADR-002 | Not a content hash |
| One parser result per session | INV-5 | Bounded concurrency |
| **Engine DETECTS and REPORTS; never MERGES** | **Step 20** | **The cardinal rule** |
| INDEX (not UNIQUE) on `(company_id, idem_key)` | ADR-009 | Replay-lookup, not uniqueness |
| `UNIQUE(uuid, company_id)` | INV-3 | Cross-tenant probe prevention |
| `config()`, never `env()` outside `config/` | CLAUDE.md rule 5 | `config:cache` correctness |
| No `chmod 0777` on `bootstrap/cache` | ADR-007 | Octane `octane:reload` |
| Health endpoint ON in v2.0.0a | ADR-008 | Ops partial-vs-total visibility |
| `BroadcastChannel` for same-origin tabs | ADR-018 | 95% multi-tab is same browser |
| Cloudflare Worker presign deferred to v3 | ADR-017 | Octane keeps low-risk path |

---

**End of consolidated doc. For deep dives, follow the links above.**
