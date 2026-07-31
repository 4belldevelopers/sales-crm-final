# 50 — Action Plan

> **Intent:** Prioritized, effort-estimated roadmap. Ordered by ROI.
> Do not reorder — each step enables the next.

---

## Priority Legend

| Symbol | Meaning |
|---|---|
| 🔴 P0 | Must do before 10K launch. System will crash without this. |
| 🟠 P1 | Should do before 10K launch. Significant reliability risk without. |
| 🟡 P2 | Do within 30 days of launch. Performance and resilience. |
| 🟢 P3 | Do within 90 days. Future-proofing for growth. |

---

## The Ordered Plan

### 🔴 P0-1 — Add Redis Server (Server D, 8 GB)
**Effort:** 1 day (provision + configure + update env vars)
**Blocks:** Everything else. Redis on the app server eats 4 GB → only 43 Octane workers.

```bash
# Steps:
# 1. Provision 8 GB VPS (any cloud provider)
# 2. Install Redis 7.x
# 3. Apply config from 40_server_configuration.md §Server D
# 4. Update .env:
REDIS_HOST=<new_redis_server_ip>
REDIS_PASSWORD=<strong_password>
# 5. php artisan config:cache
# 6. php artisan horizon:restart
# 7. Verify: redis-cli -h <new_ip> ping → PONG
```

---

### 🔴 P0-2 — Extract God Controllers (V2 Steps 7 + 16)
**Effort:** 2–3 weeks (development + staging tests)
**Blocks:** 10K concurrent handling. Without this: 50 Octane workers max → burst 502s.

This is already fully specified in the V2 implementation plan:
- Step 7 → `v2.0.0e` → Extract `store()` from `ServiceCompanyJobApplicationController`
- Step 16 → `v2.0.0m` → Extract `careerStore()` from `HomeController`

See [`20_god_controller_problem.md`](20_god_controller_problem.md) for the
exact pattern and rollback strategy.

**Measure success:**
```bash
# Check RSS (resident set size) of an Octane worker after extraction:
ps aux | grep octane | grep -v grep | awk '{print $6}' | head -1
# Before: ~460,000 KB (460 MB)
# After:  ~160,000 KB (160 MB) ← target
```

---

### 🔴 P0-3 — Set MySQL `max_connections = 300`
**Effort:** 5 minutes
**Blocks:** Server A has 130 Octane workers. Default MySQL max_connections = 151.
130 > 151 means MySQL rejects connections during load.

```bash
# On Server B (MySQL):
echo '[mysqld]
max_connections = 300
thread_cache_size = 100' > /etc/mysql/conf.d/jero-connections.cnf

systemctl reload mysql

# Verify:
mysql -u root -e "SHOW VARIABLES LIKE 'max_connections';"
# Expected: 300
```

---

### 🔴 P0-4 — Set Elasticsearch JVM Heap to 16g
**Effort:** 10 minutes + ES restart
**Blocks:** Default ES heap on many installs is 1g. At 10K concurrent searches,
1g heap causes GC pauses → search latency spikes to 5–10 seconds.

```bash
# On Server C (Elasticsearch):
echo '-Xms16g
-Xmx16g' > /etc/elasticsearch/jvm.options.d/jero-heap.options

systemctl restart elasticsearch

# Verify:
curl localhost:9200/_nodes/stats/jvm | python3 -m json.tool | grep heap_max
# Expected: "heap_max_in_bytes": 17179869184 (16 GB)
```

---

### 🟠 P1-1 — Ship V2 Upload Pipeline (Steps 1–7)
**Effort:** 3–4 weeks (already planned in detail)
**Why now:** V1 routes all file bytes through PHP. 1M/day × 6 MB = 5.7 TB/day
through PHP memory. This will OOM the app server long before 1M/day is reached.

V2 moves bytes to browser→R2 direct. PHP sees only ~7 KB/s of metadata.
This is the single biggest scale improvement in the codebase.

Refer to [`../future_resume_upload_2.0/00_STEPS_IMPLEMENTATION_PLAN.md`](../future_resume_upload_2.0/00_STEPS_IMPLEMENTATION_PLAN.md)
— Steps 1 through 7. Each step has a feature flag, ready-to-paste code,
and a staging test.

---

