# UI/UX Audit & Enterprise Implementation Specification: Registration Company Type (Pattern A: Compact Enterprise Combobox)

> **Document Version:** 4.1.0 (High-Density Enterprise Combobox Edition — Zero Scrollbar, 15.6" & Small Viewport Fit)  
> **Date:** November 12, 2026  
> **Module / Scope:** `auth_and_onboarding` / Registration Page (`resources/views/auth/login.blade.php`)  
> **Selected Pattern:** **Pattern A: Compact Enterprise Card Combobox** (46px–52px Trigger + ~180px Floating Card Popover)  
> **Ordering:** **1. Product Company** &rarr; **2. IT Services & Consulting** &rarr; **3. Recruitment & Staffing** (Independent Recruiter removed)  
> **Compliance Standard:** Rule 20 (Mandatory Card + Sub-Card Enterprise Design System), Rule 21 (Audit Documents Storage Standard), 15.6" Screen Fit (`1366×768` & `1920×1080` incl. 125%/150% DPI scale), Strict CQRS (0% MySQL Reads, Zero Business Logic Regression).  
> **Target Audience:** Engineering Leads, UI/UX Architects, Frontend Engineers, Product Managers.

---

## 1. Executive Summary & High-Density Refinement (v4.1.0)

In v4.0.0, each dropdown option used a side-by-side badge column that squeezed descriptions into a narrow left column (~260px), causing text to wrap onto 4–5 lines per option and driving the open menu height to ~375px with a vertical scrollbar on 15.6" displays.

In **v4.1.0**, the option layout was re-architected into a **2-row Enterprise Row Layout** matching top-tier SaaS platforms (Linear / Stripe / Vercel):
- **Left Column:** Vertically centered `30×30` (28×28 compact) semantic icon box.
- **Right Full-Width Body Stack:**
  - **Row 1:** Title on Left + Capability Badges (`[ATS ONLY]` or `[ATS] [CRM]`) + Active Check on Right.
  - **Row 2:** Single-line capability description spanning **100% width below both title and badges**, reclaiming the dead column underneath badges.

This reduces open menu height by **>50%** (from ~375px down to **~175px–185px**), completely eliminates the need for any vertical scrollbar (`overflow: visible; max-height: none;`), and pairs with compact signup card spacing (`@media (max-width: 1366px), (max-height: 880px)`) and automatic `.jw-dropup` viewport-boundary flip so all 3 profiles and their data fit simultaneously on 15.6" and smaller laptop displays.

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ CLOSED STATE (46px on 15.6"/laptop, 52px desktop - 100% Width Containment)             │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [🚀] 1. Product Company (Direct Employer)                     [ATS ONLY]   [ ▾ Chevron]│
│      In-house hiring · Requisitions, interviews & offers                               │
└────────────────────────────────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────────────────────────────┐
│ OPEN STATE (~180px Total Height - Zero Internal Scrollbar - z-index 1050)              │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ BUSINESS MODEL & WORKSPACE ACCESS                                         [ 3 PROFILES]│
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [🚀] 1. Product Company (Direct Employer)                         [ATS ONLY]     ( ✓ ) │
│      In-house hiring · Requisitions, interviews & offers (Pure ATS, no CRM)            │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [💻] 2. IT Services & Consulting                                  [ATS] [CRM]          │
│      Bench talent, client accounts, projects & billing (Full ATS + CRM)                │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [👥] 3. Recruitment & Staffing                                    [ATS] [CRM]          │
│      Agency recruiting, candidate sourcing & job orders (Full ATS + CRM)               │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Geometry & 15.6" / Small-Screen Optimization Matrix

| Metric | v4.0.0 Initial Combobox | **v4.1.0 Compact Enterprise Combobox** |
|---|---|---|
| **Height per Option Card** | ~95–105px (4–5 wrapped lines) | **~45–49px (2 crisp aligned rows)** |
| **Total Dropdown Menu Height** | ~360–375px | **~175–185px (>50% height reduction)** |
| **Internal Vertical Scrollbar** | Required (`overflow-y: auto; max-height: 380px`) | **Eliminated (`overflow: visible; max-height: none`)** |
| **Trigger Height (1366×768 / DPI scaled)** | Fixed 54px | **46px matching `.jw-inp` & Phone input** |
| **Viewport Flip Protection** | Opens downward only | **Auto-flips `.jw-dropup` if bottom room < menu height** |

---

## 3. Implemented Taxonomy & Hierarchy (Exact Requested Order)

### 1st: Product Company (Direct Employer)
- **Database Value:** `3`
- **Icon:** Emerald Layer Polygon (`#ecfdf5`, border `#a7f3d0`, text `#059669`)
- **Capability Badge:** `<span class="jw-cap-tag jw-tag-ats-only">ATS Only</span>`
- **Description:** *"In-house hiring · Requisitions, interviews & offers (Pure ATS, no CRM)"*
- **Entitlement Scope:** In-house ATS enabled. External Client CRM omitted from sidebar.

### 2nd: IT Services & Consulting
- **Database Value:** `2`
- **Icon:** Blue Monitor (`#eff6ff`, border `#bfdbfe`, text `#2563eb`)
- **Capability Badges:** `<span class="jw-cap-tag jw-tag-ats">ATS</span> <span class="jw-cap-tag jw-tag-crm">CRM</span>`
- **Description:** *"Bench talent, client accounts, projects & billing (Full ATS + CRM)"*
- **Entitlement Scope:** Full ATS + external Client CRM enabled.

### 3rd: Recruitment & Staffing
- **Database Value:** `1`
- **Icon:** Purple Users (`#faf5ff`, border `#f3e8ff`, text `#9333ea`)
- **Capability Badges:** `<span class="jw-cap-tag jw-tag-ats">ATS</span> <span class="jw-cap-tag jw-tag-crm">CRM</span>`
- **Description:** *"Agency recruiting, candidate sourcing & job orders (Full ATS + CRM)"*
- **Entitlement Scope:** Full ATS + external Client CRM enabled.

*Option 4 ("Freelancer / Individual Recruiter") remains completely removed.*

---

## 4. Technical Architecture & Invariants

1. **Hidden Payload Input:**
   - `<input type="hidden" name="company_type" id="jw-signup-company-type" value="{{ old('company_type', '') }}" required>`
   - Dispatches native `change` event on update, guaranteeing 100% compatibility with Laravel backend validation (`in:1,2,3,4`) and form serialization.
2. **Accessible Keyboard & Dismissal Handlers:**
   - `Enter` / `Space` toggles the combobox menu.
   - `Escape` dismisses the floating popover.
   - Document-level outside click listener dismisses the popover cleanly.
3. **Form Validation & Auto-Scroll:**
   - On empty form submit, `#jw-combobox-trigger` receives `.is-invalid` (crimson border + shadow), alerts the user via `#jw_signup_alert`, and smoothly scrolls the trigger into focus.
4. **Agency Invitation Lock:**
   - Direct agency invitation links (`$isAgencyInvite = true`) render an auto-selected, locked, read-only trigger card for `Recruitment & Staffing Company` with badges `[ATS] [CRM]` and `Auto-Selected`.
5. **Code Freeze & CQRS Compliance:**
   - Zero changes to `product_company/` or `staffing_company/` views (Rule 18).
   - Zero MySQL reads added (Rule 1 & Rule 2).
