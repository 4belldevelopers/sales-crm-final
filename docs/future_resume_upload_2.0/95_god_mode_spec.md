# 95 — GOD-mode: minimize server load further

> *Intent: how far we can push the resume upload flow toward zero PHP round-trips*
> *per recruiter. Names the tiers (good / great / GOD), what each costs, and*
> *what stays server-bound. Companion pieces: ADR-017 + ADR-018.*

This file extends [00_overview.md](00_overview.md) and
[30_architecture.md](30_architecture.md). The v2 plan already minimises
PHP-byte load (zero bytes through PHP). v2.5 / v3 push metadata round-trips
down and broadcast-peer channels for same-machine tabs.

## Tier ladder

| Tier | Server round-trips per candidate | Server HTTP load (10K recruiters) | Notes |
|---|---|---|---|
| **v1** | 4 + finalize polling | ~5 RPS/recruiter | bytes + metadata through PHP |
| **v2.0** (we have this) | 4 + Reverb WS | ~3 RPS + 5 broadcast events | bytes **out** of PHP; metadata stays |
| **v2.5** (this spec, cheap) | **2 + zero cross-tab WS** | ~2 RPS + same-machine: 0 | merges `finalize`+`submit`; same-origin uses BroadcastChannel |
| **v3** (GOD) | **1 + zero server fan-out** | ~1 RPS + zero | presign moves to Cloudflare Worker |

## Tier v2.5 — Two round-trips per candidate

### The two requests

| # | Endpoint | What it commits |
|---|---|---|
| 1 | `POST /service_company/api/resume-sessions` | presigned URL + session row |
| 2 | `POST /service_company/api/job-applications` | candidate row + session→linked (atomic) — REMOVED 2026-07-27: no parse dispatch; parsing happens client-side |

### Why this is safe

- IDEMPOTENCY (per-request random UUID via `crypto.randomUUID()`,
  HTTP `Idempotency-Key` header, **INDEX** — NOT UNIQUE — on
  `(company_id, idempotency_key)`; see ADR-002, ADR-009, INV-4)
  handles transport-retry replay; double-session race at the
  upload-subsystem level is handled by the canonical state-machine
  row transitions, not by a UNIQUE constraint.
- `finalize` no longer exists as a separate request — submit does it
  inside one transaction.
- Session moves `Created` → `Uploaded` (HEAD-verified) → `Completed`
  (linked candidate) inside the submit transaction.
  (REMOVED 2026-07-27: `Parsed` is gone — terminal post-link is `Completed`.)
- Server `HEAD` R2 inside submit: replaces what was the `finalize` HEAD.

### What we cut

- `finalize` endpoint → removed
- Reverb for same-browser same-origin tab fan-out → `BroadcastChannel`
- One OPTIONS preflight by collapsing XHR/fetch (PUT direct, GET cacheable)

## Tier v3 — One round-trip per candidate

### The one request

| # | Endpoint | Notes |
|---|---|---|
| 1 | `POST /service_company/api/job-applications` (already merges with `session_uuid`) | only the candidate write |

### Presign moves out

**Before v3:** browser → Octane → R2 presign.
**At v3:** browser → Cloudflare Worker → R2 presign.

The Worker holds the R2 API token (HMAC scoped to bucket prefix
`temp/{year}/{month}/${tenant_id}/`). It only signs URLs. It never
sees bytes. It never logs user PII — only `tenant_id` + `httprid`.

Octane **only sees the submit POST**. That's the single stretch of
the entire user flow where PHP has to coordinate DB + ES + audit.

### What we cut

- presign HTTP round-trip
- presign rate-limit bottleneck at Octane
- presign pod count needed (drop to 0)
- presign CPU on `hash_hmac`/`openssl_sign` cycles

### What we pay

- One more piece of infrastructure to operate (Cloudflare Worker +
  KV-bounded API tokens)
- Two places where presign secret rotation runs (Worker + Laravel)
- Harder on-call story (Worker is TypeScript, not PHP)

This is **not** worth doing in v2. Wait until v2.5 traffic numbers
are real and we can measure the saving. ADR-017 captures why.

## Tier rationale — by recruiter

| Recruiter profile | v2.5 rtt | v3 rtt |
|---|---|---|
| Recruiter, 1 tab, healthy network | 2 | 1 |
| Recruiter, 3 tabs, same browser | 2 | 1 |
| Same recruiter on phone + desktop | 2 + WS | 1 + WS |
| Public career applicant | 1 (careerStore stays v1 form, status polled at 1 Hz — REMOVED 2026-07-27: parser is browser-side, no server polling needed) | 1 |
| Mobile, anonymous | 2 (polling) | 1 |

