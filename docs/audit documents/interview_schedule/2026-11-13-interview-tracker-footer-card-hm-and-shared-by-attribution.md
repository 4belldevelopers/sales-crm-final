# Interview Tracker Footer Card Attribution (HM & Shared By) Implementation Audit

## 1. Metadata
- **Module:** Service Company Interview Tracker (`/service_company/interview-schedule/table-view`)
- **Date:** 2026-11-13
- **Author:** Antigravity Agent
- **Standard Reference:** Rule 1 (CQRS), Rule 2 (Universal ES Read), Rule 14 & Rule 20 (Card + Sub-Card Design System), Rule 21 (Mandatory Audit Location), Rule 25 (Canonical Tags & Tokens)

---

## 2. Executive Summary
The user requested adding dynamic attribution to the footer card / bottom activity strip (`.it-card-bottom`) of each primary interview card on the Service Company Interview Tracker:
- **For Internal Client or My Organization (`!$isDirectClient`):** Display the **Hiring Manager (HM)** (e.g., `HM: Dr. Rajesh Sharma (Engineering VP)`).
- **For Direct Client (`$isDirectClient`):** Display **Shared by** (e.g., `Shared by: Vikram Malhotra · Apex Staffing` or `Shared by: Apex Staffing`).
- **UI/UX Design Adjustment:** Responsive attribution pill (`.it-attribution-cell`) with canonical color tokens, Font Awesome icons, text truncation, and full tooltip hover to guarantee 100% responsiveness and zero horizontal overflow on 15.6" screens (`1366×768` and `1920×1080`).

---

## 3. Architecture & Data Flow

### 3.1 Pre-hydration & Zero N+1 Queries (Rule 1 & Rule 2 CQRS)
In `InterviewScheduleController::prepareViewMaps()`:
1. **Hiring Manager Resolution (`$hmMap`):**
   - Single batch query on `client_contact_info_jobs` joined with `client_contact_info` for all `$jobIds` on the page.
   - Graceful fallback for jobs without explicit `client_contact_info_jobs` entries: batch query on `jobs` joined with `client_contact_info` via `client_id`.
   - Stored in `$jobMetaMap[$jobId]['hm_name']` and `$jobMetaMap[$jobId]['hm_designation']`.
2. **Direct Client Shared-by Resolution (`$sharedByMap`):**
   - Single batch query on `recommendation_relation` joined with `companies` and `users` for all `$jobIds` where `to_company_id = $companyId`.
   - Batch lookup on `companies` for any direct `from_company_id` on the hit documents.
   - Stored in `$sharedByMap[$jobId]` and passed to the Blade view.
3. **Cache Version Invalidation:**
   - Incremented UI version payload key to `ui_v => 'card_v7'` to ensure instantaneous, cache-fresh rendering for all organization users.

### 3.2 View-Layer Dynamic Resolution (`interview-tracker-ajax.blade.php`)
- **Direct Client (`$isDirectClient === true`):**
  - Resolves `$sbUser` and `$sbComp`.
  - Fallbacks: `from_company_id` lookup in `$fromCompanyNames`, `$row->from_company_name`, `$row->post_company_name`, `$row->company_name`, `$row->uc_company_name`, `$ownerRecruiterName`.
  - Formats: `{$sbUser} · {$sbComp}` or `{$sbComp}`.
  - Rendered in `.it-attribution-cell.it-attr-direct` with icon `fa-share-nodes` (Emerald tone `#ecfdf5` / `#059669` / `#a7f3d0`).
- **Internal Client / My Organization (`!$isDirectClient`):**
  - Resolves `$hmName` and `$hmDesig` from `$jobMetaMap`.
  - Fallbacks: Lead panelist from `interviewers`, assigned SPOC from `$spokpersonMap`, or row attribution.
  - Formats: `{$hmName} ({$hmDesig})` or `{$hmName}`.
  - Rendered in `.it-attribution-cell.it-attr-hm` with icon `fa-user-tie` (Cyan tone `#ecfeff` / `#0891b2` / `#a5f3fc`).

---

## 4. File Changes Matrix

| File Path | Nature of Change |
| :--- | :--- |
| `app/Http/Controllers/ServiceCompany/InterviewScheduleController.php` | Added batch pre-fetching for `$hmMap`, `$sharedByMap`, `$fromCompanyNames` in `prepareViewMaps()`; bumped UI cache key to `card_v7`. |
| `resources/views/service_company/ajax_pages/interview-tracker-ajax.blade.php` | Implemented dynamic HM vs Shared by resolution and inserted `.it-attribution-cell` in `.it-card-bottom` (`.it-bottom-left`). |
| `resources/views/service_company/interview-schedule/table.blade.php` | Added enterprise CSS styles for `.it-attribution-cell`, `.it-attr-direct`, `.it-attr-hm`, `.it-attr-icon-wrap`, and `.it-attr-val`. |
| `tests/Feature/ServiceCompanyInterviewTrackerTest.php` | Added feature test cases for dynamic HM and Shared by attribution rendering, classes, and CSS token assertions. |

---

## 5. 15.6" Screen Fit (1366×768 & 1920×1080)
- `.it-card-bottom` utilizes `flex-wrap: wrap; gap: 10px; justify-content: space-between;`.
- `.it-attr-val` has `max-width: 170px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;`.
- Full name, designation, and company details are accessible on hover via `title="..."` attribute.
- Total left strip width is comfortably within 600px, leaving ample room on 1366px screens without horizontal scrollbars or wrapping overlap.

---

## 6. Verification Evidence
- Syntax lint: `php -l` passed with zero errors across all modified controllers and views.
- Style test: `./vendor/bin/pint --test` passed with zero issues (2/2 files clean).
- Automated tests: `php artisan test tests/Feature/ServiceCompanyInterviewTrackerTest.php` passed 7/7 tests (145 assertions).
