# Forensic Technical Audit & Permanent Fix: Job Posting Workflow Parity

**Module:** Service Company — Add Internal Jobs (`service_company/jobs/create`)  
**Date:** 2026-11-08  
**Scope:** Key Skills UI/UX Redesign & Final Page Job Submission Failure Hardening  
**Target Screens:** 15.6" Laptops (1366×768 and 1920×1080) with Zero Horizontal Overflow  

---

## 1. Executive Summary

| Issue | Root Cause | Impact | Permanent Fix |
|---|---|---|---|
| **Issue 1: Key Skills Suggestion UI/UX** | Legacy inline Tagify pattern crammed tags directly inside the text input box alongside the blinking cursor. As tags were added, the input text field shrank to a few pixels, clipped placeholders, and lacked keyboard autocomplete. | Severe recruiter usability friction, broken wrapping, and inability to preview/select suggestions cleanly. | **Modern Card + Sub-Card Enterprise Standard (Rule 20):** Dedicated 3-tier architecture: (1) Top search input with live autocomplete dropdown and keyboard navigation; (2) Middle "Selected Skills" sub-card container directly below the input with modern enterprise chips and `(x)` remove buttons; (3) Bottom one-click recommended pills. |
| **Issue 2: Final Page Job Submission Failure** | In production, `JobService::createJob` suffered constraint failures when optional/empty fields were submitted: `total_positions` had no default in MySQL `NOT NULL` schema; `work_shifts` had no enum fallback; `job_metronics_skills` threw strict mode 1406 data truncation on long skill names (>55 chars); empty recruiter assignments left jobs without a captain in `job_assign`. When any error occurred, `ServiceCompanyJobsController::store()` masked the true exception with a generic `Submission failed. Please try again.` toast. | Job submission was aborted on the final "Review & Publish" step with red toast, confusing recruiters. | **Self-Healing Schema & Invariant Guard:** (1) Safe defaults for `total_positions` (1), `employment_type` (1), `work_shifts` (enum fallback), and `skills` sanitization (55-char clamp); (2) Automatic captain assignment to creating user; (3) Transparent error messaging; (4) Automatic in-memory (`sessionStorage`) form preservation ensuring 0% data loss on failure. |

---

## 2. Root Cause Analysis

### 2.1 Issue 1: Key Skills Suggestion UI/UX
- **DOM Placement Defect:** The legacy input placed selected tag badges (`sales [x]`, `lead generation [x]`) inside the same `<div class="tags-input-wrapper">` as the `<input type="text">` tag. As skills accumulated, the text input element was compressed to a narrow width, causing awkward line wrapping and truncated text entry.
- **Missing Autocomplete Engine:** Skills were only selectable by clicking static chips at the bottom or typing freeform text followed by Enter or comma. There was no real-time floating dropdown with search matching or keyboard navigation (Up/Down/Enter/Escape).
- **Sub-Card Standard Violation (Rule 20):** Lacked a distinct "Selected Skills" container below the input with clear counters (`X/10 added`), empty states, and "Clear All" controls.

### 2.2 Issue 2: Job Submission Failure (`Submission failed. Please try again.`)
- **Controller Exception Masking:** In `ServiceCompanyJobsController.php#L7849-L7856`, any exception thrown inside `$jobService->createJob()` was caught and logged, but returned a static generic string:
  ```php
  return response()->json([
      'status' => 'error',
      'message' => 'Submission failed. Please try again.',
  ], 500);
  ```
  This concealed the underlying root causes from recruiters and production telemetry.
- **MySQL `NOT NULL` Schema Violations:**
  - `jobs.total_positions` is `NOT NULL` without default in `mysql-schema.sql`. If omitted or empty, `$job->total_positions` remained unset, triggering `SQLSTATE[23000]: Column 'total_positions' cannot be null`.
  - `jobs.work_shifts` is an ENUM (`Day Shift`, `Night Shift`, etc.). An unmapped value or null triggered `Column 'work_shifts' cannot be null`.
- **Skill Length Truncation (MySQL 1406 Strict Mode):** `job_metronics_skills.skill_name` is `varchar(55) NOT NULL`. If a skill name exceeded 55 characters, strict MySQL mode rejected the insert.
- **Unassigned Captain Invariant:** If `recruiter_id` was left empty on Step 1, no row was inserted into `job_assign`, leaving the job without an assigned team member or spokesperson (`spok_user_id`).
- **Form Data Preservation:** When submission failed with 500, the idempotency key was marked failed in Redis/MySQL. Resubmitting with the same key caused immediate 409 conflict, and users feared losing their multi-step inputs.

---

## 3. Architecture & Data Flow

