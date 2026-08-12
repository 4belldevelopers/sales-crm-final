# Inhouse Talent Listing (Bench Candidates) — V2 Architecture & Implementation Plan

> **Target Feature:** Inhouse Talent Listing V2  
> **Route:** `http://localhost/git/JWMaincode28June2026/service_company/bench-candidates`  
> **Status:** PROPOSED ARCHITECTURE & IMPLEMENTATION PLAN — DO NOT IMPLEMENT V2 WITHOUT APPROVAL

---

## 1. V2 Objective & Key Results

### Primary Objective
Transform the **Inhouse Talent Listing** page into a high-performance, ultra-scalable enterprise talent marketplace capable of handling **10,000+ concurrent recruiters** with **sub-100ms response times**, zero N+1 database queries, and 100% preservation of multi-tenant business logic and schema contracts.

### Key Results (Target Metrics)
* ⚡ **Query Reduction:** Reduce backend requests per page load from **42 queries down to 1 primary query** (97.6% reduction).
* ⚡ **Page Load Latency:** Reduce uncached server response time from ~650ms to **< 80ms**.
* ⚡ **Payload Size:** Reduce AJAX response payload from ~85 KB (rendered HTML) to **< 8 KB** (raw JSON data payload).
* ⚡ **Concurrency Capacity:** Scale system throughput to **10,000+ concurrent recruiter requests** without database connection pool exhaustion or CPU spikes in Elasticsearch.

---

## 2. Proposed System Architecture

### 2.1 Refactored Architecture Flow

```
[Browser Client (Vue/JS Client-Side Templating)]
       │
       ├── 1. Static Initial Shell GET /service_company/bench-candidates
       │      └─► Serves cached Blade container shell + JS assets (No heavy financial SQL)
       │
       └── 2. Client Async Parallel Data Fetch
              │
              ├── Endpoint A: GET /service_company/bench-candidates_ajax/v2/data
              │      │
              │      ▼
              │  [ServiceCompanyBenchCandidatesController@data_v2]
              │      │
              │      ├── Redis Cache Lookup (Key: ats:v2:bench:{tenant_id}:{hash})
              │      ├── [Cache Miss] ──► Elasticsearch Search (ats_candidates_bench_elastic_v2)
              │      │                          * Direct field sort on `np_days` (No Painless scripts!)
              │      │                          * Pre-indexed counters & `wm` preference included!
              │      └── Returns JSON Payload Array (No HTML strings!)
              │
              └── Endpoint B: GET /service_company/bench-candidates/v2/analytics (Lazy Loaded)
                     │
                     ▼
                 [BenchStaffingAnalyticsService]
                     │
                     └── Reads pre-aggregated financial metrics from Redis/MySQL cache
```

---

## 3. Detailed Architectural Improvements

### 3.1 N+1 Query Elimination Strategy

#### Problem 1: Blade Loop Elasticsearch Counter Requests
* **V1 Mechanism:** `GeneralMethod::bench_explore_listing_counter($row->id)` fires 1 ES search + 9 aggregations per row (10 ES HTTP calls per page).
* **V2 Solution:** Denormalize candidate pipeline counters directly into the candidate document stored in `ats_candidates_bench_elastic`. When a candidate's pipeline status changes in `job_applications`, dispatch an asynchronous queue job (`ElasticBenchCandidateIndex`) to increment/update doc fields:
  - `doc.sourcing_count`, `doc.screening_count`, `doc.interview_count`, `doc.offered_count`, `doc.joined_count`.
* **Risk:** Eventual consistency window (~500ms queue latency) between candidate application update and ES search index.
* **Testing Strategy:** Unit test `ElasticBenchCandidateIndex` job; verify pipeline counts match raw SQL `GROUP BY candidate_id` results.

#### Problem 2: Blade Loop View/Download Count Queries
* **V1 Mechanism:** Two MySQL `COUNT(*)` queries executed on `bench_count_view_download` for every candidate row in Blade loop.
* **V2 Solution:** Pre-fetch view and download stats in a single bulk batch query inside the controller before passing data to the view:
  ```php
  $stats = DB::table('bench_count_view_download')
      ->select('candidate_id', 'type', DB::raw('COUNT(*) as total'))
      ->whereIn('candidate_id', $pageIds)
      ->where('status', 0)
      ->groupBy('candidate_id', 'type')
      ->get();
  ```
