# Offer Tracker Listing Page — V2 Performance Implementation Plan

> **Target Directory:** `docs/V2_offer_tracker`  
> **Related Audit:** [`docs/V2_offer_tracker/AUDIT.md`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/docs/V2_offer_tracker/AUDIT.md)  
> **Goal:** Optimize the Offer Tracker listing page to achieve sub-120ms response times, 0 N+1 database queries, and seamless scalability for **10,000+ concurrent recruiters**, while strictly preserving 100% of existing business logic and UI features.

---

## Technical Architecture Overview

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│                        V2 OPTIMIZED OFFER TRACKER ARCHITECTURE                         │
└────────────────────────────────────────────────────────────────────────────────────────┘

 [ GET /service_company/applicationoffer ]
        │
        ├── 1. ServiceCompanyApplicationOfferController@index
        │      ├── Remove $this->appCounter 6-table JOIN count (0 DB queries)
        │      ├── Remove initial redundant DB KPI aggregation (0 DB queries)
        │      └── Cache country reference dataset in DragonflyDB/Redis
        │
 [ AJAX GET /service_company/all-candidate-list?status_cus=5 ]
        │
        ├── 2. ServiceCompanyCvTrackerController@allcandidates
        │      ├── Single Consolidated Elasticsearch Search & Aggregation Query
        │      ├── Batch Job/Client Metadata Resolution (1 query for entire page)
        │      ├── Batch Confidential Access Check (1 query for non-admins, 0 for admins)
        │      └── Pass resolved metadata array into Blade view context
        │
        └── 3. View Layer Rendering (offer-tracker.blade.php)
               └── Zero DB queries executed inside @foreach loop (100% pre-resolved metadata)
```

---

## Detailed Step-by-Step Implementation Plan

### Phase 1: View-Layer N+1 Query Elimination (`offer-tracker.blade.php`)

#### Objective
Completely eliminate all per-row database queries (`companyDetailsByClient`, `jobDetails`, `confidentialAmontShow`) during Blade template rendering.

#### Implementation Steps
1. **Pre-Resolve Metadata in Controller (`ServiceCompanyCvTrackerController@allcandidates`):**
   * Extract all unique `jobId`s from the current page hits (`$jobIds = collect($data)->pluck('jobId')->filter()->unique()->values()->all()`).
   * **Batch Fetch Job Status & Client Company Names:**
     Query `jobs` left-joined with `clients` and `companies` once for all `jobIds` on the page:
     ```php
     $jobDetailsMap = [];
     if (!empty($jobIds)) {
         $jobRows = DB::table('jobs as j')
             ->leftJoin('clients as c', 'c.id', '=', 'j.client_id')
             ->leftJoin('companies as comp', 'comp.id', '=', 'j.company_id')
             ->whereIn('j.id', $jobIds)
             ->select('j.id as job_id', 'j.status', 'j.title', 'c.name as client_name', 'comp.name as company_name')
             ->get();
         foreach ($jobRows as $r) {
             $jobDetailsMap[$r->job_id] = $r;
         }
     }
     ```
   * **Batch Fetch Confidentiality Assignments:**
     Check confidentiality access once for the entire user session / page:
     ```php
     $assignedJobIds = [];
     $isAdmin = auth()->user()->hasRole('admin');
     if (!$isAdmin && !empty($jobIds)) {
         $assignedJobIds = DB::table('job_assign')
             ->whereIn('job_id', $jobIds)
             ->where('user_id', auth()->id())
             ->pluck('job_id')
             ->toArray();
     }
     ```
2. **Update Blade Template (`offer-tracker.blade.php`):**
   * Replace `GeneralMethod::companyDetailsByClient($row->jobId)` with pre-resolved `$jobDetailsMap[$row->jobId]->client_name ?? $row->client_name`.
   * Replace `GeneralMethod::jobDetails($row->jobId)->status` with `$jobDetailsMap[$row->jobId]->status ?? 'active'`.
   * Replace `GeneralMethod::confidentialAmontShow($row->jobId)` with `$isAdmin || in_array($row->jobId, $assignedJobIds)`.

---

### Phase 2: Optimize Initial Page Load (`ServiceCompanyApplicationOfferController@index`)

#### Objective
Reduce initial HTML page load time from ~400ms to <50ms by stripping unused count queries and redundant database calls.

#### Implementation Steps
1. **Remove Unused `$this->appCounter` Multi-Table JOIN Query:**
   * Delete lines 37–53 in [`ServiceCompanyApplicationOfferController.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyApplicationOfferController.php#L37-L53).
   * Tab counts and badge metrics are already dynamically populated via AJAX from Elasticsearch aggregations.
2. **Remove Synchronous MySQL KPI Aggregations:**
   * Remove lines 68–108 in `index()`. Initialize `$this->offerStats` and `$this->offerPipeline` as empty arrays or basic placeholders; the frontend JS `updateOfferCounts()` immediately replaces these metrics via AJAX upon page load.
