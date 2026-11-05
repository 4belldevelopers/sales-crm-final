# Architecture & Intelligence Standard: God-Level Entity Duplication, Point-of-Entry Validation & Enterprise UI

**Module:** Client Management (`service_company/clients`)  
**Document Type:** Architecture Standard, Audit & System Blueprint  
**Date:** 2026-11-04  
**Author:** DeepMind Coding Agent / JERO Architecture Team  
**Scope:** Universal Blueprint for ATS & Marketplace modules (Clients, Candidates, Jobs, Vendors, Teams)  

---

## 1. Executive Summary

During the development and hardening of the **Internal Client creation workflow** (`service_company/clients/create`), we designed, built, and verified an end-to-end **Intelligence and UX Architecture** combining:
1. **Strict Domain Boundary Isolation:** Direct Client and Internal Client records exist independently without false-positive collision.
2. **Canonicalization & Normalization Engine:** Exact, predictable sanitization for URLs, company names, emails, and phone numbers.
3. **3-Tier Hierarchical Validation Hierarchy:** (Tier 1: Intra-draft/session duplicate, Tier 2: Main Account & Tenant Admin credentials protection, Tier 3: Cross-entity internal database check).
4. **Point-of-Entry / Early-Intervention UX:** Duplicates are detected immediately on input, blur, and drawer addition—never deferred to final submission.
5. **Enterprise-Grade UI/UX Error Presentation:** Table structure integrity via valid `<tr class="client-row-error-notice"><td colspan="N">` error rows, Metronic alert cards, and badge styling with zero layout shifts.
6. **Defensive Drawer State Hydration:** Fail-safe data indexing, event delegation, and phone component initialization.

This document serves as the **Authoritative Reference Blueprint** so that any future module across JERO (such as Candidate Onboarding, Job Creation, Vendor Portals, and Team Management) can replicate this exact intelligence and design pattern.

---

## 2. Core Architectural & Intelligence Principles

### Principle 1: Strict Domain Boundary Isolation (Cross-Domain Coexistence)
- **The Problem:** In multi-domain platforms (e.g. ATS Direct Clients vs Internal Agency Clients, or Public Bench Candidates vs Private Team Profiles), checking duplicates globally across disparate domains breaks legitimate business operations. A company, domain, or contact may legitimately exist in both Direct Client and Internal Client records.
- **The Rule:**
  - **Direct Client records must NEVER block or trigger duplicate errors when creating an Internal Client.**
  - **Internal Client validation queries ONLY Internal Client records and tenant boundaries.**
  - Cross-domain coexistence is explicitly allowed; intra-domain collisions are strictly blocked.

### Principle 2: Authoritative Canonicalization & Normalization Engine
Raw user input must never be compared directly. Both client-side JavaScript and backend PHP services use identical normalization rules:
- **Domain Canonicalization:**
  - Converts URLs (`https://www.google.com/careers/`, `http://Google.com`, `WWW.GOOGLE.COM`) to canonical root domain `google.com`.
  - Strips protocols (`http://`, `https://`), `www.`, path slashes, port numbers, and query strings.
- **Company Name Normalization:**
  - Trims outer whitespace, collapses internal consecutive whitespace (`"  ABC   Tech  "` → `"abc tech"`), and converts to lowercase.
- **Email Normalization:**
  - Trims whitespace, converts to lowercase, and validates syntax.
- **Phone Number Normalization:**
  - Strips all non-digit characters (`+`, `-`, `(`, `)`, spaces).
  - Handles country dialing codes (`+91`, `+1`) consistently across components.

### Principle 3: Hierarchical 3-Tier Duplicate Checking Architecture
Validation must evaluate three distinct tiers in strict order:
1. **Tier 1 — Intra-Draft / Local Session Check (Scenario B):**
   - Detects if the user enters the same email or phone number multiple times within the *current* form draft or contacts list.
   - Immediate feedback: *"This contact is already registered with the current company."*
2. **Tier 2 — Main Account & Tenant Administrator Protection Check:**
   - Prevents tenant administrators or sub-users from creating child entities matching the tenant's own root company email, domain, or root user administrator credentials.
   - Immediate feedback: *"This contact/website is already registered with your Main Account (Tenant Administrator)."*
