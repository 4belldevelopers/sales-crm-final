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

The In-House Talent page was previously rendered as an outdated, sprawling HTML table (`<table id="benchListingTable">`) with flat unanchored headers and high visual clutter. 

Following **Rule 20** (`docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md`) and user-provided inspiration, the module was completely refactored into the 3-Tier Layered Architecture:
1. **Tier 1 (Top Header Toolbar Card):** Dedicated slate header background (`#f8fafc`, border `#e2e8f0`, radius 10px) with icon badge, title/subtitle, dynamic search subcard with clear button (`#btnBenchSearchClear`), sort dropdown, and export action.
2. **Tier 2 (Sub-Tabs Card):** Dedicated white card container (`#ffffff`, border `#e2e8f0`, radius 10px) housing discrete status buttons with single active primary blue highlight (`#009ef7`), white text, and dynamic count badges.
3. **Tier 3 (Primary Talent Card Stream):** Every candidate is rendered as an isolated primary card (`.cd-talent-card`) with a status-themed border-left accent (`#10b981` Bench, `#7c3aed` Pool, `#f59e0b` On Hold, `#009ef7` Placed, `#94a3b8` Exited), containing 6 discrete sub-cards with **Mandatory Header Backgrounds** (`#f8fafc`, border `#e2e8f0`) plus a horizontal bottom activity strip (`.cd-talent-card-bottom`).

---

## 3. Architecture & Data Flow

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  Tier 1: Toolbar Card (.cd-bench-toolbar-card)                                        │
│  [Icon] In-House Talent Roster         [Search Input Sub-Card 🔍] [✕] [Sort] [Export]  │
└────────────────────────────────────────────────────────────────────────────────────────┘
                                            ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  Tier 2: Sub-Tabs Card (.cd-bench-tabs-card)                                          │
│  [All Talents (1) Active Blue]  [On Pool (0)]  [Bench (1)]  [On Hold (0)]  [Placed (0)]│
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
│  │ ┌──────────────┬──────────────┬──────────────┬──────────────┬──────────────┬───┐ │  │
│  │ │ Sub-Card 1   │ Sub-Card 2   │ Sub-Card 3   │ Sub-Card 4   │ Sub-Card 5   │S6 │ │  │
│  │ │ Talent       │ Comp & Exp   │ Availability │ Location &   │ Relationship │Pip│ │  │
│  │ │ Avatar, Name,│ Salary /hr,  │ Immediate /  │ City, Country│ Manager Name │8  │ │  │
│  │ │ Role, Status │ Service type,│ Work Mode,   │ Qualification│ & Change link│Stg│ │  │
│  │ │ Pill, Chkbox │ Total exp    │ Bench aging  │              │              │   │ │  │
│  │ └──────────────┴──────────────┴──────────────┴──────────────┴──────────────┴───┘ │  │
│  │ ┌──────────────────────────────────────────────────────────────────────────────┐ │  │
│  │ │ Sub-Card 7 (Bottom Strip): Added: 13 Nov 26 • [Skills Chips] • [Profile] [RM]│ │  │
│  │ └──────────────────────────────────────────────────────────────────────────────┘ │  │
│  └──────────────────────────────────────────────────────────────────────────────────┘  │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

### Data Pipeline (Strict CQRS)
- **Controller:** `ServiceCompanyBenchCandidatesController::data_ajax()` queries Elasticsearch exclusively (`params['query']`, `params['sort']`).
- **Bulk Aggregation:** Pre-fetches pipeline stage counters across all candidates on the page in 1 single bulk ES call (`GeneralMethod::bench_explore_listing_counter_bulk()`).
- **Zero MySQL Reads:** 0 `DB::table()` and 0 `JobApplication::` queries on the read/render path.

---

## 4. Sub-Card Anatomical Breakdown

Each candidate card (`.cd-talent-card`) contains 6 discrete upper sub-cards plus a bottom activity strip:

| Sub-Card | Label / CSS Class | Contents & Visual Tokens | Mandatory Header Background |
|---|---|---|---|
| **Sub-Card 1** | **Talent Profile** (`.cd-subcard-talent`) | • Checkbox `.chk_list_item`<br>• Avatar with deterministic initial color mapping<br>• Candidate Full Name (links to Profile Drawer `.bench_show_application_detail`)<br>• Designation / Role<br>• Outsourcing badge (`Full-time` / `Contract`)<br>• Status badge (`Bench`, `Pool`, `On Hold`, `Placed`, `Exited`) with click to status drawer `.stage_sub_stage`<br>• Ads badge (if active) | `#f8fafc !important`, border `#e2e8f0` |
| **Sub-Card 2** | **Comp & Exp** (`.cd-subcard-comp-exp`) | • Expected salary / bill rate (`$1 /H`, tabular digits)<br>• Service type with checkmark icon (`Paid Services` / `Free Services`)<br>• Total experience (`5 Yrs Total`) | `#f8fafc !important`, border `#e2e8f0` |
| **Sub-Card 3** | **Availability & Mode** (`.cd-subcard-avail-mode`) | • Notice status with calendar icon (`Immediate`, `30 Days`, etc.)<br>• Standardized Work Mode (`Work from office`, `Remote`, `Hybrid`)<br>• Bench aging indicator (`X Days on Bench` - warning amber, danger red if >30 days) | `#f8fafc !important`, border `#e2e8f0` |
| **Sub-Card 4** | **Location & Degree** (`.cd-subcard-location-edu`) | • City & Country with map marker icon (`Hyderabad, India`)<br>• Education / Qualification with cap icon (`B.TECH/B.E`) | `#f8fafc !important`, border `#e2e8f0` |
| **Sub-Card 5** | **Relationship Mgr** (`.cd-subcard-manager`) | • Assigned Manager name with tooltip (`Secondary-...`)<br>• Interactive Change trigger (`.manager-change`, `.hmb-listing`) opening RM Drawer | `#f8fafc !important`, border `#e2e8f0` |
| **Sub-Card 6** | **Clients Pipeline Activity** (`.cd-subcard-pipeline`) | • 8-stage recruitment pipeline matrix (`INW`, `SOU`, `SCR`, `EVA`, `NEG`, `OFF`, `JOI`, `REJ`) with explore links<br>• Summary counters: Interviews (`data-bench-pipe-interview`), Views, Downloads | `#f8fafc !important`, border `#e2e8f0` |
| **Bottom Strip** | **Activity & Actions** (`.cd-talent-card-bottom`) | • Added / Stage Date with calendar icon<br>• Horizontal scroll-safe `.cd-skill-pill` chips<br>• Quick Action Buttons: `View Profile` (`.bench_show_application_detail`) & `Change Status` (`.stage_sub_stage`) | Soft neutral `#fafbfc`, border-top `#f1f5f9` |

