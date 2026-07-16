# Task Memory — Candidate Management

## Purpose
Manage candidates as **`JobApplication`** records (candidate attached to a job/pipeline),
plus bench candidates, CV/App trackers, notes, resumes, and statuses. This is the
busiest part of the app.

## Workflows
- Create/edit candidate (manual + resume upload) → MySQL → S3 resume → ES index.
- Bulk import (spreadsheet) → `JobApplicationsNewUpload` job.
- Move through pipeline statuses (`master_status_id`, `status`, `column_priority`).
- Bench management & job-wise matching (`add_job_wise_condidates`, `skill_match`).
- Forward candidate + resume by email.
See full traces in [DATA_FLOW.md](../../docs/DATA_FLOW.md) §1, §2, §6.

## Business rules (verify before changing)
- Every candidate belongs to a tenant (`company_id`). Raw queries must filter it.
- Resume stored as polymorphic `Document` (name="Resume") on S3.
- Any write must keep `ats_candidates_elastic` / `ats_candidates_bench_elastic` in sync
  (via Observer → ES job). **Don't bypass the observer.**
- `duplicate` flag, `is_viewed`, `rating` are tracked per application.
- Matching/`skill_match` is business logic — don't alter without confirmation.
- **Client-job pipeline (agency submits candidates to a client's job) has a stage-ownership
  split**: agency owns InReview(8)↔Sourcing(1)↔Reject(9), client owns Sourcing(1) through
  Rejected(7); client can never revert to InReview; candidates at stage 8/9 must stay
  invisible to the client until sourced.
- **Any future search bar / sort / filter work on an AppTracker candidate-list page**
  should reuse the design + logic already built for Similar Candidates / In-house Bench
  Talents / Agency Bench Talents / Total Candidates In Job (and their client-job
  mirrors) — adjust only the business logic specific to that page's own data (ES index,
  field names, available filters, sort tiebreak). Don't reinvent or copy blindly.
  Full rules + `master_status` table + enforcement locations + the search/sort/filter
  convention: [CLIENT_JOB_STAGE_OWNERSHIP.md](../../docs/CLIENT_JOB_STAGE_OWNERSHIP.md).

## Common files
- Controllers: `ServiceCompany/ServiceCompanyJobApplicationController.php` 🔴,
  `ServiceCompanyCvTrackerController.php` 🔴, `ServiceCompanyBenchCandidatesController.php` 🔴,
  `ProductCompany/ProductCompanyJobApplicationController.php`
- Services: `ElasticCandidatesIndex.php`, `ElasticBenchCandidatesIndex.php`,
  `JobApplicationsNewUploadService.php`, `CandidatesMailAndResumeForward.php`
- Models: `JobApplication`, `BenchApplication`, `AddJobWiseCondidate`, `Document`,
  `ApplicantNote`, `CandidateStatus`, `OfferJobApplication`
- Jobs: `ElasticCandidateIndex`, `UploadResumeToS3`, `JobApplicationsNewUpload`,
  `ProcessCandidateRequest`
- Observers: `JobApplicationObserver`, `ApplicationStatusObserver`, `DocumentObserver`
- Requests: `app/Http/Requests/*` (grep candidate/application)

## Gotchas
- ⚠️ `JobApplication` global company scope is **commented out** — confirm tenant filter.
- Wide table; watch N+1 and indexes. Grep controller methods, don't read whole (537 KB).
- Spelling `condidate` is intentional (table/pivot names).
