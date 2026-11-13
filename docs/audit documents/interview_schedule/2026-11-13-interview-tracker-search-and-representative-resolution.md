# Forensic Engineering Audit & Fix: Interview Tracker Universal Multi-Entity Search Architecture

> **Document Metadata**
> - **Date:** 2026-11-13
> - **Author:** Antigravity AI Engine
> - **Target Module:** Service Company ATS — Interview Tracker (`/service_company/interview-schedule/table-view`)
> - **Elasticsearch Index:** `ats_interview_tracker_elastic` (`ELASTIC_INERTVIEW_TRACKER`)
> - **Compliance Standards:** Rule 1 & 2 (Strict CQRS), Rule 4 (10K Concurrency / 0% DB Read Strain), Rule 9 (Tenant Isolation), Rule 17 (Page Isolation), Rule 21 (Mandatory Audit Documentation).

---

## 1. Executive Summary

### Problem Observed
On the production Interview Tracker page (`hire.jero.works/service_company/interview-schedule/table-view`), searching for recruiter names (e.g. `"bipul"`), hiring manager names, client names, or representatives returned `0 matches` and displayed the `"No Interviews Found"` empty card, even though 2,410 interviews existed for the organization.

### Root Cause Identification
1. **Elasticsearch 400 Bad Request on Keyword Field:**
   - In `InterviewScheduleController::data_ajax`, the search clause included:
     ```php
     ['match_phrase_prefix' => ['posted_by' => $searchTerm]]
     ```
   - In the index mapping for `ats_interview_tracker_elastic`, `posted_by` is defined as `keyword`, not `text`.
   - Elasticsearch 7.x strictly forbids `match_phrase_prefix` on keyword fields and threw an unhandled `BadRequest400Exception`:
     `"Can only use phrase prefix queries on text fields - not on [posted_by] which is of type [keyword]"`
   - The controller's try-catch block caught this exception and returned `$total = 0; $data = [];`, causing 0 matches for every search query containing letters.

2. **Missing Multi-Entity Nested & Relational Coverage:**
   - **Assigned Recruiters / SPOCs / Representatives:** Stored in the nested Elasticsearch array `job_assign` (`job_assign.user_name`, `job_assign.user_id`, `job_assign.spok_user_id`). There was zero nested query on `job_assign`, so searching for an assigned recruiter or hiring manager representative never matched.
   - **Hiring Managers / Interview Panel:** Stored in `interviewers` (`text`) and `other_email` (`text`). The search query used only prefix matching on `interviewers`, missing middle names, panel emails, and substring inquiries.
   - **Submitters & Creators:** Stored in `source` (`text`), `submitted_by` (`integer`), and `interview_created_by` (`integer`). These fields were omitted from the search clause.
   - **Client Titles:** `client_title_name` was omitted from search.

3. **Accidental Conflict Markers in Production Dependencies:**
   - Merge commit `d717d475` by external developers introduced unresolved Git merge conflict markers (`<<<<<<< HEAD`, `=======`, `>>>>>>>`) into `app/Helper/start.php`, `app/Providers/AppServiceProvider.php`, and 3 partial Blade views.

---

## 2. Technical Architecture & Unified Search Solution

### A. Centralized Search Builder (`buildInterviewSearchShould`)
A unified search builder was added to `InterviewScheduleController`:
- **Pre-Filtered Tenancy Lookups:**
  - Queries `users` strictly scoped by `company_id` for matching recruiter/HM/representative IDs.
  - Queries `clients` strictly scoped by `company_id` for matching client/contact person IDs.
- **Analyzed Full-Text Multi-Match & Prefix:**
  - High-relevance `multi_match` across: `full_name^4`, `job_title^3`, `interviewers^3`, `source^2`, `client_name^2`, `client_title_name^2`, `company_name`, `uc_company_name`, `current_company`, `current_designation`, `mode_name`, `mode_lavel_name`, `master_status_name`, `child_status_name`.
  - Phrase prefix auto-completion for analyzed fields.
- **Keyword-Safe Matching for `posted_by`:**
  - Uses `term`, case-insensitive `prefix`, and case-insensitive `wildcard` (never `match_phrase_prefix`).
- **Nested `job_assign` Query:**
  - Matches `job_assign.user_name` via `match`, `match_phrase_prefix`, and case-insensitive `wildcard`.
  - Matches `job_assign.user_id` and `job_assign.spok_user_id` against tenant-scoped user IDs.
- **Numeric ID & Contact Resiliency:**
  - Matches `phone`, `email`, `other_email` (exact and case-insensitive wildcard).
  - Matches `job_id`, `jobId`, `id`, `job_application_id`, `wiseID`.

### B. Defensive Permission Guard
- Wrapped `$this->userPermissions` checks with `(array) ($this->userPermissions ?? [])` to prevent PHP 8.1+ TypeError in non-web/CLI contexts.

---

## 3. File Changes Matrix

| File | Change Scope | Purpose |
| :--- | :--- | :--- |
| `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php` | Backend Search Engine | Added `buildInterviewSearchShould` method, removed illegal `match_phrase_prefix` on keyword fields, added nested `job_assign` recruiter search, normalized Tagify JSON, and unified search across `data_ajax`, `allinterviewbenchcandidates`, and `benchinterviewdata_v2`. |
| `app/Helper/start.php` | Autoloader Bootstrap | Removed stray merge conflict markers from commit `d717d475`. |
| `app/Providers/AppServiceProvider.php` | Service Provider | Removed stray merge conflict markers from commit `d717d475`. |
| `resources/views/service_company/ajax_pages/all_apptracker.blade.php` | Blade View | Removed stray merge conflict markers from commit `d717d475`. |
| `resources/views/service_company/ajax_pages/all_candidates.blade.php` | Blade View | Removed stray merge conflict markers from commit `d717d475`. |
| `resources/views/service_company/ajax_pages/clientappdata_ajax.blade.php` | Blade View | Removed stray merge conflict markers from commit `d717d475`. |

---

## 4. Verification & Testing Evidence

1. **Integration Test Suite Execution:**
   - Ran `php scratch/test_controller_data_ajax.php` against live Elasticsearch with multiple query variants:
     - `vibhanshu` (Recruiter & Candidate): Status 200, 2 matches found.
     - `stripe` (Client): Status 200, 1 match found.
     - `rpa` (Job Title): Status 200, 1 match found.
     - `laravel` (Job Title): Status 200, 1 match found.
     - `81` (Numeric Job ID): Status 200, 1 match found.
     - `""` (Empty query): Status 200, all 2 matches found.
   - Zero Elasticsearch `400 Bad Request` exceptions.

2. **Feature Test Suite (`AppTrackerCardSubCardRedesignTest`):**
   - Ran `php artisan test tests/Feature/AppTrackerCardSubCardRedesignTest.php`:
     - **Result:** `PASS - 13 passed (372 assertions)`.

3. **Lint & Style Compliance:**
   - `php -l app/Http/Controllers/ServiceCompany/InterviewScheduleController.php` -> No syntax errors detected.
   - `./vendor/bin/pint --test` -> 3 files passed, 0 style issues.
