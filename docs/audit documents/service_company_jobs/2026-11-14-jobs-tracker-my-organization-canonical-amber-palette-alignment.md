# Jobs Tracker & Entity Listings: "My Organization" Canonical Warm Amber Palette Alignment

## 1. Document Metadata
- **Date:** 2026-11-14
- **Module:** Service Company Jobs Tracker (`/service_company/jobs`), Client Drawers, and Entity Listings
- **Standard Reference:** Rule 20 (Card + Sub-Card Enterprise Design System), Rule 25 (Canonical Organization Classification Tags & Colors), `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` (Section 6 A)
- **Target Screens:** 15.6" Displays (`1366×768` and `1920×1080`)
- **Isolation Scope:** Page and component isolated per Rule 17; zero business logic mutations (Rule 7).

---

## 2. Executive Summary
Following user feedback and the established design tokens in `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` (Section 6 A), the color combination for the **My Organization** entity classification has been harmonized across all Service Company job tracking interfaces.

Previously, a discrepancy existed where earlier rules codified cyan/teal (`#ecfeff` bg / `#0891b2` text) while the design standard table specified warm amber/yellow (`#fef3c7` bg / `#92400e` text / `#fcd34d` border). This created a mismatch on the Jobs Tracker (`/service_company/jobs?kpi=my_organization`) between the top KPI tab and card badges.

This update aligns:
1. **Jobs Tracker KPI Tab (`My Organization Jobs`):**
   - **Active State (`.cd-kpi-card.cd-kpi-active[data-kpi="my_organization"]`, `.cd-kpi-card.jh-card-active[data-kpi="my_organization"]`):** Soft warm yellow background (`#fffbeb`), golden amber border (`#facc15`), warm amber accent box-shadow (`rgba(217, 119, 6, 0.12)`), white icon container with amber icon (`#d97706`), deep warm amber count (`#92400e`), and warm amber label (`#b45309`).
   - **Inactive State (`.cd-kpi-icon.icon-my-org`, `.cd-kpi-sub.sub-my-org`):** Soft amber icon container (`#fef3c7`) with amber glyph (`#d97706`), and amber subtext (`#d97706`).
2. **Job Card Tag / Scope Badge (`[MY ORGANIZATION]`):**
   - Standardized across `.cd-badge-client-type.type-my-org`, `.type-my-org`, and `.scope-pill-myorg` to warm amber background (`#fef3c7`), dark warm amber text (`#92400e`), solid amber border (`1px solid #fcd34d`), and amber icon (`#d97706`).
3. **Core Documentation & AI Rules Synchronization:**
   - Synchronized `AGENTS.md` (Rule 25), `CLAUDE.md` (Rule 20), `docs/AI_RULES.md`, and `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` to prevent future agent discrepancies.

---

## 3. Canonical Color Palette Specification

| Entity / Scope | Condition | Badge Pill Style | Active KPI Card Style | Inactive KPI Style |
|---|---|---|---|---|
| **My Organization** | Own company hiring (`self_company == 1` / `is_self_company === true`) | Bg: `#fef3c7`<br>Text: `#92400e`<br>Border: `1px solid #fcd34d`<br>Icon: `#d97706` (`fa-id-badge`) | Bg: `#fffbeb`<br>Border: `#facc15`<br>Shadow: `0 0 0 1.5px #d97706`<br>Num: `#92400e`<br>Label: `#b45309` | Icon Bg: `#fef3c7`<br>Icon Glyph: `#d97706`<br>Subtext: `#d97706` |
| **Internal Client** | Managed non-marketplace client (`self_company == 0 && !is_direct_client`) | Bg: `#eff6ff`<br>Text: `#2563eb`<br>Border: `1px solid #bfdbfe`<br>Icon: `#2563eb` (`fa-building`) | Bg: `#fff7ed`<br>Border: `#fb923c`<br>Shadow: `0 0 0 1.5px #ea580c`<br>Num: `#9a3412`<br>Label: `#c2410c` | Icon Bg: `#fff7ed`<br>Icon Glyph: `#ea580c`<br>Subtext: `#ea580c` |
| **Direct Client** | Marketplace client mandate (`is_direct_client === true`) | Bg: `#ecfdf5`<br>Text: `#059669`<br>Border: `1px solid #a7f3d0`<br>Icon: `#059669` (`fa-shield-alt`) | Bg: `#ecfdf5`<br>Border: `#34d399`<br>Shadow: `0 0 0 1.5px #059669`<br>Num: `#065f46`<br>Label: `#047857` | Icon Bg: `#ecfdf5`<br>Icon Glyph: `#16a34a`<br>Subtext: `#16a34a` |
| **Job Request** | Inbound requirement (`is_job_request === true`) | Dual tags: `Direct Client` + `Job Request` (`#f5f3ff` bg / `#7c3aed` text / `#ddd6fe` border) | Bg: `#faf5ff`<br>Border: `#c084fc`<br>Shadow: `0 0 0 1.5px #9333ea`<br>Num: `#6b21a8`<br>Label: `#7e22ce` | Icon Bg: `#faf5ff`<br>Icon Glyph: `#9333ea`<br>Subtext: `#9333ea` |

