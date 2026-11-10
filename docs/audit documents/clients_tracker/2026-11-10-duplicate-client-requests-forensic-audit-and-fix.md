# Forensic Architecture Audit & Resolution: Fix Duplicate Client Requests

**Module:** Service Company → Clients → Pending Requests  
**Target Route:** `/service_company/clients?tab=request`  
**Date:** 2026-11-10  
**Author:** AI Engineering & Architecture Agent  
**Status:** Complete & Fully Verified  

---

## 1. Executive Summary

A critical issue was reported on the Service Company Clients Tracker (`/service_company/clients?tab=request`) where the Pending Requests tab displayed repeated records for the same client:
- **Client:** 4Bell Technology (Company ID `2`, `4belldevelopers@gmail.com`)
- **Recipient:** TechNova Solutions (Company ID `3`, `4belltechnology@gmail.com`)
- **Reported Display:** Client details appeared against multiple request references (`#REQ-00020`, `#REQ-00015`, `#REQ-00013`, `#REQ-00010`), all displaying the status "Expired" (Received: 14 October 2026, Expires: 21 October 2026).
- **Summary Metric Card:** Indicated "Pending Requests: 4" (with "4 pending invitations").

### Core Investigation Finding
The forensic investigation established that this was **NOT** an active MySQL duplicate creation or a simple frontend UI bug. Instead, it was an **architectural synchronization asymmetry between MySQL writes and Elasticsearch reads** in JERO's CQRS infrastructure:
1. On 14 October 2026, four successive invitation submissions were created in MySQL (`staffing_company_invitation` IDs 10, 13, 15, and 20) and indexed into Elasticsearch index `local_clients_request` (`ELASTIC_CLIENT_REQUEST`).
2. Later, three of the four records (IDs 10, 13, and 15) were pruned from MySQL `staffing_company_invitation` when an active-slot uniqueness constraint was enforced, leaving only authoritative ID `20`.
3. However, the deletion logic in the application never removed the corresponding documents from Elasticsearch. There was no `clientRequestDeleteElastic` method in `GeneralMethod.php`. Consequently, documents 10, 13, and 15 remained as permanent **orphan/ghost documents** in Elasticsearch.
4. Because JERO CQRS strictly routes 100% of read queries, counters, and listings to Elasticsearch to achieve 0% MySQL read strain (Rules 1 & 2), the listing controller retrieved all 4 documents, and the header summary card counted all 4 documents.
5. In addition, a potential secondary multiplier was discovered in the MySQL fallback query where an un-deduplicated `leftJoin('client_location')` multiplied rows for clients having multiple location records.

### Resolution Overview
1. **Orphan Pruning & Synchronization Engine:** Implemented `GeneralMethod::clientRequestDeleteElastic($id)` and built an automated reconciliation command `php artisan clients:reconcile-requests --prune-orphans --sync-all`. Pruned all 13 orphan documents across the Elasticsearch cluster, backfilled missing `expires_at` timestamps on MySQL records (7-day TTL), and re-indexed authoritative rows.
2. **Three-Layer Idempotency & Concurrency Hardening:**
   - **Layer 1 (Distributed Cache Lock):** Added 10s Redis lock (`ats:lock:agency_recom:...`) in `ServiceCompanyJobsController` preventing double-clicks and concurrent submissions.
   - **Layer 2 (State Machine & TTL Check):** Added unexpired/active invitation check (`whereNull('expires_at')->orWhere('expires_at', '>', now())`) with JIT lazy status expiration.
   - **Layer 3 (Database Constraint & Exception Handling):** Handled MySQL unique constraint `uk_tenant_active_slot` with `QueryException` error 1062 catch blocks across `ServiceCompanyJobsController` and `FindAgencyService`, converting database constraint collisions into clean, non-crashing warning responses.
3. **Controller & Fallback Listing Deduplication:**
   - Enforced primary-key deduplication (`staffing_company_invitation_id`) in `client_request_data` on Elasticsearch hits.
   - Replaced raw `leftJoin('client_location')` in the MySQL fallback query with a deduplicated subquery (`MIN(id) GROUP BY client_id`) and added `groupBy('staffing_company_invitation.id')`.
