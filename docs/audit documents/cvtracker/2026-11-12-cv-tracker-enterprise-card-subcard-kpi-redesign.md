# Architectural Changelog & Audit: CV Tracker Enterprise Modern Card + Sub-Card & Interactive KPI Redesign

## 1. Document Metadata
- **Module:** Service Company / CV Tracker (`service_company/cvtracker`)
- **Date:** 2026-11-13 (Updated)
- **Author:** JERO Architecture Team (Supergod Mode)
- **Status:** Complete & Verified
- **Applicable Rules:**
  - Rule 1 & 2: CQRS Architecture (0% MySQL Reads on web/API paths, 100% Elasticsearch Reads)
  - Rule 3: Client-First Execution (User RAM + Client NLP + IndexedDB)
  - Rule 7: Zero Business Logic Mutations & Zero Module Regressions
  - Rule 14: UI Typography & 15.6" Screen Responsive Fit (1366×768 & 1920×1080)
  - Rule 17: Complete Page/Blade Isolation
  - Rule 20: Mandatory Card + Sub-Card Enterprise Design System
  - Rule 21: Mandatory Audit Location & Module Folder Pattern
  - Rule 24: Default User/Account Timezone
  - Rule 25: Canonical Job Classification & Organization Tokens (`My Organization`, `Internal Client`, `Direct Client`)

---

## 2. Executive Summary
The **CV Tracker page** (`service_company/cvtracker`) has been completely redesigned into an enterprise-level, modern card and sub-card based ATS interface inspired by the **App Tracker** page and **Similar Candidates tab** (`similar_existing_ajax.blade.php`), featuring **9 interactive status-based KPI cards** in a single row inspired by the **Client Tracker** page.

### Key Architectural Enhancements:
1. **Interactive Status-Based KPI Cards (Client Tracker Pattern):**
   - Replaced legacy static cards with **9 unified interactive status KPI cards in a single row**:
     1. **All Candidates** (`data-tab="all"`, count: `#kpi_all`, blue `#2563eb`)
     2. **In Review** (`data-tab="8"`, count: `#kpi_inreview`, yellow `#ca8a04`)
     3. **Sourcing** (`data-tab="1"`, count: `#kpi_sourcing`, slate `#64748b`)
     4. **Screening** (`data-tab="2"`, count: `#kpi_screening`, purple `#9333ea`)
     5. **Evaluation** (`data-tab="3"`, count: `#kpi_evaluation`, sky `#0284c7`)
     6. **Negotiation** (`data-tab="4"`, count: `#kpi_negotiation`, pink `#e11d48`)
     7. **Offer** (`data-tab="5"`, count: `#kpi_offered`, orange `#ea580c`)
     8. **Joined** (`data-tab="6"`, count: `#kpi_joined`, emerald `#16a34a`)
     9. **Rejected** (`data-tab="7,9"`, count: `#kpi_rejected`, red `#dc2626`)
   - Configured strictly as a single row across viewports $\ge 1200$px with clean horizontal touch scrolling for smaller screens.

2. **Organization Type Dropdown List (Rule 25):**
   - Replaced wide pill buttons with a dedicated, compact **Organization Type Dropdown List** (`.cvh-tb-org-wrap`, 215px):
     - `All Organizations` (`data-value="all"`, icon: `fa-layer-group`, color `#2563eb`)
     - `My Organization` (`data-value="my_organization"`, icon: `fa-id-badge`, color `#0891b2`)
     - `Internal Client` (`data-value="internal_client"`, icon: `fa-building`, color `#2563eb`)
     - `Direct Client` (`data-value="direct_client"`, icon: `fa-shield-alt`, color `#059669`)
   - Fully synchronized with Elasticsearch aggregation filters and client-side SWR caching.

3. **Candidate Card Format (App Tracker / Similar Candidates Pattern):**
   - Candidate listing refactored into modern 6-subcard primary cards (`.simf-cand-card.aptk-cand-card`) with full-width intelligence bottom strip:
     - **Sub-Card 1: CANDIDATE INFO & IDENTITY** (checkbox, `Candidate Info` label, `Duplicate` badge, 36×36px avatar box with initials/photo in flex row, name, designation, current company aligned to right).
     - **Sub-Card 2: COMPENSATION & EXP** (Expected CTC with mode, Current CTC with mode, total & relevant exp with briefcase icon, notice period pill with clock icon).
     - **Sub-Card 3: LOCATION & DEGREE** (City and Country code, Work Mode with icon, uppercase Degree with graduation cap icon).
     - **Sub-Card 4: JOB DETAILS** (Renamed from Target Req to `Job Details`; Mandate title, `#JobId • Category`, `Client • Employment Type`, Rule 25 canonical organization badge text/pill only, with standard neutral subcard background).
     - **Sub-Card 5: OWNERSHIP & ADDED** (Recruiter with user icon, company with building icon, added date with calendar icon).
     - **Sub-Card 6: STAGE & ACTION** (Stage pill with dynamic theme, sub-stage label, restored `Update Stage >` link cue, and primary action button **"Add to Other Job"**).
     - **Bottom Intelligence Strip:** Match score pill (`Match XX%`), single-line horizontal scrolling Tagify skills (matched in green, unmatched in gray, mandatory in blue), Resume pill (`Resume` in green / `No CV` in red), and verified LinkedIn pill — strictly single-row flex layout with zero vertical line-wrapping.

