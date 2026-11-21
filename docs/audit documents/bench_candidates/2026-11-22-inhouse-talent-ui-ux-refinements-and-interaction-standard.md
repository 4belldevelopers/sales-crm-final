# In-House Talent Page – UI/UX Refinements & Interaction Standard

## Metadata
- **Module:** Service Company Bench Candidates / In-House Talent (`/service_company/bench-candidates`)
- **Author:** Antigravity AI God-Level Engineer
- **Date:** 2026-11-22
- **Target Route:** `https://testing.jero.works/service_company/bench-candidates`
- **Reference Standards:**
  - Job Tracker (`resources/views/service_company/jobs/index.blade.php`) — Header layout, styling, Add Button, Hiring Pipeline activity track
  - Candidate Tracker (`resources/views/service_company/ajax_pages/all_candidates.blade.php` & `\App\Services\AppTrackerCardPresenter`) — Candidate Info typography, Notice Period & SNP LWD color logic, skills placement
- **Architecture Compliance:** Rule 20 (Modern Card + Sub-Card Enterprise Design System), Rule 14 (15.6" Screen Fit 1366×768 & 1920×1080), Rule 7 (Zero Business Logic Regressions), Rule 18 (Code Freeze Scoping), Rule 21 (Audit Document Standard), Rule 24 (Timezone Alignment).

---

## Executive Summary

This release performs a comprehensive, multi-point refinement of the **In-House Talent (Bench Candidates)** page. By adopting established patterns from the **Job Tracker** and **Candidate Tracker** modules, it resolves layout overlaps, synchronizes stage branding, optimizes typography and candidate metadata, and introduces full-surface click interactions across all modern sub-cards without mutating existing business logic or database queries.

### Key Refinements Summary (11 Requirements)
1. **Header Spacing & Alignment:** Eliminated aggressive negative margin (`margin-top: -65px !important;`) on `.kt_content_bench` that caused vertical overlap with the top navigation bar. Matched `.cd-jobs-header-card` to Job Tracker with 44×44px soft blue icon box (`#eff6ff`), verified badge (`#16a34a`), count pill (`#eff6ff`), subtitle (`#64748b`), and primary action button `.cd-jobs-add-btn` (8px radius, `#009ef7`, crisp hover transition).
2. **Rename Labels & KPI Text:**
   - Renamed `Total talent roster` / `Total Talent Roaster` $\rightarrow$ **`Total Inhouse Talents`**.
   - Renamed `Active on Bench` $\rightarrow$ **`Active on Marketplace`**.
3. **Stage Tabs – Strict Color Consistency:**
   - Established unified stage color identities:
     - **All Talents:** Blue (`#2563eb`)
     - **Pool:** Purple (`#7c3aed`)
     - **Bench:** Blue (`#2563eb`)
     - **On Hold:** Orange (`#ea580c`)
     - **Placed:** Green (`#059669`)
     - **Exited:** Red (`#dc2626`)
   - When a tab is active/selected, its icon color, title text color, count number, sub-label, and bottom active indicator/border all match the designated stage color.
4. **Relationship Manager Subcard Designation:**
   - Replaced static fallback `In-House Talent` with the actual **Relationship Manager designation** (`users_profile.designation` via `$firstHr->designation` or `$hmEntry['designation']`).
   - Graceful fallback: If no designation is configured, it displays `Relationship Manager` rather than a candidate status tag.
5. **Client Pipeline Activity – Complete Redesign:**
   - Completely redesigned Sub-Card 6 following **Job Tracker $\rightarrow$ Hiring Pipeline** track standard (`.cd-pipeline-track`).
   - Uses stage-specific colored badges: `s-inw` (`#eff6ff`), `s-sou` (`#f8fafc`), `s-scr` (`#fef3c7`), `s-eva` (`#f5f3ff`), `s-neg` (`#fff7ed`), `s-off` (`#ecfdf5`), `s-joi` (`#f0fdf4`), `s-rej` (`#fef2f2`).
   - Compact summary row below track: `Interviews: X • Views: Y`.
6. **Notice Period & Last Working Day (LWD) Color Logic:**
   - Adopted candidate tracker standard via `\App\Services\AppTrackerCardPresenter::resolveNoticePeriod($row)`:
     - `NP: Immediate` $\rightarrow$ Green (`#16a34a`)
     - `NP: X Days` $\rightarrow$ Amber/Orange (`#d97706`)
     - `SNP LWD: d M y` $\rightarrow$ Purple (`#7c3aed`)
7. **Skills Placement:**
   - Positioned skill chips cleanly at the bottom-left of the candidate card directly below the Candidate Info subcard (`.cd-bottom-skills { justify-content: flex-start !important; }`).
   - No skill match % or floating badges displayed; clean tags only.
8. **Clickable Subcards – Candidate Drawer (Sub-Cards 1, 2, 3):**
   - Candidate Info, Bill Rate, and Location & Degree subcards are full-surface clickable (`.js-open-cand-drawer.cursor-pointer`) to open the Candidate Drawer (`.bench_show_application_detail`).
   - Inner interactive elements (`chk_list_item`, `.stage_sub_stage`) are protected with `event.stopPropagation()`.
9. **Relationship Manager Subcard – Drawer Interaction (Sub-Card 4):**
   - Entire area of Sub-Card 4 is clickable (`.hmb-listing.cursor-pointer`) to open the Relationship Manager drawer (`#kt_activitiesHMB_toggle`).
   - Replaced nested `<a class="manager-change">` with `<span class="manager-change cd-rm-change-btn">` to eliminate nested click conflicts.
10. **Similar Clients Subcard – Drawer Interaction (Sub-Card 5):**
    - Entire area of Sub-Card 5 is clickable (`.btn-similar-clients.cursor-pointer`) to open the Similar Clients drawer (`#kt_activitiesSimilarClients_toggle`).
11. **Client Pipeline Activity – Navigation (Sub-Card 6):**
    - Entire area of Sub-Card 6 is clickable (`.js-open-pipe-explore.cursor-pointer`) to navigate to `bench_explore/{id}/all`.

---

## Architecture & Data Flow

```
┌────────────────────────────────────────────────────────────────────────┐
│                        IN-HOUSE TALENT PAGE                            │
│                 (/service_company/bench-candidates)                    │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │
    ┌───────────────────────────────┴───────────────────────────────┐
    ▼                                                               ▼
┌──────────────────────────────────────┐        ┌──────────────────────────────────────┐
│  Tier 1: Enterprise Header Card      │        │  Tier 2: Stage KPI Filter Tabs       │
│  - Job Tracker layout parity         │        │  - Unified stage color system        │
│  - 44×44px blue icon box             │        │  - Active tab text, icon, border sync│
│  - "Add In-house Talent" CTA button  │        │  - "Total Inhouse Talents" KPI       │
│  - Verified badge & talent count     │        │  - "Active on Marketplace" KPI       │
└──────────────────────────────────────┘        └──────────────────────────────────────┘
                                    │
                                    ▼
┌──────────────────────────────────────────────────────────────────────────────┐
│  Tier 3: Modern Card + Sub-Card Candidate Stream (Rule 20)                   │
├───────────────────┬───────────────────┬───────────────────┬──────────────────┤
│ Sub-Card 1        │ Sub-Card 2        │ Sub-Card 3        │ Sub-Card 4       │
│ CANDIDATE INFO    │ BILL RATE         │ LOCATION & DEGREE │ RELATIONSHIP MGR │
│ • Name & Title    │ • Clean $ Rate/hr │ • City & Workmode │ • RM Name        │
│ • NP / SNP LWD    │ • Exp & Rel Exp   │ • Degree / Qual   │ • RM Designation │
│ (Click -> Drawer) │ (Click -> Drawer) │ (Click -> Drawer) │ (Click -> Drawer)│
├───────────────────┴───────────────────┴───────────────────┴──────────────────┤
│ Sub-Card 5: SIMILAR CLIENTS           │ Sub-Card 6: CLIENT PIPELINE ACTIVITY │
│ • Direct / Internal / Org pills       │ • Colored 8-stage activity track     │
│ (Click -> Similar Clients Drawer)     │ • Interviews & Views summary row     │
│                                       │ (Click -> bench_explore/{id}/all)    │
├───────────────────────────────────────┴──────────────────────────────────────┤
│ Bottom Activity & Skills Strip:                                              │
│ • Bottom Left: Skill chips (Zero match %)                                    │
│ • Bottom Right: Added Date & Time (Rule 24 Timezone) + Bench Days + Status   │
└──────────────────────────────────────────────────────────────────────────────┘
```

---

## File Changes Matrix

| File | Change Description | Requirements Addressed |
|---|---|---|
| `resources/views/service_company/bench-candidates/index.blade.php` | • Fixed negative margin on `.kt_content_bench` to `0 !important` and `padding-top: 14px !important;` to eliminate header overlap.<br>• Redesigned header card (`.cd-jobs-header-card`) and Add Talent button (`.cd-jobs-add-btn`) matching Job Tracker.<br>• Synchronized stage colors across all 6 KPI cards (All: Blue, Pool: Purple, Bench: Blue, Hold: Orange, Placed: Green, Exited: Red).<br>• Renamed KPI labels: `Total talent roster` $\rightarrow$ `Total Inhouse Talents`, `Active on Bench` $\rightarrow$ `Active on Marketplace`.<br>• Added CSS for `.cd-talent-subcard.cursor-pointer` hover states (`translateY(-1px)`, border `#93c5fd`, subtle shadow).<br>• Added CSS for notice period badges (`.simf-notice-*`) and Hiring Pipeline track (`.cd-pipeline-track`, `.s-inw` to `.s-rej`).<br>• Anchored skills to bottom-left (`.cd-bottom-skills { justify-content: flex-start !important; }`).<br>• Implemented event delegation handlers for `.js-open-cand-drawer` and `.js-open-pipe-explore`. | Req 1, 2, 3, 5, 6, 7, 8, 11 |
| `resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php` | • Resolved Notice Period and SNP LWD via `\App\Services\AppTrackerCardPresenter::resolveNoticePeriod($row)`.<br>• Resolved Relationship Manager designation from `$firstHr->designation` / `$hmEntry['designation']` with `Relationship Manager` fallback.<br>• Bound Sub-Cards 1, 2, 3 with `.js-open-cand-drawer.cursor-pointer` and `data-row-id="{{ $row->id }}"`.<br>• Protected checkboxes and status dropdowns with `onclick="event.stopPropagation();"`.<br>• Bound Sub-Card 4 with `.hmb-listing.cursor-pointer` and replaced nested `<a>` change button with `<span>`.<br>• Bound Sub-Card 5 with `.btn-similar-clients.cursor-pointer`.<br>• Redesigned Sub-Card 6 with `.cd-pipeline-track`, 8 colored stage badges, and `.js-open-pipe-explore.cursor-pointer` navigating to `bench_explore/{id}/all`. | Req 4, 5, 6, 8, 9, 10, 11 |

---

## 15.6" Screen Fit & Responsiveness (Rule 14 & Rule 20)

- **Grid Sizing:** The 6 upper sub-cards use auto-fit CSS grid (`grid-template-columns: repeat(6, minmax(0, 1fr)); gap: 10px;`) designed to fit comfortably without horizontal scrolling across `1366×768` (minimum desktop viewport) and `1920×1080` (standard 1080p).
- **Typography Calibration:**
  - Sub-card header labels: `9.5px` (fw: 700, uppercase, letter-spacing: `0.02em`, color: `#64748b`).
  - Candidate Name: `13px` (fw: 700, color: `#0f172a`).
  - Bill Rate: `$1 /H` (`12px` fw: 700 `#0f172a` amount, `10px` fw: 500 `#64748b` unit).
  - Experience numbers: `10px` fw: 700 total, `9.5px` fw: 600 relevant.
  - Notice Period / SNP LWD: `10px` fw: 600 with high-contrast semantic color tokens.
  - Location & Degree: `10px`–`11px` fw: 500–600 with clean single-line truncation.
  - Pipeline Track Stage Badges: `10px` fw: 700 on 22×20px rounded badges (`4px` radius).
- **Zero Horizontal Overflow:** All text containers implement `overflow: hidden; text-overflow: ellipsis; white-space: nowrap;` to prevent layout breaking on narrow viewports.

---

## Verification Evidence

1. **PHP Syntax Verification:**
   ```bash
   php -l resources/views/service_company/bench-candidates/index.blade.php
   # Output: No syntax errors detected in resources/views/service_company/bench-candidates/index.blade.php
   
   php -l resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php
   # Output: No syntax errors detected in resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php
   ```

2. **Blade Cache Clearance:**
   ```bash
   php artisan view:clear
   # Output: INFO Compiled views cleared successfully.
   ```

3. **Tenancy & Business Logic Audit:**
   - Multi-tenant boundary (`company_id`) completely intact.
   - Stage transitions (`bench_change_stage_ajax`) and status updates remain 100% untouched.
   - Zero modifications to queries or Eloquent models.
   - Rule 18 compliance: Only `service_company` files modified. Product Company and Staffing Company views remain untouched.
