# Joining Tracker — Enterprise Card + Sub-Card Redesign & Client-Tracker KPI Architecture

## 1. Metadata
- **Date:** 2026-11-12
- **Module:** Service Company — Joining Tracker (`service_company/job-onboard`)
- **Persona Scope:** Service Company (`resources/views/service_company/`)
- **Standards Applied:** Rule 20 (Modern Card + Sub-Card Enterprise Design System), Rule 14 (Metronic + Poppins + 15.6" Screen Fit), Rule 17 (Page/Blade Isolation), Rule 18 (Product/Staffing Company Freeze), Rule 21 (Module Subfolder Documentation), Strict CQRS & Multi-Tenant Isolation

---

## 2. Executive Summary
Redesigned the Service Company **Joining Tracker** (`service_company/job-onboard`) from a flat HTML `<table>` listing into an enterprise-grade **Modern Card + Sub-Card** interface inspired by the **Clients Tracker** KPI architecture:
1. **Enterprise Header Card:** Pure white card (`#ffffff`) with verified ATS module shield badge, live total joining pill (`#jtHeaderTotalBadge`), **Sync** action (`#jtRefreshBtn`), **Export** dropdown (Client-First Browser RAM CSV + Executive Print/Save PDF), and functional **More Actions** quick-filter menu (`View Joining Today`, `Review Delayed Joinings`, `Check Pending Docs`, `Reset All Filters`).
2. **Client-Tracker-Inspired 6-Card Interactive KPI Strip:** Single horizontal row (`repeat(6, minmax(0, 1fr))`) for *Total Joinings* (with pulsing `X today` badge and current-month summary), *Joining Today* (with `X this week` sub-metric), *Onboarded* (with `% of total joinings`), *Pending Docs*, *Delayed*, and *Backed Out* (with `% · X no-show`). Clicking any KPI card filters the stream and applies Client-Tracker-style semantic ring/tint highlighting synchronized with the sub-tabs bar.
3. **Rule 20 Tier 1 & Tier 2 Toolbars:** Pure white Search & Filter toolbar card (300ms debounced search with `Ctrl+K` shortcut, clear `✕` button, `Esc` clear, Location select, Sort select covering Joining Date / Compensation / Offer Date, and Reset button) and pure white Sub-Tabs card (`All`, `Joining Today`, `This Week`, `Delayed`, `No Show`, `Pending Docs`, `Onboarded`, `Backed Out`) with single `#009ef7` primary blue active highlight, semantic count pills, and live Reverb/ES status chip.
4. **Rule 20 Tier 3 Joining Card + 5 Discrete Sub-Cards + Bottom Action Strip:** Replaced `<tr>` table rows in `joining-tracker.blade.php` with `.jt-entity-card` primary cards featuring semantic left status borders, 5 upper discrete sub-cards (*Candidate*, *Requisition*, *Compensation*, *Joining Status*, *Joining Date*), and a bottom *Owner / Stage / Ref / Quick Actions* strip.

---

## 3. Architecture & Data Flow (CQRS Compliance)
- **Read Path:** 100% Elasticsearch (`ELASTIC_CANDIDATES`, `status_cus=6`) via `ServiceCompanyCvTrackerController::allcandidates` with L1 RAM (`JoiningTrackerCache`), L2 IndexedDB (`JoiningTrackerIDB`, tenant-scoped key), Stale-While-Revalidate background revalidation, and `activeXhr.abort()` race-condition protection.
- **Real-Time Sync:** Preserved `BroadcastChannel` (`jero_joining_tracker_{TENANT_ID}`) and Laravel Echo/Reverb `.candidate.drawer.updated` listener on `company.{company_id}`, updated to target `.jt-entity-card[data-wise-id]` and `.jt-stage-btn` / `.jt-substage-pill` in place.
- **Pre-Batched Metadata:** `joining-tracker.blade.php` consumes controller-batched `$jobDetailsMap`, `$allowedConfidentialJobIds`, `$spokUserMap`, `$childStatusMap`, and `$cityDataMap`, with clean Blade-escaped currency formatting via `GeneralMethod::currency()`.
- **Client-First Export:** `exportJoiningCsv()` generates and downloads CSV directly in browser RAM from rendered `.jt-entity-card` data attributes with UTF-8 BOM and 0% server read strain.
- **Preserved Workflows:** Candidate profile drawer (`.show-application-detail`), duplicate pulse indicator, `[Bench]` badge, confidentiality salary mask, and Change Candidate Stage drawer (`#csd_drawer` via `.stage_sub_stage` with idempotency key submission) remain 100% intact.

---

## 4. File Changes Matrix
| File | Change Summary |
| :--- | :--- |
| [index.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/job-onboard/index.blade.php) | Redesigned into Enterprise Header Card, Client-Tracker-inspired 6-card clickable KPI grid, Rule 20 Tier 1 Toolbar & Tier 2 Sub-Tabs cards, Card Stream container, Client-First CSV export, updated Reverb card delta handler, and 15.6" responsive CSS. |
| [joining-tracker.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/ajax_pages/joining-tracker.blade.php) | Replaced `<tr>` table rows with Rule 20 `.jt-entity-card` containing 5 discrete upper sub-cards (`Candidate`, `Requisition`, `Compensation`, `Joining Status`, `Joining Date`) and bottom ownership/stage/actions strip; wired pre-batched maps. |

---

## 5. 15.6" Screen Fit (`1366×768` & `1920×1080`)
- Fractional CSS Grid (`minmax(0, ...fr)`) on both `.jt-kpi-grid` and `.jt-entity-grid` with `overflow-x: hidden` eliminates horizontal window scrollbars.
- Tightened `@media (max-width: 1440px)` breakpoints fit all 6 KPI cards in one row and all 5 sub-cards side-by-side on `1366×768` laptops, collapsing cleanly to 3/2/1 columns on tablet and mobile viewports.

---

## 6. Verification Evidence
- Compiled and PHP-linted both `job-onboard/index.blade.php` and `ajax_pages/joining-tracker.blade.php` via Laravel's `blade.compiler` + `php -l` with zero syntax errors.
- Validated the inline JavaScript engine in `job-onboard/index.blade.php` via `node --check` with zero syntax errors.
- Verified `git status` shows changes strictly isolated to Service Company Joining Tracker views and this documentation file (zero edits to `product_company/` or `staffing_company/`).
