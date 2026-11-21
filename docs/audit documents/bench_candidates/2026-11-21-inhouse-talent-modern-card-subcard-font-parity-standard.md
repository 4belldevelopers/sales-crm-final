# In-House Talent Modern Card & Sub-Card Typography Parity Standard

## Metadata
- **Module:** Service Company Bench Candidates / In-House Talent (`/service_company/bench-candidates`)
- **Author:** Antigravity AI God-Level Engineer
- **Date:** 2026-11-21
- **Target Route:** `https://testing.jero.works/service_company/bench-candidates`
- **Reference Standard:** All Candidates Tracker (`resources/views/service_company/ajax_pages/all_candidates.blade.php` & `resources/views/service_company/cvtracker/index.blade.php`)
- **Architecture Compliance:** Rule 20 (Modern Card + Sub-Card Enterprise Design System), Rule 14 (15.6" Screen Fit 1366×768 & 1920×1080), Rule 7 (Zero Business Logic Regressions), Rule 18 (Code Freeze Scoping).

---

## Executive Summary

This standard establishes exact 1:1 parity between the **In-House Talent (Bench Candidates)** modern card + sub-card layout and the **All Candidates Tracker** (`cvtracker`) design standard.

### Typography, Size & Spacing Synchronizations
1. **Master Font Family & Smoothing:**
   - Enforced `font-family: 'Poppins', sans-serif !important;` with `-webkit-font-smoothing: antialiased;` and `-moz-osx-font-smoothing: grayscale;` across `.cv-cards-stream`, `.cd-talent-card`, and all `.cd-talent-subcard` containers.
2. **Sub-Card Container Dimensions & Padding:**
   - Standardized `.cd-talent-subcard` to `background: #f8fafc !important; border: 1px solid #edf2f7 !important; border-radius: 8px !important; padding: 7px 9px !important;` for breathable, crisp hierarchy on 15.6" screens.
3. **Sub-Card Header Labels:**
   - Standardized `.cd-subcard-label` to `font-size: 9.5px !important; font-weight: 700 !important; text-transform: uppercase !important; letter-spacing: 0.35px !important; color: #64748b !important; line-height: 1.1 !important;`.
4. **Sub-Card 1 (Talent Profile & Status):**
   - Candidate Name: `12.5px`, `font-weight: 700`, `color: #0f172a`, `line-height: 1.25`.
   - Role / Designation: `11px`, `font-weight: 500`, `color: #475569`, `line-height: 1.25`, `margin-top: 1px`.
   - Current / Bench Company: `10.5px`, `font-weight: 500`, `color: #64748b`, `line-height: 1.25`, `margin-top: 2px`, building icon `9.5px #94a3b8`.
   - Avatar Box: `36x36px`, `border-radius: 8px`, `font-size: 13.5px`, `font-weight: 700`.
   - Category Pill (Full-Time / Contract): `8.5px`, `font-weight: 700`, `padding: 1.5px 6px`, `border-radius: 4px`.
   - Stage Pill (Canonical Bench Stage Colors): `8.5px`, `font-weight: 700`, `padding: 1.5px 6px`, `border-radius: 4px`, uppercase.
5. **Sub-Card 2 (Compensation & Experience):**
   - Header Badge: `8.5px`, `font-weight: 700`, `padding: 1.5px 6px`, `border-radius: 4px`.
   - Bill Rate: Label `10.5px fw-500 #64748b`, Value `12px fw-700 #0f172a`, Mode suffix `10px fw-500 #64748b`.
   - Experience: `10.5px fw-500 #475569`, bold number `10.5px fw-700 #0f172a`, relevant experience `10px #64748b`.
   - Notice Period: Label `10.5px fw-500 #64748b`, Value `10.5px fw-600` with high contrast semantic tint.
6. **Sub-Card 3 (Location & Degree):**
   - City / Location: `11.5px fw-600 #0f172a`, icon `10px #94a3b8`.
   - Work Mode: `10.5px fw-500 #475569`, icon `9.5px text-primary`.
   - Education: `10.5px fw-500 #64748b`, icon `10px #94a3b8`, uppercase.
7. **Sub-Card 4 (Relationship Manager):**
   - Manager Name: `11.5px fw-600 #0f172a`, icon `10.5px text-primary`.
   - Organization / Scope: `10.5px fw-500 #475569`, icon `9.5px #94a3b8`.
   - Change Button: `10.5px fw-600 #009ef7`.
8. **Sub-Card 5 (Similar Clients):**
   - Header: `9.5px fw-700 uppercase`.
   - Badges (Direct, Internal, My Organization): `9.5px fw-700`, `padding: 1.5px 6px`, `border-radius: 4px`.
   - Counts: `11px fw-700 #0f172a`.
9. **Sub-Card 6 (Clients Pipeline Activity):**
   - 8-col Headers (INW, SOU, SCR, EVA, NEG, OFF, JOI, REJ): `8px fw-700 #64748b`.
   - 8-col Counts: `10.5px fw-700 #0f172a`.
   - Summary Row: Interviews `10px` (`#009ef7` count), Views `10px` (`#0f172a` count).
10. **Sub-Card 7 (Bottom Activity Strip):**
   - Skills Pills (Left): `10px fw-500`, `padding: 2px 7px`, `border-radius: 4px`, `background: #f1f5f9`, `color: #475569`, `border: 1px solid #e2e8f0`.
   - Added Timestamp (Rule 24): Label `10.5px fw-500 #64748b`, Date/Time `10.5px fw-700 #0f172a`.
   - Days on Bench Badge: `10px fw-600`, `padding: 2px 7px`, `border-radius: 4px`, `background: #fffbeb`, `color: #d97706`, `border: 1px solid #fde68a`.
   - Change Status Button: `10.5px fw-600`, `height: 26px`, `padding: 3px 10px`, `border-radius: 6px`, `background: #ecfdf5`, `color: #059669`, `border: 1px solid #a7f3d0`.

---

## Detailed Typography & Hierarchy Parity Matrix

| Component Element | All Candidates Tracker Standard | In-House Talent (Bench Candidates) | Status |
|---|---|---|---|
| Card Font Family | `'Poppins', sans-serif !important;` | `'Poppins', sans-serif !important;` | Identical |
| Subcard Padding | `7px 9px` | `7px 9px` | Identical |
| Subcard Background | `#f8fafc` | `#f8fafc` | Identical |
| Subcard Border | `1px solid #edf2f7` | `1px solid #edf2f7` | Identical |
| Subcard Radius | `8px` | `8px` | Identical |
| Header Label | `9.5px 700 uppercase #64748b` | `9.5px 700 uppercase #64748b` | Identical |
| Candidate Name | `12.5px 700 #0f172a (line-height: 1.25)` | `12.5px 700 #0f172a (line-height: 1.25)` | Identical |
| Candidate Role | `11px 500 #475569 (margin-top: 1px)` | `11px 500 #475569 (margin-top: 1px)` | Identical |
| Candidate Company | `10.5px 500 #64748b (icon 9.5px)` | `10.5px 500 #64748b (icon 9.5px)` | Identical |
| Salary / Bill Rate | `12px 700 #0f172a (suffix 10px 500)` | `12px 700 #0f172a (suffix 10px 500)` | Identical |
| Experience Value | `10.5px 500 #475569 (num 10.5px 700)` | `10.5px 500 #475569 (num 10.5px 700)` | Identical |
| Notice Period Value | `10.5px 500 #475569 (val 10.5px 600)` | `10.5px 500 #475569 (val 10.5px 600)` | Identical |
| Location City | `11.5px 600 #0f172a (icon 10px)` | `11.5px 600 #0f172a (icon 10px)` | Identical |
| Work Mode | `10.5px 500 #475569 (icon 9.5px)` | `10.5px 500 #475569 (icon 9.5px)` | Identical |
| Education / Degree | `10.5px 500 #64748b uppercase` | `10.5px 500 #64748b uppercase` | Identical |
| Relationship Manager | `11.5px 600 #0f172a` | `11.5px 600 #0f172a` | Identical |
| Change RM CTA | `10.5px 600 #009ef7` | `10.5px 600 #009ef7` | Identical |
| Similar Clients Badges | `9.5px 700 uppercase (counts 11px 700)` | `9.5px 700 uppercase (counts 11px 700)` | Identical |
| Pipeline Micro Grid | `8px 700 (headers) / 10.5px 700 (counts)` | `8px 700 (headers) / 10.5px 700 (counts)` | Identical |
| Bottom Strip Skills | `10px 500 (pad: 2px 7px, rad: 4px)` | `10px 500 (pad: 2px 7px, rad: 4px)` | Identical |
| Bottom Strip Date | `10.5px 500 (date: 10.5px 700 #0f172a)` | `10.5px 500 (date: 10.5px 700 #0f172a)` | Identical |
| Bottom Strip Days Badge | `10px 600 #d97706 (pad: 2px 7px)` | `10px 600 #d97706 (pad: 2px 7px)` | Identical |
| Change Status Button | `10.5px 600 #059669 (height: 26px)` | `10.5px 600 #059669 (height: 26px)` | Identical |

---

## File Changes Matrix

| File Path | Changes Made |
|---|---|
| `resources/views/service_company/bench-candidates/index.blade.php` | 1. Updated `.cd-talent-card` and `.cd-talent-subcard` to use exact All Candidates Tracker typography, `padding: 7px 9px;`, and border styling (`border: 1px solid #edf2f7`).<br>2. Aligned `.cd-talent-name` (`12.5px, 700`), `.cd-talent-role` (`11px, 500`), `.cd-talent-company` (`10.5px, 500`), `.cd-val-salary` (`12px, 700`), `.cd-val-exp` (`10.5px`), `.cd-val-avail` (`10.5px`), `.cd-val-location` (`11.5px, 600`), `.cd-val-wm` (`10.5px, 500`), `.cd-val-edu` (`10.5px, 500`), `.cd-manager-name` (`11.5px, 600`), and `.cd-rm-change-btn` (`10.5px, 600`).<br>3. Aligned responsive `@media (max-width: 1440px)`, `(max-width: 1180px)`, and `(max-width: 640px)` breakpoints directly with `cvtracker/index.blade.php`. |
| `resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php` | 1. Refactored all 6 sub-cards and bottom strip inline styles to match exact font sizes, font weights, line heights, and padding rules of `all_candidates.blade.php`.<br>2. Maintained all existing drawer and event bindings (`chk_list_item`, `stage_sub_stage`, `hmb-listing`, `btn-similar-clients`, `exploreBase`). |

---

## 15.6" Screen Fit & Responsiveness (Rule 14 & Rule 20)

- **1366x768 & 1920x1080 Zero Overflow:**
  - Standard grid column widths `minmax(0, 1.85fr) minmax(0, 1.35fr) minmax(0, 1.25fr) minmax(0, 1.25fr) minmax(0, 1.35fr) minmax(0, 1.65fr)` ensure all 6 sub-cards fit seamlessly on a standard 15.6" display.
  - Subcard internal padding is clamped to `7px 9px` (and `6px 8px` on screens $\le 1440$px) to prevent vertical bloat.
  - Text truncation with `text-overflow: ellipsis; white-space: nowrap;` guarantees zero layout shift or accidental wrapping.

---

## Verification Evidence

1. **PHP Syntax Validation:**
   - `php -l resources/views/service_company/bench-candidates/index.blade.php` -> Passed (0 errors).
   - `php -l resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php` -> Passed (0 errors).
2. **Compiled View Cache:**
   - `php artisan view:clear` -> Compiled views cleared successfully.
3. **Tenancy & Security Verification:**
   - Zero modifications to backend queries or controller tenant scoping.
   - All drawer data attributes (`data-bench-row-id`, `data-row-id`, `data-candidate-id`) fully preserved.
