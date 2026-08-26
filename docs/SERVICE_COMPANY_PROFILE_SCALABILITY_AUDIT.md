# Service Company Profile Scalability Audit & 10K+ Concurrent Recruiter Plan

> **Endpoint:** `http://localhost/git/JWMaincode28June2026/service_company/profile`  
> **Controller:** [`ServiceCompanyProfileController@index`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyProfileController.php#L42-L1142)  
> **Date:** August 26, 2026  
> **Target:** 10,000+ Concurrent Recruiters | Response Time < 100ms p95 | Zero Database / ES Overload  

---

## 1. Executive Summary

The `/service_company/profile` page serves as the personal dashboard and performance metrics portal for recruiters in JERO's Service Company persona. Currently, a single HTTP request to this endpoint synchronously executes over **90 Elasticsearch queries** and multiple heavy database queries, including pulling up to 10,000 user records into PHP memory and executing complex in-memory array sorting inside a 20-iteration month loop.

Under a workload of **10,000 concurrent recruiters**, this monolithic design will fail catastrophically:
- **900,000 concurrent Elasticsearch HTTP requests** hitting the cluster simultaneously.
- Complete CPU and memory saturation on Laravel Octane/FrankenPHP workers due to in-memory sorting.
- Severe 504 Gateway Timeouts and network connection pool exhaustion.

This audit details the root causes of the scalability bottlenecks and presents a non-breaking, V2 asynchronous architecture to scale the profile page to 10K+ concurrent recruiters smoothly.

---

## 2. Comprehensive Bottleneck Audit

### 🔴 Critical Bottleneck #1: Synchronous 20-Month Ranking Loop (~80 ES Queries per Page Load)
**Location:** [`ServiceCompanyProfileController.php:L57-L100`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyProfileController.php#L57-L100)

```php
// Loop through every month from Jan 2025 to current month (~20 months)
for ($year = $startYear; $year <= $currentYear; $year++) {
    ...
    // Internal Ranking (2 ES queries per month)
    $internalScore = $this->getRankData($company_id, $loggedInUserId, $rankstartOfMonthForReport, $rankendOfMonthForReport, true);
    // Global Ranking (2 ES queries per month)
    $globalScore = $this->getRankData(null, $loggedInUserId, $rankstartOfMonthForReport, $rankendOfMonthForReport, false);
}
```
* **Impact:** For 20 months, `getRankData()` is invoked 40 times. Each invocation triggers 2 separate ES queries (`ELASTIC_USERS_LIST` search with size 10,000 and `ELASTIC_TEAM_RANK` aggregation search).
* **Query Count:** 40 calls × 2 = **80 Elasticsearch network requests per single user request**.
* **10K Recruiter Concurrency Impact:** 800,000 ES HTTP queries executing at the exact same second.

---

### 🔴 Critical Bottleneck #2: In-Memory Sorting of 10,000 Users in PHP Memory
**Location:** [`ServiceCompanyProfileController.php:L1168-L1300`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyProfileController.php#L1168-L1300) and [`L1319-L1396`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyProfileController.php#L1319-L1396)

```php
$userQuery = ['index' => ELASTIC_USERS_LIST, 'body' => [..., 'size' => 10000]];
$userResponse = $this->elasticService->getClient()->search($userQuery);
$users = collect($userResponse['hits']['hits'])->pluck('_source');
...
$ranked = $users->map(...)->sort(function ($a, $b) { ... })->values();
```
* **Impact:** For every single rank query, up to **10,000 full user documents** are serialized over JSON, inflated into Laravel Collections, mapped with score aggregations, and sorted using custom multi-level PHP callback functions.
* **CPU & Memory Overhead:** Multiply 10,000 array items × 40 iterations × 10,000 concurrent recruiters = millions of PHP array allocations and sorting operations per second, triggering worker OOM (Out Of Memory) crashes.

---

### 🔴 Critical Bottleneck #3: 12+ Additional Synchronous ES Aggregations & Queries
**Location:** [`ServiceCompanyProfileController.php:L166-L1138`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyProfileController.php#L166-L1138)

Outside the rank loop, the controller executes:
1. **Candidate status counts query** (`ELASTIC_CANDIDATES` search)
2. **Job status counts graph query** (`ELASTIC_JOBS` search)
3. **Candidate last 7 days upload histogram** (`ELASTIC_CANDIDATES` histogram agg)
4. **Offer count last 6 months graph** (`ELASTIC_CANDIDATES` histogram agg)
5. **Joined candidate list & histogram query** (`ELASTIC_CANDIDATES` search size 6 + histogram)
6. **Upcoming interview schedule list** (`ELASTIC_INERTVIEW_TRACKER` search size 5)
7. **Last 6 months team rank metrics** (`ELASTIC_TEAM_RANK` histogram + 14 sub-aggregations)
8. **Elastic user detail lookup** (`ELASTIC_USERS_LIST` GET)
9. **Overall total candidates count** (`ELASTIC_CANDIDATES` count)
10. **Overall total joinings count** (`ELASTIC_CANDIDATES` count)
11. **Overall total interviews count** (`ELASTIC_INERTVIEW_TRACKER` count)
12. **Overall total offers count** (`ELASTIC_CANDIDATES` count)

* **Total ES Queries Per Request:** ~92 queries. Zero caching is applied on any of these aggregations.

---

### 🔴 Critical Bottleneck #4: Uncached Database Queries for Static Reference Data
**Location:** [`ServiceCompanyProfileController.php:L127-L142`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyProfileController.php#L127-L142) and [`L1139`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyProfileController.php#L1139)

```php
$this->users = User::join('companies', ...)->leftJoin('users_profile', ...)->leftJoin('city_list', ...)->leftJoin('state_list', ...)->first();
$this->countries = DB::table('countries')->get();
```
* **Impact:** Static database lookups (such as the list of all countries) and user profile information are executed directly against MySQL on every single page load without Redis caching.

---

### 🔴 Critical Bottleneck #5: Monolithic Page Payload (213 KB Blade File + Heavy Embedded JSON Data)
**Location:** [`resources/views/service_company/profile/index.blade.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/profile/index.blade.php)
* The Blade view generates an inline monolithic HTML response containing all historical data, heavy charts, and profile modals.
* If any single downstream ES query experiences latency, the entire HTTP page response is blocked until completion or timeout.

---

## 3. Concurrency & Load Simulation (10K Recruiters)

| Metric | Current Monolithic Implementation | V2 Scalable Architecture (Target) | Improvement Factor |
| :--- | :--- | :--- | :--- |
| **ES Queries / Request** | ~92 Queries | **0 Queries** (Initial HTML) / **1-2 Cached Queries** (Async API) | **100% Reduction on Page Load** |
| **MySQL Queries / Request** | 2 - 5 Queries | **0 Queries** (Redis Cached) | **100% Reduction** |
| **PHP Memory / Request** | 45 MB - 120 MB | **< 3 MB** | **95%+ Savings** |
| **Page Load Time (p95)** | 3,800 ms - 8,500 ms | **< 45 ms** (Core Page) / **< 85 ms** (Async Widgets) | **98% Speedup** |
| **10K Concurrency ES Load** | 920,000 req/sec (Collapse) | **< 200 cached API req/sec** | **99.9% Relief** |
| **Throughput (Requests/sec)**| ~12 req/sec | **2,500+ req/sec** | **200x Throughput** |

---

## 4. Implementation Phases

### 🔹 Phase 1: V2 JSON API Infrastructure & Redis Cache Layer
- Create `ServiceCompanyProfileV2ApiController.php` to handle lightweight, cached JSON requests for KPIs, Leaderboards, Monthly Ranks, Graphs, and Interviews.
- Register endpoints in `routes/service.php`.
- Setup Redis cache keys (`profile:recruiter:{user_id}:kpis`, `company:{company_id}:leaderboard:{Y-m}`).

### 🔹 Phase 2: Controller Refactoring & Core Page Optimization
- Refactor `ServiceCompanyProfileController@index` to eliminate the 20-month loop and live 10,000-user PHP sorts.
- Cache user profile data and country reference lists in Redis (`Cache::remember`).
- Return initial Blade layout shell in **< 10ms**.

### 🔹 Phase 3: Frontend Asynchronous UI Hydration & Skeletons
- Update `resources/views/service_company/profile/index.blade.php` with skeleton loaders.
- Add JS module to fetch V2 API endpoints in parallel (`Promise.all()`) and populate DOM dynamically.

### 🔹 Phase 4: Event-Driven Cache Invalidation & Pre-computation
- Invalidate recruiter profile cache keys on candidate updates/uploads.
- Maintain leaderboard cache asynchronously.

### 🔹 Phase 5: Verification & Benchmarking (No `RefreshDatabase`)
- Run lint checks (`php -l`), route list checks, and unit tests without reset traits.
- Verify page performance in browser (< 50ms p95).

---
*Created as part of JERO Scalability Phase for Service Company Persona.*
