# Offer Tracker — Enterprise Card + Sub-Card Redesign & Client-Tracker KPI Architecture

## 1. Metadata
- **Date:** 2026-11-12
- **Module:** Service Company — Offer Tracker (`service_company/applicationoffer`)
- **Persona Scope:** Service Company (`resources/views/service_company/`)
- **Standards Applied:** Rule 20 (Modern Card + Sub-Card Enterprise Design System), Rule 14 (Metronic + Poppins + 15.6" Screen Fit), Rule 17 (Page/Blade Isolation), Rule 21 (Module Subfolder Documentation), Strict CQRS & Multi-Tenant Isolation

---

## 2. Executive Summary
Redesigned the Service Company **Offer Tracker** page (`service_company/applicationoffer`) from a flat HTML `<table>` listing into an enterprise-grade **Modern Card + Sub-Card** interface inspired by the **Clients Tracker** KPI architecture:
1. **Enterprise Header Card:** Clean pure white card (`#ffffff`) with verified module shield badge, live offer count pill, Sync action, Export dropdown (Client-First Browser CSV + Print/Save PDF), and functional More Actions menu.
2. **Client-Tracker-Inspired 6-Card Interactive KPI Strip:** Single horizontal row (`repeat(6, minmax(0, 1fr))`) for *Total Offers*, *Pending Offers*, *Offer Released*, *Accepted*, *Joining Soon*, and *Expired*. Clicking any KPI card filters the offer stream and highlights the active card with semantic ring/tint styling synchronized with the sub-tabs bar.
3. **Offers Pipeline Funnel Card:** Preserved interlocking chevron funnel (`Created → Released → Accepted → Rejected → Joined → Offer to Join Ratio`) with `#ot_pipeline_range` date window selector (`This Month`, `Last 6 Months`, `All Time`).
4. **Rule 20 Tier 1 & Tier 2 Toolbars:** Pure white Search & Filter toolbar card (300ms debounced search with `Ctrl+K` focus, clear `✕` button, Location select, Sort select, Reset) and pure white Sub-Tabs card with single `#009ef7` active blue highlight and semantic count pills.
5. **Rule 20 Tier 3 Offer Card + 5 Discrete Sub-Cards + Bottom Action Strip:** Replaced the `<table>` rows in `offer-tracker.blade.php` with `.ot-entity-card` primary cards featuring semantic left status borders, 5 upper discrete sub-cards (*Candidate*, *Job & Client*, *Compensation*, *Offer Status*, *Joining*), and a bottom *Owner / Stage / Ref / Quick Actions* strip.

---

## 3. Architecture & CQRS Compliance
- **Read Path:** 100% Elasticsearch (`ELASTIC_CANDIDATES`, `status_cus=5`) via `ServiceCompanyCvTrackerController::allcandidates` with 3-layer browser caching (`clientCache` RAM → `JeroOfferLocalStore` IndexedDB scoped by `company_id` & `user_id` → ES/Redis fallback) and real-time Reverb updates (`.offer.updated` on `company.{company_id}`).
- **Per-Row Query Reduction:** `offer-tracker.blade.php` now consumes controller-batched `$jobDetailsMap`, `$allowedConfidentialJobIds`, `$childStatusMap`, and `$cityDataMap` first before falling back to `GeneralMethod`, alongside the existing bulk `$offerMeta` query scoped by `o.company_id`.
- **Client-First CSV Export:** `exportOffersCsv()` generates and downloads CSV directly in browser RAM from rendered offer cards with 0% server read strain.
- **Preserved Workflows:** `.create_offer` drawer, `.ot-client-offer-warn` client-job guard, `.stage_sub_stage` stage-change drawer (`#csd_drawer`), `.fwd_offer` approver drawer (`#kt_fwd_offer_drawer`), and `.show-application-detail` candidate profile drawer remain 100% intact.

---

## 4. File Changes Matrix
| File | Change Summary |
| :--- | :--- |
| [index.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/applicationoffer/index.blade.php) | Redesigned into Enterprise Header Card, Client-Tracker-inspired 6-card clickable KPI grid, Offers Pipeline card, Rule 20 Tier 1 Toolbar & Tier 2 Sub-Tabs cards, Card Stream container, Client-First CSV export, and responsive 15.6" CSS. |
| [offer-tracker.blade.php](file:///c:/Users/VIBHANSHU%20JAISWAL.000/Documents/GitHub/JWMaincode28June2026/resources/views/service_company/ajax_pages/offer-tracker.blade.php) | Replaced `<tr>` table rows with Rule 20 `.ot-entity-card` containing 5 discrete upper sub-cards (`Candidate`, `Job & Client`, `Compensation`, `Offer Status`, `Joining`) and bottom action/metadata strip; wired pre-batched maps. |

---

## 5. 15.6" Screen Fit (`1366×768` & `1920×1080`)
- Fractional CSS Grid (`minmax(0, ...fr)`) on both `.ot-kpi-grid` and `.ot-entity-grid` with `overflow-x: hidden` prevents horizontal page scrollbars.
- Tightened `@media (max-width: 1440px)` breakpoints fit all 6 KPI cards in one row and all 5 sub-cards side-by-side on `1366×768` laptops, collapsing gracefully to 3/2/1 columns on tablet and mobile viewports.

---

## 6. Verification Evidence
- Verified Blade compilation for both `applicationoffer/index.blade.php` and `ajax_pages/offer-tracker.blade.php` with zero syntax errors.
- Confirmed zero edits to frozen `product_company/` or `staffing_company/` paths and zero changes to backend business logic.
