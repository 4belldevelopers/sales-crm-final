# Direct Client Candidate Pipeline Count Forensic Audit & Resolution Log

**Date:** 2026-11-04  
**Author:** Antigravity (GOD-Level SaaS Architect & Engineer)  
**Scope:** Service Company Direct Client Pipeline Counters (`/service_company/clients`) & Direct Client Drawer Pipeline Synchronization (Candidates List, Counters, and Performance Analytics Graphs)  
**Related Entities:**  
- Service Company (Agency Tenant): `InnovateSoft Technologies` (`companies.id = 4`)  
- Direct Client (Employer Tenant): `4Bell Technology` (`companies.id = 2`)  
- Elasticsearch Indices: `ELASTIC_CANDIDATES`, `ELASTIC_INERTVIEW_TRACKER`, `ELASTIC_JOBS`  

---

## 1. Executive Summary & Authoritative Target Contract

On the **Service Company Clients Tracker** (`/service_company/clients`), when viewing the direct client card for **4Bell Technology**, and inside the **Direct Client Sliding Drawer** (`dviewMore.blade.php`), all candidate-related metrics, candidate listings, tab counters, KPI summary cards, and the Performance Analytics graphs/funnel MUST strictly reflect the candidate submissions shared between **4Bell Technology** and **InnovateSoft Technologies** (excluding in-review status 8 and archived status 9).

### Authoritative Pipeline Metrics (100% Synchronized Across Tracker, Drawer, List, & Graphs):
- **Sourcing:** 6 (Candidate IDs: 13, 14, 15, 17, 48, 54)
- **Screening:** 3 (Candidate IDs: 9, 10, 11)
- **Evaluation:** 0
- **Negotiation:** 0
- **Offer:** 0
- **Joined:** 1 (Candidate ID: 3)
- **Rejected:** 0
- **In Review:** 0 (strictly excluded: Candidate IDs 18, 19, 20, 29, 30)
- **Interviews:** 1 (Interview ID: 1)
- **Total Applications:** 10

---

## 2. Root Cause Analysis

1. **Tracker Pipeline Over-Inflation (14 Candidates):**  
   The legacy query checked pairs broadly without scoping candidates to agency attribution (`ClientComID`). Direct applicants submitted in-house to 4Bell (`ClientComID = 0`) on Job 2 and Candidate 33 (Rejected) leaked into the counter, inflating the total count to 14.
2. **Intermediate Under-Counting (5 Candidates):**  
   Requiring `final_status in [1, 2]` on the candidate level excluded Candidates 13, 17, 11 on Job 1, Candidate 54 on Job 6, and Candidate 48 on Job 7 because those jobs had pending or direct client requisition attributions without the recommendation relation flag in ES.
3. **Performance Graph Scope Inconsistency (6 Candidates):**  
   `ClientPerformanceScopeResolver::resolveScope` previously enforced:
   - Restricting candidate queries strictly to `jobId IN sharedJobIds` where `sharedJobIds` only queried `nested recommendation_relation.final_status = 1` (which only matched Job 2, missing Jobs 1, 6, and 7).
   - Enforcing `final_status = 1` on the candidate document, which was empty for candidates on Jobs 1, 6, and 7.
   As a result, opening the Performance tab in the direct client drawer showed 6 candidates instead of 10.
4. **The True Authoritative Contract:**  
   In JERO's B2B ATS multi-tenant architecture:
   - All candidates submitted by InnovateSoft to 4Bell have `company_id = 2` (or `from_company_id = 2` / `client_id = 2`) and `ClientComID = 4`.
   - Requiring `ClientComID = $authCompanyId` and positive allow-list statuses [1..6] cleanly captures all 10 agency submissions while strictly excluding in-house client talent (`ClientComID = 0`), In Review (status 8), and Archived (status 9).

---

## 3. Engineering Remediation & Code Modifications

### A. `app/Helper/GeneralMethod.php`
- **`DirectClientCandidatesCountList($from_company_id, $to_company_id)`**:
  - Dynamically scopes to relationship pair: `company_id IN ($pairCompIds)` or `from_company_id IN ($pairCompIds)`.
  - Scopes candidate attribution to agency: `ClientComID = $viewerCompanyId` or (`ClientComID IN ($pairCompIds)` AND `ClientComID > 0`).
  - Strictly excludes `job_master_status_id in [8, 9, '8', '9']` and unassigned `master_status_id in [8, 9, '8', '9']`.
  - Excludes `master_status_id in [8, 9, '8', '9']` and `job_master_status_id in [8, 9, '8', '9']` on the interview tracker query.
- **`DirectClientCandidatesCount($from_company_id, $to_company_id)`**:
  - Fully synchronized with the identical query contract.

### B. `app/Services/Candidate/CandidateDrawerQueryService.php`
- **`resolveClientJobIds($companyId, $clientId, 'direct')`**:
  - Resolves all jobs belonging to the client (`company_id = $clientId`, `client_id = $clientId`) or shared with agency.
