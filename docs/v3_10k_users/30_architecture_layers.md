# 30 — Architecture Layers

> **Intent:** The 8-layer GOD-level architecture for 10K concurrent / 1M daily.
> Each layer is independent. Each has a specific job. Each has a failure mode
> and a degraded-not-dead fallback.

---

## Architecture Diagram

```
                          INTERNET
                              │
                    ┌─────────▼─────────┐
                    │   Cloudflare CDN  │  Layer 1
                    │   WAF + Rate Limit│
                    │   GeoDNS → R2     │
                    └─────────┬─────────┘
                              │
              ┌───────────────┼───────────────┐
              │               │               │
    ┌─────────▼────┐  ┌───────▼──────┐  ┌────▼────────┐
    │ Static Assets│  │  API Requests │  │ File Uploads│
    │ (cached, CDN)│  │ (Octane/PHP)  │  │ (browser→R2)│
    └─────────────┘  └───────┬───────┘  └─────────────┘
                              │                 Layer 2
                    ┌─────────▼─────────┐
                    │  Server A (32 GB) │
                    │  Octane Workers   │  Layer 2
                    │  Horizon Workers  │
                    │  Reverb (WS)      │
                    └──┬────────────┬───┘
                       │            │
           ┌───────────▼──┐   ┌─────▼──────────┐
           │ Server B     │   │ Server D (8 GB)│
           │ MySQL 32 GB  │   │ Redis          │  Layer 4
           │              │   │                │
           └──────────────┘   └────────────────┘
                Layer 3
           ┌───────────────┐
           │ Server C      │
           │ ES 32 GB      │  Layer 5
           └───────────────┘
```

---

## Layer 1 — Cloudflare (CDN / WAF / R2)

**Job:** Absorb everything that does not need PHP.

### What Cloudflare handles before PHP sees a request

| Traffic type | Cloudflare handles | PHP involvement |
|---|---|---|
| Static assets (JS, CSS, images) | CDN cache hit | **Zero** |
| Bot / DDoS traffic | WAF block | **Zero** |
| Rate-limit violations (>30/min/user) | WAF 429 | **Zero** |
| File upload bytes (browser PUT to R2) | R2 direct | **Zero** |
| Job listing page (cacheable) | s-maxage=60 cache | **Zero after first request** |

### Configuration

```
# Cloudflare WAF Custom Rule — presign rate limiting
# (Defense-in-depth even when Redis is down)
Rule: http.request.uri.path contains "/resume-sessions"
      AND http.request.method eq "POST"
Rate: 30 requests per minute per IP
Action: Block with 429, Retry-After: 60
```

```
# Cache rule for job listing pages
# 1K daily job updates = cache invalidated every ~86 seconds on average
Cache-Control: public, s-maxage=60, stale-while-revalidate=120
```

**Failure mode:** If Cloudflare has an outage (rare, <0.01% historically),
traffic hits origin directly. PHP rate limiting via Redis still works.
Defense in depth — both layers protect independently.

---

## Layer 2 — Octane / FrankenPHP (Server A, 32 GB)

**Job:** Handle all PHP API requests (presign, submit, session status, cancel).

### Worker allocation after god controller extraction

```
Total available RAM:    26 GB (after Reverb, Horizon, OS)
RAM per Octane worker:  200 MB (thin controllers)
Max workers:            130

Concurrent PHP at 10K average: 67  → 48% workers busy
Concurrent PHP at 10K burst:   200 → all 130 workers busy + 70 queued
Queued requests timeout (FrankenPHP): 30s — burst resolves in <5s
```

### Key Octane config

```php
// octane.php
return [
    'server'       => 'frankenphp',
    'max_requests' => 500,     // Recycle worker every 500 req — prevents memory drift
    'workers'      => null,    // Auto = CPU cores × 4; override if RAM limits first
];
```

```ini
; php.ini for production Octane
opcache.enable=1
opcache.jit=1255
opcache.jit_buffer_size=256M
opcache.memory_consumption=512
opcache.validate_timestamps=0
opcache.max_accelerated_files=20000
```

**Failure mode:** Pod OOM → FrankenPHP restarts the worker automatically.
In-flight requests on that worker get a 502. Client retries with exponential
backoff (already in V2 browser JS). Recovery: <5 seconds per worker.

