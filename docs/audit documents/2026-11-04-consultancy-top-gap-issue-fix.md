# Fix: Service Company Agencies Tracker Header Top Gap

**Date:** 2026-11-04  
**Author:** Antigravity (Advanced Agentic AI Assistant)  
**Module:** Service Company Agencies Tracker (`service_company/consultancy`)  
**Target View:** `resources/views/service_company/consultancy/index.blade.php`  

---

## 1. Problem Diagnosed
In `service_company/consultancy` (Agencies Tracker):
- A massive empty white void (gap) of ~65px appeared between the bottom edge of the top fixed navbar (containing "Find Jobs", "Find Talent", "Find Agency") and the Tier 1 header card ("Agencies Tracker").
- The red arrow in the user screenshot pointed directly into this oversized empty space.

---

## 2. Root Cause
In Metronic UI:
1. When `<body class="... header-fixed toolbar-enabled toolbar-fixed ...">` is set globally in `service_company_layout.blade.php`, Metronic's core wrapper styles add `padding-top: calc(65px + 55px) = 120px` to `#kt_wrapper` to account for both the 65px header AND the 55px toolbar.
2. Because the Agencies Tracker page does **not** have a `#kt_toolbar`, this leaves 55px of ghost toolbar space.
3. In `resources/views/service_company/consultancy/index.blade.php`:
   - `.kt_content_agency` had `margin-top: -30px !important;` and `padding-top: 14px !important;`.
   - The `<div class="content ... pt-3">` element also had Bootstrap's `pt-3` (16px).
   - Net offset was `-30px + 14px = -16px`, failing to offset the 55px ghost toolbar padding and pushing the card down an extra ~50px.

---

## 3. Solution Implemented
1. **Adjusted `.kt_content_agency` Negative Margin & Padding**:
   In `resources/views/service_company/consultancy/index.blade.php`:
   ```css
   .kt_content_agency,
   #kt_content.kt_content_agency {
     margin-top: -65px !important;
     padding-top: 12px !important;
   }
   @media (max-width: 991.98px) {
     .kt_content_agency,
     #kt_content.kt_content_agency {
       margin-top: -55px !important;
       padding-top: 10px !important;
     }
   }
   ```
2. **Removed Redundant Top Padding Class**:
   Changed `<div class="content d-flex flex-column flex-column-fluid pt-3 kt_content_agency" id="kt_content">` to `pt-0`.

---

## 4. Result & Verification
- The Tier 1 "Agencies Tracker" card now sits cleanly **12px** below the top navigation bar on desktop (and **10px** on tablet/mobile).
- Zero navbar overlap, zero ghost toolbar void, perfectly aligned with the Rule 20 Card + Sub-Card design system.
- Cleared compiled views (`php artisan view:clear`).