- **`getCandidates($companyId, $clientId, 'direct')`**:
  - Scopes candidate retrieval for direct clients to client jobs and candidate attribution `ClientComID = $companyId`.
  - Returns the exact 10 candidate submissions: 6 Sourcing, 3 Screening, 1 Joined.

### C. `app/Services/Interview/InterviewTenantScope.php`
- **`forClient($companyId, $clientId, 'direct')`**:
  - Enforces that candidates are attributed to the agency (`ClientComID`/`thirdparty_company_id`/`to_company_id`), associated with the client, and scheduled for interviews on client jobs, returning exactly 1 active interview.

### D. `app/Services/Analytics/ClientPerformanceScopeResolver.php`
- **`resolveScope($authCompanyId, $clientId, 'direct')`**:
  - Aligned direct client `candidate_filter`: Scopes to client organization (`from_company_id`, `company_id`, `client_id`, or `sharedJobIds`) and viewer agency attribution (`ClientComID = $authCompanyId`).
  - Replaces restrictive `final_status = 1` filter on candidate documents with authoritative allow-list `PerformanceStatusMap::SHARED_PIPELINE_STATUSES` [1..6], strictly excluding status 8 and 9.
  - Aligned direct client `job_filter`: Scopes to client jobs (`company_id`, `client_id`) and recommendation relations.
- **`resolveSharedJobIds($authCompanyId, $clientId, 'direct')`**:
  - Resolves client jobs matching `company_id = $clientId`, `client_id = $clientId`, or shared recommendation relations.

---

## 4. Verification & Automated Test Coverage

1. **Direct Elasticsearch Aggregation Verification:**
   ```
   DirectClientCandidatesCountList(2, 4):
   - all_candidates: 10
   - sourced: 6
   - screening: 3
   - evaluated: 0
   - negotiations: 0
   - offers: 0
   - joined: 1
   - rejected: 0
   - in_review: 0
   - all_interview: 1
   ```

2. **Drawer Candidate List Verification:**
   ```
   CandidateDrawerQueryService::getCandidates(4, 2, 'direct'):
   - Returned Candidates: 10 (IDs: 54, 48, 17, 15, 14, 13, 11, 10, 9, 3)
   - Stage counts: {"sourcing": 6, "screening": 3, "evaluation": 0, "negotiations": 0, "offer": 0, "joined": 1, "rejected": 0, "in_review": 0}
   ```

3. **Drawer Performance Analytics & Graph Verification:**
   ```
   ClientPerformanceAnalyticsService::getPerformanceDashboard(4, 2, 'direct'):
   - Summary Total Candidates: 10 (10 submissions, 5 unique)
   - Summary Screened: 3
   - Summary Interviews Scheduled: 1
   - Summary Offers: 0
   - Summary Joins: 1
   - Funnel Total: 10
   - Funnel In Review: 0 (strictly 0)
   - Funnel Stages:
     * Candidates: 10 (100.0%)
     * Screened: 3 (30.0%)
     * Interviewed: 0
     * Offers: 0
     * Joined: 1 (10.0%)
   - Velocity Chart (Candidates & Interviews):
     * Candidates Submitted: [Sep 2026: 8, Oct 2026: 2] -> Total 10
     * Joins: 1
   ```

4. **PHPUnit Test Suites:**
   - `tests/Feature/ClientDrawerCandidateVisibilityRuleTest.php`: **11 passed (48 assertions)**
   - `tests/Feature/DirectClientDrawerAttributionSecurityTest.php`: **39 passed (105 assertions)**

---

## 5. Offers & Joined Tabs Separation (Drawer Architecture & Rule 20 Compliance)

Per user request ("make Separate Offers and Joined Tabs"), the merged "Offers & Joins" tab pill in the Direct Client Sliding Drawer was divided into two distinct, high-performance tabs adhering to the **Modern Card + Sub-Card Enterprise Design System (Rule 20)**:

### Architecture & Implementation Details:
1. **Tier 2 Drawer Sub-Tabs List (`dviewMore.blade.php`)**:
   - Replaced merged `#candidate_joining` ("Offers & Joins") pill with two discrete pills:
     - **Offers**: Targets `#candidate_offers`, icon `fas fa-file-signature`, dynamic badge `#offersDirectTabBadge` (reflects `$kpiOffers`, hidden if 0).
     - **Joined**: Targets `#candidate_joining`, icon `fas fa-handshake`, dynamic badge `#joinedDirectTabBadge` (reflects `$kpiJoins`, hidden if 0).
