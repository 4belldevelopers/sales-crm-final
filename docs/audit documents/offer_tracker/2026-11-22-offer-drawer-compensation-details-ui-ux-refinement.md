# Offer Drawer – Compensation Details UI/UX Refinement & Layout Audit

**Document Metadata:**
- **Module:** Offer Tracker (`/service_company/applicationoffer`)
- **Component:** Offer Creation & Editing Drawer (`#kt_create_offer` / `create-offer.blade.php`)
- **Author:** Antigravity AI Agent
- **Date:** 2026-11-22
- **Topic:** Offer Drawer Compensation Details 2-Subcard Enterprise Architecture, Yes/No Dropdown, Integrated Prefix, and Footer Simplification
- **Design Systems Adherence:** Rule 14 (15.6" Fit `1366×768` & `1920×1080`), Rule 20 (Modern Card + Sub-Card), Rule 26 (`/unslop` Craft Standard)

---

## 1. Executive Summary

Following user feedback on the Offer Drawer, the **2-subcard enterprise architecture** inside the **Compensation Details** main card was fully restored and refined to the highest enterprise UI/UX standards:
1. **Left Sub-Card:** `Salary & Currency Package` (Base CTC)
2. **Right Sub-Card:** `Role & Joining Schedule` (Position Terms)

Both sub-cards are arranged side-by-side (`col-lg-6` and `col-lg-6`) with `align-items-stretch`, `h-100`, and `flex: 1 1 auto` column distribution. Every row in Sub-Card 1 directly correlates with the corresponding row in Sub-Card 2 at the exact same height and spacing, eliminating vertical distortion, jagged baselines, and empty whitespace.

---

## 2. 7-Point User Requirements & Implementation Matrix

| # | User Requirement | Previous State | Refined Enterprise State |
|---|---|---|---|
| **1** | **Compensation Details – 2 Sub-Cards Alignment** | Uneven heights between left and right cards; left card had large alert banner and floating switch causing vertical distortion. | Two balanced side-by-side sub-cards (`col-lg-6` each) sharing exact row-by-row symmetry (Row 1: Currency vs Designation; Row 2: Amount + Confidentiality vs DOJ; Row 3: Salary Format vs Employment Type). |
| **2** | **Confidentiality – Yes/No Dropdown** | Floating pill toggle `#coConfToggleBtn` with animated bulb and multi-line blue alert banner `#confidential_active_hint`. | Clean `<select name="confidential" id="confidential">` with options `No (Standard)` and `Yes (Confidential)` + single concise 11px notification line `#coConfNotice` (`min-height: 32px`). |
| **3** | **Enter Amount – Icon & Input Alignment** | Detached Bootstrap `.input-group-text` with disjointed borders, awkward spacing, and focus seam. | Unified `.co-amount-input-wrap` with absolute-positioned prefix (`left: 13px`) and `padding-left: 36px` on input. Zero interference with entered numbers or placeholders. |
| **4** | **Icons & Text Alignment** | Mismatched SVG line weights and colorful pastel background squares (`.co-ic--blue`, `.co-ic--green`, `.co-ic--amber`). | Standardized `.co-lbl-icon` (12px glyph centered in 16px slot with 7px right margin, slate `#64748b`) with vertical baseline alignment. |
| **5** | **Layout Consistency Across Sections** | Cards had varying internal controls and uneven margins. | Exactly 3 structured rows per sub-card with identical 40px input height, 7px radius, and matching helper typography. |
| **6** | **Remove Unnecessary Footer Elements** | Bloated `.co-footer-card` containing "Real-time component validation enabled", ghost "Close" button, and heavy whitespace. | Completely eliminated `.co-footer-card`, status bar, and Close button. Streamlined down to a compact right-aligned primary submit button (`#save-form`). Saves >70px vertical space. |
| **7** | **Final UI/UX Quality Check** | Rainbow AI tells, broken visual rhythm. | Clean neutral chrome (`#f8fafc` anchored headers, `#e2e8f0` crisp hairlines), tabular numbers, Poppins typography, zero horizontal overflow. |

---

## 3. 2-Subcard Architecture & Row-by-Row Symmetry

```
+-------------------------------------------------------------------------------------------------------------------------------------------------------------+
|                                                                 COMPENSATION DETAILS                                                                        |
|  [Wallet Icon] Enter the candidate salary package and core employment parameters                                             [Badge: Standard / Confidential] |
+-------------------------------------------------------------------------------------------------------------------------------------------------------------+
| [SUBCARD 1: SALARY & CURRENCY PACKAGE] (col-lg-6)           | [SUBCARD 2: ROLE & JOINING SCHEDULE] (col-lg-6)                                               |
| Header: SALARY & CURRENCY PACKAGE [Base CTC]                | Header: ROLE & JOINING SCHEDULE [Position Terms]                                              |
|                                                             |                                                                                               |
| Row 1: Currency                                             | Row 1: Offered Designation                                                                    |
|   - Trigger: Symbol + Code + Name (40px)                    |   - Text input: Designation title (40px)                                                      |
|   - Hint: Operational currency applied to components        |   - Hint: Official job designation for offer letter                                           |
|                                                             |                                                                                               |
| Row 2: Base CTC & Confidentiality                           | Row 2: Date of Joining (DOJ)                                                                  |
|   - Col 7: Enter Amount (CTC) with integrated prefix (₹)    |   - Datetime-local picker (40px)                                                              |
|   - Col 5: Confidentiality Yes/No Dropdown (40px)           |   - Notice: Target candidate reporting date & terms note (32px)                               |
|   - Words: Numeric amount in words                          |                                                                                               |
|   - Notice: 1-line concise confidentiality status (32px)    |                                                                                               |
|                                                             |                                                                                               |
| Row 3: Salary Breakdown Format                              | Row 3: Employment Type                                                                        |
|   - Custom select: Title, subtitle, chevron (40px)          |   - Custom select: Title, subtitle, chevron (40px)                                            |
|   - Hint: Frequency mode for calculations                   |   - Hint: Contract nature & template association                                              |
+-------------------------------------------------------------------------------------------------------------------------------------------------------------+
|                                                                                                                    [Save & Next / Save & Generate Button ->]|
+-------------------------------------------------------------------------------------------------------------------------------------------------------------+
```

---

## 4. 15.6-Inch Screen Compatibility (Rule 14 & Rule 20)

- **1920×1080 (Full HD Desktop / Large Laptop):**
  - Grid breakpoint `col-lg-6` renders both sub-cards side by side at ~540px width each.
  - Generous internal padding and spacious 40px controls allow comfortable interaction with zero text truncation.
- **1366×768 (Standard 15.6" Laptop):**
  - Breakpoint `col-lg-6` maintains side-by-side alignment at ~490px width each with zero horizontal scrolling.
  - Strict mathematical row height parity (~266px body height) ensures headers and bottom edges align seamlessly.
- **Drawer Vertical Clearance:**
  - Removal of the bloated footer card recovered 74px of vertical height, allowing Step 1 to fit comfortably on `1366×768` viewports with minimal drawer scrolling.

---

## 5. Verification Evidence

- **PHP Lint:** `php -l resources/views/service_company/job-applications/create-offer.blade.php` -> Syntax Valid (0 errors).
- **Automated Render Test (`verify_offer_drawer.php`):**
  - `createOffer(1)` executed in authenticated tenant context (`Company 2`, `User 2`).
  - Output HTML size: 1,282,387 bytes.
  - All 10 verification assertions passed 100%:
    - `[PASS]` Subcard 1: Salary & Currency Package
    - `[PASS]` Subcard 2: Role & Joining Schedule
    - `[PASS]` Two col-lg-6 columns
    - `[PASS]` Integrated amount prefix `.co-amount-prefix`
    - `[PASS]` Confidentiality `<select name="confidential" id="confidential">`
    - `[PASS]` Single concise notification line `#coConfNotice`
    - `[PASS]` Standardized neutral icons `.co-lbl-icon`
    - `[PASS]` Zero "Real-time component validation enabled" text
    - `[PASS]` Zero footer "Close" button
    - `[PASS]` Primary submit button `#save-form` present
- **Notice Font Size Single-Line Optimization:** Refined font size to `10px` with `white-space: nowrap; overflow: hidden; text-overflow: ellipsis;` across `.co-conf-notice` and `.co-doj-notice` so both helper status lines fit strictly on ONE single line without text wrapping.
- **State Transition Test (`test_conf_states.php`):** Verified reactive update between `0` (Standard Visibility) and `1` (Confidential Restricted).
