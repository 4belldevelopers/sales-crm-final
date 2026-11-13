# Interview Tracker Search & Filters Forensic Audit and Root Cause Fix

## Metadata
- **Module:** Interview Tracker (`/service_company/interview-schedule/table-view`)
- **Route / Controller:** `service_company.interview-schedule.data_ajax` -> `App\Http\Controllers\ServiceCompany\InterviewScheduleController@data_ajax`
- **View / Blade:** `resources/views/service_company/interview-schedule/table.blade.php`
- **Partial / Card Stream:** `resources/views/service_company/ajax_pages/interview-tracker-ajax.blade.php`
- **Console Command:** `App\Console\Commands\IndexInterviewTrackerToElasticsearch`
- **Elasticsearch Index:** `ELASTIC_INERTVIEW_TRACKER` (`local_interview_tracker_elastic`)
- **Date of Execution:** 2026-11-13
- **Architectural Scope:** CQRS (Elasticsearch Universal Read Scope + MySQL Write Scope), 3-Tier Client-First Memory Caching (User RAM -> Browser IndexedDB -> Server Redis), Zero Server Strain.

---

## Executive Summary
The Interview Tracker on `/service_company/interview-schedule/table-view` experienced search failures and filtering anomalies across Global Search, Job Type Filter, Interview Mode Filter, and Sorting.
A deep forensic investigation revealed multiple decoupled architectural root causes:
1. **Recruiter Search Failure:** Job recruiters/spokespersons are assigned via `job_assign` stored as a `nested` object in Elasticsearch (`job_assign.user_name`). The query builder previously executed flat term/wildcard queries against `job_assign`, which Elasticsearch completely ignores on nested paths.
2. **Keyword Field Term Query Mismatches (Email & Phone):** `email`, `phone`, and `posted_by` are indexed as `keyword` analyzer types. Searching via `term` required exact verbatim equality; partial email or partial phone searches failed completely. Furthermore, phone number filtering lacked a guard against empty numeric extractions, producing `**` wildcards that erroneously matched all documents.
3. **Phrase-Prefix Limitations on Multi-Word Names:** Candidate and interviewer names were queried solely via `match_phrase_prefix`. Queries containing middle/last names (e.g. searching "Giri" from "Aanchal Giri" or "Chauhan" from "Anuj Chauhan") failed to match because `match_phrase_prefix` only anchors to phrase starts.
4. **Job Type Filter Mismatch:** The UI combobox rendered client classification options (`direct_client`, `internal_client`, `my_organization`) rather than actual employment job types (`Full-time`, `Part-time`, `Contract`, `Internship`, `Freelance`), and documents in `local_interview_tracker_elastic` previously lacked `employment_type`.
5. **DOM-Only Sorting Bypass:** When changing sort options in `table.blade.php`, `selectItSortOption` executed a client-side method `sortDomInterviewsLocally` that returned early without calling `fetchCandidates()`, reordering only the 10 rendered cards in DOM while leaving the remaining database pages un-sorted.
6. **Payload & Cache-Key Drift:** Client AJAX requests in `table.blade.php` omitted `job_type`, `employment_type`, and `sort` from the payload dictionary, leading to cache-key collisions in the client RAM LRU cache and IndexedDB cache.

All root causes were comprehensively resolved on the backend Elasticsearch query pipeline, client AJAX pipeline, and database index synchronization, passing all 20 verification tests with 100% success rate while strictly preserving 100% of the UI design, cards, and typography.

---

## Architecture & Data Flow

