# 75 — Capacity plan

> *Intent: place real numbers on pod counts, Redis size, R2 budget, and*
> *backpressure thresholds. These are **design targets**, not measured.*

## Load projection

10,000 concurrent users uploading resumes. We further assume:

- 8-second median upload time → ~1,200 files started/sec at peak
- 50–200 ms median browser-side parse time (deterministic pdf.js) →
  no server parser queue
- 30-second median candidate submit → 333 concurrent submits/sec

These are upper-bound. Empirical medians will run ~5s upload on a
healthy stack; the browser-side parser finishes during the same window.

## Pod count

| Component | Min pods | Burst pods | Notes |
|---|---|---|---|
| Octane (API) | 8 (4 CPU, 8 GB each) | 16 (HPA at 70% CPU) | each handles ~625 concurrent |
| Reverb | 4 | 8 | reads-heavy, scale by conn count |
| ~~Horizon (`parse_resumes`)~~ | **REMOVED 2026-07-27** | — | no server parser; no parser pods |
| Horizon (`bulkelasticindex`) | 2 | 4 | batch 1,000 per 5 s |
| Horizon (`reconcile_orphans`) | 1 | 1 | cron-only |
| Horizon (`repair_stuck`) | 1 | 1 | cron-only; parser-half gone |
| MySQL (primary) | 1 | 1 + replicas | hot-write; writes only INSERT/UPDATE on resume_uploads |
| MySQL (read replica) | 2 | 4 | handles GET /resume-sessions/{uuid} |
| Redis | 1 master + 2 replicas | n/a | counters + idempotency keys |
| Elasticsearch | 2 (data + master) | 4 | index-per-tenant alias |
| R2 | managed | managed | scale-r-out via Cloudflare |
| S3 | managed | managed | Standard class |

Total pod count (peak): ~32 (was ~39 in v2.0 with parser pods; REMOVED 2026-07-27).

## What fails first under load

| Component | Failure shape | v2 mitigation |
|---|---|---|
| Octane ingest | `too many open files` → 502s | HPA + larger `ulimit` on pods |
| Redis CPU | counter `INCRBY` latency spikes | Redis Enterprise HA; tiered scan |
| Reverb fan-out | slow consumer → backpressure | per-tenant cap of 5 conns |
| ~~`parse_resumes`~~ | REMOVED 2026-07-27 — no server parser queue | n/a |
| ES write throughput | bulk_reject[429] gracefully | already in j1; ratelimit per-tenant |
| R2 PUT bandwidth | client-side backoff: `?` | client retries; partial-PUT resumption |
| Network egress | Cloudflare/region saturation | already multi-region; pick closest |

## Queue sizing math

> **Removed 2026-07-27** — there is no `parse_resumes` queue and no
> parser pod budget to size. The parser is browser-side
> (`pdf.js` + `re_test()`), runs in 50–200 ms locally, and never
> leaves the browser. There is no parser-side backpressure: only
> Octane ingest + R2 PUT + ES bulk-index + DB row-inserts.

Rate-limiting at presign (30/min/user, 120/min/company) is still in
force and protects against burst on the upload subsystem.

## Backpressure thresholds (Health endpoint levels)

| Component | `ok` | `degraded` | `down` |
|---|---|---|---|
| MySQL | latency < 50 ms | 50-150 ms | > 150 ms |
| Redis | latency < 5 ms | 5-15 ms | > 15 ms |
| R2 | latency < 200 ms | 200-800 ms | > 800 ms or 5xx |
| ~~parse_resumes depth~~ | REMOVED 2026-07-27 — no `parse_resumes` queue | n/a |
| bulkelasticindex depth | < 100 | 100-1000 | > 1000 |
| orphan_uploads_count | < 50 | 50-500 | > 500 |
| stuck_sessions_count | < 10 | 10-50 | > 50 |

`ok` status: load balancers serve.
`degraded`: LB serves; alert fires.
`down`: LB sheds from rotation; on-call page.

## Network bandwidth

Peak ingress at Octane = ~10 KB / request × 333 / s = ~3.3 MB / sec. Trivial.

Peak browser→R2 egress = 6 MB × 1200 / s = 7.2 GB / sec peak —
**this is the biggest cost**. Plan: cache R2 region mapping; GeoDNS
routes user to closest R2 region (3 regions globally).

## Cost sketch (US East, peak)

| Resource | Monthly estimate (peak) |
|---|---|
| Pod-hours | ~50k USD / hr × peak (skip for sanity) |
| R2 storage | $2 / TB / month × ~100 GB = $0.20 |
| R2 egress | $0.09 / GB × 7.2 GB / sec × 10% uptime = $1,800 / mo |
| MySQL | $1000 / mo (single primary, plus 2 read replicas) |
| Redis | $400 / mo (1+2 cluster) |
| Elasticsearch | $800 / mo (2 hot + 1 cold) |
| Reverb | bundled with Laravel Cloud |

Approximate total: $5K-$10K/mo peak.

## When the load changes

| Change | Threshold | Action |
|---|---|---|
| 5K concurrent | maintain | ops watches |
| 10K concurrent | plan above | automated HPA + oncall the team |
| 20K+ | v3: pre-warm pods, R2 bilocation | review next quarter |

## Acceptance criteria

1. `tests/Load/10k_concurrent.php` runs 10K concurrent clients
   issuing presign → PUT → finalize → submit flow. 0% expected
   server-side 5xx over 5 minutes.
2. `php artisan test --testsuite=ResumeV2` green.
3. Health endpoint polling for 24 hours: 100% `ok`/`degraded`.
4. No manual intervention; chaos harness + tests together show
   ≥99% recovery.
