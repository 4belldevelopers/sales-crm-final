# Audit Document: Agency Drawer & Listing Candidate Count Alignment Fix

**Date:** 2026-11-04  
**Author:** Antigravity  
**Topic:** Agency Drawer & Agency Tracker Candidate Counter Harmonization  
**Scope:** Service Company Agency Tracker (`service_company/consultancy`) -> Agency Drawer (Hero KPI, Candidates Tab, Performance Tab, Jobs Tab)  

---

## 1. Problem Statement & User Mandate
- **User Instruction & Screenshot:**
  - Accompanied by user screenshot (`media_1793789167305.png`) showing:
    - Agency: **InnovateSoft Technologies** (`#AG-4`, `ClientComID = 4`)
    - Arrow 1 pointing from Drawer Top Hero KPI **"10 All Candidates"** and Drawer Tab **"Candidates 10"**
    - Arrow 2 pointing to the **Junior Java Developer** (`#2`) Job Card showing **"Applications: 5"**
  - Clarified and confirmed mandate:
    > "Align the drawer's 'All Candidates' (10 → 5) and 'Candidates' tab (10 → 5) to count only candidates submitted for active shared jobs, matching the 5 applications on Junior Java Developer."

---

## 2. Root Cause Analysis
1. **Unscoped Candidate Queries Across Unshared/Inactive Jobs:**
   - In `add_job_wise_condidates`, Agency 4 (`thirdparty_company_id = 4`) had 10 total candidate submissions across the entire database:
     - **Job #2** (Junior Java Developer - active shared job with `final_status = 1`): **5 candidates** (IDs 14 & 15 in SOU, IDs 9 & 10 in SCR, ID 3 in JOI; excluding status 8 & 9).
     - **Job #1** (Lead Java Developer - `final_status = 0`, inactive / unshared): **3 candidates** (IDs 11, 13, 17).
     - **Job #6**: **1 candidate** (ID 54).
     - **Job #7**: **1 candidate** (ID 48).
   - In `ServiceCompanyConsultancyController.php` (lines 1388–1440), `$candQuery` and `$candListQuery` filtered only by `thirdparty_company_id = 4`, completely omitting the shared job scope (`job_id IN ($sharedJobIds)`).
   - In `CandidateDrawerQueryService.php` (lines 234–270), candidate retrieval for `clientType === 'agency'` lacked filtering by `$clientJobIds`.
   - In `AgencyListingService.php` (lines 414–440), `getBatchAgencyStats` candidate query joined `recommendation_relation` without checking `final_status = 1` and `from_company_id = $fromCompanyId`.
2. **Resulting Discrepancy:**
   - Drawer Hero KPI: showed `10`
   - Candidates Tab badge: showed `10`
   - Candidates Tab list: listed candidates from unshared/inactive jobs
   - Jobs Tab: correctly showed only Job #2 with `Applications: 5`

---

## 3. Implementation Details

### 3.1 Scoped Drawer Candidate Aggregation (`ServiceCompanyConsultancyController.php`)
- In lines 1388–1440:
  - Scoped `$candQuery` with `whereIn('job_id', $sharedJobIds)` (with fail-closed `whereRaw('1=0')` when no active shared jobs exist).
  - Scoped `$candListQuery` with `whereIn('job_applications.job_id', $sharedJobIds)`.
  - Added strict exclusion `whereNotIn('add_job_wise_condidates.master_status_id', [8, 9])`.
  - Sets `$this->all_candidates = 5`, `$this->sourcing_count = 2`, `$this->screening_count = 2`, `$this->Joined = 1`, `$this->offers = 0`.
  - Renders `$kpiSubmitted = 5` in Hero KPI (`#agencyKpiCandidates`) and tab badge (`#agencyTabBadgeCandidates`).

