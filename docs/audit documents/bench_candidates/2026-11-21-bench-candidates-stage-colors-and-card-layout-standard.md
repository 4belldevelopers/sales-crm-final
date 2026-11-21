# Bench Candidates Stage Colors, Bottom Strip Interchange & Tab Redesign Standard

## Metadata
- **Module:** Service Company Bench Candidates (`/service_company/bench-candidates`)
- **Author:** Antigravity AI God-Level Engineer
- **Date:** 2026-11-21
- **Target Route:** `https://testing.jero.works/service_company/bench-candidates`
- **Architecture Compliance:** Rule 20 (Modern Card + Sub-Card Enterprise Design System), Rule 24 (Default Timezone with Date & Time), Rule 7 (Zero Business Logic Regressions), Rule 18 (Code Freeze Scoping).

---

## Executive Summary

This standard formally establishes:
1. **Sub-Card 7 Bottom Activity Strip Alignment:** Interchanged positions so technical keywords / skills pills are positioned on the left flex column (`.cd-bottom-skills`), while the Added Date & Time (formatted strictly under Rule 24: `d M y, h:i A T`), Days on Bench badge, and Change Status button are positioned on the right (`.cd-bottom-right`).
2. **Action Tags Cleanup:** Completely removed redundant action buttons (`Similar Clients` and `View Profile`) from the bottom strip of Sub-Card 7. Similar Clients is already prominently featured with direct counts and click triggers in Sub-Card 5 (`Direct Client`, `Internal Client`, `My Organization`), and candidate profile view is natively bound to the candidate avatar and name click handlers in Sub-Card 1.
3. **Toolbar Refresh Button Removal:** Removed the toolbar refresh button (`#btnCafRefreshList`) completely from the Tier 1 search and sort card (`.caf-toolbar-card`), streamlining the toolbar and allowing the micro-search input to fill available horizontal width.
4. **Interactive Tab Cards (Client Tracker Inspiration):** Redesigned the 6 status KPI cards (`#benchKpiGrid` / `.cl-stat-grid`) with inspiration from `service_company/client/index.blade.php`.
5. **Canonical Bench Talent Stage Colors (Unique from Candidate Job Pipeline Stages):** Saved and codified unique, distinct colors for Bench Talent stages across the application so they are never confused with candidate job pipeline stages:
   - **Pool:** **Purple** (Deep Royal Violet `#7c3aed`, bg `#faf5ff`, border `#ddd6fe` / `#c084fc`, num `#6d28d9`, label `#581c87`)
   - **Bench:** **Blue** (Vibrant Cobalt Royal Blue `#2563eb`, bg `#eff6ff`, border `#bfdbfe` / `#60a5fa`, num `#1d4ed8`, label `#1e40af`)
   - **Hold:** **Orange** (Vivid Coral Tangerine Orange `#ea580c`, bg `#fff7ed`, border `#fed7aa` / `#fb923c`, num `#c2410c`, label `#9a3412`)
   - **Placed:** **Green** (Forest Jade Emerald Green `#059669`, bg `#ecfdf5`, border `#a7f3d0` / `#34d399`, num `#065f46`, label `#047857`)
   - **Exited:** **Red** (Crimson Ruby Red `#dc2626`, bg `#fef2f2`, border `#fecaca` / `#f87171`, num `#b91c1c`, label `#991b1b`)
   - **All Talents:** **Cobalt Blue / Neutral Slate** (`#2563eb`, bg `#eff6ff`, border `#60a5fa`, num `#1d4ed8`, label `#1e40af`)

---

## Bench Talent vs. Candidate Job Stage Palette Comparison

To ensure zero ambiguity, Bench Talent stage colors use dedicated color tokens that differ from candidate job pipeline stages:

