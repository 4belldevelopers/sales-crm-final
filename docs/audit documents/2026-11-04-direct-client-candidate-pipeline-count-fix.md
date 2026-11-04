# Direct Client Candidate Pipeline Count Forensic Audit & Resolution Log

**Date:** 2026-11-04  
**Author:** Antigravity (GOD-Level SaaS Architect & Engineer)  
**Scope:** Service Company Direct Client Pipeline Counters (`/service_company/clients`) & Drawer Pipeline Synchronization  
**Related Entities:**  
- Service Company (Agency Tenant): `InnovateSoft Technologies` (`companies.id = 4`)  
- Direct Client (Employer Tenant): `4Bell Technology` (`companies.id = 2`)  
- Elasticsearch Indices: `ELASTIC_CANDIDATES`, `ELASTIC_INERTVIEW_TRACKER`, `ELASTIC_JOBS`  

---

## 1. Executive Summary & Authoritative Target Contract

On the **Service Company Clients Tracker** (`/service_company/clients`), when viewing the direct client card for **4Bell Technology**, the candidate pipeline must reflect the candidate submissions shared between **4Bell Technology** and **InnovateSoft Technologies** (excluding in-review status 8 and archived status 9).

### Authoritative Pipeline Metrics (100% Synchronized with Agencies Tracker):
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

1. **Previous Over-Inflation (14 Candidates):**  
   The legacy query checked pairs broadly without scoping candidates to agency attribution (`ClientComID`). Direct applicants submitted in-house to 4Bell (`ClientComID = 0`) on Job 2 and Candidate 33 (Rejected) leaked into the counter, inflating the total count to 14.
2. **Intermediate Under-Counting (5 Candidates):**  
   Requiring `final_status in [1, 2]` on the candidate level excluded Candidates 13, 17, 11 on Job 1, Candidate 54 on Job 6, and Candidate 48 on Job 7 because those jobs had pending or direct client requisition attributions without the recommendation relation flag in ES.
3. **The True Authoritative Contract:**  
   In JERO's B2B ATS multi-tenant architecture, all candidates submitted by InnovateSoft to 4Bell have `company_id = 2` (or `from_company_id = 2`) and `ClientComID = 4`. Requiring `ClientComID in $pairCompIds` and `range: ClientComID > 0` cleanly includes all 10 agency submissions while strictly excluding in-house client talent (`ClientComID = 0`).

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

2. **Drawer Query Service Verification:**
   ```
   CandidateDrawerQueryService::getCandidates(4, 2, 'direct'):
   - Returned Candidates: 10 (IDs: 54, 48, 17, 15, 14, 13, 11, 10, 9, 3)
   - Stage counts: {"sourcing": 6, "screening": 3, "evaluation": 0, "negotiations": 0, "offer": 0, "joined": 1, "rejected": 0, "in_review": 0}
   ```

3. **PHPUnit Test Suites:**
   - `tests/Feature/ClientDrawerCandidateVisibilityRuleTest.php`: **11 passed (48 assertions)**
   - `tests/Feature/DirectClientDrawerAttributionSecurityTest.php`: **39 passed (105 assertions)**
   - Pint Code Style: **All files PASS**.
