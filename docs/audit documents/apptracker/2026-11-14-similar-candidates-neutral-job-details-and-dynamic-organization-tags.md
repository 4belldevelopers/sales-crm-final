# Architectural Changelog & Audit: Similar Candidates Neutral Job Details Sub-Card, Dynamic Organization Tags & Listing Controls Alignment

## 1. Document Metadata
- **Module:** Service Company / AppTracker — Similar Candidates (`service_company/apptracker/similar_candidates` & `dsimilar_candidates`)
- **Date:** 2026-11-14
- **Author:** JERO Architecture Team (Supergod Mode)
- **Status:** Complete & Verified
- **Applicable Rules:**
  - Rule 1 & 2: CQRS — 0% MySQL reads on web/API request paths, Elasticsearch Universal Scope for reads
  - Rule 6: Multi-Tenant Atomic Cache Invalidation (Cache version bumped to `card_v12`)
  - Rule 7: Zero Business Logic Mutations & Zero Module Regressions
  - Rule 14: Metronic UI, Poppins Font, 15.6" Screen Fit (`1366×768` & `1920×1080`), Zero Horizontal Overflow
  - Rule 17: Complete Page/Blade Isolation (`simf-*` for Similar Candidates, `dsf-*` for Client Similar Candidates)
  - Rule 20: Mandatory Modern Card + Sub-Card Enterprise Design System
  - Rule 21: Mandatory Storage Location & Module Folder Pattern for Audits
  - Rule 25: Canonical Job Classification & Organization Tokens (`My Organization`, `Internal Client`, `Direct Client`)

---

## 2. Executive Summary

This update refines the **Job Details sub-card** (Sub-Card 4) and **listing controls** on the **Similar Candidates tab** (`similar_candidates.blade.php`, `similar_existing_ajax.blade.php`, `dsimilar_candidates.blade.php`, and `dsimilar_existing_ajax.blade.php`), resolving full-subcard color tinting and establishing automated context-aware organization-type classification.

### Key Enhancements & Refinements:
1. **Job Details Sub-Card Neutral Styling & Tag-Only Highlighting:**
   - Completely eliminated container background tinting (`simf-cand-subcard--my-org`, `simf-cand-subcard--internal`, `simf-cand-subcard--direct`) and container border coloring.
   - The Job Details sub-card now retains the standard neutral `#f8fafc` background with `#edf2f7` border matching all other sub-cards in the stream.
   - Highlighting is strictly scoped to the organization-type badge pill (`.simf-target-type-badge`, `.dsf-target-type-badge`).

2. **Dynamic Organization-Type Tags (Automated Context Detection):**
   - Automatically determines the correct organization tag based on actual job and client relationships without manual selection or hardcoding:
     - **Direct Client:** Requisition received through the marketplace (`is_direct_client`, `is_job_request`, `recommendation_id > 0`, `ClientComID != company_id`, or `companyIdCheck != company_id`). Rendered in Emerald (`#ecfdf5` background, `#059669` text, `#a7f3d0` border, `fa-shield-alt` icon).
     - **Internal Client:** Hiring for a managed internal client such as Tesla, Stripe, etc. (`self_company === 0` or attached client entity is not own organization). Rendered in Blue (`#eff6ff` background, `#2563eb` text, `#bfdbfe` border, `fa-building` icon).
     - **My Organization:** Job created for our own organization (`self_company === 1` or own company hiring). Rendered in warm amber/gold (`#fef3c7` background, `#92400e` text, `#fcd34d` border, `fa-id-badge` icon).

3. **Listing Controls Alignment & Order:**
   - Synchronized listing toolbar controls in `simf-toolbar-card` and `dsf-toolbar-card`:
     1. **Search Bar** (`flex-grow: 1`, `min-width: 240px`, debounced candidate name/company/recruiter search with clear button).
     2. **Sort Dropdown** (`220px`, `flex-shrink: 0`, Best Match / Upload Date / Salary / Experience / Notice Period).
     3. **Filter Button** (`flex-shrink: 0`, primary blue filter toggle button).
     4. **Refresh Button** (`flex-shrink: 0`, single-click candidate list reload).
   - Candidate stream control bar (`.simf-stream-bar`, `.dsf-stream-bar`) consistently arranges:
     `[Select All]` checkbox → `[Similar Candidates]` active stage pill → `[X candidates]` count → `[👆 Click candidate for profile drawer • Click Add to Job to assign]` hint.

---

## 3. Architecture & Data Flow

