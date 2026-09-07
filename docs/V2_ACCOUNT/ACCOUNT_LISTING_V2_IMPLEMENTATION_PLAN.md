# Account Listing & Settings V2 — Implementation Plan

**Target Page:** `http://localhost/git/JWMaincode28June2026/service_company/account`  
**Workspace:** `c:\xampp\htdocs\git\JWMaincode28June2026`  
**Date:** August 12, 2026  
**Status:** Implementation Plan — Waiting for Approval (DO NOT IMPLEMENT)

---

## 1. V2 Objective & Overview

The goal of the V2 Account Listing & Settings architecture is to transform the existing 249 KB monolithic, 52+ query synchronous view into a ultra-fast, modular, tab-on-demand service-oriented architecture capable of scaling seamlessly to **10,000+ concurrent recruiters**.

### Key Target Metrics
- **Initial HTML Response Size:** Reduced from **249 KB to < 25 KB** (90%+ payload reduction).
- **Initial Page Load Database Queries:** Reduced from **52+ SQL queries to 3 queries** on initial Overview tab load.
- **Elasticsearch Load:** Replaced size:10,000 mass queries with cached, targeted notification queries.
- **Page Response Time:** Reduced TTFB from 850ms to < 80ms.

---

## 2. Proposed Architecture

```
[Browser Client]
       │
       ▼ (Initial GET: Overview Tab Only)
[ServiceCompanyAccountSettingsV2Controller@index]
       │
       ├──► [AccountSettingsService] ──► [Redis Cache (Static Masters & Company Profile)]
       │                                         │ (Cache Miss)
       │                                         ▼
       │                                  [AccountRepository] ──► [MySQL DB (3 Queries)]
       │
       ▼ (Lazy Load Other Tabs via Async AJAX)
[GET /service_company/account/tab/{tab_name}]
       │
       ▼
[Modular Blade Component / JSON API Response]
```

### Architecture Highlights
1. **Lazy Tab Loading (On-Demand Data Fetching):** Initial GET loads ONLY shell HTML and Overview tab data. Remaining 8 tabs (`interview_Stage`, `terms`, `offerterms`, `agencyofferterms`, `offercomponent`, `services`, `boostkeyword`, `settings`) load via lightweight async AJAX requests when clicked.
2. **AccountSettingsService & Repository Pattern:** Business logic is decoupled from controllers into `AccountSettingsService` and `AccountRepository`.
3. **Redis Multi-Layer Caching:** Master reference tables and company profile settings are cached in Redis with tenant invalidation hooks.

---

## 3. Comprehensive Recommendations (Problem → Solution → Risk → Testing)

### 3.1 Architecture & Modular Blade Decomposition
- **Problem:** Monolithic 249 KB `index.blade.php` renders 9 tab panes and 3,800+ lines of inline JS synchronously, starving memory and CPU.
- **Solution:** Decompose `index.blade.php` into modular Blade partials (`resources/views/service_company/account/v2/tabs/*.blade.php`). Implement async tab fetching via `GET /service_company/account/tab/{tabName}`.
- **Risk:** Minor UX delay (~50ms) on first click of an unvisited tab.
- **Testing:** Verify each tab loads correctly via AJAX with loading spinners; test back-button / URL hash navigation (`#interview_Stage`, etc.).

### 3.2 Query Consolidation & N+1 Removal
- **Problem:** 52+ SQL queries executed per request, including 4 redundant `client_location` queries and 7 separate `candidate_status` queries.
- **Solution:**
  1. Consolidate 4 `client_location` queries into 1 `first()` query.
  2. Consolidate 7 `candidate_status` queries into 1 grouped query:
     ```sql
     SELECT * FROM candidate_status 
     WHERE company_id = ? AND parent IN (1, 2, 3, 4, 5, 6, 7) 
     ORDER BY parent ASC, priority ASC;
     ```
  3. Consolidate `offer_point_companies` queries into 1 tenant-wide retrieval and map in PHP.
  4. Consolidate `users` queries into single aggregated count / admin collection.
- **Risk:** Slight risk of key mapping mismatch during array grouping in PHP.
- **Testing:** Unit test repository methods to ensure output arrays match legacy array structure exactly across all 7 interview stages and offer points.

### 3.3 Redis Caching Strategy for Static Master Tables
- **Problem:** Static reference tables (`master_languages`, `master_timezone`, `master_currency`, `master_time_format`, `master_date_format`, `master_email_preferences`, `countries`) are re-queried from DB on every request.
- **Solution:** Cache all static master tables in Redis with key prefix `master_data:*` and 24-hour TTL (`Cache::remember()`).
- **Risk:** Cache stale data if master dropdown options are modified by super-admin.
- **Testing:** Verify cache hits via `Redis::keys('master_data:*')`; test cache flush trigger when admin updates master options.

