# BASELINE METRICS REPORT — RECOMMEND INTERNAL REQUISITIONS
## Service Company → Recommend Internal Request (Bench Candidates)

> **Target URL:** `http://localhost/git/JWMaincode28June2026/service_company/bench-candidates/recommend_internal_req/6`  
> **Candidate ID:** `6` (*Bhupendra Char*, Company `2`, Status `2`, Skills: `Java , Python , React`)  
> **Environment Context:** JERO Multi-tenant B2B Hiring SaaS (Laravel 11.48.0, PHP 8.2.12, MySQL 8.0/MariaDB, Elasticsearch 8.12.2, DragonflyDB/Redis)  
> **Baseline Date:** October 12, 2026  

---

## 1. Baseline Performance Inventory

### 1.1 Initial Page Load: `recommend_internal_req/6`

| Metric | Cold Cache | Warm Cache | Measurement Methodology |
| :--- | :--- | :--- | :--- |
| **Duration (Wall-clock)** | `332.68 ms` | `6.05 ms` | PHP `microtime(true)` high-resolution timer |
| **SQL Queries** | `9 queries` | `3 queries` | Laravel `DB::getQueryLog()` |
| **Total SQL Execution Time** | `10.87 ms` | `3.45 ms` | Sum of query durations in log |
| **Elasticsearch Requests** | `3 requests` | `0 requests` | ES Client instrumented calls |
| **PHP Memory Usage (Delta)**| `6.0 MB` | `0.4 MB` | `memory_get_usage(true)` |
| **PHP Peak Memory Usage** | `48.0 MB` | `48.0 MB` | `memory_get_peak_usage(true)` |
| **Response Type** | Full Blade HTML | Full Blade HTML | `view('service_company.bench-candidates.recommend_internal_req')` |

#### Detailed SQL Query Execution Plan (Cold Cache):
1. `SELECT job_applications.*, bench_master_status.name, countries.country_name, state_list.state, city_list.city FROM job_applications JOIN bench_master_status ... WHERE job_applications.id = 6 LIMIT 1` (2.24 ms)
2. `SELECT * FROM job_applications WHERE id = 6 LIMIT 1` (1.93 ms) — *Duplicate fetch inside `canGgdFilterData()`*
3. `SELECT name FROM qualification_master WHERE id = 16 LIMIT 1` (0.60 ms)
4. `SELECT city_list.city FROM job_applications LEFT JOIN city_list ... WHERE id = 6` (1.18 ms)
5. `SELECT SUM(CASE WHEN type=1 THEN 1 ELSE 0 END) as views, SUM(CASE WHEN type=2 THEN 1 ELSE 0 END) as downloads FROM bench_count_view_download WHERE status=0 AND candidate_id=6 LIMIT 1` (0.75 ms)
6. `SELECT count(*) as aggregate FROM interview_schedules WHERE job_application_id = 6` (0.47 ms)
7. `SELECT * FROM job_applications WHERE id = 6 LIMIT 1` (0.91 ms) — *Duplicate fetch inside `benchjobAndRecomCount()`*
8. `SELECT name FROM qualification_master WHERE id = 16 LIMIT 1` (0.48 ms) — *Duplicate fetch*
9. `SELECT city_list.city FROM job_applications LEFT JOIN city_list ... WHERE id = 6` (0.56 ms) — *Duplicate fetch*

---

### 1.2 Secondary AJAX Data Endpoint: `all_internal_data_ajax`

| Metric | Cold Cache | Warm Cache | Measurement Methodology |
| :--- | :--- | :--- | :--- |
| **Duration (Wall-clock)** | `413.18 ms` | `9.15 ms` | Controller execution timer |
| **SQL Queries** | `11 queries` | `1 query` | Laravel `DB::getQueryLog()` |
| **Total SQL Execution Time** | `18.22 ms` | `1.45 ms` | Sum of query durations |
| **Elasticsearch Requests** | `1 query (search)` | `0 queries` | `local_jobs_elastic` search query |
| **Payload Size (Transferred)**| `37,752 bytes (36.87 KB)` | `37,752 bytes (36.87 KB)` | Server-rendered HTML string payload |
| **Candidate Count Returned** | `4 jobs` | `4 jobs` | Hits returned for candidate #6 |

#### Detailed SQL Query Execution Plan (Cold Cache):
1. `SELECT * FROM job_applications WHERE id = 6 LIMIT 1` (1.84 ms) — *Runs BEFORE cache check!*
2. `SELECT name FROM qualification_master WHERE id = 16 LIMIT 1` (0.90 ms)
3. `SELECT city_list.city FROM job_applications LEFT JOIN city_list ... WHERE id = 6` (1.12 ms)
4. `SELECT * FROM job_applications WHERE id = 6 LIMIT 1` (1.52 ms)
5. `SELECT permissions.* FROM permissions JOIN model_has_permissions ...` (0.72 ms)
6. `SELECT roles.* FROM roles JOIN model_has_roles ...` (0.68 ms)
7. `SELECT * FROM rec_candidate_assign WHERE source_application_id = 6 AND source_jobid IN (1, 6) ORDER BY id DESC` (2.26 ms)
8. `SELECT * FROM job_metronics_skills WHERE job_id IN (1, 2, 6, 7)` (4.37 ms) — *Avoidable: already in ES doc!*
9. `SELECT name, id FROM job_categories WHERE id IN (1, 2, 4)` (1.68 ms)
10. `SELECT company_name, id FROM companies WHERE id IN (2)` (1.53 ms)
11. `SELECT jobs.id, clients.name, companies.company_name FROM jobs LEFT JOIN clients ... LEFT JOIN companies ... WHERE jobs.id IN (1, 2, 6, 7)` (1.94 ms)

