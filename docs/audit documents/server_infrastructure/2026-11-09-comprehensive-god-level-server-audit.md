# Comprehensive Server Audit: Forensic Hardware, Database, Elasticsearch & Runtime Analysis

## Metadata
- **Date:** 2026-11-09
- **Module:** `server_infrastructure`
- **Topic:** Forensic God-Level Infrastructure Audit & Concurrency Optimization
- **Author:** Antigravity AI God-Level Coding Agent
- **Target Host:** `76.13.243.68` (Ubuntu 24.04.4 LTS / AMD EPYC 9354P)
- **Status:** Complete Telemetry Verification

---

## Executive Summary
A forensic audit was performed across all infrastructure layers of host `76.13.243.68:25168` utilizing the aaPanel API, MySQL status counters, Elasticsearch JVM telemetry, and Linux OS metrics.

While the server is stable with **22 days of uninterrupted uptime**, we discovered severe resource bottlenecks:
1. **CPU & Swap Saturation:** 5-minute Load Average is **5.15 on 2 vCPUs** (257% capacity), with **2.73 GB of active Swap thrashing** and **78.2% `iowait`**.
2. **Severely Under-Allocated Database & Search Tiers:** MySQL InnoDB Buffer Pool is set to the default **128 MB** (86% full), and Elasticsearch JVM heap is constrained to only **512 MB** while holding 126 active primary shards.
3. **HTTP Cron Anti-Pattern:** Scheduled system tasks execute via public web requests (`curl -s https://testing.jero.works/...`) instead of CLI commands.
4. **Architectural Gap:** All services (Nginx, FrankenPHP, MySQL, Elasticsearch, Redis, and other websites) are co-located on a single 8 GB RAM / 2 vCPU virtual machine, contrasting with JERO's target 3-tier 32 GB RAM specification.

---

## Live Telemetry Matrix

| Metric / Layer | Measured Value | Standard Target | Status / Risk |
| :--- | :--- | :--- | :---: |
| **CPU Architecture** | AMD EPYC 9354P (2 vCPUs assigned) | Dedicated Multi-Core | ⚠️ Bottleneck |
| **Load Average (1m / 5m / 15m)** | `2.57` / `5.15` / `3.93` (Limit: 4.0) | < 1.50 per 2 cores | 🛑 **CRITICAL (Overloaded)** |
| **Physical RAM Total** | 7,940 MB (~8 GB) | 32 GB per Tier | ⚠️ Co-located |
| **Physical RAM Free** | 139 MB (2.3GB Real Used, 5.4GB Cache) | > 1.5 GB Headroom | ⚠️ Saturated |
| **Swap Memory Usage** | **2,735 MB used** out of 4,096 MB | 0 MB (No Swapping) | 🛑 **CRITICAL (Thrashing)** |
| **Disk I/O Wait (`iowait`)** | **78.2%** | < 5% | 🛑 **CRITICAL (Disk Starved)** |
| **SSD Storage (`/dev/sda1`)** | 96 GB Total (65 GB Used, 32 GB Free) | 200 GB+ NVMe | 67% (Monitor growth) |
| **MySQL Buffer Pool Size** | **128 MB (8,192 pages)** | 4 GB (8GB host) / 24 GB (Prod) | 🛑 **CRITICAL (Bottleneck)** |
| **MySQL Buffer Pool Free** | 1,151 pages (86% consumed) | Dynamic with large pool | ⚠️ Near Exhaustion |
| **MySQL Max Connections** | **151** (Peak used: 26) | 1,000 - 2,000 | ⚠️ Concurrency Risk |
| **Elasticsearch JVM Heap** | **512 MB (heap_max: 536MB)** | 2 GB (8GB host) / 16 GB (Prod) | 🛑 **CRITICAL (OOM Risk)** |
| **Elasticsearch Shards** | 126 Primary, 10 Unassigned (Status: Yellow) | Green (0 unassigned) | ⚠️ Degraded Health |
| **Redis Cache / Horizon** | Redis 8.2.5 (0 queue backlog) | Dragonfly / Redis | ✅ Healthy |
| **Uptime** | 22 Days, 16 Hours | 99.99% | ✅ Stable Baseline |

