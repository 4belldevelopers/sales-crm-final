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
