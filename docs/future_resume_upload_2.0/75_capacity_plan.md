# 75 — Capacity plan

> *Intent: place real numbers on pod counts, Redis size, R2 budget, and*
> *backpressure thresholds. These are **design targets**, not measured.*

## Load projection

10,000 concurrent users uploading resumes. We further assume:

- 8-second median upload time → ~1,200 files started/sec at peak
- 30-second median parse time → 16 concurrent parser slots per pod
- 30-second median candidate submit → 333 concurrent submits/sec

These are upper-bound. Empirical medians will run ~5s upload / 8s
parse on a healthy stack.

## Pod count

| Component | Min pods | Burst pods | Notes |
|---|---|---|---|
| Octane (API) | 8 (4 CPU, 8 GB each) | 16 (HPA at 70% CPU) | each handles ~625 concurrent |
| Reverb | 4 | 8 | reads-heavy, scale by conn count |
| Horizon (`parse_resumes`) | 3 | 6 | 8 concurrent per pod, 24/sec total |
| Horizon (`bulkelasticindex`) | 2 | 4 | batch 1,000 per 5 s |
| Horizon (`reconcile_orphans`) | 1 | 1 | cron-only |
| Horizon (`repair_stuck`) | 1 | 1 | cron-only |
| MySQL (primary) | 1 | 1 + replicas | hot-write; writes only INSERT/UPDATE on resume_uploads |
| MySQL (read replica) | 2 | 4 | handles GET /resume-sessions/{uuid} |
| Redis | 1 master + 2 replicas | n/a | counters + idempotency keys |
| Elasticsearch | 2 (data + master) | 4 | index-per-tenant alias |
| R2 | managed | managed | scale-r-out via Cloudflare |
| S3 | managed | managed | Standard class |

Total pod count (peak): ~39.

## What fails first under load

| Component | Failure shape | v2 mitigation |
|---|---|---|
| Octane ingest | `too many open files` → 502s | HPA + larger `ulimit` on pods |
| Redis CPU | counter `INCRBY` latency spikes | Redis Enterprise HA; tiered scan |
| Reverb fan-out | slow consumer → backpressure | per-tenant cap of 5 conns |
| `parse_resumes` | queue depth grows | 6 burst parsers + admission control |
| ES write throughput | bulk_reject[429] gracefully | already in j1; ratelimit per-tenant |
| R2 PUT bandwidth | client-side backoff: `?` | client retries; partial-PUT resumption |
| Network egress | Cloudflare/region saturation | already multi-region; pick closest |

## Queue sizing math

```
peak_throughput_subs = 333/s
peak_throughput_pars = 1200/s
parse_pod_conc = 8
parse_pod_count = 3
parse_throughput_sustained = 3 * 8 / 30 = 0.8/s  # ! wrong: concurrency model
parse_throughput_correct = 8 parallel per pod
                    = 8 / 30 sec per parse
                    = 0.27 parses/sec per pod
parse_throughput_pod_count = 24 / 30 = 0.8 parses/sec, 3 pods
                            actually
```

Correction:

Each pod can process 8 parses in parallel. With 30-second parse
time: 8 parses / 30 s = 0.27 parses/sec per pod. With 3 pods:
0.8 parses/sec sustained.

Queue depth = 1,200 files/min = 20/s. So queue depth grows until
parse throughput catches up; under burst, depth could reach 30,000.

We respond with rate-limiting at presign: 30/min/user, 120/min/company
spreads the queue growth. Burst absorbed on Redis rate-limiter side.

## Backpressure thresholds (Health endpoint levels)

| Component | `ok` | `degraded` | `down` |
|---|---|---|---|
| MySQL | latency < 50 ms | 50-150 ms | > 150 ms |
| Redis | latency < 5 ms | 5-15 ms | > 15 ms |
| R2 | latency < 200 ms | 200-800 ms | > 800 ms or 5xx |
| parse_resumes depth | < 100 | 100-1000 | > 1000 |
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