---

## Deep Architectural Analysis

### 1. The Swap Thrashing & `iowait` Crisis
The live OS metrics explain why the server load is elevated:
- Total RAM is 8 GB.
- Linux cache consumed 5.4 GB, real processes consumed 2.3 GB, leaving only 139 MB free.
- The Linux kernel responded by swapping **2.73 GB of memory pages** to the SSD (`/dev/sda1`).
- Because MySQL's buffer pool is only 128 MB, every transactional query causes a cache miss that reads from disk. Concurrently, Elasticsearch with 126 shards constantly reads/writes to `/dev/sda1`.
- The SSD cannot handle simultaneous swap swapping + MySQL reads + Elasticsearch Lucene indexing, causing `iowait` to surge to **78.2%**.

### 2. Elasticsearch Memory Starvation
- 126 primary shards are being served on a **512 MB JVM heap**.
- Standard Lucene rule-of-thumb allocates ~30 MB to 50 MB of heap per active shard.
- Running 126 shards on 512 MB means JVM garbage collection (G1 GC) is running continuously (1,406 GC cycles already recorded), adding CPU load.

### 3. HTTP Cron Job Vulnerability
In `crontab?action=GetCrontab`, six critical automation tasks run via:
```bash
curl -s "https://testing.jero.works/<action>?key=SECRET_KEY"
```
- **Disadvantages:**
  1. Requests hit the Nginx reverse proxy and web worker queue, competing with active recruiters.
  2. Bounded by 60s/120s HTTP gateway timeouts; bulk email jobs or heavy expirations fail silently.
  3. Secret keys in URLs can leak in web server access logs and browser histories.

---

## Actionable God-Level Optimization Plan

### Phase 1: Immediate Triage (Same 8 GB Host — 15 Minutes)

1. **Reduce Linux Swappiness:**
   Prevent the kernel from aggressively writing active memory to disk:
   ```bash
   sysctl -w vm.swappiness=10
   echo "vm.swappiness=10" >> /etc/sysctl.conf
   ```

2. **Rebalance MySQL in aaPanel:**
   - In aaPanel $\rightarrow$ App Store $\rightarrow$ MySQL $\rightarrow$ Performance Tuning:
     - `innodb_buffer_pool_size` = `2048M` (2 GB)
     - `max_connections` = `1000`
     - `innodb_log_file_size` = `256M`
     - `thread_cache_size` = `64`

3. **Rebalance Elasticsearch Heap & Shards:**
   - In Docker / `/etc/elasticsearch/jvm.options`:
     - Set `-Xms1536m` and `-Xmx1536m` (1.5 GB Heap).
   - Run setting update to clear unassigned shards:
     ```bash
     curl -X PUT "http://76.13.243.68:9200/*/_settings" -H 'Content-Type: application/json' -d'{"index":{"number_of_replicas":0}}' -u elastic:<REDACTED_ES_AUTH>
     ```
   - Cluster health will immediately transition from **Yellow to Green**.

4. **Migrate Cron Jobs to Native Laravel CLI:**
   Replace HTTP `curl` commands in aaPanel with native artisan runs:
   ```bash
   * * * * * php /www/wwwroot/testing.jero.works/artisan schedule:run >> /dev/null 2>&1
   ```

---

### Phase 2: Production Scaling (Achieving the 10,000 Concurrency Target)
To sustain 10,000 concurrent recruiters without server collapse per Rule 3 and Directive 3:
- Split the monolithic host into the **dedicated 3-tier architecture**:
  1. **Application Server (32 GB RAM):** FrankenPHP Octane worker pool + Redis/Horizon.
  2. **Database Server (32 GB RAM):** MySQL with 24 GB InnoDB Buffer Pool (pure write store).
  3. **Elasticsearch Cluster (32 GB RAM):** 16 GB JVM Heap + 16 GB OS Page Cache (pure read store).
