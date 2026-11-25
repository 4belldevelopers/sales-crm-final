# Interview Tracker — Pipeline & Stage Sub-Card Adjustment Audit

## Metadata

| Field | Detail |
| :--- | :--- |
| **Module / Feature** | Service Company Interview Tracker (`service_company/interview-schedule/table-view`) |
| **Date** | 2026-11-25 |
| **Controllers** | `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php`, `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` |
| **Views** | `resources/views/service_company/interview-schedule/table.blade.php`, `resources/views/service_company/ajax_pages/interview-tracker-ajax.blade.php` |
| **Tests** | `tests/Feature/ServiceCompanyInterviewTrackerTest.php`, `tests/Feature/ServiceCompanyBenchInterviewScheduleTest.php` |
| **Rules Applied** | Rule 1, Rule 2, Rule 5, Rule 6, Rule 7, Rule 14, Rule 17, Rule 18, Rule 20, Rule 21, Rule 24, Rule 26, Rule 27 |

## Executive Summary

Adjusted the **Pipeline & Stage** presentation on the Service Company **Interview Tracker** (`/service_company/interview-schedule/table-view`) per user layout decisions:

1. **Dedicated 7th Upper Sub-Card (`Pipeline & Stage`):** Moved Stage and Sub-stage out of the bottom card footer strip (`.it-card-bottom`) into a dedicated display-only 7th sub-card (`.it-subcard.it-subcard-stage`) inside `.it-card-grid` alongside Candidate, Requisition, Schedule, Panelists, Ownership & Recruiter, and Evaluation.
2. **Display-Only Interaction:** Removed interactive `#csd_drawer` triggers (`stage_sub_stage`, `Update Stage >`, and `.it-stage-cell`) from Interview Tracker cards; the sub-card displays live stage badge, sub-stage (`Sub: <name>`, defaulting to `Private` for `InReview` / ID `8` or `Standard` otherwise), proportional pipeline progress bar, and step counter (`Step X of 7` or `Exited Pipeline`).
3. **Live ATS Stage Synchronization & Timezone Safety (Rules 5, 6, 24):** Enriched `InterviewScheduleController::prepareViewMaps()` with live `master_status_id` / `child_status_id` from `ELASTIC_CANDIDATES` (`mget`), canonical `masterStatusMap`, batched `childStatusMap` (`candidate_status` `whereIn`), and pre-resolved `tenantTimezone` (with safe fallback in Blade/tests). Added `allinterviewschedule_version_user_*` / `company_id` cache invalidation to `ServiceCompanyJobApplicationController::stage_submit()` and `client_stage_submit()`.

## Architecture & Data Flow

1. **Read Path (`InterviewScheduleController::data_ajax()` -> `prepareViewMaps()`):**
   - Primary schedule rows continue to come from `ELASTIC_INERTVIEW_TRACKER`.
   - `prepareViewMaps()` fetches live candidate metadata from `ELASTIC_CANDIDATES` via `mget` (`_source`: `id`, `current_company`, `created_at`, `job_application_id`, `job_master_status_id`, `childStatus`, `name`) and scoped fallback records, caching `it_master_status_id_name_map` (`masterStatusMap`), batching `candidate_status` names in one `whereIn` query (`childStatusMap`), and resolving `tenantTimezone` once via `GeneralMethod::defaultTimeZone($companyId)` (defaulting cleanly to `'Asia/Kolkata'` on fallback).
   - Bumped AJAX response UI version marker to `'ui_v' => 'card_v9'` and shell IndexedDB `DB_VERSION = 8` so stale cached HTML cards are purged automatically.
2. **Stage Resolution Order (`interview-tracker-ajax.blade.php`):**
   - Resolves `$candMeta` from `$candidateMetaMap['by_wise'][$wiseId]` or `$candidateMetaMap['by_app'][$jobAppId]`.
   - Prefers live `$candMeta['master_status_id']` and `$candMeta['child_status_id']` over snapshot values cached on `$row`, mapping canonical colors and order (`1..7` / `Exit`) through `GeneralMethod::pipelineStageTokens()`.
   - When `master_status_id === 8` (`InReview`) has no explicit sub-stage, resolves sub-stage label to `Private`; other stages fall back to `Standard`.
   - Follow-up timestamps in `.it-card-bottom` format via `$tenantTimezone` (`d M y, h:i A T`).