* **Risk:** Minimal. Pure SQL aggregation replacing loop queries.
* **Testing Strategy:** Assert total query count remains `1` regardless of page size (10, 50, or 100 candidates).

#### Problem 3: Company Currency & Show Mode Loop Query
* **V1 Mechanism:** `GeneralMethod::getCompanyCurrencyBenchshowMode($company_id)` joins `companies` and `master_currency` per candidate row.
* **V2 Solution:** Query company currency settings **once** at the start of `data_ajax()` and pass the single object to all candidate rows.
* **Risk:** None. Company ID is constant for a given user session.
* **Testing Strategy:** Verify currency symbol formatting matches across all rows in rendered view.

---

### 3.2 Search & Sorting Optimization (Painless Script Removal)

#### Problem: Painless Script Sort on Notice Period String
* **V1 Mechanism:** Dynamic Painless script parses `np_id.keyword` string (`splitOnToken(',')`) during every query sort execution.
* **V2 Solution:** Add an indexed numeric field `np_days` (integer) to `job_applications` and `ats_candidates_bench_elastic`. Calculate `np_days` upon candidate save/update:
  - Immediate = `0`
  - 30 Days = `30`
  - LWD Date = `(LWD_Date - Current_Date)` in days.
* **ES Sort Pipeline (V2):**
  ```json
  "sort": [
    { "ads_switch_rank": { "order": "asc" } },
    { "np_days": { "order": "asc", "unmapped_type": "integer" } }
  ]
  ```
* **Risk:** Background backfill migration required for existing candidates.
* **Testing Strategy:** Benchmark ES query execution time before/after script removal; verify sort order consistency.

---

### 3.3 Database Indexing Strategy

Create targeted compound indexes to convert full table scans into index lookups:

```sql
-- 1. Index for view/download counters bulk fetch
CREATE INDEX `idx_bcvd_cand_type_stat` 
ON `bench_count_view_download` (`candidate_id`, `type`, `status`);

-- 2. Index for bench placement financial queries (preserving schema typo: source_comapny_id)
CREATE INDEX `idx_bp_source_company` 
ON `bench_place` (`source_comapny_id`, `create_at`);

-- 3. Composite index for bench candidates status filtering (preserving schema typo: condidate_type)
CREATE INDEX `idx_ja_company_type_status` 
ON `job_applications` (`company_id`, `condidate_type`, `status`);
```

---

### 3.4 API & Data Payload Optimization

#### Shift to Client-Side JSON Templating
* **V1 Approach:** Returns HTML string inside JSON object: `{ "html": "<tr>...</tr>", "total": 10 }`.
* **V2 Approach:** Return clean, compact JSON objects:
  ```json
  {
    "status": "success",
    "total": 142,
    "page": 1,
    "data": [
      {
        "id": 101,
        "full_name": "Jane Doe",
        "designation": "Senior Laravel Developer",
        "exp": "6 Yrs",
        "wm": "Remote",
        "notice": "Immediate",
        "location": "Bangalore, India",
        "ectc_formatted": "₹1,800,000 /Yr",
        "views": 42,
        "downloads": 12,
        "pipeline": { "sourcing": 5, "screening": 2, "interview": 1 }
      }
    ]
  }
  ```
* **Benefits:** Reduces payload size by ~90%, enables client-side rendering (Alpine.js / Vue.js / Mustache), drastically reduces PHP CPU overhead spent on Blade string concatenation.

---

### 3.5 Caching & High-Concurrency Scaling Strategy (10k Recruiters)

1. **Multi-Layer Cache Architecture:**
   - **L1 (In-Memory Micro-Cache):** Cache user permission flags & company settings in Swoole/Octane memory for request lifetime.
   - **L2 (DragonflyDB / Redis):** Cache normalized candidate JSON arrays with tenant tag invalidation.
2. **Cache Stampede Protection (Mutex Locking):** Implement Redis atomic locks (`Cache::lock()`) during cache misses to ensure only **one worker** recomputes an expired search query while concurrent users receive stale data for up to 2 seconds.
3. **Elasticsearch Connection Pooling:** Use persistent HTTP connections (`keep-alive`) between Octane workers and ES node `76.13.243.68`.

---

## 4. Preservation of Non-Negotiable Business Logic & Contracts

The V2 implementation MUST adhere strictly to the following constraints:

