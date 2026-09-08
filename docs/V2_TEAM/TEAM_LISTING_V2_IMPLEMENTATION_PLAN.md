# Team Listing V2 Implementation Plan (High-Concurrency Architecture)

> **Document Type:** Engineering Implementation Specification  
> **Target Module:** Service Company Team Listing (`service_company/team`)  
> **Target Concurrency:** 10,000+ Active Recruiters & Agency Admins  
> **Status:** Pending Approval (DO NOT IMPLEMENT WITHOUT EXPLICIT APPROVAL)  

---

## 1. V2 Objective & Architectural Overview

The objective of Team Listing V2 is to redesign the data retrieval, aggregation, and view rendering pipeline to achieve **sub-50ms API response times**, **zero N+1 queries**, and **low memory consumption (<5MB per request)** under 10,000+ concurrent recruiter traffic on the Ubuntu 24 / Octane / DragonflyDB server stack.

### V2 Proposed Architecture
```
[Client Web Browser]
       │
       ├─► 1. GET /service_company/team ────────────────────────► ServiceCompanyTeamController@index
       │                                                          ├─► Loads static Blade shell service_company.team.index
       │                                                          └─► Uses cached teamCounts (no unneeded User::get() query)
       │
       └─► 2. AJAX GET /service_company/team_ajax/data ─────────► ServiceCompanyTeamController@data_ajax
                                                                  ├─► DragonflyDB Cache Lookup (Key: ats:teamv2:{tenantId}:{hash})
                                                                  │    (If Miss):
                                                                  ├─► Batch ES Fetch: ELASTIC_USERS_LIST (Only required fields)
                                                                  ├─► Single ES Aggregation: ELASTIC_TEAM_RANK
                                                                  ├─► Batch MySQL Fetch: Active Jobs, Captain Jobs, TRM Talents
                                                                  │    (Single IN(...) queries for sliced page IDs only)
                                                                  ├─► Direct Array Assembly (Zero Blade sub-queries)
                                                                  ├─► Store JSON Payload in DragonflyDB (TTL: 10 mins)
                                                                  └─► Return Fast JSON Response to Client
```

---

## 2. Phase-Wise Implementation Matrix

| Phase | Title | Scope & Objectives | Est. Effort | Key Deliverable |
| :---: | :--- | :--- | :---: | :--- |
| **Phase 1** | **Query & Controller Cleanup** | Remove dead `$this->users` query in `index()`, trim ES `_source` fields in `data_ajax()` | 0.5 Days | Lean controller with zero dead DB queries |
| **Phase 2** | **N+1 Removal & Batching** | Batch pre-fetch Active Jobs, Captain Jobs, and TRM Talents for page slice; pass pre-computed metrics to view | 1.0 Day | 100 sub-queries reduced to 3 batch queries |
| **Phase 3** | **Blade Template Optimization** | Purge helper calls (`getTeamRankData`, `teamActiveJobsCount`) from `team-list-ajax.blade.php` | 0.5 Days | Pure presentational Blade component |
| **Phase 4** | **Cache Strategy Upgrade** | Implement tenant-scoped JSON caching in DragonflyDB with atomic cache tags | 0.5 Days | Sub-20ms cache hit response time |
| **Phase 5** | **Database Index Strategy** | Verify/Add composite indexes on `job_assign` and `bench_assign` | 0.5 Days | Fast MySQL batch aggregations |
| **Phase 6** | **Verification & Telemetry** | Load testing (10k simulated concurrent recruiters), regression verification | 1.0 Day | Verified performance benchmark report |

---

## 3. Detailed Technical Recommendations (Problem → Solution → Risk → Testing)

### Recommendation 1: Eliminate Dead Database Query on Initial Page Load
* **Problem:** `ServiceCompanyTeamController@index` executes `$this->users = User::with('company')->company()->get();`, fetching all tenant users into memory. This data is completely unused by `index.blade.php`.
* **Solution:** Remove `$this->users = User::with('company')->company()->get();` line from `index()`.
* **Risk:** Extremely low. Verified that `$users` variable is not referenced anywhere in `index.blade.php`.
* **Testing:** Open `/service_company/team` in browser, verify page loads without errors and inspect query log to confirm query count dropped by 1.

---

### Recommendation 2: Eliminate N+1 Blade View Sub-Queries via Server-Side Batching
* **Problem:** `team-list-ajax.blade.php` executes 5 sub-queries per row inside the `@foreach` loop (2 ES calls in `getTeamRankData`, 1 ES call in `teamActiveJobsCount`, 2 MySQL queries). For a 20-item page, this fires 100 queries inside the view.
* **Solution:**
  1. Calculate ranking score and rank directly in `data_ajax()` from the initial ES search and rank aggregate queries.
  2. For the sliced page of users (e.g. 20 IDs), execute 3 batch metrics queries in `data_ajax()` before view rendering:
     - **Batch Active Jobs:** Fetch active jobs count for sliced user IDs from `ELASTIC_JOBS` using a single terms filter.
     - **Batch Captain Jobs:** `DB::table('job_assign')->select('spok_user_id', DB::raw('COUNT(DISTINCT job_id) as total'))->where('company_id', $companyId)->whereIn('spok_user_id', $slicedUserIds)->groupBy('spok_user_id')->pluck('total', 'spok_user_id');`
     - **Batch TRM Talents:** `DB::table('bench_assign')->select('user_id', DB::raw('COUNT(*) as total'))->whereIn('user_id', $slicedUserIds)->groupBy('user_id')->pluck('total', 'user_id');`
  3. Pass the pre-computed `$metricsMap` to `team-list-ajax.blade.php` and render pre-fetched values.
