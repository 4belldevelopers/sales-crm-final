# Transaction Log Listing V2 Implementation Plan

> **Target Page:** `http://localhost/git/JWMaincode28June2026/service_company/transection-log`  
> **Route:** `subscribe.transectionLog`  
> **Target Architecture:** Layered Service/Repository + AJAX DataTables/Tabs + Optimized Composite Indexes + Redis Caching  
> **Date:** August 2026  
> **Status:** Proposal — Awaiting User Approval  

---

## 1. V2 Objective

Transform the Transaction Log listing page into a high-performance, enterprise-grade, multi-tenant auditing interface capable of scaling seamlessly to **10,000+ concurrent recruiters**. 

Key goals:
1. Eliminate pagination bugs (`skip` + `paginate` conflict).
2. Reduce database query load by 50%+ via single-tab lazy loading.
3. Eliminate `filesort` operations using optimized composite indexes.
4. Improve UI load speeds by removing ~1,000 lines of dead Blade template bloat and moving to AJAX tab switching.
5. Provide keyword search, date range filters, and transaction type filters.

---

## 2. Proposed V2 Architecture

```
User Request (Browser)
   │
   ├─► Initial Page Load (GET /service_company/transection-log)
   │     └─► Renders lightweight Blade shell (No heavy DB query)
   │
   └─► Tab Data Load / Pagination / Filter (AJAX GET /service_company/transection-log/data)
         │
         ▼
    TransactionController (Thin Controller)
         │
         ▼
    CoinTransactionService (Business & Tenancy Validation)
         │
         ▼
    CoinTransactionRepository
         │
         ├─► [Check Redis Cache for Total Count]
         │
         └─► [Execute MySQL Query with Index Optimization] / [Elasticsearch Read]
         │
         ▼
    JSON Response Payload (Fast rendering in modern Metronic Timeline/DataTable)
```

---

## 3. Detailed Recommendations & Optimizations

### Recommendation 1: Fix Pagination Double Offset Bug
- **Problem:** Controller code `.skip(($page - 1) * $perPage)->paginate($perPage)` causes Laravel to apply double offset calculation, skipping data and returning wrong rows on page 2+.
- **Solution:** Remove `.skip(...)` entirely. Standardize on `$query->paginate($perPage)` or manual offset calculation `$query->offset(($page - 1) * $perPage)->limit($perPage)->get()`.
- **Risk:** Low risk. Fixes broken pagination behavior.
- **Testing:** Request `page=1`, `page=2`, `page=3` and verify offset in query log (`DB::enableQueryLog()`). Ensure contiguous record IDs are returned without missing items.

### Recommendation 2: Single-Tab Lazy Loading (Eliminate Dual Query Execution)
- **Problem:** The V1 controller executes both Operational and Sales database queries on every page request, generating 4 queries per request even though the user views only 1 tab.
- **Solution:** Execute only the active tab's query based on `pagetype` request parameter (defaulting to `operationaltab`). For V2 AJAX, fetch tab data lazily on demand when user clicks tab.
- **Risk:** Low risk. 
- **Testing:** Verify with Laravel Telescope or Debugbar that switching tabs executes exactly 1 count query and 1 data query (2 queries total instead of 4).

### Recommendation 3: Database Composite Index Optimization
- **Problem:** Queries filter by `company_id`, `event_type`, `coins > 0` and order by `id DESC`. Existing index `idx_comp_event_created (company_id, event_type, created_at)` causes MySQL to perform `filesort` on `id DESC`.
- **Solution:** Add an optimized composite index to `coin_transection_logs`:
  ```sql
  ALTER TABLE `coin_transection_logs` ADD INDEX `idx_company_event_type_id` (`company_id`, `event_type`, `id` DESC);
  ```
- **Risk:** Low risk. Adding index on write-heavy table requires non-blocking execution (`ALGORITHM=INPLACE, LOCK=NONE`).
- **Testing:** Run `EXPLAIN SELECT ...` on transaction query. Verify `Extra` field changes from `Using filesort` to `Using index condition`.

### Recommendation 4: Fix Data Loss Risk via `LEFT JOIN` on Users
- **Problem:** `join('users', 'users.id', '=', 'coin_transection_logs.event_by')` drops records where `event_by` is `0`, `NULL`, or references a missing/deleted user.
- **Solution:** Replace `join('users', ...)` with `leftJoin('users', ...)`, ensuring system/free transaction logs are always preserved.
- **Risk:** Low risk.
- **Testing:** Insert test transaction log with `event_by = 0`. Verify record appears in audit listing with label "Free / System".

### Recommendation 5: Column Selection & Blade Template Cleanup
- **Problem:** Controller fetches all 26 columns (`select('coin_transection_logs.*')`), and Blade view contains ~1,054 lines of dead, hidden subscription package HTML (`class="card shadow-none d-none"`).
- **Solution:** 
  1. Select only required columns: `id`, `created_at`, `event`, `event_type`, `transection_type`, `coins`, `actual_coins`, `settlement_coins`, `remain_coins`, `event_by`, `from_company_id`.
  2. Purge dead lines 586–1639 from Blade view.