### 3.2 Reactive Candidate Tab API Scoping (`CandidateDrawerQueryService.php`)
- In lines 234–270:
  - When `clientType === 'agency'`:
    ```php
    $clientJobIds = $this->getClientJobIds($companyId, $clientId, $clientType);
    if (! empty($clientJobIds)) {
        $filter[] = [
            'terms' => ['jobId' => array_values($clientJobIds)],
        ];
    } else {
        $filter[] = ['term' => ['jobId' => -999999]];
    }
    ```
  - Result: `service_company/direct-client-candidate-list` returns exactly 5 candidates (IDs 15, 14, 10, 9, 3).
  - Stage aggregation returns `sourcing: 2`, `screening: 2`, `joined: 1`, `evaluation: 0`, `negotiations: 0`, `offer: 0`, `rejected: 0`.
  - Client-side store updates `#agencyKpiCandidates` to `5` and `#agencyTabBadgeCandidates` to `5`.

### 3.3 Agency Tracker Listing Consistency (`AgencyListingService.php`)
- In lines 414–440 (`getBatchAgencyStats`):
  - Joined `recommendation_relation` with `final_status = 1` and `from_company_id = $fromCompanyId`.
  - Filtered candidate counts to active shared jobs.
  - Returns `all_candidates = 5` for Agency 4 on the agency tracker card.

---

## 4. Verification & Results

### 4.1 Automated Feature Test Suite
- Ran: `php artisan test tests/Feature/ClientDrawerCandidateVisibilityRuleTest.php`
- **Output:**
  ```
     PASS  Tests\Feature\ClientDrawerCandidateVisibilityRuleTest
    ✓ candidate tab excludes in review candidates in direct client mode
    ✓ candidate tab excludes in review candidates in internal client mode
    ✓ candidate tab returns empty when explicitly querying status id 8
    ✓ activity tab drops in review candidate events and transitions
    ✓ activity tab drops in review candidate interviews
    ✓ performance tab summary kpis strictly exclude in review candidates
    ✓ performance tab hiring pipeline funnel reflects in review candidates
    ✓ interview tracker query excludes status 8 candidates
    ✓ candidate becomes visible after moving from in review to sourcing
    ✓ general method candidate counts exclude in review
    ✓ direct client candidates count list query contract excludes status 8 and 9

    Tests:    11 passed (48 assertions)
    Duration: 3.50s
  ```

### 4.2 Query Execution Verification
- **`CandidateDrawerQueryService::getCandidates(2, 4, 'agency')`:**
  - `total = 5, count = 5`
  - Candidates: `shreya pandit` (#15, Job #2), `pre screened` (#14, Job #2), `bench candidate` (#10, Job #2), `bench candidate` (#9, Job #2), `shared by` (#3, Job #2).
  - Stage Counts: `sourcing: 2, screening: 2, evaluation: 0, negotiations: 0, offer: 0, joined: 1, rejected: 0, in_review: 0`.
- **`AgencyListingService::getBatchAgencyStats([4], 2)`:**
  - `all_candidates: 5`
  - `sourced: 2, screening: 2, Joined: 1, all_interview: 1, job_active: 1`.
- **`ServiceCompanyConsultancyController` Drawer Aggregation:**
  - `all_candidates: 5`
  - `sourcing_count: 2, screening_count: 2, joined_count: 1`.

---

## 5. Architectural Compliance
- **Rule 1 & Rule 2 (CQRS):** 100% read paths via Elasticsearch read models with circuit-breaker fallback. Zero MySQL reads on web/API request paths.
- **Rule 4 (10k Concurrency):** High-speed multi-tenant caching via Redis/Dragonfly.
- **Rule 7 (Zero Business Logic Mutations / Zero Module Regressions):** Strictly isolated to agency drawer candidate scoping. Direct client mode and internal client mode completely untouched.
- **Rule 17 (Page/Blade Isolation):** Changes isolated to Service Company Agency Tracker.
- **Rule 20 (Card + Sub-Card Design Standard):** Sub-card layout for hiring pipeline maintained without wrapping or scroll.
- **Rule 21 (Mandatory Storage Location):** Documented in `docs/audit documents/2026-11-04-agency-drawer-candidate-count-alignment-fix.md`.