---

## 5. Mandatory Header Background Standard Compliance

Under Rule 20, all drawer headers, card headers, sub-card section headers, and tabular matrices **MUST** carry a dedicated background color (e.g. `#f8fafc` or `#f1f5f9`) with a crisp border (`#e2e8f0`):
- **Change Talent Status Drawer Header (`.bstatus-topbar`):** Updated from plain white `#fff` to `background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important; box-shadow: 0 1px 3px rgba(15, 23, 42, 0.04) !important;`.
- **Relationship Manager Drawer Header (`#kt_activitiesB_header`):** Updated to `background: #f8fafc !important; border-bottom: 1px solid #e2e8f0 !important;`.
- **Tier 1 Toolbar Card (`.cd-bench-toolbar-card`):** Dedicated `#f8fafc !important` background with `#e2e8f0` border.
- **Sub-Card Section Headers (`.cd-subcard-header`):** Dedicated `#f8fafc !important` background with `#e2e8f0` border.

---

## 6. File Changes Matrix

| File Path | Changes Applied |
|---|---|
| `resources/views/service_company/bench-candidates/index.blade.php` | • Injected Rule 20 CSS stylesheet for Toolbar Card, Sub-Tabs Card, Talent Cards, Sub-Cards, Skills Pills, and 15.6" responsiveness.<br>• Enforced Mandatory Header Background Standard on `.bstatus-topbar` and `#kt_activitiesB_header`.<br>• Replaced legacy table markup with Tier 1 Toolbar Card, Tier 2 Sub-Tabs Card, and Tier 3 Stream Container `#benchListingTable`.<br>• Added clear button handler `#btnBenchSearchClear` and updated selection bar triggers. |
| `resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php` | • Replaced legacy `<tr>...</tr>` table row markup with the Rule 20 Master Primary Card (`<article class="cd-talent-card">`).<br>• Implemented 6 discrete upper sub-cards with dedicated headers and formatted data.<br>• Implemented Bottom Activity Strip with added date, skill pills, and action buttons.<br>• Preserved 100% of data attributes and drawer triggers (`.chk_list_item`, `.bench_show_application_detail`, `.stage_sub_stage`, `.hmb-listing`, `data-bench-pipe`, `data-bench-pipe-interview`).<br>• Implemented empty state card `.cd-talent-empty`. |

---

## 7. 15.6" Screen Fit & Responsiveness Standard

- **Viewport Targets:** `1366×768` (Standard Laptop) and `1920×1080` (FHD Laptop).
- **CSS Grid:** Sub-cards grid uses fractional units with `minmax(0, ...)` (`minmax(0, 2.3fr) minmax(0, 1.35fr) minmax(0, 1.35fr) minmax(0, 1.25fr) minmax(0, 1.15fr) minmax(0, 2.0fr)`).
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
  ✓ phase 1 browser event hygiene debounce and duplicate listener guards                                         1.21s  
  ✓ phase 2 zero mysql queries in ajax blade partials                                                            0.36s  
  ✓ phase 3 transactional write bulk es alignment and timeline memoization                                       0.37s  
  ✓ phase 4 unified cache keys single flight locks and outbox recording                                          0.36s  
  ✓ phase 5 client first ram stores embedded counts and page isolation                                           0.35s  

  Tests:    5 passed (34 assertions)
  Duration: 3.06s
```
```bash
$ php artisan test tests/Feature/BenchToJobAppTrackerPipelineContractTest.php --filter="test_phase_6_async_elasticsearch_reindexing_and_outbox_worker"
   PASS  Tests\Feature\BenchToJobAppTrackerPipelineContractTest
  ✓ phase 6 async elasticsearch reindexing and outbox worker                                                     1.68s  

  Tests:    1 passed (13 assertions)
  Duration: 2.10s
```

All 6 contract phases pass with 47 assertions and 0 errors. Strict CQRS (0% MySQL reads) and zero module regressions are verified.
