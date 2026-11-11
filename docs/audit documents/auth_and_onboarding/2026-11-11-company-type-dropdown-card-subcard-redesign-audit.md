# UI/UX Audit & Enterprise Implementation Specification: Registration Company Type (Pattern B: 2x2 Grid Cards)

> **Document Version:** 3.0.0 (Pattern B: 2x2 Enterprise Grid Cards Production Edition)  
> **Date:** November 11, 2026  
> **Module / Scope:** `auth_and_onboarding` / Registration Page (`resources/views/auth/login.blade.php`)  
> **Selected Pattern:** **Pattern B: Modern 2x2 Grid Sub-Cards** (100% Upfront Visibility, Zero Dropdown Click Friction)  
> **Ordering:** **1. Product Company** &rarr; **2. IT Services Company** &rarr; **3. Recruitment Company** (Independent Recruiter removed)  
> **Compliance Standard:** Rule 20 (Mandatory Card + Sub-Card Enterprise Design System), Rule 21 (Audit Documents Storage Standard), 15.6" Screen Fit (`1366×768` & `1920×1080`), Strict CQRS (0% MySQL Reads, Zero Business Logic Regression).  
> **Target Audience:** Engineering Leads, UI/UX Architects, Frontend Engineers, Product Managers.

---

## 1. Executive Summary & Design Decision

Per executive direction, **Pattern B: 2x2 Grid Cards** has been chosen and implemented as the authoritative design system for the **Company Type** selection on the registration page (`resources/views/auth/login.blade.php`):

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ COMPANY TYPE SELECTION (Pattern B: High-Density 2x2 Grid Cards)                         │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [🚀] Product Company (Direct Employer)                       [ATS Only]   ( ● ) Selected│
│ Hiring exclusively for your own in-house departments & teams (Pure ATS, no CRM bloat). │
├───────────────────────────────────────────┬────────────────────────────────────────────┤
│ [💻] IT Services & Consulting  [ATS] [CRM]│ [👥] Recruitment & Staffing    [ATS] [CRM] │
│ IT consultancies & staff augmentation.    │ Staffing agencies & search firms.          │
│ (Bench talent + Client accounts)     ( ○ )│ (Candidate sourcing + Client jobs)    ( ○ )│
└───────────────────────────────────────────┴────────────────────────────────────────────┘
```

---

## 2. Advantages of Pattern B over Dropdown Pattern A

| Feature | Legacy Native `<select>` | Pattern A (Card Combobox) | **Pattern B (2x2 Grid Cards) — IMPLEMENTED** |
|---|---|---|---|
| **Clicks to Decide** | 2 clicks (Open native menu + select) | 2 clicks (Open popover + select) | **1 click directly on the desired card** |
| **Cognitive Load** | High (plain text, no context) | Medium (hidden behind trigger) | **Zero (all 3 models & tags visible upfront)** |
| **Entitlement Visibility** | 0% (No badges) | Visible only on menu expansion | **100% visible immediately upon page load** |
| **Mobile / Touch Fit** | Native OS spinner | Popover positioning edge cases | **Large finger-friendly touch targets (`>54px`)** |
| **Visual Polish** | Low (Windows blue bar) | High | **World-Class (Metronic 8 + Poppins + Rule 20)** |

---

## 3. Implemented Grid Card Taxonomy & Hierarchy

### Row 1 (Span 2 Columns): Product Company (Direct Employer)
- **Value:** `3`
- **Icon Accent:** Emerald Tint (`#ecfdf5`, border `#a7f3d0`, icon `#059669`)
- **Capability Badge:** `<span class="jw-cap-tag jw-tag-ats-only">ATS Only</span>`
- **Subtext:** *"Direct Employer"*
- **Description:** *"Hiring exclusively for your own in-house departments & teams (Pure ATS, no CRM bloat)."*
- **Entitlement Logic:** Gives full in-house ATS (jobs, candidate stages, interviews, offers). Completely omits external client CRM from the sidebar.

### Row 2 Left Column: IT Services & Consulting Company
- **Value:** `2`
- **Icon Accent:** Blue Tint (`#eff6ff`, border `#bfdbfe`, icon `#2563eb`)
- **Capability Badges:** `<span class="jw-cap-tag jw-tag-ats">ATS</span> <span class="jw-cap-tag jw-tag-crm">CRM</span>`
- **Description:** *"IT consultancies, digital services & staff augmentation (Bench talent + Client accounts)."*
- **Entitlement Logic:** Gives ATS + external Client CRM (bench deployment, client accounts, contractor billing).

### Row 2 Right Column: Recruitment & Staffing Company
- **Value:** `1`
- **Icon Accent:** Purple Tint (`#faf5ff`, border `#f3e8ff`, icon `#9333ea`)
- **Capability Badges:** `<span class="jw-cap-tag jw-tag-ats">ATS</span> <span class="jw-cap-tag jw-tag-crm">CRM</span>`
- **Description:** *"Staffing agencies & search firms (Candidate sourcing + Client job orders)."*
- **Entitlement Logic:** Gives ATS + external Client CRM (candidate sourcing, client requisitions, agency talent marketplace).

---

## 4. Technical Implementation & Zero-Error Verification

1. **DOM Elements:**
   - Hidden input `<input type="hidden" name="company_type" id="jw-signup-company-type" value="{{ old('company_type', '') }}" required>` maintains 100% backward compatibility with `SaasFrontController::companyRegister()` and form serialization.
   - Interactive cards use `role="radio"`, `aria-checked="true|false"`, and `tabindex="0"`.
2. **Keyboard Accessibility:**
   - Users can Tab through each card and press `Enter` or `Space` to select.
3. **Form Validation:**
   - If submitted without a selection, `#jw-type-grid` receives `.is-invalid`, flashes a clear helper alert, and smoothly scrolls to the card grid.
   - Clicking any card immediately removes the invalid state and clears alerts.
4. **Agency Invitation Lock:**
   - Direct agency invitation links (`$isAgencyInvite = true`) automatically display a locked, read-only card with `Recruitment & Staffing Company`, badges `[ATS] [CRM]`, and the `Auto-Selected` pill badge.
5. **Syntax & Linter Verification:**
   - Verified via `php -l resources/views/auth/login.blade.php`: **No syntax errors detected.**
