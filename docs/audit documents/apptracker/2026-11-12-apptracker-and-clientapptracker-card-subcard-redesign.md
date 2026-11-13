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
7. **Job Header Flat Light-Tinted Strip & Org Theme Parity (`toolbar.blade.php`, `apptracker/index`, `apptracker/clientapp`):** Refined `#kt_toolbar` into an opt-in full-bleed edge-to-edge (`padding: 0`, `border-radius: 0`, `--kt-toolbar-height: 62px`) light-tinted header strip (`aptk-hdr-*` / `caf-hdr-*`) with zero nested sub-cards and zero left/right outer gutters — merging directly into the viewport and sidebar boundaries like the original Metronic toolbar. Evenly distributed columns across full width (`justify-content: space-between`), `38px` (`36px` at `<=1440px`) `View Job` and `+ Add Candidate` buttons separated by a vertical hairline divider, balanced 2-line column rhythm, CSS responsive title truncation (`Str::limit(..., 55)`), org-themed light directional backgrounds (`hp-org-my` `#fffbeb`→`#ffffff`, `hp-org-internal` `#eff6ff`→`#ffffff`, `hp-org-direct` `#ecfdf5`→`#ffffff`), and `+ Add Candidate` solid button styling exact-matched to each org's active `Total Candidates` card (`#fde047`→`#eab308` + `#422006` text/icon for `hp-org-my`, `#1d4ed8`→`#1e3a8a` + `#ffffff` for `hp-org-internal`, `#047857`→`#065f46` + `#ffffff` for `hp-org-direct`). Legacy markup in `toolbar.blade.php` remains untouched for all non-opt-in views (Rule 17).
8. **Multi-Channel Extension Across Remaining AppTracker & ClientAppTracker Pages:**
   - Extended the approved full-bleed light-tinted header strip (`sections.service_company.toolbar` with `'apptrackerHeaderCard' => true`) and Rule 20 Modern Card + Sub-Card design system across all 5 remaining ATS channels:
     1. **Similar Candidates:** `similar_candidates.blade.php` + `similar_existing_ajax.blade.php` (`simf-*`) & `dsimilar_candidates.blade.php` + `dsimilar_existing_ajax.blade.php` (`dsf-*`).
     2. **Inhouse Bench Talent:** `internal_bench.blade.php` + `internal_bench_ajax.blade.php` (`ibf-*`) & `dinternal_bench.blade.php` + `dinternal_bench_ajax.blade.php` (`dib-*`).
     3. **Agency Bench Talent:** `public_bench.blade.php` + `public_bench_ajax.blade.php` (`abf-*`).
     4. **Bench Talent Requests:** `candidates_request.blade.php` + `candidates_request_ajax.blade.php` (`brf-*`).
     5. **Career Pages:** `career_apply.blade.php` + `career_apply_ajax.blade.php` (`wcf-*`).
   - Preserved 100% of channel-specific domain logic, filters, drawer triggers (`.show-application-detail`, `.client-show-application-detail`, `.bench_show_application_detail`, `.pbench_show_application_detail`, `.website-show-application-detail`), action buttons (`add_to_inreview`, `add_to_list`, `data-row-source_from_type`), and Rule 17 prefix isolation.
   - Cleaned duplicate assignments in `ServiceCompanyAppTrackerController`, resolved `$this->hpOrgClass` for all 7 channel actions, and bumped `$cachePayload['ui_v']` to `card_v7` across all channel AJAX endpoints.
