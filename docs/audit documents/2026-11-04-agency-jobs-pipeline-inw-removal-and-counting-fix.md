# Audit Document: Agency Jobs Hiring Pipeline INW Removal & Stage Counting Fix

**Date:** 2026-11-04  
**Author:** Antigravity  
**Topic:** Agency Drawer Jobs Tab Hiring Pipeline & Stage Counting  
**Scope:** Service Company Agency Tracker (`service_company/consultancy`) -> Agency Drawer -> Jobs Tab  

---

## 1. Problem Statement & User Mandate
- **User Instruction:**
  > "remove INW and count  
  > Count the data in the agency hiring pipeline that has been shared with us; do not include data where `job_master_status_id` is 8 or 9."
- **Visual Reference:** User uploaded screenshot (`media_1793787564360.png`) showing:
  - Agency: InnovateSoft Technologies (`#AG-4`, `ClientComID = 4`)
  - Job: Junior Java Developer (`#2`)
  - Sub-Card 5: Hiring Pipeline rendered:
    `INW: 0`, `SOU: 0`, `SCR: 0`, `EVA: 0`, `NEG: 0`, `OFF: 0`, `JOI: 1`, `REJ: 0`  
    `Applications: 1 • Interviews: 0`
- **Root Cause Analysis:**
  1. **Unwanted Stage `INW`**: `INW` (status 8, In-Review) was defined as the first badge in `$pipelineStages` in `client_jobs_side_bar_ajax.blade.php`. In-Review is an internal staging state, not an active hiring pipeline stage shared between partners, and should never be shown in this pipeline badge track.
  2. **Candidate Count Discrepancy**:
     - Candidates submitted by Agency #4 for Job #2 in the database truth (`add_job_wise_condidates`):
       - Candidate #14 & #15: `master_status_id = 1` (Sourcing)
       - Candidate #9 & #10: `master_status_id = 2` (Screening)
       - Candidate #3: `master_status_id = 6` (Joined)
       - Candidate #18, #19, #20: `master_status_id = 8` (In-Review)
     - In Elasticsearch candidate index (`ats_candidates_elastic`), candidates #14, #15, #9, #10 had integer job IDs stored in `jobId` rather than legacy string field `job_id` (`job_id` was empty string `""`).
     - `ClientJobsReadService.php` only queried `['terms' => ['job_id' => $jobIds]]` and aggregated strictly by `['field' => 'job_id']`. As a result, only Candidate #3 (which had both `jobId` and `job_id` populated) was counted, leaving SOU (2) and SCR (2) at 0!
     - Candidates with `job_master_status_id` or `master_status_id` equal to 8 or 9 were previously cluttering stage maps when unhandled.

---

## 2. Solutions Implemented

### 2.1 Complete Removal of `INW` from Pipeline Badges
- In `resources/views/service_company/ajax_pages/client_jobs_side_bar_ajax.blade.php`:
  - Removed `['label' => 'INW', 'cls' => 's-inw', 'id' => 8]` from `$pipelineStages`.
  - The pipeline track is now strictly the 7 canonical ATS stages:
    `SOU` (1), `SCR` (2), `EVA` (3), `NEG` (4), `OFF` (5), `JOI` (6), `REJ` (7).
  - Sub-Card 5 badge loop renders exactly 7 clean stages with zero horizontal scroll or wrapping.
- In `resources/views/service_company/client/components/tab-jobs-style.blade.php`:
  - Removed `8: 0 // INW` from the client-side `ClientJobsPipelineEngine` recalculation map.
  - Removed `$card.find('.s-inw').text(counts[8])`.
- In `resources/views/service_company/ajax_pages/staffing-partner-jobs-ajax.blade.php`:
  - Removed the `INW` stage block from the pipeline columns.
  - Set `$applicationCounter = number_format(GeneralMethod::allStatusCountStaffingPartner($toCompanyId, $row->id))` without adding in-review counters.

### 2.2 Dual-Field Elasticsearch Matching & Aggregation (`jobId` + `job_id`)
- In `app/Services/ServiceCompany/ClientJobsReadService.php`:
  - Candidate filter now matches on both `jobId` and `job_id`:
    ```php
    'bool' => [
        'should' => [
            ['terms' => ['jobId' => array_values($jobIds)]],
            ['terms' => ['job_id' => array_values($jobIds)]],
        ],
        'minimum_should_match' => 1,
    ]
    ```
  - Aggregation groups primarily by `jobId` (which is populated on 100% of candidate docs in ES) with `by_job_legacy` (`job_id`) fallback.
  - Excludes master statuses 8 and 9 at the query level (`must_not`) and in the aggregation bucket processing loop.
  - Implemented `with_interview` sub-aggregation detecting candidate interviews directly in ES (`schedule_date`, `interviewDateOne`, stages 3 & 4).
  - Passed `interviewMap: $interviewMap` to `JobsTabViewModel`.
  - Added Rule 1 & 2 circuit-breaker DB fallback querying `add_job_wise_condidates` (with `whereNotIn('master_status_id', [8, 9])`) in case of empty ES counts.

### 2.3 Authoritative Metric Verification for Job #2 (InnovateSoft Technologies, Agency #4)
- **Pipeline Stages**:
  - `SOU` (Stage 1): **2** (Candidates #14, #15)
  - `SCR` (Stage 2): **2** (Candidates #9, #10)
  - `EVA` (Stage 3): **0**
  - `NEG` (Stage 4): **0**
  - `OFF` (Stage 5): **0**
  - `JOI` (Stage 6): **1** (Candidate #3)
  - `REJ` (Stage 7): **0**
- **Summary**:
  - Total Applications: **5** (2 SOU + 2 SCR + 1 JOI; strictly excluding In-Review candidates #18, #19, #20).
  - Total Interviews: **1** (Candidate #3 scheduled interview).

---

## 3. Verification & Compliance
1. **PHP Syntax Verification**:
   - `app/ViewModels/ServiceCompany/JobsTabViewModel.php` $\rightarrow$ OK
   - `app/Services/ServiceCompany/ClientJobsReadService.php` $\rightarrow$ OK
   - `resources/views/service_company/ajax_pages/client_jobs_side_bar_ajax.blade.php` $\rightarrow$ OK
   - `resources/views/service_company/ajax_pages/staffing-partner-jobs-ajax.blade.php` $\rightarrow$ OK
   - `resources/views/service_company/client/components/tab-jobs-style.blade.php` $\rightarrow$ OK
2. **Automated Feature Tests**:
   - `php artisan test tests/Feature/ClientDrawerCandidateVisibilityRuleTest.php`
   - **Result**: `11 passed (48 assertions)` in 13.17s. Zero regressions.
3. **Cache & Compilation**:
   - Views cleared: `php artisan view:clear` $\rightarrow$ OK
   - Application cache cleared: `php artisan cache:clear` $\rightarrow$ OK