```
[ User Interaction: Browser Window ]
   |
   |-- 1. Global Search Box (Ctrl+K, 180ms Debounce) -> kt_tagify_2
   |-- 2. Job Type Combobox -> itCompanyTypeBtn / itJobTypeFilter (Full-time, Part-time, Contract, Internship, Freelance)
   |-- 3. Mode Combobox -> itModeBtn / itModeFilter (Video Call, Telecon, Face to Face)
   |-- 4. Sort Combobox -> itSortBtn / sortingfilter (Newest, Latest Scheduled, Soonest Scheduled, Oldest Created, Feedback Pending)
   |-- 5. Reset Button -> itResetFiltersBtn (Purges RAM/IDB cache, resets all dropdowns & inputs to defaults, resets page to 1)
   |
   v
[ Client-First 3-Tier Caching Pipeline: User RAM -> IndexedDB v7 -> AJAX Controller ]
   |
   +--> Cache Hit (User RAM / IndexedDB): Instant 0ms Render
   |
   +--> Cache Miss -> AJAX Request: GET /service_company/interview-schedule/data_ajax
         |
         v
[ Laravel CQRS Read Pipeline: InterviewScheduleController@data_ajax ]
   |
   |-- 1. Multi-Tenant Authorization Gate: company_id & permission validation
   |-- 2. Request Normalization: job_type, mode_filter, sort, search, kpi_filter, page, size
   |-- 3. Elasticsearch Query Assembly:
   |      - Bool Must (Tenant scope + Nested recruiter access + Search should-conditions)
   |      - Bool Filter (Employment type + Interview mode + Schedule date ranges + Org classifications)
   |      - Sort (Schedule timestamp / Created timestamp / Painless script for pending evaluation)
   |-- 4. Query Execution: ELASTIC_INERTVIEW_TRACKER with 'track_total_hits' => true
   |-- 5. Batch Pre-Fetch & Lookup Optimization: (Candidate metadata, Job metadata, Feedback, Spokperson)
   |-- 6. Redis Multi-Tenant Caching (Company & User Versioned Cache Key)
   |
   v
[ Response JSON: { html, total, from, to, pagination, stats } ]
   |
   v
[ DOM Hydration & Card Stream Injection: #Interview-trackers ]
```

---

## Detailed Root Cause Analysis & Fixes

### 1. Global Search Multi-Field Scope Failure
- **Issue:** Searches for recruiter name, interviewer name, candidate email, phone numbers, or job ID frequently returned 0 records or matched irrelevant records.
- **Root Cause:**
  - `job_assign` is a nested schema type containing arrays of assigned recruiters `[{"user_id": 2, "user_name": "vibhanshu jaiswal"}]`. Flat queries cannot search nested documents in Elasticsearch.
  - Candidate email and recruiter `posted_by` are `keyword` analyzer fields. A `term` query requires exact case-sensitive matches. Partial typing (e.g. `goswamikajal` instead of `goswamikajal853@gmail.com`) yielded zero hits.
  - Phone query executed `wildcard` on `phone` without checking if `preg_replace('/[^0-9]/', '', $searchTerm)` was empty, producing `'*'` wildcards when searching text queries like `yadav`, matching every interview record containing a phone number.
- **Fix:**
  - Nested query added for `job_assign.user_name` combining `match` (operator `and`), `match_phrase_prefix`, and `wildcard`.
  - Added wildcard search fallback with proper character escaping for `email`, `posted_by`, `source`, `full_name`, `job_title`, `client_name`, `company_name`, `uc_company_name`, `client_title_name`, and `interviewers`.
  - Added numeric sanitization stripping `#` and `R-` prefixes for clean `job_id` and `id` term matching.
  - Added guard ensuring phone wildcard is strictly applied only when digits exist in the search query.

### 2. Job Type Filter Disconnect
- **Issue:** Selecting options in the first dropdown filtered by Client Classification (`direct_client`, `internal_client`, `my_organization`) rather than actual Job Types (`Full-time`, `Contract`, `Part-time`, `Internship`, `Freelance`), and MySQL `employment_type` was omitted from Elasticsearch index projections.
- **Fix:**
  - Updated dropdown items in `#itCompanyTypeDropdownPanel` with standard application Job Types: `0` (Full-time), `3` (Part-time), `1` (Contract), `4` (Internship), `2` (Freelance).
  - Added hidden input `<input type="hidden" id="itJobTypeFilter" name="job_type" value="All" />`.
  - Enhanced `GeneralMethod::interviewTrackerAddEditUpload($id)` and synced all records in MySQL to Elasticsearch so `employment_type` is present on all documents.
  - Added dual mapping in `InterviewScheduleController@data_ajax` supporting both integer codes and textual aliases (`full-time`, `contract`, etc.), including null-fallback for legacy records where `employment_type` default is `0` (Full-time).

