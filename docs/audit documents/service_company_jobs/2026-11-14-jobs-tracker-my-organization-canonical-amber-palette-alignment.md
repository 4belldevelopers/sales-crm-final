# Jobs Tracker & Entity Listings: Canonical Classification Tab & Badge Color Alignment

## 1. Document Metadata
- **Date:** 2026-11-14
- **Module:** Service Company Jobs Tracker (`/service_company/jobs`), Client Drawers, and Entity Listings
- **Standard Reference:** Rule 20 (Card + Sub-Card Enterprise Design System), Rule 25 (Canonical Organization Classification Tags & Colors), `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` (Section 6 A)
- **Target Screens:** 15.6" Displays (`1366×768` and `1920×1080`)
- **Isolation Scope:** Page and component isolated per Rule 17; zero business logic mutations (Rule 7).

---

## 2. Executive Summary
Following user feedback and the established design tokens in `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` (Section 6 A), the color combinations for both **My Organization** and **Internal Client** entity classifications have been harmonized across all Service Company job tracking interfaces.

Previously:
1. **My Organization:** Was partially rendered in cyan/teal (`#ecfeff` bg / `#0891b2` text) while the design standard table specified warm amber/yellow (`#fef3c7` bg / `#92400e` text / `#fcd34d` border).
2. **Internal Client Jobs Tab:** Was rendered in orange/amber (`#fff7ed` bg / `#fb923c` border / `#ea580c` icon) while the corresponding card tag badge was blue (`#eff6ff` bg / `#2563eb` text / `#bfdbfe` border).

This update aligns:
1. **Jobs Tracker KPI Tab (`Internal Client Jobs`):**
   - **Active State (`.cd-kpi-card.cd-kpi-active[data-kpi="internal"]`, `.jh-card.jh-card-active.jh-card-source-internal`):**
     - Background: `#eff6ff` (soft blue)
     - Border: `1px solid #bfdbfe`
     - Focus Ring & Box Shadow: `0 0 0 1.5px #bfdbfe, 0 4px 14px rgba(37, 99, 235, 0.12)`
     - Icon Container: Pure white (`#ffffff`) with `#2563eb` glyph, border `1px solid #bfdbfe`, and soft shadow
     - Number & Label: Blue (`#2563eb`, font-weight `700`)
     - Subtext (`.cd-kpi-sub`): `#2563eb` (font-weight `600`)
   - **Inactive State (`.cd-kpi-icon.icon-internal`, `.cd-kpi-sub.sub-internal`):** Soft blue icon container (`#eff6ff`) with blue glyph (`#2563eb`), border `1px solid #bfdbfe`, and blue subtext (`#2563eb`).
   - **Hover State:** Soft blue hover background `#f8fbff` with `#bfdbfe` border.
2. **Jobs Tracker KPI Tab (`My Organization Jobs`):**
   - **Active State (`.cd-kpi-card.cd-kpi-active[data-kpi="my_organization"]`, `.jh-card.jh-card-active.jh-card-source-my-org`):** Warm yellow background (`#fef3c7`), golden amber border (`1px solid #fcd34d`), amber accent box-shadow (`rgba(217, 119, 6, 0.12)`), white icon container with amber icon (`#92400e`), deep warm amber count (`#92400e`), and warm amber label (`#92400e`).
   - **Inactive State (`.cd-kpi-icon.icon-my-org`, `.cd-kpi-sub.sub-my-org`):** Soft amber icon container (`#fef3c7`) with amber glyph (`#92400e`), border `1px solid #fcd34d`, and amber subtext (`#92400e`).
3. **Core Documentation & AI Rules Synchronization:**
   - Synchronized `AGENTS.md` (Rule 25), `CLAUDE.md` (Rule 20), `docs/AI_RULES.md`, and `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` so that the tag pills and active/inactive KPI cards are 100% harmonized.

---

## 3. Canonical Color Palette Specification