(Note: mobile / anonymous still goes through Octane v1 path with multipart
fallback — [ADR-011](99_decision_log.md). The "1 rtt" target is internal-tenant.)

## Same-machine tab sync — `BroadcastChannel` (v2.5)

```js
// resume-broadcast-sync.js  (~1 KB)
const chan = new BroadcastChannel('resume-sync-' + window.JERO.companyId);

chan.onmessage = (e) => {
  const data = JSON.parse(e.data);
  if (data.uuid && window.localResumeUUID === data.uuid) {
    applyProgressToUI(data);
  }
};

// On every state-change in the orchestrator:
function emitLocal(data) {
  chan.postMessage(JSON.stringify(data));  // zero server bytes
}
```

| Property | Value |
|---|---|
| Cross-tab latency | <5 ms |
| Bytes per fan-out | ~250 B JSON, zero network |
| Server load contribution | **0** for same-origin tabs |
| Limits | Single browser process only |

Used for: upload progress, badge state, cancel/refresh events. (REMOVED 2026-07-27: "parser progress" — no server parser.)

## Cross-machine / cross-device sync — Reverb (still needed)

`BroadcastChannel` only helps when tabs share a browser process. Two
recruiters on two laptops is still a job for Reverb.

For v2.5:
- `BroadcastChannel` covers ~95% of multi-tab scenarios (recruiter
  opens two tabs).
- Reverb covers the ~5% (recruiter on phone + laptop at the same time).
- The 95% reduction in Reverb fan-out load drops the Reverb pod budget.

## What fails at v2.5 (still worth shipping)

| Failure | Behavior |
|---|---|
| Cloudflare Worker down (v3) | Fall back to Octane presign (legacy) |
| BroadcastChannel not supported (Safari < 15.4) | Falls back to localStorage `storage` event |
| HEAD r2 inside submit hangs | Server timeout 1.5s, submit returns 422 `upload_unknown` |

## Bytes / operations budget — 10K recruiters

### v1 (broken)

```
bytes through PHP: 60 GB/s peak
HTTP req/s on Octane: 50,000
realistic: collapses at ~500 concurrent
```

### v2.0 (we have this)

```
bytes through PHP: 0 (R2 PUT direct)
HTTP req/s on Octane: ~6,000 (3 across presign/finalize/submit + heartbeat)
Reverb connections: ~10,000 (one per tab)
realistic: holds 10,000
```

### v2.5 (this spec — cheap version)

```
bytes through PHP: 0
HTTP req/s on Octane: ~2,000
Reverb connections: ~500 (only cross-device; rare)
realistic: holds 30,000+ comfortably
```

### v3 (GOD version — Cloudflare Worker presign)

```
bytes through PHP: 0
HTTP req/s on Octane: ~100 (submit only; preflights cut to 0)
Reverb connections: ~500
Octane pods required at peak: ~5 instead of 8
realistic: holds 100,000+
```

## Implementation path

| Step | v2.5 brings | v3 brings |
|---|---|---|
| Move 1 — Merge `finalize`+`submit` into one transaction | ✓ | ✓ |
| Move 2 — `BroadcastChannel` for same-origin tabs | ✓ | ✓ |
| Move 3 — `If-Match` / `If-None-Match` header on R2 PUT | ✓ | ✓ |
| Move 4 — Cloudflare Worker for presign (token-bound to bucket prefix) | — | ✓ |
| Move 5 — Octane presign path removed once v3 ships | — | ✓ |

The first three moves live in v2.5, ship in a single sprint, no infra
debt. Move 4 happens at v3 — needs separate infra, no rush.

## Honest assessment

**v2.5 is the right place to STOP** before doing v3.

Reasons:
1. v2.5 already hits the two hard goals: **0 bytes through PHP** and
   **server round-trips per recruiter halved**.
2. v3's Cloudflare Worker presign shifts an operational tail onto us.
   The saving is real but the busy-work is also real. Defer.
3. The 10K target was set in SCALABILITY.md; current v2 holds it. v3
   is for 100K+, which product hasn't asked for.
4. ADR-018 (`BroadcastChannel`) is a 1-KB JS module — cheap.
5. ADR-017 (Cloudflare Worker) is a multi-week infra effort. Better as
   a single, focused project than a sprint-burden.

## Failure budgets after GOD-level moves

| Budget | v2.0 | v2.5 | v3 |
|---|---|---|---|
| Server-side 5xx | <0.01% | <0.005% | <0.001% |
| Bytes through PHP | 0 | 0 | 0 |
| Round-trips per candidate | 4 | 2 | 1 |
| Reverb load | 100% | 5% | 5% |
| Octane pods needed | 8 | 5 | 4 |
| Worst-case single recruiter | 700 ms | 350 ms | 200 ms |
