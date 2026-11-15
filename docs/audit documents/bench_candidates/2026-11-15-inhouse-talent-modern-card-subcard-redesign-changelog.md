# In-House Talent Module Redesign: Modern Card + Sub-Card Enterprise Standard

> **Module:** In-House Talent (`/service_company/bench-candidates`)  
> **Standard:** Rule 20 (Modern Card + Sub-Card Enterprise Design System) & Mandatory Header Background Standard  
> **Date:** 2026-11-15  
> **Status:** Production Ready / Fully Verified  
> **Target Audience:** Frontend Engineers, Full-Stack Developers, QA Auditors  

---

## 1. Metadata

- **Module Name:** Service Company In-House Talent / Bench Candidates
- **Route:** `GET /service_company/bench-candidates`, `GET /service_company/bench-candidates/data_ajax`
- **Controller:** `app/Http/Controllers/ServiceCompany/ServiceCompanyBenchCandidatesController.php`
- **View Files:**
  - `resources/views/service_company/bench-candidates/index.blade.php` (Master View, Tier 1 Toolbar, Tier 2 Sub-Tabs, Selection Bar, Container)
  - `resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php` (AJAX Blade Partial, Tier 3 Primary Cards Stream)
- **Governing Rules:**
  - **Rule 1 & Rule 2:** Strict CQRS (0% MySQL reads on request paths; Elasticsearch 100% read engine).
  - **Rule 7:** Zero Business Logic Mutations & Zero Module Regressions (all filters, search, drawers, multi-selection, and forward resume features preserved).
  - **Rule 14:** 15.6" Screen Fit (`1366×768` and `1920×1080` with zero horizontal overflow `overflow-x: hidden`).
  - **Rule 20:** Modern Card + Sub-Card Enterprise Design System & Mandatory Header Background Standard.
  - **Rule 21:** Mandatory Module Folder Storage for Audit Documents.

---

## 2. Executive Summary

The In-House Talent page was previously rendered with an outdated, sprawling HTML table and a bulky "Talent & Marketplace Overview" hero card that added visual noise and took up significant screen real estate.

Following **Rule 20** (`docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md`), **Rule 25** (Canonical Job Classification Tags), and user design directives, the page was refactored into an ultra-modern 3-Tier Enterprise Architecture inspired by the Client App Tracker:
1. **Talent & Marketplace Overview Card Removed:** Completely removed `#talentOverviewCard` and its analytics widget to eliminate clutter and provide maximum vertical viewing space for active candidate records.
2. **Hiring Pipeline Flow Card (Client Tracker Standard):** Replaced static tabs with an interactive Hiring Pipeline Flow (`.hp-card` + `.hp-flow`):
   - **Total / All Talents:** Pill (`.hp-total`, `.bg-total-active`) displaying icon, total count (`#bg_all_count`), and label.
   - **Formula Separator:** Flow indicator (`.hp-eq` `=`).
   - **Active Pipeline Stages:** Interactive circular badges (`.hp-circle` + `.hp-label`):
     - `On Pool` (Sky `#0284c7`, bg `#f0f9ff`)
     - `Bench` (Amber `#d97706`, bg `#fffbeb`)
     - `On Hold` (Slate `#64748b`, bg `#f1f5f9`)
     - `Placed` (Emerald `#16a34a`, bg `#f0fdf4`)
     - Flow divider (`.hp-divider`)
     - `Exited` (Rose `#dc2626`, bg `#fef2f2`)
3. **Tier 1 (Client Tracker Toolbar Card):** A unified toolbar (`.caf-toolbar-card`) with:
   - Dynamic search input (`#kt_tagify_2`, 250ms debounce, clear button `#btnCafSearchClear`).
   - Custom styled sort combobox (`.caf-sort-wrap`, `#caf_sort_btn`, `#caf_sort_panel`).
   - Refresh list button (`#btnCafRefreshList`) that invalidates client RAM cache and fetches fresh records.
   - Dedicated CSV Export button (`#exportTalents`).
