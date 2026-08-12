# Subscription & Wallet Center V2 Implementation Plan

## Executive Summary
This document outlines the proposed **V2 Implementation Plan** for the Subscription & Wallet Center (`service_company/new-subscribe/newindex`). The goal is to refactor the current architecture into a modern, high-throughput, scale-ready module capable of handling **10,000+ concurrent recruiters** while maintaining 100% functional and business logic parity.

> [!IMPORTANT]
> **DO NOT IMPLEMENT THIS PLAN UNTIL EXPLICITLY APPROVED BY USER.**

---

## 1. V2 Objective & Key Metric Targets

| Metric Area | V1 Current State | V2 Target State |
| :--- | :--- | :--- |
| **Total DB Queries per Page Load** | 29 to 50+ queries | **2 to 4 queries** (for initial `#tab=overview` view) |
| **External API Blocking Calls** | Synchronous cURL call in HTTP thread | **0 synchronous API calls** (offloaded to queue worker) |
| **N+1 Query Loops** | 4 active loops | **0 N+1 loops** (eager loaded / cached) |
| **Master Data Read Source** | MySQL table scans | **Redis Cache** (24h TTL with admin invalidation) |
| **Transactions Tab Data** | Loaded on every initial page visit | **Lazy-loaded strictly on demand** via AJAX |
| **DB Index Coverage** | Missing composite FK indexes on 6 tables | **100% indexed** composite keys on tenant & FK columns |
| **10K Recruiter Concurrency** | High risk of MySQL pool crash | **High throughput / zero-blocking I/O** |

---

## 2. Phase-Wise Implementation Roadmap

| Phase | Task Name | Problem Solved | Solution Summary | Risk Level | Testing Method |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Phase 1** | **Database Index Migration** | Full table scans on 6 key subscription tables | Create composite indexes on `cart_transaction_history`, `coin_order_events`, `coin_package_points`, `top_up_logs`, `package_active`, `carts`. | **LOW** | `EXPLAIN` query plan analysis in MySQL console. |
| **Phase 2** | **Asynchronous External API Offloading** | Synchronous Razorpay cURL requests blocking web thread | Dispatch `SyncPendingRazorpayPaymentsJob` to Redis/Horizon queue; listen to Razorpay webhook events. | **MEDIUM** | Simulate pending transactions; verify background worker updates status without HTTP latency. |
| **Phase 3** | **N+1 Query Elimination & Eager Loading** | 4 N+1 query loops inside `.map()` callbacks | Batch fetch related `coin_order_events` and `coin_package_points` using `whereIn()` before mapping. | **LOW** | Profile DB query count using Laravel Query Log / Telescope. |
| **Phase 4** | **Redis Caching for Master Packages** | Repeated MySQL queries for static package catalogs | Wrap `coin_packages`, `top_up_coin_packages`, and `payment_settings` in Redis cache (`Cache::remember`). | **LOW** | Cache hit/miss inspection via Redis CLI (`redis-cli monitor`). |
| **Phase 5** | **Lazy-Load Transactions Tab & Code Cleanup** | Redundant payload on initial load & duplicate company queries | Remove transaction history pre-fetch from `newindex()`; load strictly via AJAX on tab switch. Remove duplicate company query. | **LOW** | Verify `#tab=overview` loads 0 transaction queries; click tab to confirm AJAX load. |
| **Phase 6** | **Service Layer & Modular Blade Clean-Up** | 2,100+ line god controller & 121 KB monolithic view file | Extract business logic into `SubscriptionService`; split `newindex.blade.php` into modular Blade partials. | **LOW** | End-to-end regression UI test on 1366x768 to 4K screen resolutions. |

---

## 3. Detailed Recommendations (Problem → Solution → Risk → Testing)