3. **Tier 3 — Tenant-Scoped Sibling Check (Scenario C):**
   - Queries the database for existing records within the current `company_id` tenant, ignoring the current record ID when editing.
   - Immediate feedback: *"This client / contact is already registered with another internal company."*

### Principle 4: Point-of-Entry / Early-Intervention UX (Zero Late Surprises)
- **Anti-Pattern:** Forcing users through a multi-step wizard only to reject them on final step submit creates severe user frustration.
- **God-Level Pattern:**
  - **Debounced Asynchronous Checking:** As the user types (280–300ms debounce) or on `blur`/`change`, an asynchronous check evaluates the field.
  - **Drawer Isolation:** For entities managed inside slide-over drawers (e.g. Contacts, Addresses), duplicates are caught and flagged **inside the drawer** before the user can click "Add" or "Save Changes".
  - **Action Button Gating:** The "Save" or "Next Step" button is disabled while an active duplicate or invalid error persists, and re-enabled as soon as resolved.
  - **Step-Transition Circuit Breakers:** When clicking "Next", the wizard step validator (`validateWizardStep`) performs a synchronous safety check, blocking step transition if an unresolved duplicate remains.

### Principle 5: Enterprise-Grade UI/UX Error Rendering System
- **HTML Table Structure Integrity:**
  - **Critical Rule:** Never inject a raw `<span>` or `<div>` directly into a `<tbody>` outside a `<td>`. In HTML tables, this breaks table formatting and causes the browser to push unstyled red text into column 1.
  - **Standard Implementation:** Always insert a dedicated error row:
    ```html
    <tr class="client-row-error-notice" style="background:#fff1f2;">
      <td colspan="6" style="padding:6px 20px 10px 20px; border-bottom:1px solid #fecdd3; border-left:3.5px solid #e11d48;">
        <div style="display:inline-flex; align-items:center; gap:8px; background:#ffffff; border:1px solid #fecdd3; padding:6px 14px; border-radius:6px; color:#be123c; font-size:11.5px; font-weight:600;">
          <svg .../><!-- Warning SVG -->
          <span>{errorMessage}</span>
        </div>
      </td>
    </tr>
    ```
- **Alert Banner Design:**
  - Upgraded alerts use soft rose backgrounds (`#fff1f2`), fine borders (`#fecdd3`), prominent left accent borders (`border-left: 4px solid #e11d48`), rounded icon badges, bold titles ("Duplicate Contact Notice"), and high-contrast descriptive text.
- **Input Error Badges (`.cdf-error` & `.client-inline-error`):**
  - Replaced unstyled red text with compact inline badges (`background: #fff1f2; border: 1px solid #fecdd3; color: #be123c; border-radius: 6px; padding: 4px 10px; font-size: 11.5px; font-weight: 600;`) featuring SVG warning icons.

### Principle 6: Drawer State Machine & Event Delegation Hygiene
- **Event Bubbling & Child Icons:**
  - When rendering action buttons (`<button class="client-edit-contact"><i class="la la-pencil"></i></button>`), clicks on the child `<i>` tag can cause `$(this)` in delegated listeners to refer to the `<i>` rather than the `<button>`, losing `data-index`.
  - **Rule:** Apply `style="pointer-events:none;"` to all child icons inside interactive buttons, and bind `data-index` directly on both the button and parent row.
- **Safe Hydration Fallback:**
  - When passing indices to open an edit drawer, validate:
    ```javascript
    var index = $btn.attr('data-index') || $btn.closest('tr').attr('data-contact-index');
    var num = parseInt(index, 10);
    ```
  - For complex custom widgets (such as `x-jero-phone-input`), wrap engine calls (`JeroPhoneBox.setCountry`, `JeroPhoneBox.setPhone`) in `try / catch` blocks and provide direct fallback value assignments (`$('#phone_input').val(...)`).

---

## 3. Detailed Chronology of Implementation in this Chat

1. **Internal Client Duplication Architecture (`InternalClientDuplicateService.php`):**
   - Implemented `canonicalizeDomain()`, `normalizeCompanyName()`, `normalizeEmail()`, `normalizePhone()`.
   - Built isolated scenario handlers (Scenarios 1–6, Scenario B, Scenario C).
   - Built Main Account company records & Tenant Administrator check.
2. **Controller Endpoint (`ServiceCompanyClientController.php`):**
   - Created lightweight AJAX validator `validateInternalClientField(Request $request)` supporting `name`, `website`, `contact`, `phone`, and `all`.