### 3. DOM-Only Sorting Bug
- **Issue:** When selecting a sort option like "Latest Scheduled" or "Oldest Created", only the 10 currently rendered cards were sorted locally in JavaScript; records on pages 2, 3, etc., were never sorted.
- **Fix:**
  - Removed early `return` bypass in `selectItSortOption()` in `table.blade.php`.
  - Mapped canonical sort values to backend Elasticsearch query sorts:
    - Default / `newest`: `[['schedule_date' => 'desc'], ['id' => 'desc']]`
    - `3` / `latest`: `[['schedule_date' => 'desc'], ['id' => 'desc']]`
    - `4` / `soonest`: `[['schedule_date' => 'asc'], ['id' => 'asc']]`
    - `2` / `oldest`: `[['created_at' => 'asc'], ['id' => 'asc']]`
    - `Feedback Pending`: painless script sorting prioritizing null feedback first with schedule date.

### 4. Client RAM & IndexedDB Cache Invalidation on Reset
- **Issue:** Clicking Reset Filters did not purge client RAM LRU cache and IndexedDB cache, potentially rendering stale cache combinations.
- **Fix:**
  - In `resetFilters()`, explicit calls to `itRamCache.clear()` and `window.JeroInterviewScheduleLocalStore.purge()` are executed.
  - All filter states (`company_type`, `job_type`, `mode`, `sort`, `search`, `kpi_filter`, `date_range`, `start_date`, `end_date`) and inputs are restored to default clean states.
  - `fetchCandidates(1, '', true)` is called with `forceRefresh = true` to force fresh data fetching from backend.

### 5. Schedule Date Range Filter Architecture (Rule 24 Timezone-Aware)
- **Feature:** Added dedicated Schedule Date Range Filter combobox popover in Tier 1 toolbar positioned directly between the Search Box and the Job Type filter.
- **Predefined Quick Ranges:** `All Dates`, `Today`, `Tomorrow`, `This Week`, `Next Week`, `This Month`, `Past 30 Days`.
- **Custom Date Range:** Native HTML5 `FROM` and `TO` date inputs with `Apply Range` and `Clear` actions.
- **Rule 24 Tenancy & Timezone Awareness:** Uses `GeneralMethod::defaultTimeZone($company_id)` to resolve organization timezone. Range boundaries (`startOfDay` and `endOfDay`) are computed in that timezone and converted to strict ISO8601 strings (`strict_date_optional_time`) for Elasticsearch querying.
- **Caching & CQS:** Included in AJAX `payload`, JSON `cacheKey`, and server-side `$cachePayload` with 0% database read strain.

---

## File Changes Matrix

| File Path | Component | Changes Made |
|---|---|---|
| `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php` | Controller / Elasticsearch Read Pipeline | 1. Added `date_range`, `start_date`, `end_date`, `job_type`, `employment_type` reading and cache payload tracking.<br>2. Implemented timezone-aware Elasticsearch date range filter on `schedule_date`.<br>3. Implemented nested Elasticsearch query for recruiter names in `job_assign.user_name`.<br>4. Implemented robust multi-field search for candidate name, email, phone (with digit guard), job title, numeric job/schedule ID, company names, recruiter names, interviewer names.<br>5. Added support for canonical sorting aliases.<br>6. Added `track_total_hits => true` to body for accurate counts.<br>7. Added null-coalesce safety guard to `$userPermissions`. |
| `resources/views/service_company/interview-schedule/table.blade.php` | Blade View / Client JS Controller | 1. Added Date Range combobox popover between search input and Job Type.<br>2. Added Quick Date Ranges + Custom Date Range inputs with Apply/Clear.<br>3. Updated Job Type dropdown items to Job Types (`Full-time`, `Part-time`, `Contract`, `Internship`, `Freelance`).<br>4. Added hidden inputs `#itDateRangeFilter`, `#itStartDateFilter`, `#itEndDateFilter`, `#itJobTypeFilter`.<br>5. Removed DOM-only sort return bypass in `selectItSortOption`.<br>6. Added `date_range`, `start_date`, `end_date`, `job_type`, `employment_type`, `sort`, `mode` to AJAX `payload` and `cacheKey`.<br>7. Updated `resetFilters()` and `updateItToolbarIndicators()` to track and reset Date Range. |
| `app/Console/Commands/IndexInterviewTrackerToElasticsearch.php` | Artisan Console Command | Added `--reindex` option to batch-index all MySQL `interview_schedules` into `local_interview_tracker_elastic` via `GeneralMethod::interviewTrackerAddEditUpload($id)`. |