### Recommendation 1: Shift Razorpay Status Sync to Background Queue Job / Webhooks
- **Problem**: In `ManageSubscriptionController@loadTransactionHistory`, up to 5 pending Razorpay transactions are queried and synced via synchronous `curl_exec` calls to `api.razorpay.com` on every page request. External latency or timeouts stall the user web server worker thread.
- **Solution**: Remove cURL calls from the HTTP request handler. Implement `SyncPendingRazorpayPaymentsJob` dispatched via Redis queue, and configure a Razorpay Webhook Controller (`RazorpayWebhookController@handle`) for real-time payment status callbacks.
- **Risk**: Transaction status in the UI may take a few seconds to update after payment completion if background queue workers are overloaded.
- **Testing**:
  1. Trigger payment creation with pending status.
  2. Verify initial page load time is free of cURL HTTP calls.
  3. Execute queue worker (`php artisan queue:work`) and confirm `cart_transaction_history` status updates automatically.

### Recommendation 2: Eliminate All N+1 Query Loops via Batch Eager Loading
- **Problem**: Mapping over `current_coins_operational`, `current_coins_sales`, `operational_package`, and `sales_package` executes iterative DB queries per item for `coin_order_events` and `coin_package_points`.
- **Solution**:
  1. Collect all `coin_order_id` values and execute a single `whereIn('coin_order_id', $orderIds)` query on `coin_order_events`. Group results by `coin_order_id` in PHP memory.
  2. Collect all `coin_package_id` values and execute a single `whereIn('coin_package_id', $packageIds)` query on `coin_package_points`. Group results by `coin_package_id` in PHP memory.
- **Risk**: Negligible memory overhead from loading grouped collections in memory (mitigated by company/event filtering).
- **Testing**: Log database queries using `DB::enableQueryLog()`. Confirm query count for coin package loading drops from $20+$ queries down to exactly 4 queries.

### Recommendation 3: Implement Composite Indexes on 6 Target Tables
- **Problem**: `cart_transaction_history`, `coin_order_events`, `coin_package_points`, `top_up_logs`, `package_active`, and `carts` lack tenant-scoped foreign key indexes, causing full table scans.
- **Solution**: Add composite database indexes via schema migration:
  ```sql
  ALTER TABLE cart_transaction_history ADD INDEX idx_cart_trans_company_gateway (company_id, payment_gateway);
  ALTER TABLE coin_order_events ADD INDEX idx_coin_order_events_order (coin_order_id);
  ALTER TABLE coin_package_points ADD INDEX idx_coin_pkg_points_pkg (coin_package_id);
  ALTER TABLE top_up_logs ADD INDEX idx_topup_logs_company_event (company_id, event_type);
  ALTER TABLE package_active ADD INDEX idx_package_active_company (company_id);
  ALTER TABLE carts ADD INDEX idx_carts_company_status (company_id, status);
  ```
- **Risk**: Slight index write maintenance overhead during `INSERT`/`UPDATE` operations (negligible compared to query scan gains).
- **Testing**: Run `EXPLAIN` on all 6 target queries before and after index creation. Verify `type` changes from `ALL` to `ref`/`range`, with `rows` scanned dropping to exact matching counts.

### Recommendation 4: Lazy Load Transactions Tab & De-duplicate Queries
- **Problem**: `loadTransactionHistory()` runs during initial page render of `newindex()`, even when the user lands on `#tab=overview`. Furthermore, `Company` model is queried twice.
- **Solution**:
  1. Omit `$this->cart_transaction_history` from `newindex()`.
  2. Fetch transaction history strictly via `transactionsAjax` when the user clicks the "Transactions" tab.
  3. De-duplicate company model lookup into a single variable `$this->company`.
- **Risk**: User experiences a brief (100ms) spinner delay when clicking the "Transactions" tab for the first time.
- **Testing**: Land on `http://localhost/.../newindex#tab=overview`. Inspect network tab to verify no transaction history queries or payloads are generated until "Transactions" tab is clicked.

### Recommendation 5: Redis Caching for Static Master Package Catalog
- **Problem**: `top_up_coin_packages`, `coin_packages`, `coin_package_points`, and `payment_settings` are static master records queried directly from MySQL on every single user request.
- **Solution**: Cache static master datasets in Redis with a 24-hour TTL:
  ```php
  $operationalPackages = Cache::remember('master_coin_packages_operational', 86400, function () {
      return DB::table('coin_packages')...get();
  });
  ```
  Provide cache invalidation hooks whenever a Super Admin creates or modifies packages.