3. **Cache Country Reference Dataset:**
   * Replace `$this->countries = DB::table('countries')->get();` with:
     ```php
     $this->countries = cache()->remember('ref_countries_list', 86400, function () {
         return DB::table('countries')->select('id', 'name', 'nicename', 'phonecode')->get();
     });
     ```

---

### Phase 3: Optimize Elasticsearch Queries & Aggregations (`ServiceCompanyCvTrackerController@allcandidates`)

#### Objective
Consolidate Elasticsearch search and aggregation requests into a single network payload, minimizing network roundtrips between PHP-FPM and Elasticsearch.

#### Implementation Steps
1. **Consolidate ES Aggregations:**
   * Embed `aggs` directly within the main candidate search request (`$params['body']['aggs']`) instead of issuing a second search call (`$countsResp`).
   * Structure aggregation blocks:
     * `by_acceptance`: Terms aggregation on `of_offer_acceptance`.
     * `pipeline`: Date-filtered terms aggregation for the pipeline metrics.
2. **Optimize Pagination & Track Total Hits:**
   * Set `'track_total_hits' => 10000` to avoid unnecessary deep hit counts beyond standard pagination limits.
   * Maintain zero-offset pagination with fixed page sizes (`size=10, 25, 50`).

---

### Phase 4: Database Schema & Composite Indexing

#### Objective
Ensure all remaining MySQL write and metadata read queries operate on indexed columns.

#### Migration / Composite Index Script (MySQL):
```sql
-- Composite index for tenant-scoped offer lookup and date filtering
ALTER TABLE `offer_job_applications` 
ADD INDEX `idx_company_deleted_created` (`company_id`, `deleted_at`, `created_at`);

-- Composite index for offer lifecycle status lookups
ALTER TABLE `offer_create_main_tds` 
ADD INDEX `idx_offer_status_candidate` (`offer_id`, `status`, `candidate_status`);

-- Composite index for job assign user lookups
ALTER TABLE `job_assign` 
ADD INDEX `idx_user_job` (`user_id`, `job_id`);
```

---

### Phase 5: High-Concurrency & Multi-Tenancy Strategy (10,000+ Recruiters)

1. **Strict Tenancy Scoping (Project Rule 3):**
   * Enforce `where('company_id', $company_id)` on all `DB::table()` batch queries.
2. **DragonflyDB / Redis Layering:**
   * Utilize tenant-level caching keys with user tags (`ats:offer_tracker:{company_id}:{user_id}:*`).
   * Automatic cache invalidation on offer status updates, salary changes, and candidate status changes.
3. **Response Compression:**
   * Enable gzip/brotli output buffer compression for rendered AJAX HTML payloads.

---

## File Modification Summary

| Component / File | Proposed Changes | Expected Gain |
| :--- | :--- | :--- |
| [`ServiceCompanyApplicationOfferController.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyApplicationOfferController.php) | Remove 6-table JOIN `$this->appCounter`, strip redundant DB KPI query, cache `countries`. | Initial page load reduced by **85%** (~50ms) |
| [`ServiceCompanyCvTrackerController.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php) | Batch-resolve `jobDetailsMap` and `assignedJobIds`, consolidate ES aggregations into 1 request. | AJAX response time reduced by **85%** (<120ms) |
| [`offer-tracker.blade.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/ajax_pages/offer-tracker.blade.php) | Replace per-row helper calls with pre-resolved array lookups. | **0 N+1 queries** in view template |
| `database/schema/mysql-schema.sql` | Add composite indexes on `offer_job_applications`, `offer_create_main_tds`, `job_assign`. | Eliminates MySQL full-table scans |

---

## Verification & Testing Plan

### 1. Automated Verification Commands
```bash
# Syntax and lint verification
php -l app/Http/Controllers/ServiceCompany/ServiceCompanyApplicationOfferController.php
php -l app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php
./vendor/bin/pint --test

# Verify route list for application offer routes
php artisan route:list --path=applicationoffer
```

### 2. Manual & Performance Verification
* **Query Count Audit:** Enable `DB::enableQueryLog()` during AJAX request execution and assert `$queryLog` length is **≤ 2 queries** total.
* **UI & Data Integrity Verification:**
  * Verify candidate name, photo/avatar, current designation, and bench badge display accurately.
  * Verify Job Title, Client Name, Client Type (Internal vs. Direct Client), and Job Status pills match original V1 logic.
  * Verify compensation amount, currency symbol, salary mode, employment type, and Confidential badge match original V1 rules.
  * Verify Offer Status badges (Pending, Offer Created, Offer Released, Approval Pending, Accepted, Rejected, Expired) and dates match.
  * Verify Joining date, time, and countdown timer.
