# Offer Drawer Step 5 – SQL Error Fix & E-Signature UI/UX Refinement

## 1. Metadata
- **Module:** Offer Tracker / Offer Drawer
- **Files Modified:**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php`
  - `resources/views/service_company/job-applications/create-offer.blade.php`
- **Date:** 2026-11-23
- **Author:** JERO Architecture Team & AI Coding Agent
- **Standard Reference:** 
  - [Rule 8: Database Schema Truth (`mysql-schema.sql`)](../../docs/AI_RULES.md)
  - [Rule 14: 15.6" Screen Fit & Enterprise Typography Standard](../../docs/UI_UX_STANDARDS.md)
  - [Rule 20: Modern Card + Sub-Card Enterprise Design System](../../docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md)
  - [Rule 21: Audit Documents Standard Folder Pattern](../../docs/AI_RULES.md)
  - [Rule 26: Zero AI Slop UI/UX Review Standard](../../docs/AI_RULES.md)

---

## 2. Executive Summary
This update resolves two distinct requirements on Step 5 (`Send Offer Letter`) of the Create Offer Drawer:
1. **Critical SQL Error Fix in `fwd_offer_send`:** Fixed a fatal `SQLSTATE[42S22]: Column not found: 1054 Unknown column 'candidate_status' in 'field list'` error that prevented offer letters from being dispatched. The column `candidate_status` does not exist on `add_job_wise_condidates` or `job_applications` (it exists strictly on `offer_create_main_tds` and `updatetimeline`).
2. **E-Signature UI/UX Enterprise Refinement:** Transformed the raw E-Signature checkbox toggle into a modern, interactive **Enterprise Tile Card** (`.co-send-sig-card`) that matches the height (`40px`), padding, and visual hierarchy of the neighboring `Email Template` selector.

---

## 3. Root Cause & Architectural Resolution

### 3.1 Unknown Column `candidate_status` Error
- **Symptom:** When clicking "Send Offer Letter" / "Save & Next" on Step 5, MySQL rejected the query:
  ```sql
  update `add_job_wise_condidates` set `master_status_id` = 5, `candidate_status` = 1, `updated_at` = ... where `id` = 152
  ```
- **Database Truth (Rule 8):** Inspected `database/schema/mysql-schema.sql`.
  - `add_job_wise_condidates` schema contains: `id`, `job_id`, `job_application_id`, `rec_candidate_assign_id`, `status_id`, `master_status_id`, `child_status_id`, `created_at`, `updated_at`, `thirdparty_company_id`, `company_id`, `submitted_by`, `placed_end_date`, `existing`, `skill_match`.
  - `job_applications` schema contains: `id`, `submitted_by`, `company_id`, `master_status_id`, `child_status_id`, `status_id`, etc.
  - Neither table contains a `candidate_status` column. The offer-level candidate status column is correctly stored on `offer_create_main_tds.candidate_status` and `updatetimeline.candidate_status`.
- **Fix:** In `ServiceCompanyCvTrackerController::fwd_offer_send()`, removed `'candidate_status' => 1` from the updates targeting `add_job_wise_condidates` and `job_applications`. Both now correctly update `master_status_id = 5` ("Offer Released").

### 3.2 E-Signature Section Enterprise UI/UX Refinement
- **Tile Architecture:** Replaced the unstyled inline switch with an enterprise `.co-send-sig-card`:
  - **Height & Spacing:** Standardized to `min-height: 40px`, perfectly aligning with the adjacent `Email Template` dropdown.
  - **Visual Hierarchy:** Features a soft circular SVG signature-pen badge (`.co-send-sig-icon`), bold title (`#co_send_sigtext`), semantic state badge (`Required` in primary tint vs `Optional` in neutral gray), and descriptive hint.
  - **Interactive State:** Clicking anywhere on the tile toggles the switch. The border and background smoothly transition between active soft blue (`#bfdbfe` / `#f0f7ff`) and neutral light slate (`#e2e8f0` / `#f8fafc`).

---

## 4. File Changes Matrix

| File Path | Section | Changes Implemented |
| :--- | :--- | :--- |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` | `fwd_offer_send()` (lines 8507–8519) | Removed `'candidate_status' => 1` from `add_job_wise_condidates` and `job_applications` updates, eliminating the fatal MySQL 1054 column error. |
| `resources/views/service_company/job-applications/create-offer.blade.php` | CSS (lines 2275–2285) | Added hover and state transition styling for `.co-send-sig-card`. |
| `resources/views/service_company/job-applications/create-offer.blade.php` | HTML (lines 5175–5205) | Refactored E-Signature section into an enterprise tile card matching the 40px height of the template selector with icon badge and dynamic status pill. |
| `resources/views/service_company/job-applications/create-offer.blade.php` | JS (lines 8003–8035) | Added full UI reactivity on checkbox toggle (border/background swap, text/badge update) and card click delegator. |

---

## 5. Verification Evidence

1. **PHP Syntax Verification:**
   ```powershell
   php -l resources/views/service_company/job-applications/create-offer.blade.php
   # Output: No syntax errors detected in resources/views/service_company/job-applications/create-offer.blade.php

   php -l app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php
   # Output: No syntax errors detected in app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php
   ```
2. **Database Invariants (Rule 8):**
   - Verified that `offer_create_main_tds` updates `candidate_status = 1`.
   - Verified that `updatetimeline` updates `candidate_status = 1`.
   - Verified that `add_job_wise_condidates` and `job_applications` update only `master_status_id = 5` and `updated_at`.
3. **15.6" Screen Fit:**
   - Both columns in Row 4 have identical 40px height, eliminating vertical imbalance and overflow.