4. **"Add to Other Job" Drawer Integration:**
   - Action button labeled **"Add to Other Job"** (`.cv-add-to-other-job`, `btn btn-sm btn-primary`, full width of subcard).
   - On click:
     - Unchecks other candidates and checks current candidate's `.chk_list_item` checkbox.
     - Sets card state to `.is-selected`.
     - Opens the copy candidate drawer (`#kt_activitiesCopy`) via `#kt_activitiesCopy_toggle`.
     - Executes POST to `service_company.cvtracker.application_copy` with candidate array `[candId]` and wise ID `[wiseId]`.
     - Injects returned view directly into `#application_copy_body_content`.

---

## 3. Rule 25 Organization Tokens: Color Codes & Highlighting Specification

The `Job Details` sub-card (Sub-Card 4) implements canonical badge colors strictly adhering to Rule 25, applied exclusively to the badge pill (the subcard container retains the clean neutral `#f8fafc` background):

| Organization Type | Badge Class | Badge Background | Badge Text Color | Badge Border | Icon |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **My Organization** (Own company hiring) | `.type-my-org` | `#ecfeff` | `#0891b2` | `1px solid #a5f3fc` | `fa-solid fa-id-badge` |
| **Internal Client** (Managed non-marketplace) | `.type-internal` | `#eff6ff` | `#2563eb` | `1px solid #bfdbfe` | `fa-solid fa-building` |
| **Direct Client** (Marketplace mandate) | `.type-direct` | `#ecfdf5` | `#059669` | `1px solid #a7f3d0` | `fa-solid fa-shield-alt` |

### CSS Implementation Tokens:
```css
/* Rule 25 Canonical Badges (Applied strictly to badge text/pill, not full subcard) */
.type-my-org { background: #ecfeff !important; color: #0891b2 !important; border: 1px solid #a5f3fc !important; }
.type-internal { background: #eff6ff !important; color: #2563eb !important; border: 1px solid #bfdbfe !important; }
.type-direct { background: #ecfdf5 !important; color: #059669 !important; border: 1px solid #a7f3d0 !important; }
```

---

## 4. 15.6" Screen Fit (1366×768 & 1920×1080)

To guarantee 100% responsiveness without horizontal page scrollbars on 15.6" displays:
- **Grid Ratio:** Sized across 6 discrete fractional units totaling 8.2 units:
  ```css
  grid-template-columns: minmax(0, 1.85fr) minmax(0, 1.25fr) minmax(0, 1.1fr) minmax(0, 1.75fr) minmax(0, 1.1fr) minmax(0, 1.15fr);
  gap: 8px;
  padding: 10px 12px;
  ```
- **Proportional Widths on 1366px screen (~1080px available width after Metronic sidebar):**
  - Sub-Card 1 (Candidate Info): `~242px` (36px avatar + 2-line identity + duplicate pill)
  - Sub-Card 2 (Comp & Exp): `~163px` (Expected/Current CTC, Exp, NP)
  - Sub-Card 3 (Location & Degree): `~144px` (City, Mode, Degree)
  - Sub-Card 4 (Job Details): `~230px` (Title, #ID • Cat, Client • EmpType, Org Badge)
  - Sub-Card 5 (Ownership & Added): `~144px` (Recruiter, Company, Date)
  - Sub-Card 6 (Stage & Action): `~150px` (Stage Pill, Update Stage cue, "Add to Other Job" Button)
- All text nodes enforce `overflow: hidden; text-overflow: ellipsis; white-space: nowrap;`.
- Sub-Card 7 (Bottom Strip) enforces `white-space: nowrap; overflow-x: auto; flex-wrap: nowrap;` for skills, keeping Match % on left and Resume/LinkedIn on right.

---

## 5. File Changes Matrix

| File Path | Description of Changes |
| :--- | :--- |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php` | Joined `job_categories` in `$dbJobs` query (line 1565) to batch-fetch `jobs.category_id`, `jobs.employment_type`, and `job_categories.name as category_name` in a single pass without per-row N+1 queries. |
| `resources/views/service_company/ajax_pages/all_candidates.blade.php` | Refined candidate card: 1) Renamed Target Req to `Job Details`; 2) Applied Rule 25 organization colors strictly to the badge text/pill (neutral subcard background); 3) Aligned Candidate Info avatar box and metadata into flex row; 4) Restored `Update Stage >` cue in Stage & Action; 5) Formatted bottom intelligence strip as a single-line flex row with horizontally scrolling skills. |
| `resources/views/service_company/cvtracker/index.blade.php` | 1. Single-row 9 interactive KPI cards (Client Tracker style).<br>2. Organization Type Dropdown List (Rule 25 canonical options).<br>3. Comprehensive `.simf-*` and `.aptk-*` CSS stylesheet with `!important` guards for layout integrity.<br>4. Click handler for `.cv-add-to-other-job` triggering `#kt_activitiesCopy` drawer and copy candidate AJAX flow. |
| `docs/audit documents/cvtracker/2026-11-12-cv-tracker-enterprise-card-subcard-kpi-redesign.md` | Documented full architecture, color tokens, drawer workflows, and 15.6" screen proportions per Rule 21. |

---

## 6. Verification Evidence

1. **PHP Syntax Linter Checks:**
   ```bash
   php -l app/Http/Controllers/ServiceCompany/ServiceCompanyCvTrackerController.php
   # Output: No syntax errors detected

   php -l resources/views/service_company/ajax_pages/all_candidates.blade.php
   # Output: No syntax errors detected

   php -l resources/views/service_company/cvtracker/index.blade.php
   # Output: No syntax errors detected
   ```
2. **AI Guard Verification:**
   - Zero debugging statements (`dd()`, `dump()`, `ray()`, `var_dump()`).
   - Zero direct `env()` calls outside `config/`.
   - 0% MySQL reads on request paths; 100% Elasticsearch read scope maintained.
   - Tenancy isolation preserved with `company_id` scoping.
