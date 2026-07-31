# 40 — Server Configuration

> **Intent:** The exact configuration values for every server.
> Copy-paste ready. Every value has a comment explaining why.

---

## Server A — App Server (Octane + Horizon + Reverb)

### `/etc/php/8.3/cli/conf.d/99-octane.ini`

```ini
; Octane production tuning for JERO on FrankenPHP
; Intent: maximize worker throughput on 32 GB server

; OPcache — code cache
opcache.enable                = 1
opcache.enable_cli            = 1
opcache.jit                   = 1255      ; tracing JIT — best for web workloads
opcache.jit_buffer_size       = 256M      ; JIT native code buffer
opcache.memory_consumption    = 512       ; 512 MB for PHP bytecode cache
opcache.max_accelerated_files = 20000     ; covers all vendor + app files
opcache.validate_timestamps   = 0         ; NEVER re-stat files in production
opcache.revalidate_freq       = 0         ; paired with validate_timestamps=0
opcache.huge_code_pages       = 1         ; Linux huge pages for JIT

; Limits
memory_limit     = 256M      ; per worker max — god controller safe after extraction
max_execution_time = 30      ; requests > 30s are bugs, not features
```

### `config/octane.php` key values

```php
return [
    'server'       => 'frankenphp',
    'max_requests' => 500,        // Recycle worker after 500 req — prevents slow leaks
    'workers'      => null,       // null = auto (CPU cores × multiplier)
                                  // On 4-core server: ~16 workers
                                  // Override with env OCTANE_WORKERS if needed
    'warm'         => [
        // Pre-warm frequently used services to avoid cold-start on first request
        \App\Services\ResumeUploadSession::class,
        \App\Services\ElasticIndexService::class,
    ],
    'flush'        => [
        // Clear per-request state to prevent memory leaks across requests
        \Illuminate\Http\Request::class,
    ],
];
```

### `config/horizon.php` key sections

```php
return [
    'memory_limit' => 256,   // MB — Horizon supervisor max memory before restart

    'environments' => [
        'production' => [
            'supervisor-bulkelasticindex' => [
                'connection' => 'redis',
                'queue'      => ['bulkelasticindex'],
                'balance'    => 'auto',
                'processes'  => 4,
                'tries'      => 5,
                'timeout'    => 30,
                'memory'     => 256,
                'backoff'    => [10, 30, 60, 120, 300],
            ],
            'supervisor-reconcile' => [
                'connection' => 'redis',
                'queue'      => ['reconcile_orphans'],
                'processes'  => 1,
                'tries'      => 3,
                'timeout'    => 120,
                'memory'     => 256,
            ],
            'supervisor-high' => [
                'connection' => 'redis',
                'queue'      => ['high', 'default'],
                'processes'  => 4,
                'tries'      => 3,
                'timeout'    => 60,
                'memory'     => 256,
            ],
        ],
    ],
];
```

---

## Server B — MySQL (32 GB)

### `/etc/mysql/conf.d/jero-production.cnf`

```ini
[mysqld]

# ── Memory ──────────────────────────────────────────────────────────────────
# 85% of 32 GB = 27.2 GB for InnoDB buffer pool
# Caches: indexes + data pages + undo pages
# At 10 GB estimated hot dataset, 27 GB gives 17 GB of headroom
innodb_buffer_pool_size      = 27G
innodb_buffer_pool_instances = 8        # 1 instance per ~3 GB — reduces mutex contention

# ── Connections ──────────────────────────────────────────────────────────────
# 130 Octane workers + 50 headroom (Horizon, admin, cron)
max_connections              = 300
thread_cache_size            = 100      # Reuse threads instead of spawn

# ── Write Performance ────────────────────────────────────────────────────────
innodb_flush_log_at_trx_commit = 2     # Flush to OS every second, not every tx
                                        # Risk: up to 1 second of data on crash
                                        # Acceptable for ATS — not financial
sync_binlog                  = 0        # OS handles binlog flush
                                        # Risk: binlog may lag by 1 OS write cycle
innodb_log_file_size         = 1G       # Large redo log = fewer checkpoints
innodb_log_buffer_size       = 256M     # Buffer large transactions
innodb_thread_concurrency    = 64       # Match CPU cores × 2

# ── Read Performance ─────────────────────────────────────────────────────────
# Buffer pool is primary read cache. Additional:
innodb_read_ahead_threshold  = 56       # Sequential read-ahead
join_buffer_size             = 4M       # For joins without indexes (avoid with good indexes)
sort_buffer_size             = 4M       # Per-thread sort buffer

# ── Slow Query Logging ───────────────────────────────────────────────────────
slow_query_log               = 1
slow_query_log_file          = /var/log/mysql/slow.log
long_query_time              = 0.5      # Log queries > 500ms
log_queries_not_using_indexes = 0       # Off in production (noisy)

# ── Character Set ────────────────────────────────────────────────────────────
character_set_server         = utf8mb4
collation_server             = utf8mb4_unicode_ci
```

### Critical DDL to run after config

