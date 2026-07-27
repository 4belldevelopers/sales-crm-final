# 80 — Observability

> *Intent: name every counter, log line, and health probe. This is what we*
> *read when production breaks.*

## Logging contract

Every v2 log line includes, at minimum:

| Field | Source | Why |
|---|---|---|
| `pod_id` | `gethostname()` | multi-pod forensics |
| `request_id` | `RequestContextMiddleware` | one request → all its lines |
| `trace_id` | ULID generated on entry | cross-service tracing (SG→API→Parser) |
| `uu_id` (resume upload uuid) | contextual | one upload → all its lines |
| `company_id` | from `Auth::user()` (NEVER from body) | tenant-scope only |
| `event` | structured event name | grep-friendly |
| `level` | log level | filters |

Sample structured-log lines (canonical grep patterns):

```
jobs.resume.session_created     { uuid, company_id, idempotency_key }
jobs.resume.session_reused      { uuid, company_id, idempotency_key }
jobs.resume.session_expired     { uuid, company_id, last_status }
jobs.resume.upload_started      { uuid, file_size_bytes, mime_type }
jobs.resume.upload_progress     { uuid, pct, eta_ms }   # throttled at ±5% increments
jobs.resume.upload_finalized    { uuid, took_ms_status }
jobs.resume.upload_failed       { uuid, reason, attempts }
jobs.resume.parse_started       { uuid, extractor_version }
jobs.resume.parse_progress      { uuid, pct, stage }    # throttled to 4 events
jobs.resume.parse_completed     { uuid, took_ms, n_fields }
jobs.resume.parse_failed        { uuid, code, retry_after_s? }
jobs.resume.parse_retry         { uuid, attempt, waited_ms }
jobs.resume.submit_attempted    { uuid, idempotency_token }
jobs.resume.submit_completed    { uuid, candidate_id, took_ms }
jobs.resume.submit_recovered    { uuid, candidate_id from cache }
jobs.resume.cross_tenant_attempted { supplied_company_id, actual_company_id }
jobs.resume.security.throttled  { tenant, ip_hash, reason }
```

## Metrics: Redis counter store

Each metric has:
- Redis key `resume:metrics:{name}`
- TTL 90 days
- Monotonic via `INCRBY`
- Read by health endpoint + dashboards

| Metric | Type | Where |
|---|---|---|
| `resume_presign_total` | counter | counter incremented in `ResumeUploadSessionController::store()` |
| `resume_upload_started_total` | counter | API tier |
| `resume_upload_completed_total` | counter | API tier, on finalize |
| `resume_upload_failed_total`  | counter | catch-all at PUT and finalize; tagged with reason |
| `resume_parse_started_total` | counter | Pickup from queue |
| `resume_parse_completed_total` | counter | ParseResumeJob success path |
| `resume_parse_failed_total` | counter | ParseResumeJob failure path; tagged code |
| `resume_parser_429_total` | counter | parser returned Retry-After |
| `resume_submit_total` | counter | store() called |
| `resume_submit_duplicate_total` | counter | store() returns existing |
| `resume_resolve_total` | counter | UI expands `resume_session_uuid_*` |
| `resume_reverb_failed_total` | counter | Reverb crash markers |
| `resume_idempotency_key_reuse_total` | counter | new sessions that reused idem UUID |
| `resume_idempotency_key_rotation_total` | counter | new sessions seeing diff idem key |

## Health endpoint

GET `/service_company/api/internal/health/resume` — bearer token auth.

### Response shape

```json
{
  "status": "ok" | "degraded" | "down",
  "pod_id": "app-1",
  "checks": {
    "mysql":               { "ok": true | false, "latency_ms": 4, "code": null, "message": null },
    "redis":               { "ok": true | false, "latency_ms": 1, "code": null, "message": null },
    "r2":                  { "ok": true | false, "latency_ms": 12, "code": null, "message": null },
    "queue_default":       { "ok": true | false, "depth": 12 },
    "queue_parse_resumes": { "ok": true | false, "depth": 24 },
    "queue_bulkelasticindex": { "ok": true | false, "depth": 8 },
    "scheduler_leadership":{ "is_leader": true | false, "lock_owner_pod": "app-1" },
    "orphan_uploads_count":            0,
    "stuck_sessions_count":             0,
    "parser_backlog_count":             7,
    "global_throttle_hit_total":       14
  }
}
```

`/`s status map:
- All checks ok → "ok"
- All-but-mysql ok → "degraded"
- MySQL fails → "down"

### Latency budget

< 250 ms target. Soft check inside timeout controller.

## Dashboards (Grafana)

Two boards in v2.0.0b:

### Board 1 — Live load

- Throughput per second by kind
- Queue depth per queue
- Latency p50/p95/p99
- 5xx rate (sliding 1 min)

### Board 2 — Subsystem health

- orphan_uploads over time
- stuck_sessions over time
- parser 429 rate
- Parser pod CPU

## Alerting

| Alert | Trigger | Severity |
|---|---|---|
| ResumeParseFailed > 5% in 5 min | `parse_failed_total/(parse_started_total) > 0.05` for 5 min | P3 |
| R2 PUT 5xx > 1% in 1 min | `upload_failed_total{reason=r2_5xx}/upload_started_total > 0.01` | P2 |
| Queue depth > 1000 sustained | `parse_resumes depth > 1000` for 5 min | P2 |
| Stale sessions > 50 | `currentStuckSessions() > 50` | P3 |
| MySQL latency > 150 ms | `mysql.latency_ms > 150` for 1 min | P2 |
| Pod 5xx rate > 0.5% | pod metric | P2 |
| Health `down` | orchestrator | P1 oncall |

## Structured event payload

Use Monolog's `Log::info('jobs.resume.<event>', [array])` with
`ResumeLogContext` (carried forward from v1 Phase 6). The processor
adds `pod_id`, `hostname`, `request_id`, `trace_id` automatically.

## Debug-session for on-call

A read-only SQL view (`vw__resume_session_lifeline`) is added in
v2.0.0b:

```sql
SELECT
  ru.uuid, ru.company_id, ru.status, ru.created_at,
  ru.parser_started_at, ru.parser_completed_at,
  ru.last_error,
  u.email AS recruiter_email,
  a.action AS last_audit_action, a.created_at
FROM resume_uploads ru
LEFT JOIN users u ON u.id = ru.user_id
LEFT JOIN upload_session_audits a
  ON a.resume_upload_id = ru.id
  AND a.created_at = (
    SELECT MAX(a2.created_at)
    FROM upload_session_audits a2
    WHERE a2.resume_upload_id = ru.id
  )
WHERE ru.uuid = ?;
```

Used by on-call. NEVER referenced from app code.