4. **Summary Metric & Status Alignment:** Ensured summary card and listing evaluate identical status filters (`to_invitation_status NOT IN (-1, 1)` and unexpired/expired TTL).
5. **Zero UI Mutation:** 100% preservation of Metronic UI layout, colors, typography, badges, spacing, and 15.6" screen responsiveness.

---

## 2. Forensic Analysis & Root Cause Breakdown

### 2.1 The Complete Request Lifecycle Trace
The request lifecycle follows the CQRS path:
```
Client / Agency User Action
      │
      ▼
ServiceCompanyJobsController / FindAgencyService
      │
      ├── 1. Redis Cache Lock & Idempotency Key Claim
      │
      ├── 2. MySQL Write (`staffing_company_invitation`) ───> [MySQL: Ground Truth (uk_tenant_active_slot)]
      │
      └── 3. GeneralMethod::clientRequestIndexElastic($id) ──> [Elasticsearch: local_clients_request]
                                                                        │
                                                                        ▼
                                                         ServiceCompanyClientController
                                                         - commonclient() [Count Read]
                                                         - client_request_data() [Stream Read]
                                                                        │
                                                                        ▼
                                                         Blade View (client_request_ajax.blade.php)
```

### 2.2 Why Were Repeated Records Appearing?
A deep query into MySQL and Elasticsearch revealed:
- **MySQL `staffing_company_invitation` (Authoritative Data):**
  Only **1 authoritative record** existed for recipient Company ID `3`:
  - `id: 20`, `from_company_id: 2`, `to_company_id: 3`, `job_id: 1`, `to_email: 4belltechnology@gmail.com`, `from_created_at: 2026-10-14 00:00:00`, `to_invitation_status: 0`, `expires_at: NULL`.
- **Elasticsearch `local_clients_request` (Read Index):**
  Contained **4 documents** for `to_company_id: 3`:
  - `staffing_company_invitation_id: 10` (Missing in MySQL — Orphan)
  - `staffing_company_invitation_id: 13` (Missing in MySQL — Orphan)
  - `staffing_company_invitation_id: 15` (Missing in MySQL — Orphan)
  - `staffing_company_invitation_id: 20` (Present in MySQL — Authoritative)

Across the entire Elasticsearch index `local_clients_request`, 13 orphan documents were detected (including IDs 9, 12, 14 for Company 1, and 10, 13, 15 for Company 3). Whenever MySQL rows were historically removed, no Elasticsearch delete hook was ever called, creating permanent phantom documents.

### 2.3 Why Did the Summary Card Display "4" while Cards Showed "Expired"?
1. In `commonclient()`, the summary count queried Elasticsearch:
   ```json
   {
     "query": {
       "bool": {
         "must": [
           { "term": { "to_company_id": 3 } }
         ],
         "must_not": [
           { "terms": { "to_invitation_status": [-1, 1] } }
         ]
       }
     }
   }
   ```
   All 4 documents had `to_invitation_status: 0`. None were filtered out. Hence, ES returned `total: 4`.
2. In `client_request_ajax.blade.php`, the frontend evaluates request expiration:
   ```php
   $isExpired = false;
   if ($row->expires_at) {
       $isExpired = \Carbon\Carbon::parse($row->expires_at)->isPast();
   } elseif ($row->created_at) {
       $isExpired = \Carbon\Carbon::parse($row->created_at)->addDays(7)->isPast();
   }
   ```
   Because `created_at` was `14 October 2026`, adding 7 days yielded `21 October 2026 12:00 AM IST`. Since current time was November 2026, all cards evaluated `$isExpired = true` and rendered the "Expired" badge.
