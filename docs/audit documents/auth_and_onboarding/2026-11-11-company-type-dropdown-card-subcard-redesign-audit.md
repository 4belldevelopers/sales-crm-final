# UI/UX Audit & Enterprise Implementation Specification: Registration Company Type (Pattern A: Card Combobox)

> **Document Version:** 4.0.0 (Pattern A: Enterprise Card Combobox Authoritative Standard)  
> **Date:** November 11, 2026  
> **Module / Scope:** `auth_and_onboarding` / Registration Page (`resources/views/auth/login.blade.php`)  
> **Selected Pattern:** **Pattern A: Enterprise Card Combobox** (Compact 54px Trigger + Floating Card Popover)  
> **Ordering:** **1. Product Company** &rarr; **2. IT Services Company** &rarr; **3. Recruitment Company** (Independent Recruiter removed)  
> **Compliance Standard:** Rule 20 (Mandatory Card + Sub-Card Enterprise Design System), Rule 21 (Audit Documents Storage Standard), 15.6" Screen Fit (`1366×768` & `1920×1080`), Strict CQRS (0% MySQL Reads, Zero Business Logic Regression).  
> **Target Audience:** Engineering Leads, UI/UX Architects, Frontend Engineers, Product Managers.

---

## 1. Executive Summary & Design Decision

Following browser testing of Pattern B (2x2 Grid Cards), it was determined that rendering multi-column cards inside the compact registration form container (`max-width: 520px`) caused horizontal overflow on the right margin and pushed the password input below the fold on standard 15.6" displays.

Per user instruction, **Pattern A: Card Combobox** has been deployed as the authoritative enterprise design standard for the **Company Type** selection on the registration page (`resources/views/auth/login.blade.php`):

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│ CLOSED STATE (54px Standard Input Height - 100% Width Containment)                     │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [🚀] Product Company (Direct Employer)                       [ATS ONLY]   [ ▾ Chevron ]│
│      Hiring exclusively for in-house teams & departments                               │
└────────────────────────────────────────────────────────────────────────────────────────┘

┌────────────────────────────────────────────────────────────────────────────────────────┐
│ OPEN STATE (Floating Card Popover - z-index 1050)                                      │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ BUSINESS MODEL & CAPABILITIES                                    [ 3 ENTERPRISE PROFILES]│
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [🚀] 1. Product Company (Direct Employer)                    [ATS ONLY]       ( ✓ )    │
│      Direct employers, SaaS, startups & corporate enterprises hiring in-house.         │
│      ✓ Pure ATS Pipeline: Requisitions, stages & interviews (No CRM bloat).            │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [💻] 2. IT Services & Consulting Company                     [ATS] [CRM]      (   )    │
│      IT consulting, software services, project delivery & staff augmentation.          │
│      ✓ Included: Bench talent deployment, client accounts & contractor billing.        │
├────────────────────────────────────────────────────────────────────────────────────────┤
│ [👥] 3. Recruitment & Staffing Company                      [ATS] [CRM]      (   )    │
│      Sourcing, vetting & delivering candidates to external client employers.           │
│      ✓ Included: Candidate sourcing, resume parsing, pipeline & client accounts.       │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 2. Why Pattern A Resolves Real-World Production Issues

| Evaluation Metric | Pattern B: 2x2 Grid Cards (Deprecated) | **Pattern A: Card Combobox (Authoritative)** |
|---|---|---|
| **Container Fit** | Spilled past right card margin in ~440px form | **100% width containment inside `.jw-field`** |
| **Vertical Space** | Consumed ~160px permanently | **Consumes only 54px when closed (standard input height)** |
| **Viewport Ergonomics** | Pushed Password & Submit below the fold | **Entire signup card fits comfortably on 1366×768 / 1080p** |
| **Visual Hierarchy** | Cluttered dense text alongside radio buttons | **Clean, uncluttered, focused popover card selection** |
| **Responsiveness** | Required brittle multi-breakpoint column switches | **Naturally responsive from 320px mobile to 4K monitors** |

---

## 3. Implemented Taxonomy & Hierarchy (Exact Requested Order)

### 1st: Product Company (Direct Employer)
- **Database Value:** `3`
- **Icon:** Emerald Layer Polygon (`#ecfdf5`, border `#a7f3d0`, text `#059669`)
- **Capability Badge:** `<span class="jw-cap-tag jw-tag-ats-only">ATS Only</span>`
- **Subtitle:** *"Hiring exclusively for in-house teams & departments"*
- **Included Capabilities:** *"✓ Pure ATS Pipeline: Requisitions, stages & interviews (No CRM bloat)."*
- **Entitlement Scope:** In-house ATS enabled. External Client CRM omitted from sidebar.

### 2nd: IT Services & Consulting Company
- **Database Value:** `2`
- **Icon:** Blue Monitor (`#eff6ff`, border `#bfdbfe`, text `#2563eb`)
- **Capability Badges:** `<span class="jw-cap-tag jw-tag-ats">ATS</span> <span class="jw-cap-tag jw-tag-crm">CRM</span>`
- **Subtitle:** *"Manage client accounts, requisitions & deploy consultants"*
- **Included Capabilities:** *"✓ Included: Bench talent deployment, client accounts & contractor billing."*
- **Entitlement Scope:** Full ATS + external Client CRM enabled.

### 3rd: Recruitment & Staffing Company
- **Database Value:** `1`
- **Icon:** Purple Users (`#faf5ff`, border `#f3e8ff`, text `#9333ea`)
- **Capability Badges:** `<span class="jw-cap-tag jw-tag-ats">ATS</span> <span class="jw-cap-tag jw-tag-crm">CRM</span>`
- **Subtitle:** *"Agency talent placement, candidate sourcing & client billing"*
- **Included Capabilities:** *"✓ Included: Candidate sourcing, resume parsing, pipeline & client accounts."*
- **Entitlement Scope:** Full ATS + external Client CRM enabled.

*Note: Option 4 ("Freelancer / Individual Recruiter") has been completely removed.*

---

## 4. Technical Architecture & Invariants

1. **Hidden Payload Input:**
   - `<input type="hidden" name="company_type" id="jw-signup-company-type" value="{{ old('company_type', '') }}" required>`
   - Dispatches native `change` event on update, guaranteeing 100% compatibility with Laravel backend validation (`in:1,2,3,4`) and form serialization.
2. **Accessible Keyboard & Dismissal Handlers:**
   - `Enter` / `Space` toggles the combobox menu.
   - `Escape` dismisses the floating popover.
   - Document-level outside click listener dismisses the popover when clicking anywhere else on the screen.
3. **Form Validation & Auto-Scroll:**
   - On empty form submit, `#jw-combobox-trigger` receives `.is-invalid` (crimson border + shadow), alerts the user via `#jw_signup_alert`, and smoothly scrolls the trigger into view.
   - Selecting any option immediately clears the invalid state and hides the alert.
4. **Agency Invitation Lock:**
   - Direct agency invitation links (`$isAgencyInvite = true`) render an auto-selected, locked, read-only trigger card for `Recruitment & Staffing Company` with badges `[ATS] [CRM]` and the `Auto-Selected` tag.
5. **Code Freeze Compliance:**
   - Zero changes made to `product_company/` or `staffing_company/` views (Rule 18).
   - Zero MySQL reads added (Rule 1 & Rule 2).
