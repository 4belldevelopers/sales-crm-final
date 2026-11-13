# Locked Company Fields Editing Restricted Warning Architecture & Audit

- **Date:** 2026-11-13
- **Author:** JERO Architecture Team
- **Component:** Company Profile, `CompanyProfileRestrictionService`, `tab-basic-info.blade.php`, `ServiceCompanyAccountSettingsController`
- **Scope:** Interactive warning on attempted edits of locked company fields, non-stacking SweetAlert guards, keyboard/mouse event capture, and authoritative backend security.

---

## 1. Executive Summary

When a company profile's restricted fields (Company Website, Company Name, Industry, Company Type) are locked—either due to registration age reaching $\ge 7$ complete days or Super Admin account verification (`accountVerify == 1`)—any user attempt to interact with, focus, click, type into, select, or paste text into these fields must be prevented and immediately trigger a standardized warning popup:

- **Title:** `Editing Restricted`
- **Message:** `"You can't edit these company details. Please contact your Administrator for assistance."`
- **Icon:** `warning`
- **Button:** `OK`

This implementation enforces seamless client-side interaction prevention with zero duplicate popup stacking, preserves existing UI design and Locked badges, and maintains 100% authoritative backend rejection against direct API or modified AJAX requests.

---

## 2. Interaction Matrix & Interception Architecture

### 2.1 The Four Restricted Fields

| Field Name | Target HTML Elements | Intercepted Event Triggers | Prevention Behavior |
|---|---|---|---|
| **Company Website** | `#website`, `.god-website-input-box`, `#client_auto_fetch`, `label[for="website"]` | `click`, `mousedown`, `focus`, `focusin`, `keydown`, `keypress`, `keyup`, `paste`, `input`, `beforeinput` | Event cancelled in capture phase, input blurred, `showLockedCompanyFieldWarning()` displayed. |
| **Company Name** | `#client_id`, `.god-input-box`, `label[for="client_id"]` | `click`, `mousedown`, `focus`, `focusin`, `keydown`, `keypress`, `keyup`, `paste`, `input`, `beforeinput` | Event cancelled in capture phase, input blurred, `showLockedCompanyFieldWarning()` displayed. |
| **Industry** | `#industry_display`, `#jero_ind_select_wrap`, `#jero_ind_input_box`, `label[for="industry_display"]` | `click`, `mousedown`, `focus`, `focusin`, `keydown`, `paste`, `change` | Combobox wrapper freed from `pointer-events: none` to detect interaction; dropdown list withheld from rendering; warning popup displayed. |
| **Company Type** | `#company_type_display`, `#jero_ctype_select_wrap`, `#jero_ctype_input_box`, `label[for="company_type_display"]` | `click`, `mousedown`, `focus`, `focusin`, `keydown`, `paste`, `change` | Combobox wrapper freed from `pointer-events: none` to detect interaction; dropdown list withheld from rendering; warning popup displayed. |

### 2.2 Anti-Stacking Single Popup Guard

To satisfy requirement 2 (*"Do not show repeated warning popups unnecessarily while one is already open"*):
1. A state guard `isWarningOpen = false` is tracked within the closure.
2. If `isWarningOpen` is `true` OR `Swal.isVisible()` returns `true`, any incoming `click`, `focus`, or `keydown` event is immediately discarded.
3. Upon closing the modal via the **OK** button, a 250ms debounce window prevents accidental rapid re-triggering from mouse release.

---

## 3. Backend Security & Authoritative Enforcement

Frontend restrictions are backed by authoritative server-side validation in `CompanyProfileRestrictionService`:
1. `validateModificationAttempt($company, $request->all())` compares the persistent company database record against incoming payload.
2. If any of the four restricted fields (`website`, `company_name`/`name`, `industry`/`industries`, `company_type`/`industries_type`) differ while locked, the request is rejected with HTTP 422:
   ```json
   {
     "status": "fail",
     "message": "Your company profile has been verified. These details are permanently locked.",
     "error_name": "company_profile_locked"
   }
   ```
3. Unrelated company profile fields (e.g. phone, address, tax_id, description, social links, logo) continue to update normally without disruption.

---

## 4. File Changes Matrix

| File | Change Description |
|---|---|
| `app/Services/CompanyProfileRestrictionService.php` | Added `MSG_EDITING_RESTRICTED` and `WARNING_TITLE` constants; exposed `warning_title` and `warning_message` in `getRestrictionStatus()` payload. |
| `resources/views/service_company/account/components/tab-basic-info.blade.php` | Added `is-locked-company-field` and `data-company-field-locked="true"` to inputs, boxes, wrappers, labels, and fetch button; removed `pointer-events: none` to enable click interception; added capture-phase event interceptors and SweetAlert popup. |

---

## 5. Verification Evidence

- `scratch/test_locked_company_fields_warning_popup.php`: **15 passed, 0 failed**
  - Verified `WARNING_TITLE` and `MSG_EDITING_RESTRICTED` constants.
  - Verified DOM presence of `is-locked-company-field` and `data-company-field-locked`.
  - Verified wrapper clickability without `pointer-events: none`.
  - Verified SweetAlert warning configuration, icon, title, text, and `OK` button.
  - Verified anti-stacking guard (`isWarningOpen` + `Swal.isVisible()`).
  - Verified that unlocked companies do not render locked attributes or warning scripts.
  - Verified backend rejection on direct modification attempts.
- `scratch/test_company_profile_7day_restriction_comprehensive.php`: **18 passed, 0 failed**
- `scratch/test_controller_http_and_view_rendering.php`: **All integration checks passed**
- `php artisan test --filter AppTrackerCardSubCardRedesignTest`: **13 passed, 344 assertions (0 failures)**
