# Offer Tracker Candidate Card Layout Redesign Audit & Specification

> **Module:** Service Company &bull; Offer Tracker (`applicationoffer`)  
> **Date:** 2026-11-21  
> **Topic:** Candidate Card Layout Redesign (Combined Compensation & Status, Ownership & Stage Side-by-Side in Card Body)  
> **Type:** UI/UX Redesign & Layout Alignment  
> **Author:** Antigravity AI  
> **Standard:** Rule 14 (15.6" Screen Fit & Responsiveness), Rule 20 (Card + Sub-Card Enterprise Design System), Rule 21 (Mandatory Audit Documentation)

---

## 1. Executive Summary

This update redesigns the candidate offer card layout on the Offer Tracker page (`resources/views/service_company/ajax_pages/offer-tracker.blade.php` and `resources/views/service_company/applicationoffer/index.blade.php`) per direct user specifications and reference designs (`media_1795278103836_8212f075.png` and `media_1795278134606_d51dbfc6.png`):

1. **Combined Compensation & Offer Status:**
   - Grouped Compensation amount and Offer Status together into a single, compact, modern sub-card (`ot-subcard-comp-status`).
   - Row 1 displays the salary compensation amount (`INR 1,301,279/A`) and the quick `Change` pill (or `Create Offer` CTA / client lock indicator).
   - Row 2 displays the lifecycle Offer Status badge (`Offer Created`, `Offer Released`, `Offer Accepted`) with contextual icon, background/border tints, and descriptive sub-status text (`Compensation saved`, etc.).
   - Header clearly displays `Compensation & Status` and the status timestamp/date (e.g. `17 Nov 2026`).

2. **Ownership & Stage Side-by-Side Placement in Card Body:**
   - Moved **Ownership & Recruiter** and **Pipeline & Stage** out of the bottom footer strip and placed them directly inside the main card body in the same horizontal row.
   - **Ownership & Recruiter Sub-Card:** Fully replicates reference design Image 2:
     - Header: `Ownership & Recruiter` with user-tie icon.
     - Row 1: Recruiter name with blue circular avatar icon (`fa-user-circle text-primary`).
     - Row 2: Submitting/Post company name with grey building icon (`fa-building`).
     - Row 3: Relative submission age (`Submitted: X days ago` / `Today`) with calendar icon (`fa-calendar-alt`).
   - **Pipeline & Stage Sub-Card:** Fully replicates reference design Image 3:
     - Header: `Pipeline & Stage`.
     - Row 1: Stage pill with soft semantic background and border (e.g. Amber `#fffbeb` for `Offered`, Blue `#eff6ff` for `InReview`, Emerald `#ecfdf5` for `Accepted`/`Joined`).
     - Row 2: Sub-stage info text (`Sub: Private` or `Sub: Standard`).
     - Row 3: `Update Stage >` interactive prompt.
     - Entire sub-card is clickable via `.stage_sub_stage` class and data attributes (`data-row-id`), instantly opening the candidate stage drawer.

3. **Footer Cleanup & Zero Overflow on 15.6" Screens:**
   - The bottom strip now cleanly acts as a dedicated reference & action bar containing `#OFF-xxxx`, `#APP-xxxx`, status date, and primary actions (`Manage Offer` / `Create Offer` and `Candidate Profile`).
   - The grid definition in CSS is configured with 6 fractional columns (`2fr 1.6fr 1.6fr 1.15fr 1.35fr 1.25fr`) with `min-width: 0 !important; overflow: hidden !important;`, preventing any horizontal scrollbar on 1366×768 (15.6") and 1920×1080 displays.

---

## 2. Architecture & Data Flow

```
[ Offer Tracker Page (index.blade.php) ]
                  │
                  ▼
[ AJAX Endpoint: service_company/ajax_pages/offer-tracker.blade.php ]
                  │
    ┌─────────────┴──────────────────────────────────────────────────────┐
    │ 6 Discrete Sub-Cards Grid (.ot-entity-grid)                        │
    ├──────────────────┬─────────────────┬─────────────────┬─────────────┤
    │ 1. CANDIDATE     │ 2. JOB & CLIENT │ 3. COMP & STATUS│ 4. JOINING  │
    │  - Name & Title  │  - Job Title    │  - Salary Row   │  - Date/Time│
    │  - Exp & City    │  - Client Code  │  - Status Badge │  - Countdown│
    ├──────────────────┴─────────────────┴─────────────────┴─────────────┤
    │ 5. OWNERSHIP & RECRUITER           │ 6. PIPELINE & STAGE           │
    │  - Recruiter Name (fa-user-circle) │  - Master Stage Pill          │
    │  - Company Name (fa-building)      │  - Sub-Stage Label            │
    │  - Submitted X days ago            │  - Clickable Stage Drawer CTA │
    └────────────────────────────────────┴───────────────────────────────┘
                  │
                  ▼
    ┌────────────────────────────────────────────────────────────────────┐
    │ Bottom Metadata & Quick Actions Strip (.ot-entity-bottom)          │
    │  - Left: Offer Ref (#OFF-xxxx), Candidate ID (#APP-xxxx)           │
    │  - Right: [Manage / Create Offer] [Candidate Profile ->]           │
    └────────────────────────────────────────────────────────────────────┘
```

---

## 3. File Changes Matrix

| File Path | Action | Description |
|-----------|--------|-------------|
| `resources/views/service_company/ajax_pages/offer-tracker.blade.php` | Modify | 1. Combined Compensation and Offer Status into subcard 3 (`ot-subcard-comp-status`).<br>2. Implemented Stage color mapping & `Carbon` relative submission time (`$submittedAgoText`).<br>3. Placed Ownership (`ot-subcard-owner`) and Stage (`ot-subcard-stage`) side-by-side in main grid.<br>4. Cleaned up bottom strip by removing moved items and keeping reference identifiers + primary actions. |
| `resources/views/service_company/applicationoffer/index.blade.php` | Modify | 1. Updated `.ot-entity-grid` to 6 columns: `2fr 1.6fr 1.6fr 1.15fr 1.35fr 1.25fr`.<br>2. Added CSS rules for `.ot-subcard-comp-status`, `.ot-comp-row`, `.ot-comp-amount-wrap`, `.ot-comp-amount`, `.ot-comp-edit-pill`, `.ot-status-row`, `.ot-offer-badge`.<br>3. Added CSS rules for `.ot-subcard-owner`, `.ot-owner-body`, `.ot-owner-name`, `.ot-owner-company`, `.ot-owner-submitted`.<br>4. Added CSS rules for `.ot-subcard-stage`, `.ot-stage-body`, `.ot-stage-pill`, `.ot-substage-txt`, `.ot-stage-cue`.<br>5. Added `.ot-micro-date` styling for clean subcard header timestamps.<br>6. Updated 1440px, 1200px, 768px, and 520px responsive media queries. |

---

## 4. 15.6" Screen Fit & Responsiveness (Rule 14 & Rule 20)

- **1366×768 (15.6" Laptop / Standard):**
  - Grid columns use `minmax(0, x.xfr)` with `min-width: 0` and `overflow: hidden`, preventing cell expansion.
  - Sub-card padding is set to 8px 10px, typography is proportional (10px–12.5px), and text truncation (`text-overflow: ellipsis`) is applied to prevent horizontal overflowing.
  - Zero horizontal page scrolling.
- **1920×1080 (15.6" / 24" / 27" Full HD):**
  - Generous spacing, balanced cards, elegant horizontal row scanning.
- **<= 1200px (Tablets / Split View):**
  - Gracefully wraps to 3 columns per row with equal-height subcards.
- **<= 768px (Mobile):**
  - Wraps to 2 columns and 1 column respectively.

---

## 5. Verification Evidence

1. **PHP Syntax Validation:**
   ```bash
   php -l resources/views/service_company/ajax_pages/offer-tracker.blade.php
   # Result: No syntax errors detected
   php -l resources/views/service_company/applicationoffer/index.blade.php
   # Result: No syntax errors detected
   ```
2. **AI Guard & Security Checks:**
   - 0 `dd()`, `dump()`, `var_dump()` calls.
   - 0 `env()` calls outside `config/`.
   - Multi-tenant data scoping strictly preserved (`company_id`).
   - Zero business logic mutations (Rule 7).
   - Product Company and Staffing views remain 100% frozen (Rule 18).
