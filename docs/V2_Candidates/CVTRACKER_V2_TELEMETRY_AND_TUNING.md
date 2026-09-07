# CV Tracker V2 Telemetry & Server Tuning Guide

## Executive Summary
This document provides production telemetry guidelines and server tuning configurations for the **CV Tracker V2** module to maintain sub-100ms response times and 0% error rates under 10,000+ concurrent recruiter traffic on Ubuntu 24 / aaPanel 8.0.3 stack (8 CPU Cores, 31.34 GB RAM).

---

## 1. Production Server Tuning Configurations

### PHP-FPM Configuration (`/etc/php/8.x/fpm/pool.d/www.conf`)
```ini
[www]
pm = dynamic
pm.max_children = 120
pm.start_servers = 30
pm.min_spare_servers = 20
pm.max_spare_servers = 60
pm.max_requests = 1000
request_terminate_timeout = 30s
```

### OPcache Configuration (`/etc/php/8.x/fpm/conf.d/10-opcache.ini`)
```ini
opcache.enable = 1
opcache.enable_cli = 1
opcache.memory_consumption = 512
opcache.interned_strings_buffer = 64
opcache.max_accelerated_files = 30000
opcache.validate_timestamps = 0
opcache.save_comments = 1
```

### MySQL 8.0 Configuration (`/etc/mysql/mysql.conf.d/mysqld.cnf`)
```ini
[mysqld]
innodb_buffer_pool_size = 20G
innodb_buffer_pool_instances = 8
innodb_log_file_size = 2G
innodb_flush_log_at_trx_commit = 2
innodb_read_io_threads = 8
innodb_write_io_threads = 8
max_connections = 1000
thread_cache_size = 100
```

### Redis / DragonflyDB Configuration (`/etc/redis/redis.conf`)
```ini
maxmemory 4gb
maxmemory-policy allkeys-lru
tcp-backlog 511
timeout 0
tcp-keepalive 300
```

---

## 2. Telemetry Monitoring Thresholds

| Metric | Target / Threshold | Alert Trigger | Action |
| :--- | :---: | :---: | :--- |
| **API Latency (p95)** | < 100ms | > 150ms for 2 mins | Check Redis cache hit ratio & ES queue size |
| **API Latency (p99)** | < 200ms | > 300ms for 2 mins | Check PHP-FPM worker pool saturation |
| **HTTP Error Rate** | 0.00% | > 0.1% | Inspect Nginx & Laravel error logs |
| **MySQL Read Queries** | 0 queries / request | > 1 query / request | Audit Blade template N+1 fallbacks |
| **Redis Cache Hit Ratio** | > 95% | < 80% | Verify Redis memory eviction policy & key TTLs |
| **ES Search Latency** | < 15ms | > 30ms | Check ES thread pool queue & node memory |

---

## 3. Load Test Verification Command

Run the k6 load test script:
```bash
k6 run --env TARGET_URL=http://localhost/git/JWMaincode28June2026 tests/Feature/cvtracker_v2_load_test.js
```
