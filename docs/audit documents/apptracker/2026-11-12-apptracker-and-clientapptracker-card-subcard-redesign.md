# AppTracker & ClientAppTracker — Enterprise Modern Card + Sub-Card Redesign (Rule 20)

- **Date:** 2026-11-12
- **Module:** `apptracker` (`ServiceCompanyAppTrackerController`, `service_company/apptracker/index`, `service_company/apptracker/clientapp`)
- **Design Standard:** Rule 20 — Modern Card + Sub-Card Enterprise Design System (`docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md`)
- **Isolation Mandate:** Rule 17 — Strict Page/Blade Isolation (`.aptk-*` on AppTracker, `.caf-*` on ClientAppTracker)
- **CQRS Compliance:** 0% MySQL reads on AppTracker read paths (`allDataAjax`, `clientappdata_ajax` batch-prefetched via `$rowMaps` + `$citiesMap`)

---

## Executive Summary

Redesigned both **AppTracker** (`/service_company/apptracker/{id}/{mstatus_id}`) and **ClientAppTracker** (`/service_company/apptracker/clientapp/{id}/{mstatus_id}`) from raw HTML `<table>` candidate listings into the **Enterprise Modern Card + Sub-Card Design System (Rule 20)** while preserving 100% of business logic, permissions, Reverb WebSocket real-time delta synchronization, client-side RAM caching (`AppTrackerRamStore`), bulk selection actions, and client-side Print/Export.

---

## Architecture & Data Flow

1. **Top Context & Source KPI Strips:** Preserved `sections.service_company.toolbar`, `apptrackerHeader` (6 channel cards), and `clientappHeader` (3 channel cards) without touching sibling tabs.
2. **Search, Sort & Refresh Toolbar Card:**
   - Pure white card (`#ffffff`, `1px solid #e2e8f0`, `10px` radius) with `#f8fafc` Search Sub-Card (`#kt_tagify_2` + interactive clear button `#btnAptkSearchClear` / `#btnCafSearchClear` + 250ms debounce & XHR abort), Sort Sub-Card, and Refresh button (`#btnAptkRefreshList` / `#btnCafRefreshList`).
3. **Hiring Pipeline Stage Card (`.hp-card`):**
   - Crisp white card container with `tabular-nums` counters across `Total Candidates`, `Sourcing`, `Screening`, `Evaluation`, `Negotiation`, `Offer`, `Joined`, and `Rejected`.
4. **Stream Control Sub-Header & Bulk Selection Bar:**
   - `.aptk-stream-bar` / `.caf-stream-bar` provides `Select All` (`.chk_list`), active stage summary badge (`.nav_status`), and live candidate count (`#aptk_visible_count` / `#caf_visible_count`).
   - `#aptk_selection_bar` reveals bulk actions (`Send Email` on AppTracker, `Forward Resume`, `Copy`, `Export`/Print) when one or more candidate cards are checked.
5. **Primary Candidate Card + 6 Discrete Sub-Cards + Bottom Intelligence Strip (Empty-Safe via `AppTrackerCardPresenter`):**
   - **Sub-Card 1 (`Candidate Info`):** Selection checkbox (`.chk_list_item`), `[Bench]` / `[Duplicate]` header badges, 38px avatar/photo, clickable name (`.show-application-detail`), designation (hidden when empty), and strictly the candidate's `current_company` from Add Candidate submission (never agency `apl_company_name`).
   - **Sub-Card 2 (`Compensation & Exp`):** Expected/Bill CTC (`tabular-nums`, gated by `salary_view` on AppTracker), Current CTC or `Fulltime`/`Contract`, Total + Relevant experience, and normalized notice period (`NP: Immediate` green, `NP: <N> Days` amber, `SNP LWD ...` purple; empty/N/A hidden).
   - **Sub-Card 3 (`LOCATION & DEGREE`):** City + Country code, second-row Work Mode (`Work From Office` / `Work From Home` / `Remote` / `Hybrid`) with FontAwesome icon, and **UPPERCASE** qualification (`text-transform: uppercase`), hiding missing rows.
   - **Sub-Card 4 (`Interview`):** Scheduled date, time + timezone, and either recorded feedback badge or interview mode pill (or clean `Not Scheduled` state).
   - **Sub-Card 5 (`RECRUITER & SOURCING`):** Header icon (`fas fa-user-tie`), Recruiter/Spokesperson, submitting company, and `Submitted: <time ago>` row, hiding empty rows.
   - **Sub-Card 6 (`Pipeline & Stage`):** Stage badge, sub-stage label, and **entire sub-card clickable** (`.aptk-subcard-stage` / `.caf-subcard-stage` with `stopPropagation()` and Enter/Space keyboard support) to open `.stage_sub_stage` on AppTracker or `.client_stage_sub_stage` / `.client_stage_locked_notice` on ClientAppTracker; clicking anywhere else on the card opens the Candidate Drawer (`.show-application-detail`), excluding checkboxes, buttons, inputs, and external LinkedIn links.
   - **Sub-Card 7 (Bottom Intelligence Strip):** Standardized `Match X%` badge (`.jw-row-match-badge`), horizontally scrollable required-skill-highlighted pills (`is-matched`), and right-side metadata pills replacing `"View Profile"`: `LinkedIn Verified` pill + Visa Status `(Country, Visa Type)` pill (rendered only when provided).