9. **Direct Client Exclusive Light Background Green Palette (`apptracker/index`, `apptracker/clientapp`, `AppTrackerCardSubCardRedesignTest`):**
   - **Problem:** The previous dark green background (`#047857` → `#065f46`) on the Direct Client AppTracker pages (`hp-org-direct`) was too dark and visually heavy, creating poor contrast against dark text and overpowering the header layout.
   - **Solution:** Replaced the dark green background with an exclusive, light, enterprise-grade Mint Emerald Light Glow palette applied synchronously to **both** the **`+ Add Candidate`** header button and the active **`Total Candidates`** pipeline card:
     * **Background Gradient:** `linear-gradient(135deg, #a7f3d0 0%, #6ee7b7 100%)` (emerald-200 to emerald-300 light radiant tint).
     * **Border Color:** `#34d399` (emerald-400 crisp boundary).
     * **Typography & Icon Color:** `#064e3b` (emerald-950 rich forest text with WCAG AAA high contrast).
     * **Label Color:** `#065f46` (emerald-800).
     * **Button Hover State:** `linear-gradient(135deg, #6ee7b7 0%, #34d399 100%)`, border `#10b981`, text `#064e3b`.
     * **Shadow Glow:** `0 4px 14px rgba(16, 185, 129, 0.22)`.
   - **Parity & Consistency:** Applied identically across AppTracker (`index.blade.php`), ClientAppTracker (`clientapp.blade.php`), and automated test assertions (`AppTrackerCardSubCardRedesignTest.php`).


10. **Target Req Redesign, Restored Work Mode, Clean Compensation Glyphs & Multi-Channel Add Candidate Button:**
    - **1. Similar Candidates Compensation Subcard:**
      * Replaced legacy raw HTML string (`<i class="fa fa-inr" aria-hidden="true"></i>`) returned by `GeneralMethod::currencySymbole()` with clean Unicode currency symbols via `CurrencyService::resolvePlainSymbolFromLegacy()`.
      * Eliminated literal escaped HTML rendering (`<i class="fa fa-inr"...`) on screen.
      * Standardized salary formatting for expected and current CTC with empty-safe zero fallbacks.
    - **2. Restored Work Mode in Location Subcard:**
      * Restored the Work Mode row (`Remote`, `Hybrid`, `Work From Office`, `Work From Home`) between City/Country and Degree across all channel AJAX views (`similar_existing_ajax`, `dsimilar_existing_ajax`, `internal_bench_ajax`, `dinternal_bench_ajax`, `public_bench_ajax`, `candidates_request_ajax`, `career_apply_ajax`).
      * Integrated `AppTrackerCardPresenter::resolveWorkMode($row)` with icon mapping (`fas fa-laptop` for Remote/WFH, `fas fa-building` for Office/Hybrid).
      * Added batch `job_applications.wm` pre-fetching in `prepareSimilarCandidatesRowMaps()` for candidate rows missing `wm` in Elasticsearch, avoiding N+1 queries.
    - **3. Matched Job Subcard (Target Req Redesign per Rule 25):**
      * Strictly replaced bare/ambiguous `Internal` label with canonical `Internal Client` (Blue `#eff6ff` bg, `#1d4ed8` text, `#bfdbfe` border).
      * When candidate's matched job belongs to own company (`self_company === 1`), dynamically renders `My Organization` (Cyan `#ecfeff` bg, `#0891b2` text, `#a5f3fc` border).
      * When candidate's matched job belongs to a marketplace client, renders `Direct Client` (Emerald `#ecfdf5` bg, `#059669` text, `#a7f3d0` border).
      * Redesigned Subcard 4 matching user reference (`media_1794491413897_67f7cc29.png`):
        - Header: `TARGET REQ...` with dual right-aligned pills (Classification badge + `HIRING` status badge).
        - Body: 34×34px gradient icon box (`#4f46e5` to `#6366f1` with white code icon) on left; 3-line details on right:
          * Line 1: Job title (12.5px bold `#0f172a`).
          * Line 2: `#ID` (bold cyan `#0284c7`) • `Category` (slate `#64748b`).
          * Line 3: `Company` (medium text `#334155`) • briefcase icon + `Employment Type` (Full-Time / Contract).
    - **4. Add Candidate Button Header Uniformity:**
      * Ensured consistent position, height (`38px`, `36px` at `<=1440px`), padding, border-radius (`8px`), and org-matching colors across all channel headers (`similar_candidates`, `dsimilar_candidates`, `internal_bench`, `dinternal_bench`, `public_bench`, `candidates_request`, `career_apply`).
      * Wired `#addServiceCandidate` click handlers to package validation endpoint (`service_company.auth-custom-package.validatePackage`) matching `all_apptracker` and `clientapp`.
    - **5. Tenancy & Cache Invalidation:**
      * Bumped cache version across all 9 AppTracker controller endpoints to `card_v8` to immediately invalidate legacy HTML.
      * 13 feature tests in `AppTrackerCardSubCardRedesignTest` passing with 332 assertions.

