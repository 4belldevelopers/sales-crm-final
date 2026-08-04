# 10 — Hardware Verdict: 3-Server Setup

> **Intent:** Exact RAM accounting for each server against real load numbers.
> Every number is derived from [`00_capacity_math.md`](00_capacity_math.md).

---

## The Setup Being Analysed

```
Server A: 32 GB RAM — App server (Octane + Horizon + Reverb)
Server B: 32 GB RAM — MySQL 8 primary
Server C: 32 GB RAM — Elasticsearch
```

---

## Server A — App Server (32 GB)

### What runs here and how much RAM it needs

| Process | RAM | Basis |
|---|---|---|
| OS + kernel | 1.5 GB | Non-negotiable |
| FrankenPHP master | 0.2 GB | Single coordinator process |
| Reverb WebSocket server | 2.5 GB | 10,000 connections × ~200 KB state/conn |
| Horizon daemon | 0.1 GB | Lightweight supervisor |
| Horizon `bulkelasticindex` (4 workers) | 1.0 GB | 4 × 256 MB |
| Horizon `reconcile_orphans` (1 worker) | 0.25 GB | |
| System headroom (GC, burst) | 0.5 GB | |
| **Available for Octane workers** | **~26 GB** | |

> ⚠️ **If Redis runs on this server, subtract 4 GB → only 22 GB for Octane workers.**

### Octane Worker Count — the critical calculation

```
RAM per Octane worker TODAY (god controllers loaded):
  HomeController alone: ~16K LOC × average PHP object overhead
  + all service providers + dependency graph
  = 400–512 MB per worker

RAM per Octane worker AFTER god controller extraction (V2 Steps 7+16):
  Thin controller: ~200 LOC
  + only its own service deps
  = 128–200 MB per worker
```

| Scenario | RAM/worker | Workers in 26 GB | Max concurrent PHP |
|---|---|---|---|
| **Today (god controllers)** | 512 MB | **50 workers** | **50** |
| **After V2 extraction** | 200 MB | **130 workers** | **130** |
| **After V2 + JIT opcache** | 160 MB | **162 workers** | **162** |

### Can Server A handle the targets?

| Load | Concurrent PHP needed | Today (50 workers) | After extraction (130+ workers) |
|---|---|---|---|
| 1M uploads/day average | 48 | ✅ | ✅ |
| 10K concurrent average | 67 | ⚠️ Tight | ✅ |
| 10K concurrent burst | 200 | ❌ Queue backs up → 502s | ✅ |
| Worst-case spike (all submit) | 600 | ❌ Crash | ⚠️ Rate-limited + queued |

### Server A verdict

```
TODAY:     ❌ Handles 1M/day average, FAILS at 10K concurrent burst
AFTER V2:  ✅ Handles all targets comfortably
CONDITION: Redis MUST be on a separate server (see gap analysis below)
```

---

## Server B — MySQL (32 GB)

### RAM allocation

| Component | RAM | Notes |
|---|---|---|
| OS + kernel | 1.5 GB | |
| MySQL process | 1.0 GB | |
| **InnoDB buffer pool** | **27 GB** | Set `innodb_buffer_pool_size=27G` in `my.cnf` |
| OS file cache headroom | 2.5 GB | Filesystem cache for binlog + temp files |

### Does the hot dataset fit in 27 GB?

```
resume_uploads (1M rows): ~500 MB
candidates / job_applications (5M rows combined): ~3 GB
upload_session_audits (1M rows/day, hot 7 days): ~1.4 GB
All critical indexes: ~5 GB
Total hot dataset: ~10 GB

Buffer pool: 27 GB
Surplus: 17 GB (entire hot dataset fits in RAM with 17 GB to spare)
```

### Write throughput

```
Peak writes: 105/s (candidate inserts + session updates combined)
MySQL capability on modern hardware: 10,000–50,000 writes/s
Utilization: 105 / 10,000 = 1.05% — completely trivial
```

### Connection ceiling — the one real risk