2. **Dedicated Tab Panes**:
   - **`#candidate_offers` Pane**:
     - Tier 1 Header Card: "Candidate Offers", rose theme (`#fff1f2`, `#e11d48`), badge `#cdOffersCountBadgeDirect` (`#cdOffersCountTextDirect`).
     - Dynamic Search: `#offersSearchInputDirect` with debounced input (300ms), clear button `#offersSearchClearDirect`, reset button `#offersResetBtnDirect`.
     - Sub-tab card: "Offers Extended" button (`#offersCountOffersDirect`).
     - Cards stream container: `#candidatetListOffers`, pagination container `#paginationcandidateoffers`.
   - **`#candidate_joining` Pane**:
     - Tier 1 Header Card: "Joined Candidates", emerald/teal theme (`#ecfdf5`, `#059669`), badge `#cdJoinedCountBadgeDirect` (`#cdJoinedCountTextDirect`).
     - Dynamic Search: `#ojSearchInputDirect` with debounced input (300ms), clear button `#ojSearchClearDirect`, reset button `#ojResetBtnDirect`.
     - Sub-tab card: "Joined & Onboarded" button (`#ojCountJoinedDirect`).
     - Cards stream container: `#candidatetListJoin`, pagination container `#paginationcandidatejoin`.
3. **Performance Dashboard KPI Triggers**:
   - Offers Generated KPI Card (`#perfKpiOffers`) now triggers `data-target="#candidate_offers"`.
   - Successful Joins KPI Card (`#perfKpiJoins`) triggers `data-target="#candidate_joining"`.
4. **JavaScript Routing & Execution Separation**:
   - `mapDirectTabIdentity`: Resolves `#candidate_offers`/`offers` to `normTab = 'offers'`, `domPaneId = 'candidate_offers'`. Resolves `#candidate_joining`/`joined` to `normTab = 'joined'`, `domPaneId = 'candidate_joining'`.
   - `loadDirectTabContent`: Distinct execution branches invoking `directfetchCandidatesOffers` (segment `offers`) and `directfetchCandidatesJoin` (segment `joined`).
   - `directfetchCandidatesOffers`: Dedicated AJAX client calling `/service_company/direct-client-candidate-join-list?segment=offers` and rendering into `#candidatetListOffers`.
   - `directfetchCandidatesJoin`: Scoped to `segment=joined` and rendering into `#candidatetListJoin`.
   - `ClientPipelineStore.updateDrawer` in `index.blade.php`: Real-time Reverb / state updates now independently update `#offersDirectTabBadge` and `#joinedDirectTabBadge`.

---

## 6. Internal Client Drawer Offers & Joined Separation (`show.blade.php`)

In addition to the direct client drawer, the internal client drawer (`resources/views/service_company/client/show.blade.php`) was also updated with complete symmetry and page isolation (Rule 17 & Rule 20):

1. **Tier 2 Sub-Tabs List (`show.blade.php`)**:
   - Replaced merged `#candidate_joining` ("Offers & Joins") with two distinct tabs:
     - **Offers**: Targets `#candidate_offers`, icon `fas fa-file-signature`, live badge `#offersInternalTabBadge` (`$kpiOffers`).
     - **Joined**: Targets `#candidate_joining`, icon `fas fa-handshake`, live badge `#joinedInternalTabBadge` (`$kpiJoins`).
2. **Dedicated Panes**:
   - **`#candidate_offers` Pane**:
     - Header Toolbar Card: "Candidate Offers", rose theme (`#fff1f2`, `#e11d48`), badge `#cdOffersCountBadgeInternal` (`#cdOffersCountTextInternal`).
     - Search Card: `#offersSearchInputInternal` with 300ms debounce, clear `#offersSearchClearInternal`, reset `#offersResetBtnInternal`.
     - Sub-Tab Card: "Offers Extended" button (`#offersCountOffersInternal`).
     - Container: `#candidatetListOffers` with pagination `#paginationcandidateoffers`.
   - **`#candidate_joining` Pane**:
     - Header Toolbar Card: "Joined Candidates", emerald theme (`#ecfdf5`, `#059669`), badge `#cdJoinedCountBadgeInternal` (`#cdJoinedCountTextInternal`).
     - Search Card: `#ojSearchInputInternal` with 300ms debounce, clear `#ojSearchClearInternal`, reset `#ojResetBtnInternal`.
     - Sub-Tab Card: "Joined & Onboarded" button (`#ojCountJoinedInternal`).
     - Container: `#candidatetListJoin` with pagination `#paginationcandidatejoin`.
3. **KPI Card Navigation**:
   - KPI 5 ("All Offers", `#cdKpiOffersGeneratedInternal`) now targets `#candidate_offers`.
   - Performance KPI 3 ("Offers Generated", `#perfKpiOffers`) now targets `#candidate_offers`.
4. **JavaScript Routing & Handlers**:
   - `mapInternalTabIdentity`: Maps `candidate_offers` to `normTab = 'offers'` and `candidate_joining` to `normTab = 'joined'`.
   - `loadInternalTabContent`: Separate execution branches calling `fetchCandidatesOffers` (segment `offers`) and `fetchCandidatesJoin` (segment `joined`).
   - `fetchCandidatesOffers`: Dedicated AJAX client calling `/service_company/client-candidate-join-list?segment=offers` and rendering into `#candidatetListOffers`.
   - `fetchCandidatesJoin`: Scoped to default segment `joined` and rendering into `#candidatetListJoin`.
   - `index.blade.php`: `ClientPipelineStore.updateDrawer` synchronizes both `#offersInternalTabBadge` and `#joinedInternalTabBadge`.