3. **Cache Invalidation & Live Delta Updates:**
   - `ServiceCompanyJobApplicationController::stage_submit()` and `client_stage_submit()` now pass `$finalCompanyId` to `GeneralMethod::cacheClean(...)` including `'allinterviewschedule_version_user_' . $currentUserId` alongside company-scoped invalidation.
   - Client-side `patchInterviewRowDelta()` targets `.it-subcard-stage .it-stage-badge` and `.it-subcard-stage .it-substage-txt strong` upon `candidate.drawer.updated` Reverb broadcasts.

## File Changes Matrix

| File | Summary of Changes | Scope / Isolation |
| :--- | :--- | :--- |
| `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php` | Bumped `'ui_v' => 'card_v9'`; added live stage/substage `_source` fields in `ELASTIC_CANDIDATES` `mget` + fallbacks; added `masterStatusMap`, batched `childStatusMap`, and `tenantTimezone` in `prepareViewMaps()` / empty branch | Service Company Interview Tracker only |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobApplicationController.php` | Added `'allinterviewschedule_version_user_' . $currentUserId` to `$cachekeys` with `$finalCompanyId` in `stage_submit()` and `client_stage_submit()` | Tenant-scoped cache invalidation |
| `resources/views/service_company/ajax_pages/interview-tracker-ajax.blade.php` | Added display-only 7th sub-card `.it-subcard-stage` with stage badge, sub-stage label, progress bar, and step counter; removed `.it-stage-cell` from `.it-card-bottom`; guarded `$tenantTimezone` lookup; formatted follow-up timestamp with `$tenantTimezone` | Page-isolated partial |
| `resources/views/service_company/interview-schedule/table.blade.php` | Updated `.it-card-grid` to 7 balanced `minmax(0, ...fr)` columns, added `.it-subcard-stage` / `.it-pipeline-*` styles (`prefers-reduced-motion` compliant), tuned `@media` breakpoints, bumped `DB_VERSION = 8`, updated `patchInterviewRowDelta()` | Page-isolated shell |
| `tests/Feature/ServiceCompanyInterviewTrackerTest.php` | Updated Rule 20 assertions for 7 sub-cards + bottom strip removal + display-only guard; added `table.blade.php` CSS/DB v8 assertions; added `test_interview_tracker_pipeline_subcard_prefers_live_candidate_meta_and_substage` | Feature verification |

## 15.6" Screen Fit (`1366×768` & `1920×1080`)

- **7-Column Desktop Grid (`.it-card-grid`):**
  `grid-template-columns: minmax(0, 1.52fr) minmax(0, 1.82fr) minmax(0, 1.32fr) minmax(0, 1.22fr) minmax(0, 1.44fr) minmax(0, 1.38fr) minmax(0, 1.3fr)` prevents horizontal overflow via `minmax(0, ...)` tracks inside the card container on `1920×1080` and `1366×768` (`@media (max-width: 1440px)` compact padding/avatar/label sizing).
- **Responsive Breakpoints:** Transitions smoothly to 4 columns at `@media (max-width: 1200px)` and 3 columns at `@media (max-width: 992px)`.
- **Motion & Craft:** Uses `#f8fafc` sub-card header background (`#e2e8f0` border), `tabular-nums`, and `@media (prefers-reduced-motion: reduce)` guards on `.it-pipeline-fill`.

## Verification Evidence

Verified after the final edits on 2026-11-25:

1. **PHP Syntax Lint (`php -l`):** Passed (`No syntax errors detected`) across all 5 touched PHP/Blade files (`InterviewScheduleController.php`, `ServiceCompanyJobApplicationController.php`, `interview-tracker-ajax.blade.php`, `table.blade.php`, `ServiceCompanyInterviewTrackerTest.php`).
2. **Code Style (`./vendor/bin/pint --test`):**
   - Passed (`PASS`, 2 files) on `InterviewScheduleController.php` and `ServiceCompanyInterviewTrackerTest.php`.
   - `ServiceCompanyJobApplicationController.php` contains pre-existing Pint notices outside the touched cache invalidation lines; our new lines follow the surrounding file concatenation style.
3. **PHPUnit Test Suites (`php artisan test tests/Feature/ServiceCompanyInterviewTrackerTest.php tests/Feature/ServiceCompanyBenchInterviewScheduleTest.php`):**
   - `ServiceCompanyInterviewTrackerTest`: **8 passed** (including new `test_interview_tracker_pipeline_subcard_prefers_live_candidate_meta_and_substage`).
   - `ServiceCompanyBenchInterviewScheduleTest`: **4 passed** (confirming zero regressions on Talent Bench Interviews).
   - **Total: 12 passed (219 assertions)**, exit code `0`.