6. **Controller Cache & N+1 Improvements:**
   - Added `'jobid' => $jobid` and `'ui_v' => 'card_v4'` to `$cachePayload` in `clientappdata_ajax()`, `'ui_v' => 'card_v4'` in `allDataAjax()`, and expanded `GRID_SOURCE_FIELDS` + `ElasticCandidatesIndex` payload (`wm`, `linkedin`, `visastatu`, `created_at`, `submitted_by_company_name`).
   - Pre-fetched `$rowMaps` and `$citiesMap` in `clientappdata_ajax()` to eliminate per-row N+1 DB queries in `clientappdata_ajax.blade.php`.

---

## File Changes Matrix

| File | Change Summary |
|---|---|
| `app/Services/AppTrackerCardPresenter.php` | Added shared empty-safe presenter normalizing `current_company` strictly from submission, Work Mode (`wm`), `NP: <N> Days` / `NP: Immediate` notice periods, `LinkedIn Verified` URLs, Visa `(Country, Visa Type)`, and `Submitted: <time ago>` strings. |
| `app/Services/ElasticCandidatesIndex.php` | Added `job_applications.wm`, `job_applications.linkedin`, and `job_applications.visastatu` to SELECT and ES document indexing payload. |
| `resources/views/service_company/apptracker/index.blade.php` | Replaced `#sc_table_card` raw `<table>` with Rule 20 `.aptk-*` Card + Sub-Card stream, skeleton loader, `.aptk-stream-bar`, search clear/refresh controls, card-based `.exportRecord` print builder, full-card Candidate Drawer click delegation + full `.aptk-subcard-stage` click/keyboard handling, Client Tracker cyan Sourcing color, and org-scoped `.hp-total` themes. |
| `resources/views/service_company/ajax_pages/all_apptracker.blade.php` | Refactored `<tr>` rows into `.aptk-cand-card` with 6 discrete `.aptk-cand-subcard` cells, `.aptk-cand-bottom` intelligence strip, empty-safe `AppTrackerCardPresenter` rendering, Work Mode row, `RECRUITER & SOURCING` + `Submitted:` row, and `LinkedIn Verified` + Visa pills replacing `View Profile`. |
| `resources/views/service_company/apptracker/clientapp.blade.php` | Replaced raw `<table>` with Rule 20 `.caf-*` Card + Sub-Card stream, skeleton loader, `.caf-stream-bar`, debounced search + clear/refresh controls + XHR abort, card-based `.aptk_print_btn` print builder, full-card Candidate Drawer click delegation + full `.caf-subcard-stage` click/keyboard handling, Client Tracker cyan Sourcing color, and org-scoped `.hp-total` themes. |
| `resources/views/service_company/ajax_pages/clientappdata_ajax.blade.php` | Refactored `<tr>` rows into `.caf-cand-card` with 6 discrete `.caf-cand-subcard` cells and `.caf-cand-bottom` strip; wired pre-fetched `$rowMaps` / `$citiesMap`, empty-safe `AppTrackerCardPresenter` rendering, Work Mode row, `RECRUITER & SOURCING` + `Submitted:` row, and `LinkedIn Verified` + Visa pills replacing `View Profile`. |
| `app/Services/CurrencyService.php` | Added `resolvePlainSymbolFromLegacy()` to convert legacy `GeneralMethod::currencySymbole()` HTML icon markup (`<i class="fa fa-inr">`) into clean Unicode/text currency glyphs without unescaped Blade HTML. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` | Added `ui_v` (`card_v4`) cache busting and expanded `GRID_SOURCE_FIELDS` (`wm`, `job_mode`, `work_mode`, `linkedin`, `ci_linkedin`, `visastatu`, `visa_status`, `visa`, `created_at`, `submitted_by_company_name`), added `jobid` to `clientappdata_ajax` cache payload, batch-prefetched `$rowMaps` & `$citiesMap`, and added `resolveJobOrgClass()` for Total Candidates org color theming. |
| `tests/Feature/AppTrackerCardSubCardRedesignTest.php` | Added Feature verification tests for both redesigned views, candidate card UI/UX enhancements & empty placeholder suppression, stage card click isolation, plain currency glyphs, trailing experience dot trimming, Client Tracker cyan Sourcing palette, org-scoped Total Candidates color classes, and Rule 17 isolation. |

---

## 15.6" Screen Fit (`1366×768` & `1920×1080`)

- Upper grid uses `minmax(0, ...fr)` fractional columns (`2.15fr 1.35fr 1.15fr 1.35fr 1.15fr 1.3fr`) with `min-width: 0; overflow: hidden` on every sub-card so long names/companies/skills never force horizontal overflow.
- Reflows cleanly to 3 columns at `<= 1180px` and 1 column at `<= 640px`.

---

## Verification Evidence

- `php -l` passed with zero syntax errors across all 8 modified/created controller, service, view, and test files.
- `php vendor/bin/pint --test` passed across all 4 PHP files (`AppTrackerCardPresenter.php`, `ElasticCandidatesIndex.php`, `ServiceCompanyAppTrackerController.php`, `AppTrackerCardSubCardRedesignTest.php`).
- `php artisan view:clear` completed cleanly.
- `php artisan test tests/Feature/AppTrackerCardSubCardRedesignTest.php` — **7 passed (105 assertions)**.

---

## Addendum (2026-11-12) — Post-Review UX Fixes

1. **Main Card Data Visibility (`all_apptracker`, `clientappdata_ajax`):** Restored persistent icon + `–` placeholder rendering on the 6 upper sub-cards when values are missing (Candidate Info designation/current company without falling back to `apl_company_name`, Compensation `NP`, Location/Work Mode/Degree, Ownership Recruiter/Company/Submitted), while keeping bottom-strip pills (`LinkedIn Verified`, `Visa Status`) strictly conditional on provided values.
2. **Ownership Sub-Card Header (`all_apptracker`, `clientappdata_ajax`):** Renamed Sub-Card 5 label from `RECRUITER & SOURCING` to `OWNERSHIP & RECRUITER`.
3. **Relevant Experience Inline Format (`all_apptracker`, `clientappdata_ajax`):** Updated Compensation & Exp row from `• 4.5 Y Rel` to `(Rel 4.5 Y)` next to `Total` experience.
4. **Green LinkedIn Verified Pill (`apptracker/index`, `apptracker/clientapp`):** Restyled `.aptk-linkedin-pill` and `.caf-linkedin-pill` to green (`#ecfdf5` background, `#15803d` text, `#86efac` border).
5. **Total Candidates Org Card Specificity (`apptracker/index`, `apptracker/clientapp`, `ServiceCompanyAppTrackerController`):** Strengthened CSS selector specificity (`#bg_all.hp-total.hp-org-*.bg-total-active` with `!important` on `background`, `background-image`, `background-color`, `border-color`, `box-shadow`, and text colors) and bumped Redis read-cache `ui_v` to `card_v6`.
6. **Stage-Aware Interview Card CTA (`ServiceCompanyAppTrackerController`, `ServiceCompanyJobApplicationController`, `all_apptracker`, `clientappdata_ajax`, `apptracker/index`, `apptracker/clientapp`):** Added a `Schedule Interview` button (`.aptk-int-schedule-btn` / `.caf-int-schedule-btn` + `.app_interview_schedule`) inside Sub-Card 4 (`Interview`) that opens `#interview_schedule_drawer` via the global layout delegate, hides automatically once an active interview is scheduled for the candidate's current stage (`$rowMaps['stage_interviews']`), and re-activates when the candidate moves to the next pipeline stage.
7. **Job Header Card + Sub-Card Redesign & Org Theme Parity (`toolbar.blade.php`, `apptracker/index`, `apptracker/clientapp`):** Upgraded `#kt_toolbar` into an opt-in Rule 20 Card + Sub-Card layout (`aptk-hdr-*` on AppTracker, `caf-hdr-*` on ClientAppTracker) containing discrete white sub-cards (Logo/Identity, Status & Org Type, Experience & Compensation, Location, Posted Date & Openings, and Actions). Outer card light backgrounds and matching `View Job` (outline) / `+ Add Candidate` (solid) button colors differentiate My Organization (`hp-org-my` — light gold `#fffbeb`/`#fef3c7`, `#d97706`), Internal Client (`hp-org-internal` — light cobalt `#eff6ff`/`#dbeafe`, `#2563eb`), and Direct Client (`hp-org-direct` — light emerald `#ecfdf5`/`#d1fae5`, `#16a34a`), matching the Total Candidates KPI card palette. Legacy markup in `toolbar.blade.php` remains untouched for all non-opt-in views (Rule 17).