### 3.4 Elasticsearch Notification Query Optimization
- **Problem:** `ELASTIC_MASTER_NOTIFICATIONS` is queried twice with `size: 10000` payload.
- **Solution:** Remove un-paginated `size: 10000` Elastic queries. Store master notification templates in Redis cache or DB, querying Elastic only when paginated notification logs are needed.
- **Risk:** Notification settings missing newly added notification types if cache TTL hasn't expired.
- **Testing:** Verify notification toggles update correctly; benchmark Elastic cluster memory under simulated load.

### 3.5 Database Composite Index Strategy
- **Problem:** Missing composite indexes cause full table scans and file-sorts on `candidate_status`, `offer_point_companies`, `users`, `boostkeywords`.
- **Solution:** Add non-blocking composite indexes:
  ```sql
  ALTER TABLE candidate_status ADD INDEX idx_company_parent_priority (company_id, parent, priority);
  ALTER TABLE offer_point_companies ADD INDEX idx_company_section_sorting (company_id, section_type, section_divided, sorting);
  ALTER TABLE boostkeywords ADD INDEX idx_company_country_id (company_id, country_id, id);
  ALTER TABLE users ADD INDEX idx_company_type_status (company_id, type, status);
  ALTER TABLE company_coins ADD INDEX idx_company_event (company_id, event_type);
  ```
- **Risk:** Minor table lock during index creation if run synchronously on production.
- **Testing:** Run index creation during low-traffic maintenance window; verify `EXPLAIN` shows index usage with `type=ref` and no `Using filesort`.

### 3.6 API Security, Tenancy & Permissions Optimization
- **Problem:** Sub-action endpoints (stage updates, keyword status toggles, address updates) lack explicit role authorization gates.
- **Solution:** Implement Form Requests (`UpdateAccountSettingsRequest`) and enforce policy gates (`Gate::authorize('update-account-settings')`) on all V2 endpoints. Ensure `company_id` is always retrieved from `auth()->user()->company_id`.
- **Risk:** Authorized admins blocked if policy roles are misconfigured.
- **Testing:** Write feature tests attempting cross-tenant parameter tampering and unauthorized user role access (expect HTTP 403).

### 3.7 Frontend Asset & Script Optimization
- **Problem:** 3,800+ lines of inline jQuery code in `index.blade.php` leading to slow DOM parsing and maintainability issues.
- **Solution:** Extract inline JS into modular JS asset modules (`resources/js/account/account-v2.js`), bundled via Vite/Mix. Use event delegation for tab actions.
- **Risk:** Broken event listeners if dynamic AJAX tab content isn't bound to delegated container selectors.
- **Testing:** Test all modal forms, stage drag-and-drop / ordering triggers, boost status toggles, and form submissions across Chrome, Firefox, Safari, and mobile viewports.

---

## 4. 10,000+ Concurrent Recruiters Scaling Strategy

| Metric / Layer | Current V1 Architecture | Proposed V2 Scalable Architecture | Expected Capacity Improvement |
| :--- | :--- | :--- | :--- |
| **Initial HTTP Payload** | 249 KB monolithic HTML | < 25 KB shell HTML | **90% bandwidth saving** |
| **SQL Queries / Initial GET** | 52+ SQL queries | 3 SQL queries (Overview tab) | **94% DB query reduction** |
| **Elasticsearch Requests** | 2 queries x 10,000 size | Cached in Redis (0 Elastic queries on render) | **100% Elastic load reduction on render** |
| **Static Master Queries** | Re-queried on every request | 100% Redis Cached (24h TTL) | **Zero DB overhead for master data** |
| **PHP-FPM Memory / Request** | ~18 MB | ~3.5 MB | **80% RAM saving per worker** |
| **Max Concurrent Users (8 Cores)** | ~150 concurrent users | **10,000+ concurrent users** | **66x Concurrency Scaling Factor** |

### Infrastructure Tuning Recommendations
1. **PHP-FPM Pool Sizing:** Configure `pm = dynamic`, `pm.max_children = 120`, `pm.start_servers = 20`, `pm.min_spare_servers = 10`, `pm.max_spare_servers = 30`, `pm.max_requests = 1000`.
2. **OPcache Configuration:** `opcache.enable=1`, `opcache.memory_consumption=256`, `opcache.interned_strings_buffer=16`, `opcache.max_accelerated_files=20000`, `opcache.validate_timestamps=0` (in production).
3. **MySQL InnoDB Buffer Pool:** Allocate 20 GB (approx 65% of 31.34 GB RAM) to `innodb_buffer_pool_size` on Ubuntu server. Set `innodb_buffer_pool_instances=8`.
4. **Redis Cache Store:** Set `maxmemory 4gb`, `maxmemory-policy volatile-lru` for application cache Redis instance.

