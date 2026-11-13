# Locked Company Fields Editing Restricted Warning Architecture & Audit

- **Date:** 2026-11-13
- **Author:** JERO Architecture Team
- **Component:** Company Profile, `CompanyProfileRestrictionService`, `tab-basic-info.blade.php`, `ServiceCompanyAccountSettingsController`
- **Scope:** Interactive warning on attempted edits of locked company fields, elevated enterprise modal design, dynamic per-field messaging, non-stacking SweetAlert guards, keyboard/mouse event capture, and authoritative backend security.

---

## 1. Executive Summary

When a company profile's restricted fields (Company Website, Company Name, Industry, Company Type) are locked—either due to registration age reaching $\ge 7$ complete days or Super Admin account verification (`accountVerify == 1`)—any user attempt to interact with, focus, click, type into, select, or paste text into these fields is prevented and immediately triggers an elevated, enterprise-grade warning modal.

### 1.1 Dynamic Per-Field Messaging
Rather than a generic alert, the modal dynamically identifies and states the exact field that was interacted with:
- **Company Website:** `"You can't edit Company Website. Please contact your Administrator for assistance."`
- **Company Name:** `"You can't edit Company Name. Please contact your Administrator for assistance."`
- **Industry:** `"You can't edit Industry. Please contact your Administrator for assistance."`
- **Company Type:** `"You can't edit Company Type. Please contact your Administrator for assistance."`

### 1.2 Elevated Enterprise Modal Design
- Replaced the default SweetAlert yellow exclamation icon `(!)` with a custom, flat enterprise lock badge (soft amber circular badge `#fffbeb`, border `#fde68a`, crisp amber SVG lock `#d97706`).
- Bold title: `Editing Restricted` in `#0f172a`.
- Discrete field pill badge: `<div class="jero-locked-field-badge"><svg lock></svg> <span>{Field Name} Locked</span></div>` with `#fff7ed` background and `#c2410c` text.
- Description paragraph clearly highlighting the restricted field: `<p>You can't edit <strong>{Field Name}</strong>. Please contact your Administrator for assistance.</p>`.
- JERO Metronic theme **OK** confirm button: `#ffc107` background, `#0f172a` text, 8px border radius, subtle amber shadow, smooth hover animation.

---

## 2. Interaction Matrix & Field Resolution

### 2.1 The Four Restricted Fields

| Field Name | Target HTML Elements | `data-locked-field-name` | Prevention & Resolution Behavior |
|---|---|---|---|
| **Company Website** | `#website`, `.god-website-input-box`, `#client_auto_fetch`, `label[for="website"]` | `Company Website` | Intercepted in capture phase; input blurred; modal shows *"You can't edit Company Website. Please contact your Administrator for assistance."* |
| **Company Name** | `#client_id`, `.god-input-box`, `label[for="client_id"]` | `Company Name` | Intercepted in capture phase; input blurred; modal shows *"You can't edit Company Name. Please contact your Administrator for assistance."* |
| **Industry** | `#industry_display`, `#jero_ind_select_wrap`, `#jero_ind_input_box`, `label[for="industry_display"]` | `Industry` | Wrapper freed from `pointer-events: none` to detect clicks; dropdown menu not rendered; modal shows *"You can't edit Industry. Please contact your Administrator for assistance."* |
| **Company Type** | `#company_type_display`, `#jero_ctype_select_wrap`, `#jero_ctype_input_box`, `label[for="company_type_display"]` | `Company Type` | Wrapper freed from `pointer-events: none` to detect clicks; dropdown menu not rendered; modal shows *"You can't edit Company Type. Please contact your Administrator for assistance."* |

### 2.2 Field Resolution Engine (`resolveFieldName`)
The client-side resolver utilizes a 3-tier cascade:
1. Direct element or ancestor search for `[data-locked-field-name]`.
2. Direct attribute inspection (`getAttribute('data-locked-field-name')`).
3. Fallback matching against IDs (`#website`, `#client_id`, `#industry_display`, `#company_type_display`), names (`website`, `name`, `industry`, `company_type`), and `for` attributes on `<label>`.

### 2.3 Anti-Stacking Single Popup Guard
To satisfy requirement 2 (*"Do not show repeated warning popups unnecessarily while one is already open"*):
1. A state guard `isWarningOpen = false` is tracked within the IIFE closure.
2. If `isWarningOpen` is `true` OR `Swal.isVisible()` returns `true`, any incoming `click`, `focus`, or `keydown` event is immediately discarded.
3. Upon closing the modal via the **OK** button, backdrop click, or Escape key, a 200ms debounce window prevents accidental rapid re-triggering from mouse release.

---

## 3. Backend Security & Authoritative Enforcement

Frontend restrictions are backed by authoritative server-side validation in `CompanyProfileRestrictionService`:
1. `CompanyProfileRestrictionService::formatFieldWarningMessage(?string $fieldName)` provides single-source-of-truth dynamic warning messages.
2. `validateModificationAttempt($company, $request->all())` compares the persistent company database record against incoming payload.
3. If any of the four restricted fields (`website`, `company_name`/`name`, `industry`/`industries`, `company_type`/`industries_type`) differ while locked, the request is rejected with HTTP 422:
   ```json
   {
     "status": "fail",
     "message": "Your company profile has been verified. These details are permanently locked.",
     "error_name": "company_profile_locked"
   }
   ```
4. Unrelated company profile fields (e.g. phone, address, tax_id, description, social links, logo) continue to update normally without disruption.

---

## 4. File Changes Matrix

| File | Change Description |
|---|---|
| `app/Services/CompanyProfileRestrictionService.php` | Added `RESTRICTED_FIELD_LABELS` and `formatFieldWarningMessage(?string $fieldName)` method; preserved all 7-day boundary logic. |
| `resources/views/service_company/account/components/tab-basic-info.blade.php` | Added `data-locked-field-name` to all four restricted fields (Website, Name, Industry, Company Type); added elevated modal CSS (`.jero-locked-modal-popup`, `.jero-locked-modal-icon-badge`, `.jero-locked-modal-ok-btn`); added `resolveFieldName` and dynamic modal markup. |
| `scratch/test_locked_company_fields_warning_popup.php` | Full test suite verifying per-field messaging, elevated modal CSS/HTML, field attribute coverage, and single popup guards. |

---

## 5. Verification Evidence

- `scratch/test_locked_company_fields_warning_popup.php`: **20 passed, 0 failed**
  - Verified `WARNING_TITLE` and `MSG_EDITING_RESTRICTED` constants.
  - Verified `formatFieldWarningMessage` for all 4 fields (Company Website, Company Name, Industry, Company Type).
  - Verified DOM presence of `data-locked-field-name` on all 4 fields.
  - Verified wrapper clickability without `pointer-events: none`.
  - Verified elevated modal CSS classes (`jero-locked-modal-popup`, `jero-locked-modal-icon-badge`, `jero-locked-modal-ok-btn`, `jero-locked-field-badge`).
  - Verified dynamic per-field resolution and message formatting in script.
  - Verified anti-stacking guard (`isWarningOpen` + `Swal.isVisible()`).
  - Verified event interceptors (`click`, `mousedown`, `focus`, `keydown`, `paste`, `input`).
  - Verified that unlocked companies do not render locked attributes or warning scripts.
  - Verified backend rejection on direct modification attempts.
- `scratch/test_company_profile_7day_restriction_comprehensive.php`: **18 passed, 0 failed**
- `php artisan test --filter AppTrackerCardSubCardRedesignTest`: **13 passed, 344 assertions (0 failures)**