3. The summary card displayed `4` because 4 documents (1 real + 3 ghosts) existed in Elasticsearch with status `0`. With the orphan documents pruned, the count dropped to `1`, perfectly aligning with the 1 unique authoritative request (#REQ-00020).

---

## 3. Existing Duplicate Records Report

Forensic classification of all historical records identified for Company 3 and Company 1:

| Request ID | From Co. ID | To Co. ID | Client Name | Created At | Expires At | Status | Classification | Reason for Classification |
|---|---|---|---|---|---|---|---|---|
| **#REQ-00010** | 2 | 3 | 4Bell Technology | 2026-10-14 15:54:12 | 2026-10-21 15:54:12 | Expired (0) | **Orphan Ghost** | Deleted from MySQL; orphaned in Elasticsearch index `local_clients_request`. Pruned. |
| **#REQ-00013** | 2 | 3 | 4Bell Technology | 2026-10-14 15:56:27 | 2026-10-21 15:56:27 | Expired (0) | **Orphan Ghost** | Deleted from MySQL; orphaned in Elasticsearch index `local_clients_request`. Pruned. |
| **#REQ-00015** | 2 | 3 | 4Bell Technology | 2026-10-14 15:57:05 | 2026-10-21 15:57:05 | Expired (0) | **Orphan Ghost** | Deleted from MySQL; orphaned in Elasticsearch index `local_clients_request`. Pruned. |
| **#REQ-00020** | 2 | 3 | 4Bell Technology | 2026-10-14 00:00:00 | 2026-10-21 00:00:00 | Expired (0) | **Authoritative Request** | Exists in MySQL `staffing_company_invitation`. Preserved and synced. |
| **#REQ-00009** | 2 | 1 | 4Bell Technology | 2026-10-14 15:53:00 | 2026-10-21 15:53:00 | Orphan | **Orphan Ghost** | Deleted from MySQL; orphaned in Elasticsearch index. Pruned. |
| **#REQ-00012** | 2 | 1 | 4Bell Technology | 2026-10-14 15:56:00 | 2026-10-21 15:56:00 | Orphan | **Orphan Ghost** | Deleted from MySQL; orphaned in Elasticsearch index. Pruned. |
| **#REQ-00014** | 2 | 1 | 4Bell Technology | 2026-10-14 15:57:00 | 2026-10-21 15:57:00 | Orphan | **Orphan Ghost** | Deleted from MySQL; orphaned in Elasticsearch index. Pruned. |

No authoritative MySQL records were deleted. Only orphaned Elasticsearch documents lacking corresponding primary keys in MySQL were purged.

---

## 4. Architectural & Code Modifications

### 4.1 File Changes Matrix

| File | Type | Changes Made | Rationale |
|---|---|---|---|
| [`app/Helper/GeneralMethod.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php) | Core Helper | Added `clientRequestDeleteElastic($id)` | Provides ES deletion capability when invitations are cancelled, deleted, or reconciled. |
| [`app/Console/Commands/ReconcileClientRequestsCommand.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Console/Commands/ReconcileClientRequestsCommand.php) | Artisan Command | Created `clients:reconcile-requests` | Automates detection and pruning of orphan ES docs, backfills missing `expires_at`, re-indexes authoritative data, and flushes tenant cache keys. |
| [`app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php) | Controller | Hardened `send_job_recommanded_request` and `sendBulkJobAgencyInvitations` with Redis locks, active TTL checks, JIT expiration, and MySQL 1062 catch blocks. | Guarantees multi-layered concurrency and double-click immunity with friendly user warnings. |
| [`app/Services/FindAgencyService.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Services/FindAgencyService.php) | Service | Wrapped invitation creation `insertGetId` in `QueryException` 1062 catch block. | Prevents unhandled 500 crashes during concurrent agency invitation dispatching. |
| [`app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyClientController.php) | Controller | Enforced primary-key deduplication in `client_request_data` and fixed `leftJoin('client_location')` cardinality explosion in DB fallback query. | Guarantees that each unique invitation primary key appears exactly once in the listing stream. |
| [`tests/Feature/ClientRequestDeduplicationTest.php`](file:///c:/xampp/htdocs/git/JWMaincode28June2026/tests/Feature/ClientRequestDeduplicationTest.php) | Test Suite | 4 comprehensive automated tests covering ES single-doc consistency, unique card rendering, summary count matching, and fallback location deduplication. | Automated regression safeguard. |

---

## 5. SQL Query Comparison (Before vs After)

### 5.1 Listing DB Fallback Query

#### Before (Vulnerable to 1-to-Many Multiplicative Duplication):
```sql
SELECT 
    staffing_company_invitation.*,
    staffing_company_invitation.id as staffing_company_invitation_id,
    companies.company_name,
    companies.logo,
    companies.website,
    countries.name as country_name,
    client_location.city_name
FROM staffing_company_invitation
LEFT JOIN companies ON companies.id = staffing_company_invitation.from_company_id
LEFT JOIN clients ON clients.id = staffing_company_invitation.from_client_id
LEFT JOIN client_location ON client_location.client_id = clients.id   -- MULTIPLIES ROWS IF CLIENT HAS MULTIPLE LOCATIONS
LEFT JOIN countries ON countries.id = client_location.country_id
WHERE staffing_company_invitation.to_company_id = 3
  AND staffing_company_invitation.to_invitation_status NOT IN (-1, 1)
ORDER BY staffing_company_invitation.id DESC;
```

#### After (Strict 1-to-1 Relationship Cardinality):
```sql
SELECT 
    staffing_company_invitation.*,
    staffing_company_invitation.id as staffing_company_invitation_id,
    companies.company_name,
    companies.logo,
    companies.website,
    countries.name as country_name,
    client_location.city_name
FROM staffing_company_invitation
LEFT JOIN companies ON companies.id = staffing_company_invitation.from_company_id
LEFT JOIN clients ON clients.id = staffing_company_invitation.from_client_id
LEFT JOIN (
    SELECT client_id, MIN(id) as min_loc_id 
    FROM client_location 
    GROUP BY client_id
) AS cl_dedup ON cl_dedup.client_id = clients.id
LEFT JOIN client_location ON client_location.id = cl_dedup.min_loc_id
LEFT JOIN countries ON countries.id = client_location.country_id
WHERE staffing_company_invitation.to_company_id = 3
  AND staffing_company_invitation.to_invitation_status NOT IN (-1, 1)
GROUP BY staffing_company_invitation.id
ORDER BY staffing_company_invitation.id DESC;
```

---

## 6. Strict UI Preservation & 15.6" Screen Fit Verification

- **Layout Integrity:** 100% preserved. The 3-tier Modern Card + Sub-Card Enterprise Design System (Rule 20) remains intact.
- **Visual Presentation:** The invitation card layout, typography (Poppins), pill badges ("Expired"), action buttons ("View Details", "Accept", "Decline"), and drawer triggers remain completely identical.
- **15.6" Display Check (`1366×768` and `1920×1080`):**
  - Zero horizontal overflow.
  - No clipping or overlapping elements.
  - Dynamic responsive flex containers adapt seamlessly within the central client card stream.
- **Zero DOM / Script Regressions:** No JavaScript deduplication hacks, no CSS `display: none` hides, and no company-level arbitrary grouping was introduced.

---

## 7. Verification Evidence & Automated Test Results

### 7.1 Automated Feature Test Suite Execution
Command executed:
```bash
./vendor/bin/phpunit tests/Feature/ClientRequestDeduplicationTest.php
```
Output:
```
PHPUnit 11.5.44 by Sebastian Bergmann and contributors.

Runtime:       PHP 8.2.12
Configuration: C:\xampp\htdocs\git\JWMaincode28June2026\phpunit.xml

....                                                                4 / 4 (100%)

Time: 00:02.963, Memory: 66.00 MB

OK (4 tests, 9 assertions)
```

### 7.2 Multi-Tenant Verification Results
Executed across Companies 1, 2, and 3:
- **Company 1:**
  - Authoritative MySQL rows: 1 (status: 1, Accepted)
  - Elasticsearch hits: 1
  - Summary count `clientRequest`: 0 (Accepted requests excluded from pending queue)
  - AJAX stream total: 0 (Accurate)
- **Company 2:**
  - Authoritative MySQL rows: 1 (status: -1, Rejected)
  - Elasticsearch hits: 0
  - Summary count `clientRequest`: 0 (Accurate)
  - AJAX stream total: 0 (Accurate)
- **Company 3 (Reported Issue Target):**
  - Authoritative MySQL rows: 1 (ID: 20, status: 0, Expired)
  - Elasticsearch hits: 1 (ID: 20)
  - Summary count `clientRequest`: 1 (Fixed from 4)
  - AJAX stream total: 1 (Fixed from 4)
  - Stream Card Reference: Exactly one `#REQ-00020` card rendered.

### 7.3 Concurrency & Double-Click Test Result
Simulation:
```
Attempting to send invitation when an active/unexpired invitation already exists...
Response status: warning
Response message: A request is currently being processed for this agency. Please wait a moment.
✓ PASS: Double-click / duplicate submission prevented safely with status 'warning'.
```

---

## 8. Final Confirmation

1. **Root Cause Resolved:** Orphan Elasticsearch documents pruned and synchronizer added; listing queries hardened against 1-to-many relationship duplication.
2. **Business Workflows Unchanged:** Genuine separate invitations for different jobs or unexpired requests proceed normally.
3. **Zero Stale Data:** Monotonic tenant versioning invalidated all stale client caches across Redis and browser memory.
4. **Code Quality:** All files pass PHP lint (`php -l`) and Laravel Pint code style checks.

---

## 9. Phase 2: Inactive Tab Data Desynchronization & Entity Boundary Leak Audit

### 9.1 Forensic Symptom Analysis (Screenshot `media_1794314261554.png`)
User navigating to `/service_company/clients?tab=inactive#` for Company 3 (TechNova Solutions Pvt. Ltd.) observed:
- Top Counters: `Total Clients: 1`, `Internal Clients: 1 (1 active internal)`, `Inactive Companies: 1`, `Pending Requests: 0 ("You're all caught up")`.
- Bottom Stream: 1 client card with a **blank client name (`""`)**, badges `INTERNAL CLIENT` and `INACTIVE`, location `Noida, India`, connected date `14 Oct, 2026`.
- Data Truth: TechNova Solutions has **0 internal clients** and **0 direct clients**. Its only associated record is an incoming pending request (#REQ-00020 from 4Bell Technology).

### 9.2 Exact Root Cause
1. **DTO Contract Drift in `client_request_data`:**
   - In `ServiceCompanyClientController.php` (line 2394), `$rowsDto` assembled in `client_request_data` passed `'company_name'` instead of canonical `'name'`. Thus, `r.name` was `undefined` in client JavaScript.
   - It lacked `'is_pending_request' => true`, `'is_direct' => true`, `'client_type' => 'Direct Client'`, `'jobs'`, and `'pipeline'`.
2. **Browser Engine Cross-Tab State Bleed:**
   - When the user visited `tab=request`, `index.blade.php` seeded `initialRows` into `ClientSearchEngine`.
   - Because `totalClientsCount` was 0, `(initialRows.length >= totalClientsCount)` evaluated to `(1 >= 0) = true`, falsely marking `ClientSearchEngine` as a full seed of directory clients (`isFullSeed = true`).
   - Because `is_pending_request` was missing, `isPendingRequest` evaluated to `false`, causing the pending invitation to be categorized as an `internal`, `inactive` directory client with an empty name.
   - When the user clicked the `Inactive Companies` tab, `ClientSearchEngine.query({ tab: 'inactive' })` executed in memory without network requests, fabricating counters (`all: 1, internal: 1, inactive: 1, request: 0`) and rendering the blank card.
3. **Subtext Typography:**
   - In `index.blade.php` (lines 1281 & 1487), `#span_clientCustomSub` hardcoded `' active internal'`, showing `'1 active internal'` even when the entity was marked `Inactive`.

### 9.3 Resolution Applied
1. **`ServiceCompanyClientController.php` (`client_request_data`):**
   - Populated complete canonical DTO: `'name' => ucwords($cleanName)`, `'is_pending_request' => true`, `'is_direct' => true`, `'client_type' => 'Direct Client'`, `'is_active_hiring' => false`, `'is_inactive' => false`, plus normalized `'jobs'` and `'pipeline'` objects.
   - Bumped `$schemaVersion = '2.2'` to invalidate stale cached DTOs in Redis/Dragonfly.
2. **`resources/views/service_company/client/index.blade.php`:**
   - Guaranteed that `isFullSeed` is `false` when on `tab=request` (`const isDirectoryTab = (activeClientTab !== 'request')`).
   - Added `activeClientTab !== 'request'` to the Zero-Network Guard so that `ClientSearchEngine` only manages directory clients, while workflow requests always query the request queue.
   - Corrected subtext typography in Blade line 1487 and JS line 1281 from `' active internal'` to `' internal client'` / `' internal clients'`.
3. **Automated Verification:**
   - Added automated tests in `tests/Feature/ClientRequestDeduplicationTest.php` asserting that `tab=inactive` returns 0 cards, 0 total, and preserves `clientRequest: 1`. All 6 tests (30 assertions) pass with 100% success.

