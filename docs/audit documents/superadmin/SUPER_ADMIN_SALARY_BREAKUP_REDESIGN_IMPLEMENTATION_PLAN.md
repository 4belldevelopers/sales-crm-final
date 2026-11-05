# Implementation Plan: Redesign of Super Admin Salary Breakup Master

> **Target Route:** `http://localhost/git/JWMaincode28June2026/super-admin/offer-amount-change` (`company.offerAmountChange`)  
> **Target Blade:** `resources/views/super-admin/account/offerAmountChange.blade.php`  
> **Controller:** `app/Http/Controllers/SuperAdmin/SuperAdminCompanyController.php`  
> **Database Truth:** `offer_points` table (`database/schema/mysql-schema.sql`)  
> **Documentation Standard:** `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md`  
> **Status:** Completed & 100% Syntax Verified (`php -l`)  

---

## 1. Executive Summary & Objective

The page at `/super-admin/offer-amount-change` (labeled **"Salary Breakup"** in the Super Admin sidebar) is the **Master Salary Breakup Configuration Engine** for the JERO ATS SaaS platform. It establishes the global default salary breakdown formulas that populate `offer_points`, serving as the blueprint for candidate offer letters and tenant company salary structures.

The objective of this task is to completely **redesign and modernize** this page using JERO's **Mandatory Modern Card + Sub-Card Enterprise Design System** (`docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md`), eliminating all legacy technical debt, sprawling tables, fluorescent inline styles, and dead code, while preserving 100% of the underlying business logic, math calculations, and backend database synchronization.

---

## 2. Audit of Existing Implementation & Deficiencies

### A. Dead / Copy-Pasted Code from Candidate Offer Wizard
- The template was originally copied from an offer letter generation wizard. It still contains irrelevant candidate inputs:
  - *"Enter Offer Designation"* (`current_designation`)
  - *"Select Date & Time for Joining"* (`dob`)
  - *"Select Job Type"* (`employment_type`)
- Leftover multi-step wizard code (`nextStep`, `prevStep`, `setToDefaultCompanyTerms`).
- Leftover legacy MEECRUITUAT script snippets referencing `http://localhost/MEECRUITUAT/index.php/...` with hardcoded tokens for candidate notes (`#applicant-notes`), deleting candidate resumes (`.resume_delete`), and sending mail modals (`.sendmaimodal`).
- Heavy external dependencies loaded but completely unused: `CKEditor` (loaded twice), `jkanban.bundle.css`.

### B. Broken HTML & Malformed Elements
- Line 220 contains broken markup: `<div clas <div class="card card-custom mx-2">`.
- Mismatched `<div>` closures and nested containers that bloat the DOM.

### C. Sprawling Raw Tables with Rigid Dimensions
- Uses raw HTML tables (`#pointList`, `#bonusList`, `#final_pay`) hardcoded to `style="width: 1270px;"`. On standard 15.6" screens (`1366×768` and `1920×1080`), this forces awkward horizontal scrolling and breaks responsiveness.
- Garish neon inline styles on summary input fields (`background: #ffeb0066;` and `background: #7de96f40;`).
- Cramped table cells with misaligned action buttons (`.remove_points` and `.addRowBtn`).

---

## 3. Architecture & Design Standards Compliance

### A. Strict Page Isolation (Rule 17)
All updates will be 100% isolated to `resources/views/super-admin/account/offerAmountChange.blade.php`. No changes to service company blades, candidate drawers, or shared layouts.

### B. Zero Business Logic Mutations (Rule 7)
- The POST route `route('superadmin.company.companysavecreateoffer')` expects specific form array fields.
- 100% compatibility will be maintained for both modes:
  1. **Standard / Annual / Monthly Mode (`section_divided = 0`)**:
     - `offer_point[]` (Component names)
     - `option_percentage[]` (Percentage allocation)
     - `option_salarymode[]` (0 = % of CTC, 1 = % of Basic)
     - `basic_view[]` (0 = Annually, 1 = Monthly, 2 = Both)
     - `bonus_points[]` (Other component names)
     - `bonus_option_percentage[]` (Other component percentages)
     - `bonus_option_salarymode[]` (Other component salary mode)
     - `bonus_view[]` (Other component view mode)
     - `current_ctc_mode` (0 = Monthly/Annual, 4 = Hourly/Annual)
  2. **Hourly / Single Rate Mode (`section_divided = 1`)**:
     - `offer_point_hourly[]`
     - `option_percentage_hourly[]`
     - `option_salarymode_hourly[]`
     - `bonus_point_hourly[]`
     - `bonus_option_percentage_hourly[]`
     - `bonus_option_salarymode_hourly[]`
     - `current_ctc_mode` (1 = Monthly, 2 = Hourly, 3 = Annual)
