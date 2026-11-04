# Audit & Fix: Agency Candidate Pipeline Stage Counting Correction

**Date:** 2026-11-04  
**Author:** Antigravity (Advanced Agentic AI Assistant)  
**Scope:** Service Company Agencies Tracker (`service_company/consultancy`), Agency Listing Service, Batch Stats Aggregation  
**Target Agency:** Innovatesoft Technologies (`#AG-4`, `ClientComID = 4`)  

---

## 1. Executive Summary & Problem Diagnosis

### Symptoms Reported by User
On the Agencies Tracker listing page (`service_company/consultancy`), under the **Candidate Pipeline** sub-card for **Innovatesoft Technologies**:
1. Displayed counts were:
   - `Sourcing: 2`
   - `Screening: 0`
   - `Evaluation: 0`
   - `Negotiation: 0`
   - `Offer: 0`
   - `Joined: 1`
   - `Rejected: 0`
   - `Interview: 0`
   - Card Footer: `Submissions: 5`
2. However, the authoritative ATS candidate pipeline (in the Agency Drawer Candidates tab & Analytics Funnel) had:
   - `Sourcing: 6`
   - `Screening: 3`
   - `Evaluation: 0`
   - `Negotiations: 0`
   - `Offer: 0`
   - `Joined: 1`
   - `Rejected: 0`
   - Total Submissions: `10`
   - Scheduled Interviews: `1`

---

## 2. Root Cause Analysis

### A. Flawed Join & Incomplete Status Aggregation in `AgencyListingService::getBatchAgencyStats`
1. **Restrictive Recommendation Join:**
   ```php
   // BEFORE (FLAWED):
   $candidateCounts = DB::table('add_job_wise_condidates')
       ->join('recommendation_relation', function ($join) use ($fromCompanyId) {
           $join->on('recommendation_relation.job_id', '=', 'add_job_wise_condidates.job_id')
               ->on('recommendation_relation.to_company_id', '=', 'add_job_wise_condidates.thirdparty_company_id')
               ->where('recommendation_relation.from_company_id', '=', $fromCompanyId)
               ->where('recommendation_relation.final_status', '=', 1);
       })
   ```
   The query joined `recommendation_relation` with `final_status = 1`, which excluded candidate submissions made across other jobs or past assignments. In the ATS pipeline, candidate submissions are owned by the tenant company (`company_id`) and submitted by the agency (`thirdparty_company_id`), regardless of job relation flags.
2. **Missing and Mismatched Status IDs:**
   - Did **NOT** query `master_status_id = 2` (Screening) at all!
   - Did **NOT** query `master_status_id = 4` (Negotiation) at all!
   - Did **NOT** query `master_status_id = 7` (Rejected)!
   - Counted `master_status_id = 10` for Offers (in JERO ATS, Offer is status `5`).
   - Mapped `master_status_id = 7` (Rejected) to `all_interview`!
3. **No Interview Schedules Query:**
   - Did not query `interview_schedules`, resulting in `0` interviews displayed for agencies with scheduled candidate interviews.

### B. View Mapping Bugs in `staffing-partner-ajax.blade.php`
1. `Screening` step displayed `$evaluated` instead of `$screening`.
2. `Negotiation` step was hardcoded to `0`.
3. `Rejected` step was hardcoded to `0`.
4. The pill spans lacked the class `cl-pipe-num`, which prevented CSV export and dynamic selectors from reading the stage counts.

---

## 3. Implemented Fixes

### 1. Unified Batch Stats in `AgencyListingService.php`
Updated `getBatchAgencyStats` to:
1. Initialize complete metric defaults: `sourced`, `screening`, `evaluated`, `negotiation`, `offers`, `Joined`, `rejected`, `all_interview`.
2. Query `add_job_wise_condidates` directly by tenant tenancy (`company_id = $fromCompanyId` or `0`/`null`) and `thirdparty_company_id IN ($agencyCompanyIds)` excluding in-review (8) and duplicates (9).
3. Compute discrete counts for all 7 ATS pipeline statuses:
   - Sourcing (`master_status_id = 1`)
   - Screening (`master_status_id = 2`)
   - Evaluation (`master_status_id = 3`)
   - Negotiation (`master_status_id = 4`)
   - Offers (`master_status_id = 5`)
   - Joined (`master_status_id = 6`)
   - Rejected (`master_status_id = 7`)
4. Bulk query `interview_schedules` joined with `job_applications` / `add_job_wise_condidates` to populate `all_interview` with authoritative interview events.
5. Corrected status IDs in `getTopPerformingAgencyIds` from `[3, 6, 7, 10]` to valid milestone statuses `[3, 4, 5, 6]`.
6. Updated `getAgencyPipelineCounts` to include all valid candidate pipeline statuses `[1, 2, 3, 4, 5, 6, 7]`.

### 2. Comprehensive Fallback in `GeneralMethod::agencyCandidatesCountList`
Enhanced `agencyCandidatesCountList` in `app/Helper/GeneralMethod.php` with ES aggregations and DB fallback for `screening`, `negotiation`, and `rejected`.

### 3. Clean View Template in `staffing-partner-ajax.blade.php`
- Extracted `$screening`, `$negotiation`, `$rejected` from `$candidatesProfiles`.
- Mapped all 8 discrete steps in the `cl-pipeline-flow` sub-card with `.cl-pipe-num` and `.has-count` states.
- Synchronized Submissions badge to reflect true total candidate submissions (`10`).

### 4. Cache Key Bumping & Invalidation
- Bumped listing cache key to `ats:allconsultancy_v3:` and ETag to `agency-v3-` in `ServiceCompanyConsultancyController.php`.
- Cleared application cache and compiled views via `php artisan cache:clear; php artisan view:clear`.

---

## 4. Verification & Output Comparison

### Authoritative Agency Listing Output (Innovatesoft Technologies, `#AG-4`):
| Metric | Before Fix | After Fix | Authoritative ATS Value | Match |
|---|---|---|---|---|
| **Total Submissions** | 5 | 10 | 10 | MATCH |
| **Sourcing** | 2 | 6 | 6 (Status 1) | MATCH |
| **Screening** | 0 | 3 | 3 (Status 2) | MATCH |
| **Evaluation** | 0 | 0 | 0 (Status 3) | MATCH |
| **Negotiation** | 0 | 0 | 0 (Status 4) | MATCH |
| **Offer** | 0 | 0 | 0 (Status 5) | MATCH |
| **Joined** | 1 | 1 | 1 (Status 6) | MATCH |
| **Rejected** | 0 | 0 | 0 (Status 7) | MATCH |
| **Interview** | 0 | 1 | 1 (interview_schedules) | MATCH |

### Automated Test Suite:
`php artisan test tests/Feature/ClientDrawerCandidateVisibilityRuleTest.php`  
Result: **10 passed (38 assertions)** in 3.61s.