3. **Point-of-Entry Client-Side Validation (`create.blade.php`):**
   - Integrated immediate website validation on blur/paste/debounced input.
   - Integrated immediate drawer email (`checkDrawerEmailDuplicate`) and phone (`checkDrawerPhoneDuplicate`) validation.
   - Gated "Save Contact" button in drawer while duplicates are present.
4. **Resolution of Page 4 Blank Edit Drawer Bug:**
   - Identified root cause: missing `data-index` on `.client-edit-contact` button + delegated click handler falling back to `undefined` + `openContactDrawer(undefined)` resetting form to Add mode.
   - Added `data-index` to edit/delete buttons, added `pointer-events: none` on `<i>` tags, and hardened delegated listener.
5. **Redesign of Inline Validation Text & Tables:**
   - Refactored `showInlineError` and `clearFieldError` for table entities to insert valid `<tr class="client-row-error-notice"><td colspan="6">` rows.
   - Refactored `#client_contacts_duplicate_alert` into an enterprise card with SVG badges.
   - Upgraded `.cdf-error` to enterprise pill badges.
6. **Form Label Updates:**
   - Page 1: `"Company Website"` → `"Company Website (Optional)"`
   - Page 1: `"Tax ID / Business Reg No"` → `"Tax ID / Business Reg No / GST / Service Number (Optional)"`
   - Page 5: `"Social Links"` → `"Social Links (Optional)"`

---

## 4. Replication Checklist for Other Modules

When applying this pattern to Candidates, Jobs, Vendors, Requisitions, or Teams:

| Step | Requirement | Implementation Target |
|---|---|---|
| **1. Domain Separation** | Define whether cross-domain coexistence is permitted (e.g. Talent Pool vs Active Application). | Backend Service / Repository |
| **2. Canonicalizer** | Canonicalize unique identifiers (URL, email, phone, Tax ID, Code). | Centralized Service helper |
| **3. Three-Tier Check** | Implement Intra-Draft → Root Tenant/Admin → Tenant Siblings hierarchy. | Duplication Service |
| **4. Point-of-Entry UX** | Attach debounced (`300ms`) listeners on inputs; disable primary button while duplicate. | Form Blade JavaScript |
| **5. Drawer Gate** | Check duplicates *inside* sub-drawers before committing items to memory tables. | Drawer `saveDrawer()` function |
| **6. Table Error Rows** | When rendering errors on list/table items, always render a `<tr class="...-row-error"><td colspan="N">` row. | `showInlineError()` helper |
| **7. Button & Event Safety** | Use `data-index` attributes, `pointer-events: none` on icons, and safe integer parsing. | Dynamic HTML renderers |
| **8. Automated Tests** | Write comprehensive feature tests asserting all scenarios, edge cases, and AJAX endpoints. | PHPUnit Feature Tests |

---

## 5. Verification & Quality Evidence

- **PHP Syntax & Quality:**
  - `php -l resources/views/service_company/client/create.blade.php` → **Pass (0 errors)**
  - `php -l resources/views/service_company/client/components/tab-contacts.blade.php` → **Pass (0 errors)**
  - `php -l resources/views/service_company/client/components/tab-basic-info.blade.php` → **Pass (0 errors)**
  - `php -l resources/views/service_company/client/components/tab-social-links.blade.php` → **Pass (0 errors)**
  - `vendor/bin/pint` → **Clean code styling**
- **Automated PHPUnit Test Suite (`InternalClientDuplicationAndReadinessTest`):**
  - **16 Passed (73 assertions, 0 failures)**:
    - Domain canonicalization
    - Company name normalization
    - Email & Phone normalization
    - Scenario 1 (Direct Client company coexistence)
    - Scenario 2 (Internal Client company duplication blocked)
    - Scenario 3 (Direct Client website coexistence)
    - Scenario 4 (Internal Client website duplication blocked)
    - Scenario 5 (Direct Client contact coexistence)
    - Scenario 6 (Internal Client contact duplication blocked)
    - Scenario B (Intra-draft duplicate contact blocked)
    - Readiness Service probe & CQRS validation
    - AJAX website duplicate endpoint
    - AJAX contact duplicate endpoint
    - Main Account website protection
    - Main Account and Tenant Administrator contact protection
- **Deployment Status:** Committed and pushed to `origin/staging`.