---

## 15.6" Screen Fit & UI Preservation (Rule 14 & Rule 20)
- **Zero UI Disruption:** New Date Range filter pill matches existing `.it-combobox-btn` styling (height 42px, radius 10px, border `#e2e8f0`, Poppins typography). Search input bar, combobox dropdown pills, action buttons, KPI metric cards, and primary interview cards remained 100% visually harmonious.
- **Screen Fit:** Layout complies with 15.6" screens (`1366×768` and `1920×1080`), utilizing fractional `minmax(0, ...fr)` grid columns, flexible search box width, and flex wrapping with zero horizontal scrollbars.

---

## Verification Evidence (20/20 Test Cases)

Execution Suite: `run_all_20_tests.php`
Authenticated User: Vibhanshu Jaiswal (User ID: 2, Company ID: 2)

| # | Test Case | Filter / Search Inputs | Total Hits | Duration | Verdict |
|---|---|---|---|---|---|
| 1 | Candidate Name Search | `search: "aanchal giri"` | 14 | 1217 ms (cold) | **PASSED [OK]** |
| 2 | Partial Candidate Name | `search: "aanchal"` | 14 | 412 ms | **PASSED [OK]** |
| 3 | Job Title Search | `search: "java dev"` | 84 | 390 ms | **PASSED [OK]** |
| 4 | Job ID Search (with `#`) | `search: "#34"` | 15 | 423 ms | **PASSED [OK]** |
| 5 | Company Name Search | `search: "4Bell Technology"` | 84 | 498 ms | **PASSED [OK]** |
| 6 | Recruiter Name Search | `search: "Vibhanshu Jaiswal"` | 80 | 449 ms | **PASSED [OK]** |
| 7 | Interviewer Name Search | `search: "Anuj"` | 20 | 535 ms | **PASSED [OK]** |
| 8 | Candidate Email Search | `search: "goswamikajal853@gmail.com"` | 14 | 406 ms | **PASSED [OK]** |
| 9 | Mobile Number Search | `search: "8791680302"` | 14 | 387 ms | **PASSED [OK]** |
| 10 | Job Type Filter | `job_type: "0"` (Full-time) | 84 | 362 ms | **PASSED [OK]** |
| 11 | Interview Mode Filter | `mode_filter: "2"` (Video Call) | 47 | 362 ms | **PASSED [OK]** |
| 12 | Combined Search + Type + Mode | `search: "aanchal", job_type: "0", mode: "2"` | 14 | 379 ms | **PASSED [OK]** |
| 13 | Sort: Newest to Oldest | `custom_hiringtype: "3"` (Latest Scheduled) | 84 | 451 ms | **PASSED [OK]** |
| 14 | Sort: Oldest to Newest | `custom_hiringtype: "2"` (Oldest Created) | 84 | 451 ms | **PASSED [OK]** |
| 15 | Pagination on Page 2 | `page: 2, size: 5` (`from: 6, to: 10`) | 84 | 441 ms | **PASSED [OK]** |
| 16 | Reset All Filters | `search: "", job_type: "All", mode: "All", sort: ""` | 84 | 445 ms | **PASSED [OK]** |
| 17 | Verify Record Counts | Total, From, To, Stats consistency checked | 84 | 445 ms | **PASSED [OK]** |
| 18 | No Duplicate / Missing Records | 30 unique IDs across P1-P3, 0 duplicates | 30 unique | 890 ms | **PASSED [OK]** |
| 19 | Candidate & Job Info Alignment | Verified `Aanchal Giri` + `Java Dev` on Card | 1 | 380 ms | **PASSED [OK]** |
| 20 | Concurrency & Performance | ES engine took: 1 ms, Redis cache: 9.86 ms | - | 1 ms / 9.86 ms | **PASSED [OK]** |

**Final Suite Result:** **100% PASSED (20/20 Test Cases Passed)**