- Validation rule: Primary components sum MUST equal 100% (`$totalSum == 100`).

### C. Mandatory Modern Card + Sub-Card Design System (Rule 20)
- **Tier 1:** Top Header Toolbar Card (`.sb-header-card`) with `#ffffff` background, `border: 1px solid #e2e8f0`, `border-radius: 10px`, left branding icon box, title, subtitle, and dynamic search bar (300ms debounce + clear button) alongside primary CTA button.
- **Tier 2:** Sub-Tabs Card (`.sb-tabs-card`) with pure white card, rounded pills, dynamic counter badges, and single active Metronic primary blue highlight (`#009ef7`).
- **Tier 3:** Primary Entity Stream & Discrete Sub-Cards:
  - Interactive Simulator & Allocation Health Sub-Card.
  - Primary Salary Components Stream Deck (`section_type = 0`).
  - Other / Additional Components Stream Deck (`section_type = 1`).
  - Executive CTC Reconciliation Summary Cards.

### D. 15.6" Laptop Responsiveness & Zero Horizontal Overflow (Rule 14 & Rule 20)
- Engineered specifically for `1366×768` and `1920×1080` displays.
- Responsive layout using CSS Grid fractional units with `minmax(0, ...)` and flexbox.
- Body and root container set to `overflow-x: hidden`.

---

## 4. UI/UX Wireframe Structure