11. **Similar Candidates Card Refinement & Subcard Alignments (Job Subcard, Resume Badge, 3-Row Standardization):**
    - **1. Matched Job Subcard Text Expansion (Subcard 4):**
      * **Removed 34×34px Icon Box:** Eliminated the left gradient code icon (`simf-req-icon-box` / `dsf-req-icon-box`) which compressed the body column and forced job titles, requisition numbers, categories, and companies into excessive ellipsis truncation (`Senior Java Devel...`, `#54`). Body details now utilize 100% of the subcard width with clean single/multi-line readability.
      * **Removed Redundant `HIRING` Tag in Header:** Eliminated the `HIRING` status pill from the Subcard 4 header which crowded out the left label, preventing `TARGET REQ` from clipping into `TJ`. The header now cleanly accommodates `TARGET REQ` on the left and the canonical organization classification tag (`MY ORGANIZATION` / `INTERNAL CLIENT` / `DIRECT CLIENT`) on the right.
    - **2. Stage & Action Subcard Resume Status Badge (Subcard 6 Header):**
      * Integrated CV Tracker parity indicator into the header of Subcard 6 across both `similar_existing_ajax` and `dsimilar_existing_ajax`:
        - **Resume Available:** `<span class="badge badge-light-success ..."><i class="fas fa-check-circle me-1 text-success"></i>Resume</span>` (Mint green background `#ecfdf5`, `#10b981` text).
        - **Resume Missing:** `<span class="badge badge-light-danger ..."><i class="fas fa-exclamation-circle me-1 text-danger"></i>No CV</span>` (Light red background `#fef2f2`, `#ef4444` text).
      * Pre-fetched `resume_map` in `prepareSimilarCandidatesRowMaps()` and included `resume` in `GRID_SOURCE_FIELDS` for zero-query execution.
    - **3. Strict 3-Row Vertical Alignment (Subcards 3 & 5):**
      * Standardized vertical rhythm, heights, and icon widths across **Subcard 3 (Location & Degree)** and **Subcard 5 (Ownership & Added)**:
        - **Row 1:** Primary identifier (City/Country vs Recruiter name) with fixed-width icon (`width: 13px; text-align: center;`), `min-height: 18px`, `font-size: 11.5px`, `font-weight: 600`.
        - **Row 2:** Secondary attribute (Work Mode vs Company name) with fixed-width icon (`width: 13px; text-align: center;`), `min-height: 18px`, `font-size: 11px`, `color: #475569`, `margin-top: 4px`.
        - **Row 3:** Tertiary attribute (Degree/Education vs Created Date) with fixed-width icon (`width: 13px; text-align: center;`), `min-height: 18px`, `font-size: 11px`, `color: #64748b`, `margin-top: 4px`.
      * Ensured pixel-level horizontal alignment across both subcards without staggered baselines or wrapping shifts.
    - **4. Bracketed Relevant Experience Formatting (Subcard 2):**
      * Replaced the bullet-separated relevant experience with standard bracket notation: `0.0 Y Total (Rel 4.0 Y)` instead of `0.0 Y Total • 4.0 Y Rel`.
      * Applied synchronously across `similar_existing_ajax`, `dsimilar_existing_ajax`, and `dinternal_bench_ajax`.
    - **5. Tenancy, Cache Invalidation & Verification:**
      * Bumped cache version across all 9 AppTracker controller endpoints to `card_v9`.
      * Updated feature tests in `AppTrackerCardSubCardRedesignTest.php` asserting `card_v9`, `TARGET REQ`, `No CV`/`Resume`, `(Rel `, and absence of `simf-req-icon-box` and `simf-status-pill`.
      * All 13 feature tests pass with 344 assertions.