```sql
-- 1. Fix connection ceiling (run immediately)
SET GLOBAL max_connections = 300;

-- 2. Audit table monthly partitioning
-- Run ONCE. Prevents 365M-row table after 1 year at 1M/day.
ALTER TABLE upload_session_audits
  PARTITION BY RANGE (YEAR(created_at) * 100 + MONTH(created_at)) (
    PARTITION p202607 VALUES LESS THAN (202608),
    PARTITION p202608 VALUES LESS THAN (202609),
    PARTITION p202609 VALUES LESS THAN (202610),
    PARTITION p202610 VALUES LESS THAN (202611),
    PARTITION p202611 VALUES LESS THAN (202612),
    PARTITION p202612 VALUES LESS THAN (202613),
    PARTITION p202701 VALUES LESS THAN (202702),
    PARTITION pmax    VALUES LESS THAN MAXVALUE
  );

-- 3. Add covering index for session status lookup (if not already in V2 migrations)
ALTER TABLE resume_uploads
  ADD INDEX idx_session_status_lookup (uuid, company_id, status, updated_at);

-- Verify indexes:
SHOW INDEX FROM resume_uploads;
-- Expected: idx_company_idem, resume_uploads_uuid_company_unique,
--           resume_uploads_status_expires_at_index, idx_session_status_lookup
```

---

## Server C — Elasticsearch (32 GB)

### `/etc/elasticsearch/jvm.options.d/jero-heap.options`

```
# CRITICAL: Both must be equal. Never exceed 31g.
# Above 31g: JVM disables compressed OOPs → 40% GC overhead → slow search.
-Xms16g
-Xmx16g
```

### `/etc/elasticsearch/elasticsearch.yml`

```yaml
# ── Cluster ────────────────────────────────────────────────────────────────
cluster.name: jero-production
node.name: jero-es-node-1

# ── Memory ─────────────────────────────────────────────────────────────────
# The OS file cache (remaining 14 GB after JVM heap) is used by Lucene
# to cache index segments. This is critical — do NOT disable swap entirely
# but lock the JVM heap:
bootstrap.memory_lock: true         # Lock JVM heap in RAM — prevent swapping

# ── Indexing ───────────────────────────────────────────────────────────────
indices.memory.index_buffer_size: 20%    # 20% of JVM heap = 3.2 GB indexing buffer
indices.queries.cache.size: 10%          # 10% of JVM heap = 1.6 GB query cache
thread_pool.write.queue_size: 1000       # Buffer 1000 write ops before rejecting with 429

# ── Recovery ───────────────────────────────────────────────────────────────
cluster.routing.allocation.disk.threshold_enabled: true
cluster.routing.allocation.disk.watermark.low: 70%     # Warn at 70% disk
cluster.routing.allocation.disk.watermark.high: 80%    # Stop indexing at 80%
cluster.routing.allocation.disk.watermark.flood_stage: 90%

# ── Tenant Isolation ───────────────────────────────────────────────────────
# Index naming convention: candidates_tenant_{company_id}
# Alias: candidates_search → routes to all tenant indices
# Never cross-tenant search by default — query must specify company_id filter
```

### ILM Policy (run once via Kibana or curl)

```bash
curl -X PUT "localhost:9200/_ilm/policy/jero_candidates_policy" \
  -H 'Content-Type: application/json' -d '{
  "policy": {
    "phases": {
      "hot": {
        "min_age": "0ms",
        "actions": {
          "rollover": { "max_docs": 5000000, "max_size": "20gb" }
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
          "searchable_snapshot": { "snapshot_repository": "s3_cold_repo" }
        }
      },
      "delete": {
        "min_age": "365d",
        "actions": { "delete": {} }
      }
    }
  }
}'
```

---

## Server D — Redis (8 GB) ← New Server

### `/etc/redis/redis.conf`

```conf
# ── Memory ──────────────────────────────────────────────────────────────────
maxmemory 6gb                      # Leave 2 GB for OS overhead
maxmemory-policy allkeys-lru       # Evict LRU keys when full — rate limit counters
                                    # are short-lived anyway

# ── Persistence ─────────────────────────────────────────────────────────────
save ""                            # Disable RDB snapshots — Redis is ephemeral cache
appendonly yes                     # Enable AOF for Horizon job queue durability
appendfsync everysec               # Sync AOF every 1 second — max 1s data loss on crash
auto-aof-rewrite-percentage 100
auto-aof-rewrite-min-size 64mb

# ── Connections ──────────────────────────────────────────────────────────────
maxclients 1000                    # 130 Octane workers + 4 Horizon supervisors + headroom
tcp-keepalive 300                  # Detect dead connections

# ── Performance ──────────────────────────────────────────────────────────────
hz 20                              # Background task frequency (default 10)
lazyfree-lazy-eviction yes         # Async key eviction — don't block event loop
lazyfree-lazy-expire yes           # Async TTL expiry
```

### Logical database separation (in `config/database.php`)

```php
'redis' => [
    'client' => 'phpredis',
    'default' => [
        'host'     => env('REDIS_HOST'),
        'password' => env('REDIS_PASSWORD'),
        'port'     => 6379,
        'database' => 0,  // Horizon job queue
    ],
    'cache' => [
        'host'     => env('REDIS_HOST'),
        'password' => env('REDIS_PASSWORD'),
        'port'     => 6379,
        'database' => 1,  // Application cache
    ],
    'session' => [
        'host'     => env('REDIS_HOST'),
        'password' => env('REDIS_PASSWORD'),
        'port'     => 6379,
        'database' => 2,  // Session store
    ],
    'ratelimit' => [
        'host'     => env('REDIS_HOST'),
        'password' => env('REDIS_PASSWORD'),
        'port'     => 6379,
        'database' => 3,  // Rate limiting counters
    ],
],
```

**Why separate databases:** If `database 3` (rate limiting) has an OOM-driven
eviction spike, it does not evict `database 0` (Horizon jobs). Independent
blast radius per concern.