---

## Layer 3 — MySQL (Server B, 32 GB)

**Job:** Single source of truth for all state (resume sessions, candidates, jobs).

### Configuration

```ini
# my.cnf — production settings for 32 GB server
[mysqld]
innodb_buffer_pool_size     = 27G      # 85% of RAM
innodb_buffer_pool_instances = 8       # 1 per GB of buffer pool (avoid lock)
max_connections             = 300      # Covers 130 Octane workers + 50 headroom
thread_cache_size           = 100      # Avoid thread spawn overhead
innodb_thread_concurrency   = 64       # Match CPU core count × 2
innodb_flush_log_at_trx_commit = 2    # Slightly relaxed durability — ok for ATS
sync_binlog                 = 0        # Max write throughput (risk: 1 binlog tx on crash)
innodb_log_file_size        = 1G       # Large redo log — reduces checkpoint frequency
slow_query_log              = 1
slow_query_log_file         = /var/log/mysql/slow.log
long_query_time             = 0.5      # Log queries > 500ms
```

### The 3 indexes that matter most

```sql
-- Already in V2 plan:
ALTER TABLE resume_uploads ADD INDEX idx_company_idem (company_id, idempotency_key);
ALTER TABLE resume_uploads ADD UNIQUE INDEX resume_uploads_uuid_company_unique (uuid, company_id);
ALTER TABLE resume_uploads ADD INDEX resume_uploads_status_expires_at_index (status, expires_at);

-- Add for 1M/day scale — session status lookup covering index:
ALTER TABLE resume_uploads ADD INDEX idx_session_status_lookup (uuid, company_id, status, updated_at);

-- Audit table — monthly partition for 1M rows/day:
-- See 40_server_configuration.md for full partition DDL
```

**Failure mode:** MySQL primary OOM or crash → read replicas serve reads.
Writes fail with 503. Client retries submit up to 3×. Recovery: MySQL restart <30s,
or replica promotion <5 min (failover plan required before going to 10K).

---

## Layer 4 — Redis (Server D, 8 GB)

**Job:** Rate limiting counters, idempotency key TTL cache, Horizon job queue.

### Why Redis is a separate server

```
Shared blast radius table:
  Redis on Server A → Redis OOM → rate limiter + Octane compete for RAM
  Redis as Server D → Redis OOM → rate limiter fails open (documented)
                                   Octane unaffected
                                   Sessions unaffected (MySQL is truth)
```

### Configuration

```bash
# redis.conf for 8 GB server
maxmemory 6gb                       # Leave 2 GB for OS
maxmemory-policy allkeys-lru        # Evict least-recently-used on OOM
save ""                             # Disable RDB snapshots (not needed for counters)
appendonly yes                      # AOF for Horizon job durability
appendfsync everysec                # Balance: 1 second max data loss on crash

# Separate logical databases by concern:
# DB 0: Horizon job queue
# DB 1: Rate limiting counters
# DB 2: Application cache
# DB 3: Session + idempotency keys
```

### TTL on every key (non-negotiable)

```php
// Every key written MUST have a TTL:
Redis::set("resume_idem:{$companyId}:{$key}", $uuid, 'EX', 1800);  // 30 min
Redis::incr("resume_rate:{$userId}:{$minute}");
Redis::expire("resume_rate:{$userId}:{$minute}", 120);

// Without TTL: 1M uploads/day × 30 days = 30M orphan keys → Redis OOM
```

**Failure mode:** Redis OOM or crash → rate limiter fails open (Cloudflare WAF
still throttles as Layer 1 backup). Horizon jobs may be lost for the crash window
(AOF limits to 1-second data loss). Sessions survive (MySQL is truth).
Recovery: Redis restart + AOF replay <30 seconds.

---

## Layer 5 — Elasticsearch (Server C, 32 GB)

**Job:** Full-text search for candidates and jobs. Read-heavy, write-async.

### Configuration

```yaml
# jvm.options
-Xms16g   # EXACTLY 16g — both equal, no swap between min/max
-Xmx16g   # EXACTLY 16g — pointer compression works below 32g

# elasticsearch.yml
indices.memory.index_buffer_size: 20%     # 20% of heap for indexing buffer
indices.queries.cache.size: 10%           # 10% of heap for query cache
thread_pool.write.queue_size: 1000        # Buffer write ops before rejecting
```

