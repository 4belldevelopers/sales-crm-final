# Service Company Career Page Settings — Enterprise Card + Sub-Card Redesign

## Metadata
- **Date:** 2026-11-12
- **Module:** Service Company → Career Page Settings (`/service_company/career-page/settings`)
- **Design Standard:** Rule 20 (Modern Card + Sub-Card Enterprise Design System), Rule 14 (15.6" Responsive Fit), Rule 17 (Page Isolation)
- **Files Modified / Added:**
  - `resources/views/service_company/career-page/settings.blade.php`
  - `resources/views/service_company/career-page/tabs/general.blade.php`
  - `resources/views/service_company/career-page/tabs/hero.blade.php`
  - `resources/views/service_company/career-page/tabs/company.blade.php`
  - `resources/views/service_company/career-page/tabs/benefits.blade.php`
  - `resources/views/service_company/career-page/tabs/jobs.blade.php`
  - `resources/views/service_company/career-page/tabs/seo.blade.php`
  - `resources/views/service_company/career-page/tabs/integrations.blade.php`
  - `resources/views/service_company/career-page/tabs/advanced.blade.php`
  - `resources/views/service_company/career-page/tabs/templates.blade.php`
  - `tests/Feature/CareerPageSettingsV2Test.php`
  - `docs/audit documents/career_page/2026-11-12-career-page-settings-card-subcard-redesign.md`

## Executive Summary
Redesigned the Service Company Career Page Settings studio into an enterprise-grade, page-isolated Modern Card + Sub-Card workspace with zero backend or business-logic mutations:
1. **Rule 20 Tier 1 Pure White Toolbar Card (`settings.blade.php`):** Replaced the legacy full-bleed blue gradient banner with a clean `#ffffff` toolbar card featuring a `42×42` tinted icon box, live portal status pill (`Live` / `Maintenance` / `Offline`), tenant company badge, dirty-state indicator (`Unsaved Changes`), a 300ms debounced **Global Settings Search** across all 9 tabs (with `✕` clear and `/` keyboard shortcut), **Visit Career Page** button, and primary `#009ef7` **Save All Changes** CTA.
2. **Interactive 5-Card KPI Telemetry Strip:** Added a 5-column clickable KPI strip (`Portal Status`, `Design Template`, `Job Board Layout`, `SEO & Social`, `Speed & Cache`) using `tabular-nums` that syncs live with form toggles and jumps directly to the relevant tab.
3. **Rule 20 Tier 2 Pure White Sub-Tabs Card:** Replaced the flat joined tab strip with a dedicated white sub-tabs card featuring single-active Metronic primary blue (`#009ef7`) highlighting, live status/count badges, and URL `#tab-*` deep-linking.
4. **Rule 20 Tier 3 Primary Cards + Discrete Sub-Cards Across All 9 Tabs:** Refactored every tab (`general`, `hero`, `company`, `benefits`, `jobs`, `seo`, `integrations`, `advanced`, `templates`) into semantic left-bordered primary cards (`.cps-card`) housing structured `.cps-subcard` grids (`minmax(0, ...)`), sticky live preview panels, dynamic tenant brand previews (eliminating hardcoded `4Bell` placeholders), client-first integrations filtering, and client-first JSON settings export in browser RAM.

## Architecture & Data Flow
- **Zero Business Logic / Controller Mutations:** Preserves 100% of `ServiceCompanyAccountSettingsController::careerPageSettings()` and `careerPageSettingsSave()` form field names, file upload handlers, and 24-hour Redis cache invalidation (`AccountSettingsService::flushCareerCache($company_id)`).
- **Color & Preview Coherence:** Synchronizes `primary_color` / `secondary_color` (General tab) with `td_color_primary` / `td_color_secondary` (Templates Design tab) in browser memory so saving from any tab persists consistent colors.
- **Security & XSS Hygiene:** Removed raw `{!! ... !!}` output on user-entered `hero_heading` inside the settings preview, rendering escaped text safely.

## 15.6" Screen Fit & Verification Evidence
- Uses `minmax(0, 1.55fr) minmax(0, 1fr)` workspace grids, `minmax(0, 1fr)` sub-card grids, and `overflow-x: hidden` with responsive breakpoints at `1280px`, `1024px`, and `768px` to ensure zero horizontal scroll on `1366×768` and `1920×1080` screens.
- Verified Blade compilation (`php artisan view:cache`) and automated test suite (`php artisan test --filter=CareerPageSettingsV2Test`).