12. **Footer Card Intelligence Strip LinkedIn Verified & Visa Status (Total Candidates in Job & CV Tracker):**
    - **Total Candidates in Job Parity (`all_apptracker.blade.php` & `clientappdata_ajax.blade.php`):**
      * Verified that both AppTracker and ClientAppTracker render the green `LinkedIn Verified` pill (`.aptk-linkedin-pill` / `.caf-linkedin-pill`) with target `_blank` whenever `linkedin` URL is present on the candidate profile.
      * Verified that Visa status (`.aptk-visa-pill` / `.caf-visa-pill`) renders conditionally as `(Country, Visa Type)` with passport icon when visa details are provided.
      * Standardized label text to `LinkedIn Verified` across all candidate views including `all_candidates.blade.php`.
      * Confirmed that `LinkedIn Verified` and `Visa Status` pills are strictly scoped to the footer intelligence strip of the **Total Candidates in Job** tab and CV Tracker, without polluting channel bench/similar candidate subcards.
      * Verified with 13 feature tests in `AppTrackerCardSubCardRedesignTest.php` with 344 assertions passing.

13. **Matched Job Subcard Department Resolution, Organization Tinting & Footer Strip Resume/No CV Alignment:**
    - **1. Matched Job / Target Req Subcard Width & No-Wrap Standard (Subcard 4):**
      * Expanded Subcard 4 in CSS grid `.simf-cand-grid` and `.dsf-cand-grid` from `1.4fr` to `1.75fr` (and `1.7fr` on responsive `1440px` screens) so that `MY ORGANIZATION` comfortably fits on a single line with `white-space: nowrap !important; flex-shrink: 0;`.
      * Rescaled companion subcard fractions to preserve exact 100% card width without horizontal overflow:
        `minmax(0, 1.85fr) minmax(0, 1.25fr) minmax(0, 1.1fr) minmax(0, 1.75fr) minmax(0, 1.1fr) minmax(0, 1.15fr);`
    - **2. Resilient Multi-Key Job & Department Extraction:**
      * Resolved issue where department name (`job_categories.name`) was missing for candidates whose `wise_id` did not match candidate ID directly.
      * Enhanced `prepareSimilarCandidatesRowMaps()` in `ServiceCompanyAppTrackerController.php` to index `$wiseJobInfoMap` by `wise_id`, `job_application_id`, `cand_{job_application_id}`, and direct `job_{jobId}` joining `job_categories`.
      * In `similar_existing_ajax.blade.php` and `dsimilar_existing_ajax.blade.php`, implemented multi-key resolution with fallback to `$rowMaps['job_category_map'][$matchedJobId]` ensuring department name is 100% reliably displayed in Row 2: `#{{ $matchedJobId }} • {{ $matchedCategory ?: 'General' }}`.
    - **3. Organization Background Tinting & Canonical Rule 25 Color Tokens:**
      * Applied subtle tinted background highlights and matching border tokens to Subcard 4 based on classification:
        - **My Organization (`isSelfCompany`):** Tint `#f0fdfa`, border `#a5f3fc`, badge `background: #ecfeff; color: #0891b2; border: 1px solid #a5f3fc;` with `fa-id-badge`. Class: `.simf-cand-subcard--my-org` / `.dsf-cand-subcard--my-org`.
        - **Internal Client (`!isDirectClient && !isSelfCompany`):** Tint `#eff6ff`, border `#bfdbfe`, badge `background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;` with `fa-building`. Class: `.simf-cand-subcard--internal` / `.dsf-cand-subcard--internal`.
        - **Direct Client (`isDirectClient`):** Tint `#ecfdf5`, border `#a7f3d0`, badge `background: #ecfdf5; color: #059669; border: 1px solid #a7f3d0;` with `fa-shield-alt`. Class: `.simf-cand-subcard--direct` / `.dsf-cand-subcard--direct`.
    - **4. Stage & Action Subcard Header De-cluttering & Footer Strip Resume Placement:**
      * Removed the `Resume` / `No CV` badge from Subcard 6 header completely, resolving the header text clipping (`STAGE & ACTIO...` -> clean `STAGE & ACTION`).
      * Moved `Resume` / `No CV` badge into the bottom intelligence strip (`.simf-bottom-right` / `.dsf-bottom-right` / `.aptk-bottom-right` / `.caf-bottom-right`).
      * Positioned `Resume` / `No CV` strictly **BEFORE** `Visa Status` and `LinkedIn Verified` on the same row (`[Resume / No CV] [Visa Status] [LinkedIn Verified]`).
      * Handled both array and stdClass candidate data structures safely without type errors.
    - **5. Tenancy, Cache Invalidation & Verification:**
      * Bumped cache version across all 9 AppTracker controller endpoints to `card_v10`.
      * Updated feature tests in `AppTrackerCardSubCardRedesignTest.php` asserting `card_v10`, department rendering, organization classification classes (`--my-org`, `--internal`, `--direct`), icons, and footer strip resume/no-cv positioning.
      * All 13 feature tests pass with 372 assertions.

