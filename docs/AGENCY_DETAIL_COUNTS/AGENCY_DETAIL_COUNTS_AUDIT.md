# Agency Detail Counts & Data Isolation — Audit Report

## Executive Summary
This document provides a thorough audit of the Agency Detail feature (`service_company/consultancy/dviewMore/{id}`) in JERO SaaS.
It details the root cause of incorrect calculations across KPI cards, tabs, AJAX endpoints, search queries, state contamination, and tenant isolation.

---

## 1. Scope & Architecture
- **Page / Feature**: Agency Detail Drawer (`service_company/consultancy` -> `dviewMore`)
- **Controllers Involved**:
  - [ServiceCompanyConsultancyController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php) (`dviewMore`, `dashboard_ajax`, `allreq_ajax`)
  - [ServiceCompanyJobApplicationController.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php) (`staffingclientCandidateList`, `staffingclientCandidateJoinList`, `staffingclientCandidateInterviewTracker`)
- **View File**:
  - [dviewMore.blade.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/resources/views/service_company/consultancy/dviewMore.blade.php)
- **Service/Helper Layer**:
  - [GeneralMethod.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Helper/GeneralMethod.php) (`agencyCandidatesCountList`, `agencyJobsCountAllstatus`)
  - [AgencyListingService.php](file:///c:/xampp/htdocs/git/JWMaincode28June2026/app/Services/AgencyListingService.php)

---

## 2. Identified Root Causes of Incorrect Red-Marked Counts

### A. Interviews Scheduled (KPI) & Interview Tracker Count (Tab + KPI)
- **Root Cause**:
  `GeneralMethod::agencyCandidatesCountList` and `ServiceCompanyJobApplicationController::staffingclientCandidateInterviewTracker` executed Elasticsearch queries on `ELASTIC_INERTVIEW_TRACKER` looking specifically for `['term' => ['ClientComID' => $to_company_id]]`.
  However, `IndexInterviewTrackerToElasticsearch.php` indexes documents with property `to_company_id`. Because `ClientComID` was not present on interview documents in ES, the aggregation returned `0` interviews.
- **Authoritative Relationship**:
  - Client Company ID = `auth()->user()->company_id` (`from_company_id`)
  - Agency Company ID = `clients.company_id` (`to_company_id` / `ClientComID`)
  - DB Table = `interview_schedules` JOIN `job_applications` / `add_job_wise_condidates` WHERE `company_id` = Client AND `thirdparty_company_id` = Agency.

### B. Offers Generated (KPI)
- **Root Cause**:
  In `GeneralMethod::agencyCandidatesCountList` (lines 14922-14928), the offer count aggregation queried `'exists' => ['field' => 'of_created_date']`.
  However, `IndexCandidatesToElasticsearch.php` defines the offer date field as `offer_released_at` and stores `job_master_status_id = 5` (Offered). The `of_created_date` field does not exist in ES mapping, causing the count to resolve to `0`.
- **Authoritative Relationship**:
  - Candidate pipeline stage `master_status_id = 5` (Offered) OR presence of `offer_released_at` / `offer_job_applications` record.

### C. Successful Joins (KPI & Joinings Tab Count)
- **Root Cause**:
  `staffingclientCandidateJoinList` in `ServiceCompanyJobApplicationController.php` only queried `ClientComID` = `$to_company_id` without checking `to_company_id` or `thirdparty_company_id`. If `ClientComID` was null on placed candidate ES documents, the query returned `0` records.
- **Authoritative Relationship**:
  - Candidate pipeline stage `master_status_id = 6` (Joined) with `from_company_id` = Client AND (`to_company_id` / `thirdparty_company_id` / `ClientComID` = Agency).

### D. Candidates Submitted (KPI & Candidates Tab Count)
- **Root Cause**:
  Candidate count helper in `GeneralMethod::agencyCandidatesCountList` did not combine all agency identifier aliases (`ClientComID`, `to_company_id`, `thirdparty_company_id`), missing valid candidate submissions.

---

## 3. Data Isolation, Search & Stale AJAX Vulnerabilities

1. **Unfiltered Search**:
   - On the Agency Detail screen, typing in `#search` invoked `loadTabData`, passing `{ search: searchVal }` to AJAX endpoints.
   - None of the 4 tab AJAX endpoints (`staffingclientCandidateList`, `staffingclientCandidateJoinList`, `staffingclientCandidateInterviewTracker`, `allreq_ajax`) included the `search` string in their ES query `must` clauses. As a result, search was ineffective and totals did not re-calculate after filtering.

2. **Stale Response Contamination**:
   - `AgencyDetailTabManager.resetState()` cleared `activeControllers` without calling `.abort()` on in-flight requests.
   - When switching rapidly between Agency A and Agency B, an in-flight response from Agency A shared the same sequence ID `1` as Agency B, causing Agency A data to overwrite Agency B's UI drawer.

3. **Server-Side Security Authorization**:
   - `allreq_ajax`, `staffingclientCandidateList`, `staffingclientCandidateJoinList`, and `staffingclientCandidateInterviewTracker` trusted `clientid` / `id` from `$_GET` without validating `staffing_company_relation` (`final_status = 1`) server-side.

---

## 4. Remediation Requirements Strategy
1. Unify agency identifier mapping (`from_company_id`, `to_company_id`, `ClientComID`, `thirdparty_company_id`) across all ES queries and SQL queries.
2. Fix ES aggregations and provide DB query fallbacks.
3. Enforce server-side authorization guards on all AJAX endpoints.
4. Implement strict search filtering across all tabs.
5. Upgrade `AgencyDetailTabManager` frontend state manager to abort in-flight requests and validate `currentAgencyId` on every response callback.
