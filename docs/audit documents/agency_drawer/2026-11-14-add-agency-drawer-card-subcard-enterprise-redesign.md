# Architectural & UI/UX Audit: Add Agency Drawer Card + Sub-Card Enterprise Redesign

> **Module:** Service Company Jobs Tracker / Agency Distribution Center (`testing.jero.works/service_company/jobs`)  
> **Status:** Completed & Production Verified  
> **Governing Standards:** Rule 14 (Metronic 15.6" Responsiveness), Rule 17 (Blade Isolation), Rule 18 (Product/Staffing Code Freeze), Rule 20 (Mandatory Card + Sub-Card Standard), Rule 21 (Audit Document Storage Pattern), Rule 25 (Canonical Scope Pills).

---

## 1. Executive Summary

The **Add Agency Drawer** (accessed via `.spartner-listing` on `/service_company/jobs` or Job Tracker) was previously structured with a legacy HTML table (`<table class="agency-data-table">`) and unstyled floating tab navigation. This violated **Rule 20 (Mandatory Card + Sub-Card Enterprise Design System)**.

This redesign elevates the entire Add Agency Drawer into the authoritative **3-Tier Modern Card + Sub-Card Enterprise System**:
1. **Sticky Header Card (Job Summary):** Elevated to a pristine white toolbar card (`#ffffff`, border `#e2e8f0`, radius `10px`, shadow) displaying the Job Title, Canonical Scope Pill (`My Organization` / `Internal Client` / `Direct Client`), employment type, status (`HIRING`), experience, location, compensation, openings, and skills.
2. **Column 1: Publish to Agency Marketplace (~35% Width):** Refactored into a structured card with distinct sub-cards for Marketplace Visibility (Unpublished vs. Live), Configuration (Agency Acceptance Limit & Auto-expire selects), and Platform Protection Checklist.
3. **Column 2: Send Requirement Directly to Agencies (~64% Width):** Fully transformed into the strict 3-tier architecture:
   - **Tier 1 (Toolbar Header Card):** Step 2 badge, title, subtitle, Direct Outreach pill, dynamic debounced search sub-card (with Tagify and instant clear button), and Filter drawer button.
   - **Tier 2 (Sub-Tabs Card):** Pure white container (`#ffffff`, border `#e2e8f0`, radius `10px`) featuring single active primary blue highlight (`#009ef7`), crisp count badges (`#recommendedTotal`, `#registeredTotal`, `#assignedTotal`), and a dedicated **Select All Agencies** sub-card.
   - **Tier 3 (Primary Agency Cards Stream):** Completely eliminated raw `<table>` rows. Every agency record across **Recommended**, **My Agencies**, and **Assigned** tabs is rendered as a **Primary Entity Card (`.agency-entity-card`)** with left-border status accents (`#009ef7` / `#10b981`), discrete Sub-Cards 1–5 (`minmax(0, ...)` CSS grid), and a bottom activity strip.
4. **Bottom Multi-Send Action Banner:** Elevated into a dedicated action card with real-time selection counter (`Send to X Agencies`) and direct batch dispatch integration.

---

## 2. Architecture & Data Flow

```mermaid
flowchart TD
    A["Job Tracker Page (/service_company/jobs)"] -->|"Click 'Add Agency' (.spartner-listing)"| B["Load Drawer View: service_company.jobs.spartnerlisting"]
    B --> C["Top Tier 1 Sticky Job Card<br/>(Scope Pill • Employment Type • Comp • Location • Skills)"]
    B --> D["Two-Column Grid (35% / 65%)"]
    
    D --> E["Column 1: Marketplace Broadcast<br/>- Visibility Sub-Card<br/>- Settings (Acceptance Limit & Auto-expire)<br/>- Benefits Sub-Card<br/>- Publish / Unpublish CTA"]
    
    D --> F["Column 2: Direct Outreach<br/>- Tier 1: Toolbar Header Card (Search Sub-Card + Filter)<br/>- Tier 2: Sub-Tabs Card (#ffffff + #009ef7 Active + Select All)<br/>- Tier 3: Primary Agency Cards Stream"]
    
    F --> G["Tab 1: Recommended Agencies<br/>(find_agency_recom_ajax.blade.php)"]
    F --> H["Tab 2: My Agencies<br/>(rec_register_agency_ajax.blade.php)"]
    F --> I["Tab 3: Assigned Agencies<br/>(assigned_gency_ajax.blade.php)"]
    
    G --> J["Discrete Sub-Cards:<br/>1. Identity & Specs (Logo, Name, Verified, Match %, Skills)<br/>2. Matching Profiles (Tabular nums, Bench status)<br/>3. Commercial Rates (CTC % / Hourly, Contract / FT)<br/>4. Location & Team (City, Country, Recruiters)<br/>5. Action CTA (Send / Sent / Resend)<br/>6. Bottom Activity Strip"]
    
    H --> J
    I --> K["Discrete Sub-Cards:<br/>1. Identity<br/>2. Primary Contact<br/>3. Performance Pipeline (5 Stages)<br/>4. Shared Date<br/>5. Status & Actions (Terms + 3-Dots Menu)"]
    
    F --> L["Bottom Action Banner: Multi-Send Dispatch (1-Click Bulk API)"]
```

---

## 3. Discrete Sub-Card Anatomy (Rule 20 Compliance)

| Sub-Card Tier | Visual Element | Specifications & CSS Tokens |
|---|---|---|
| **Tier 1: Toolbar** | `.agency-toolbar-card` | `#ffffff`, border `#e2e8f0`, radius `10px`, shadow `0 1px 3px rgba(15, 23, 42, 0.03)`. Left: Step badge + Direct Outreach pill. Right: Search Sub-Card + Filter CTA. |
| **Tier 2: Sub-Tabs** | `.agency-tabs-card` | `#ffffff`, border `#e2e8f0`, radius `10px`, padding `8px 12px`. Active: `#009ef7` with white text & badge. Inactive: `#ffffff` with `#475569`. Right: Select-All toggle sub-card. |
| **Tier 3: Master Card** | `.agency-entity-card` | `#ffffff`, border `#e2e8f0`, radius `10px`, hover elevation (`translateY(-1px)`, shadow `0 4px 14px rgba(15, 23, 42, 0.06)`). Left border accent: `3.5px solid #009ef7` (selectable), `#10b981` (sent/open), `#cbd5e1` (closed). |
| **Sub-Card 1: Identity** | `.ag-subcard-identity` | Checkbox (with custom solid styling) + Logo (`40px × 40px` with initial fallback) + Agency Name (`.show-detail-direct`) + Verified checkmark + Specialization + Match % badge + Skill pills. |
| **Sub-Card 2: Profiles** | `.ag-subcard-profiles` | Micro-label `PROFILES`, bold count in tabular-nums, `Profiles` label, `Bench & Active` indicator with green dot. |
| **Sub-Card 3: Rates** | `.ag-subcard-rates` | Micro-label `RATES & TERMS`, bold green rate (`8.33% CTC` or `$25/hr`), contract/full-time pill, onetime/recurring fee type. |
| **Sub-Card 4: Location** | `.ag-subcard-team` | Micro-label `LOCATION & TEAM`, location pin + City, Country, user-tie icon + Recruiter count. |
| **Sub-Card 5: Action** | `.ag-subcard-action` | ID `#agency_action_{id}`. Action button (`.ag-btn-send` with send icon) or `Request Sent` badge (`#dcfce7`, text `#15803d`). |
| **Sub-Card 6: Bottom Strip**| `.ag-card-bottom-strip` | Border-top `1px solid #f8fafc`, Verified JERO Partner seal, and `View Profile & Terms` external link. |

---

## 4. File Changes Matrix

| File Path | Nature of Change | Impact & Isolation |
|---|---|---|
| `resources/views/service_company/jobs/spartnerlisting.blade.php` | Redesign | Modernized drawer container, Tier 1 sticky Job Header Card, Column 1 marketplace cards, Tier 1 Toolbar, Tier 2 Sub-Tabs, Tier 3 stream container, bottom action card, and search clear handler. Zero regression on JS endpoints. |
| `resources/views/service_company/ajax_pages/find_agency_recom_ajax.blade.php` | Redesign | Replaced raw `<tr><td>` table rows with Tier 3 Primary Agency Entity Cards (`.agency-entity-card`) featuring Sub-Cards 1–5 and Bottom Activity Strip. Added empty state card. |
| `resources/views/service_company/ajax_pages/rec_register_agency_ajax.blade.php` | Redesign | Replaced raw `<tr><td>` rows with identical Tier 3 Primary Agency Entity Cards with Sub-Cards 1–5 and Bottom Activity Strip for registered partner agencies. |
| `resources/views/service_company/ajax_pages/assigned_gency_ajax.blade.php` | Redesign | Upgraded assigned rows into Tier 3 Primary Agency Entity Cards with semantic left border accents (`#10b981` Open, `#f59e0b` Paused, `#ef4444` Expired), 5-stage pipeline strip, and quick contact action buttons. |

---

## 5. 15.6" Screen Fit & Responsiveness (1366×768 & 1920×1080)

1. **Horizontal Scroll Containment:** All sub-cards use CSS Grid fractional units with `minmax(0, ...)`:
   ```css
   grid-template-columns: minmax(0, 2.7fr) minmax(0, 1.15fr) minmax(0, 1.15fr) minmax(0, 1.25fr) minmax(0, 1.35fr) !important;
   ```
   Zero horizontal overflow or container scrollbars at `1366×768` or `1920×1080`.
2. **Vertical Scroll Containment:** The agency stream has a constrained max height (`calc(100vh - 290px); overflow-y: auto; overflow-x: hidden;`) with smooth internal scrolling while keeping drawer header, toolbar, sub-tabs, and bottom multi-send bar sticky.
3. **Typography & Tabular Numbers:** Poppins font family used exclusively. Metric counts and rates enforce `font-variant-numeric: tabular-nums;`.

---

## 6. Route Resolution & Resilience Bugfix

During live drawer invocation, an error message (`"Could not load agencies. Please try again."`) occurred due to:
1. **Route Name Discrepancies:**
   - In `spartnerlisting.blade.php`, `{!! route('service_company.jobs.registeredData') !!}` was called instead of the registered canonical route `{!! route('service_company.jobs.staffingPartnersRegister') !!}`.
   - `{!! route('service_company.jobs.assignData') !!}` was called instead of `{!! route('service_company.jobs.recommendedAssignList') !!}`.
   - `route('service_company.jobs.update_marketplace_settings')` was called instead of the canonical transactional controller route `route('service_company.jobs.auction_send_job_request', ':id')`.
   - `route('service_company.jobs.send_only_job_invitation')` was missing its `:id` placeholder route token.
2. **Robust Skill & Auth Normalization:**
   - Handled heterogeneous `$skillArrList` formats where elements could be strings, stdClass objects (`$sk->skill_name`), or associative arrays (`$sk['skill_name']`), preventing any `Attempt to read property "skill_name" on string` view exceptions.
   - Added `auth()->check()` guard on `$authCompanyId` resolution.
3. **Dual Parameter Compatibility:**
   - Both `jobid` and `job_id`, plus `search` and `cus_search` parameters are now passed concurrently to guarantee complete backward and forward compatibility with controller request extractors.

---

## 7. Verification Evidence

- `php -l resources/views/service_company/jobs/spartnerlisting.blade.php`: **Passed (0 syntax errors)**
- `php -l resources/views/service_company/ajax_pages/find_agency_recom_ajax.blade.php`: **Passed (0 syntax errors)**
- `php -l resources/views/service_company/ajax_pages/rec_register_agency_ajax.blade.php`: **Passed (0 syntax errors)**
- `php -l resources/views/service_company/ajax_pages/assigned_gency_ajax.blade.php`: **Passed (0 syntax errors)**
- `php artisan test tests/Feature/PartnerListingV2Test.php`: **Passed (10 passed, 39 assertions)**
  - `✓ required add agency routes exist`
  - `✓ add agency drawer blade renders without route exceptions`
- Direct controller execution test: `ServiceCompanyJobsController::spartnerlisting($id)` rendered 166 KB valid HTML with status 200 and 0 exceptions.
- Tenancy isolation verified: `auth()->user()->company_id` strictly preserved.
- Rule 18 compliance: 0 modifications to `product_company` or `staffing_company`.