- **Risk:** Minimal. Dead HTML has zero active UI interactions.
- **Testing:** Verify view renders cleanly, UI matches design standards, and page response payload size drops by ~70%.

### Recommendation 6: Add Date Range, Keyword Search & Filter Controls
- **Problem:** Recruiter users cannot search transaction history by keyword, date range, or credit/debit type.
- **Solution:** Add UI controls for Date Range Picker (e.g. Flatpickr / Metronic DateRange), Keyword search input (searches `event` description and `user.name`), and Transaction Type dropdown (`credit` vs `debit`).
- **Risk:** Low risk. Query conditions added conditionally.
- **Testing:** Perform search tests with keywords, date ranges, and credit/debit filters; verify accurate filtered counts and log items.

### Recommendation 7: Redis Caching & Elasticsearch Offloading for 10K Concurrency
- **Problem:** Under 10,000 concurrent recruiters, hitting MySQL directly for pagination counts generates excessive DB load.
- **Solution:** 
  1. Cache total transaction count per company in Redis with a 5-minute TTL (`company:{id}:coin_logs_count:{type}`).
  2. Utilize Elasticsearch index `ELASTIC_COIN_TRANSECTION_LOGS` for complex keyword searches across millions of log records.
- **Risk:** Cache invalidation must be triggered upon new coin transaction log creation.
- **Testing:** Benchmark Redis hit rates during high-concurrency simulation. Verify cache invalidation fires on coin deduction.

### Recommendation 8: Server & Octane Optimization
- **Problem:** 8-core CPU server running standard FPM can experience process bottlenecks under peak concurrency.
- **Solution:** Ensure application is served via Laravel Octane (FrankenPHP / Swoole) with OPcache enabled (`opcache.validate_timestamps=0` in production).
- **Risk:** Code must be memory-leak free (no static array accumulation across requests).
- **Testing:** Verify state persistence and memory stability under `php artisan octane:status`.

---

## 4. Non-Negotiable Business Logic & Tenancy Rules

1. **Multi-Tenant Scoping:** All queries must strictly include `WHERE company_id = auth()->user()->company_id`.
2. **Coin Value Fallbacks:**
   - Displayed Coins = `actual_coins` if non-null, else `coins`.
   - Settlement Coins = `settlement_coins` if `settlement_coins > 0`.
   - Remaining Balance = `remain_coins` if non-null, else `coins`.
3. **Event User Fallback:** If `event_by != 1` display `$transection_log->name`, else display `'Free'`.
4. **Tab Segregation:** Operational coins (`event_type = 'operational'`) and Sales coins (`event_type = 'sales'`) must remain isolated in their respective tabs.

---

## 5. Phase-Wise Implementation Plan (V2)

| Phase | Component | Objective | Detailed Tasks | Deliverables | Risk Level |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Phase 1** | **Query & Controller Fixes** | Eliminate offset bug & data loss | 1. Remove `.skip()` from `ManageSubscriptionController`.<br>2. Change `join('users')` to `leftJoin('users')`.<br>3. Execute only active tab query based on `pagetype`. | Fixed Controller & Accurate Pagination | **Low** |
| **Phase 2** | **DB Indexing & Schema** | Eliminate `filesort` | Add composite index `idx_company_event_type_id (company_id, event_type, id DESC)` to `coin_transection_logs`. | SQL Index Migration | **Low** |
| **Phase 3** | **View Cleanup & Refactoring** | Purge template bloat | 1. Remove 1,000+ lines of dead HTML from `transection-logs.blade.php`.<br>2. Restructure Blade view into clean sub-views. | Clean Blade Template (~400 lines) | **Low** |
| **Phase 4** | **AJAX & Filter Features** | Modernize UI & search | 1. Implement AJAX endpoint `/service_company/transection-log/data`.<br>2. Add Date Range Picker & Keyword Search UI.<br>3. Implement smooth tab switching without page reload. | Interactive AJAX Listing | **Medium** |
| **Phase 5** | **Caching & High Concurrency** | Scale to 10k recruiters | 1. Implement Redis count caching for log records.<br>2. Integrate Elasticsearch reader for deep history search.<br>3. Verify Octane / FrankenPHP compatibility. | Scalable Enterprise Endpoint | **Medium** |
| **Phase 6** | **Verification & Testing** | Comprehensive QA | 1. Run unit/feature test suite (`php artisan test`).<br>2. Verify multi-tenant isolation.<br>3. Load test with k6 / ApacheBench. | Audit Compliance & Test Suite | **Low** |

---

## 6. Rollback Plan

If any critical issue arises during deployment:
1. **Code Rollback:** Revert `ManageSubscriptionController.php` and `transection-logs.blade.php` to previous git commit.
2. **Index Rollback:** `ALTER TABLE coin_transection_logs DROP INDEX idx_company_event_type_id;` (Non-breaking DB operation).
3. **Cache Flush:** `php artisan cache:clear` to purge any transient Redis keys.