| Entity Domain | Stage Name | Visual Accent | Hex Color | Background Tint | Border Tint | Distinguishing Characteristic |
|---|---|---|---|---|---|---|
| **Bench Talent** | **Pool** | Royal Violet | `#7c3aed` | `#faf5ff` | `#ddd6fe` / `#c084fc` | Deep royal purple (distinct from job Offer `#8b5cf6`) |
| **Bench Talent** | **Bench** | Cobalt Blue | `#2563eb` | `#eff6ff` | `#bfdbfe` / `#60a5fa` | Vibrant electric cobalt (distinct from job Screening sky `#38bdf8`) |
| **Bench Talent** | **Hold** | Tangerine Orange | `#ea580c` | `#fff7ed` | `#fed7aa` / `#fb923c` | Pure citrus orange (distinct from job Evaluation amber `#f59e0b`) |
| **Bench Talent** | **Placed** | Forest Jade Green | `#059669` | `#ecfdf5` | `#a7f3d0` / `#34d399` | Deep forest emerald (distinct from job Joined `#10b981`) |
| **Bench Talent** | **Exited** | Crimson Red | `#dc2626` | `#fef2f2` | `#fecaca` / `#f87171` | Ruby crimson red (distinct from job Rejected rose `#f43f5e`) |
| **Bench Talent** | **All Talents** | Cobalt Royal Blue | `#2563eb` | `#eff6ff` | `#60a5fa` | System active primary |

---

## File Changes Matrix

| File Path | Changes Made |
|---|---|
| `resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php` | 1. Swapped Sub-Card 7 layout: skills keywords moved to `.cd-bottom-skills` on left; Added date/time, Days on Bench, and Change Status button moved to `.cd-bottom-right` on right.<br>2. Removed `.btn-similar-clients` and `.bench_show_application_detail` from Sub-Card 7.<br>3. Mapped status pills and left card border accents (`$statusBorderAccent`, `$statusPillClass`, `$statusPillStyle`) to Pool (Purple `#7c3aed`), Bench (Blue `#2563eb`), Hold (Orange `#ea580c`), Placed (Green `#059669`), Exited (Red `#dc2626`). |
| `resources/views/service_company/bench-candidates/index.blade.php` | 1. Removed toolbar refresh button (`#btnCafRefreshList`).<br>2. Updated `.cl-card-icon` and `.cl-card-sub` color styles for `purple`, `blue`, `orange`, `green`, `red`.<br>3. Updated active tab selection CSS (`.cl-card.active[data-tab="..."]`) for each stage.<br>4. Updated HTML in `#benchKpiGrid` (Card 2 Pool -> Purple, Card 3 Bench -> Blue, Card 4 Hold -> Orange, Card 5 Placed -> Green, Card 6 Exited -> Red).<br>5. Synchronized `.status-pool`, `.status-bench`, `.status-hold`, `.status-placed`, `.status-exited` badge CSS. |
| `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` | Recorded Canonical Bench Talent Stage Palettes & Visual Tokens under Section 6.D. |
| `.ai/PROJECT_MEMORY.md` | Recorded Bench Talent Stage Palettes under Section 1 Living Memory Snapshot. |

---

## 15.6" Screen Fit & Responsiveness (Rule 14 & Rule 20)

- **1366x768 & 1920x1080 Zero Overflow:**
  - The 6 KPI stat cards in `.cl-stat-grid` use fluid CSS Grid `repeat(6, minmax(0, 1fr))` with graceful wrapping at `@media (max-width: 1200px)` into `repeat(3, 1fr)`.
  - Sub-Card 7 bottom strip uses `display: flex; justify-content: space-between; align-items: center; gap: 12px;` with horizontal scroll suppression (`overflow-x: auto; scrollbar-width: none;`) on the skills keywords wrapper, preventing any card blowout.
  - The toolbar card gracefully fills available horizontal space without the refresh button.

---

## Verification Evidence

1. **PHP Syntax Verification:**
   - `php -l resources/views/service_company/bench-candidates/index.blade.php` -> passed (0 errors).
   - `php -l resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php` -> passed (0 errors).
2. **AI Guard Cleanliness:**
   - Verified no `dd()`, `dump()`, `var_dump()`, `console.log()` debug calls introduced.
   - Tenancy checks and existing drawer bindings preserved without mutation.