| Entity / Scope | Condition | Badge Pill Style | Active KPI Card Style | Inactive KPI Style |
|---|---|---|---|---|
| **My Organization** | Own company hiring (`self_company == 1` / `is_self_company === true`) | Bg: `#fef3c7`<br>Text: `#92400e`<br>Border: `1px solid #fcd34d`<br>Icon: `#d97706` (`fa-id-badge`) | Bg: `#fef3c7`<br>Border: `1px solid #fcd34d`<br>Shadow: `0 0 0 1.5px #fcd34d`<br>Num: `#92400e`<br>Label: `#92400e` | Icon Bg: `#fef3c7`<br>Icon Glyph: `#92400e`<br>Border: `1px solid #fcd34d`<br>Subtext: `#92400e` |
| **Internal Client** | Managed non-marketplace client (`self_company == 0 && !is_direct_client`) | Bg: `#eff6ff`<br>Text: `#2563eb`<br>Border: `1px solid #bfdbfe`<br>Icon: `#2563eb` (`fa-building`) | Bg: `#eff6ff`<br>Border: `1px solid #bfdbfe`<br>Shadow: `0 0 0 1.5px #bfdbfe`<br>Num: `#2563eb`<br>Label: `#2563eb` | Icon Bg: `#eff6ff`<br>Icon Glyph: `#2563eb`<br>Border: `1px solid #bfdbfe`<br>Subtext: `#2563eb` |
| **Direct Client** | Marketplace client mandate (`is_direct_client === true`) | Bg: `#ecfdf5`<br>Text: `#059669`<br>Border: `1px solid #a7f3d0`<br>Icon: `#059669` (`fa-shield-alt`) | Bg: `#ecfdf5`<br>Border: `#34d399`<br>Shadow: `0 0 0 1.5px #059669`<br>Num: `#065f46`<br>Label: `#047857` | Icon Bg: `#ecfdf5`<br>Icon Glyph: `#16a34a`<br>Subtext: `#16a34a` |
| **Job Request** | Inbound requirement (`is_job_request === true`) | Dual tags: `Direct Client` + `Job Request` (`#f5f3ff` bg / `#7c3aed` text / `#ddd6fe` border) | Bg: `#faf5ff`<br>Border: `#c084fc`<br>Shadow: `0 0 0 1.5px #9333ea`<br>Num: `#6b21a8`<br>Label: `#7e22ce` | Icon Bg: `#faf5ff`<br>Icon Glyph: `#9333ea`<br>Subtext: `#9333ea` |

---

## 4. File Changes Matrix

| File Path | Description of Changes |
|---|---|
| `resources/views/service_company/jobs/index.blade.php` | Updated `.cd-kpi-card[data-kpi="internal"]` active state and hover styles to blue palette (`#eff6ff`, `#bfdbfe`, `#2563eb`); updated inactive icon/subtext classes (`.icon-blue.icon-internal`, `.sub-blue.sub-internal`) with `#bfdbfe` border and `#2563eb` text; updated `data-kpi="my_organization"` active/inactive styles; updated Sub-Card 5 (Hiring Pipeline) summary footer label from `"Apps:"` to `"Applications:"`. |
| `demo/css/topCard.css` | Updated `.jh-card-source-internal.jh-card-active` radio dot border and background color from orange `#EA580C` to blue `#2563EB`. |
| `resources/views/sections/service_company/jobHeader.blade.php` | Updated `jh-icon-orange` to `jh-icon-blue`, and ensured label reads `Internal Client Jobs` (never bare `Internal Jobs`). |
| `AGENTS.md` | Synchronized Rule 25 with blue active KPI tokens for `Internal Client`. |
| `CLAUDE.md` | Synchronized Rule 20 with blue active KPI tokens for `Internal Client`. |
| `docs/AI_RULES.md` | Harmonized Section 25 classification table with blue active KPI tokens for `Internal Client`. |
| `docs/UI_UX_CARD_SUB_CARD_DESIGN_STANDARD.md` | Aligned Section 6 A classification table for `Internal Client` to `#eff6ff` / `#bfdbfe` / `#2563eb`; updated Sub-Card 5 footer spec to `"Applications: X • Interviews: Y"`. |

---

## 5. 15.6" Screen Fit & Non-Regression Verification
- **1366×768 and 1920×1080 Viewports:** All KPI cards remain in their responsive grid layout with zero overflow (`overflow-x: hidden`).
- **Tag Pill Layout:** Badge pills maintain compact vertical footprint (8.5px bold font, `1px 6px` padding, 4px border radius) ensuring that Sub-Card 1 headers never wrap awkwardly on 1366px screens.
- **PHP Syntax:** Verified with `php -l` on all modified Blade files with 0 syntax errors.
- **Tenant Safety:** Tenancy (`company_id`) and CQRS read/write boundaries remain 100% untouched.