```
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  SIMILAR CANDIDATES TOOLBAR CARD (.simf-toolbar-card / .dsf-toolbar-card)               │
│  [Search Bar 🔍] [Sort Dropdown ▼] [Filter Button ⚙] [Refresh Button 🔄]                │
└────────────────────────────────────────────────────────────────────────────────────────┘
                                            ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  STREAM CONTROL BAR (.simf-stream-bar / .dsf-stream-bar)                               │
│  [☑ Select All]  [Similar Candidates Pill]  [X Candidates]  ...  [Click Candidate Hint]│
└────────────────────────────────────────────────────────────────────────────────────────┘
                                            ▼
┌────────────────────────────────────────────────────────────────────────────────────────┐
│  CANDIDATE PRIMARY CARD (.simf-cand-card / .dsf-cand-card)                             │
│  ┌──────────────┬──────────────┬──────────────┬──────────────┬──────────────┬────────┐ │
│  │ SUB-CARD 1:  │ SUB-CARD 2:  │ SUB-CARD 3:  │ SUB-CARD 4:  │ SUB-CARD 5:  │ SUB-6: │ │
│  │ Candidate    │ Compensation │ Location &   │ JOB DETAILS  │ Ownership &  │ Stage  │ │
│  │ Info         │ & Exp        │ Degree       │ (Neutral bg) │ Added        │ & Act. │ │
│  │ [Avatar]     │ Expected/Cur │ City, Mode,  │ [Tag Pill]   │ Recruiter,   │ [Add   │ │
│  │ Name, Role   │ Exp, NP      │ Education    │ Title, #ID   │ Company, Date│ to Job]│ │
│  └──────────────┴──────────────┴──────────────┴──────────────┴──────────────┴────────┘ │
│  [Match %] [Mandatory Skills ★] [Optional Skills] [Unmatched] ... [Resume] [LinkedIn] │
└────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 4. File Changes Matrix

| File | Change Summary |
|---|---|
| `app/Http/Controllers/ServiceCompany/ServiceCompanyAppTrackerController.php` | Selected `jobs.client_id` in `wiseRows`, `extraWiseRows`, and `directJobRows` batch queries for resilient client relationship detection; bumped cache version across all endpoints to `card_v12` for atomic multi-tenant cache invalidation (Rule 6). |
| `resources/views/service_company/ajax_pages/similar_existing_ajax.blade.php` | Neutralized Sub-Card 4 (`Job Details`) container removing full-card background and border colors; implemented dynamic organization-type classification (`Direct Client` emerald, `Internal Client` blue, `My Organization` warm amber/gold) with canonical Rule 25 tokens. |
| `resources/views/service_company/ajax_pages/dsimilar_existing_ajax.blade.php` | Synchronously neutralized Sub-Card 4 for Client Similar Candidates; implemented identical dynamic organization-type classification and Rule 25 canonical tokens. |
| `resources/views/service_company/apptracker/similar_candidates.blade.php` | Removed `.simf-cand-subcard--my-org`, `.simf-cand-subcard--internal`, and `.simf-cand-subcard--direct` container overrides and hover styles; defined `.type-my-org`, `.type-internal`, and `.type-direct` badge styles; refined toolbar flex ordering with `flex-xl-nowrap` on 15.6" screens. |
| `resources/views/service_company/apptracker/dsimilar_candidates.blade.php` | Removed `.dsf-cand-subcard--*` container overrides; defined Rule 25 badge styles; aligned toolbar flex controls ordering. |
| `tests/Feature/AppTrackerCardSubCardRedesignTest.php` | Updated assertions to verify neutral Sub-Card 4 container (absence of background/border container overrides), presence of canonical badge classes (`type-my-org`, `type-internal`, `type-direct`), cache version `card_v12`, and full-suite test pass. |

---

## 5. 15.6" Screen Fit (`1366×768` & `1920×1080`)

- Toolbar controls (`Search` → `Sort` → `Filter` → `Refresh`) utilize `flex-wrap flex-xl-nowrap` with `min-width: 240px` for search and `220px` for sort, preventing wrapping or control clipping on 15.6" displays.
- Sub-Card 4 (`Job Details`) uses fractional CSS grid columns (`minmax(0, 1.75fr)`) with `white-space: nowrap !important; flex-shrink: 0;` on the organization pill, eliminating horizontal overflow and text clipping.

---

## 6. Verification Evidence

- `php -l` passed with zero syntax errors on all modified files.
- `php artisan view:clear` executed successfully.
- `./vendor/bin/pint --test` passed with zero style issues across all PHP files.
- `php artisan test tests/Feature/AppTrackerCardSubCardRedesignTest.php` passed: **14 passed (404 assertions)**.