14. **Direct Resume Pill Click-to-Drawer `#resume_cv` Tab Loading Across All Candidate Lists:**
    - **1. User Story & Core Requirement:**
      * When a recruiter clicks the green `Resume` pill located on the candidate listing card (in the bottom activity strip), the system must immediately open the candidate sliding drawer and automatically switch to and load the **`Resume / CV`** tab (`#resume_cv`) instead of the default `Profile Overview` tab.
      * Displays the resume viewer, keyword highlights, match analytics, and download controls without requiring a second manual click inside the drawer.
      * Regular candidate card, name, and avatar clicks continue to open the default `Profile Overview` (`#personal_details`) tab with zero behavioral mutation.
      * Missing resumes (`No CV` pill) are not clickable and do not switch tabs.
    - **2. Global Drawer Tab Lifecycle & Viewer Synchronization (`service_company_layout.blade.php`):**
      * Implemented `activateCandidateDrawerTab(targetTab)` helper function exported to `window.activateCandidateDrawerTab`:
        - Searches all 4 candidate drawer containers in the ATS:
          * `#application_show_right_bar` (AppTracker applications, career website applications, similar candidates)
          * `#client_application_show_right_bar` (Client AppTracker applications)
          * `#bench_application_show_right_bar` (In-house bench candidates)
          * `#public_bench_application_show_right_bar` (Agency bench candidates)
        - Targets tab triggers via standard selectors (`a[href="#resume_cv"]`, `button[data-target="resume_cv"]`, `[data-rcv-tab]`).
        - Uses Bootstrap `.tab('show')` combined with native `click` dispatch so Metronic tab switch handlers and OpenDoc / V2 Resume viewer initialization listeners (`rcvTabBtn`, `click.v2resume`) execute seamlessly.
        - Synchronizes `.active .tab-active` classes, manages `aria-selected="true"`, ensures `.tab-pane.show.active`, and fires `window.dispatchEvent(new Event('resize'))` so canvas/WASM resume viewers recalculate page layout dimensions immediately.
      * Connected tab activation hook into all candidate drawer AJAX loader callbacks:
        - `renderCandidateDrawerView`
        - `show-application-detail`
        - `website-show-application-detail`
        - `client-show-application-detail`
        - `bench_show_application_detail`
        - `pbench_show_application_detail`
      * Implemented unified click delegator for `.jw-open-resume-tab` and all channel resume pills (`.aptk-resume-pill`, `.caf-resume-pill`, `.simf-resume-pill`, `.dsf-resume-pill`, `.ibf-resume-pill`, `.dib-resume-pill`, `.abf-resume-pill`, `.brf-resume-pill`, `.wcf-resume-pill`):
        - Prevents event bubbling (`e.preventDefault()`, `e.stopPropagation()`).
        - Sets `window.__pendingCandidateDrawerTab = 'resume_cv'`.
        - Resolves candidate drawer trigger element from the card and triggers drawer open immediately.
    - **3. Universal Candidate Card Parity (All 10 Candidate Lists):**
      * **App Tracker Total Candidates:** `.aptk-resume-pill.jw-open-resume-tab` (`all_apptracker.blade.php`).
      * **Client App Tracker Total Candidates:** `.caf-resume-pill.jw-open-resume-tab` (`clientappdata_ajax.blade.php`).
      * **Similar Candidates:** `.simf-resume-pill.jw-open-resume-tab` (`similar_existing_ajax.blade.php`).
      * **Client Similar Candidates:** `.dsf-resume-pill.jw-open-resume-tab` (`dsimilar_existing_ajax.blade.php`).
      * **In-house Bench Talents:** `.ibf-resume-pill.jw-open-resume-tab` (`internal_bench_ajax.blade.php`).
      * **Client In-house Bench Talents:** `.dib-resume-pill.jw-open-resume-tab` (`dinternal_bench_ajax.blade.php`).
      * **Agency Bench Talents:** `.abf-resume-pill.jw-open-resume-tab` (`public_bench_ajax.blade.php`).
      * **Bench Talent Requests:** `.brf-resume-pill.jw-open-resume-tab` (`candidates_request_ajax.blade.php`).
      * **Career Website Applications:** `.wcf-resume-pill.jw-open-resume-tab` (`career_apply_ajax.blade.php`).
      * **CV Tracker:** `.simf-resume-pill.aptk-resume-pill.jw-open-resume-tab` (`all_candidates.blade.php`).
    - **4. Verification Evidence:**
      * Added `test_resume_pill_click_triggers_candidate_drawer_resume_tab_across_all_candidate_lists()` to `tests/Feature/AppTrackerCardSubCardRedesignTest.php`.
      * Verified `php -l` clean across all modified blades and layout.
      * `php artisan test tests/Feature/AppTrackerCardSubCardRedesignTest.php` — **14 passed (393 assertions)**.
      * `php artisan view:clear` completed cleanly.