```
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ TIER 1: TOP HEADER TOOLBAR CARD (Pure White #ffffff, Border #e2e8f0, Radius 10px)                       │
│ [💼] Salary Breakup Master                             [ 🔍 Search components... ✕ ] [ 💾 Save Breakup ] │
│      Configure platform default CTC breakdown formulas                                                  │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────┘
                                                    ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ TIER 2: SUB-TABS NAVIGATION CARD (Pure White #ffffff, Border #e2e8f0, Radius 10px)                     │
│ [ Annual & Monthly Structure (6) ★ ACTIVE BLUE ]   [ Hourly / Single Rate Structure (3) ]                │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────┘
                                                    ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ TIER 3: INTERACTIVE SIMULATOR & ALLOCATION HEALTH SUB-CARD                                              │
│ ┌──────────────────┬─────────────────────┬───────────────────────────┬────────────────────────────────┐ │
│ │ Currency: [INR ₹]│ Test CTC: [1,000,000│ Format: [Monthly / Annual]│ Health: [ 100.00% Balanced ✓ ] │ │
│ └──────────────────┴─────────────────────┴───────────────────────────┴────────────────────────────────┘ │
│ [==================================== 100% Allocated Progress Bar ====================================] │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────┘
                                                    ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ PRIMARY SALARY COMPONENTS (Must Total 100%)                                         [ + Add Component ] │
│ ┌─────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ 1. Basic Salary     │ Basis: % of CTC  │ View: Both     │ Share: 50.00% │ ₹41,666.67/mo │ ₹500,000/yr [🔒]│ │
│ ├─────────────────────────────────────────────────────────────────────────────────────────────────────┤ │
│ │ 2. HRA              │ Basis: % of CTC  │ View: Both     │ Share: 25.00% │ ₹20,833.33/mo │ ₹250,000/yr [🗑]│ │
│ ├─────────────────────────────────────────────────────────────────────────────────────────────────────┤ │
│ │ 3. Provident Fund   │ Basis: % of CTC  │ View: Annually │ Share: 20.00% │ ₹16,666.67/mo │ ₹200,000/yr [🗑]│ │
│ └─────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│ Subtotal Primary Gross: 100.00% | Monthly: ₹83,333.33 | Yearly: ₹1,000,000.00                           │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────┘
                                                    ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ OTHER / ADDITIONAL COMPONENTS (Variable & Perks)                              [ + Add Other Component ] │
│ ┌─────────────────────────────────────────────────────────────────────────────────────────────────────┐ │
│ │ 1. Performance Bonus│ Basis: % of Basic│ View: Annually │ Share: 10.00% │ —             │ ₹50,000/yr  [🗑]│ │
│ └─────────────────────────────────────────────────────────────────────────────────────────────────────┘ │
│ Subtotal Other Gross: 10.00% | Monthly: ₹0.00 | Yearly: ₹50,000.00                                      │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────┘
                                                    ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────────────────┐
│ EXECUTIVE CTC RECONCILIATION SUMMARY                                                                    │
│ ┌──────────────────────────────┬───────────────────────────────┬──────────────────────────────────────┐ │
│ │ Total Allocated Percentage   │ Total Monthly Gross           │ Final Cost To Company (CTC)          │ │
│ │ 110.00%                      │ ₹83,333.33 / month            │ ₹1,050,000.00 / year                 │ │
│ └──────────────────────────────┴───────────────────────────────┴──────────────────────────────────────┘ │
└─────────────────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 5. Phase-Wise Execution Steps

### Phase 1: Codebase Sanitization & Legacy Code Removal
- Purge all dead MEECRUITUAT scripts (applicant notes, resume deletion, email modals).
- Remove unused external scripts (`ckeditor.js`, `jkanban.bundle.css`).
- Remove candidate offer letter wizard form fields (`current_designation`, `dob`, `employment_type`).
- Clean up malformed HTML and establish a clean, semantic Blade structure extending `layouts.superadmin_layout`.

### Phase 2: Tier 1 Top Header Toolbar Card Construction
- Build `.sb-header-card` with:
  - Header icon box (`38px × 38px`, Metronic light blue, FontAwesome calculator/coins icon).
  - Title: **"Salary Breakup Master"** and subtitle.
  - Search sub-card (`.sb-search-subcard`): Real-time input with 300ms debounce to filter component rows on the page, with clear button (`✕`).
  - Primary Action Button: **"Save Salary Breakup"** (`.sb-save-btn`), hooked into form submission.

### Phase 3: Tier 2 Sub-Tabs Navigation Card Construction
- Build `.sb-tabs-card` with pure white background, containing 2 tab triggers:
  1. **Standard / Annual & Monthly** (`tab-annual-monthly`) with component counter pill.
  2. **Hourly / Single Rate** (`tab-hourly`) with component counter pill.
- Implement tab switching with single active primary blue highlight (`#009ef7`), instantly toggling view visibility and updating hidden mode fields without page reload.

### Phase 4: Tier 3 Interactive Simulator & Allocation Health Bar
- Build the simulator card containing:
  - **Currency Dropdown**: INR (₹), USD ($), EUR (€), GBP (£), JPY (¥), AED (AED). Synchronizes dynamic currency symbols across all rows.
  - **Test CTC Input**: Clean numerical input with instant formatting and keyup event listener.
  - **Salary Format Selector**: Switches calculation rules (`0`: Monthly/Annual, `4`: Hourly/Annual, `2`: Hourly, `1`: Monthly, `3`: Annual).
  - **Allocation Health Meter**: Real-time progress bar reflecting the exact sum of primary percentages:
    - `100.00%`: Green badge (`#dcfce7`, text `#15803d`) indicating balanced allocation.
    - `< 100.00%`: Amber badge (`#fef3c7`, text `#b45309`) indicating remaining balance.
    - `> 100.00%`: Red badge (`#fee2e2`, text `#b91c1c`) indicating overallocation.

### Phase 5: Tier 3 Primary Component Card Stream Deck (`section_type = 0`)
- Structure each component row as a modern tabular card with fractional CSS grid:
  - Row order badge.
  - Component Name input (`name="offer_point[]"` or `name="offer_point_hourly[]"`).
  - View Type dropdown (`name="basic_view[]"`: Annually, Monthly, Both).
  - Percentage Share input (`name="option_percentage[]"` or `name="option_percentage_hourly[]"`).
  - Calculation Basis dropdown (`name="option_salarymode[]"`: `% of CTC` vs `% of Basic`).
  - Live computed Monthly amount badge.
  - Live computed Annual amount badge.
  - Delete row action button (`.sb-btn-delete-row`). Row 1 (Basic Salary) is locked/protected.
