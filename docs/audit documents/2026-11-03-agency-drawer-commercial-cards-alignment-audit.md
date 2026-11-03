# Audit & Architectural Changelog: Agency Drawer Commercial Cards Typographic Baseline & Alignment

**Date:** 2026-11-03  
**Module:** Service Company — Consultancy / Agency Drawer (`dviewMore.blade.php`)  
**Design Standard:** Rule 20 (Modern Card + Sub-Card Enterprise Design Standard) & Rule 14 (Metronic 8, Poppins Font, 15.6-inch Screen Optimization)  
**Target Repository Branch:** `staging`  

---

## 1. Executive Summary & Problem Analysis

In the JERO Agency Drawer (`resources/views/service_company/consultancy/dviewMore.blade.php`), the commercial cards for **Fulltime Hiring Services** and **Contract Hiring Services** summarize key business terms.

A visual audit of the rendered drawer on 15.6-inch displays (`1366×768` and `1920×1080`) revealed the following issues:
1. **Vertical Baseline Misalignment:**
   - In Row 2 (`Invoicing`), the descriptor `Trigger:` and value `Candidate Joined` floated higher than `One Time Fee`.
   - In Row 4 (`Replacement`), `Free Replacement` dropped ~2–3px below `90 Days`, breaking the typographic line.
   - In Row 1 (`Default Rate` / `Bill Rate`), `Percentage of CTC` and `Contract Bill Rate` appeared staggered against their primary bold rates (`10%`, `₹ 5`).
   - **Root Cause:** The value container `.cd-srv-c-val` used `display: flex !important; align-items: center !important;`. Because `<strong>` elements and regular `<span>` elements have different font weights (700 vs 500) and computed bounding boxes under the Poppins font metrics, CSS Flexbox centered their outer boxes rather than aligning their typographic text baselines.
2. **Key Label Horizontal Boundary & Truncation:**
   - The label column `.cd-srv-c-key` had a fixed width of `98px`. Because `Payment Terms:` with its leading font-awesome icon requires ~101px in Poppins 10.5px, the colon `:` was clipped off by `overflow: hidden; text-overflow: ellipsis;` on standard resolutions.
3. **Screen Real Estate & 5-Row Rule Strategy:**
   - Replacement guarantee needed a dedicated row isolated from payment terms to prevent multi-line wraps on smaller 15.6" screens (`1366×768`).

---

## 2. Forensic Fix & Enterprise Implementation

### A. CSS Typographic Baseline Architecture
1. **Eliminated Flexbox Box-Centering in Value Cells:**
   - Removed `display: flex` from `.cd-srv-c-val`.
   - Replaced it with native CSS inline text flow (`vertical-align: baseline !important; line-height: 20px !important;`).
   - Guaranteed that all inline children (`<strong>`, `<span>`, separators `·`) sit on the identical mathematical baseline.
2. **Fixed Tabular Key Column:**
   - Standardized `.cd-srv-c-key` to `width: 105px !important; min-width: 105px !important; max-width: 105px !important;`.
   - Adjusted `line-height: 20px !important;` and `align-items: baseline !important;` to lock all row keys into an unshakeable grid.
3. **Optical Elevation of Badges and Chips:**
   - Badges (`.cd-srv-c-badge`) and country micro-chips (`.cd-srv-c-country-list`) now use `vertical-align: middle !important; position: relative !important; top: -1px !important;` so their borders and background pills align symmetrically with the text cap-height.

### B. Standardized 5-Row Commercial Specification
Both **Fulltime Hiring Services** and **Contract Hiring Services** cards strictly adhere to the 5-row enterprise layout:

| Row | Key (105px Column) | Fulltime Value Flow | Contract Value Flow |
|---|---|---|---|
| **Row 1** | `Default Rate:` / `Bill Rate:` | `10% · Percentage of CTC [5 Tiers]` | `₹ 5 / Hour · Contract Bill Rate [Hourly]` |
| **Row 2** | `Invoicing:` | `One Time Fee · Trigger: Candidate Joined` | `Monthly in Advance Invoicing · Trigger: Monthly in Advance` |
| **Row 3** | `Payment Terms:` | `Net 30 Days` | `Net 30 Days` |
| **Row 4** | `Replacement:` (Dedicated) | `90 Days Free Replacement` | `90 Days Free Replacement Contractor` |
| **Row 5** | `Operations:` | `Operate in 1 Country: [IN]` | `Operate in 1 Country: [IN]` |

---

## 3. Verification & Regressions

1. **PHP Syntax Verification:**
   - `php -l resources/views/service_company/consultancy/dviewMore.blade.php` ➔ `No syntax errors detected`.
2. **Compiled Blade Cache:**
   - `php artisan view:clear` ➔ Compiled views cleared cleanly.
3. **Automated Render Assertion Suite:**
   - Executed `scratch/test_dviewmore_render.php` across 11 test assertions covering all 5 rows for Fulltime and Contract cards.
   - All 11 assertions passed with 100% compliance.
4. **Tenancy and Business Logic Safety:**
   - Zero modifications to controller logic, queries, or database reads/writes.
   - Strictly isolated to `resources/views/service_company/consultancy/dviewMore.blade.php`.
