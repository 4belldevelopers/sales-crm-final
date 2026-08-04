# 60 — SLOs, Monitoring and Alert Thresholds

> **Intent:** Define what "working perfectly" means numerically.
> If a number in this document is green, the system is healthy.
> If a number is red, on-call is paged — not users.

---

## Service Level Objectives (SLOs)

These are the commitments the JERO platform makes to its recruiters.

| SLO | Target | Measurement window |
|---|---|---|
| Upload success rate | ≥ 99.9% | Rolling 7 days |
| Candidate submit success rate | ≥ 99.95% | Rolling 7 days |
| Candidate search availability | ≥ 99.5% | Rolling 7 days |
| P50 upload latency (presign + R2 PUT) | < 2 seconds | Rolling 1 hour |
| P95 upload latency | < 5 seconds | Rolling 1 hour |
| P99 upload latency | < 10 seconds | Rolling 1 hour |
| P95 submit latency | < 500 ms | Rolling 1 hour |
| P95 search latency | < 300 ms | Rolling 1 hour |
| Recovery from single-component failure | < 60 seconds | Per incident |
| Recovery from full rollback (flag flip) | < 90 seconds | Per incident |

---

## Health Endpoint — The Canary

Every Octane pod exposes:

```
GET /api/internal/health/resume
Authorization: Bearer <RESUMES_HEALTH_TOKEN>
```

Response:
```json
{
  "status": "ok",          // "ok" | "degraded" | "down"
  "components": {
    "mysql": {
      "status": "ok",
      "latency_ms": 3,
      "connections_used": 87,
      "connections_max": 300
    },
    "redis": {
      "status": "ok",
      "latency_ms": 1,
      "memory_used_mb": 412,
      "memory_max_mb": 6144
    },
    "elasticsearch": {
      "status": "ok",
      "latency_ms": 12,
      "queue_depth_bulkelasticindex": 45
    },
    "r2": {
      "status": "ok",
      "latency_ms": 88
    },
    "horizon": {
      "status": "ok",
      "queue_depths": {
        "bulkelasticindex": 45,
        "reconcile_orphans": 0,
        "default": 12
      }
    },
    "sessions": {
      "orphan_count": 7,
      "stuck_count": 0,
      "expired_last_24h": 1203
    }
  },
  "checked_at": "2026-07-31T11:00:00Z"
}
```

**Load balancer polls this every 10 seconds.**
Pod returns `"status": "down"` → LB removes pod from rotation automatically.

---

## Per-Component Thresholds

### MySQL

| Metric | Green (ok) | Yellow (degraded) | Red (down) | Action |
|---|---|---|---|---|
| Query latency P95 | < 50 ms | 50–150 ms | > 150 ms | Check slow query log |
| Connections used | < 200 | 200–270 | > 270 | Check Octane worker count |
| InnoDB buffer pool hit ratio | > 99% | 95–99% | < 95% | Check innodb_buffer_pool_size |
| Replication lag (if replica) | < 1 s | 1–10 s | > 10 s | Check replica I/O thread |

```bash
# Check connection count:
mysql -u root -e "SHOW STATUS LIKE 'Threads_connected';"

# Check buffer pool hit ratio:
mysql -u root -e "
  SELECT (
    (SELECT variable_value FROM information_schema.global_status WHERE variable_name = 'Innodb_buffer_pool_read_requests') -
    (SELECT variable_value FROM information_schema.global_status WHERE variable_name = 'Innodb_buffer_pool_reads')
  ) / (SELECT variable_value FROM information_schema.global_status WHERE variable_name = 'Innodb_buffer_pool_read_requests')
  * 100 AS hit_ratio_pct;
"
# Target: > 99.0
```

---

### Redis

| Metric | Green | Yellow | Red | Action |
|---|---|---|---|---|
| Latency P95 | < 5 ms | 5–15 ms | > 15 ms | Check maxmemory + eviction |
| Memory used | < 4 GB | 4–5.5 GB | > 5.5 GB | Check for missing TTLs |
| Evicted keys/s | 0 | 1–100 | > 100 | Add TTLs or increase maxmemory |
| Connected clients | < 200 | 200–800 | > 800 | Check Octane worker connections |
| Keyspace hit ratio | > 95% | 90–95% | < 90% | Review cache strategy |

```bash
# Quick Redis health:
redis-cli INFO stats | grep -E 'instantaneous_ops_per_sec|evicted_keys|connected_clients'
redis-cli INFO memory | grep 'used_memory_human'
```

---

### Elasticsearch

| Metric | Green | Yellow | Red | Action |
|---|---|---|---|---|
| Search latency P95 | < 100 ms | 100–500 ms | > 500 ms | Check heap, ILM, shard count |
| Indexing latency P95 | < 50 ms | 50–200 ms | > 200 ms | Check thread_pool.write.queue_size |
| `bulkelasticindex` queue depth | < 100 | 100–1,000 | > 1,000 | Scale Horizon workers for this queue |
| JVM heap used | < 70% | 70–85% | > 85% | GC pressure — restart ES if > 90% |
| Disk used | < 70% | 70–80% | > 80% | ILM moving docs to warm/cold tier |
| Rejected bulk ops | 0 | 1–10/min | > 10/min | thread_pool.write.queue_size too small |