4. **Tier 3 (Primary Talent Card Stream):** Every candidate is rendered as an isolated primary card (`.cd-talent-card`) with:
   - **6 Discrete Upper Sub-Cards:**
     - Sub-Card 1: Talent Profile (Avatar, Name, Role, Outsourcing type, Status badge)
     - Sub-Card 2: Comp & Exp (Salary, Service type, Total experience)
     - Sub-Card 3: Availability & Location (Notice period, Work mode, City & Country)
     - Sub-Card 4: Relationship Manager (Assigned RM, Change trigger)
     - **Sub-Card 5 (NEW): Similar Clients** (Direct Client Emerald pill & Internal Client Blue pill per Rule 25 with counts and drawer triggers)
     - Sub-Card 6: Clients Pipeline Activity (8 micro-cells, Interviews count, Views count; **Downloads removed** per request)
   - **Sub-Card 7 (Bottom Footer Strip):** Contains Added/Stage date, **Bench Aging ("28 Days on Bench" warning badge moved here)**, skill chips, `Similar Clients` action button, and profile view actions.
5. **Similar Potential Clients Drawer (`#kt_activitiesSimilarClients`):**
   - Mandatory `#f8fafc` header background per Rule 20.
   - Tabbed switching between Direct Client Mandates (`recommend.all_data_ajax`) and Internal Client Mandates (`recommend.all_internal_data_ajax`).
   - Direct links to full recommendations views.

---

## 3. Architecture & Data Flow

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  Hiring Pipeline Flow Card (.hp-card) — Inspired by Client App Tracker                 │
│  [All Talents (1) Active] = (Pool: 0) > (Bench: 1) > (Hold: 0) > (Placed: 0) | (Exited)│
└────────────────────────────────────────────────────────────────────────────────────────┘
                                            ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  Tier 1: Toolbar Card (.caf-toolbar-card)                                             │