15. **Job Details Label Standardization & Document Color Standard Subcard Backgrounds:**
    - **1. Subcard 4 Label Standardized to `Job Details`:**
      * Replaced legacy `TARGET REQ` / `TARGET REQUISITION` labels with **`Job Details`** (`JOB DETAILS`) across all candidate card streams:
        - Similar Candidates (`similar_existing_ajax.blade.php`)
        - Client Similar Candidates (`dsimilar_existing_ajax.blade.php`)
        - CV Tracker (`all_candidates.blade.php`)
        - Interview Tracker (`custom_client_candidates_interview_tracker.blade.php`)
        - Client Details Drawers (`client/dviewMore.blade.php`, `client/show.blade.php`, `consultancy/dviewMore.blade.php`)
    - **2. Document Color Standard (UI/UX Standard 6A) Subcard Backgrounds & Badges:**
      * Strictly aligned the Subcard 4 container background highlight colors, borders, and badge pills with `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` (Section 6 A):
        - **My Organization (`self_company == 1`):**
          * Subcard Background: Warm canary/amber tint **`#fffbeb`** (`!important`).
          * Subcard Border: Amber border **`#fde68a`** (`!important`), hover border `#fcd34d`.
          * Badge Pill: `background: #fef3c7; color: #92400e; border: 1px solid #fcd34d;` with `fa-id-badge`.
        - **Internal Client (`self_company == 0` / managed client):**
          * Subcard Background: Soft blue tint **`#eff6ff`** (`!important`).
          * Subcard Border: Blue border **`#bfdbfe`** (`!important`), hover border `#93c5fd`.
          * Badge Pill: `background: #dbeafe; color: #1e40af; border: 1px solid #93c5fd;` with `fa-building`.
        - **Direct Client (marketplace mandate):**
          * Subcard Background: Soft mint emerald tint **`#ecfdf5`** (`!important`).
          * Subcard Border: Emerald border **`#a7f3d0`** (`!important`), hover border `#6ee7b7`.
          * Badge Pill: `background: #d1fae5; color: #166534; border: 1px solid #6ee7b7;` with `fa-shield-alt`.
      * Applied both inline styles (`style="{{ $targetSubcardStyle }}"`) and CSS classes (`simf-cand-subcard--my-org`, `dsf-cand-subcard--my-org`, etc.) across `similar_existing_ajax.blade.php`, `dsimilar_existing_ajax.blade.php`, `all_candidates.blade.php`, `similar_candidates.blade.php`, `dsimilar_candidates.blade.php`, and `cvtracker/index.blade.php`.
    - **3. Tenancy, Cache Invalidation & Verification:**
      * Bumped cache version across all 9 AppTracker controller endpoints to `card_v11`.
      * Updated feature tests in `AppTrackerCardSubCardRedesignTest.php` asserting `Job Details`, `card_v11`, `#fffbeb`, and `#fde68a`.
      * All 14 feature tests pass with 393 assertions.


