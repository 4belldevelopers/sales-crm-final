# Recommend Requisition Client Jobs (`recommend_req/{id}`) — Phase-Wise Implementation Plan (10K Active Users)

> **Target Route:** `http://localhost/git/JWMaincode28June2026/service_company/bench-candidates/recommend_req/22`  
> **Environment Context:** JERO Multi-tenant B2B Hiring SaaS (Laravel 11, MySQL 8.0, Elasticsearch 7.x/8.x, Redis/DragonflyDB, Octane/FrankenPHP)  
> **Target Capacity:** 10,000 Active Concurrent Users  
> **Document Type:** TECHNICAL IMPLEMENTATION PLAN  

---

## 1. Overview & Objectives

This implementation plan details the step-by-step tasks required to scale the **Recommend Requisition Client Jobs (`recommend_req/{id}`)** route for 10,000 active concurrent users with <15ms response latency and 99.9% cache hit ratios.

---

## 2. Phase-Wise Implementation Roadmap

### Phase 1: Emergency Quick-Wins (Immediate Latency & Memory Drop)
- **Goal:** Eliminate immediate OOM crashes and redundant database/ES queries.
- **Tasks:**
  1. **Purge Unused `city_list` Pluck**:
     - Remove `DB::table('city_list')->pluck('city')->toArray()` from `recommend_req()`, `recommend_internal_req()`, and `benchjobAndRecomCount()`.
     - *Impact:* Saves 15MB RAM per request thread (~150 GB across 10K workers).
  2. **Eliminate Double Candidate Database Query**:
     - Update `benchjobAndRecomCount($id, $benchDetail)` to accept pre-fetched `$this->bench_detail` object instead of executing `JobApplication::find($id)` again.
  3. **Consolidate KPI & Hero Queries into 1 SQL Query**:
     - Combine 3 separate DB queries in `benchHeroKpis()` into a single optimized query:
       ```sql
       SELECT 
         SUM(CASE WHEN type = 1 AND status = 0 THEN 1 ELSE 0 END) AS views,
         SUM(CASE WHEN type = 2 AND status = 0 THEN 1 ELSE 0 END) AS downloads
       FROM bench_count_view_download 
       WHERE candidate_id = ?;
       ```
  4. **Localize External CDNs**:
     - Replace Cloudflare CDN script/style links for SweetAlert with local assets.

---

### Phase 2: DragonflyDB / Redis Caching Architecture (10K Scale)
- **Goal:** Offload 99%+ of initial page hits and count queries from MySQL and Elasticsearch to DragonflyDB/Redis.
- **Tasks:**
  1. **Candidate Hero Header & KPI Caching**:
     - Key: `ats:bench_candidate_hero:{candidate_id}` (TTL: 300s).
     - Store pre-computed candidate details, hero KPIs (`views`, `downloads`, `interviews`), and recommendation counts.
  2. **Recommendation Count Aggregation Caching**:
     - Key: `ats:bench_recommend_counts:{candidate_id}:{company_id}:{skills_hash}` (TTL: 180s).
     - Caches `recommend_req_counter` and `recommend_internal_req_counter`.
  3. **Cache Stampede Prevention with Atomic Locks**:
     - Wrap cache misses in `Cache::lock("lock:bench_recommend:{$candidate_id}", 5)->get(function() { ... })`.
     - Ensures only 1 worker thread rebuilds cache on miss while 9,999 workers receive cached/stale response seamlessly.

---

### Phase 3: Elasticsearch & Location Query Optimization
- **Goal:** Optimize search performance and node query caching in Elasticsearch.
- **Tasks:**
  1. **Replace SQL Location Cross-Joins with Indexed ES Term Filters**:
     - Deprecate SQL wildcard search across `countries`/`state_list`/`city_list`.
     - Filter directly on indexed `location_city_ids` in `ELASTIC_JOBS`.
  2. **Maximize ES Node Query Cache**:
     - Structure ES queries using `filter` context for deterministic clauses (`status: active`, `company_id`, `max_exp >= 0`).
     - Set `'request_cache' => true` on search requests.

---

### Phase 4: Frontend Hydration & Network Compression
- **Goal:** Reduce network payload size from 45KB to 3KB and enable browser validation caching.
- **Tasks:**
  1. **JSON-First Payload Payload Structure**:
     - Update `all_data_ajax` to return lightweight JSON arrays:
       ```json
       {
         "status": "success",
         "total": 142,
         "data": [ ... ],
         "pagination": { ... }
       }
       ```
  2. **Browser ETag & HTTP 304 Validation**:
     - Add `ETag` header middleware based on candidate modification timestamp and query parameters.
     - Return HTTP 304 Not Modified when user refreshes tab without filter changes.

---

## 3. Verification & Test Plan

### 3.1 Automated Verification
```bash
# 1. PHP Syntax Check
php -l app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php

# 2. Code Style & Pint Verification
./vendor/bin/pint --test app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php

# 3. Route Registration Check
php artisan route:list --path=service_company/bench-candidates/recommend_req
```

### 3.2 Manual & Load Verification
1. **Memory Inspection**: Verify PHP memory consumption per request thread drops from ~28.5MB to < 2.5MB.
2. **Database & ES Query Inspection**: Verify initial page load executes **0 SQL & 0 ES queries** when DragonflyDB cache is warm.
3. **Functional Integrity**:
   - Verify candidate header info, KPIs (views, downloads, interviews), and summary cards display accurate numbers.
   - Verify external client jobs tab (`recommend_req`) and internal jobs tab (`recommend_internal_req`) load correct job matches.
   - Verify search bar, filter drawer, salary mode, experience ranges, and pagination function cleanly.
