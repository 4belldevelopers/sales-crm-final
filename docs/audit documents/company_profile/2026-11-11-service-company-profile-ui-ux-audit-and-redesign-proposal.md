# Service Company — Company Profile UI/UX Redesign Specification: Internal Client Drawer Parity

> **Document Type:** UI/UX Redesign Proposal & Visual Preview Specification  
> **Target Route:** `https://testing.jero.works/service_company/account`  
> **Module / Domain:** Service Company / Company Profile (`resources/views/service_company/account/`)  
> **Visual & Architecture Benchmark:** JERO Internal Client Drawer (`resources/views/service_company/client/show.blade.php`)  
> **Status:** ⚠️ **PROPOSAL & PREVIEW ONLY** — Awaiting Explicit Approval Before Implementation  
> **Date:** November 11, 2026  
> **Author:** Antigravity AI Engineering Team  

---

## 1. Executive Summary & Design Direction

Following the user's specific directive and reference screenshot (`media_1794384858355_3dec815a.jpg`), the **Service Company – Company Profile page** (`https://testing.jero.works/service_company/account`) is redesigned to achieve **100% visual and architectural parity with JERO's Internal Client Drawer**.

This replaces the fragmented, cluttered legacy account page with the clean, modern, high-density layout shown in the reference:
1. **Top Action Header Bar (`.cd-top-bar`):** Quick actions (`Edit Company`, `+ Add Contact`, `+ Add Job`, and drawer exit/close).
2. **Streamlined Hero Identity Card (`.cd-hero-card.cd-hero-streamlined`):** Brand Avatar (48×48px with green verification badge), bold Entity Title, tenant code badge (`#CL-121`), live website hyperlink with external link icon, scope pill (`Internal Client` / `Service Company`), real-time active status with pulsing emerald dot (`● Active`), active requisition counter (`⚡ 5 Active Positions`), and bottom specialty tag strip (`🏷️ Tags:`).
3. **6-Metric KPI Summary Row (`.cd-kpi-grid`):** Compact horizontal cards displaying `All Jobs` (5), `Active Jobs` (5), `All Candidates` (1), `All Interviews` (0), `All Offers` (0), and `All Joinings` (0).
4. **Navigation Sub-Tabs Bar (`.cd-drawer-tabs-card`):** Pill navigation (`Overview`, `Activity`, `Contacts`, `Jobs`, `Candidates`, `Interviews`, `Offers`, `Joined`, `Performance`) with solid `#009ef7` active highlight.
5. **Two-Column Master Content Grid (`.cd-grid-2col`):**
   - **Left Column:** `About the Client` card (rich company description + domain link), `Recent Activity` timeline (job status, requisition status pill, timestamps), and `Office Locations & Registered Address` (primary headquarters + branch delivery centers).
   - **Right Column:** `Client Information` executive vertical listing (`Website`, `Careers Portal`, `Tax ID / GSTIN` with one-click copy, `Industry`, `Company Type`, `Company Size`, `Founded`, `Headquarters`), verified `Social & Digital Profiles` (LinkedIn, X, GitHub, Facebook, Instagram, YouTube), and `Key Organization Stakeholders` (Primary POC, contacts).

---

## 2. Component Hierarchy & Comparison Matrix

| Layout Element | Current Legacy Account Page | Target Internal Client Drawer Standard (Screenshot Reference) |
|---|---|---|
| **Top Action Toolbar** | None; actions buried inside sections | `.cd-top-bar` with `Edit Company`, `+ Add Contact`, `+ Add Job` |
| **Hero Card** | Oversized 480px banner with heavy donut rings | `.cd-hero-card.cd-hero-streamlined` with 48px avatar, verified badge, name, web link, scope pill, active status dot, active jobs pill, and bottom tag strip |
| **KPI Metrics** | Disjointed coin balances & random cards | `.cd-kpi-grid`: 6 horizontal cards (`All Jobs`, `Active Jobs`, `All Candidates`, `All Interviews`, `All Offers`, `All Joinings`) |
| **Sub-Tabs** | Metronic text underline tabs with mixed styles | `.cd-drawer-tabs-card` with rounded pill buttons and solid `#009ef7` primary blue highlight |
| **About Section** | Truncated snippet in 3-column masonry | `.cd-card-panel` with blue bookmark icon, bold title, domain link, and rich text body |
| **Company Info** | Flat 5-row text list | `.cd-card-panel` with blue info icon, verified badge, and 8 executive vertical rows with color-coded 26px icons and pills |
| **Recent Activity** | Not visible on main overview | `.cd-timeline-list` with colored dot icons, opening counters, requisition badges, and timestamps |
| **In-Place Editing** | Requires complete navigation away to 5-step wizard | Seamless in-place edit toggle or drawer edit without context loss |

---

## 3. Preserved Business Logic & Tenancy

1. **Database Schema & Tenancy Scoping:** All operations strictly scoped to `auth()->user()->company_id`.
2. **Server-Side Validation:** `validateCompanySubmission` continues to enforce mandatory fields (`company_name`, `industry`, `company_size`, `founded`, `description`, at least 1 address).
3. **Elasticsearch Synchronization:** Painless script updates to `ELASTIC_COMPANIES_INDEX` and `ELASTIC_MASTER_NOTIFICATIONS` trigger on every save.
4. **Cache Atomicity:** Tenant cache flushes (`accountSettingsService->flushAccountCache($company_id)`) execute immediately upon update.

---

## 4. Visual Preview & Artifact Verification

- **Preview Workspace File:** [`public/previews/service_company_profile_preview.html`](file:///C:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/public/previews/service_company_profile_preview.html)
- **Preview Brain Artifact File:** [`service_company_profile_preview.html`](file:///C:/Users/VIBHANSHU%20JAISWAL.000/.gemini/antigravity/brain/7110f9e7-ad93-4c63-81ff-d8ab9b8b198c/service_company_profile_preview.html)
- **Reference Screenshot Analyzed:** `media_1794384858355_3dec815a.jpg`

*Awaiting user approval before applying changes to `service_company.account.index`.*