* **Risk:** Medium. Must ensure mapped counts correctly match each user ID in the Blade view.
* **Testing:** Compare rendered table numbers (Active Jobs, Captain Jobs, TRM Talents, Medals, Scores) between V1 and V2 for accuracy. Log total executed queries (must equal <= 5 queries total for the entire page).

---

### Recommendation 3: Restrict Elasticsearch Source Fields
* **Problem:** `data_ajax()` fetches 28 fields per user document from `ELASTIC_USERS_LIST` including sensitive auth fields (`super_password`, `session_id`, `email_verification_code`).
* **Solution:** Trim `_source` array in `userSearchPayload_rank` to only required UI fields: `['id', 'company_id', 'name', 'email', 'calling_code', 'mobile', 'status', 'working_status', 'display_name', 'role_name', 'designation', 'image', 'last_login', 'created_at']`.
* **Risk:** Low. If a missing field is needed by a downstream helper, PHP will yield `null`.
* **Testing:** Inspect ES query response payload size (should decrease by ~60%), verify UI displays avatar, name, email, role, status, and last login correctly.

---

### Recommendation 4: Optimize Database Indexes for Batch Queries
* **Problem:** Unindexed queries on `job_assign` and `bench_assign` can slow down as historical assignments grow to millions of rows.
* **Solution:** Ensure composite indexes exist in MySQL:
  ```sql
  ALTER TABLE `job_assign` ADD INDEX `idx_job_assign_company_spok_job` (`company_id`, `spok_user_id`, `job_id`);
  ALTER TABLE `bench_assign` ADD INDEX `idx_bench_assign_user_talent` (`user_id`, `talent_id`);
  ```
* **Risk:** Low. Adding non-unique secondary indexes is safe and non-blocking in MySQL 8.
* **Testing:** Run `EXPLAIN` on batch count queries to confirm `USING INDEX` strategy and 0 full table scans.

---

### Recommendation 5: DragonflyDB Caching & 10,000+ Concurrency Strategy
* **Problem:** High request volumes invalidating entire cache payload when any user profile changes.
* **Solution:**
  1. Store raw JSON payload in DragonflyDB/Redis instead of large pre-rendered HTML strings.
  2. Implement fine-grained cache key tagging: `ats:teamv2:{companyId}:page_{page}_size_{size}_q_{md5(q)}`.
  3. Set cache TTL to 10 minutes (`now()->addMinutes(10)`).
* **Risk:** Low. Cache invalidation must bump company version key when a team member is added, updated, or deactivated.
* **Testing:** Execute concurrent JMeter stress test (1,000 threads), verify cache hit ratio > 98% and CPU utilization remains < 15%.

---

## 4. Server Optimization & Tuning (Ubuntu 24 / aaPanel / FrankenPHP)

| Component | Setting | Value | Purpose |
| :--- | :--- | :--- | :--- |
| **PHP OPcache** | `opcache.memory_consumption` | `512` | Prevents OPcache buffer resets under high class loading |
| **PHP OPcache** | `opcache.max_accelerated_files` | `32531` | Caches all Laravel 11 and Metronic classes |
| **MySQL 8.0** | `innodb_buffer_pool_size` | `20G` | Holds 65% of 31.34 GB RAM for fast table reads |
| **MySQL 8.0** | `innodb_buffer_pool_instances` | `8` | Reduces buffer pool lock contention across 8 CPU cores |
| **DragonflyDB** | `maxmemory` | `4GB` | Dedicates memory for lightning-fast ATS list caches |
| **FrankenPHP/Octane** | `--workers` | `16` | 2 workers per CPU core for maximum async request handling |

---

## 5. Rollout, Rollback & Testing Verification Plan

### A. Testing Verification
1. **Functional Parity Verification**: Verify total team count, active/inactive counts, medals (🥇, 🥈, 🥉), rank numbers, job counts, and status toggles match V1 output 100%.
2. **N+1 Verification**: Enable Laravel Query Log (`DB::enableQueryLog()`), trigger `data_ajax`, and confirm total query count is <= 5 queries regardless of per-page size (10, 20, 50, 100).
3. **Load Verification**: Run `ab` or `k6` benchmark simulating 10,000 requests over 60 seconds to ensure response time remains < 50ms and zero 50x errors occur.

### B. Rollout Plan
1. Deploy updated indexes to MySQL.
2. Deploy V2 controller (`ServiceCompanyTeamController.php`) and updated Blade view (`team-list-ajax.blade.php`).
3. Clear Redis/DragonflyDB team cache (`ats:allteam:*`).
4. Monitor Laravel Horizon queues and Nginx error logs for 15 minutes post-deployment.

### C. Rollback Plan
1. In event of regression, revert git commit for `ServiceCompanyTeamController.php` and `team-list-ajax.blade.php`.
2. Clear DragonflyDB cache.
3. Database index changes are backward-compatible and do NOT require rollback.

---

## 6. Business Logic Preservation Checklist

- [x] Multi-tenant isolation enforced via `company_id = auth()->user()->company_id`.
- [x] `abort_if(Gate::denies('view_team'), 403)` authorization check maintained.
- [x] Rank score formula (aggregating 21 score fields from previous month `ELASTIC_TEAM_RANK`) preserved exactly.
- [x] Status tab filtering (`all`, `active`, `inactive`, `invited`, `suspended`) preserved.
- [x] Deactivation lifecycle (`teamDelete` updating `status = 'inactive'`, `working_status = 0`, and bumping user session epoch) preserved.
- [x] Resend invite email job (`reSendmail`) dispatching `AccountConfirmationTeamMail` preserved.

---