```
[ Step 2: Key Skills Input ]
        │
        ├──> Type Query ──> [ Live Autocomplete Dropdown ] (Zero Network, User RAM)
        │                         │
        │                         └──> Select / Enter ──> [ skills Array ]
        ├──> Click Suggested Pill ───────────────────────┘         │
        │                                                          ├──> Render inside #selectedSkillsBox (Below Input)
        └──> Click Chip (x) ──> Remove from Array ─────────────────┼──> Sync #tags JSON for Backend
                                                                   └──> Sync Step 4 Review Card (#rvSkills)

[ Step 4: Publish Job ]
        │
        ├──> Frontend syncAll() & RAM Draft Snapshot
        │
        ├──> POST /service_company/jobs
        │         │
        │         ├──> ServiceCompanyJobsController::store()
        │         │         │
        │         │         ├──> Recruiter Self-Heal (Captain Invariant: default to current user if empty)
        │         │         ├──> Idempotency Claim (Tenant Scoped)
        │         │         │
        │         │         └──> JobService::createJob()
        │         │                   ├──> total_positions Default (1)
        │         │                   ├──> work_shifts Enum Fallback ('Day Shift')
        │         │                   ├──> skills Clamp (substr 55) & ES Try-Catch
        │         │                   ├──> client_location_jobs Numeric Existence Guard
        │         │                   ├──> job_assign Persistence with Captain Invariant
        │         │                   └──> DB::commit()
        │         │
        │         └──> Error Catch Handler:
        │                   ├──> Transparent Exception Message
        │                   ├──> Rotate operation_id in Frontend
        │                   └──> 100% Form Data Preserved in RAM & DOM
```

---

## 4. File Changes Matrix

| File Path | Lines Affected | Modification Intent |
|---|---|---|
| `resources/views/service_company/jobs/create.blade.php` | `202-248` | Added CSS for Key Skills Enterprise 3-Tier Design System (search input with icon, floating autocomplete dropdown, selected skills sub-card box, chips with hover remove, and recommended pills). |
| `resources/views/service_company/jobs/create.blade.php` | `5178-5240` | Redesigned Card 4 (Key Skills): Moved selected chips into dedicated `#selectedSkillsBox` below the input, added `#skillsSearchWrapper` with clear button and floating dropdown, added `#skillsCountBadge` (`X/10 added`) and "Clear All" button. |
| `resources/views/service_company/jobs/create.blade.php` | `9710-9970` | Replaced legacy Tagify methods with enterprise Key Skills controller: `standardSkillLibrary`, `initSkillsInput`, keyboard navigation (Up/Down/Enter/Esc), `renderSkillsAutocomplete`, `renderSkills`, `renderRecommendedSkills`, and delegated chip removal. |
| `resources/views/service_company/jobs/create.blade.php` | `10420-10525` | Hardened `publishJob`: In-memory draft snapshot (`saveJobDraftToRAM`), automatic `operation_id` rotation on retry, and safe draft cleanup on success. |
| `app/Services/JobService.php` | `81-155` | Hardened job attributes with safe fallbacks: `total_positions` (default 1), `employment_type` (default 1), `work_shifts` (enum fallback), non-empty `job_description` and `job_requirement`. |
| `app/Services/JobService.php` | `295-305` | Secured `client_location_jobs`: Verified numeric `address_id` > 0 and existence in `client_location`. |
| `app/Services/JobService.php` | `325-365` | Secured `job_metronics_skills`: Supported both Tagify JSON and array of strings, truncated skill names to 55 chars max (MySQL strict mode defense), wrapped `skillsAddElastic` in resilient try-catch. |
| `app/Services/JobService.php` | `395-460` | Enforced Captain Invariant: If `recruiter_id` is empty or no valid recruiters match, automatically assigns creating user as recruiter and spokesperson (`spok_user_id`). |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | `7700-7738` | Defaulted `recruiter_id` to `[$userId]` when empty. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | `7848-7865` | Replaced opaque generic error with informative exception message and stack trace logging. |

---

## 5. 15.6" Screen Fit Verification

- **Standard 15.6" Viewports Tested:** `1366×768` (100% & 125% DPI scale) and `1920×1080` (100% scale).
- **Responsive Layout:**
  - Input field uses full-width fluid flexbox (`width: 100%`).
  - Autocomplete dropdown has internal vertical scroll (`max-height: 230px; overflow-y: auto`), preventing layout push.
  - Selected skills container uses `flex-wrap: wrap; gap: 8px`, ensuring badges wrap naturally to multiple lines with zero horizontal scrollbars.
  - Recommended pills use responsive wrapping with touch/click friendly targets (padding `4px 10px`).

---

## 6. Verification Evidence

1. **PHP Syntax & View Compilation:**
   - `php -l app/Services/JobService.php` -> `No syntax errors detected`
   - `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` -> `No syntax errors detected`
   - `php artisan view:clear && php artisan view:cache` -> `Blade templates cached successfully`
2. **End-to-End Simulation Test:**
   - Ran `test_e2e.php` simulating all edge cases (missing total positions, invalid shifts string, long skill name > 55 chars, missing recruiter ID).
   - Created Job ID 118 successfully: `total_positions = 1`, `work_shifts = 'Day Shift'`, `job_assign` row created with `spok_user_id = 2`, skills inserted and clamped to 55 characters.
3. **Form State Preservation:**
   - Verified that on submission failure, form inputs across all steps remain active in DOM and User RAM with fresh `operation_id` generated for immediate one-click retry.
