# 50 — API contracts

> *Intent: define endpoint shapes, request/response JSON, error matrix, and*
> *cross-cutting headers.*

## Cross-cutting

| Header | Always | Notes |
|---|---|---|
| `X-Request-Id` | request + response | ULID; sourced from middleware |
| `X-Trace-Id` | request + response | ULID; ditto |
| `X-Company-Id` | response | from session, NEVER trusted from request body |
| `Cache-Control: no-store` | response | sensitive content |

Auth: existing service_company session guard. No new auth tokens.
Tenant: `Auth::user()->company_id` injected by middleware.

Rate limits (per-user primary, per-company secondary):

| Endpoint | Per-user limit | Per-company limit | Window |
|---|---|---|---|
| POST `…/resume-sessions` | 30/min | 120/min | 60 s |
| GET `…/resume-sessions/{uuid}` | 60/min | 240/min | 60 s |
| POST `…/resume-sessions/{uuid}/finalize` | 30/min | 120/min | 60 s |
| DELETE `…/resume-sessions/{uuid}` | 30/min | 120/min | 60 s |
| GET `…/resume-preflight` | 60/min | 600/min | 60 s |
| GET `…/internal/health/resume` | bearer token | n/a | n/a |

## Endpoints

### EP-1 — `POST /service_company/api/resume-sessions`

Create a brand-new session for this user-initiated file pick, OR replay
an existing one when the browser retries the same `fetch()` after a
network blip.

**Request headers (required)**:
- `Idempotency-Key: <uuid-or-token>` — **per-request random token**,
  generated client-side via `crypto.randomUUID()` at the moment of
  presign-request construction. Kept in JS module closure for the
  duration of the `fetch()` retries; discarded once the response is
  processed. **NOT a content hash.** See ADR-002, INV-4.
  Accepted formats: UUIDv4 (`^[a-f0-9-]{36}$`) or 64-char hex.
- All cross-cutting headers from the table above.

**Request body**:
```json
{
  "original_filename": "jane_doe_resume.pdf",
  "file_size_bytes": 142137,
  "mime_type": "application/pdf",
  "sha256_hex": "sha256hex64..."
}
```

**Response 201 (created)**:
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

**Response 200 (replayed — same `Idempotency-Key` on retry)**:
Same shape as above; body field `replayed: true` is added so the
client can distinguish "fresh session" from "transport retry". Server
log emits `jobs.resume.session_replayed`.

**Error matrix**:
| Code | Cause |
|---|---|
| `400` | missing/invalid `Idempotency-Key` header |
| `422` | file_size_bytes > 6 MB |
| `422` | mime_type ∉ allowed |
| `409` | session exists for this key but status=Failed — caller MUST issue a new `Idempotency-Key` to start fresh |
| `429` | rate-limit exceeded (X-RateLimit-Reason header) |

### EP-2 — `GET /service_company/api/resume-sessions/{uuid}`

Read-only session state. Auth: tenant-bound. 404 indistinguishable from
"never existed / wrong tenant / expired" — exact policy from v1 Phase 9.2
to prevent tenant enumeration.

**Response 200**:
```json
{
  "session": {
    "uuid": "...",
    "status": "Uploaded",
    "company_id": 7,
    "original_filename": "...",
    "file_size_bytes": 142137,
    "expires_at": "2026-07-26T12:30:00Z",
    "upload_completed_at": "2026-07-26T11:55:00Z",
    "linked_candidate_id": null,
    "linked_application_id": null
  }
}
```

**Error matrix**:
| Code | Cause |
|---|---|
| `404` | not found / wrong tenant / expired |
| `410` | expired — includes `expires_at` for client UI |

### EP-3 — `POST /service_company/api/resume-sessions/{uuid}/finalize`

Mark session as `Uploaded`. Idempotent.

**Request**: empty body.

**Server steps**:
1. Verify session row in `Created`/`Uploading` only.
2. HEAD R2 `r2_temp_key`. If 404 → 422 `upload_incomplete`.
3. Conditional UPDATE: status='Created' OR status='Uploading' → `Uploaded`,
   `upload_completed_at=now()`.
4. Emit Reverb event `ResumeUploaded` (status only — no payload).
5. **No server-side parser dispatch** (REMOVED 2026-07-27). The
   browser-side `pdf.js` + `re_test()` populated the form fields
   during Step 4's R2 PUT (parallel); by the time `finalize` lands
   parsing is done.

**Error matrix**:
| Code | Cause |
|---|---|
| `404` | wrong tenant / session gone |
| `422` | upload_incomplete (R2 object missing) |
| `409` | already finalized with different status |

### EP-4 — `DELETE /service_company/api/resume-sessions/{uuid}`

Cancel + cleanup. RECOMMENDED before `Completed` (terminal link);
safe to call any time. (Original wording referenced `Parsed`; that
status no longer exists in v2 — REMOVED 2026-07-27.)

**Server steps**:
1. Conditional UPDATE: status NOT IN ('Cancelled','Reconciled','Expired') → 'Cancelled'.
2. Best-effort DELETE R2 object.
3. Audit row: `ActionCancelRequested`, `ActionCancelCompleted`.
4. Reverb event: `ResumeCancelled`.

**Idempotent**: 200 every time, never throws beyond auth.

### EP-5 — `GET /service_company/api/resume-preflight`

Submit-side reachability check.

**Response 200**:
```json
{
  "ok": true,
  "server_time": "2026-07-26T12:00:00Z"
}
```

### EP-6 — `/service_company/api/internal/health/resume`

Bearer token. Health endpoint from v1 Phase 6 carried forward.

**Response (status=degraded)**:
```json
{
  "status": "degraded",
  "pod_id": "app-1",
  "checks": {
    "mysql":        {"ok": true,  "latency_ms": 4},
    "redis":        {"ok": true,  "latency_ms": 1},
    "r2":           {"ok": true,  "latency_ms": 12},
    "queue_default": {"ok": true,  "depth": 12},
    "scheduler":    {"is_leader": true, "pod_id": "app-1"},
    "orphan_uploads_count": 0,
    "stuck_sessions_count": 0
  }
}
```
(REMOVED 2026-07-27 — `parser_backlog_count` field is gone; there is no
server-side parser queue to measure. Browser-side pdf.js parses are
unobservable from the health endpoint.)

## Versioning

`/api/v1/...` initially. v2 is the only version. Future breaking change →
`/api/v2/...`.

## Backwards compatibility

| v1 endpoint | v2 equivalent | v1 compat |
|---|---|---|
| POST `/uploadresume` | internal presign flow | kept behind flag, deprecated |
| GET `/extract-resume-applications` | unchanged (parser proxy) | kept |
| `careerStore` files | unchanged | kept (v1 path) |

## OpenAPI / Swagger

v2 produces an OpenAPI 3.1 JSON under `docs/future_resume_upload_2.0/api.json`
(not yet generated; planned for v2.0.0b).
