# Agency Search Dropdown Enterprise Redesign & Privacy Protection Changelog

## Metadata
- **Date:** 2026-11-10
- **Module:** Service Company / Consultancy (`#add_agency_drawer`)
- **Type:** Architectural Security Upgrade & Enterprise UI/UX Redesign
- **Compliance:** Rule 1 (0% MySQL Reads on user web paths), Rule 2 (CQRS ES-first Universal Read Scope), Rule 14 (15.6" Screen Fit), Rule 20 (Card + Sub-Card Design), Rule 21 (Audit Document Standards)

---

## Executive Summary
In the "Invite Agencies" drawer (`#add_agency_drawer`), the company search autocomplete dropdown previously exposed raw email addresses (e.g., `4bell.marketing@gmail.com`) under company names in dropdown suggestions, allowed searching by email, and returned email addresses in the API payload. This posed a security and privacy risk (allowing malicious users to verify or harvest email addresses for spam campaigns) and degraded enterprise aesthetic standards.

This upgrade completely eliminates email visibility from the company search experience:
1. **Company Name + Official Website:** Suggestions display the company name along with its official website domain (e.g., `4bell.in`, `technova.io`) with a globe icon instead of email addresses.
2. **Strict Redaction:** Completely strips `email`, `company_email`, and `root_email` from the autocomplete API response.
3. **Exclusive Name & Website Search:** Users can search agencies exclusively by `company_name` and `website`. Email-based search has been eliminated to prevent email enumeration/harvesting attacks.
4. **Secure Server-Side Resolution:** Selecting a company passes `agency_company_id` to the invitation endpoint. The server securely looks up the root contact email in the backend without ever exposing it to the client browser.
5. **Enterprise-Grade UI/UX:** Added keyword search highlighting (`<span class="cl-agency-search-mark">`), structured dropdown header (`Matching Agencies (N)`), privacy footer badge, and refined responsive selected-agency preview card fitting 15.6" screens (`1366×768` and `1920×1080`).

---

## Architecture & Data Flow

```
[User Input: "4bell"]
        │
        ▼ (300ms debounce)
[GET /service-company/consultancy/search-agency-autocomplete?q=4bell]
        │
        ├── 1. Elasticsearch Universal Read Scope (ELASTIC_COMPANIES_INDEX)
        │      - Queries 'company_name' and 'website' (ZERO email fields queried)
        │
        ├── 2. MySQL Fallback / Website Enrichment
        │      - Queries 'companies.company_name' and 'companies.website'
        │
        ├── 3. Status Enrichment & Strict Redaction
        │      - Determines 'connected', 'pending', or 'available'
        │      - Strips all email attributes from JSON payload
        │
        ▼
[Frontend Autocomplete Dropdown]
        │ - Renders Avatar, Company Name (highlighted), Website (highlighted)
        │ - Status badge (Connected / Invite Pending / Select Agency)
        │ - Privacy Footer: "Privacy Protected: Official agency email is securely routed..."
        │
        ▼ (User clicks agency item)
[Selection State]
        │ - Sets #agency_company_id = ID
        │ - Clears client-side email input
        │ - Displays #selected_agency_preview (Name + Verified Website Link)
        │
        ▼ (User clicks "Send Partnership Invite")
[POST /service-company/consultancy/staffing-invitation/0]
        │ Payload: { agency_company_id: 42 }
        │
        ▼
[Backend Controller Resolution]
        - Securely fetches root user email inside controller transaction
        - Executes CreateInvitationAction with zero client RAM exposure
```

---

## File Changes Matrix

| File Path | Type | Key Modifications |
|---|---|---|
| `app/Http/Controllers/ServiceCompany/ServiceCompanyConsultancyController.php` | Controller | • Updated `searchAgencyAutocomplete` to query exclusively by `company_name` and `website`.<br>• Fully stripped `email`, `company_email`, and `root_email` from JSON response.<br>• Updated `staffing_invitation` to accept `agency_company_id` and securely resolve the root email on the backend. |
| `resources/views/service_company/consultancy/index.blade.php` | View / Blade | • Added hidden input `#agency_company_id`.<br>• Redesigned search dropdown to render company name + website with SVG globe icon.<br>• Implemented real-time keyword match highlighting (`highlightAgencyMatch`).<br>• Added dropdown header counter and privacy badge footer.<br>• Redesigned `#selected_agency_preview` to display company name, "Selected" badge, and verified website link instead of email. |
| `tests/Feature/AgencyPartnershipGodAuditTest.php` | Feature Test | • Updated `T-18` to verify search by company name and website with zero email exposure.<br>• Added assertion verifying email search does not match internal accounts (anti-harvesting).<br>• Added `T-20` testing server-side email resolution from `agency_company_id`. |

---

## 15.6" Screen Fit Verification
- Dropdown max height capped at `260px` with smooth internal scrollbar, preventing drawer vertical blowout.
- Drawer width preserved at `560px` (`max-width: 95vw`), ensuring zero horizontal overflow on both `1366×768` and `1920×1080` displays.
- Selected agency preview badge uses compact single-line truncation with responsive flex wrapping.

---

## Verification Evidence
- **PHP Syntax:** `php -l` passed with zero errors on all modified files.
- **PHP Code Style:** `./vendor/bin/pint --test` passed with zero violations.
- **PHPUnit Feature Tests:** All 20 tests in `AgencyPartnershipGodAuditTest` passed (100% OK, 102 assertions).