1. **Tenancy Isolation:** All MySQL queries and Elasticsearch filters MUST explicitly include `company_id = auth()->user()->company_id` (or `source_comapny_id`).
2. **Authorization Guards:** Preserve `abort_if(Gate::denies('view_job_applications'), 403);` on all controller endpoints.
3. **Preservation of Schema Typos:**
   - Do NOT rename `job_applications.condidate_type` in code or database.
   - Do NOT rename `bench_place.source_comapny_id` in code or database.
   - Do NOT rename `job_applications.secondrary_email` or `visastatu`.
4. **Metronic UI / Poppins Typography Standards:** Maintain clean Metronic theme structure, Poppins font, and compact 12px/13px table layout.

---

## 5. Phase-Wise Implementation Roadmap

The V2 rollout is structured into 6 isolated, verifiable phases to ensure zero disruption to production operations:

| Phase | Milestone Name | Scope of Work | Risk Level | Rollback Trigger |
| :---: | :--- | :--- | :---: | :--- |
| **PHASE 1** | **Database Indexing & Schema Readiness** | • Apply compound indexes (`idx_bcvd_cand_type_stat`, `idx_bp_source_company`, `idx_ja_company_type_status`).<br>• Add `np_days` and `ads_switch_rank` columns to `job_applications`. | Low | Remove newly added secondary indexes if DB lock occurs. |
| **PHASE 2** | **Elasticsearch Mapping & Sync Enhancement** | • Update `ats_candidates_bench_elastic` index mapping to include `wm`, `np_days`, `ads_switch_rank`, and pipeline status counts.<br>• Update `ElasticBenchCandidateIndex` worker.<br>• Run backfill reindex script for existing candidates. | Medium | Fallback ES query to read legacy fields if new mapping missing. |
| **PHASE 3** | **Backend Controller Refactoring (N+1 Removal)** | • Refactor `ServiceCompanyBenchCandidatesController@data_ajax` to pre-fetch view/download counts in bulk SQL.<br>• Replace Painless ES script sorts with native field sorts on `np_days`.<br>• Eliminate loop DB calls. | High | Revert controller route to point to V1 method (`data_ajax_v1`). |
| **PHASE 4** | **API Payload & Frontend Optimization** | • Create `data_v2` JSON endpoint returning structured candidate arrays.<br>• Implement lightweight client-side renderer in `index.blade.php`.<br>• Reduce network payload by 90%. | Medium | Toggle Blade view switch `use_v2_renderer` back to `false`. |
| **PHASE 5** | **Cache Stampede & Concurrency Scaling** | • Implement Redis `Cache::lock()` on ES search cache misses.<br>• Configure persistent HTTP connection pool for Elasticsearch.<br>• Validate Octane worker memory stability. | Low | Disable Redis lock wrapper and fall back to standard `Cache::remember()`. |
| **PHASE 6** | **Load Testing, Verification & Final Rollout** | • Execute synthetic load testing (1,000 → 10,000 concurrent simulated recruiters).<br>• Monitor MySQL connection count and ES CPU load.<br>• Final sign-off. | Low | Revert feature flag `BENCH_LISTING_V2_ENABLED=false`. |

---

## 6. Testing, Rollout & Rollback Strategy

### 6.1 Automated & Manual Verification Plan
1. **Automated Query Count Verification:** Write a Pest/PHPUnit test that wraps the `data_ajax` route call in `DB::listen()` and asserts total database queries `<= 2` per request.
2. **Data Consistency Check:** Compare JSON output of V2 against V1 for 100 randomly sampled candidates to ensure exact match of names, CTCs, skills, notice periods, and placement status counts.
3. **Tenancy Leak Audit:** Run multi-tenant automated tests attempting to query candidate IDs belonging to Company B while authenticated as Company A. Assert HTTP 200 returns 0 results.

### 6.2 Rollout Mechanism (Feature Flag Controlled)
Feature flag `BENCH_LISTING_V2_ENABLED` configured in `config/features.php`:
```php
if (config('features.bench_listing_v2_enabled')) {
    return $this->data_ajax_v2($request);
}
return $this->data_ajax_v1($request);
```

### 6.3 Rollback Plan
If production error rates rise or Elasticsearch mapping errors occur:
1. Instantly set `BENCH_LISTING_V2_ENABLED=false` in application config.
2. Flush Redis cache namespace `ats:v2:bench:*`.
3. System seamlessly falls back to V1 execution without code deployment or server restart.