---

### 1.3 Primary Mutation Endpoint: `recommandedrequestcondidate`

| Metric | Cold Execution | Replay Execution (Duplicate) | Measurement Methodology |
| :--- | :--- | :--- | :--- |
| **Duration (Wall-clock)** | `791.76 ms` | `5.03 ms` | Controller execution timer |
| **SQL Queries** | `19 queries` | `2 queries` | Laravel `DB::getQueryLog()` |
| **Lock Strategy** | Redis `Cache::lock(10s)` | Hit in-flight or `alreadyAssigned` | In-memory atomic lock |
| **Outbox Coverage** | 0% for assignment (only Coin Deductions logged) | N/A | `sys_outbox_events` query inspection |
| **Idempotency Coverage**| 0% (no key table) | Returns warning message | `sys_idempotency_keys` table |
| **Status Response** | `{"status":"success","message":""}` | `{"status":"warning","message":"..."}` | JSON Response |

---

## 2. Unmeasured Baseline Metrics & Collection Specification

As required by God-Level Audit standards, the following metrics cannot currently be instrumented in this local CLI environment and are explicitly marked:

| Metric | Status | Collection Specification |
| :--- | :--- | :--- |
| **Browser CPU Utilization** | `Not measured` | Open Chrome DevTools > Performance Panel > Record 10s interaction with filters and sorting. |
| **Browser Memory (Heap Snapshot)** | `Not measured` | Take Heap Snapshot in Chrome DevTools > Memory before and after 20 pagination cycles. |
| **DOM Rendering Duration** | `Not measured` | Measure `performance.measure()` from AJAX `.success` callback to DOM paint completion. |
| **WebSocket Delivery Latency** | `Not measured` | Add Echo subscription and record `performance.now() - event.timestamp` in milliseconds. |
| **Network Payload on Compression (Gzip/Brotli)** | `Not measured` | Measure in FrankenPHP/Nginx production gateway with gzip compression enabled. |
| **Elasticsearch JVM Heap Pressure** | `Not measured` | Query `GET /_nodes/stats/jvm` under concurrent load test. |
| **MySQL Thread Pool & Lock Contention** | `Not measured` | Run `SHOW GLOBAL STATUS LIKE 'Threads_running'` and `information_schema.innodb_trx` under load. |

---

## 3. High-Concurrency Mathematical Model (10,000 Active Users)

### 3.1 Workload Parameters
- **Active Concurrent Users:** $N = 10,000$
- **Browsing Active Concurrency Ratio:** $\alpha = 0.05$ (5% browsing/filtering per second = 500 RPS)
- **Mutation Concurrency Ratio:** $\mu = 0.005$ (0.5% submitting candidate per second = 50 RPS)
- **Average Read Requests / Sec:** $\text{RPS}_{\text{read}} = 500 \text{ req/s}$
- **Average Mutation Requests / Sec:** $\text{RPS}_{\text{mut}} = 50 \text{ req/s}$

### 3.2 Current Infrastructure Load at 10K Users
1. **Database Queries / Sec:**
   $$\text{QPS}_{\text{read}} = 500 \times 11 = 5,500 \text{ QPS}$$
   $$\text{QPS}_{\text{mut}} = 50 \times 19 = 950 \text{ QPS}$$
   $$\text{Total QPS} = 6,450 \text{ QPS}$$
   *Result:* MySQL connection pool saturation, lock waits in `company_coins`, and high CPU utilization.
2. **Elasticsearch Searches / Sec:**
   $$\text{ES RPS} = 500 \text{ req/s} \times 1 \text{ search} = 500 \text{ search/s}$$
   *Result:* JVM thread pool saturation if caching is not warm.
3. **Network Bandwidth Demand:**
   $$\text{Bandwidth} = 500 \text{ req/s} \times 36.87 \text{ KB} = 18.435 \text{ MB/s} = 147.5 \text{ Mbps}$$
   *Result:* Significant egress bandwidth consumption for plain HTML fragments.

### 3.3 Optimized Infrastructure Target at 10K Users
1. **Database Queries / Sec (Target):**
   $$\text{QPS}_{\text{read}} = 500 \times 0.02 = 10 \text{ QPS (99.8% offload via Redis + Client RAM)}$$
   $$\text{QPS}_{\text{mut}} = 50 \times 6 = 300 \text{ QPS (Consolidated transaction + Outbox)}$$
   $$\text{Total QPS} = 310 \text{ QPS}$$
2. **Elasticsearch Searches / Sec (Target):**
   $$\text{ES RPS} = 500 \times 0.05 = 25 \text{ search/s (95% cache hit ratio)}$$
3. **Network Bandwidth Demand (Target):**
   $$\text{Bandwidth} = 500 \text{ req/s} \times 2.8 \text{ KB (JSON)} = 1.4 \text{ MB/s} = 11.2 \text{ Mbps (92.4% reduction)}$$