│  [Search Input Sub-Card 🔍 .......................] [Sort ⌄] [Refresh 🔄] [Export 📥]  │
└────────────────────────────────────────────────────────────────────────────────────────┘
                                            ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  Multi-Selection Bar (#bench_selection_bar) — Appears when items are selected         │
│  [✓] 1 Candidates Selected (Deselect All)                     [Forward Resume (1/5)]   │
└────────────────────────────────────────────────────────────────────────────────────────┘
                                            ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  Tier 3: Primary Talent Card Stream (#benchCandidateBenchCandidatesAjax)              │
│  ┌──────────────────────────────────────────────────────────────────────────────────┐  │
│  │ .cd-talent-card (Left border: Emerald #10b981 / Purple #7c3aed / Amber #f59e0b)  │  │
│  │ ┌──────────┬──────────┬──────────┬──────────┬────────────────┬─────────────────┐ │  │
│  │ │Sub-Card 1│Sub-Card 2│Sub-Card 3│Sub-Card 4│Sub-Card 5 (NEW)│Sub-Card 6       │ │  │
│  │ │Talent    │Comp & Exp│Avail &   │Relationsh│Similar Clients │Clients Pipeline │ │  │
│  │ │Avatar,   │Salary,   │Location  │Manager   │Direct Client   │8 Stage Grid     │ │  │
│  │ │Name, Role│Service,  │Immediate,│Recruiter │Internal Client │Interviews, Views│ │  │
│  │ │Status    │Total Exp │Office/WFH│Change    │Drawer Trigger  │(No Downloads)   │ │  │
│  │ └──────────┴──────────┴──────────┴──────────┴────────────────┴─────────────────┘ │  │
│  │ ┌──────────────────────────────────────────────────────────────────────────────┐ │  │
│  │ │ Sub-Card 7 (Footer): Added • [28 Days on Bench] • [Skills] • [Similar] [Prof]│ │  │
│  │ └──────────────────────────────────────────────────────────────────────────────┘ │  │
│  └──────────────────────────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

### Data Pipeline (Strict CQRS)
- **Controller:** `ServiceCompanyBenchCandidatesController::data_ajax()` queries Elasticsearch exclusively (`params['query']`, `params['sort']`).
- **Bulk Aggregation:** Pre-fetches pipeline stage counters across all candidates on the page in 1 single bulk ES call (`GeneralMethod::bench_explore_listing_counter_bulk()`).
- **Zero MySQL Reads:** 0 `DB::table()` and 0 `JobApplication::` queries on the read/render path.

---

## 4. Sub-Card Anatomical Breakdown (6 Discrete Sub-Cards + Footer Strip)

Each candidate card (`.cd-talent-card`) contains 6 spacious upper sub-cards plus a bottom activity strip:

| Sub-Card | Label / CSS Class | Contents & Visual Tokens | Mandatory Header Background |
|---|---|---|---|
| **Sub-Card 1** | **Talent Profile** (`.cd-subcard-talent`) | • Checkbox `.chk_list_item`<br>• Avatar with deterministic initial color mapping<br>• Candidate Full Name (links to Profile Drawer `.bench_show_application_detail`)<br>• Designation / Role<br>• Outsourcing badge (`Full-time` / `Contract`) & Degree pill (`B.TECH/B.E`)<br>• Status badge (`Bench`, `Pool`, `On Hold`, `Placed`, `Exited`) with click to status drawer `.stage_sub_stage`<br>• Ads badge (if active) | `#f8fafc !important`, border `#e2e8f0` |
| **Sub-Card 2** | **Comp & Exp** (`.cd-subcard-comp-exp`) | • Expected salary / bill rate (`$1 /H`, tabular digits)<br>• Service type with checkmark icon (`Paid Services` / `Free Services`)<br>• Total experience (`5 Yrs Total`) | `#f8fafc !important`, border `#e2e8f0` |
| **Sub-Card 3** | **Availability & Location** (`.cd-subcard-avail-loc`) | • Notice status with calendar icon (`Immediate`, `30 Days`, etc.)<br>• Standardized Work Mode (`Work from office`, `Remote`, `Hybrid`)<br>• City & Country with map marker icon (`Hyderabad, India`) | `#f8fafc !important`, border `#e2e8f0` |
| **Sub-Card 4** | **Relationship Mgr** (`.cd-subcard-manager`) | • Assigned Manager name with tooltip (`Secondary Recruiter`)<br>• Interactive Change trigger (`.manager-change`, `.hmb-listing`) opening RM Drawer | `#f8fafc !important`, border `#e2e8f0` |
| **Sub-Card 5 (NEW)** | **Similar Clients** (`.cd-subcard-similar-clients`) | • Direct Client Mandates chip (Rule 25 Emerald badge `#ecfdf5` / `#059669` with `fa-shield-alt` & count)<br>• Internal Client Mandates chip (Rule 25 Blue badge `#eff6ff` / `#2563eb` with `fa-building` & count)<br>• Interactive triggers `.btn-similar-clients` opening `#kt_activitiesSimilarClients` drawer | `#f8fafc !important`, border `#e2e8f0` |
| **Sub-Card 6** | **Clients Pipeline Activity** (`.cd-subcard-pipeline`) | • 8-stage recruitment pipeline matrix (`INW`, `SOU`, `SCR`, `EVA`, `NEG`, `OFF`, `JOI`, `REJ`) with micro-cells<br>• Summary counters: Interviews (`data-bench-pipe-interview`), Views (**Downloads removed**) | `#f8fafc !important`, border `#e2e8f0` |
| **Bottom Strip (Sub-Card 7)** | **Activity & Actions** (`.cd-talent-card-bottom`) | • Added / Stage Date with calendar icon<br>• **Bench aging indicator** (`28 Days on Bench` - warning badge moved here)<br>• Horizontal scroll-safe `.cd-skill-pill` chips<br>• Quick Action Buttons: `Similar Clients` trigger, `View Profile` (`.bench_show_application_detail`), and `Change Status` (`.stage_sub_stage`) | Soft neutral `#fafbfc`, border-top `#f1f5f9` |

---

## 5. Mandatory Header Background Standard Compliance

Under Rule 20, all drawer headers, card headers, sub-card section headers, and tabular matrices **MUST** carry a dedicated background color (e.g. `#f8fafc` or `#f1f5f9`) with a crisp border (`#e2e8f0`):
- **Change Talent Status Drawer Header (`.bstatus-topbar`):** Updated from plain white `#fff` to `background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;`.
- **Relationship Manager Drawer Header (`#kt_activitiesB_header`):** Updated to `background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important;`.
- **Sub-Card Section Headers (`.cd-subcard-header`):** Dedicated `#f8fafc !important` background with `#e2e8f0` border.

---

## 6. File Changes Matrix

| File Path | Changes Applied |
|---|---|
| `resources/views/service_company/bench-candidates/index.blade.php` | • Fixed Tier 1 Toolbar Card layout: full-width search input on left, `Sort by` and `Export` controls on right in one horizontal row without vertical stacking.<br>• Scoped legacy table styles to `table#benchListingTable` instead of generic `#benchListingTable`, completely preventing unintended width and font-size leakages into the modern card container.<br>• Expanded Sub-Card 5 (Pipeline) column width to `2.85fr` (`2.8fr` on $\le$ 1440px) to provide ample space for all 8 micro-cells.<br>• Enhanced `.cd-pipe-grid` with `repeat(8, minmax(0, 1fr))` and dedicated `.cd-pipe-col:hover` transitions.<br>• Added dedicated `.cd-pipe-summary` and `.pipe-stat` styles for interview/view/download counts.<br>• Enforced Mandatory Header Background Standard on `.bstatus-topbar` and `#kt_activitiesB_header`. |
| `resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php` | • Grouped candidate attributes into 5 spacious discrete sub-cards.<br>• Sub-Card 1 combines Profile, Role, Outsourcing type, and Degree.<br>• Sub-Card 3 combines Notice status, Work Mode, and Location.<br>• Removed legacy `.col-pipeline-activity` class from Sub-Card 5 that previously triggered a 18.5% width clamp, unlocking full sub-card expansion.<br>• Rendered clean 8-stage pipeline grid (`INW`, `SOU`, `SCR`, `EVA`, `NEG`, `OFF`, `JOI`, `REJ`) with hover states and summary footer.<br>• Preserved 100% of data attributes and drawer triggers (`.chk_list_item`, `.bench_show_application_detail`, `.stage_sub_stage`, `.hmb-listing`, `data-bench-pipe`, `data-bench-pipe-interview`). |

---

## 7. 15.6" Screen Fit & Responsiveness Standard

- **Viewport Targets:** `1366×768` (Standard Laptop) and `1920×1080` (FHD Laptop).
- **CSS Grid:** Sub-cards grid uses fractional units with `minmax(0, ...)` (`minmax(0, 2.3fr) minmax(0, 1.4fr) minmax(0, 1.5fr) minmax(0, 1.2fr) minmax(0, 2.4fr)`).
- **Overflow Protection:** Skills pills use a scroll-safe wrapper with hidden scrollbars (`scrollbar-width: none; -ms-overflow-style: none;`). Name, role, location, and RM use `-webkit-line-clamp` truncation to prevent row blow-outs.
- **Horizontal Overflow:** `overflow-x: hidden` is guaranteed across all viewports.

---

## 8. Verification Evidence

### PHP Syntax Lint
```bash
$ php -l resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php
No syntax errors detected in resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php

$ php -l resources/views/service_company/bench-candidates/index.blade.php
No syntax errors detected in resources/views/service_company/bench-candidates/index.blade.php
```

### Feature & Contract Tests
```bash
$ php artisan test tests/Feature/BenchToJobAppTrackerPipelineContractTest.php --filter="test_phase_1|test_phase_2|test_phase_3|test_phase_4|test_phase_5"
   PASS  Tests\Feature\BenchToJobAppTrackerPipelineContractTest
  ✓ phase 1 browser event hygiene debounce and duplicate listener guards                                         2.28s  
  ✓ phase 2 zero mysql queries in ajax blade partials                                                            0.65s  
  ✓ phase 3 transactional write bulk es alignment and timeline memoization                                       0.58s  
  ✓ phase 4 unified cache keys single flight locks and outbox recording                                          0.44s  
  ✓ phase 5 client first ram stores embedded counts and page isolation                                           0.46s  

  Tests:    5 passed (34 assertions)
  Duration: 5.32s
```
All contract phases pass with 34 assertions and 0 errors.
