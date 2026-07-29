# 85 — Scalability redteam

> *Intent: a check-list of "what happens when each component fails" —*
> *pre-trained on-call muscle memory.*

## RED-1 — Octane pods all crash

| Question | Answer |
|---|---|
| Reverb connections? | drop; clients auto-reconnect on next page load |
| In-flight PUTs? | carry on (go to R2, not to PHP) |
| In-flight finalize POSTs? | return 502; client retry from localStorage |
| Recovery time | HPA = 30 s to 60 s for first new pods ready |
| On-call action | none — let HPA do its thing |

## RED-2 — Redis OOM

| Question | Answer |
|---|---|
| Counter writes fail | bounded protection: rate-limit falls back to local array → on-call alert |
| Rate-limit fails | `429` returned; clients hold up; messages degrade |
| Session/idem key | unaffected (Redis is not source of truth) |
| Recovery time | drain queue, restart Redis (planned maintenance) |
| On-call action | page vendor if multi-region; clone from backup |

## RED-3 — R2 outage

| Question | Answer |
|---|---|
| New PUTs | client gets 5xx; auto-retry up to 3; UI shows "Service Busy" |
| Finalize → HEAD check | server timeout → 422 `upload_unknown`; user retries |
| Reads (HEAD to verify) | fail with timeout |
| Presign | unaffected (R2 PUT is the only thing to R2) |
| Recovery time | vendor SLA |

## RED-4 — Reverb pods all crash

| Question | Answer |
|---|---|
| Cross-tab progress | stale; recovery on next refresh |
| Upload progress UI | local XHR still works |
| Submit preflight | unaffected |

## RED-5 — MySQL primary dies

| Question | Answer |
|---|---|
| Write endpoints | 503; user retries on L4 |
| Read endpoints | read replica services |
| Recovery time | fail-over (Read replicas promoted) |
| On-call action | carry out failover plan |

## RED-6 — Elasticsearch cluster fails

| Question | Answer |
|---|---|
| Candidate write | DB-only; candidate exists in `job_applications`; ES reindexed later |
| Search | 500; user sees "Search temporarily unavailable" |
| Recovery time | IC vendor SLA |

## RED-7 — Network partition between Octane ↔ Reverb

| Question | Answer |
|---|---|
| Upload + browser-side pdf.js parse | continue; Reverb fail-out is at-most-once |
| Submit | unaffected |

## RED-8 — Cert / TLS expired

| Question | Answer |
|---|---|
| API | connection refused — on-call alert |
| Browser PUT | fails; resume submit blocked |

## RED-9 — Anti-bot DDoS

| Question | Answer |
|---|---|
| 100K presign POSTs | rate-limit kicks in 30/min/user, 120/min/company |
| Larger DDoS | Cloudflare WAF + origin protections |
| Recovery time | vendor SLA |

## RED-10 — Idempotency key reused from blocked tenant

| Question | Answer |
|---|---|
| Same hashing (x_c_id, x_name) | possible but rare across tenants; still surfaces same UUID per binding |
| Different tenant subscribing | 403; metric increments |
| Storage inside R2 | bounded; we don't double-write |

## RED-11 — Browser hangs during chunked PUT

| Question | Answer |
|---|---|
| LocalStorage state | preserved; user can refresh |
| Resumption on refresh | `GET /api/resume-sessions/{uuid}` returns current status |
| R2 PUT retry | resumes from the size of last successful byte |

## RED-12 — User uploads massive file (1 GB+)

| Question | Answer |
|---|---|
| Client-side cap | 6 MB hard cap; UI prevents |
| Server-side cap | presign throws 422 |
| Bypass attempt | R2 bucket policy enforces content-length (config) |

## RED-13 — Bad binary (zip bomb / nested DOCX)

| Question | Answer |
|---|---|
| Doc parsers | browser-side pdf.js + mammoth.js have built-in limits (1 GB extraction ceiling) |
| Defense in depth | client-side cap = 6 MB hard cap; server-side `presign` validates `file_size_bytes` and rejects oversized (422) before signing |
| Recovery time | manual patch |

## RED-14 — Hot tenant (one tenant pushes 50% of load)

| Question | Answer |
|---|---|
| Per-company rate limit | 120/min caps at 2/s tenant — absorbs |

## RED-15 — Rollout goes wrong

| Question | Answer |
|---|---|
| 50% error rate in v2 code path | revert Octane image; light traffic diversion via LB rule |
| Recovery time | 1 minute to 5 minutes |

## RED-16 — Bad `ResumeUploadException` shipping

| Question | Answer |
|---|---|
| Returns 500 | caught by global handler; render `errors.upload_general` Blade |
| Sensitive log | never logs server exception details to user |

## RED-17 — Cloudflare CDN purge failure

| Question | Answer |
|---|---|
| Static asset | 404 from Chromium — fallback content |
| Recovery | vendor purge retry |

## RED-18 — Dead-letter queue flooded

| Question | Answer |
|---|---|
| Cap | `dlq.default` cap at 1000; older records shipped off |
| Recovery | dlq overflow written to S3 flat files for triage |

## RED-19 — Health endpoint runs slow

| Question | Answer |
|---|---|
| Timeout | MySQL probe has explicit timeout; does not block other checks |
| On long tail | metric `resume_health_endpoint_latency_ms` |

## RED-20 — Worker pod OOM

| Question | Answer |
|---|---|
| `bulkelasticindex` worker OOM | pod restarts; Horizon reschedules |
| Reconciliation worker OOM | pod restarts; idempotent re-run picks up where it left off (state machine guard) |

## Worst case scenario

Mass user surge + Reverb down + Redis slow. Response:

1. Octane ingests presign POSTs up to 30/min/user; 429s at the limit
2. Browser-to-R2 PUTs thereafter directly; no PHP byte path
3. Submit preflight returns 5xx; client retries with backoff
4. Finalize persists session row in DB only (case if Redis is down)
5. Reverb fall-back: refresh re-sync from DB

This is exactly the scenario the v1 /login 500 chain taught us
to design around: server-side boot-time dependencies must be
loose enough to keep the system "partially serving" rather than
"completely broken".

This is the intent of v2: **2** end up partially-degraded, never
**completely broken**, in the production stack.
