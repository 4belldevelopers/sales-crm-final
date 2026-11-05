# Direct Client Drawer — Job Type Performance & Shared Job Scoping Fix

**Date:** 2026-11-04  
**Scope:** Direct Client Drawer (Performance Tab, Job Type Performance Chart, Shared Jobs Scoping)  
**Author:** AI Pair Programmer (Antigravity)

---

## 1. Problem Description

In the Direct Client drawer (`service_company/clients`), when viewing a connected Direct Client (e.g., 4Bell Technology), the **Job Type Performance** card and the Performance Dashboard displayed incorrect data:

1. **Job Leakage & Unscoped Aggregations:**
   - **Job Type Performance** showed `4 Active Jobs` in the donut center and `Full-Time: 6 (100.0%)` in the breakdown list.
   - **All Jobs Status** showed `Hiring (Active): 4`, `On Hold: 1`, `Closed: 1` (Total = 6 jobs).
   - In reality, only **1 job** (Job #2: Junior Java Developer, Active, Full-Time) was shared between the operating company (Company #4) and the Direct Client (Company #2).
   - The other 5 jobs (Jobs #1, #3, #6, #7, #8) were internal jobs of Company #2 created for other clients, never shared with Company #4.

2. **Root Cause in `ClientPerformanceScopeResolver` and `ClientPerformanceQueryBuilder`:**
   - In `ClientPerformanceScopeResolver.php`, `resolveSharedJobIds` for `clientType === 'direct'` used a loose `should` query containing `['term' => ['company_id' => $clientId]]`. This matched every job created by the direct client company, including their private unshared jobs.
   - In `'job_filter'`, it similarly used a `should` structure without `'must'`.
   - In `ClientPerformanceQueryBuilder.php`, `buildJobQuery` extracted `$scope['job_filter']['bool']['must']`. Because `'must'` was null, it defaulted to `[]`, generating an empty query `{"bool": {"must": []}}` (a `match_all` query over the entire Elasticsearch index).
   - In `'candidate_filter'`, it did not restrict candidates to the shared job IDs (`jobId IN ($sharedJobIds)`), causing candidate submissions from unshared jobs to be aggregated.

3. **Visual UI Clipping in Donut Center:**
   - The center label `<div class="cd-donut-center-lbl">Active Jobs</div>` clipped the first letter ("A") as `ctive Jobs` due to lack of `white-space: nowrap` and rigid font styling inside the 110px canvas cutout.
   - The center text did not dynamically handle singular vs plural labels (`Active Job` vs `Active Jobs`).

---

## 2. Changes Applied

### A. `app/Services/Analytics/ClientPerformanceScopeResolver.php`
- **Strict Shared Jobs Scoping (`resolveSharedJobIds`):**
  - Updated the Direct Client query to strictly filter via `nested recommendation_relation` where `final_status IN [1, 2]` between `$clientId` and `$authCompanyId`.
  - Removed unconstrained `company_id` and `client_id` terms that were pulling unshared tenant jobs.
- **Strict `job_filter` for Direct Client:**
  - Configured `job_filter` to strictly require `['terms' => ['id' => $sharedJobIds]]` (or fail-closed `['term' => ['id' => -999999]]` if no shared jobs exist).
- **Strict `candidate_filter` for Direct Client:**
  - Constrained candidate queries to `jobId` / `job_id` matching `$sharedJobIds` only.

### B. `app/Services/Analytics/ClientPerformanceQueryBuilder.php`
- **Hardened `buildJobQuery`:**
  - Removed fragile dependency on `$scope['job_filter']['bool']['must']`.
  - Now safely consumes the full `$scope['job_filter']` structure or fails closed to `['bool' => ['must' => [['term' => ['id' => -999999]]]]]`. Never generates an empty `match_all` query.

### C. `resources/views/service_company/client/dviewMore.blade.php`, `show.blade.php`, & `consultancy/dviewMore.blade.php`
- **Dynamic Singular/Plural Label:**
  - Added `id="donutCenterLabel"` to the donut center label element.
  - Updated JS:
    ```javascript
    var activeJobsCount = j.active || 0;
    $('#donutTotalJobs').text(activeJobsCount);
    if ($('#donutCenterLabel').length) {
        $('#donutCenterLabel').text(activeJobsCount === 1 ? 'Active Job' : 'Active Jobs');
    }
    ```
- **CSS Anti-Clipping (`.cd-donut-center-lbl`):**
  - Added `white-space: nowrap; text-align: center;` and adjusted font-size to `9px !important; font-weight: 500;`.
  - Eliminates text wrapping and horizontal clipping inside the 79px cutout.

---

## 3. Verification & Test Results

1. **Direct Client Performance Analytics Payload (`verify_perf.php`):**
   - **Jobs Data:** `{"total_count": 1, "active": 1, "hold": 0, "closed": 0}` ✅
   - **Job Types:** `Full-Time: 1 (100.0%)`, `Contract: 0`, `Freelance: 0`, `Part-Time: 0` ✅
   - **Donut Center:** `1 Active Job` ✅
   - **Funnel / Summary:** Accurately reflects only candidates associated with shared jobs.
2. **Automated Test Suite:**
   - `ClientDrawerCandidateVisibilityRuleTest`: 11 passed (48 assertions) ✅
   - `DirectClientDrawerAttributionSecurityTest`: 39 passed (105 assertions) ✅
   - Total: 50 passed (153 assertions), 0 failures.
3. **Cache Cleared:**
   - Application cache, compiled views, and Redis `ats:perf_analytics:*` flushed.
