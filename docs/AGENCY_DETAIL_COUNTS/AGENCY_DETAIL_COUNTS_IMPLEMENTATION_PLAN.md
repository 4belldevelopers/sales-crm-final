# Agency Detail Counts & Data Isolation — Implementation Plan

## Overview
This implementation plan documents the precise technical steps to resolve the critical calculation bugs and enforce complete data isolation, search filtering, stale AJAX response protection, and server-side security authorization for the Agency Detail screen (`service_company/consultancy/dviewMore/{id}`).

---

## 1. Unified Agency Context & Relationship Model
For any requested agency detail drawer:
- **Client Company ID (`from_company_id` / `$company_id`)**: `auth()->user()->company_id`
- **Selected Agency Client Record (`$id`)**: `clients.id`
- **Selected Agency Company ID (`$partnerCompanyId` / `$to_company_id`)**: `clients.company_id`
- **Authorized Relation Check**: `DB::table('staffing_company_relation')->where('from_company_id', $company_id)->where('to_company_id', $partnerCompanyId)->where('final_status', 1)->exists()`

All ES and DB queries across all tabs must enforce `from_company_id = $company_id` AND agency identifier (`to_company_id` / `ClientComID` / `thirdparty_company_id` = `$partnerCompanyId`).

---

## 2. Affected Components & Proposed Changes

### A. Helper Layer: `app/Helper/GeneralMethod.php`
- Modify `agencyCandidatesCountList($to_company_id, $from_company_id)`:
  - Update candidate query to match both `ClientComID` and `to_company_id` / `thirdparty_company_id`.
  - Fix `offers` aggregation in ES to check `job_master_status_id = 5` OR `master_status_id = 5` OR `exists => ['field' => 'offer_released_at']`.
  - Fix `all_interview` aggregation on `ELASTIC_INERTVIEW_TRACKER` to check `to_company_id` OR `ClientComID`.
  - Implement DB query fallback when ES returns 0 or fails:
    - Jobs count: `recommendation_relation` JOIN `jobs`
    - Candidate count & stage stats: `add_job_wise_condidates`
    - Interviews count: `interview_schedules` JOIN `job_applications` / `add_job_wise_condidates`

### B. Controller Layer: `app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php`
- `dviewMore($id)`:
  - Enforce tenant ownership and relation guard.
  - Calculate accurate `$Active`, `$all_candidates`, `$all_interview`, `$offers`, `$Joined` counts using unified agency context.
- `dashboard_ajax($request, $id)`:
  - Verify tenant authorization guard.
  - Fix ES queries for candidate status breakdown, doughnut graph, candidate trends, and interview trends to match `from_company_id` and (`to_company_id` / `ClientComID`).
- `allreq_ajax(Request $request)`:
  - Add tenant authorization guard (`staffing_company_relation` check).
  - Add search filter condition for `$request->input('search')` or `$request->input('q')`.
  - Ensure total hits and pagination reflect the filtered agency jobs.

### C. Controller Layer: `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php`
- `staffingclientCandidateList(Request $request)`:
  - Add tenant authorization guard.
  - Support `$request->input('search')` matching `full_name`, `email`, `phone`, `skills`, `current_designation`.
  - Update ES bool query to match `to_company_id` OR `ClientComID`. Add SQL fallback.
- `staffingclientCandidateJoinList(Request $request)`:
  - Add tenant authorization guard.
  - Filter by `job_master_status_id = 6` (Joined).
  - Support `$request->input('search')`. Add SQL fallback.
- `staffingclientCandidateInterviewTracker(Request $request)`:
  - Add tenant authorization guard.
  - Update ES query to match `to_company_id` OR `ClientComID`.
  - Support `$request->input('search')`. Add SQL fallback.

### D. Blade & JavaScript: `resources/views/service_company/consultancy/dviewMore.blade.php`
- Update `AgencyDetailTabManager`:
  - `resetState()` must iterate through `activeControllers` and call `.abort()` before resetting objects.
  - Embed `currentAgencyId = '{{ $directClientDetail->company_id }}'` in JS context.
  - Validate `if (responseAgencyId !== AgencyDetailTabManager.currentAgencyId) return;` on all AJAX completion handlers.
  - Wire search input `#search` to trigger tab data re-load with search string.

---

## 3. Verification Plan
1. Test Agency A vs Agency B: Verify KPI cards and tab badges match selected agency's records exclusively.
2. Test Search in Jobs, Candidates, Interviews, and Joinings tabs for Agency A: Verify search results and total counts update strictly for Agency A.
3. Rapid Switching Test: Open Agency A, immediately open Agency B before Agency A AJAX finishes: Verify Agency A response is aborted and never populates Agency B.
4. Security Test: Attempt calling AJAX endpoints with unauthorized `clientid`: Verify 403 / 404 response.
