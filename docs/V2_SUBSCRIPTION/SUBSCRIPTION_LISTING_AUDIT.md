# Subscription & Wallet Listing Audit Report (V1)

## Executive Summary
This document provides a comprehensive technical audit of the **Subscription & Wallet Center Listing** page in JERO SaaS (`service_company/new-subscribe/newindex`). It evaluates the complete end-to-end request flow, database queries, N+1 query patterns, indexing strategy, external API interactions, server infrastructure, and concurrency bottlenecks under 10,000+ active recruiters.

---

## 1. Current Files and Architecture

### Primary Entry Point & Route
- **URL**: `http://localhost/git/JWMaincode28June2026/service_company/new-subscribe/newindex#tab=overview`
- **Route Name**: `service_company.subscribe.newindex`
- **Route File**: [routes/service.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/routes/service.php#L586)
- **Middleware Scope**: `auth`, `service_company` tenant middleware.

### Controller & Views
- **Controller Class**: [App\Http\Controllers\ServiceCompany\ManageSubscriptionController](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ManageSubscriptionController.php)
- **Target Method**: `newindex()` ([L144-L326](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ManageSubscriptionController.php#L144-L326))
- **Primary Blade View**: [resources/views/service_company/subscription/newindex.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/subscription/newindex.blade.php) (Size: ~121 KB)
- **AJAX Endpoint**: `service_company.transactions.subscriptionAjax` (`ManageSubscriptionController@transactionsAjax`) -> View: `service_company.ajax_pages.subscription_transactions`

### Data Flow Overview
```
Client Request (HTTP GET /service_company/new-subscribe/newindex)
  │
  ├──► Route: service.php (subscribe.newindex)
  │
  ├──► Controller: ManageSubscriptionController@newindex
  │     ├──► Auth Tenant context: company_id = auth()->user()->company_id
  │     ├──► DB Queries (29 to 50+ synchronous MySQL queries via DB facade)
  │     ├──► Outbound HTTP cURL calls to api.razorpay.com (inside loadTransactionHistory loop)
  │     └──► In-memory mapping & collection aggregations
  │
  └──► Render Blade Template (resources/views/service_company/subscription/newindex.blade.php)
        └──► jQuery client-side tab switching & AJAX pagination for transaction history
```

---

## 2. Query & Execution Audit

During a single page load of `newindex()`, the controller executes **between 29 and 50+ individual database queries**, along with synchronous external HTTP requests.

### Complete Query Breakdown

1. **Active Package Check**:
   `DB::table('package_active')->select('package_active.*')->where('package_active.company_id', '=', $company_id)->first();`
2. **Cart Items Count**:
   `DB::table('carts')->where('company_id','=',$company_id)->where('status','=',0)->count();`
3. **Operational Top-Up Master Packages**:
   `DB::table('top_up_coin_packages')->where('event_type','=','operational')->where('status','=',1)->get();`
4. **Sales Top-Up Master Packages**:
   `DB::table('top_up_coin_packages')->where('event_type','=','sales')->where('status','=',1)->get();`
5. **Company Model Lookup (Eloquent)**:
   `Company::withoutGlobalScope('company')->findOrFail($company_id);`
6. **Active Company Package (Eloquent Helper)**:
   `CompanyPackage::activePackage($company_id);`
7. **Operational Top-Up Logs**:
   `DB::table('top_up_logs')->select('top_up_logs.*')->leftJoin('carts','carts.id','=','top_up_logs.cart_id')->where('top_up_logs.event_type','=','operational')->where('top_up_logs.company_id','=',$company_id)->get();`
8. **Sales Top-Up Logs**:
   `DB::table('top_up_logs')->select('top_up_logs.*')->leftJoin('carts','carts.id','=','top_up_logs.cart_id')->where('top_up_logs.event_type','=','sales')->where('top_up_logs.company_id','=',$company_id)->get();`
9. **Transaction History Razorpay Sync Loop**:
   `DB::table('cart_transaction_history')->select('cart_transaction_history.*')->where('company_id','=',$company_id)->where('payment_gateway', 'razorpay')->where(function ($query) { $query->whereNull('payment_Id')->orWhere('payment_Id', '=', ''); })->orderByDesc('id')->limit(5)->get();`
10. **Payment Gateway Settings**:
    `DB::table('payment_settings')->select('payment_settings.*')->first();`
11. **[CRITICAL BOTTLENECK] Synchronous External cURL API Requests**:
    For each pending Razorpay transaction (up to 5), an HTTP cURL request is sent to `https://api.razorpay.com/v1/orders/{orderID}/payments` inside the web execution thread, followed by an UPDATE query per item:
    `DB::table('cart_transaction_history')->where('payment_orderId','=',$orderID)->update(['status'=>$payment['status']]);`
12. **Paginated Transaction History**:
    `DB::table('cart_transaction_history')->select('cart_transaction_history.*')->where('cart_transaction_history.company_id','=',$company_id)->orderByDesc('id')->paginate(50);`
13. **Duplicate Company Lookup**:
    `DB::table('companies')->select('*')->where('id', $company_id)->first();` (Duplicates Query #5).
14. **Package Active Features**:
    `DB::table('package_active')->join('package_invoice'...)...where('package_active.company_id', $company_id)->get();`
15. **Package Active Details**:
    `DB::table('package_active')->join('package_invoice'...)->join('packages'...)...where('package_active.company_id', $company_id)->first();`
16. **Current Coins - Operational Orders**:
    `DB::table('company_coins')->join('coin_orders'...)->join('coin_packages'...)->join('coin_package_types'...)->where('company_coins.company_id',$company_id)->where('company_coins.event_type','operational')->where('coin_orders.status',1)->get();`
17. **[N+1 Query Loop #1]**: Iterates through each operational coin order and queries `coin_order_events`:
    `DB::table('coin_order_events')->join('coin_events'...)->where('coin_order_id', $coinOrder->coin_order_id)->get();` (Executes $N$ times).
18. **Current Coins - Sales Orders**:
    `DB::table('company_coins')->join('coin_orders'...)...where('company_coins.company_id',$company_id)->where('company_coins.event_type','sales')->where('coin_orders.status',1)->get();`
19. **[N+1 Query Loop #2]**: Iterates through each sales coin order and queries `coin_order_events`:
    `DB::table('coin_order_events')->join('coin_events'...)->where('coin_order_id', $coinOrder->id)->get();` (Executes $M$ times).
20. **Operational Coin Packages Master List**:
    `DB::table('coin_packages')->join('coin_package_types'...)->where('coin_packages.event_type', '=', 'operational')->get();`
21. **[N+1 Query Loop #3]**: Iterates through each operational package and queries `coin_package_points`:
    `DB::table('coin_package_points')->where('coin_package_points.coin_package_id', $row->id)->get();` (Executes $P$ times).
22. **Sales Coin Packages Master List**:
    `DB::table('coin_packages')->join('coin_package_types'...)->where('coin_packages.event_type', '=', 'sales')->get();`
23. **[N+1 Query Loop #4]**: Iterates through each sales package and queries `coin_package_points`:
    `DB::table('coin_package_points')->where('coin_package_points.coin_package_id', $row->id)->get();` (Executes $Q$ times).
24. **Latest Operational Credit Ledger Entry**:
    `DB::table('coin_transection_logs')->where('event_type','=','operational')->where('transection_type','=','credit')->where('company_id', $company_id)->orderByDesc('id')->first();`
25. **Latest Operational Company Coin Balance**:
    `DB::table('company_coins')->where('event_type','=','operational')->where('company_id', $company_id)->orderByDesc('id')->first();`
26. **Operational Total Credited Sum**:
    `DB::table('coin_transection_logs')->where('company_id', $company_id)->where('event_type', 'operational')->where('transection_type', 'credit')->sum('coins');`
27. **Latest Sales Credit Ledger Entry**:
    `DB::table('coin_transection_logs')->where('event_type','=','sales')->where('transection_type','=','credit')->where('company_id', $company_id)->orderByDesc('id')->first();`
28. **Latest Sales Company Coin Balance**:
    `DB::table('company_coins')->where('event_type','=','sales')->where('company_id', $company_id)->orderByDesc('id')->first();`
29. **Sales Total Credited Sum**:
    `DB::table('coin_transection_logs')->where('company_id', $company_id)->where('event_type', 'sales')->where('transection_type', 'credit')->sum('coins');`

---

## 3. N+1 Query Audit

| N+1 Issue | File & Location | Description | Severity |
| :--- | :--- | :--- | :--- |
| **Coin Order Events (Operational)** | `ManageSubscriptionController.php` (L182-L190) | `.map()` callback executes a DB query on `coin_order_events` for every active operational coin order. | **HIGH** |
| **Coin Order Events (Sales)** | `ManageSubscriptionController.php` (L201-L209) | `.map()` callback executes a DB query on `coin_order_events` for every active sales coin order. | **HIGH** |
| **Coin Package Points (Operational)** | `ManageSubscriptionController.php` (L217-L224) | `.map()` callback executes a DB query on `coin_package_points` for every operational coin package in master catalog. | **MEDIUM** |
| **Coin Package Points (Sales)** | `ManageSubscriptionController.php` (L235-L242) | `.map()` callback executes a DB query on `coin_package_points` for every sales coin package in master catalog. | **MEDIUM** |

---

## 4. Relevant vs. Unused Data

1. **Unused / Premature Transaction History Payload**:
   `$this->cart_transaction_history` is loaded in `newindex()` on initial view render, even though the user lands on `#tab=overview` by default. The transaction history tab is hidden initially, and tab pagination already uses an AJAX endpoint (`transactionsAjax`). Loading it on initial render wastes memory and time.
2. **Duplicate Company Record Retrieval**:
   The controller queries `Company::withoutGlobalScope('company')->findOrFail($company_id)` at L156 and then re-queries `DB::table('companies')->where('id', $company_id)->first()` at L166.
3. **Un-cached Static Master Data**:
   Master tables like `top_up_coin_packages`, `coin_packages`, `coin_package_points`, and `payment_settings` change very infrequently, yet are fetched directly from MySQL on every page request.

---

## 5. Database Index Audit

Analysis against `database/schema/mysql-schema.sql`:

| Table Name | Inspected Indexes in Schema | Missing Indexes for Subscription Queries | Scan Impact |
| :--- | :--- | :--- | :--- |
| `cart_transaction_history` | `PRIMARY KEY (id)` | Missing `(company_id)`, `(company_id, payment_gateway, payment_Id)` | **FULL TABLE SCAN** on every history lookup & Razorpay sync check |
| `coin_order_events` | `PRIMARY KEY (id)` | Missing `(coin_order_id)` | **FULL TABLE SCAN** inside N+1 loops for every coin order |
| `coin_package_points` | `PRIMARY KEY (id)` | Missing `(coin_package_id)` | **FULL TABLE SCAN** inside N+1 loops for every package display |
| `top_up_logs` | `PRIMARY KEY (id)` | Missing `(company_id, event_type)` | **FULL TABLE SCAN** on top-up logs retrieval |
| `package_active` | `PRIMARY KEY (id)` | Missing `(company_id)` | **FULL TABLE SCAN** on active package checks |
| `carts` | `PRIMARY KEY (id)` | Missing `(company_id, status)` | **FULL TABLE SCAN** on active cart count checks |
| `company_coins` | `PRIMARY (id)`, `idx_company_coins_company_id`, `idx_company_coins_company_event` | Has multi-column index `(company_id, event_type)` | INDEX SCAN (Sufficient for current scope) |
| `coin_transection_logs` | `PRIMARY (id)`, `idx_comp_event_created (company_id, event_type, created_at)` | Has multi-column index `(company_id, event_type, created_at)` | INDEX SCAN (Sufficient for current scope) |

---

## 6. Infrastructure, Elasticsearch, Redis & Queue Audit

- **Elasticsearch Usage**: None. All subscription, wallet balance, and transaction history reads query MySQL directly.
- **Redis / Cache Usage**: None. No caching is implemented for master package catalogs or company balance aggregations.
- **Queue / Background Worker Usage**: None. Razorpay payment status polling is executed synchronously in the HTTP request thread (`curl_exec`) inside `loadTransactionHistory()`. If Razorpay API responds slowly (or times out), the web server worker blocks completely.

---

## 7. Server Environment & Resource Audit

### Server Environment (User Provided Specs vs Local Verification)
- **Operating System**: Ubuntu 24.04 LTS (Production Server) / Windows Dev Environment
- **Control Panel**: aaPanel 8.0.3
- **CPU Cores**: 8 Virtual Cores
- **RAM**: 31.34 GB Total
- **Disk Space**: 386.4 GB
- **Web Server**: Nginx / FrankenPHP / Octane (as configured in JERO stack)
- **PHP**: PHP 8.2+ with OPcache enabled
- **Database**: MySQL / MariaDB (InnoDB engine)

### Performance & Resource Bottlenecks
- **Response Latency**: *Not measured* (requires APM tool / Xdebug profile on production load).
- **Peak Memory Usage**: *Not measured*.
- **Database Connection Footprint**: High connection pool usage due to ~35+ queries per user page load.

---

## 8. 10,000+ Concurrent Recruiters Scaling Analysis

If 10,000 recruiters access the Subscription & Wallet Center concurrently:

1. **MySQL Connection Pool Exhaustion**:
   $10,000 \text{ concurrent users} \times 35 \text{ queries per load} = 350,000 \text{ queries/sec}$. Without caching or indexing, MySQL connection pools will crash with `SQLSTATE[HY000] [1040] Too many connections`.
2. **Blocking External I/O Cascades**:
   Synchronous `curl_exec` calls to Razorpay API in web worker threads will lock PHP-FPM / Octane workers. A single 2-second Razorpay timeout will exhaust all available web workers within seconds, causing site-wide HTTP 504 Gateway Timeouts.
3. **CPU Thrashes from Un-indexed Joins**:
   Sequential full table scans on `cart_transaction_history` and `coin_order_events` will cause 100% CPU utilization across all 8 cores.

---

## 9. Categorized Issues

### Confirmed Problems
1. **Synchronous External API Calls**: Outbound cURL requests to Razorpay run inside `loadTransactionHistory()` during page requests.
2. **4 Active N+1 Query Loops**: Iterative queries for `coin_order_events` and `coin_package_points`.
3. **Missing Critical Database Indexes**: 6 core subscription tables (`cart_transaction_history`, `coin_order_events`, `coin_package_points`, `top_up_logs`, `package_active`, `carts`) lack foreign key indexes.
4. **Redundant Initial Load**: Transaction history table is paginated and rendered on initial view load despite `#tab=overview` being the default tab.
5. **Duplicate Queries**: `companies` table queried twice in the same controller method.
6. **Zero Redis Caching**: Static package master data fetched repeatedly from MySQL.

### Potential Problems
1. Memory bloat on Blade template rendering due to large 121 KB template with embedded inline scripts and un-minified markup.
2. Database deadlock risk if concurrent coin debit transactions update `company_coins` without strict row-level locking.

### Not Measured
1. Production response time in milliseconds.
2. Exact memory consumption (MB) per request under PHP 8.2.
3. Database IOPS under peak concurrent write loads.

---

## 10. Non-Negotiable Business Logic & Data Constraints

The following core business logic **MUST REMAIN UNCHANGED** in V2:
1. **Operational vs. Sales Wallet Segregation**: Strict separation of coin ledgers, balances, top-up packages, and usage rules between Operational and Sales wallets.
2. **Transaction Ledger Integrity**: Historical transaction history entries, transaction types (`credit`/`debit`), and payment gateway metadata must not be altered or purged.
3. **Tenant Security Scoping**: Strict isolation where `company_id` filters ensure recruiters only view their own company's wallet data.
4. **Razorpay & Custom Package Checkout Flow**: Cart creation, coupon application rules, custom quote lead triggers, and PDF receipt downloads must function identically.