```
Default MySQL max_connections: 151
Octane workers (Server A): 130 workers
Each worker holds 1 persistent MySQL connection (Octane keeps them alive)

130 connections > 151 limit? NO — but add headroom:
REQUIRED: max_connections = 300 (set in my.cnf)
```

### Server B verdict

```
✅ MySQL 32 GB is your STRONGEST server for this workload
✅ Entire hot dataset fits in RAM — near-zero disk I/O for hot queries
✅ Write throughput: 1% utilized at peak
✅ Handles read replicas' load too if you add one later
⚠️ ONLY action needed: set max_connections=300 in my.cnf (5-minute change)
```

---

## Server C — Elasticsearch (32 GB)

### RAM allocation — the 50% heap rule

```
ES heap:     16 GB  (EXACTLY 50% of RAM — never exceed 31 GB)
OS file cache: 14 GB (Lucene segment cache — ES uses this heavily)
OS overhead:   2 GB

WHY 50% rule: JVM uses pointer compression below 32 GB heap.
              Above 32 GB: 64-bit pointers = 40% more GC pressure = slower search.
              Set -Xms16g -Xmx16g in jvm.options — hard limits, both equal.
```

### How long does 32 GB ES handle 1M/day?

```
Average candidate document size: ~5 KB (fields + inverted indexes)
Lucene segment overhead: ~2× raw data = ~10 KB effective per document

1M candidates/day × 10 KB = 10 GB/day written to disk
OS file cache (14 GB) caches the hottest ~1.4M documents (1.4 days)
After day 2: older documents go to disk — search latency increases for old docs

Action: ILM (Index Lifecycle Management) — move docs >30 days to warm tier
```

### Index throughput

```
Peak ES index ops: 105/s
ES bulk indexing capability: 10,000–50,000 docs/s per node
Utilization: 105 / 10,000 = 1.05% — completely trivial

Method: bulkelasticindex Horizon queue batches 1,000 docs per 5s
= 200 ops/s max throughput from Horizon
= Well within ES capacity
```

### Server C verdict

```
✅ ES 32 GB handles 10K concurrent searches comfortably
✅ Index throughput: 1% utilized at peak
✅ Full hot dataset in OS cache for first 1–2 days
⚠️ After 3–6 months at 1M/day: needs ILM warm tier (S3) or second ES node
⚠️ Set: -Xms16g -Xmx16g (both equal) — many installs default to 1g heap
```

---

## The Missing Server — Redis

**Redis is not in your 3-server setup. It must live somewhere.**

### Option A — Redis on Server A (wrong)

```
App server loses 4 GB to Redis
Available for Octane workers: 26 GB → 22 GB
Workers today: 50 → 43
Workers after extraction: 130 → 110

Redis restart → shared impact on Octane process table
Redis OOM → rate limiter and session both fail simultaneously
```

### Option B — Redis on its own server (correct)

```
8 GB VPS: ~$30–50/month depending on provider
Redis uses: 1 GB for 1M idempotency keys + rate limit counters + sessions
Available headroom: 7 GB — ample

App Server keeps full 26 GB for Octane workers
Redis restart has zero impact on Octane
Redis OOM affects only rate limiting (fails open, documented) — not sessions
```

**Add the 4th server. It is the cheapest infrastructure decision you will ever make.**

---

## Complete Hardware Verdict

| Server | 1M/day | 10K burst | Issue | Fix |
|---|---|---|---|---|
| **App 32 GB (today)** | ✅ | ❌ | 50 Octane workers, god controllers | Extract controllers (V2 Steps 7+16) |
| **App 32 GB (after V2)** | ✅ | ✅ | Redis must be off this server | Add 4th server |
| **MySQL 32 GB** | ✅ | ✅ | Default max_connections=151 too low | Set to 300 in my.cnf |
| **ES 32 GB** | ✅ | ✅ | Heap not configured (defaults to 1g) | Set -Xms16g -Xmx16g |
| **Redis (missing)** | ❌ | ❌ | Does not exist | Add 8 GB server ($30–50/mo) |
