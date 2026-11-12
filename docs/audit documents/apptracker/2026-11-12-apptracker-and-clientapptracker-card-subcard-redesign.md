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
5. **Primary Candidate Card + 6 Discrete Sub-Cards + Bottom Intelligence Strip:**
   - **Sub-Card 1 (`Candidate Info`):** Selection checkbox (`.chk_list_item`), `[Bench]` / `[Duplicate]` header badges, 38px avatar/photo, clickable name (`.show-application-detail`), designation, and full current company.
   - **Sub-Card 2 (`Compensation & Exp`):** Expected/Bill CTC (`tabular-nums`, gated by `salary_view` on AppTracker), Current CTC or `Fulltime`/`Contract`, Total + Relevant experience, and color-coded notice period (`Immediate` green, `Days` amber, `SNP LWD` purple).
   - **Sub-Card 3 (`Location & Edu`):** City + Country code and **UPPERCASE** qualification (`text-transform: uppercase`).
   - **Sub-Card 4 (`Interview`):** Scheduled date, time + timezone, and either recorded feedback badge or interview mode pill (or clean `Not Scheduled` state).
   - **Sub-Card 5 (`Ownership`):** Recruiter/Spokesperson and submitting company.
   - **Sub-Card 6 (`Pipeline & Stage`):** Stage badge, sub-stage label, and stage action trigger (`.stage_sub_stage` on AppTracker; `.client_stage_sub_stage` for `InReview` or `.client_stage_locked_notice` `"Client Managed"` on ClientAppTracker).
   - **Sub-Card 7 (Bottom Intelligence Strip):** Standardized `Match X%` badge (`.jw-row-match-badge`), horizontally scrollable required-skill-highlighted pills (`is-matched`), and `"View Profile"` drawer CTA.
6. **Controller Cache & N+1 Improvements:**
   - Added `'jobid' => $jobid` and `'ui_v' => 'card_v1'` to `$cachePayload` in `clientappdata_ajax()` (fixing cross-job cache collision and stale HTML caching) and `'ui_v' => 'card_v1'` to `allDataAjax()`.
   - Pre-fetched `$rowMaps` and `$citiesMap` in `clientappdata_ajax()` to eliminate per-row N+1 DB queries in `clientappdata_ajax.blade.php`.

---

## File Changes Matrix

| File | Change Summary |
|---|---|
| `resources/views/service_company/apptracker/index.blade.php` | Replaced `#sc_table_card` raw `<table>` with Rule 20 `.aptk-*` Card + Sub-Card stream, skeleton loader, `.aptk-stream-bar`, search clear/refresh controls, card-based `.exportRecord` print builder, `.aptk-cand-card` highlight/selection targets, and Client Tracker cyan (`#0891b2` / `#ecfeff`) Sourcing pipeline color. |
| `resources/views/service_company/ajax_pages/all_apptracker.blade.php` | Refactored `<tr>` rows into `.aptk-cand-card` with 6 discrete `.aptk-cand-subcard` cells, `.aptk-cand-bottom` intelligence strip, plain currency glyphs, trailing exp dot trim, and Client Tracker cyan (`#0891b2` / `#ecfeff` / `#0e7490` / `#a5f3fc`) Sourcing rail + badge. |
| `resources/views/service_company/apptracker/clientapp.blade.php` | Replaced raw `<table>` with Rule 20 `.caf-*` Card + Sub-Card stream, skeleton loader, `.caf-stream-bar`, debounced search + clear/refresh controls + XHR abort, card-based `.aptk_print_btn` print builder, active pagination page fix, and Client Tracker cyan (`#0891b2` / `#ecfeff`) Sourcing pipeline color. |
| `resources/views/service_company/ajax_pages/clientappdata_ajax.blade.php` | Refactored `<tr>` rows into `.caf-cand-card` with 6 discrete `.caf-cand-subcard` cells and `.caf-cand-bottom` strip; wired pre-fetched `$rowMaps` / `$citiesMap`, safe plain currency resolution, trailing exp dot trim, and Client Tracker cyan Sourcing rail + badge. |
| `app/Services/CurrencyService.php` | Added `resolvePlainSymbolFromLegacy()` to convert legacy `GeneralMethod::currencySymbole()` HTML icon markup (`<i class="fa fa-inr">`) into clean Unicode/text currency glyphs without unescaped Blade HTML. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` | Added `ui_v` (`card_v3`) cache busting in `allDataAjax()` and `clientappdata_ajax()`, added `jobid` to `clientappdata_ajax` cache payload, and batch-prefetched `$rowMaps` & `$citiesMap`. |
| `tests/Feature/AppTrackerCardSubCardRedesignTest.php` | Added Feature verification tests for both redesigned views, plain currency glyphs, trailing experience dot trimming, Client Tracker cyan Sourcing palette, permissions, stage triggers, empty states, and Rule 17 isolation. |

---

## 15.6" Screen Fit (`1366×768` & `1920×1080`)

- Upper grid uses `minmax(0, ...fr)` fractional columns (`2.15fr 1.35fr 1.15fr 1.35fr 1.15fr 1.3fr`) with `min-width: 0; overflow: hidden` on every sub-card so long names/companies/skills never force horizontal overflow.
- Reflows cleanly to 3 columns at `<= 1180px` and 1 column at `<= 640px`.

---

## Verification Evidence

- `php -l` passed with zero syntax errors across all modified controller, service, view, and test files.
- `php artisan test tests/Feature/AppTrackerCardSubCardRedesignTest.php` — **5 passed (65 assertions)**.
- `php artisan test tests/Feature/CandidateSubmitReadinessAndCacheTest.php --filter="apptracker"` — **2 passed (13 assertions)**.
- `php artisan test tests/Feature/BenchToJobAppTrackerPipelineContractTest.php --filter="phase_1|phase_4|phase_5"` — verified after final edits.
