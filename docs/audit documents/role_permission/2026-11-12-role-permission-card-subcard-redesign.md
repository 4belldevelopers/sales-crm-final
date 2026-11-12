# Service Company Roles & Permissions — Enterprise Card + Sub-Card Redesign

## Metadata
- **Date:** 2026-11-12
- **Module:** Service Company → Roles & Permissions (`/service_company/role-permission`)
- **Design Standard:** Rule 20 (Modern Card + Sub-Card Enterprise Design System), Client-Tracker-Inspired KPI Strip, Rule 14 (15.6" Responsive Fit), Rule 17 (Page Isolation)
- **Files Modified / Added:**
  - `resources/views/service_company/role-permission/index.blade.php`
  - `resources/views/service_company/role-permission/components/role_stat_cards.blade.php`
  - `resources/views/service_company/role-permission/components/role_filter_bar.blade.php`
  - `resources/views/service_company/role-permission/components/role_card_item.blade.php`
  - `tests/Feature/RolePermissionCardSubCardRedesignTest.php`

## Executive Summary
Redesigned the Service Company Roles & Permissions page into an enterprise-grade RBAC governance workspace with zero backend or business-logic mutations:
1. **Enterprise Header Toolbar Card (Client Tracker Inspired):** Added a `42×42` tinted icon badge, `16px` bold title with green verified RBAC shield, live role count pill, Client-First Export dropdown (browser-side CSV & Print-to-PDF audit report), and primary `#009ef7` **Add New Role** CTA.
2. **Client-Tracker-Inspired 6-Card Interactive KPI Strip (`role_stat_cards.blade.php`):** Built a single-row 6-column KPI grid (`Total Roles`, `System Roles`, `Custom Roles`, `High Access`, `Assigned Users`, `Permission Groups`) with `tabular-nums`, semantic icon boxes, micro-subtitles, and Client Tracker active highlight rings that filter the role stream and sync with Tier 2 sub-tabs.
3. **Rule 20 Tier 1 Toolbar & Tier 2 Sub-Tabs (`role_filter_bar.blade.php`):** Replaced basic inputs with a debounced search sub-card (with `✕` clear button and `/` keyboard shortcut), pill filters (`Role Type`, `Access Level`, `Sort`), `Reset` button, and a dedicated white sub-tabs card featuring single-active Metronic primary blue (`#009ef7`) highlighting and live visible role count.
4. **Rule 20 Tier 3 Primary Card + 5 Discrete Sub-Cards + Audit Strip (`role_card_item.blade.php`):** Transformed each role into a full-width primary card (`rp-primary-card`) with a semantic left status border and 5 `minmax(0, ...fr)` sub-cards: **Role Identity**, **Domain Coverage** (4-domain progress bars), **Permission Groups** (interactive `+N more` drawer trigger), **Assigned Users** (real user initials & tooltips replacing `A, B, C` placeholders), and **Governance** (`Overview`, `Configure`, and custom role `Delete`), plus a **Bottom Audit Strip** (`Created`, `Updated`, `Policy Coverage %`, and deep-links into the Permissions/Members drawer tabs).

## Architecture & Data Flow
- **Zero DB Queries in Blade:** Consumes pre-computed `$roles`, `$allRoleStats`, `$totalPermissions`, and `$groupTotals` from `RolePermissionService::getRoleListingData($companyId)` (`tenant:{companyId}:roles_permissions_v2` Redis cache).
- **100% Client-First Filtering & Export:** Search, KPI card filtering, sub-tab switching, sorting, CSV generation, and Print-to-PDF report rendering run entirely in browser RAM with zero server/DB read strain.

## 15.6" Screen Fit & Verification Evidence
- Uses `minmax(0, ...fr)` CSS Grid tracks and `overflow-x: hidden` with responsive breakpoints at `1440px`, `1240px`, `768px`, and `460px` to prevent horizontal scroll on `1366×768` and `1920×1080` screens.
- Compiled Blade templates verified via `php -l` with zero syntax errors, and feature tests verified via `RolePermissionCardSubCardRedesignTest`.