```bash
# ES cluster health:
curl -s localhost:9200/_cluster/health | python3 -m json.tool
# Expected: "status": "green"

# JVM heap:
curl -s localhost:9200/_nodes/stats/jvm | python3 -m json.tool | grep heap_used_percent
# Target: < 70%

# Queue depth:
curl -s localhost:9200/_cat/thread_pool/write?v
# Rejected column should be 0
```

---

### Octane / App Server

| Metric | Green | Yellow | Red | Action |
|---|---|---|---|---|
| HTTP 5xx rate | < 0.1% | 0.1–1% | > 1% | Check worker count, god controllers |
| P95 request latency | < 200 ms | 200–500 ms | > 500 ms | Check MySQL / Redis latency first |
| Worker RSS (RAM per process) | < 200 MB | 200–400 MB | > 400 MB | Check god controller extraction |
| Active worker count | < 80% of max | 80–95% | 95–100% | Workers exhausted — add pods |
| `max_requests` recycling rate | Normal | — | Workers recycling every minute | Memory leak in app code |

```bash
# Octane worker memory check:
ps aux | grep frankenphp | grep -v grep | awk '{sum += $6; count++} END {print "Avg RSS:", sum/count/1024, "MB"}'
# Target after god controller extraction: < 200 MB average

# HTTP error rate from Nginx/Cloudflare logs or your APM
```

---

### Horizon Queue

| Metric | Green | Yellow | Red | Action |
|---|---|---|---|---|
| `bulkelasticindex` depth | < 100 | 100–1,000 | > 1,000 | Add Horizon workers |
| `reconcile_orphans` depth | 0 | 1–10 | > 10 | Check cron schedule |
| Failed jobs (last 1h) | < 10 | 10–100 | > 100 | Check DLQ, check ES health |
| DLQ depth | < 50 | 50–500 | > 500 | Manual triage required |
| Horizon status | running | paused | stopped | `php artisan horizon:start` |

```bash
# Check Horizon status:
php artisan horizon:status

# Check queue depths via Horizon dashboard or:
php artisan tinker --execute="
  echo 'bulkelasticindex: ' . Queue::size('bulkelasticindex') . PHP_EOL;
  echo 'reconcile:        ' . Queue::size('reconcile_orphans') . PHP_EOL;
  echo 'failed:           ' . DB::table('failed_jobs')->count() . PHP_EOL;
"
```

---

## Alert Rules (Pseudocode — adapt to your APM)

```yaml
# Critical alerts — page on-call immediately
alerts:
  - name: MySQLConnectionsExhausted
    condition: mysql.connections_used > 270
    severity: critical
    action: page on-call

  - name: OctaneHighErrorRate
    condition: http.5xx_rate > 1% over 5 minutes
    severity: critical
    action: page on-call

  - name: ESSearchLatencySpike
    condition: elasticsearch.p95_latency > 500ms over 10 minutes
    severity: critical
    action: page on-call

  - name: HorizonQueueBacklog
    condition: horizon.queue_depth['bulkelasticindex'] > 1000
    severity: critical
    action: page on-call

  - name: RedisMemoruHigh
    condition: redis.memory_used > 5.5gb
    severity: critical
    action: page on-call

# Warning alerts — ticket during business hours
  - name: OctaneWorkerRSSHigh
    condition: octane.avg_worker_rss > 350mb
    severity: warning
    action: create ticket

  - name: ESHeapHigh
    condition: elasticsearch.jvm_heap_used_pct > 75
    severity: warning
    action: create ticket

  - name: MySQLSlowQueries
    condition: mysql.slow_queries_per_minute > 10
    severity: warning
    action: create ticket
```

---

## On-Call Runbook (Quick Reference)

| Symptom | First check | Likely cause | Fix |
|---|---|---|---|
| 502s on upload | Octane worker RSS | God controllers not extracted | V2 Steps 7+16 |
| Slow search | ES heap % | Heap not set to 16g | Set `-Xms16g -Xmx16g` |
| Login 500 | MySQL connections | max_connections=151 hit | Set to 300 |
| Rate limits not working | Redis latency | Redis on app server, competing | Move Redis to Server D |
| Candidates missing from search | Horizon queue depth | `bulkelasticindex` backed up | Add Horizon workers |
| Progress bar frozen | Reverb pod | Reverb restart | `php artisan reverb:start` |
| Upload fails with 403 | Session TTL | User took >30 min to upload | UI: re-pick file |

---

## "Never Crashes" Definition

The system is considered "never crashing" when ALL of these are true simultaneously:

1. ✅ HTTP 5xx rate < 0.1% (rolling 7 days)
2. ✅ Upload success rate ≥ 99.9% (rolling 7 days)
3. ✅ No component is in "down" state for > 60 seconds
4. ✅ Horizon has processed all jobs within 5 minutes of dispatch
5. ✅ MySQL buffer pool hit ratio > 99%
6. ✅ ES JVM heap < 70%
7. ✅ No on-call pages in the last 7 days

When all 7 are green: **the system is working perfectly.**
