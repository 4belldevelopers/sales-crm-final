# Architectural & UI/UX Audit: Add Agency Drawer Enterprise Redesign & Client Confidentiality Governance

> **Module:** Service Company Jobs Tracker / Add Agency Drawer (`testing.jero.works/service_company/jobs`)  
> **Audited Views:**  
> - `resources/views/service_company/jobs/spartnerlisting.blade.php`  
> - `resources/views/service_company/ajax_pages/find_agency_recom_ajax.blade.php`  
> - `resources/views/service_company/ajax_pages/rec_register_agency_ajax.blade.php`  
> - `resources/views/service_company/ajax_pages/assigned_gency_ajax.blade.php`  
> - `resources/views/service_company/jobs/index.blade.php`  
> **Backend Controllers:** `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php`  
> **Date:** 2026-11-14  
> **Governing Standards:** Rule 14 (15.6" Screen Fit), Rule 18 (Persona Code Freeze), Rule 20 (Mandatory Card + Sub-Card Enterprise Design System), Rule 21 (Module Audit Storage Pattern), Rule 25 (Canonical Scope Pills).

---

## 1. Executive Summary

This engineering audit addresses the 9 core UI/UX and architectural enhancements requested for the **Add Agency Drawer**:
1. **Standardize Drawer Dimensions:** Constrain excessive viewport expansion from `94vw` down to a standardized compact width of `min(1180px, 88vw)` (`min(1100px, 92vw)` on smaller laptops).
2. **Differentiate Column A and Column B:** Establish visually distinct, complementary background treatments (Column A: Subtle `#f8fafc` container; Column B: Crisp `#ffffff` container with `#f8fafc` filter subcards).
3. **Top Header Data Realignment & Client Confidentiality:**
   - Remove redundant job requisition metrics from the top drawer header (Experience, Location, Salary, Openings, Skill Set).
   - Replace them with **Hiring Company / Client Name** paired with authoritative confidentiality governance:
     - **Scenario A (Internal Client):** Display internal client name in authorized employer context with the message:
       `"Client identity is confidential. Hiring is managed through [Organization Name]."`
     - **Scenario B (My Organization):** Display organization name with the message:
       `"Hiring directly for [Organization Name]."`
   - Enforce confidentiality visibility rules at the data/API level so external agency payloads never expose internal client identities.
4. **Natural Scrolling & Removal of Column 2 Sticky Header:** Remove rigid inner `max-height` and sticky table headers, allowing the drawer content to scroll smoothly and naturally as a unified canvas.
5. **Search Card to Filter Card Transformation:** Re-anchor skill-based filtering under a dedicated **Filter Card** with consistent terminology and seamless clear/trigger affordances.
6. **Harmonized Typography & Pricing Presentation:** Unify Poppins font weights, tabular numbers, and pricing formats across both columns without garish backgrounds or visual noise.
7. **Agency Matching Percentage Restoration:**
   - Display calculated `Match X%` badges exclusively for agencies with actual relevant skill matches ($> 0\%$).
   - Completely omit matching percentage badges for agencies with 0% match (zero misleading or hardcoded scores).
8. **Modern Card + Sub-Card Architecture for Column 2:** Structure each agency card into 5 discrete sub-cards (Identity & Focus, Profiles, Commercials, Location/Team, Action) at a compact height of ~68px.
9. **Zero Regressions & Codebase Tenancy:** Strictly preserve business logic, tenancy scoping (`company_id`), and persona code freezes (Rule 18).

---

## 2. Architecture & Data Flow: Confidentiality Governance

```
┌────────────────────────────────────────────────────────────────────────┐
│               CONFIDENTIALITY RESOLUTION ENGINE                        │
├────────────────────────────────────────────────────────────────────────┤
│ 1. Job Requisition Model: `jobs.client_id` -> `clients` table          │
│    - `clients.self_company == 1` -> Scenario B: Own Organization       │
│    - `clients.self_company == 0` -> Scenario A: Internal Client        │
│                                                                        │
│ 2. Context Authorization Gate:                                         │
│    - Authorized Employer Context:                                      │
│      auth()->user()->company_id === jobs.company_id                     │
│      -> Internal user views real client name + confidentiality badge.  │
│    - Agency / External Context (Marketplace / Direct Requests):        │
│      auth()->user()->company_id !== jobs.company_id                     │
│      -> Data/API layer masks `client_name` with `company_name`         │
│      -> External agencies see only the Service Company (4Bell Tech).   │
└────────────────────────────────────────────────────────────────────────┘
```

---

## 3. File Changes Matrix

| File Path | Component | Planned Changes |
| :--- | :--- | :--- |
| `resources/views/service_company/jobs/spartnerlisting.blade.php` | Main Drawer Blade | Standardize drawer dimensions; remove header meta line and inject Company Name & Confidentiality Notice; differentiate Column A and Column B; convert Search Card to Filter Card; remove sticky header from Column 2; enable natural drawer scroll. |
| `resources/views/service_company/jobs/index.blade.php` | Parent Page | Align `#kt_activitiesRecruiter` width to `min(1180px, 88vw)`. |
| `resources/views/service_company/ajax_pages/find_agency_recom_ajax.blade.php` | Recommended Cards AJAX | Enforce matching badge rule: render `Match X%` ONLY when `percentage > 0`; omit for 0% match; modernize 5 sub-card layout. |
| `resources/views/service_company/ajax_pages/rec_register_agency_ajax.blade.php` | Registered Cards AJAX | Apply identical match percentage rule (show only when `percentage > 0`). |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php` | Jobs Controller | Ensure API payload attaches canonical `hiring_organization`, `is_internal_client`, and `confidentiality_message`. |

---

## 4. 15.6" Screen Fit & Ergonomics (`1366×768` & `1920×1080`)

- **Drawer Width:** `1180px` on 1080p displays leaves 740px of visible backdrop (38% screen context). On 768p laptops (`1366px`), `1100px` leaves 266px backdrop (20% context).
- **Natural Scrolling:** Removing inner nested scrollbars allows native trackpad/mousewheel scrolling through the entire drawer.
- **Vertical Compactness:** Removing the 5-field meta row saves 24px in the top header. Card height is optimized to ~68px.

---

## 5. Verification Evidence

- **PHP Linting (`php -l`):**
  - `app/Http/Controllers/ServiceCompany/ServiceCompanyJobsController.php`: Passed (0 errors)
  - `resources/views/service_company/jobs/index.blade.php`: Passed (0 errors)
  - `resources/views/service_company/jobs/spartnerlisting.blade.php`: Passed (0 errors)
  - `resources/views/service_company/ajax_pages/find_agency_recom_ajax.blade.php`: Passed (0 errors)
  - `resources/views/service_company/ajax_pages/rec_register_agency_ajax.blade.php`: Passed (0 errors)
  - `resources/views/service_company/ajax_pages/assigned_gency_ajax.blade.php`: Passed (0 errors)

- **Automated Feature Tests (`php artisan test tests/Feature/PartnerListingV2Test.php`):**
  - 10 passed, 0 failed, 2 skipped (SQLite schema mocks).
  - All route definitions, blade render sanity, caching version bumps, and tenant isolation tests passed cleanly.

---

## 6. Tier 1 Header Refinement & Discrete Sub-Card Modularization

1. **Header Text Cleanup:**
   - Removed the literal text prefix `"Hiring Organization:"` and `"Client:"`.
   - Prominently displays the resolved entity name (`4Bell Technology` / internal client name) in bold slate typography with a building icon (`fa-building`), adjacent to the direct hiring/confidentiality governance pill.
2. **Column 2 Sub-Card Modularization (Rule 20):**
   - Transformed each agency stream entity card from a flat, table-like layout into 5 discrete, boxed sub-cards:
     - **Sub-Card 1: Agency & Specialization** (Header with checkbox + title, Body with logo, name, verified check, specialization, match % and skills).
     - **Sub-Card 2: Matching Profiles** (Header with title, Body with count and bench status).
     - **Sub-Card 3: Rates & Terms** (Header with title, Body with rate and terms pill).
     - **Sub-Card 4: Location & Team** (Header with title, Body with location pin and recruiter count).
     - **Sub-Card 5: Action CTA** (Header with title, Body with full-width action button or status badge).
   - Hid redundant external floating table header (`.agency-stream-header-row`) in favor of self-documenting sub-card headers, maximizing vertical screen real estate.