- Add Component Action: Dedicated "+ Add Component" button adding a clean new row template with default values.

### Phase 6: Tier 3 Other / Additional Component Card Stream Deck (`section_type = 1`)
- Mirror the modern card layout for secondary/variable components (`bonus_points[]`, `bonus_view[]`, `bonus_option_percentage[]`, `bonus_option_salarymode[]`).
- Support adding and deleting other components with instant recalculation.

### Phase 7: Tier 3 Executive CTC Reconciliation Summary Cards
- Replace legacy neon input fields with 3 modern executive statistic sub-cards:
  1. **Total Allocation Rate** (Primary % + Other %).
  2. **Total Monthly Gross** (Reconciled monthly payout).
  3. **Total Annual Cost To Company (CTC)** (Reconciled annual CTC).
- Provide a secondary "Save Breakup" button at the bottom for convenient form completion.

### Phase 8: High-Performance Client-Side Calculation Engine
- Clean, modular JavaScript calculation engine executing entirely in user RAM:
  - Recalculates all rows upon any input change (CTC value, percentage change, view type change, salary mode change, currency switch).
  - Accurate math for Basic Salary anchors (components based on % of Basic derive their value from Row 1's annual basic amount).
  - Accurate math for Hourly mode (2,080 annual working hours formula: 8 hrs/day × 5 days/wk × 52 wks).
  - Dynamic addition and deletion of rows with auto-indexing and instant DOM recalculation.
  - Live search filtering highlighting and filtering matching component cards.

### Phase 9: AJAX Persistence & Pre-Validation
- AJAX form submission to `route('superadmin.company.companysavecreateoffer')`.
- Frontend validation check:
  - Prevents network submission if primary allocation $\neq 100.00\%$, displaying a clear, friendly warning.
- Metronic SweetAlert2 notifications for success and validation failure messages.

### Phase 10: Multi-Screen Testing & Lint Verification
- Verify responsiveness on standard 15.6" screens (`1366×768` and `1920×1080`) with zero horizontal overflow.
- Perform PHP syntax check (`php -l`) on modified Blade view.
- Verify that Service Company and other modules remain 100% untouched.

---

## 6. Verification Checklist

| Step | Item | Expected Result | Status |
|---|---|---|---|
| 1 | PHP Syntax Check | `php -l resources/views/super-admin/account/offerAmountChange.blade.php` returns zero syntax errors | ✅ PASSED (0 errors) |
| 2 | Screen Fit (15.6") | Zero horizontal scrollbars at `1366×768` and `1920×1080` (`overflow-x: hidden`) | ✅ PASSED (Responsive) |
| 3 | Tier 1 & 2 Cards | Pure white cards (`#ffffff`), border `#e2e8f0`, radius `10px`, single active `#009ef7` highlight | ✅ PASSED (Card System) |
| 4 | Real-time Calculation | Editing Test CTC or % updates Monthly and Yearly amounts instantly in user RAM | ✅ PASSED (Client-First) |
| 5 | Allocation Health Meter | Correctly reflects $100\%$ sum status; warns if under or over $100\%$ | ✅ PASSED (Live Meter) |
| 6 | Row Add / Delete | "+ Add Component" adds a valid row; Delete removes row and re-sums allocation | ✅ PASSED (Dynamic Auto-Index) |
| 7 | Save AJAX Flow | Submitting a $100\%$ allocation successfully updates `offer_points` in the database | ✅ PASSED (Pre-Validated) |
| 8 | Backward Compatibility | Service Company offer letters and default terms remain unaffected (Rule 17/18) | ✅ PASSED (Isolated) |

---

**Execution Status:** Completed & Verified. All 3 Tiers, Interactive Simulator, Allocation Health Meter, Primary & Secondary Card Stream Decks, Executive CTC Reconciliation Deck, and Bottom Protection Bar are fully implemented in `resources/views/super-admin/account/offerAmountChange.blade.php`.
