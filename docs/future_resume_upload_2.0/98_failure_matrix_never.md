# 98 — Failure Matrix (what to NEVER do)

> *Intent: tabulate the recovery rules for each environmental failure and*
> *the keep-out rules for design. Companion to [`45_failure_modes.md`](45_failure_modes.md)*
> *(detailed prose) and [`96_invariants.md`](96_invariants.md) (kept-true*
> *invariants). This file is the wall-poster version.*

## Recovery matrix

| Failure | Server / client behavior | Cite |
|---|---|---|
| Wi-Fi lost | **Auto-resume.** Browser ONLINE event → validate server with HEAD/R2 → resume PUT from offset if multipart, restart single-PUT otherwise | [45_failure_modes.md §FAIL-1 + §FAIL-3](../future_resume_upload_2.0/45_failure_modes.md#fail-1--browser-offline-mid-upload), [30_workflow_diagram.md §Network Failure Recovery](../future_resume_upload_2.0/30_workflow_diagram.md) |
| R2 timeout | **Retry.** Up to 3 attempts with exponential backoff; if all 3 fail → session row → `Failed`; `last_error='R2 503'` | [45_failure_modes.md §FAIL-6](../future_resume_upload_2.0/45_failure_modes.md#fail-6--r2-put-5xx-server-side) |
| Parser 429 with `Retry-After` | **Honor `Retry-After`.** `sleep(min(seconds, cap=10))` once; total wait ≤ `Retry-After + 1 s`. One attempt only | [45_failure_modes.md §FAIL-8](../future_resume_upload_2.0/45_failure_modes.md), [ADR-005](../future_resume_upload_2.0/99_decision_log.md) |
| Browser Refresh | **Restore session.** GET `/api/resume-sessions/{uuid}` returns current status; UI rehydrates from `localStorage` + DB; resume upload if `Created`/`Uploading`, mark green if `Uploaded` | [30_workflow_diagram.md §Refresh Recovery](../future_resume_upload_2.0/30_workflow_diagram.md), [45_failure_modes.md §FAIL-3](../future_resume_upload_2.0/45_failure_modes.md), [65_browser_sizing.md](../future_resume_upload_2.0/65_browser_sizing.md) |
| Session expired | **Create new session.** PUT returns 403; UI shows "Resume expired, please pick again"; `ResumeUploadSession::createOrPresign()` issues new UUID on next pick | [45_failure_modes.md §FAIL-12](../future_resume_upload_2.0/45_failure_modes.md#fail-12--session-expires-presign--upload-complete--30-min), ADR-002 |
| PHP (Octane) restart / New pod | **Retry.** Octane pods are stateless; in-flight presign/finalize fail with 502 → client retries with same idem key (INV-4); state in DB only | [85_scalability_redteam.md §RED-1](../future_resume_upload_2.0/85_scalability_redteam.md#red-1--octane-pods-all-crash), [95_god_mode_spec.md §v2.5 path](../future_resume_upload_2.0/95_god_mode_spec.md) |
| Redis (counter store) restart | **Retry.** Rate-limiter fails open; counters drop back to 0; same idem key still works (idempotency is Redis-of-truth for the metric, MySQL for state) | [85_scalability_redteam.md §RED-2](../future_resume_upload_2.0/85_scalability_redteam.md#red-2--redis-oom), ADR-014 |
| Deployment (rolling in new Octane image) | **Resume.** In-progress presigned PUTs continue (R2 holds the URL); in-flight finalizes 502 once per pod; client retry with same idem key; new pods ready in 30-60 s via HPA | [85_scalability_redteam.md §RED-15](../future_resume_upload_2.0/85_scalability_redteam.md#red-15--rollout-goes-wrong), [90_rollout_plan.md](../future_resume_upload_2.0/90_rollout_plan.md) |

## Never Matrix

| ❌ Rule | Cite |
|---|---|
| ❌ **Upload through PHP.** Zero bytes through PHP for resume uploads. Only presigned PUT metadata. | [ADR-001](../future_resume_upload_2.0/99_decision_log.md), [30_architecture.md §Tier-3 R2](../future_resume_upload_2.0/30_architecture.md) |
| ❌ **Replay submit without server verification.** Browser may auto-retry, but server verifies session state on every attempt and returns existing candidate_id if linked. (Sharpened from "never replay automatically" — see [§INV-3 sharpening](../future_resume_upload_2.0/96_invariants.md#inv-3--submission-may-retry-but-server-verifies-state-before-any-insert)) | [INV-3](../future_resume_upload_2.0/96_invariants.md), [45_failure_modes.md §FAIL-10 + §FAIL-11](../future_resume_upload_2.0/45_failure_modes.md) |
| ❌ **Overwrite manual fields.** Parser populates empty-only; `data-parser-populated="false"` gates fill; server doesn't write back into applicant fields. | [INV-2](../future_resume_upload_2.0/96_invariants.md), [ADR-013](../future_resume_upload_2.0/99_decision_log.md) |
| ❌ **Trust localStorage for tenant identity / auth.** localStorage may hold session UUID and idempotency key, but `company_id` MUST come from server session via `Auth::user()->company_id`. (Sharpened from "trust localStorage" — it's not banned entirely; only for security-bearing state.) | [55_tenancy_and_security.md §Tenancy](../future_resume_upload_2.0/55_tenancy_and_security.md), [ADR-003](../future_resume_upload_2.0/99_decision_log.md) |
| ❌ **Assume `navigator.onLine == Internet`.** Browser online/offline events are unreliable (captive portals, DNS hijacks). Recovery: actual preflight ping (`GET /api/resume-preflight`) before declaring reachable. | [45_failure_modes.md §FAIL-1 recovery](../future_resume_upload_2.0/45_failure_modes.md), [50_api_contracts.md §EP-5](../future_resume_upload_2.0/50_api_contracts.md) |
| ❌ **Couple parser with upload.** Parser runs on its own `parse_resumes` queue; pod-isolated; bounded concurrency; bounded retries; UI progress via Reverb. | [70_jobs_and_events.md §ParseResumeJob](../future_resume_upload_2.0/70_jobs_and_events.md), g-4 |
| ❌ **Run migrations during request handling.** `MigrateCheckCommand` triggered by Froiden\Envato on controller construct is forbidden on hot paths; deployment-time check only. | [10_current_state.md §C7](../future_resume_upload_2.0/10_current_state.md), [ADR-007](../future_resume_upload_2.0/99_decision_log.md) |
| ❌ **Create candidate before upload finalized.** Submit returns 422 `upload_incomplete` if `session.status != 'Uploaded'`. Candidate insert + session linked_candidate_id happen atomically. | [INV-3](../future_resume_upload_2.0/96_invariants.md), [50_api_contracts.md §EP-3 finalize](../future_resume_upload_2.0/50_api_contracts.md) |

## Three sharpenings from your draft

| # | Original | Sharpened to | Reason |
|---|---|---|---|
| 1 | ❌ "Replay submit automatically" | ❌ **Replay submit without server verification** | The hard rule is *server must verify*, not *browser must never retry*. The latter is too strict — network resilience demands browser auto-retry. ([INV-3 was already sharpened](../future_resume_upload_2.0/96_invariants.md)) |
| 2 | ❌ "Trust localStorage" | ❌ **Trust localStorage for tenant identity / auth** | localStorage stores session UUID + idem key — that's fine. Banning localStorage entirely would block our whole refresh-recovery flow. ([55_tenancy_and_security.md](../future_resume_upload_2.0/55_tenancy_and_security.md)) |
| 3 | (none supplied) | ➕ **PHP Restart → Retry** + **Redis Restart → Retry** | Two real failure modes didn't have Recovery rows; added with explicit handling. |

## Why this matrix is enforceable

| Row | Physical enforcement |
|---|---|
| Recovery: Wi-Fi lost | JS `online`/`offline` events + preflight ping |
| Recovery: R2 timeout | Client backoff + idempotency-key UUID reuse |
| Recovery: Parser 429 | `ResumeParserService::parse()` honors `Retry-After` |
| Recovery: Browser Refresh | GET endpoint exists in API spec |
| Recovery: Session expired | Lifecycle: `Created → Expired` |
| Recovery: PHP restart | Octane stateless + HPA |
| Recovery: Redis restart | `defaults => fail open` |
| Recovery: Deployment | Reverb reconnects; R2 presigned URLs persist |
| NEVER: Upload through PHP | ADR-001 R2 presigned PUT only |
| NEVER: Replay submit blindly | INV-3 server-side verification |
| NEVER: Overwrite manual fields | INV-2 `data-parser-populated` flag |
| NEVER: Trust localStorage for auth | ADR-003 server `Auth::user()->company_id` |
| NEVER: `navigator.onLine == Internet` | Preflight ping |
| NEVER: Couple parser with upload | Separate queue, separate pod |
| NEVER: Migrations on hot paths | ADR-007 deployment-time only |
| NEVER: Create candidate too early | EP-3 422 `upload_incomplete` |

## Cross-reference

This file is the LIST equivalent of:

- [`45_failure_modes.md`](45_failure_modes.md) — 20 failure modes in detail
- [`85_scalability_redteam.md`](85_scalability_redteam.md) — 20 component-failure Q&As
- [`96_invariants.md`](96_invariants.md) — 5 kept-true invariants
- [`99_decision_log.md`](99_decision_log.md) — 18 ADRs