---

## 4. File Changes Matrix

| File Path | Description of Changes |
|---|---|
| `resources/views/service_company/jobs/index.blade.php` | Updated `.cd-kpi-card.cd-kpi-active[data-kpi="my_organization"]` and `.jh-card-active[data-kpi="my_organization"]` to warm amber palette (`#fffbeb`, `#facc15`, `#d97706`, `#92400e`, `#b45309`); updated inactive icon/subtext classes (`.icon-cyan, .icon-my-org`, `.sub-cyan, .sub-my-org`) to `#fef3c7`/`#d97706`; added `.cd-badge-client-type.type-my-org`, `.type-my-org`, and `.scope-pill-myorg` styling with `#d97706` icon color. |
| `resources/views/service_company/client/components/tab-jobs-style.blade.php` | Updated scoped `.cd-badge-client-type.type-my-org`, `.type-my-org`, and `.scope-pill-myorg` rules with `#fef3c7` bg, `#92400e` text, `#fcd34d` border, and `#d97706` icon. |
| `resources/views/service_company/client/components/tab-offers-joinings-style.blade.php` | Harmonized `.cd-badge-client-type.type-my-org`, `.type-my-org`, and `.scope-pill-myorg` to warm amber. |
| `resources/views/service_company/client/show.blade.php` | Scoped `.cd-badge-client-type.type-my-org` and `.type-my-org` badge styles to warm amber. |
| `resources/views/service_company/client/dviewMore.blade.php` | Scoped `.cd-badge-client-type.type-my-org` and `.type-my-org` badge styles to warm amber. |
| `resources/views/service_company/consultancy/dviewMore.blade.php` | Scoped `.cd-badge-client-type.type-my-org` and `.type-my-org` badge styles to warm amber. |
| `AGENTS.md` | Synchronized Rule 25 with warm amber/yellow tokens for `My Organization`. |
| `CLAUDE.md` | Synchronized Rule 20 with warm amber/yellow tokens for `My Organization`. |
| `docs/AI_RULES.md` | Harmonized Section 25 classification table with warm amber/yellow tokens. |
| `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` | Aligned Section 4.1 Sub-Card 1 and Section 6 B text with Section 6 A warm amber specification. |

---

## 5. 15.6" Screen Fit & Non-Regression Verification
- **1366×768 and 1920×1080 Viewports:** All KPI cards remain in their responsive grid layout with zero overflow (`overflow-x: hidden`).
- **Tag Pill Layout:** Badge pills maintain compact vertical footprint (8.5px bold font, `1px 6px` padding, 4px border radius) ensuring that Sub-Card 1 headers never wrap awkwardly on 1366px screens.
- **PHP Syntax:** Verified with `php -l` on all modified Blade files with 0 syntax errors.
- **Tenant Safety:** Tenancy (`company_id`) and CQRS read/write boundaries remain 100% untouched.