- **Risk**: Stale package prices or descriptions shown if admin update invalidation hooks fail.
- **Testing**: Update package details in Admin panel; confirm cache invalidates immediately. Verify 0 database queries for master packages on subsequent recruiter requests.

### Recommendation 6: Modularize Blade Template into Reusable Views
- **Problem**: `newindex.blade.php` is a monolithic ~121 KB template containing inline scripts, CSS, and multi-tab HTML.
- **Solution**: Refactor `newindex.blade.php` into modular Blade components:
  - `resources/views/service_company/subscription/partials/_wallet_header.blade.php`
  - `resources/views/service_company/subscription/partials/_overview_tab.blade.php`
  - `resources/views/service_company/subscription/partials/_modals.blade.php`
- **Risk**: Broken DOM selectors or broken scope if JavaScript event handlers rely on brittle IDs.
- **Testing**: Perform visual regression testing across desktop, laptop (1366x768), and 4K displays; test modal triggers for plan upgrades and top-ups.

---

## 4. 10,000+ Concurrent Recruiters Scaling Strategy

1. **Query Reduction Engine**:
   By combining Redis caching, lazy loading, and N+1 elimination, initial page load query count per user drops from **35+ queries down to 2-3 queries**. Total query volume under 10k concurrency drops from **350,000 queries/sec to ~25,000 indexed queries/sec**.
2. **Zero Web Worker Blocking**:
   Removing synchronous cURL calls ensures web worker execution time drops to <30ms, preventing worker pool exhaustion under heavy traffic spikes.
3. **Database Connection Safety**:
   Utilizing FrankenPHP / Laravel Octane with persistent Redis connections keeps MySQL connection counts well within safe capacity limits (<100 active connections).

---

## 5. Verification, Rollout & Rollback Strategy

### Verification Plan
- **Automated Tests**:
  - Run existing test suite: `php artisan test --filter=SubscriptionTest`
  - Write new feature test to assert tenant scoping and wallet calculations match expected totals.
- **Manual Verification**:
  - Validate Operational vs. Sales wallet balance counters.
  - Test Custom Quote lead generation modal (`customCheckoutSubscribe`).
  - Test Cart addition (`checkoutSubscribe`) and Razorpay checkout redirection.
  - Test PDF invoice receipt download (`receiptdownload`).

### Rollout Strategy
1. Apply database migrations to create indexes during low-traffic maintenance window.
2. Deploy backend service refactoring (`SubscriptionService`) and Redis cache layers.
3. Deploy frontend modular Blade views and JS updates.
4. Warm Redis cache for static package master records.

### Rollback Strategy
1. **Code Rollback**: `git checkout main` to revert controller and Blade views to V1.
2. **Database Rollback**: Execute migration rollback (`php artisan migrate:rollback --step=1`) to drop newly created indexes if write performance degrades.
3. **Cache Flush**: Run `php artisan cache:clear` if stale package data persists.

---

## 6. Business Logic & Tenancy Preservation Matrix

| Feature / Logic | V1 Implementation | V2 Implementation | Verification Method |
| :--- | :--- | :--- | :--- |
| **Operational & Sales Isolation** | Independent wallet balances & transaction queries | Retained exact event_type scoping (`operational` vs `sales`) | Assert wallet card totals match ledger sum |
| **Tenant Isolation** | Scoped via `auth()->user()->company_id` | Enforced strict `company_id` filters on all service queries | Security test with alternate `company_id` |
| **Custom Package Quote** | Ajax POST to `customCheckoutSubscribe` | Identical endpoint & SweetAlert modal flow | Submit custom quote request; verify log entry |
| **Receipt Download** | PDF download via `receiptdownload` | Identical route & response headers | Trigger PDF download; verify header content |