### Index Lifecycle Management (ILM) — required at 1M/day

```json
PUT _ilm/policy/candidates_policy
{
  "policy": {
    "phases": {
      "hot": {
        "min_age": "0ms",
        "actions": {
          "rollover": { "max_docs": 5000000 }
        }
      },
      "warm": {
        "min_age": "30d",
        "actions": {
          "shrink": { "number_of_shards": 1 },
          "forcemerge": { "max_num_segments": 1 },
          "readonly": {}
        }
      },
      "cold": {
        "min_age": "90d",
        "actions": {
          "searchable_snapshot": { "snapshot_repository": "s3_repo" }
        }
      }
    }
  }
}
```

Without ILM: at 1M/day, the hot index grows to 300M docs in 30 days →
OS file cache misses → search latency spikes from 50ms to 2000ms+.

**Failure mode:** ES cluster down → candidate search returns 500.
New candidates saved to MySQL (DB is truth). `bulkelasticindex` queue
retries with exponential backoff (up to 5 attempts). Candidates become
searchable once ES recovers. **Submit never blocks on ES.**

---

## Layer 6 — Horizon Queue Workers

**Job:** Async work — ES indexing, orphan cleanup, dead-letter handling.

### Queue architecture

```php
// config/horizon.php
'environments' => [
    'production' => [
        // ES indexing — high throughput, fault-tolerant
        'supervisor-bulkelasticindex' => [
            'queue'     => ['bulkelasticindex'],
            'processes' => 4,
            'tries'     => 5,
            'backoff'   => [10, 30, 60, 120, 300],
            'timeout'   => 30,
            'memory'    => 256,
        ],
        // Orphan cleanup — cron-driven, low priority
        'supervisor-reconcile' => [
            'queue'     => ['reconcile_orphans'],
            'processes' => 1,
            'tries'     => 3,
            'timeout'   => 120,
        ],
        // General high-priority work
        'supervisor-high' => [
            'queue'     => ['high', 'default'],
            'processes' => 4,
            'tries'     => 3,
        ],
    ],
]
```

### Dead-letter queue (DLQ) overflow

```
At 1M/day with 0.1% failure rate = 1,000 failed jobs/day
DLQ cap: 10,000 jobs
After cap: overflow written to S3 flat file (triage-20260731.json)
Alert fires when DLQ depth > 500
```

---

## Layer 7 — Reverb (WebSocket)

**Job:** Real-time progress events for upload status per tenant.

### Connection budget at 10K users

```
10,000 concurrent users × 1 WebSocket each = 10,000 connections
Reverb on Server A: handles 10,000 connections at ~200 KB/conn = 2 GB RAM ✅

V2.5 with BroadcastChannel (ADR-018):
  Same-browser tabs use BroadcastChannel (zero server cost)
  95% of multi-tab traffic = zero Reverb cost
  Effective Reverb connections: 10,000 × 5% cross-device = 500 Reverb conns
  RAM for Reverb drops from 2 GB → 100 MB
```

### Security rule (non-negotiable)

```php
// Reverb channel auth — ALWAYS:
Broadcast::channel('resume.{companyId}', function ($user, $companyId) {
    return (int) $user->company_id === (int) $companyId;  // integer cast CRITICAL
});
// Without int cast: "123abc" == 123 returns true in PHP loose comparison
```

---

## Layer 8 — The Operating Contract

**Partial degradation, never total failure.**

| Component fails | System does | NOT: |
|---|---|---|
| Redis OOM | Rate limit fails open; Cloudflare WAF backstops | Return 500 to all users |
| ES down | Candidates save to MySQL; search unavailable | Block candidate submit |
| Reverb down | Progress bar stale; refresh recovers from DB | Block upload |
| MySQL replica lag | Read from primary (fallback) | Return 500 |
| R2 slow | Client retries 3× with backoff; session stays Created | Immediate error |
| Octane worker OOM | Worker restarts; 1 request gets 502; client retries | Cascade 502 storm |
| Cloudflare outage | Traffic hits origin; PHP rate limiting still active | None — defense in depth |
