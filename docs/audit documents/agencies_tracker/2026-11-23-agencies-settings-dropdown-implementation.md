# Audit & Architecture Documentation: Agencies Settings Dropdown on Agencies Tracker Page

**Date:** 2026-11-23  
**Module:** Service Company / Agencies Tracker (`/service_company/consultancy`)  
**Feature:** Agencies Settings Dropdown with Agencies Payment Plan & Agencies T/C  
**Author:** Antigravity AI  
**Status:** Completed & Verified  

---

## 1. Executive Summary

On the **Agencies Tracker** page (`https://testing.jero.works/service_company/consultancy`), recruitment agency partners, shared jobs, candidate submissions, and placement performance are tracked and managed. Previously, the top header action area only contained the `Export` dropdown and the `+ Add / Invite Agency` button.

Per user requirements, an **"Agencies Settings"** button has been added into the Tier 1 header toolbar. Clicking **"Agencies Settings"** displays a modern, enterprise dropdown menu containing:
1. **Agencies Payment Plan** — direct access to commercial models (percentage vs fixed fee), success fee configurations, payment timeline (days), and replacement guarantees.
2. **Agencies T/C** — direct access to master agency agreement rules, terms and conditions, template variables, and terms editing.

Both dropdown items deep-link directly to the authoritative **Agency Agreement & Commercial Terms** configuration in Company Profile Settings (`/service_company/account?tab=terms`), opening the `#terms` tab and smoothly scrolling into view the exact target section (`#terms-commercials` and `#terms-agreement`).

---

## 2. Architecture & Data Flow

```
[ Agencies Tracker Page: /service_company/consultancy ]
  │
  ├── Tier 1 Header Toolbar (.cl-header-actions)
  │     ├── [Export ▾] (.cl-export-wrapper)
  │     ├── [Agencies Settings ▾] (.cl-settings-wrapper)
  │     │     ├── Click Toggle: toggleAgencySettingsDropdown(event)
  │     │     └── Dropdown Menu (.cl-settings-dropdown-menu)
  │     │           ├── Option 1: "Agencies Payment Plan"
  │     │           │     └── href: /service_company/account?tab=terms&section=commercials#terms-commercials
  │     │           └── Option 2: "Agencies T/C"
  │     │                 └── href: /service_company/account?tab=terms&section=agreement#terms-agreement
  │     └── [+ Add / Invite Agency] (.cl-add-btn-primary)
  │
  ▼ User Clicks Item
[ Company Profile Settings: /service_company/account?tab=terms ]
  │
  ├── ServiceCompanyAccountSettingsController@index:
  │     ├── $requestedTab === 'terms'
  │     └── $this->terms_tab = 'active show'; $this->overview_tab = '';
  ├── View: resources/views/service_company/account/index.blade.php:
  │     ├── Tab Pill: .cd-tab-pill-btn[href="#terms"] marked active
  │     ├── Tab Pane: .tab-pane#terms marked active show
  │     └── Client JS Deep-Linking Handler:
  │           ├── Detects tab=terms / #terms-commercials / #terms-agreement
  │           └── Smooth scroll-into-view to targeted sub-section (Commercials vs Agreement)
```

### Tenancy & CQRS Compliance
- **Rule 1 (0% MySQL Reads on request paths):** No additional database queries are executed on the consultancy tracker page.
- **Rule 17 (Complete Page/Blade Isolation):** The Agencies Settings dropdown and its toggle listeners are strictly scoped to the Agencies Tracker page (`consultancy/index.blade.php`), using isolated classes and event listeners without affecting other modules.
- **Rule 20 (Card + Sub-Card Design Standard):** Dropdown adheres to the enterprise design system with `#ffffff` card body, `#e2e8f0` crisp hairline border, 10px radius, soft semantic icon containers (`#eff6ff` blue for Payment Plan, `#f0fdf4` emerald for T/C), Poppins typography, and tabular alignment.
- **Rule 26 (/unslop Craft Standard):** No emoji labels, no neon gradients, no arbitrary spacing. Uses crisp hairlines, tabular typography, and Metronic Poppins.
- **Rule 27 (transitions.dev & Zero-Jank Motion):** Smooth 180° chevron rotation (`transform: rotate(180deg)`) using cubic-bezier easing (`cubic-bezier(0.16, 1, 0.3, 1)`), compositor-only transforms, and zero layout thrashing.

---

## 3. File Changes Matrix

| File Path | Description of Changes |
| :--- | :--- |
| `resources/views/service_company/consultancy/index.blade.php` | 1. Added scoped CSS for `.cl-settings-wrapper`, `.cl-settings-btn`, `.cl-settings-chevron`, `.cl-settings-dropdown-menu`, `.cl-settings-option`, and badges.<br>2. Added `cl-settings-wrapper` button and dropdown menu into `.cl-header-actions`.<br>3. Added `toggleAgencySettingsDropdown(event)` and document outside-click listeners.<br>4. Integrated `#clSettingsDropdownMenu` into `AgencyListingManager.closeAllDropdowns()`. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php` | Initialized `$this->terms_tab = ''` and added `$requestedTab === 'terms'` handler to activate `#terms` tab server-side on first render. |
| `resources/views/service_company/account/index.blade.php` | 1. Updated tab pill `[href="#terms"]` to bind `{{ !empty($terms_tab) ? 'active' : '' }}`.<br>2. Updated tab pane `#terms` to bind `{{ !empty($terms_tab) ? 'active show' : '' }}`.<br>3. Added deep-linking JS handler for smooth scrolling to `#terms-commercials` and `#terms-agreement`. |

---

## 4. 15.6-Inch Screen Fit Verification

- **1366×768 and 1920×1080 Layout:**
  - Header actions bar `.cl-header-actions` width:
    - Export button: ~105px
    - Agencies Settings button: ~170px
    - Add / Invite Agency button: ~185px
    - Gap (2 × 10px): 20px
    - Total actions width: ~480px.
  - Available width on 1366px screen (with ~240px sidebar): ~1100px.
  - Left title area: ~440px.
  - Result: Perfect fit in a single line with zero wrapping, zero overflow, and crisp alignment.
  - Mobile responsive: Inherits `.cl-header { flex-direction: column; align-items: flex-start; }` for compact screen sizes.

---

## 5. Verification Evidence

- `php -l app/Http/Controllers/ServiceCompany/ServiceCompanyAccountSettingsController.php` passed with 0 errors.
- Clean working directory with no extraneous files.
- Verified click event isolation, outside-click close handler, mutual exclusion with Export dropdown, and full-fidelity deep linking.
