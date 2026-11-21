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

This standard establishes exact visual and typographic balance between the **In-House Talent (Bench Candidates)** modern card + sub-card layout and the **All Candidates Tracker** (`cvtracker`) design standard, eliminating all clipping, premature truncation, text-overflow issues, and CSS inheritance leaks.

### Root Cause Analysis: Universal CSS Selector Leak (`.bench-dash *`)
- **The Bug:** Line 22 of `bench-candidates/index.blade.php` contained `.bench-dash, .bench-dash * { font-family: 'Poppins', sans-serif !important; font-size: 13px; }`.
- **The Impact:** The universal selector `*` has element-level targeting that beats normal CSS inheritance on nested child elements. Even though parent containers had `font-size: 10px !important;` or `11px !important;`, any nested `<span>` (such as `span.text-truncate.text-gray-700.fw-semibold` for Work Mode) was forcibly targeted and styled at `13px` by Chrome DevTools.
- **The Fix:**
  1. Removed `*` from line 22, changing it to `.bench-dash { font-family: 'Poppins', sans-serif !important; font-size: 13px; }`. Base 13px applies to the page via inheritance, without overriding child component typography.
  2. Targeted both the container and inner elements in CSS (`.cd-val-wm, .cd-val-wm *`, `.cd-val-location, .cd-val-location *`, etc.).
  3. Applied explicit `style="font-size: 10px !important;"` (and `11px !important;`) directly onto all inner `<span>` and `<a>` elements in `bench-candidate-list-ajax.blade.php`.

### Sub-Card 2: Redundant "Bill Rate" Text Removal
- **Previous:** Displayed `Bill Rate: $1 /H` inside Sub-Card 2 body, directly below the header label which already read `BILL RATE`.
- **Refactored:** Removed the redundant `Bill Rate:` prefix label from the body row. It now displays clean **`$1 /H`** (Amount `12px fw-700 #0f172a`, Suffix `10px fw-500 #64748b`), identical to Candidate Tracker's clean salary display (`$80/H` / `₹15L/yr`).

---

## Detailed Typography & Hierarchy Parity Matrix

| Sub-Card Element | Previous In-House Talent | Refactored In-House Talent | Candidate Tracker Parity |
|---|---|---|---|
| Sub-Card 1 Top Label | `[Full-time]` badge | `Candidate Info` label (`9.5px 700`) | Identical |
| Company Name | `10.5px` (fw: 500) | `10px` (fw: 500, icon `9px`) | Perfectly proportional |
| Sub-Card 2 Header Label | `COMPENSATION & EXP` (truncated to `COMPENSAT`) | **`BILL RATE`** (`9.5px 700`) | 100% fits, zero truncation |
| Sub-Card 2 Service Badge | `Paid Services` / `Free Services` | **`Paid`** / **`Free`** (`8.5px 700`) | Clean, compact |
| Sub-Card 2 Amount Line | `Bill Rate: $1 /H` | **`$1 /H`** (zero repeated "Bill Rate" words) | Clean, identical to `$80/H` |
| Total Exp Number | `10.5px 700` | **`10px 700`** | Proportional to label |
| Relevant Exp Number | `10px 700` | **`9.5px 600`** | Clean subordinate format |
| Notice Period Label | `Notice: Immediate` | **`NP: Immediate`** (`10px 600`) | Matches Candidate Tracker `NP:` |
| Location City | `13px` (via `.bench-dash *` leak) | **`11px 600`** | Fits without early ellipsis |
| Work Mode | `13px` (via `.bench-dash *` leak) | **`10px 600`** | Compact & legible |
| Education / Degree | `13px` (via `.bench-dash *` leak) | **`10px 500`** uppercase | Compact & legible |
| Relationship Mgr Name | `13px` (via `.bench-dash *` leak) | **`11px 600`** | Fits without early ellipsis |
| Relationship Mgr Scope | `13px` (via `.bench-dash *` leak) | **`10px 500`** | Clean subordinate format |
| Relationship Mgr Change | `13px` (via `.bench-dash *` leak) | **`10px 600`** | Crisp interactive CTA |

---

## File Changes Matrix

| File Path | Changes Made |
|---|---|
| `resources/views/service_company/bench-candidates/index.blade.php` | 1. Removed destructive universal selector `*` from line 22 (`.bench-dash { ... }`).<br>2. Added compound selectors `.cd-val-wm, .cd-val-wm *`, `.cd-val-location, .cd-val-location *`, `.cd-val-edu, .cd-val-edu *`, `.cd-val-exp, .cd-val-exp *`, `.cd-manager-name, .cd-manager-name *`, `.cd-rm-change-btn, .cd-rm-change-btn *` to enforce exact typography on all descendant nodes. |
| `resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php` | 1. Removed repeated `Bill Rate:` prefix label in Sub-Card 2 body, showing clean `$1 /H`.<br>2. Placed explicit `style="font-size: 10px !important;"` (and `11px !important;`) on all inner `<span>` and `<a>` elements for Work Mode, Degree, Manager Name, In-House Talent, and Company Name. |

---

## Verification Evidence

1. **PHP Syntax Validation:**
   - `php -l resources/views/service_company/bench-candidates/index.blade.php` -> Passed (0 errors).
   - `php -l resources/views/service_company/ajax_pages/bench-candidate-list-ajax.blade.php` -> Passed (0 errors).
2. **Compiled View Cache:**
   - `php artisan view:clear` -> Compiled views cleared successfully.
3. **Chrome DevTools Computed Value Invariant:**
   - Verified that inner `<span>` elements now compute to `10px` or `11px` instead of leaking `13px` from `.bench-dash *`.
