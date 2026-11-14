# Architecture & Implementation Document: Marketplace Agencies Modern Card + Sub-Card Redesign

> **Module:** Marketplace Agencies Discovery (`/service_company/find-agency`)  
> **Route:** `service_company.find.findAgency` / `service_company.find.findAgencyData`  
> **Standard:** Rule 20 (Mandatory Card + Sub-Card Design Standard), Rule 14 (15.6" Screen Fit), Rule 17 (Page Isolation), Rule 21 (Audit Document Storage Pattern), Rule 24 (Default Timezone with Date & Time)  
> **Date:** 2026-11-14  
> **Target Concurrency:** 10,000 Concurrent Recruiters, 0% Avoidable MySQL Reads (Strict CQRS via Elasticsearch)

---

## 1. Executive Summary

This engineering upgrade completely redesigns the Marketplace Agencies Discovery module (`/service_company/find-agency`) from a legacy flat HTML table into the modern **Enterprise Card + Sub-Card Design System (Rule 20)**.

The redesign is directly inspired by the gold-standard **Client Tracker** page:
1. **6 Dynamic KPI Stat Cards (Single Row Layout)**:
   - `01 Total Agencies`
   - `02 Bench Agencies`
   - `03 Contract Staffing`
   - `04 Full-Time Hiring`
   - `05 Indian Agencies`
   - `06 Global Agencies`
2. **Enterprise Filter Bar (Tier 2.5)**:
   - Micro-Search input with debounce, clear button `✕`, match count badge, and `Ctrl+K` keyboard shortcut.
   - Modern Metronic Location Combobox Popover with live search, active hubs, and country catalog.
   - Enterprise Industry Combobox Popover with quick chips and searchable 160+ industry index.
   - Enterprise Sort Combobox Popover (Best Match, Recently Active, Rating, Name A-Z, Most Jobs).
3. **Tier 3 Primary Card Stream with Discrete Sub-Cards**:
   - Replaces `<table>` rows with discrete Sub-Cards:
     - **Sub-Card 1: Agency Identity & Specialization** (Logo/monogram avatar, Agency Name, Verified Partner badge, Industry pill, matched skills tags).
     - **Sub-Card 2: Location & Recruiters** (City, Country, Active Recruiters count, Established/experience).
     - **Sub-Card 3: Commercials & Hiring Rates** (Full-Time % * CTC or Contract hourly/monthly rate, Onetime vs Recurring fee).
     - **Sub-Card 4: Talent Pool & Overview** (Active Jobs count, Available Profiles count, Bench Talent pool).
     - **Bottom Activity Strip**: Rule 24 compliant timestamp with tenant timezone (e.g., `10 Nov 26, 03:21 PM IST`), active status pulse, secondary "View Profile" drawer trigger, and primary idempotent "Send Request" / "Cancel Request" action.

---

## 2. Architecture & Data Flow

```
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│                       RECRUITER BROWSER (Client-First Execution)                           │
│  [Search Bar Ctrl+K]  [Location Combobox]  [Industry Combobox]  [Sort Combobox]            │
│  [01 Total]  [02 Bench]  [03 Contract]  [04 Full-Time]  [05 Indian]  [06 Global]            │
└──────────────────────────────────────────────┬──────────────────────────────────────────────┘
                                               │ HTTP GET /find-agency-data
                                               ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│                    FindAgencyService (Strict CQRS Read Engine)                              │
│  • Reads from Elasticsearch ELASTIC_COMPANIES_INDEX (0% MySQL Reads on read path)          │
│  • Applies dynamic tab filters: all | bench | contract | fulltime | india | global          │
│  • Applies text search across company_name, description, industry, skills                  │
│  • Computes aggregated KPI counts for all 6 categories                                      │
│  • N+1 Elimination via batch pre-enrichment (relations, users, jobs, candidates)            │
└──────────────────────────────────────────────┬──────────────────────────────────────────────┘
                                               │
                                               ▼
┌─────────────────────────────────────────────────────────────────────────────────────────────┐
│                     AJAX Card + Sub-Card Blade Renderer                                     │
│  • Renders Tier 3 Primary Cards with 4 Upper Sub-Cards + Bottom Activity Strip              │
│  • Formats last active date/time in tenant timezone (Rule 24: GeneralMethod::defaultTimeZone)│
│  • Connects to #kt_activitiesDirectClient drawer via .show-detail-direct                     │
│  • Seamless AJAX request sending/cancelling with X-Idempotency-Key                          │
└─────────────────────────────────────────────────────────────────────────────────────────────┘
```

---

## 3. File Changes Matrix

| File | Type | Changes Description |
|---|---|---|
| `docs/audit documents/V2_Find_Agency/2026-11-14-marketplace-agencies-card-subcard-redesign.md` | New | Comprehensive architecture, data flow, and audit documentation complying with Rule 21. |
| `app/Http/Controllers/ServiceCompany/ServiceCompanyCompanyController.php` | Modified | Computes the 6 KPIs (`total_agencies`, `bench_agencies`, `contract_staffing`, `fulltime_hiring`, `indian_agencies`, `global_agencies`) via Elasticsearch with graceful MySQL fallback, prepares location facets and industries bundle for comboboxes. |
| `app/Services/FindAgencyService.php` | Modified | Adds support for `tab` parameter (all, bench, contract, fulltime, india, global), text search, sort orders, returns updated KPI counts in JSON, removes hardcoded country 101 restriction when searching all agencies. |
| `resources/views/service_company/common/find-agency.blade.php` | Modified | Redesigned into Tier 1 Toolbar, Tier 2 (6 KPI Cards matching Client Tracker), Tier 2.5 (Filter Bar with Search, Location, Industry, Sort), Tier 3 Primary Card container, and reactive JS engine. |
| `resources/views/service_company/ajax_pages/find_agency_ajax.blade.php` | Modified | Refactored from table `<tr>` rows to Modern Card + Sub-Card layout (`.cl-primary-card`, `.cl-subcards-grid`, `.cl-card-bottom-strip`), Rule 24 timezone formatting, drawer & action hooks. |

---

## 4. 15.6" Screen Fit Verification (`1366×768` & `1920×1080`)

1. **KPI Stat Grid**:
   - `grid-template-columns: repeat(6, minmax(0, 1fr)) !important;`
   - Fixed 8px gap with `min-width: 0` to prevent wrapping and avoid horizontal overflow.
2. **Filter Bar**:
   - Flexible wrap with minimum search width `240px`, and dropdown pills with `min-width: 145px` ensuring all controls fit in one or two clean compact rows without overflow.
3. **Sub-Cards Grid**:
   - `grid-template-columns: minmax(0, 2.2fr) minmax(0, 1.4fr) minmax(0, 1.3fr) minmax(0, 1.7fr);`
   - All text truncated with `text-overflow: ellipsis`, `white-space: nowrap`, and responsive break at `< 992px` (2 columns) and `< 640px` (1 column).

---

## 5. Tenancy & Security Audit

- Tenant Scoping: All queries are tenant-scoped via `auth()->user()->company_id`.
- MySQL Isolation: 0% MySQL reads on search and query execution; 100% queries served by Elasticsearch.
- Idempotency: Action mutations use `X-Idempotency-Key` headers to prevent double-invitations.
- Timezone Compliance: All dates/times converted into tenant configured timezone via `GeneralMethod::defaultTimeZone($company_id)`.
