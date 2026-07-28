# 30 — Architecture

> *Intent: stop designing from the top down at "feature exists" and start from*
> *"what does 10K concurrent look like in the pipeline?"*

## Six-tier read

```
[Browser]   ── (presigned PUT) ─►  [R2 temp bucket]
[Browser]   ── HTTP (JSON) ─────►  [API tier (Octane)]
                                       │
                                       ▼ events
[Browser]   ◄── Reverb WS ───── [Reverb pod]
                                       │
                                       │ jobs
                                       ▼
                                   [Horizon]
                                       │
                                       │ queue
                                       ▼
                          [Reconcile / Repair / BulkElasticIndexCandidate]
                                       │
                                       ▼ state
                                  [MySQL (state machine)]
                                       │
                                       │ index
                                       ▼
                                  [Elasticsearch]
```

## Tier-1 — Browser

Single source of interaction. Runs:
- `resume-upload-progress.js` — orchestrator + UI
- `resume-populate-if-empty.js` — browser-side pdf.js + `re_test()` driver
  (REMOVED 2026-07-27: was a parser fan-out subscriber; now just runs
  the in-browser parser inline)
- `resume-presign.js` — presign request + state restore
- Input validation: file extension, size, MIME
- localStorage: `resume_session_uuid_{company_user_id}` + idempotency key
- Hashes: SHA256 of file bytes via `SubtleCrypto.digest('SHA-256', …)`
  before posting presign; same hash re-checked at submit time

## Tier-2 — API tier (Octane / FrankenPHP)

3 endpoints, all JSON, no file bytes:

| Endpoint | Method | Purpose |
|---|---|---|
| `/service_company/api/resume-sessions` | POST | create + presign |
| `/service_company/api/resume-sessions/{uuid}` | GET | status (404 if expired/wrong tenant) |
| `/service_company/api/resume-sessions/{uuid}/finalize` | POST | mark uploaded; no server parse dispatch (REMOVED 2026-07-27) |
| `/service_company/api/resume-sessions/{uuid}` | DELETE | cancel — emit + cleanups |
| `/service_company/api/resume-preflight` | GET | submit-side reachability |
| `/service_company/api/internal/health/resume` | GET | ops-only bearer-token |

Auth: session-cookie bearer (existing service_company guard). Tenant:
`Auth::user()->company_id` injected into every endpoint, never accepted
from the request body.

Why these endpoints and not bulk psPro:
- presign is the **only** state mutation on the API.
- GET is reentrant — idempotent reads.
- DELETE has a small allocator surface; rate-limited per-user/per-session.

## Tier-3 — R2 temp bucket

Direct browser PUT via presigned URL signed by Laravel. Lifecycle:
- Bucket key: `temp/{year}/{month}/{company_id}/{uuid}-{sha256-7-prefix}`
- Lifecycle rule: 30-day auto-delete, dedicated bucket-prefix
- Public read: false; **never** public. URLs are signed and short TTL.
- Object is restricted to `application/pdf`,
  `application/msword`, `application/vnd.openxmlformats-officedocument.wordprocessingml.document`.

## Tier-4 — Reverb pod

Per-tenant private channel `private-resume.{companyId}`. Subscribed by
the browser when it boots; same-tenant-only auth (server-side, not URL).

Channel events:

| Event | Payload | When |
|---|---|---|
| `ResumeUploaded` | `{uuid, original_filename, file_size_bytes, t_upload_ms}` | after R2 finalize (status only — REMOVED 2026-07-27: no `fields_summary`) |
| ~~`ResumeParseProgress`~~ | REMOVED 2026-07-27 | n/a |
| ~~`ResumeParsed`~~ | REMOVED 2026-07-27 | n/a |
| ~~`ResumeParseFailed`~~ | REMOVED 2026-07-27 | n/a |
| `ResumeCancelled` | `{uuid, at_pct}` | on DELETE |
| `ResumeFailed` (status only, NOT parser) | `{uuid, last_error}` | on terminal state |

Backpressure: each tenant at most 5 outstanding connections; per-channel
fan-out ≤4 subscribers (tested). Reverb pod count = 4 (multiply by 10 if
going beyond 50K).

## Tier-5 — Horizon queue + workers

Workers distribute by `tries` and `backoff`. Concurrency cap per pod:

| Queue | tries | backoff | pod concurrency |
|---|---|---|---|
| ~~`parse_resumes`~~ | REMOVED 2026-07-27 — no server parser queue | n/a | n/a |
| `reconcile_orphans` | 1 | — | 1 per pod |
| `repair_stuck` | 1 | — | 1 per pod |
| `bulkelasticindex` | 5 | [10, 30, 60, 120, 300] | 1 (batch 1,000 / 5 s) |

> **Removed 2026-07-27 — parser concurrency reasoning.** The
> `tries=1` rationale belonged to `parse_resumes`. There is no
> server parser; no `RESUMES_QUEUE_PARSER_TRIES` env knob.

## Tier-6 — MySQL + Elasticsearch

MySQL writes are CONDITIONAL UPDATEs (race-safe). Elasticsearch writes
go through `ElasticBulkelasticindex` worker — never inline on the request
that produced the candidate row.

## Architecture cross-cuts

### AC-1 — Tenant isolation everywhere (CLAUDE.md rule 3)

Every row has `company_id`. Every Redis key has `company_id` BEFORE the
business key. Every Reverb channel has it. Every R2 key has it. Every ES
index alias has it. A line in ContributeStyle songbook: "no `company_id`
prefix = CLOSED at PR review."

### AC-2 — Config, not env (CLAUDE.md rule 5)

Every value referenced from controllers/services comes from `config()`.
Direct `env()` only inside `config/*.php`. This matters at scale
because `config:cache` produces a static map; `env()` returns null under
Octane.

### AC-3 — Idempotency-keyed (split-brain survivability)

Two pods hit `finalize` simultaneously — only one wins (the conditional
UPDATE); the other is a safe no-op. Idempotency key flows through:

- Browser → presign request (idempotency key)
- Server `ResumeUploadSession` row (key indexed unique per tenant)
- Browser → finalize request (same key)
- Server `finalize` (set on session row)
- Submit (`StoreJobApplication`) verifies the *session* is `Uploaded`
  and the file still exists in R2

### AC-4 — Observability as first-class

Every state transition logs at structured level with `pod_id`, `request_id`,
`uuid`, `company_id` fields. The "you can't debug this post-mortem" pattern
is forbidden.

### AC-5 — Failure mode documented at every state

Every arrow on the diagram in [`30_workflow_diagram.md`](30_workflow_diagram.md)
maps 1:1 to a row in [`45_failure_modes.md`](45_failure_modes.md).

### AC-6 — Minimize hot-path permissions

`mkdir bootstrap/cache && chmod 0777` was a fix from the v1 /login 500
chain. v2 designs to avoid 0777 outright: Octane worker bootstrap
credentials are a deployment-level concern, not an `artisan` runtime
behavior. (See [`99_decision_log.md`](99_decision_log.md) ADR-007.)