### 🟠 P1-2 — Set InnoDB Buffer Pool + Octane max_requests
**Effort:** 30 minutes
**Why now:** Without buffer pool config, MySQL defaults to 128 MB buffer pool.
At 1M/day, the hot dataset (~10 GB) constantly misses cache → disk I/O spike.

```bash
# Server B — apply full my.cnf from 40_server_configuration.md
# Server A — add to octane.php:
'max_requests' => 500,
```

---

### 🟠 P1-3 — Cloudflare WAF Rate Limiting Rules
**Effort:** 1 hour (Cloudflare dashboard)
**Why now:** Defense-in-depth when Redis is temporarily down (fail-open window).
Without WAF rules, a Redis restart creates an unthrottled window for abuse.

Configure rules from [`30_architecture_layers.md §Layer 1`](30_architecture_layers.md).

---

### 🟡 P2-1 — Enable JIT OPcache
**Effort:** 5 minutes + PHP restart
**Gain:** 15–30% throughput on hot paths (form validation, JSON serialization).

Apply `99-octane.ini` from [`40_server_configuration.md`](40_server_configuration.md).

---

### 🟡 P2-2 — Separate Redis Logical Databases
**Effort:** 1 day (config + testing)
**Why:** Independent blast radius per concern (queue vs cache vs rate limit vs session).

Apply the 4-database config from `40_server_configuration.md §Server D`.

---

### 🟡 P2-3 — ElasticSearch ILM (Index Lifecycle Management)
**Effort:** 1 day
**Why:** At 1M/day, without ILM the hot ES index grows to 300M docs in 30 days.
Search latency degrades from 50ms to 2000ms+ as the OS cache can no longer
hold the full hot segment set.

Apply ILM policy from [`40_server_configuration.md §Server C`](40_server_configuration.md).

---

### 🟡 P2-4 — V2 BroadcastChannel Tab Sync (Step 14)
**Effort:** 1 sprint
**Why:** Cuts Reverb load by 60% (same-browser tabs use local IPC, zero server).
Frees 1.5 GB on Server A for more Octane workers.

Already specified in V2 Step 14 + ADR-018.

---

### 🟢 P3-1 — MySQL Read Replica
**Effort:** 1 day
**When:** When daily writes exceed 500K AND read-heavy queries (candidate search,
session status polling) start showing latency >50ms on the primary.

```
Add: 1× replica server (same spec as Server B, or smaller)
Cost: ~$50–100/month
Gain: Primary handles only writes; replica handles all GET /resume-sessions/{uuid}
```

---

### 🟢 P3-2 — `upload_session_audits` Monthly Partitioning
**Effort:** 1 hour
**When:** Table exceeds 50M rows (at 1M/day: ~50 days)

Apply partition DDL from [`40_server_configuration.md §Server B`](40_server_configuration.md).

---

### 🟢 P3-3 — V3 Cloudflare Worker Presign (ADR-017)
**Effort:** 2–3 weeks (new Cloudflare Worker + TypeScript + secret rotation)
**When:** Traffic exceeds 30K concurrent users consistently.

Moves presign signing from Octane to Cloudflare edge. PHP sees only the submit
POST. Octane drops from 8 pods to 4 pods at 30K concurrent.

Not needed before 30K concurrent. See ADR-017 for full trade-off record.

---

## Summary Gantt

```
Week 1:   P0-1 (Redis server) + P0-3 (MySQL connections) + P0-4 (ES heap)
Week 2-4: P0-2 (God controller extraction) ← CRITICAL PATH
Month 2:  P1-1 (V2 upload pipeline Steps 1-7)
Month 2:  P1-2 (buffer pool + max_requests) + P1-3 (Cloudflare WAF)
Month 3:  P2-1 (JIT) + P2-3 (ES ILM) + P2-4 (BroadcastChannel)
Month 4+: P3 items as traffic grows
```

---

## Cost Summary

| Item | Monthly cost | One-time effort |
|---|---|---|
| Redis Server D (8 GB VPS) | $30–50 | 1 day setup |
| MySQL read replica (later) | $50–100 | 1 day setup |
| Cloudflare WAF rules | $0 (included) | 1 hour config |
| ES ILM cold tier (S3) | $5–20 | 1 day config |
| **Total additional cost** | **$85–170/month** | **~3–4 weeks dev** |

This handles 10K concurrent + 1M/day reliably, with headroom for 30K+ concurrent
after V2.5 ships.