---

## 5. Implementation Phases (Table Format)

| Phase | Objective | Key Tasks | Expected Outcome | Est. Risk |
| :--- | :--- | :--- | :--- | :--- |
| **Phase 1: DB Indexing & Query Consolidation** | Optimize SQL performance without changing UI architecture | 1. Add composite indexes on `candidate_status`, `offer_point_companies`, `users`, `boostkeywords`.<br>2. Consolidate redundant `client_location` and `candidate_status` queries in controller.<br>3. Benchmark query execution time using Laravel Telescope/Debugbar. | Query count reduced from 52 to ~15. Execution time drops from 850ms to ~150ms. | Low |
| **Phase 2: Redis Caching & Service Layer** | Introduce caching layer and decouple business logic | 1. Build `AccountRepository` and `AccountSettingsService`.<br>2. Cache all static master dropdowns in Redis (24h TTL).<br>3. Implement tenant settings caching with invalidation on updates.<br>4. Replace size:10000 Elastic search with cached master notifications. | Zero DB queries for static master tables. Zero Elastic calls on initial GET. | Medium |
| **Phase 3: Modular Blade & Lazy Tab Loading** | Decompose monolithic Blade into lazy-loaded tab components | 1. Split `index.blade.php` into 9 partial views.<br>2. Implement `GET /service_company/account/tab/{tabName}` endpoint.<br>3. Load initial page with Overview tab only.<br>4. Fetch other tabs asynchronously on click via AJAX. | Initial HTML payload drops from 249 KB to < 25 KB. Initial page load DB queries drop to 3. | Medium |
| **Phase 4: Frontend JS Modularization & Security** | Refactor legacy inline scripts and enforce API policies | 1. Extract 3,800+ lines of inline JS into `account-v2.js`.<br>2. Implement Form Request validation and Policy authorization on all endpoints.<br>3. Standardize JSON responses for all sub-actions. | Clean, maintainable JS assets. Sealed API security with policy enforcement. | Medium |
| **Phase 5: Load Testing & Staged Rollout** | Validate 10K concurrency target and execute rollout | 1. Execute k6/Locust load testing simulating 10,000 concurrent recruiters.<br>2. Enable V2 via tenant feature flag (`account_v2_enabled`).<br>3. Monitor Redis cache hit ratio, MySQL connection pool, and CPU usage. | Verified sub-80ms response times at 10,000 concurrent user load. | Low |

---

## 6. Testing, Rollout & Rollback Strategy

### Testing Strategy
- **Unit Tests:** Test `AccountRepository` and `AccountSettingsService` methods for query consolidation and cache hits/misses.
- **Feature Tests:** Test all AJAX tab loading endpoints (`/account/tab/{tabName}`) and settings modification POST routes.
- **Load / Stress Testing:** Run k6 scripts simulating 10,000 virtual users executing simultaneous GETs on `/service_company/account` and updating settings.

### Rollout Strategy
- **Feature Flag Control:** Use tenant setting flag `config('features.account_v2')`.
- **Canary Release:** Enable V2 for 5% of service company tenants initially, monitoring Horizon queue latency and web response times before 100% rollout.

### Rollback Strategy
- If critical issues arise, flip `account_v2_enabled` feature flag to `false` in Redis/config.
- System instantly reverts to V1 `ServiceCompanyAccountSettingsController` without code redeployment or downtime.

---

## 7. Non-Negotiable Business Logic Preservation

1. **Strict Multi-Tenancy:** `company_id` MUST be injected from `auth()->user()->company_id` across all repository methods and cache keys (`account_settings:company:{id}`).
2. **7-Stage Interview Pipeline Integrity:** Parent stages (1: Sourcing, 2: Screening, 3: Evaluation, 4: Negotiation, 5: Offered, 6: Joined, 7: Rejected) and custom sub-stage priorities MUST be preserved identically.
3. **Offer Terms & Commercial Calculations:** Standard offer terms, agency offer terms, and hourly/monthly/annual offer component points logic MUST remain 100% backward compatible.
4. **Boost Keyword Mappings:** Boost keywords linked to countries and status toggling MUST map to existing `boostkeywords` database tables.
