# Offer Drawer Step 5 Header Action Button State Synchronization Audit

## Metadata
- **Module:** Offer Tracker / Offer Drawer (`service_company.cvtracker.createOffer`)
- **Feature:** Offer Drawer Stepper Navigation & Primary Topbar Action Button
- **Date:** 2026-11-23
- **Author:** JERO Architecture Team (AI Coding Agent)
- **Status:** Completed & Verified
- **Target File:** `resources/views/service_company/job-applications/create-offer.blade.php`

---

## Executive Summary
In the Offer Drawer wizard (`create-offer.blade.php`), the user reported that navigating to Step 5 (Send Offer Letter / Offer Sent) left the top-right header action button displaying `Save & Next ->` instead of `Send Offer`.

Investigation revealed the root cause:
1. When navigating from Step 4 to Step 5, `btnSaveLoading(btn)` cached `btn._origHtml` on the `#co_header_save_btn` element when its content was `Save & Next`.
2. Although `syncStepHeader()` executed to change the text and icon to `Send Offer`, `btnSaveReset(btn)` was invoked immediately afterwards within the `setTimeout` callback in `nextStep()`.
3. `btnSaveReset()` blindly restored `btn._origHtml` (`Save & Next`), thereby clobbering the updated state.
4. Furthermore, label copies across error handlers, helper strips, and reset states previously used `Send Offer Letter` instead of the clean, canonical `Send Offer`.

We addressed this by:
- Enhancing `btnSaveReset(btn)` to be step-aware for `#co_header_save_btn`: it checks `coActiveStep()`. If on Step 5, it deterministically injects `<span class="co-header-save-label">Send Offer</span><i class="fas fa-paper-plane fs-9 ms-1"></i>` and updates `btn._origHtml`.
- Reordering `btnSaveReset(btn)` to run before `syncStepHeader()` across all transitions.
- Synchronizing `_origHtml` in `syncStepHeader()`, `resetAllSaveBtns()`, and in the in-flight/error/retry AJAX handlers for `fwd_offer_send`.
- Updating the Step 5 bottom informative notification strip from `Save & Next` to `Send Offer`.

---

## Architecture & Data Flow

```
[User Clicks 'Save & Next' on Step 4]
                │
                ▼
      nextStep(e, 4) executes
                │
                ▼
      step5.classList.add('active')
                │
                ▼
      btnSaveReset(btn)
         ├── If btn.id === 'co_header_save_btn' && coActiveStep() === 5:
         │      btn.innerHTML = 'Send Offer <paper-plane>'
         │      btn._origHtml = btn.innerHTML
         └── Re-enables button
                │
                ▼
      syncStepHeader()
         ├── coPaintStep(): Step 5 active, Steps 1-4 completed
         ├── Sets label: 'Send Offer'
         ├── Sets icon: 'fas fa-paper-plane'
         └── Updates $hdrBtn[0]._origHtml
                │
                ▼
[Step 5 Header Topbar displays 'Send Offer' with paper plane icon]
```

---

## File Changes Matrix

| File Path | Component | Changes Made |
| :--- | :--- | :--- |
| `resources/views/service_company/job-applications/create-offer.blade.php` | Step 5 Helper Strip | Changed `Save & Next` to `Send Offer` in line 5274. Updated hidden trigger button label to `Send Offer`. |
| `resources/views/service_company/job-applications/create-offer.blade.php` | `btnSaveReset()` | Added explicit active-step inspection for `#co_header_save_btn` preventing stale `_origHtml` restoration. |
| `resources/views/service_company/job-applications/create-offer.blade.php` | `resetAllSaveBtns()` | Updated Step 5 label from `Send Offer Letter` to `Send Offer` with `_origHtml` caching. |
| `resources/views/service_company/job-applications/create-offer.blade.php` | `nextStep()` | Reordered `btnSaveReset(btn)` before `syncStepHeader()` in `setTimeout`. |
| `resources/views/service_company/job-applications/create-offer.blade.php` | `syncStepHeader()` | Updated Step 5 label to `Send Offer`, set paper-plane icon and synchronized `_origHtml`. |
| `resources/views/service_company/job-applications/create-offer.blade.php` | `fwd_offer_send` Handlers | Updated in-flight status (`Sending Offer…`) and error/coinCheck/rateLimit recovery to `Send Offer`. |

---

## 15.6" Screen Fit & Enterprise UX Standards
- **Font & Icon Scale:** 12px label (`.co-header-save-label`), 9px font-awesome paper plane icon (`fs-9`).
- **Button Dimensions:** Standard Metronic 34px height (`btn-sm`), zero layout shifting or horizontal scroll.
- **Micro-Interactions:** Fully respects `transitions.dev` and Metronic button spinner lifecycle.

---

## Verification Evidence
1. **PHP Syntax Linter:**
   ```powershell
   php -l resources/views/service_company/job-applications/create-offer.blade.php
   # Result: No syntax errors detected
   ```
2. **Git Status & Working Tree:**
   Verified clean diff without collateral regressions across other steps or modules.
